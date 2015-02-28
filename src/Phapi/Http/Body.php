<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Http;

use Psr\Http\Message\StreamableInterface;

/**
 * Class Body
 *
 * Describes streamable message body content.
 *
 * Typically, an instance will wrap a PHP stream; this class provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 *
 * @category Phapi
 * @package  Phapi\Http
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Body implements StreamableInterface {

    /**
     * Resource modes
     *
     * @var  array
     * @link http://php.net/manual/function.fopen.php
     */
    protected $modes = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+']
    ];

    protected $resource;

    protected $readable;
    protected $writable;
    protected $seekable;

    public function __construct($stream, $mode = 'r')
    {
        $this->attach($stream, $mode);
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->isReadable() ? stream_get_contents($this->resource, -1, 0) : '';
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (! $this->resource) {
            return;
        }

        fclose($this->resource);
        $this->detach();
    }

    /**
     * Separates any underlying resources from the stream. After the stream
     * has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $resource = $this->resource;

        $this->resource = null;
        $this->seekable = null;
        $this->writable = null;
        $this->readable = null;

        return $resource;
    }

    /**
     * Attach a new resource to the instance
     *
     * @param $stream
     * @param $mode
     * @throws \InvalidArgumentException for invalid resource.
     */
    public function attach($stream, $mode = 'r')
    {
        // Detach any existing resource
        $this->detach();

        if (is_resource($stream)) {
            $this->resource = $stream;
        } elseif (is_string($stream)) {
            $this->resource = @fopen($stream, $mode);

            if (!$this->resource) {
                throw new \InvalidArgumentException(
                    'Unsupported stream provided; must be a string stream identifier or resource'
                );
            }
        } else {
            throw new \InvalidArgumentException(
                'Unsupported stream provided; must be a string stream identifier or resource'
            );
        }

        $this->readable = false;
        foreach ($this->modes['readable'] as $read) {
            if (strpos($mode, $read) === 0) {
                $this->readable = true;
                break;
            }
        }
        // Is writable?
        $this->writable = false;
        foreach ($this->modes['writable'] as $write) {
            if (strpos($mode, $write) === 0) {
                $this->writable = true;
                break;
            }
        }

        $this->seekable = $this->getMetadata('seekable');
    }

    /**
     * Get the size of the stream if known
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        $stats = fstat($this->resource);
        return isset($stats['size']) ? $stats['size'] : null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int|bool Position of the file pointer or false on error.
     */
    public function tell()
    {
        return (!$this->isReadable()) ? false : ftell($this->resource);
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return (!$this->isReadable()) ? true : feof($this->resource);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return is_null($this->seekable) ? false : $this->seekable;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (! $this->isReadable() || ! $this->isSeekable()) {
            return false;
        }
        $result = fseek($this->resource, $offset, $whence);
        return (0 === $result);
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will return FALSE, indicating
     * failure; otherwise, it will perform a seek(0), and return the status of
     * that operation.
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function rewind()
    {
        if (!$this->isSeekable()) {
            return false;
        }
        $result = fseek($this->resource, 0);
        return (0 === $result);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return is_null($this->writable) ? false : $this->writable;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int|bool Returns the number of bytes written to the stream on
     *     success or FALSE on failure.
     */
    public function write($string)
    {
        return $this->isWritable() ? fwrite($this->resource, $string) : false;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return is_null($this->readable) ? false : $this->readable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string|false Returns the data read from the stream, false if
     *     unable to read or if an error occurs.
     */
    public function read($length)
    {
        return $this->isReadable() ? fread($this->resource, $length) : false;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     */
    public function getContents()
    {
        return ($this->isReadable()) ? stream_get_contents($this->resource) : '';
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if (null === $key) {
            return stream_get_meta_data($this->resource);
        }

        $metadata = stream_get_meta_data($this->resource);
        if (! array_key_exists($key, $metadata)) {
            return null;
        }

        return $metadata[$key];
    }
}