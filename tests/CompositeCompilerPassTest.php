<?php

declare(strict_types=1);

namespace Setono\CompositeCompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CompositeCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CompositeCompilerPass('composite', 'tag'));
    }

    /**
     * @test
     */
    public function it_registers_services(): void
    {
        $compositeService = new Definition();
        $this->setDefinition('composite', $compositeService);

        $taggedService = new Definition();
        $taggedService->addTag('tag');
        $this->setDefinition('tagged_service', $taggedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'composite',
            'add',
            [
                new Reference('tagged_service'),
            ],
        );
    }

    /**
     * @test
     */
    public function it_does_nothing_if_composite_service_is_not_defined(): void
    {
        $taggedService = new Definition();
        $taggedService->addTag('tag');
        $this->setDefinition('tagged_service', $taggedService);

        $this->compile();

        $this->assertContainerBuilderNotHasService('composite');
    }
}
