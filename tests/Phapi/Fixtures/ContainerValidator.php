<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Tests\Fixtures;

use Phapi\Contract\Container;

class ContainerValidator implements Container\Validator {

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function validate($value)
    {
        return $value;
    }

}