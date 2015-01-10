<?php

namespace Phapi\Exception\Success;

use Phapi\Exception\Success;
use Phapi\Http\Response;

/**
 * Class Created
 *
 * Response code 201
 *
 * New resource created
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Created extends Success {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_CREATED;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Created';

}