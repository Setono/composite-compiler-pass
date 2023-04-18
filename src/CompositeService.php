<?php

declare(strict_types=1);

namespace Setono\CompositeCompilerPass;

/**
 * @template T of object
 */
abstract class CompositeService
{
    /** @var list<T> */
    protected array $services = [];

    /**
     * @param T $service
     */
    public function add(object $service): void
    {
        $this->services[] = $service;
    }
}
