<?php

declare(strict_types=1);

namespace Framework\Http;

final class RequestFactory
{
    public static function fromGlobals(): Request
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $port = isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : null;
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $query = $_SERVER['QUERY_STRING'] ?? '';
        $fragment = '';

        $uri = new Uri($scheme, $host, $port, '', $path, $query, $fragment);

        $headers = self::getAllHeaders();

        $body = new Stream(file_get_contents('php://input') ?: '');

        $request = new Request(
            $method,
            $uri,
            $headers,
            $body,
            $_SERVER['SERVER_PROTOCOL'] ?? '1.1',
            $_SERVER
        );

        $request = $request->withQueryParams($_GET);
        $request = $request->withParsedBody($_POST ?: null);
        return $request->withCookieParams($_COOKIE);
    }

    private static function getAllHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = [$value];
            }
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = [$_SERVER['CONTENT_TYPE']];
        }

        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['content-length'] = [$_SERVER['CONTENT_LENGTH']];
        }

        return $headers;
    }
}
