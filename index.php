<?php
declare(strict_types=1);
require_once __DIR__ . '/app/bootstrap.php';

$requestPath = rawurldecode((string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'));
$basePath = rtrim((string)(parse_url(app_url(), PHP_URL_PATH) ?: '/'), '/');
if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath));
}
$route = trim($requestPath, '/');

$routes = [
    '' => static fn() => render_public_page('index.php'),
    'reciclagem' => static fn() => render_public_page('reciclagem.php'),
    'sobre' => static fn() => render_public_page('sobre.php'),
    'social' => static fn() => render_public_page('social.php'),
    'contato' => static function (): never { require __DIR__ . '/contato.php'; exit; },
];

if (isset($routes[$route])) {
    $routes[$route]();
}

http_response_code(404);
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Página não encontrada | Coopsul</title><link rel="stylesheet" href="<?= e(app_url('assets/css/estilo.css')) ?>"></head><body><main><section class="hero"><div class="container"><h2>Página não encontrada</h2><p>O endereço informado não existe ou foi alterado.</p><a class="btn" href="<?= e(app_url()) ?>">Voltar ao início</a></div></section></main></body></html>
