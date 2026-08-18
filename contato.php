<?php
declare(strict_types=1);
require_once __DIR__ . '/app/bootstrap.php';

$feedback = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    check_csrf();
    if (!empty($_POST['website'])) { http_response_code(400); exit('Solicitação inválida.'); }
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $subject = trim((string)($_POST['subject'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));
    $subjects = ['coleta', 'dúvida', 'duvida', 'cooperado', 'parceria', 'outro'];
    if ($name === '' || mb_strlen($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($subject, $subjects, true) || $message === '' || mb_strlen($message) > 5000) {
        $feedback = '<p class="form-feedback form-error" role="alert">Confira os dados e tente novamente.</p>';
    } else {
        $ipHash = hash('sha256', (string)($_SERVER['REMOTE_ADDR'] ?? '') . 'coopsul-contato');
        $stmt = db()->prepare('INSERT INTO contact_messages(name,email,phone,subject,message,ip_hash) VALUES(?,?,?,?,?,?)');
        $stmt->execute([$name, $email, mb_substr($phone, 0, 30), $subject, $message, $ipHash]);
        $_SESSION['contact_success'] = true;
        header('Location: ' . app_url('contato?enviado=1'));
        exit;
    }
}
if (isset($_SESSION['contact_success'])) {
    unset($_SESSION['contact_success']);
    $feedback = '<p class="form-feedback form-success" role="status">Mensagem enviada com sucesso! Entraremos em contato em breve.</p>';
}
$formOpen = '<form id="contactForm" method="post" action="' . e(app_url('contato')) . '">' .
    '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">' .
    '<label class="form-honeypot" aria-hidden="true">Site<input name="website" tabindex="-1" autocomplete="off"></label>';
render_public_page('contato.php', [
    '<form id="contactForm">' => $feedback . $formOpen,
]);
