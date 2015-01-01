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
     * Error message
     *
     * @var string
     */
    protected $errorMessage = null;

    /**
     * More information
     *
     * @var string
     */
    protected $information = null;

    /**
     * Link to more information
     *
     * @var string
     */
    protected $link = null;

    /**
     * Link used for redirects
     *
     * @var null
     */
    protected $location = null;

    /**
     * Create new exception
     *
     * @param null $errorCode
     * @param string $errorMessage
     * @param string $information
     * @param string $link
     * @param string $redirect
     */
    public function __construct($errorCode = null, $errorMessage = '', $information = '', $link = '', $redirect = null)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->information = $information;
        $this->link = $link;
        $this->location = $redirect;
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
     * Get error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

}