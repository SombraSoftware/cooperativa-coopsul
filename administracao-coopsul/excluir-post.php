<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php'; require_auth(); check_csrf();
$stmt=db()->prepare('SELECT * FROM posts WHERE id=?'); $stmt->execute([(int)($_POST['id']??0)]); $post=$stmt->fetch();
if (!$post || !can_manage_post($post)) { http_response_code(403); exit('Acesso negado.'); }
db()->prepare('DELETE FROM posts WHERE id=?')->execute([$post['id']]);
if ($post['image']) { $path=COOPSUL_UPLOAD_DIR.'/'.$post['image']; if (is_file($path)) unlink($path); }
redirect_admin('Notícia excluída.');

