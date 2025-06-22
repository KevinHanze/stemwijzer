<?php

namespace App\Repository;

use App\Mapper\AnswerMapper;
use App\Model\Answer;
use Framework\Database\Query;

class AnswerRepository
{
    public function __construct(private AnswerMapper $mapper) {}

    public function upsert(Answer $answer): void
    {
        $existing = $this->mapper->select(new Query([
            'statementId' => $answer->statementId,
            'userId' => $answer->userId
        ]));

        if (count($existing) > 0) {
            $answer->id = $existing[0]->id;
            $this->mapper->update($answer);
        } else {
            $this->mapper->insert($answer);
        }
    }
}
