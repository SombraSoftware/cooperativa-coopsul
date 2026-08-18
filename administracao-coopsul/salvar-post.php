<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php'; require_auth(); check_csrf();
$id=(int)($_POST['id']??0); $title=trim((string)($_POST['title']??'')); $summary=trim((string)($_POST['summary']??'')); $content=trim((string)($_POST['content']??'')); $published=isset($_POST['published'])?1:0;
if ($title==='' || $summary==='' || $content==='') { http_response_code(422); exit('Preencha todos os campos obrigatórios.'); }
try { $image=save_image($_FILES['image'] ?? []); } catch (RuntimeException $e) { http_response_code(422); exit(e($e->getMessage())); }
if ($id) {
    $stmt=db()->prepare('SELECT * FROM posts WHERE id=?'); $stmt->execute([$id]); $post=$stmt->fetch();
    if (!$post || !can_manage_post($post)) { http_response_code(403); exit('Acesso negado.'); }
    $sql='UPDATE posts SET title=?,summary=?,content=?,published=?,updated_at=CURRENT_TIMESTAMP,published_at=CASE WHEN ?=1 AND published_at IS NULL THEN CURRENT_TIMESTAMP WHEN ?=0 THEN NULL ELSE published_at END'.($image?',image=?':'').' WHERE id=?';
    $params=[$title,$summary,$content,$published,$published,$published]; if($image)$params[]=$image; $params[]=$id; db()->prepare($sql)->execute($params);
    redirect_admin('Notícia atualizada.');
}
db()->prepare('INSERT INTO posts(title,summary,content,image,published,author_id,published_at) VALUES(?,?,?,?,?,?,CASE WHEN ?=1 THEN CURRENT_TIMESTAMP END)')->execute([$title,$summary,$content,$image,$published,user()['id'],$published]);
redirect_admin('Notícia criada.');

