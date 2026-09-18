<?php

session_start();

require __DIR__ . '/config.php';

const SITUACOES = [
    'operando' => 'Operando',
    'oficina' => 'Na oficina',
    'parado' => 'Parado',
];

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function numero($valor, $casas = 2)
{
    return number_format((float) $valor, $casas, ',', '.');
}

function avisar($texto, $tipo = 'ok')
{
    $_SESSION['aviso'] = ['texto' => $texto, 'tipo' => $tipo];
}

function pegarAviso()
{
    $aviso = $_SESSION['aviso'] ?? null;
    unset($_SESSION['aviso']);
    return $aviso;
}

function irPara($pagina)
{
    header('Location: ' . $pagina);
    exit;
}

function listarTrens($pdo)
{
    return $pdo->query('SELECT id, prefixo, modelo FROM trens ORDER BY prefixo')->fetchAll();
}
