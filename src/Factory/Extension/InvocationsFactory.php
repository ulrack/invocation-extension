<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\InvocationExtension\Factory\Extension;

use Throwable;
use Ulrack\Services\Exception\InvalidArgumentException;
use GrizzIt\ObjectFactory\Common\MethodReflectorInterface;
use Ulrack\Services\Exception\DefinitionNotFoundException;
use Ulrack\Services\Common\AbstractServiceFactoryExtension;

class InvocationsFactory extends AbstractServiceFactoryExtension
{
    /**
     * Contains the results which have been invoked.
     *
     * @var array
     */
    private $services = [];

    /**
     * Register a value to a service key.
     *
     * @param string $serviceKey
     * @param mixed $value
     *
     * @return void
     */
    public function registerService(string $serviceKey, $value): void
    {
        $this->services[$serviceKey] = $value;
    }

    /**
     * Invoke the invocation and return the result.
     *
     * @param string $serviceKey
     *
     * @return mixed
     *
     * @throws InvalidArgumentException When the arguments provided for the class are invalid.
     * @throws DefinitionNotFoundException When the definition can not be found.
     */
    public function create(string $serviceKey)
    {
        $serviceKey = $this->preCreate(
            $serviceKey,
            $this->getParameters()
        )['serviceKey'];

        $internalKey = preg_replace(
            sprintf('/^%s\\./', preg_quote($this->getKey())),
            '',
            $serviceKey,
            1
        );

        if (isset($this->services[$internalKey])) {
            return $this->postCreate(
                $serviceKey,
                $this->services[$internalKey],
                $this->getParameters()
            )['return'];
        }

        $services = $this->getServices()[$this->getKey()];
        if (isset($services[$internalKey])) {
            $service = $services[$internalKey];
            $parameters = [];
            /** @var MethodReflectorInterface $methodReflector */
            $methodReflector = $this->getInternalService('method-reflector');
            $subject = $this->superCreate($service['service']);
            $parametersAnalysis = $methodReflector->reflect(
                get_class($subject),
                $service['method']
            );

            foreach ($parametersAnalysis as $parameterName => $parameterAnalysis) {
                $parameterValue = $parameterAnalysis['default'];

                if (isset($service['parameters'][$parameterName])) {
                    $parameterValue = $this->resolveReference(
                        $service['parameters'][$parameterName]
                    );
                }

                $parameters[] = $parameterValue;
            }

            try {
                $result = call_user_func_array(
                    [$subject, $service['method']],
                    $parameters
                );

                if (!isset($service['cache']) || $service['cache']) {
                    $this->registerService($internalKey, $result);
                }

                return $this->postCreate(
                    $serviceKey,
                    $result,
                    $this->getParameters()
                )['return'];
            } catch (Throwable $exception) {
                throw new InvalidArgumentException(
                    $parameters,
                    get_class($subject),
                    $exception
                );
            }
        }

        throw new DefinitionNotFoundException($serviceKey);
    }

    /**
     * Resolves a reference to another service if applicable.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function resolveReference($value)
    {
        if (is_string($value) && $this->isReference($value)) {
            $value = $this->superCreate(trim($value, '@{}'));
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->resolveReference($item);
            }
        }

        return $value;
    }
}
