<?php


namespace App\Model;

class UserAnswer
{
    public function __construct(
        public ?int    $id,
        public int     $statementId,
        public int     $userId,
        public string  $answer,
        public int $stemwijzerId
    ) {}
}

