<?php

declare(strict_types=1);

namespace Framework\Http;

use Psr\Http\Message\UriInterface;

final class Uri implements UriInterface
{
    private string $scheme;
    private string $host;
    private ?int $port;
    private string $userInfo;
    private string $path;
    private string $query;
    private string $fragment;

    public function __construct(
        string $scheme = '',
        string $host = '',
        ?int $port = null,
        string $userInfo = '',
        string $path = '',
        string $query = '',
        string $fragment = ''
    ) {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->userInfo = $userInfo;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    public function getScheme(): string { return $this->scheme; }

    public function getHost(): string { return $this->host; }

    public function getPort(): ?int { return $this->port; }

    public function getUserInfo(): string { return $this->userInfo; }

    public function getPath(): string { return $this->path; }

    public function getQuery(): string { return $this->query; }

    public function getFragment(): string { return $this->fragment; }

    public function getAuthority(): string
    {
        $authority = $this->host;

        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function __toString(): string
    {
        $uri = '';

        if ($this->scheme !== '') {
            $uri .= $this->scheme . '://';
        }

        $uri .= $this->getAuthority();
        $uri .= $this->path;

        if ($this->query !== '') {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment !== '') {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }

    // ---- Immutability (withX) methods ----

    public function withScheme($scheme): Uri
    {
        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }

    public function withHost($host): Uri
    {
        $clone = clone $this;
        $clone->host = $host;
        return $clone;
    }

    public function withPort($port): Uri
    {
        $clone = clone $this;
        $clone->port = $port;
        return $clone;
    }

    public function withUserInfo($user, $password = null): Uri
    {
        $clone = clone $this;
        $clone->userInfo = $password !== null ? "$user:$password" : $user;
        return $clone;
    }

    public function withPath($path): Uri
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    public function withQuery($query): Uri
    {
        $clone = clone $this;
        $clone->query = $query;
        return $clone;
    }

    public function withFragment($fragment): Uri
    {
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }
}
