<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Error
 *
 * Exceptions that are of an error type
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Error extends Exception {

    public function __construct(
        $message = null,
        $code = null,
        \Exception $previous = null,
        $link = null,
        $logInformation = null,
        $description = null
    ) {
        parent::__construct($message, $code, $previous, $link, $logInformation, $description);
    }

}