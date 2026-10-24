<?php

namespace Strukt;

use Strukt\Contract\Http\SessionInterface;

/**
 * Array-backed session store satisfying the session contract.
 *
 * Useful for testing, for CLI contexts, and as a reference implementation
 * for handlers that persist to a cookie, a file, or a database.
 */
class Session implements SessionInterface
{
    /** @var array<string, mixed> Values held for the current request. */
    protected array $values = [];

    /** @var array<string, mixed> Values held for the next request only. */
    protected array $flashes = [];

    /**
     * @param array<string, mixed> $values Values to preload.
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * Reads one value.
     *
     * @param string $key Session key.
     * @param mixed $default Value returned when the key is absent.
     * @return mixed Stored value, or the default when absent.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->values) ? $this->values[$key] : $default;
    }

    /**
     * Writes one value.
     *
     * @param string $key Session key.
     * @param mixed $value Value to store.
     * @return static This session.
     */
    public function set(string $key, mixed $value = null): static
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * Checks whether a key is present.
     *
     * @param string $key Session key.
     * @return bool True when the key is present, even when its value is null.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * Removes one value.
     *
     * @param string $key Session key.
     * @return static This session.
     */
    public function remove(string $key): static
    {
        unset($this->values[$key]);

        return $this;
    }

    /**
     * Removes every value.
     *
     * @return static This session.
     */
    public function clear(): static
    {
        $this->values = [];

        return $this;
    }

    /**
     * Reads every value.
     *
     * @return array<string, mixed> Values keyed by session key.
     */
    public function all(): array
    {
        return $this->values;
    }

    /**
     * Writes one value for the next request only.
     *
     * @param string $key Flash key.
     * @param mixed $value Value to store.
     * @return static This session.
     */
    public function flash(string $key, mixed $value = null): static
    {
        $this->flashes[$key] = $value;

        return $this;
    }

    /**
     * Reads one flash value, marking it as consumed.
     *
     * @param string $key Flash key.
     * @param mixed $default Value returned when the key is absent.
     * @return mixed Flash value, or the default when absent.
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, $this->flashes)) {
            return $default;
        }

        $value = $this->flashes[$key];
        unset($this->flashes[$key]);

        return $value;
    }

    /**
     * Reads every flash value, marking them as consumed.
     *
     * @return array<string, mixed> Flash values keyed by flash key.
     */
    public function pullFlash(): array
    {
        $flashes = $this->flashes;
        $this->flashes = [];

        return $flashes;
    }
}
