# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A single-purpose Symfony library that ships a reusable `CompilerPassInterface`. It removes the boilerplate of writing a custom compiler pass every time you build a [composite service](https://en.wikipedia.org/wiki/Composite_pattern) that collects tagged services. Distributed as the Composer package `setono/composite-compiler-pass`.

## Commands

Composer scripts (defined in `composer.json`):

- `composer phpunit` — run the test suite
- `composer analyse` — PHPStan static analysis (`phpstan.dist.neon`, `level: max`, scans `src` + `tests`; `phpstan-strict-rules` and `phpstan-phpunit` are auto-registered via `phpstan/extension-installer`)
- `composer check-style` / `composer fix-style` — ECS coding standard (check / autofix), config in `ecs.php` which imports the `sylius-labs/coding-standard` ruleset

Not wired into composer scripts, run directly:

- `vendor/bin/infection` — mutation testing; **fails below 100% MSI and 100% covered MSI** (`infection.json.dist`)
- Run a single test by method name: `vendor/bin/phpunit --filter it_registers_services`

## Architecture

Two classes, both under namespace `Setono\CompositeCompilerPass\` (`src/`). Tests reuse the same namespace (`autoload-dev` maps it to `tests/`), so a class and its test share the namespace.

- **`CompositeCompilerPass`** — the core. Constructed with `(string $compositeServiceId, string $tag, string $method = 'add')`. In `process()` it returns early if the composite service id isn't registered, otherwise it pulls every service tagged with `$tag`, sorts them via `PriorityTaggedServiceTrait::findAndSortTaggedServices()` (so tag `priority` is honored automatically), and appends a `$method` method call per service onto the composite definition. Consumers register it from their bundle's `build()`.
- **`CompositeService`** — optional `@template`-typed abstract base a consumer can extend for the composite itself. Holds `protected array $services` and an `add()` collector, giving Psalm/PHPStan the element type via the generic param.

The compiler pass does not require the composite to extend `CompositeService` — the two are independent and either can be used alone.

## Compatibility constraints

- `composer.json` requires `php: >=8.1` and `symfony/dependency-injection: ^6.4 || ^7.4 || ^8.0`. CI runs PHP 8.1–8.4 against both lowest and highest dependency sets.
- Source must stay **PHP 8.1-compatible** — that's the floor, even though newer PHP is installed locally. `config.platform.php` is pinned to `8.1.99` in `composer.json`, so `composer update` resolves dev tooling against 8.1; this is what keeps PHPUnit at `^10` and Infection at `^0.29` (their newer majors require PHP ≥8.2). Don't bump those past the 8.1 ceiling.
- The dev toolchain is **inlined**, not pulled via `setono/code-quality-pack` — the individual tools (PHPStan + extensions, ECS via `sylius-labs/coding-standard`, `ergebnis/composer-normalize`, PHPUnit, Infection) are listed directly in `require-dev`.
