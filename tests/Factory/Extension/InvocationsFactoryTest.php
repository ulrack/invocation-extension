<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\InvocationExtension\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use GrizzIt\Storage\Component\ObjectStorage;
use Ulrack\InvocationExtension\Tests\Mocks\BarService;
use Ulrack\InvocationExtension\Tests\Mocks\FooService;
use Ulrack\Services\Exception\InvalidArgumentException;
use Ulrack\Services\Exception\DefinitionNotFoundException;
use GrizzIt\ObjectFactory\Component\Reflector\MethodReflector;
use Ulrack\InvocationExtension\Factory\Extension\InvocationsFactory;

/**
 * @coversDefaultClass Ulrack\InvocationExtension\Factory\Extension\InvocationsFactory
 */
class InvocationsFactoryTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::create
     * @covers ::resolveReference
     * @covers ::registerService
     */
    public function testCachedCreate(): void
    {
        $subject = $this->createPartialMock(
            InvocationsFactory::class,
            [
                'preCreate',
                'getParameters',
                'getKey',
                'getServices',
                'getInternalService',
                'superCreate',
                'postCreate'
            ]
        );

        $subject
            ->expects(static::exactly(2))
            ->method('preCreate')
            ->willReturn(
                ['serviceKey' => 'invocations.foo']
            );

        $subject
            ->method('getParameters')
            ->willReturn([]);

        $subject
            ->method('getKey')
            ->willReturn('invocations');

        $subject
            ->expects(static::once())
            ->method('getServices')
            ->willReturn([
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
            ]);

        $subject
            ->expects(static::once())
            ->method('getInternalService')
            ->with('method-reflector')
            ->willReturn(new MethodReflector(new ObjectStorage()));

        $subject
            ->expects(static::exactly(2))
            ->method('superCreate')
            ->withConsecutive(['services.bar'], ['parameters.foo'])
            ->willReturnOnConsecutiveCalls(new BarService(), 'parameter');

        $subject
            ->expects(static::exactly(2))
            ->method('postCreate')
            ->with('invocations.foo', ['output' => 'parameter'], [])
            ->willReturn(
                ['return' => ['output' => 'parameter']]
            );

        $this->assertEquals(['output' => 'parameter'], $subject->create('invocations.foo'));

        $this->assertEquals(['output' => 'parameter'], $subject->create('invocations.foo'));
    }

    /**
     * @return void
     *
     * @covers ::create
     * @covers ::resolveReference
     */
    public function testErrorOutput(): void
    {
        $subject = $this->createPartialMock(
            InvocationsFactory::class,
            [
                'preCreate',
                'getParameters',
                'getKey',
                'getServices',
                'getInternalService',
                'superCreate',
                'postCreate'
            ]
        );

        $subject
            ->expects(static::once())
            ->method('preCreate')
            ->willReturn(
                ['serviceKey' => 'invocations.foo']
            );

        $subject
            ->method('getParameters')
            ->willReturn([]);

        $subject
            ->method('getKey')
            ->willReturn('invocations');

        $subject
            ->expects(static::once())
            ->method('getServices')
            ->willReturn([
                'invocations' => [
                    'foo' => [
                        'service' => 'services.bar',
                        'method' => 'getFoo',
                        'parameters' => [
                            'input' => 'output'
                        ]
                    ]
                ]
            ]);

        $subject
            ->expects(static::once())
            ->method('getInternalService')
            ->with('method-reflector')
            ->willReturn(new MethodReflector(new ObjectStorage()));

        $subject
            ->expects(static::once())
            ->method('superCreate')
            ->with('services.bar')
            ->willReturn(new FooService());

        $this->expectException(InvalidArgumentException::class);

        $subject->create('invocations.foo');
    }

    /**
     * @return void
     *
     * @covers ::create
     */
    public function testErrorNoDefinition(): void
    {
        $subject = $this->createPartialMock(
            InvocationsFactory::class,
            [
                'preCreate',
                'getParameters',
                'getKey',
                'getServices'
            ]
        );

        $subject
            ->expects(static::once())
            ->method('preCreate')
            ->willReturn(
                ['serviceKey' => 'invocations.bar']
            );

        $subject
            ->method('getParameters')
            ->willReturn([]);

        $subject
            ->method('getKey')
            ->willReturn('invocations');

        $subject
            ->expects(static::once())
            ->method('getServices')
            ->willReturn([
                'invocations' => [
                    'foo' => [
                        'service' => 'services.bar',
                        'method' => 'getFoo',
                        'parameters' => [
                            'input' => 'output'
                        ]
                    ]
                ]
            ]);

        $this->expectException(DefinitionNotFoundException::class);

        $subject->create('invocations.bar');
    }
}
