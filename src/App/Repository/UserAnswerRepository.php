<?php

namespace App\Repository;

use App\Mapper\UserAnswerMapper;
use App\Model\UserAnswer;
use Framework\Database\Query;

class UserAnswerRepository
{
    public function __construct(private UserAnswerMapper $mapper) {}

    public function getByUserId(int $userId, ?int $stemwijzerId = null): array
    {
        $filter = ['user_id' => $userId];
        if ($stemwijzerId !== null) {
            $filter['stemwijzer_id'] = $stemwijzerId;
        }
        return $this->mapper->select(new Query($filter));
    }

    public function upsert(UserAnswer $answer): void
    {
        $existing = $this->mapper->select(new Query([
            'statement_id'   => $answer->statementId,
            'user_id'        => $answer->userId,
            'stemwijzer_id'  => $answer->stemwijzerId,
        ]));

        if ($existing) {
            $answer->id = $existing[0]->id;
            $this->mapper->update($answer);
        } else {
            $this->mapper->insert($answer);
        }
    }
}
