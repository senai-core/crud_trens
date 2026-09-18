<?php $aviso = pegarAviso(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | Controle de Frota</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <header class="topo">
        <div class="topo-interno">
            <a class="marca" href="index.php">Controle de Frota</a>
            <nav class="navegacao">
                <a href="index.php">Trens</a>
                <a href="leituras.php">Leituras</a>
                <a href="simulador.php">Simulador</a>
            </nav>
        </div>
    </header>
    <main class="pagina">
        <?php if ($aviso): ?>
            <p class="recado recado-<?= e($aviso['tipo']) ?>"><?= e($aviso['texto']) ?></p>
        <?php endif; ?>
