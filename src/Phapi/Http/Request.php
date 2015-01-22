<?php
namespace Phapi\Http;

use Phapi\Bucket;

/**
 * Request class
 *
 * Class representing the request made by the client.
 *
 * @category Http
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Request
{

    const METHOD_COPY       = 'COPY';
    const METHOD_DELETE     = 'DELETE';
    const METHOD_GET        = 'GET';
    const METHOD_HEAD       = 'HEAD';
    const METHOD_LOCK       = 'LOCK';
    const METHOD_OPTIONS    = 'OPTIONS';
    const METHOD_PATCH      = 'PATCH';
    const METHOD_POST       = 'POST';
    const METHOD_PUT        = 'PUT';
    const METHOD_UNLOCK     = 'UNLOCK';

    /**
     * UUID for the request
     *
     * @var null
     */
    protected $uuid = null;

    /**
     * Request method
     *
     * @var string
     */
    protected $method;

    /**
     * Request URI
     *
     * @var string
     */
    protected $uri;

    /**
     * Headers
     *
     * @var Header
     */
    protected $headers;

    /**
     * Post parameters
     *
     * @var Bucket
     */
    protected $body;

    /**
     * Get parameters
     *
     * @var Bucket
     */
    protected $query;

    /**
     * Server parameters
     *
     * @var Server
     */
    protected $server;

    /**
     * Raw content
     *
     * @var mixed
     */
    protected $rawContent;

    /**
     * Attributes are discovered via decomposing the URI path
     *
     * @var Bucket
     */
    protected $attributes;

    /**
     * Client preferred encodings
     *
     * @var string
     */
    protected $encodings;

    /**
     * Client IP
     *
     * @var string
     */
    protected $clientIp;

    /**
     * Selected content type done by the Negotiator
     *
     * @var string
     */
    protected $contentType;

    /**
     * Selected accept type done by the Negotiator
     *
     * @var string
     */
    protected $accept;

    public function __construct($post = null, $get = null, $server = null, $rawContent = null)
    {
        $this->body = new Bucket((is_null($post)) ? $_POST: $post);
        $this->query = new Bucket((is_null($get)) ? $_GET: $get);
        $this->server = new Server((is_null($server)) ? $_SERVER: $server);
        $this->rawContent = (is_null($rawContent)) ? file_get_contents("php://input"): $rawContent;
        $this->headers = new Header($this->server->getHeaders());

        $this->attributes = new Bucket();
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
     * Get unique request identifier (UUID)
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set unique request identifier (UUID)
     *
     * @param $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Get raw content
     *
     * @return mixed
     */
    public function getRawContent()
    {
        return $this->rawContent;
    }

    /**
     * Check if the request included raw content
     *
     * @return bool
     */
    public function hasRawContent()
    {
        return (isset($this->rawContent) && !empty($this->rawContent)) ? true : false;
    }

    /**
     * Get the negotiated content type of the content in the request
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set the negotiated content type of the content in the request
     *
     * @param $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Get the negotiated Accept type that should be used
     * in the response.
     *
     * @return string
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * Set the negotiated Accept type that should be used
     * in the response.
     *
     * @param $accept
     */
    public function setAccept($accept)
    {
        $this->accept = $accept;
    }

    /**
     * Add attributes/params
     *
     * @param array $attributes
     */
    public function addAttributes(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes->set($key, $value);
        }
    }

    /**
     * Get body (POST) params
     *
     * @return Bucket
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set body (deserialized POST) params
     *
     * @param array $body
     */
    public function setBody(array $body)
    {
        $this->body = new Bucket($body);
    }

    /**
     * Get query (GET) params
     *
     * @return Bucket
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get server params
     *
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Get attributes that are discovered via
     * decomposing the URI path
     *
     * @return Bucket
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get the current request method
     *
     * @return string
     */
    public function getMethod()
    {
        if (is_null($this->method)) {
            // analyze request method
            $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));

            if ('POST' === $this->method) {
                if ($method = $this->headers->get('X-HTTP-METHOD-OVERRIDE')) {
                    $this->method = strtoupper($method);
                } else {
                    $this->method = strtoupper($this->body->get('_method', $this->query->get('_method', 'POST')));
                }
            }
        }

        return $this->method;
    }

    /**
     * Check if the request method is of type
     *
     * @param $method string The method type we want to test (in UPPERCASE)
     *
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->getMethod() == $method;
    }

    /**
     * Check if request method is COPY
     *
     * @return bool
     */
    public function isCopy()
    {
        return $this->isMethod('COPY');
    }

    /**
     * Check if request method is DELETE
     *
     * @return bool
     */
    public function isDelete()
    {
        return $this->isMethod('DELETE');
    }

    /**
     * Check if request method is GET
     *
     * @return bool
     */
    public function isGet()
    {
        return $this->isMethod('GET');
    }

    /**
     * Check if request method is HEAD
     *
     * @return bool
     */
    public function isHead()
    {
        return $this->isMethod('HEAD');
    }

    /**
     * Check if request method is LOCK
     *
     * @return bool
     */
    public function isLock()
    {
        return $this->isMethod('LOCK');
    }

    /**
     * Check if request method is OPTIONS
     *
     * @return bool
     */
    public function isOptions()
    {
        return $this->isMethod('OPTIONS');
    }

    /**
     * Check if request method is PATCH
     *
     * @return bool
     */
    public function isPatch()
    {
        return $this->isMethod('Patch');
    }

    /**
     * Check if request method is POST
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * Check if request method is PUS
     *
     * @return bool
     */
    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    /**
     * Check if request method is UNLOCK
     *
     * @return bool
     */
    public function isUnlock()
    {
        return $this->isMethod('UNLOCK');
    }

    /**
     * Get clients IP address
     *
     * @return mixed
     */
    public function getClientIp()
    {
        if (is_null($this->clientIp)) {
            // analyze client ip
            if ($this->server->has('HTTP_X_FORWARDED_FOR')) {
                $this->clientIp = $this->server->get('HTTP_X_FORWARDED_FOR');
            } else {
                $this->clientIp = $this->server->get('REMOTE_ADDR');
            }
        }
        return $this->clientIp;
    }

    /**
     * Get the requested URI
     *
     * @return string
     */
    public function getUri()
    {
        if (is_null($this->uri)) {
            $this->uri = $this->server->get('REQUEST_URI');
        }
        return $this->uri;
    }

    /**
     * Get clients preferred encodings
     *
     * @return array|string
     */
    public function getEncodingHeader()
    {
        if (is_null($this->encodings)) {
            $this->encodings = $this->headers->get('accept-encoding');
        }
        return $this->encodings;
    }

    /**
     * Gets the Etags.
     *
     * @return array The entity tags
     */
    public function getETags()
    {
        return preg_split('/\s*,\s*/', $this->headers->get('if_none_match'), null, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @return bool
     */
    public function isNoCache()
    {
        return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma');
    }
}