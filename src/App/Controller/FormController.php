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


/**
 * Handles the form used by regular users to fill in the questionnaire (stemwijzer).
 */
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

    /**
     * Handles GET to show the form, and POST to save answers + show matched parties.
     */
    public function handle(ServerRequestInterface $request): Response
    {
        $user = $request->getAttribute('user');
        $isPost = $request->getMethod() === 'POST';

        $statements = $this->statements->select(new Query([]));
        $matchedParties = null;

        if ($isPost) {

            // Create a new stemwijzer record for this user session
            $stemwijzer = new Stemwijzer(null, $user->getId(), null, '');
            $this->stemwijzers->insert($stemwijzer);

            // Save each submitted answer
            $data = $request->getParsedBody();
            foreach ($data as $key => $value) {
                if (str_starts_with($key, 'statement_')) {
                    $statementId = (int)str_replace('statement_', '', $key);
                    $answer = new UserAnswer(null, $statementId, $user->getId(), $value, $stemwijzer->id);
                    $this->userAnswers->upsert($answer);
                }
            }

            // Determine top-matching parties and update the stemwijzer record
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

    /**
     * Finds the parties whose answers match the current user's answers the most.
     *
     * Only users with the "party" role are considered match candidates.
     *
     * @param int $userId The ID of the regular user
     * @param int $stemwijzerId The questionnaire/vote guide ID
     * @return array List of matched party usernames, or ["no match"] if none
     */
    private function determineMatchedParties(int $userId, int $stemwijzerId): array
    {
        $partyAnswers = [];

        // Collect all answers given by party users (userId !== 0)
        foreach ($this->answerMapper->select(new Query([])) as $pa) {
            if ($pa->userId === 0) {
                continue;
            }
            $partyAnswers[$pa->userId][$pa->statementId] = $pa->answer;
        }
        // Load the answers submitted by the current user
        $userAnswers = $this->userAnswers->getByUserId($userId, $stemwijzerId);
        $scores = [];

        // Count matching answers between the user and each party
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

        // Collect parties with the highest match count
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
