<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
$limit = min(12, max(1, (int)($_GET['limite'] ?? 6)));
$stmt = db()->prepare('SELECT posts.id, title, summary, content, image, published_at, users.name AS author
    FROM posts JOIN users ON users.id = posts.author_id
    WHERE published = 1 ORDER BY COALESCE(posts.published_at, posts.created_at) DESC LIMIT :limit');
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$posts = array_map(static function (array $post): array {
    $post['image_url'] = $post['image'] ? app_url(trim(COOPSUL_UPLOAD_URL, '/') . '/' . rawurlencode($post['image'])) : null;
    unset($post['image']);
    return $post;
}, $stmt->fetchAll());
echo json_encode(['noticias' => $posts], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
