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

            $matchedParties = $this->determineMatchedParties($user->getId(), $stemwijzer->id);
            $stemwijzer->matchedParties = implode(', ', $matchedParties);
            $this->stemwijzers->update($stemwijzer);

            return new Response(302, ['Location' => ['/']]);
        }

        $statements = $this->statements->select(new Query([]));

        $html = $this->view->render('form.html',
            statements: $statements,
            loggedIn: !$user?->isAnonymous(),
            options: ['Helemaal oneens', 'Oneens', 'Neutraal', 'Eens', 'Helemaal eens']
        );

        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }

    private function determineMatchedParties(int $userId, int $stemwijzerId): array
    {
        $partyAnswers = [];
        foreach ($this->answerMapper->select(new Query([])) as $pa) {
            $partyAnswers[$pa->userId][$pa->statementId] = $pa->answer;
        }

        $userAnswers = $this->userAnswers->getByUserId($userId, $stemwijzerId);

        $scores = [];
        foreach ($partyAnswers as $userId => $answers) {
            $matchCount = 0;

            foreach ($userAnswers as $ua) {
                if (isset($answers[$ua->statementId]) && $answers[$ua->statementId] === $ua->answer) {
                    $matchCount++;
                }
            }

            $scores[$userId] = $matchCount;
        }

        $max = empty($scores) ? 0 : max($scores);
        $matched = [];

        foreach ($scores as $userId => $score) {
            if ($score === $max && $score > 0) {
                $party = $this->userMapper->get($userId);
                $matched[] = $party->username;
            }
        }

        return $matched;
    }
}
