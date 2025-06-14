<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Http\Response;
use Framework\Http\Stream;

// Maak een nieuwe response
$response = new Response(
    404,
    ['Content-Type' => ['text/plain']],
    new Stream("Deze pagina bestaat niet.")
);

// Verstuur HTTP headers
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}

// Stuur body
echo $response->getBody();