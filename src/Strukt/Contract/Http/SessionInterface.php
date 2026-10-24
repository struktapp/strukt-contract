<?php

namespace Strukt\Contract\Http;

/**
 * Describes the per-client key/value store used to persist state between requests.
 */
interface SessionInterface
{
    /**
     * Reads one value.
     *
     * @param string $key Session key.
     * @param mixed $default Value returned when the key is absent.
     * @return mixed Stored value, or the default when absent.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Writes one value.
     *
     * @param string $key Session key.
     * @param mixed $value Value to store.
     * @return static This session.
     */
    public function set(string $key, mixed $value = null): static;

    /**
     * Checks whether a key is present.
     *
     * @param string $key Session key.
     * @return bool True when the key is present, even when its value is null.
     */
    public function has(string $key): bool;

    /**
     * Removes one value.
     *
     * @param string $key Session key.
     * @return static This session.
     */
    public function remove(string $key): static;

    /**
     * Removes every value.
     *
     * @return static This session.
     */
    public function clear(): static;

    /**
     * Reads every value.
     *
     * @return array<string, mixed> Values keyed by session key.
     */
    public function all(): array;

    /**
     * Writes one value for the next request only.
     *
     * @param string $key Flash key.
     * @param mixed $value Value to store.
     * @return static This session.
     */
    public function flash(string $key, mixed $value = null): static;

    /**
     * Reads one flash value, marking it as consumed.
     *
     * @param string $key Flash key.
     * @param mixed $default Value returned when the key is absent.
     * @return mixed Flash value, or the default when absent.
     */
    public function getFlash(string $key, mixed $default = null): mixed;

    /**
     * Reads every flash value, marking them as consumed.
     *
     * @return array<string, mixed> Flash values keyed by flash key.
     */
    public function pullFlash(): array;
}
