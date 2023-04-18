# Composite Compiler Pass for Symfony

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

When you use composite services you find yourself writing the same compiler pass over and over again.
This library will give you the compiler pass you need so you don't have to think about it again.

## Installation

```bash
composer require setono/composite-compiler-pass
```

## Usage

Let's presume you have a composite service like this:

```php
<?php
final class YourCompositeService
{
    /**
     * @var list<object>
     */
    private array $taggedServices = [];

    public function add(object $taggedService): void
    {
        $this->taggedServices[] = $taggedService;
    }
}
```

that has the service id `your_bundle.your_service`,
and you want to add services that are tagged with `tag` automatically to this composite service.

In your bundle class you do this:

```php
<?php

use Setono\CompositeCompilerPass\CompositeCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class YourBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CompositeCompilerPass('your_bundle.your_service', 'tag'));
    }
}
```

**NOTICE** You can even define your tagged services with a priority and they will be automatically sorted before
being added to the composite service. This is thanks to the `Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait`
used under the hood.

### Composite service included

The library also comes with a small abstract class you can base your composite service on if you wish.
The class is named `CompositeService` and you can find it [here](src/CompositeService.php).

[ico-version]: https://poser.pugx.org/setono/composite-compiler-pass/v/stable
[ico-license]: https://poser.pugx.org/setono/composite-compiler-pass/license
[ico-github-actions]: https://github.com/Setono/composite-compiler-pass/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/composite-compiler-pass/branch/master/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fcomposite-compiler-pass%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/composite-compiler-pass
[link-github-actions]: https://github.com/Setono/composite-compiler-pass/actions
[link-code-coverage]: https://codecov.io/gh/Setono/composite-compiler-pass
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/composite-compiler-pass/master
