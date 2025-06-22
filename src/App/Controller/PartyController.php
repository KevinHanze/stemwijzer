<?php

namespace App\Controller;

use App\Mapper\StatementMapper;
use App\Model\Answer;
use App\Repository\AnswerRepository;
use Framework\Database\Query;
use Framework\Templating\TemplateEngine;
use Framework\Http\Response;
use Framework\Http\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Controller for party users to submit their answers and reasoning.
 */
class PartyController implements RequestHandlerInterface
{
    public function __construct(
        private TemplateEngine $view,
        private StatementMapper $statements,
        private AnswerRepository $answers
    ) {}

    /**
     * Handles GET to show the answer form, and POST to save party answers and reasoning.
     */
    public function handle(ServerRequestInterface $request): Response
    {
        $user = $request->getAttribute('user');
        $partyId = $user->getId();

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            foreach ($data as $key => $value) {
                if (str_starts_with($key, 'statement_')) {
                    $statementId = (int)str_replace('statement_', '', $key);
                    $answer = $value;
                    $reasonKey = "reason_$statementId";
                    $reason = $data[$reasonKey] ?? '';

                    $this->answers->upsert(new Answer(
                        id: null,
                        statementId: $statementId,
                        userId: $partyId,
                        answer: $answer,
                        reason: $reason
                    ));
                }
            }

            // Redirect after submission
            return new Response(302, ['Location' => ['/party']]);
        }

        // Show the form with all statements
        $statements = $this->statements->select(new Query([]));

        $html = $this->view->render('party.html',
            statements: $statements,
            options: ['Helemaal oneens', 'Oneens', 'Neutraal', 'Eens', 'Helemaal eens'],
            loggedIn: true
        );

        return new Response(200, ['Content-Type' => ['text/html']], Stream::fromString($html));
    }
}
