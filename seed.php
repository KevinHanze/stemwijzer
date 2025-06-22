<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Model\User;
use App\Model\Statement;
use App\Model\Answer;
use App\Mapper\UserMapper;
use App\Mapper\StatementMapper;
use App\Mapper\AnswerMapper;
use Framework\Database\PdoConnection;
use Framework\Database\Query;

/* -----------------------------------------------------------------
   1. DB connection
----------------------------------------------------------------- */
$db = new PdoConnection('sqlite:' . __DIR__ . '/database.db');

/* -----------------------------------------------------------------
   2. Mappers
----------------------------------------------------------------- */
$userMapper       = new UserMapper($db);
$statementMapper  = new StatementMapper($db);
$answerMapper     = new AnswerMapper($db);

/* -----------------------------------------------------------------
   3. Wipe tables
----------------------------------------------------------------- */
$db->execute('DELETE FROM answers');
$db->execute('DELETE FROM statements');
$db->execute('DELETE FROM users');
echo "🧹  Cleared users, statements, answers tables\n";

/* -----------------------------------------------------------------
   4. Seed users
----------------------------------------------------------------- */
$users = [
    ['admin',     'admin123',     ['admin']],
    ['gebruiker', 'gebruiker123', ['user']],
];

for ($i = 1; $i <= 5; $i++) {
    $users[] = ["partij$i", "partij$i", ['party']];
}

foreach ($users as [$username, $plain, $roles]) {
    $userMapper->insert(new User(
        id: null,
        username: $username,
        passwordHash: password_hash($plain, PASSWORD_DEFAULT),
        roles: $roles
    ));
    echo "✅  User inserted: $username (" . implode(',', $roles) . ")\n";
}

/* -----------------------------------------------------------------
   5. Seed statements
----------------------------------------------------------------- */
$statementTexts = [
    'De overheid moet meer investeren in duurzaamheid.',
    'Studiefinanciering moet worden verhoogd.',
    'De maximumsnelheid moet omlaag naar 90 km/u.',
    'Woningcorporaties moeten verplicht betaalbare huurwoningen bouwen.',
    'Er moet een basisinkomen komen voor alle burgers.',
    'De accijns op benzine moet verlaagd worden.',
    'Het eigen risico in de zorg moet worden afgeschaft.',
];

foreach ($statementTexts as $text) {
    $statementMapper->insert(new Statement(
        id: null,
        text: $text,
        author: 'admin'
    ));
    echo "🗒️   Statement inserted: $text\n";
}

/* -----------------------------------------------------------------
   6. Seed party answers
----------------------------------------------------------------- */
$options = ['Helemaal oneens', 'Oneens', 'Neutraal', 'Eens', 'Helemaal eens'];

$partyUsers = $userMapper->select(new Query([]));
$partyUsers = array_filter($partyUsers, fn(User $u) => in_array('party', $u->roles));

$statements = $statementMapper->select(new Query([]));

foreach ($partyUsers as $uIndex => $partyUser) {
    foreach ($statements as $sIndex => $statement) {
        $choice = $options[($uIndex + $sIndex) % count($options)];
        $answerMapper->insert(new Answer(
            id: null,
            statementId: $statement->id,
            userId:     $partyUser->id,
            answer:     $choice,
            reason:     "Wij kiezen \"$choice\" voor statement {$statement->id}."
        ));
    }
    echo "🗳️   Answers seeded for {$partyUser->username}\n";
}

echo "\n🎉  Done seeding \n";
