<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Http\RequestFactory;
use Framework\Http\Response;
use Framework\Http\Stream;

// Maak request object vanuit globals
$request = RequestFactory::fromGlobals();

// Stel inhoud samen op basis van de request
$bodyText = "✅ Je gebruikte methode: " . $request->getMethod() . "\n";
$bodyText .= "🔗 URI: " . (string) $request->getUri() . "\n";
$bodyText .= "📝 Query: " . json_encode($request->getQueryParams()) . "\n";
$bodyText .= "📩 Form: " . json_encode($request->getParsedBody()) . "\n";
$bodyText .= "🍪 Cookies: " . json_encode($request->getCookieParams()) . "\n";

// Bouw response
$response = new Response(
    200,
    ['Content-Type' => ['text/plain']],
    new Stream($bodyText)
);

// Verstuur HTTP headers
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}

// Verstuur body
echo $response->getBody();
