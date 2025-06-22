<?php

namespace Framework\AccessControl;

class AnonymousUser extends User
{
    public function __construct()
    {
        parent::__construct(0,'anonymous', '', ['guest'], true);
    }
}