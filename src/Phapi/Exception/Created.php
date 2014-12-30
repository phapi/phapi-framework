<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Created
 *
 * Class representing a 201 response code
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Created extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 201;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Created';

}