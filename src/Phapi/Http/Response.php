<?php

namespace Phapi\Http;

/**
 * Response class
 *
 * Class/Object representing the response that will be sent to the client
 *
 * @category Http
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Response
{
    /**
     * Success!
     */
    const STATUS_OK = 200;

    /**
     * New resource created
     */
    const STATUS_CREATED = 201;

    /**
     * Request accepted and set to be performed in a background task. Useful if your client is
     * requesting something on the API that is time-consuming and you don?t want the client to have to wait.
     */
    const STATUS_ACCEPTED = 202;

    /**
     * The response does not include any content.
     */
    const STATUS_NO_CONTENT = 204;

    /**
     * Moved permanently
     */
    const STATUS_MOVED_PERMANENTLY = 301;

    /**
     * There was no new data to return.
     */
    const STATUS_NOT_MODIFIED = 304;

    /**
     * Temporary Redirect
     */
    const STATUS_TEMPORARY_REDIRECT = 307;

    /**
     * The request was invalid or cannot be otherwise served. An accompanying error message will explain further.
     */
    const STATUS_BAD_REQUEST = 400;

    /**
     * Authentication credentials were missing or incorrect.
     */
    const STATUS_UNAUTHORIZED = 401;

    /**
     * The request is understood, but it has been refused or access is not allowed. An accompanying
     * error message will explain why.
     */
    const STATUS_FORBIDDEN = 403;

    /**
     * The URI requested is invalid or the resource requested, such as a user, does not exists.
     * Also returned when the requested format is not supported by the requested method.
     */
    const STATUS_NOT_FOUND = 404;

    /**
     * Returned by the API when an invalid format is specified in the request.
     */
    const STATUS_NOT_ACCEPTABLE = 406;

    /**
     * This resource is gone. Used to indicate that an API endpoint has been turned off.
     * For example: "The REST API v1 will soon stop functioning. Please migrate to API v1.1."
     */
    const STATUS_GONE = 410;

    /**
     * Payment is required before the requested method/resource can be requested.
     */
    const STATUS_PAYMENT_REQUIRED = 402;

    /**
     * The requested method is not allowed.
     */
    const STATUS_METHOD_NOT_ALLOWED = 405;

    /**
     * The request timed out.
     */
    const STATUS_REQUEST_TIMEOUT = 408;

    /**
     * The submitted data is causing a conflict with the current state of the resource.
     * An accompanying error message will explain why.
     */
    const STATUS_CONFLICT = 409;

    /**
     * The requested entity is too large.
     */
    const STATUS_REQUEST_ENTITY_TOO_LARGE = 413;

    /**
     * Media type not supported.
     */
    const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * Returned when an uploaded file is unable to be processed.
     */
    const STATUS_UNPROCESSABLE_ENTITY  = 422;

    /**
     * The requested resource is currently locked
     */
    const STATUS_LOCKED = 423;

    /**
     * Returned when a request cannot be served due to the application's rate
     * limit having been exhausted for the resource.
     */
    const STATUS_TOO_MANY_REQUESTS = 429;

    /**
     * Something is broken.
     */
    const STATUS_INTERNAL_SERVER_ERROR = 500;

    /**
     * The requested method is not implemented.
     */
    const STATUS_NOT_IMPLEMENTED = 501;
    /**
     * The API is down or being upgraded
     */
    const STATUS_BAD_GATEWAY = 502;

    /**
     * The API is up, but overloaded with requests. Try again later.
     */
    const STATUS_SERVICE_UNAVAILABLE = 503;

    /**
     * HTTP response codes and messages
     *
     * @var array
     */
    protected static $messages = [
        //Informational 1xx
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        //Successful 2xx
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        //Redirection 3xx
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Unused)',
        307 => '307 Temporary Redirect',
        //Client Error 4xx
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        418 => '418 I\'m a teapot',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        //Server Error 5xx
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported'
    ];

    /**
     * Response headers
     *
     * @var Header
     */
    protected $headers;

    /**
     * Status code
     *
     * @var int
     */
    protected $status;

    /**
     * Content/mime type
     *
     * @var string
     */
    protected $contentType;

    /**
     * Redirect location
     *
     * @var string
     */
    protected $location;

    /**
     * The body
     *
     * @var array
     */
    protected $body = [];

    /**
     * Length of the body
     *
     * @var int
     */
    protected $length;

    /**
     * Http version
     *
     * @var string
     */
    protected $httpVersion = '1.1';

    public function __construct(Header $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Add headers
     *
     * @param array $headers
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->headers->set($key, $value);
        }
    }

    /**
     * Get headers
     *
     * @return Header
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get message for HTTP status code
     *
     * @param  int         $status
     * @return string|null
     */
    public static function getMessageForCode($status)
    {
        if (isset(self::$messages[$status])) {
            return self::$messages[$status];
        } else {
            return null;
        }
    }

    /**
     * Set the response status code
     *
     * @param int $status
     */
    public function setStatus($status = self::STATUS_OK)
    {
        $this->status = (int) $status;
    }

    /**
     * Get response status code
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the content type of the response
     *
     * @param $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->headers->set('Content-Type', $contentType ."; charset=utf-8");
    }

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set redirect location
     *
     * @param $location
     */
    public function setRedirect($location)
    {
        $this->location = $location;
        $this->clearBody();
    }

    /**
     * Set the response body. Use an array. This body will
     * then be encoded to the set content/mime type.
     *
     * @param array $body
     */
    public function setBody(array $body)
    {
        $this->body = $body;
    }

    /**
     * Add content to the body. Added body
     *
     * @param array $body
     */
    public function addBody(array $body)
    {
        $this->body = array_merge($this->body, $body);
    }

    /**
     * Get body
     *
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Clear the body
     */
    public function clearBody()
    {
        $this->body = [];
    }

    /**
     * Set length of body
     *
     * @param $length
     */
    public function setLength($length)
    {
        $this->length = $length;
        $this->headers->set('Content-Length', $length);
    }

    /**
     * Set HTTP version
     *
     * @param $httpVersion
     */
    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;
    }

    /**
     * Get HTTP version
     *
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->httpVersion;
    }
}