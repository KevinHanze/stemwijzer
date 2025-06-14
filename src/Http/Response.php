<?php

declare(strict_types=1);

namespace Framework\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class Response implements ResponseInterface
{
    private int $statusCode;
    private string $reasonPhrase;
    private array $headers;
    private string $protocolVersion;
    private StreamInterface $body;

    public function __construct(
        int $statusCode = 200,
        array $headers = [],
        ?StreamInterface $body = null,
        string $protocolVersion = '1.1',
        string $reasonPhrase = ''
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body ?? new Stream('');
        $this->protocolVersion = $protocolVersion;
        $this->reasonPhrase = $reasonPhrase !== ''
            ? $reasonPhrase
            : ReasonPhrases::get($statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader($name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    public function getHeaderLine($name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withProtocolVersion($version): Response
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;
        return $clone;
    }

    public function withHeader($name, $value): Response
    {
        $clone = clone $this;
        $clone->headers[strtolower($name)] = (array) $value;
        return $clone;
    }

    public function withAddedHeader($name, $value): Response
    {
        $clone = clone $this;
        $clone->headers[strtolower($name)][] = $value;
        return $clone;
    }

    public function withoutHeader($name): Response
    {
        $clone = clone $this;
        unset($clone->headers[strtolower($name)]);
        return $clone;
    }

    public function withStatus($code, $reasonPhrase = ''): Response
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;
        return $clone;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): Response
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }
}
