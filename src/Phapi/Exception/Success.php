<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Redirect
 *
 * Exceptions that are of an redirect type. These
 * aren't really errors, just a way of telling the
 * application that a redirect should be sent to
 * the client.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Success extends Exception {

    public function __construct()
    {
        parent::__construct();
    }
}
