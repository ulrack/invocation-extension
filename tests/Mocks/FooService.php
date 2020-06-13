<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\InvocationExtension\Tests\Mocks;

use Exception;

class FooService
{
    /**
     * Mock method which always throws an error.
     *
     * @param string $input
     *
     * @return string
     */
    public function getFoo(string $input): string
    {
        throw new Exception($input);
    }
}
