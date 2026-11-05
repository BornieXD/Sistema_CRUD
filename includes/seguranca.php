<?php
// Inclua este arquivo depois de session_start().
function gerarTokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarTokenCsrf(): void
{
    $tokenRecebido = $_POST['csrf_token'] ?? '';
    $tokenSessao = $_SESSION['csrf_token'] ?? '';

    if (!$tokenRecebido || !$tokenSessao || !hash_equals($tokenSessao, $tokenRecebido)) {
        http_response_code(403);
        exit('Requisição inválida. Atualize a página e tente novamente.');
    }
}

function escapar($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
