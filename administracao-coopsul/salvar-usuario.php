<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php'; require_auth(); check_csrf();
if (!is_admin()) { http_response_code(403); exit('Acesso negado.'); }
$name=trim((string)($_POST['name']??'')); $username=trim((string)($_POST['username']??'')); $password=(string)($_POST['password']??''); $role=(string)($_POST['role']??'');
if ($name==='' || !preg_match('/^[a-zA-Z0-9._-]{3,50}$/',$username) || strlen($password)<10 || !in_array($role,['administrador','redator'],true)) { http_response_code(422); exit('Dados inválidos. A senha deve ter ao menos 10 caracteres.'); }
try { db()->prepare('INSERT INTO users(name,username,password_hash,role) VALUES(?,?,?,?)')->execute([$name,$username,password_hash($password,PASSWORD_DEFAULT),$role]); } catch(PDOException $e) { http_response_code(409); exit('Este nome de usuário já existe.'); }
redirect_admin('Usuário criado.');

