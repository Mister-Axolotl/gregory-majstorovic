<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\TitreUtils;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// AUCUNE valeur en dur : tout vient de l'environnement (12-factor app).
// Ces variables seront fournies par le service `api` de votre compose.yml.
$dsn = sprintf(
    'pgsql:host=%s;port=5432;dbname=%s',
    getenv('DB_HOST') ?: 'db',
    getenv('DB_NAME')
);

$pdo = new PDO($dsn, getenv('DB_USER') ?: null, getenv('DB_PASSWORD') ?: null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$json = static function (Response $res, mixed $data, int $code = 200): Response {
    $res->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));

    return $res->withHeader('Content-Type', 'application/json')->withStatus($code);
};

$app->get('/api/taches', function (Request $req, Response $res) use ($pdo, $json): Response {
    // faite::int : evite les surprises de conversion booleenne entre PostgreSQL et PDO
    $rows = $pdo->query('SELECT id, titre, faite::int AS faite FROM tache ORDER BY id')
                ->fetchAll(PDO::FETCH_ASSOC);

    $taches = array_map(static fn (array $r): array => [
        'id'    => (int) $r['id'],
        'titre' => $r['titre'],
        'faite' => (bool) (int) $r['faite'],
    ], $rows);

    return $json($res, $taches);
});

$app->post('/api/taches', function (Request $req, Response $res) use ($pdo, $json): Response {
    $corps = (array) $req->getParsedBody();

    try {
        $titre = TitreUtils::normaliser($corps['titre'] ?? null);
    } catch (\InvalidArgumentException $e) {
        return $json($res, ['erreur' => $e->getMessage()], 400);
    }

    $faite = (bool) ($corps['faite'] ?? false);

    $stmt = $pdo->prepare('INSERT INTO tache (titre, faite) VALUES (:titre, :faite) RETURNING id');
    $stmt->execute(['titre' => $titre, 'faite' => $faite ? 'true' : 'false']);
    $id = (int) $stmt->fetchColumn();

    return $json($res, ['id' => $id, 'titre' => $titre, 'faite' => $faite], 201);
});

// Renvoie le hostname du conteneur : permet de verifier le load-balancing.
$app->get('/api/qui', function (Request $req, Response $res): Response {
    $res->getBody()->write(gethostname());

    return $res->withHeader('Content-Type', 'text/plain');
});

$app->run();
