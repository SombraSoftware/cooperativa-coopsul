<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function db(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) return $pdo;
    $directory = dirname(COOPSUL_DB_PATH);
    if (!is_dir($directory)) mkdir($directory, 0775, true);
    $pdo = new PDO('sqlite:' . COOPSUL_DB_PATH, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        username TEXT NOT NULL UNIQUE COLLATE NOCASE,
        password_hash TEXT NOT NULL,
        role TEXT NOT NULL CHECK(role IN (\'administrador\', \'redator\')),
        active INTEGER NOT NULL DEFAULT 1,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        summary TEXT NOT NULL DEFAULT \'\',
        content TEXT NOT NULL,
        image TEXT,
        published INTEGER NOT NULL DEFAULT 0,
        author_id INTEGER NOT NULL,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        published_at TEXT,
        FOREIGN KEY(author_id) REFERENCES users(id) ON DELETE RESTRICT
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS contact_messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL DEFAULT \'\',
        subject TEXT NOT NULL,
        message TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT \'nova\' CHECK(status IN (\'nova\', \'lida\')),
        ip_hash TEXT NOT NULL DEFAULT \'\',
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )');
    return $pdo;
}

function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function app_url(string $path = ''): string {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = '';
    foreach (['/administracao-coopsul/', '/api/', '/scripts/', '/index.php'] as $marker) {
        $position = strpos($script, $marker);
        if ($position !== false) { $base = substr($script, 0, $position); break; }
    }
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}
function user(): ?array { return $_SESSION['user'] ?? null; }
function is_admin(): bool { return (user()['role'] ?? '') === 'administrador'; }
function require_auth(): void { if (!user()) { header('Location: ' . app_url('administracao-coopsul/')); exit; } }
function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function check_csrf(): void {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', (string) $_POST['csrf'])) {
        http_response_code(419); exit('Sessão expirada. Volte e tente novamente.');
    }
}
function redirect_admin(string $message = ''): never {
    if ($message !== '') $_SESSION['flash'] = $message;
    header('Location: ' . app_url('administracao-coopsul/')); exit;
}
function can_manage_post(array $post): bool { return is_admin() || (int)$post['author_id'] === (int)(user()['id'] ?? 0); }

function render_public_page(string $file, array $replacements = []): never
{
    $html = file_get_contents(__DIR__ . '/views/' . $file);
    if ($html === false) { http_response_code(500); exit('Página indisponível.'); }
    $html = str_replace(
        ['href="index.html"', 'href="reciclagem.html"', 'href="sobre.html"', 'href="contato.html"'],
        ['href="' . e(app_url()) . '"', 'href="' . e(app_url('reciclagem')) . '"', 'href="' . e(app_url('sobre')) . '"', 'href="' . e(app_url('contato')) . '"'],
        $html
    );
    if ($replacements) $html = str_replace(array_keys($replacements), array_values($replacements), $html);
    header('Content-Type: text/html; charset=UTF-8');
    echo $html;
    exit;
}

function save_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > COOPSUL_MAX_UPLOAD) throw new RuntimeException('Imagem inválida ou maior que 5 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) throw new RuntimeException('Use uma imagem JPG, PNG ou WebP.');
    if (!is_dir(COOPSUL_UPLOAD_DIR)) mkdir(COOPSUL_UPLOAD_DIR, 0775, true);
    $name = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], COOPSUL_UPLOAD_DIR . '/' . $name)) throw new RuntimeException('Não foi possível salvar a imagem.');
    return $name;
}
