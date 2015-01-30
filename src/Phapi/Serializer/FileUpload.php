<?php

namespace Phapi\Serializer;

use Phapi\Serializer;

/**
 * FileUpload Serializer
 *
 * Class handling file uploads
 *
 * @category Serializer
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class FileUpload extends Serializer
{

    /**
     * Mime types that the handler can handle
     *
     * @var array
     */
    protected $contentTypes = [
        'application/octet-stream'
    ];

    /**
     * Extra content types only for output
     *
     * @var array
     */
    protected $acceptOnlyTypes = [
        'image/jpg',
        'image/jpeg',
        'image/gif',
        'image/png'
    ];

    /**
     * Just pass the input back
     *
     * @param $input
     * @return array|mixed
     */
    public function deserialize($input)
    {
        return $input;
    }

    /**
     * Pass input back
     *
     * @param $input
     * @return mixed|string
     */
    public function serialize($input)
    {
        return $input[0];
    }
}