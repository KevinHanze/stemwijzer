<?php

namespace App\Controller;

use App\Mapper\AnswerMapper;
use App\Mapper\StatementMapper;
use App\Mapper\StemwijzerMapper;
use App\Mapper\UserMapper;
use App\Repository\UserAnswerRepository;
use App\Model\Stemwijzer;
use App\Model\UserAnswer;
use Framework\Database\Query;
use Framework\Templating\TemplateEngine;
use Framework\Http\Response;
use Framework\Http\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FormController implements RequestHandlerInterface
{
    public function __construct(
        private TemplateEngine $view,
        private StatementMapper $statements,
        private StemwijzerMapper $stemwijzers,
        private UserAnswerRepository $userAnswers,
        private AnswerMapper $answerMapper,
        private UserMapper $userMapper,
    ) {}

    public function handle(ServerRequestInterface $request): Response
    {
        $user = $request->getAttribute('user');
        $isPost = $request->getMethod() === 'POST';

        $statements = $this->statements->select(new Query([]));
        $matchedParties = null;

        if ($isPost) {
            $stemwijzer = new Stemwijzer(null, $user->getId(), null, '');
            $this->stemwijzers->insert($stemwijzer);

            $data = $request->getParsedBody();
            foreach ($data as $key => $value) {
                if (str_starts_with($key, 'statement_')) {
                    $statementId = (int)str_replace('statement_', '', $key);
                    $answer = new UserAnswer(null, $statementId, $user->getId(), $value, $stemwijzer->id);
                    $this->userAnswers->upsert($answer);
                }
            }

            $matched = $this->determineMatchedParties($user->getId(), $stemwijzer->id);
            $matchedParties = implode(', ', $matched);

            $stemwijzer->matchedParties = $matchedParties;
            $this->stemwijzers->update($stemwijzer);
        }

        $html = $this->view->render('form.html',
            statements: $statements,
            loggedIn: !$user?->isAnonymous(),
            options: ['Helemaal oneens', 'Oneens', 'Neutraal', 'Eens', 'Helemaal eens'],
            matchedParties: $matchedParties
        );

        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }


    private function determineMatchedParties(int $userId, int $stemwijzerId): array
    {
        $partyAnswers = [];
        foreach ($this->answerMapper->select(new Query([])) as $pa) {
            if ($pa->userId === 0) {
                continue;
            }
            $partyAnswers[$pa->userId][$pa->statementId] = $pa->answer;
        }

        $userAnswers = $this->userAnswers->getByUserId($userId, $stemwijzerId);
        $scores = [];

        foreach ($partyAnswers as $partyId => $answers) {
            $matchCount = 0;

            foreach ($userAnswers as $ua) {
                if (isset($answers[$ua->statementId]) && $answers[$ua->statementId] === $ua->answer) {
                    $matchCount++;
                }
            }

            $scores[$partyId] = $matchCount;
        }

        $max = empty($scores) ? 0 : max($scores);
        $matched = [];

        foreach ($scores as $partyId => $score) {
            if ($score === $max && $score > 0) {
                $party = $this->userMapper->get($partyId);

                if (in_array('party', $party->roles)) {
                    $matched[] = $party->username;
                }
            }
        }
        if ($matched != null) {
            return $matched;

        }
        return ["no match"];
    }
}
