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
     * Information that should be logged.
     * Useful for giving the developer information
     * that can be used for debugging problems.
     *
     * @var null
     */
    protected $logInformation = null;

    /**
     * Information shown for the user
     *
     * @var string
     */
    protected $userInformation = null;

    /**
     * Link to more information
     *
     * @var string
     */
    protected $userInformationLink = null;

    /**
     * Link used for redirects
     *
     * @var null
     */
    protected $location = null;

    /**
     * Create exception
     *
     * @param null          $errorMessage           An error message (shown to the user)
     * @param null          $errorCode              Error code (shown to the user)
     * @param \Exception    $previous               Previous exception (used when logging)
     * @param null          $logInformation         Information that goes in the log. Useful for debugging.
     * @param null          $userInformation        More information given to the user
     * @param null          $userInformationLink    A link to error documentation (shown to the user)
     * @param null          $redirect               Location used for redirects
     */
    public function __construct(
        $errorMessage = null,
        $errorCode = null,
        \Exception $previous = null,
        $logInformation = null,
        $userInformation = null,
        $userInformationLink = null,
        $redirect = null
    ) {
        // Check if log information should be set
        if (!is_null($logInformation)) {
            $this->logInformation = $logInformation;
        }

        // Check if information should be set
        if (!is_null($userInformation)) {
            $this->userInformation = $userInformation;
        }

        // Check if link should be set
        if (!is_null($userInformationLink)) {
            $this->userInformationLink = $userInformationLink;
        }

        // Check if redirect location should be set
        if (!is_null($redirect)) {
            $this->location = $redirect;
        }

        // Do not replace the exception message with null if its predefined in the class
        if (is_null($errorMessage)) {
            $errorMessage = $this->message;
        }

        // Do not replace the exception error code with null if its predefined in the class
        if (is_null($errorCode)) {
            $errorCode = $this->code;
        }

        // make sure everything is assigned properly
        parent::__construct($errorMessage, $errorCode, $previous);
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

    /**
     * Get extra information that are logged
     *
     * @return null
     */
    public function getLogInformation()
    {
        return $this->logInformation;
    }

    /**
     * Get "more information" shown to the user
     *
     * @return string
     */
    public function getUserInformation()
    {
        return $this->userInformation;
    }

    /**
     * Get link to more user information
     *
     * @return string
     */
    public function getUserInformationLink()
    {
        return $this->userInformationLink;
    }

    /**
     * Get redirect location
     *
     * @return null
     */
    public function getLocation()
    {
        return $this->location;
    }
}