<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\InvocationExtension\Component\Compiler\Extension;

use Ulrack\Services\Common\AbstractServiceCompilerExtension;

class InvocationsCompiler extends AbstractServiceCompilerExtension
{
    /**
     * Compile the services.
     *
     * @param array $services
     *
     * @return array
     */
    public function compile(array $services): array
    {
        return $this->postCompile(
            $services,
            $this->preCompile(
                $services,
                $this->getParameters()
            )['services'],
            $this->getParameters()
        )['return'];
    }
}
