<?php

namespace Phapi;

/**
 * Class Exception
 *
 * Class extending exception
 *
 * @category Exception
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
abstract class Exception extends \Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage;

    /**
     * Application error code
     *
     * @var null
     */
    protected $errorCode = null;

    /**
     * More information
     *
     * @var string
     */
    protected $information = '';

    /**
     * Link to more information
     *
     * @var string
     */
    protected $link = '';

    /**
     * Create new exception
     *
     * @param null $errorCode
     * @param string $information
     * @param string $link
     */
    public function __construct($errorCode = null, $information = '', $link = '')
    {
        $this->errorCode = $errorCode;
        $this->information = $information;
        $this->link = $link;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Get response status code.
     * Example: 500
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get response status message.
     * Example: Internal Server Error
     *
     * @return mixed
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * Get application error code
     *
     * @return null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get more information
     *
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
    }
}