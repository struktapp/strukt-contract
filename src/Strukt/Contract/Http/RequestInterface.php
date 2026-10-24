<?php

namespace Strukt\Contract\Http;

use Strukt\Contract\UserInterface;

/**
 * Describes an inbound HTTP request.
 */
interface RequestInterface
{
    /**
     * Reads the request method, uppercased.
     *
     * @return string Method such as `GET`, `POST`, or `DELETE`.
     */
    public function method(): string;

    /**
     * Reads the full request target.
     *
     * @return string Request target including the query string when present.
     */
    public function uri(): string;

    /**
     * Reads the path component without the query string.
     *
     * @return string Absolute path such as `/users/7`.
     */
    public function path(): string;

    /**
     * Reads one query-string value.
     *
     * @param string|null $key Parameter name, or null to return every value.
     * @param mixed $default Value returned when the parameter is absent.
     * @return mixed Parameter value, every value, or the default.
     */
    public function query(?string $key = null, mixed $default = null): mixed;

    /**
     * Reads one request header.
     *
     * @param string|null $name Header name, or null to return every header.
     * @param string|null $default Value returned when the header is absent.
     * @return string|array<string, string>|null Header value, every header, or the default.
     */
    public function header(?string $name = null, ?string $default = null): string|array|null;

    /**
     * Checks whether a header is present.
     *
     * @param string $name Header name.
     * @return bool True when the header is present.
     */
    public function hasHeader(string $name): bool;

    /**
     * Reads the raw request body.
     *
     * @return string Body bytes, or an empty string when there is no body.
     */
    public function body(): string;

    /**
     * Reads a body or query parameter.
     *
     * Body values take precedence over query-string values.
     *
     * @param string|null $key Parameter name, or null to return every value.
     * @param mixed $default Value returned when the parameter is absent.
     * @return mixed Parameter value, every value, or the default.
     */
    public function input(?string $key = null, mixed $default = null): mixed;

    /**
     * Reads every parameter, merging query-string and body values.
     *
     * @return array<string, mixed> Parameters keyed by name.
     */
    public function all(): array;

    /**
     * Checks whether the request uses one of the given methods.
     *
     * @param string ...$methods Methods to compare, case-insensitively.
     * @return bool True when the request method matches any argument.
     */
    public function isMethod(string ...$methods): bool;

    /**
     * Reads one uploaded file.
     *
     * @param string $name Form field name of the upload.
     * @return UploadedFileInterface|null Uploaded file, or null when absent.
     */
    public function file(string $name): ?UploadedFileInterface;

    /**
     * Reads the authenticated user bound to this request.
     *
     * @return UserInterface|null Authenticated user, or null for a guest.
     */
    public function user(): ?UserInterface;

    /**
     * Binds the authenticated user to this request.
     *
     * @param UserInterface|null $user User to bind, or null to clear the binding.
     * @return void
     */
    public function setUser(?UserInterface $user = null): void;
}
