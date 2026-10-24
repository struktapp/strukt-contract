<?php

namespace Strukt;

use Strukt\Contract\UserInterface;

/**
 * In-memory user record satisfying the user contract.
 *
 * Every field is nullable so an anonymous or partially hydrated user stays
 * representable. The password is write-only: it is never returned by a
 * getter and never appears in {@see self::toArray()}.
 */
class User implements UserInterface
{
    /** @var array<string, string|null> Public fields keyed by field name. */
    protected const FIELDS = [
        'id',
        'username',
        'email',
        'token',
    ];

    protected ?string $id = null;
    protected ?string $username = null;
    protected ?string $email = null;
    protected ?string $token = null;
    protected ?string $password = null;

    /**
     * @param string|null $id Unique identifier.
     * @param string|null $username Login name.
     * @param string|null $email Email address.
     * @param string|null $token Bearer token.
     * @param string|null $password Password or hash.
     */
    public function __construct(
        ?string $id = null,
        ?string $username = null,
        ?string $email = null,
        ?string $token = null,
        ?string $password = null,
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->token = $token;
        $this->password = $password;
    }

    /**
     * Reads the unique identifier.
     *
     * @return string|null Identifier, or null when the user is not persisted.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Assigns the unique identifier.
     *
     * @param string|null $id Identifier, or null to clear it.
     * @return void
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * Reads the login name.
     *
     * @return string|null Username, or null when unset.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Assigns the login name.
     *
     * @param string|null $username Username, or null to clear it.
     * @return void
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * Reads the email address.
     *
     * @return string|null Email address, or null when unset.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Assigns the email address.
     *
     * @param string|null $email Email address, or null to clear it.
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * Reads the bearer token.
     *
     * @return string|null Token, or null when the user is not authenticated.
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Assigns the bearer token.
     *
     * @param string|null $token Token, or null to clear it.
     * @return void
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * Assigns the password hash or plain password.
     *
     * @param string|null $password Password or hash, or null to clear it.
     * @return void
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * Checks whether a password has been assigned.
     *
     * @return bool True when a password value is present.
     */
    public function hasPassword(): bool
    {
        return $this->password !== null;
    }

    /**
     * Verifies a plain password against the stored value.
     *
     * @param string $password Plain password to verify.
     * @return bool True when the password matches the stored value.
     */
    public function verifyPassword(string $password): bool
    {
        if ($this->password === null) {
            return false;
        }

        return password_verify($password, $this->password) || hash_equals($this->password, $password);
    }

    /**
     * Lists the public field names.
     *
     * @return list<string> Field names, without a method prefix.
     */
    public static function fields(): array
    {
        return self::FIELDS;
    }

    /**
     * Checks whether a named field carries a non-null value.
     *
     * The password is not addressable here; use {@see self::hasPassword()}.
     *
     * @param string $field Field name, without a method prefix.
     * @return bool True when the field resolves to a non-null value.
     */
    public function has(string $field): bool
    {
        return $this->get($field) !== null;
    }

    /**
     * Reads a named field.
     *
     * The password is not addressable here; use {@see self::hasPassword()}
     * to test for one and {@see self::verifyPassword()} to check a candidate.
     *
     * @param string $field Field name, without a method prefix.
     * @param mixed $default Value returned when the field is unknown.
     * @return mixed Field value, or the default when unknown or withheld.
     */
    public function get(string $field, mixed $default = null): mixed
    {
        return match (strtolower($field)) {
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'token' => $this->token,
            'password' => $this->password === null ? null : $default,
            default => $default,
        };
    }

    /**
     * Exports the public attributes of the user.
     *
     * Credentials are omitted: the password is never readable through the
     * contract and the token is a bearer secret. Use {@see self::credentials()}
     * when those are required.
     *
     * @return array<string, mixed> Non-null public attributes keyed by field name.
     */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /**
     * Exports every field, including the bearer token.
     *
     * The password is still withheld. Treat the result as sensitive.
     *
     * @return array<string, mixed> Non-null fields keyed by field name.
     */
    public function credentials(): array
    {
        return array_filter([
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'token' => $this->token,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
