<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Redirect
 *
 * Exceptions that are of an error type
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Redirect extends Exception {

    public function __construct($location)
    {
        parent::__construct(null, null, null, null, null, null, $location);
    }
}
