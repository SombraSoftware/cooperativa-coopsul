<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__, 2);
$failures = 0;

function check_security(bool $condition, string $description): void
{
    global $failures;
    echo ($condition ? '[OK] ' : '[FALHA] ') . $description . PHP_EOL;
    if (!$condition) $failures++;
}

function contains_security(string $file, string $text): bool
{
    $content = is_file($file) ? file_get_contents($file) : false;
    return is_string($content) && str_contains($content, $text);
}

check_security(PHP_VERSION_ID >= 80000, 'PHP 8.0 ou superior');
check_security(extension_loaded('pdo_sqlite'), 'Extensão pdo_sqlite habilitada');
check_security(extension_loaded('fileinfo'), 'Extensão fileinfo habilitada');
check_security(is_file($root . '/.htaccess'), 'Arquivo .htaccess principal presente');
check_security(contains_security($root . '/.htaccess', 'RewriteRule ^ index.php'), 'Front controller configurado');
check_security(contains_security($root . '/data/.htaccess', 'Require all denied'), 'Banco bloqueado via HTTP');
check_security(contains_security($root . '/scripts/.htaccess', 'Require all denied'), 'Scripts administrativos bloqueados via HTTP');
check_security(contains_security($root . '/uploads/noticias/.htaccess', 'FilesMatch'), 'Execução de scripts bloqueada nos uploads');
check_security(contains_security($root . '/app/bootstrap.php', 'hash_equals'), 'Validação CSRF utiliza comparação segura');
check_security(contains_security($root . '/administracao-coopsul/salvar-usuario.php', 'password_hash'), 'Criação de senha utiliza hash seguro');
check_security(contains_security($root . '/administracao-coopsul/salvar-post.php', 'can_manage_post'), 'Autorização de autoria aplicada ao salvar');
check_security(contains_security($root . '/administracao-coopsul/excluir-post.php', 'can_manage_post'), 'Autorização de autoria aplicada ao excluir');
check_security(contains_security($root . '/administracao-coopsul/mensagens.php', 'is_admin'), 'Mensagens restritas ao Administrador');

echo PHP_EOL . ($failures === 0 ? 'Verificação concluída sem falhas.' : "Falhas encontradas: {$failures}.") . PHP_EOL;
exit($failures === 0 ? 0 : 1);
