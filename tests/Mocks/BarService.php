<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\InvocationExtension\Tests\Mocks;

class BarService
{
    /**
     * Mock method which returns its' input.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function getFoo($input)
    {
        return $input;
    }
}
