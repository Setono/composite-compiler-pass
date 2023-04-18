<?php

declare(strict_types=1);

namespace Setono\CompositeCompilerPass;

use PHPUnit\Framework\TestCase;

final class CompositeServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_allows_to_add(): void
    {
        $service = new Service();
        $compositeService = new ConcreteCompositeService();
        $compositeService->add($service);

        self::assertCount(1, $compositeService->getServices());
        self::assertSame($service, $compositeService->getServices()[0]);
    }
}

/**
 * @extends CompositeService<Service>
 */
final class ConcreteCompositeService extends CompositeService
{
    public function getServices(): array
    {
        return $this->services;
    }
}

final class Service
{
}
