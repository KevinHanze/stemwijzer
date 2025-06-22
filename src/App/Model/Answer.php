<?php

namespace App\Model;

class Answer
{
    public function __construct(
        public ?int   $id,
        public int    $statementId,
        public int    $userId,
        public string $answer,
        public ?string $reason
    ) {}
}
