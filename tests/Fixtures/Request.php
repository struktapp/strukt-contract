<?php

namespace Tests\Fixtures;

use Strukt\Contract\Http\RequestInterface;
use Strukt\Contract\Http\UploadedFileInterface;
use Strukt\Contract\UserInterface;

/** Minimal in-memory request used to exercise the request contract. */
final class Request implements RequestInterface
{
    /**
     * @param array<string, mixed> $query Query-string values.
     * @param array<string, string> $headers Header values.
     * @param array<string, mixed> $body Parsed body values.
     * @param array<string, UploadedFileInterface> $files Uploaded files.
     */
    public function __construct(
        private string $method,
        private string $uri,
        private array $query = [],
        private array $headers = [],
        private string $rawBody = '',
        private array $body = [],
        private array $files = [],
        private ?UserInterface $user = null,
    ) {
    }

    public function method(): string
    {
        return strtoupper($this->method);
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function path(): string
    {
        return (string) (parse_url($this->uri, PHP_URL_PATH) ?? '/');
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    public function header(?string $name = null, ?string $default = null): string|array|null
    {
        if ($name === null) {
            return $this->headers;
        }

        foreach ($this->headers as $header => $value) {
            if (strcasecmp($header, $name) === 0) {
                return $value;
            }
        }

        return $default;
    }

    public function hasHeader(string $name): bool
    {
        return $this->header($name) !== null;
    }

    public function body(): string
    {
        return $this->rawBody;
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->all();
        }

        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function isMethod(string ...$methods): bool
    {
        foreach ($methods as $method) {
            if (strcasecmp($this->method(), $method) === 0) {
                return true;
            }
        }

        return false;
    }

    public function file(string $name): ?UploadedFileInterface
    {
        return $this->files[$name] ?? null;
    }

    public function user(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user = null): void
    {
        $this->user = $user;
    }
}
