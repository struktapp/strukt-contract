<?php

namespace Strukt\Contract\Http;

/**
 * Describes an outbound HTTP response under construction.
 */
interface ResponseInterface
{
    /**
     * Reads the HTTP status code.
     *
     * @return int Status code, or 0 before one has been assigned.
     */
    public function status(): int;

    /**
     * Assigns the HTTP status code.
     *
     * @param int $status Status code between 100 and 599.
     * @return static This response.
     *
     * @throws \InvalidArgumentException When the status code is out of range.
     */
    public function setStatus(int $status): static;

    /**
     * Assigns a header, replacing any existing value.
     *
     * @param string $name Header name.
     * @param string $value Header value.
     * @return static This response.
     *
     * @throws \InvalidArgumentException When the header name is empty.
     */
    public function setHeader(string $name, string $value): static;

    /**
     * Appends a header value, keeping any existing values.
     *
     * @param string $name Header name.
     * @param string $value Header value to append.
     * @return static This response.
     *
     * @throws \InvalidArgumentException When the header name is empty.
     */
    public function addHeader(string $name, string $value): static;

    /**
     * Checks whether a header is present.
     *
     * @param string $name Header name.
     * @return bool True when the header is present.
     */
    public function hasHeader(string $name): bool;

    /**
     * Reads one header.
     *
     * @param string|null $name Header name, or null to return every header.
     * @param string|null $default Value returned when the header is absent.
     * @return string|array<string, list<string>>|null Header value, every header, or the default.
     */
    public function header(?string $name = null, ?string $default = null): string|array|null;

    /**
     * Removes a header.
     *
     * @param string $name Header name.
     * @return static This response.
     */
    public function removeHeader(string $name): static;

    /**
     * Reads the response body.
     *
     * @return string Body bytes, or an empty string when no body is set.
     */
    public function body(): string;

    /**
     * Assigns the response body.
     *
     * @param string $body Body bytes.
     * @return static This response.
     */
    public function setBody(string $body): static;

    /**
     * Assigns a JSON body and its content type.
     *
     * @param mixed $data Value to encode.
     * @return static This response.
     */
    public function setJson(mixed $data): static;

    /**
     * Emits the status line, headers, and body to the output stream.
     *
     * @return void
     */
    public function send(): void;
}
