<?php

namespace Framework\Http;

/**
 * An exception that should be rendered as an HTTP response with a non-200 status code.
 */
class Exception extends \Exception
{
    public function __construct(int $code = 500, ?string $message = null, ?\Throwable $previous = null)
    {
        parent::__construct($message ?? ReasonPhrases::get($code), $code, $previous);
    }
}