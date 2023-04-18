<?php

declare(strict_types=1);

namespace Setono\CompositeCompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CompositeCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function __construct(
        /**
         * This is the service id of composite service, i.e. the service that holds the services tagged by $tag
         */
        private readonly string $compositeServiceId,

        /**
         * Services with this tag will be added to the composite service
         */
        private readonly string $tag,

        /**
         * The name of the method to call when adding a tagged service to the composite service
         */
        private readonly string $method = 'add',
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has($this->compositeServiceId)) {
            return;
        }

        $composite = $container->getDefinition($this->compositeServiceId);

        foreach ($this->findAndSortTaggedServices($this->tag, $container) as $service) {
            $composite->addMethodCall($this->method, [$service]);
        }
    }
}
