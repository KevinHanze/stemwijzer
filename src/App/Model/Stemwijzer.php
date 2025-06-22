<?php

namespace App\Model;

class Stemwijzer
{
    public function __construct(
        public ?int   $id,
        public int $userId,
        public ?string $submittedAt,
        public string $matchedParties
    ) {}
}
