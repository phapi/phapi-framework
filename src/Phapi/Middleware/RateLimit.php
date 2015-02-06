<?php

namespace Phapi\Middleware;

use Phapi\Exception\Error\InternalServerError;
use Phapi\Exception\Error\TooManyRequests;
use Phapi\Middleware;
use Phapi\Cache\NullCache;

/**
 * Middleware class
 *
 * Middleware class for handling rate limits.
 *
 * @category Middleware
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class RateLimit extends Middleware
{

    /**
     * What header should we use that includes an identifier?
     *
     * @var string
     */
    protected $identifierHeader;

    /**
     * Unique identifier
     *
     * @var string
     */
    protected $identifier;

    /**
     * Bucket configuration
     *
     * @var array
     */
    protected $buckets;

    /**
     * Matched bucket by requested resource
     *
     * @var \Phapi\Middleware\RateLimit\Bucket
     */
    protected $bucket;

    public function __construct($identifierHeader, array $buckets = [])
    {
        // Settings
        $this->identifierHeader = $identifierHeader;
        $this->buckets = $buckets;
    }

    public function call()
    {
        // Get resource
        $resource = $this->app->getRouter()->getMatchedResource();

        // Check what bucket to use
        if (array_key_exists($resource, $this->buckets)) {
            $this->bucket = $this->buckets[$resource];
        } elseif (array_key_exists('default', $this->buckets)) {
            $this->bucket = $this->buckets['default'];
        } else {
            // No bucket configured
            throw new InternalServerError('Middleware Rate Limit needs at least one (default) bucket to work.');
        }

        $cache = $this->app->getCache();
        // Check for cache
        if ($cache instanceof NullCache) {
            // Throw error since we don't have anywhere to save the data
            throw new InternalServerError('Middleware Rate Limit needs a cache to work.');
        }

        // Get identifier and check it it's null
        if (null === $this->identifier = $this->getIdentifier()) {
            // No identifier found
            $this->getApplication()->getLogWriter()->warning(
                'Request (ID: '. $this->app->getRequest()->getUuid() .') made but without '.
                'the '. $this->identifierHeader .' header. Please note that the request was executed as normal.'
            );

            // Call next middleware if one is set
            if ($this->next !== null) {
                $this->next->call();
            }
            return;
        }

        // Get saved data from cache
        $this->bucket->remainingTokens = $cache->get('rateLimit'. $resource . $this->identifier);
        $this->bucket->updated = $cache->get('rateLimitUpdated'. $resource . $this->identifier);

        // Refill tokens
        $this->refillTokens();

        // Set headers
        $this->setHeaders();

        // Check if there are enough tokens left
        $this->checkTokens();

        // Save to cache
        $this->app->getCache()->set('rateLimit'. $resource . $this->identifier, $this->bucket->remainingTokens);
        $this->app->getCache()->set('rateLimitUpdated'. $resource . $this->identifier, $this->bucket->updated);

        // Call next middleware if one is set
        if ($this->next !== null) {
            $this->next->call();
        }
    }

    /**
     * Refill tokens
     */
    protected function refillTokens()
    {
        $rate = 0;
        // calculate how many seconds it is since cache was updated
        $seconds = time() - $this->bucket->updated;

        // check if we should add tokens continuously
        if ($this->bucket->newTokenContinuous) {
            // calculate how many tokens to add for each second
            $rate = $this->bucket->newTokens / $this->bucket->newTokensWindow;

            // add tokens based on seconds since last cache update
            $this->bucket->remainingTokens += round($rate * $seconds);

            // update when refill was made
            $this->bucket->updated = time();
        } else {
            // calculate how many periods has passed since cache update
            $periods = floor($seconds / $this->bucket->newTokensWindow);

            // add tokens based on periods
            $this->bucket->remainingTokens += $this->bucket->newTokens * $periods;

            // check if more than one period has passed since last refill
            if ($periods >= 1) {
                $this->bucket->updated = time();
            }
        }

        // make sure remaining tokens never exceeds the max number of total tokens
        if ($this->bucket->remainingTokens > $this->bucket->totalTokens) {
            $this->bucket->remainingTokens = $this->bucket->totalTokens;
        }
    }

    /**
     * Set rate limit headers to response
     */
    protected function setHeaders()
    {
        // set response headers
        $this->app->getResponse()->addHeaders([
            'X-Rate-Limit-Limit' => $this->bucket->totalTokens,
            'X-Rate-Limit-Remaining' => $this->bucket->remainingTokens
        ]);

        // headers about how many new tokens are added over time differs depending on if
        // continuous adding is active
        if ($this->bucket->newTokenContinuous) {
            $this->app->getResponse()->addHeaders([
                'X-Rate-Limit-Window' => 1,
                'X-Rate-Limit-New' => round($this->bucket->newTokens / $this->bucket->newTokensWindow)
            ]);
        } else {
            $this->app->getResponse()->addHeaders([
                'X-Rate-Limit-Window' => $this->bucket->newTokensWindow,
                'X-Rate-Limit-New' => $this->bucket->newTokens
            ]);
        }
    }

    /**
     * Check if there are enough tokens left in the bucket
     *
     * @throws TooManyRequests
     */
    protected function checkTokens()
    {
        // enough tokens left?
        if ($this->bucket->remainingTokens > 0) {
            // yes, lets remove one
            $this->bucket->remainingTokens--;
        } else {
            // no tokens left
            if ($this->bucket->newTokenContinuous) {
                throw new TooManyRequests('You\'ve run out of request tokens. You receive '. round($this->bucket->newTokens / $this->bucket->newTokensWindow) .' every second.');
            } else {
                throw new TooManyRequests('You\'ve run out of request tokens. You receive '. $this->bucket->newTokens .' new tokens every '. $this->bucket->newTokensWindow .' seconds.');
            }
        }
    }

    /**
     * Get the unique identifier. Uses the provided header name to look
     * for a unique header.
     *
     * IMPORTANT: Extend this class and implement your own getIdentifier function if you want to.
     *
     * @return string
     */
    public function getIdentifier()
    {
        if ($this->identifier === null) {
            $this->identifier = $this->app->getRequest()->getHeaders()->get($this->identifierHeader);
        }
        return $this->identifier;
    }

    /**
     * Set identifier
     *
     * @param $identifier string
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }
}