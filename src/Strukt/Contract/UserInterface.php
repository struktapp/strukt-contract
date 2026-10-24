<?php

namespace Strukt\Contract;

/**
 * Describes an authenticated principal.
 *
 * Implementations are free to resolve fields from a database, an identity
 * provider, or a plain in-memory record. Every accessor is nullable so an
 * anonymous or partially hydrated user stays representable.
 */
interface UserInterface
{
    /**
     * Reads the unique identifier.
     *
     * @return string|null Identifier, or null when the user is not persisted.
     */
    public function getId(): ?string;

    /**
     * Reads the login name.
     *
     * @return string|null Username, or null when unset.
     */
    public function getUsername(): ?string;

    /**
     * Reads the email address.
     *
     * @return string|null Email address, or null when unset.
     */
    public function getEmail(): ?string;

    /**
     * Reads the bearer token.
     *
     * @return string|null Token, or null when the user is not authenticated.
     */
    public function getToken(): ?string;

    /**
     * Assigns the login name.
     *
     * @param string|null $username Username, or null to clear it.
     * @return void
     */
    public function setUsername(?string $username): void;

    /**
     * Assigns the email address.
     *
     * @param string|null $email Email address, or null to clear it.
     * @return void
     */
    public function setEmail(?string $email): void;

    /**
     * Assigns the bearer token.
     *
     * @param string|null $token Token, or null to clear it.
     * @return void
     */
    public function setToken(?string $token): void;

    /**
     * Assigns the password hash or plain password.
     *
     * Exposed for hydration and verification only. The value is never
     * returned by a getter and is masked by the exporter.
     *
     * @param string|null $password Password or hash, or null to clear it.
     * @return void
     */
    public function setPassword(?string $password): void;

    /**
     * Checks whether a password has been assigned.
     *
     * @return bool True when a password value is present.
     */
    public function hasPassword(): bool;

    /**
     * Checks whether a named field carries a non-null value.
     *
     * @param string $field Field name, without a method prefix.
     * @return bool True when the field resolves to a non-null value.
     */
    public function has(string $field): bool;

    /**
     * Reads a named field.
     *
     * @param string $field Field name, without a method prefix.
     * @param mixed $default Value returned when the field is unknown.
     * @return mixed Field value, or the default when unknown.
     */
    public function get(string $field, mixed $default = null): mixed;

    /**
     * Exports the public attributes of the user.
     *
     * Implementations must not include the password in the result.
     *
     * @return array<string, mixed> Public attributes keyed by field name.
     */
    public function toArray(): array;
}
