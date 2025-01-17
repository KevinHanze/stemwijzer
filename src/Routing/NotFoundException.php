<?php

namespace Framework\Routing;

use Framework\Http\Exception;

/**
 * An exception that is thrown when no route is found for a request.
 */
class NotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct(404);
    }
}