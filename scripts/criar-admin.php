<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') exit("Execute pela linha de comando.\n");
require_once __DIR__ . '/../app/bootstrap.php';
$username=$argv[1]??''; $name=$argv[2]??''; $password=$argv[3]??'';
if (!$username || !$name || strlen($password)<10) exit("Uso: php scripts/criar-admin.php usuario \"Nome\" \"senha-com-10-caracteres\"\n");
try { db()->prepare('INSERT INTO users(name,username,password_hash,role) VALUES(?,?,?,\'administrador\')')->execute([$name,$username,password_hash($password,PASSWORD_DEFAULT)]); echo "Administrador criado.\n"; } catch(PDOException $e) { exit("Não foi possível criar: usuário já existe ou banco indisponível.\n"); }

