<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    check_csrf();
    $stmt = db()->prepare('SELECT * FROM users WHERE username = ? AND active = 1');
    $stmt->execute([trim((string)$_POST['username'])]);
    $account = $stmt->fetch();
    if ($account && password_verify((string)$_POST['password'], $account['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $account['id'], 'name' => $account['name'], 'username' => $account['username'], 'role' => $account['role']];
        redirect_admin();
    }
    $error = 'Usuário ou senha inválidos.';
}
if (!user()): ?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Administração Coopsul</title><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body class="admin-login"><main class="login-card"><img src="/assets/img/logo_coopsul.png" alt="Coopsul"><h1>Área editorial</h1><?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>Usuário<input name="username" autocomplete="username" required autofocus></label><label>Senha<input type="password" name="password" autocomplete="current-password" required></label><button name="login" value="1">Entrar</button></form></main></body></html>
<?php exit; endif;
$edit = null;
if (isset($_GET['editar'])) {
    $stmt = db()->prepare('SELECT * FROM posts WHERE id = ?'); $stmt->execute([(int)$_GET['editar']]); $edit = $stmt->fetch();
    if (!$edit || !can_manage_post($edit)) { http_response_code(403); exit('Acesso negado.'); }
}
$posts = db()->query('SELECT posts.*, users.name AS author FROM posts JOIN users ON users.id=posts.author_id ORDER BY created_at DESC')->fetchAll();
$users = is_admin() ? db()->query('SELECT id,name,username,role,active,created_at FROM users ORDER BY name')->fetchAll() : [];
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Painel editorial | Coopsul</title><link rel="stylesheet" href="../assets/css/admin.css"></head><body>
<header class="admin-header"><div><strong>Coopsul</strong><span>Painel editorial</span></div><div><?php if (is_admin()): ?><a href="mensagens.php">Mensagens</a><?php endif; ?> <?= e(user()['name']) ?> · <?= e(ucfirst(user()['role'])) ?> <a href="logout.php">Sair</a></div></header>
<main class="admin-wrap"><?php if ($flash): ?><p class="alert"><?= e($flash) ?></p><?php endif; ?>
<section class="panel"><h1><?= $edit ? 'Editar notícia' : 'Nova notícia' ?></h1><form action="salvar-post.php" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>"><label>Título<input name="title" maxlength="180" required value="<?= e($edit['title'] ?? '') ?>"></label><label>Resumo<textarea name="summary" maxlength="320" rows="3" required><?= e($edit['summary'] ?? '') ?></textarea></label><label>Conteúdo<textarea name="content" rows="10" required><?= e($edit['content'] ?? '') ?></textarea></label><label>Imagem (JPG, PNG ou WebP, até 5 MB)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label><label class="check"><input type="checkbox" name="published" value="1" <?= !empty($edit['published']) ? 'checked' : '' ?>> Publicar na página inicial</label><button>Salvar notícia</button><?php if ($edit): ?> <a class="button secondary" href="/administracao-coopsul/">Cancelar</a><?php endif; ?></form></section>
<section class="panel"><h2>Notícias</h2><div class="table-scroll"><table><thead><tr><th>Título</th><th>Autor</th><th>Status</th><th>Ações</th></tr></thead><tbody><?php foreach ($posts as $post): ?><tr><td><?= e($post['title']) ?></td><td><?= e($post['author']) ?></td><td><?= $post['published'] ? 'Publicada' : 'Rascunho' ?></td><td><?php if (can_manage_post($post)): ?><a href="?editar=<?= (int)$post['id'] ?>">Editar</a> <form class="inline" action="excluir-post.php" method="post" onsubmit="return confirm('Excluir esta notícia?')"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= (int)$post['id'] ?>"><button class="link danger">Excluir</button></form><?php else: ?>—<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></section>
<?php if (is_admin()): ?><section class="panel"><h2>Usuários</h2><form action="salvar-usuario.php" method="post" class="user-form"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label>Nome<input name="name" required></label><label>Usuário<input name="username" required></label><label>Senha inicial<input type="password" name="password" minlength="10" required></label><label>Perfil<select name="role"><option value="redator">Redator</option><option value="administrador">Administrador</option></select></label><button>Criar usuário</button></form><div class="table-scroll"><table><tbody><?php foreach ($users as $account): ?><tr><td><?= e($account['name']) ?></td><td><?= e($account['username']) ?></td><td><?= e(ucfirst($account['role'])) ?></td><td><?= $account['active'] ? 'Ativo' : 'Inativo' ?></td></tr><?php endforeach; ?></tbody></table></div></section><?php endif; ?></main></body></html>
