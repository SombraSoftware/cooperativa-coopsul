<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
require_auth();
if (!is_admin()) { http_response_code(403); exit('Acesso negado.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $status = ($_POST['status'] ?? '') === 'nova' ? 'nova' : 'lida';
    db()->prepare('UPDATE contact_messages SET status=? WHERE id=?')->execute([$status, $id]);
    header('Location: mensagens.php'); exit;
}
$messages = db()->query('SELECT id,name,email,phone,subject,message,status,created_at FROM contact_messages ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Mensagens | Coopsul</title><link rel="stylesheet" href="../assets/css/admin.css"></head><body>
<header class="admin-header"><div><strong>Coopsul</strong><span>Mensagens recebidas</span></div><div><a href="./">Voltar ao painel</a> <a href="logout.php">Sair</a></div></header>
<main class="admin-wrap"><section class="panel"><h1>Mensagens de contato</h1>
<?php if (!$messages): ?><p>Nenhuma mensagem recebida.</p><?php endif; ?>
<?php foreach ($messages as $item): ?><article class="message-card <?= $item['status'] === 'nova' ? 'unread' : '' ?>">
<header><h2><?= e($item['subject']) ?></h2><small><?= e($item['created_at']) ?> · <?= e($item['status']) ?></small></header>
<p><strong><?= e($item['name']) ?></strong> · <a href="mailto:<?= e($item['email']) ?>"><?= e($item['email']) ?></a><?php if ($item['phone']): ?> · <?= e($item['phone']) ?><?php endif; ?></p>
<p class="message-content"><?= nl2br(e($item['message'])) ?></p>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><input type="hidden" name="status" value="<?= $item['status'] === 'nova' ? 'lida' : 'nova' ?>"><button><?= $item['status'] === 'nova' ? 'Marcar como lida' : 'Marcar como nova' ?></button></form>
</article><?php endforeach; ?></section></main></body></html>

