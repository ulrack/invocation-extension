<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\InvocationExtension\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Services\Common\ServiceRegistryInterface;
use GrizzIt\Validator\Component\Logical\AlwaysValidator;
use Ulrack\InvocationExtension\Component\Compiler\Extension\InvocationsCompiler;

/**
 * @coversDefaultClass Ulrack\InvocationExtension\Component\Compiler\Extension\InvocationsCompiler
 */
class InvocationsCompilerTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::compile
     */
    public function testCompiler(): void
    {
        $services = [
            'invocations' => [
                'foo' => [
                    'service' => 'services.bar',
                    'method' => 'getFoo',
                    'parameters' => [
                        'input' => [
                            'output' => '@{parameters.foo}'
                        ]
                    ]
                ]
            ]
        ];

        $subject = new InvocationsCompiler(
            $this->createMock(ServiceRegistryInterface::class),
            'invocations',
            new AlwaysValidator(true),
            [],
            [$this, 'getHooks']
        );

        $this->assertEquals($services, $subject->compile($services));
    }

    /**
     * Required method.
     *
     * @return array
     */
    public function getHooks(): array
    {
        return [];
    }
}
