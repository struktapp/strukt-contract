<?php

namespace Strukt\Contract;

/**
 * Describes a service provider that contributes bindings to a container.
 */
interface ProviderInterface
{
    /**
     * Registers bindings and aliases with the container.
     *
     * @return void
     */
    public function register(): void;

    /**
     * Runs after every provider has registered its bindings.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Lists the identifiers this provider binds.
     *
     * @return list<string> Container identifiers owned by this provider.
     */
    public function provides(): array;
}
