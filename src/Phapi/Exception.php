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
class Exception extends \Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = null;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = null;

    /**
     * Link to more information
     *
     * @var null|string
     */
    protected $link;

    /**
     * Extra information sent to the log
     *
     * @var null|string
     */
    protected $logInformation;

    /**
     * Error description
     *
     * @var null|string
     */
    protected $description;

    /**
     * Location used for redirect
     *
     * @var null|string
     */
    protected $location;

    /**
     * Create a new exception
     *
     * @param null          $message            More information given to the user (shown to the user)
     * @param null          $code               Error code (shown to the user)
     * @param \Exception    $previous           Previous exception (used for logging)
     * @param null          $link               A link to error documentation (shown to the user)
     * @param null          $logInformation     Information that goes in the log. Useful for debugging. (used for logging)
     * @param null          $description        An error message (shown to the user). All Phapi Exceptions have predefined error messages so you can pass * **null** * to use the predifined message.
     * @param null          $location           Location used for redirects
     */
    public function __construct(
        $message = null,
        $code = null,
        \Exception $previous = null,
        $link = null,
        $logInformation = null,
        $description = null,
        $location = null
    ) {
        // Do not overwrite predefined values with null
        $this->link = (is_null($link)) ? $this->link: $link;
        $this->logInformation = (is_null($logInformation)) ? $this->logInformation: $logInformation;
        $this->description = (is_null($description)) ? $this->description: $description;
        $this->location = (is_null($location)) ? $this->location: $location;

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get link to documentation
     *
     * @return null|string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Get extra log information
     *
     * @return null|string
     */
    public function getLogInformation()
    {
        return $this->logInformation;
    }

    /**
     * Get error description
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get redirect location
     *
     * @return null|string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get status message
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

}