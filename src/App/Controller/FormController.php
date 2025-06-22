<?php

namespace App\Controller;

use App\Mapper\StatementMapper;
use App\Mapper\StemwijzerMapper;
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
        private UserAnswerRepository $userAnswers
    ) {}

    public function handle(ServerRequestInterface $request): Response
    {
        $user = $request->getAttribute('user');
        $isPost = $request->getMethod() === 'POST';

        if ($isPost) {
            if ($user?->isAnonymous()) {
                return new Response(403, ['Content-Type' => ['text/plain']], Stream::fromString("Niet toegestaan."));
            }

            // Maak nieuwe stemwijzer entry aan
            $stemwijzer = new Stemwijzer(null, $user->getId(), null);
            $this->stemwijzers->insert($stemwijzer);

            $data = $request->getParsedBody();
            foreach ($data as $key => $value) {
                if (str_starts_with($key, 'statement_')) {
                    $statementId = (int)str_replace('statement_', '', $key);
                    $answer = new UserAnswer(null, $statementId, $user->getId(), $value, $stemwijzer->id);
                    $this->userAnswers->upsert($answer);
                }
            }

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
}
