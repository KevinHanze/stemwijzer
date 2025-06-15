<?php

declare(strict_types=1);

namespace Framework\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Framework\Http\Stream;

final class Request implements ServerRequestInterface
{
    private string $method;
    private UriInterface $uri;
    private array $headers;
    private StreamInterface $body;
    private string $protocolVersion;
    private array $serverParams;
    private array $queryParams = [];
    private array|object|null $parsedBody = null;
    private array $cookieParams = [];
    private array $attributes = [];

    public function __construct(
        string $method,
        UriInterface $uri,
        array $headers = [],
        StreamInterface $body = new Stream(),
        string $protocolVersion = '1.1',
        array $serverParams = []
    ) {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
        $this->protocolVersion = $protocolVersion;
        $this->serverParams = $serverParams;
    }

    public function getMethod(): string { return $this->method; }

    public function getUri(): UriInterface { return $this->uri; }

    public function getHeaders(): array { return $this->headers; }

    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function getBody(): StreamInterface { return $this->body; }

    public function getProtocolVersion(): string { return $this->protocolVersion; }

    public function withProtocolVersion($version): Request
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;
        return $clone;
    }

    public function withMethod($method): Request
    {
        $clone = clone $this;
        $clone->method = strtoupper($method);
        return $clone;
    }

    public function withUri(UriInterface $uri, $preserveHost = false): Request
    {
        $clone = clone $this;
        $clone->uri = $uri;
        return $clone;
    }

    public function withHeader($name, $value): Request
    {
        $clone = clone $this;
        $clone->headers[strtolower($name)] = (array) $value;
        return $clone;
    }

    public function withAddedHeader($name, $value): Request
    {
        $clone = clone $this;
        $clone->headers[strtolower($name)][] = $value;
        return $clone;
    }

    public function withoutHeader($name): Request
    {
        $clone = clone $this;
        unset($clone->headers[strtolower($name)]);
        return $clone;
    }

    public function withBody(StreamInterface $body): Request
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    public function getServerParams(): array { return $this->serverParams; }

    public function getQueryParams(): array { return $this->queryParams; }

    public function withQueryParams(array $query): Request
    {
        $clone = clone $this;
        $clone->queryParams = $query;
        return $clone;
    }

    public function getParsedBody(): array|object|null { return $this->parsedBody; }

    public function withParsedBody($data): Request
    {
        $clone = clone $this;
        $clone->parsedBody = $data;
        return $clone;
    }

    public function getCookieParams(): array { return $this->cookieParams; }

    public function withCookieParams(array $cookies): Request
    {
        $clone = clone $this;
        $clone->cookieParams = $cookies;
        return $clone;
    }

    public function getAttributes(): array { return $this->attributes; }

    public function getAttribute($name, $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    public function withAttribute($name, $value): Request
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    public function withoutAttribute($name): Request
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);
        return $clone;
    }

    public function getUploadedFiles(): array
    {
        return []; // voor nu leeg
    }

    public function withUploadedFiles(array $uploadedFiles): Request
    {
        return $this; // dummy voorlopig
    }

    public function getRequestTarget(): string
    {
        // TODO: Implement getRequestTarget() method.
        return "'";
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        // TODO: Implement withRequestTarget() method.
    }
}
