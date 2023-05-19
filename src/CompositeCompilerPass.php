<?php

declare(strict_types=1);

namespace Setono\CompositeCompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CompositeCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private string $compositeServiceId;

    private string $tag;

    private string $method;

    /**
     * @param string $compositeServiceId This is the service id of composite service, i.e. the service that holds the services tagged by $tag
     * @param string $tag Services with this tag will be added to the composite service
     * @param string $method The name of the method to call when adding a tagged service to the composite service
     */
    public function __construct(string $compositeServiceId, string $tag, string $method = 'add')
    {
        $this->compositeServiceId = $compositeServiceId;
        $this->tag = $tag;
        $this->method = $method;
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
