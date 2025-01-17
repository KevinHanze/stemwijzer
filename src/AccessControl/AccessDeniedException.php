<?php

namespace Framework\AccessControl;

use Framework\Http\Exception;

/**
 * An exception that is thrown when a user does not have access to a resource.
 */
class AccessDeniedException extends Exception
{
    public function __construct()
    {
        parent::__construct(403);
    }
}