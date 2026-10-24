<?php

namespace Tests\Fixtures;

use Strukt\Contract\Http\ResponseInterface;

/** Minimal in-memory response used to exercise the response contract. */
final class Response implements ResponseInterface
{
    /** @var array<string, list<string>> Header values keyed by lowercased name. */
    private array $headers = [];

    /** @var list<string> Lines captured instead of written to the output stream. */
    private array $sent = [];

    public function __construct(private int $status = 200, private string $body = '')
    {
    }

    public function status(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        if ($status < 100 || $status > 599) {
            throw new \InvalidArgumentException(sprintf('Status %d is out of range!', $status));
        }

        $this->status = $status;

        return $this;
    }

    public function setHeader(string $name, string $value): static
    {
        $this->assertName($name);
        $this->headers[strtolower($name)] = [$value];

        return $this;
    }

    public function addHeader(string $name, string $value): static
    {
        $this->assertName($name);
        $this->headers[strtolower($name)][] = $value;

        return $this;
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    public function header(?string $name = null, ?string $default = null): string|array|null
    {
        if ($name === null) {
            return $this->headers;
        }

        $key = strtolower($name);

        return $this->headers[$key][0] ?? $default;
    }

    public function removeHeader(string $name): static
    {
        unset($this->headers[strtolower($name)]);

        return $this;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function setJson(mixed $data): static
    {
        $this->setHeader('Content-Type', 'application/json');

        return $this->setBody((string) json_encode($data));
    }

    public function send(): void
    {
        $this->sent = [sprintf('HTTP/1.1 %d', $this->status), $this->body];
    }

    /** @return list<string> Lines captured by the last send(). */
    public function sent(): array
    {
        return $this->sent;
    }

    /** @throws \InvalidArgumentException When the header name is empty. */
    private function assertName(string $name): void
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('A header name is required!');
        }
    }
}
