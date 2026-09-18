<?php

require 'funcoes.php';

$id = (int) ($_GET['id'] ?? 0);
$erros = [];
$anoAtual = (int) date('Y');

$trem = [
    'prefixo' => '',
    'modelo' => '',
    'ano_fabricacao' => '',
    'capacidade_t' => '',
    'situacao' => 'operando',
];

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM trens WHERE id = ?');
    $stmt->execute([$id]);
    $encontrado = $stmt->fetch();

    if (!$encontrado) {
        avisar('Trem não encontrado.', 'erro');
        irPara('index.php');
    }

    $trem = $encontrado;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trem = [
        'prefixo' => strtoupper(trim($_POST['prefixo'] ?? '')),
        'modelo' => trim($_POST['modelo'] ?? ''),
        'ano_fabricacao' => trim($_POST['ano_fabricacao'] ?? ''),
        'capacidade_t' => str_replace(',', '.', trim($_POST['capacidade_t'] ?? '')),
        'situacao' => $_POST['situacao'] ?? '',
    ];

    if ($trem['prefixo'] === '') {
        $erros[] = 'O prefixo é obrigatório.';
    }

    if ($trem['modelo'] === '') {
        $erros[] = 'O modelo é obrigatório.';
    }

    if (!ctype_digit($trem['ano_fabricacao']) || $trem['ano_fabricacao'] < 1900 || $trem['ano_fabricacao'] > $anoAtual) {
        $erros[] = "O ano de fabricação precisa estar entre 1900 e $anoAtual.";
    }

    if (!is_numeric($trem['capacidade_t']) || $trem['capacidade_t'] <= 0) {
        $erros[] = 'A capacidade precisa ser maior que zero.';
    }

    if (!isset(SITUACOES[$trem['situacao']])) {
        $erros[] = 'Escolha uma situação da lista.';
    }

    if (!$erros) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM trens WHERE prefixo = ? AND id <> ?');
        $stmt->execute([$trem['prefixo'], $id]);

        if ($stmt->fetchColumn() > 0) {
            $erros[] = 'Já existe um trem com esse prefixo.';
        }
    }

    if (!$erros) {
        $dados = [
            $trem['prefixo'],
            $trem['modelo'],
            (int) $trem['ano_fabricacao'],
            (float) $trem['capacidade_t'],
            $trem['situacao'],
        ];

        if ($id > 0) {
            $dados[] = $id;
            $stmt = $pdo->prepare('UPDATE trens SET prefixo = ?, modelo = ?, ano_fabricacao = ?, capacidade_t = ?, situacao = ? WHERE id = ?');
            $stmt->execute($dados);
            avisar('Dados do trem ' . $trem['prefixo'] . ' atualizados.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO trens (prefixo, modelo, ano_fabricacao, capacidade_t, situacao) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute($dados);
            avisar('Trem ' . $trem['prefixo'] . ' cadastrado.');
        }

        irPara('index.php');
    }
}

$titulo = $id > 0 ? 'Editar trem' : 'Cadastrar trem';
require 'topo.php';
?>
<div class="cabecalho">
    <h1><?= e($titulo) ?></h1>
</div>

<?php if ($erros): ?>
    <ul class="recado recado-erro">
        <?php foreach ($erros as $erro): ?>
            <li><?= e($erro) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form class="ficha" method="post">
    <div class="colunas">
        <label class="campo">
            <span>Prefixo</span>
            <input type="text" name="prefixo" maxlength="20" placeholder="SC-0000" value="<?= e($trem['prefixo']) ?>" required>
        </label>

        <label class="campo">
            <span>Ano de fabricação</span>
            <input type="number" name="ano_fabricacao" min="1900" max="<?= $anoAtual ?>" value="<?= e($trem['ano_fabricacao']) ?>" required>
        </label>
    </div>

    <label class="campo">
        <span>Modelo</span>
        <input type="text" name="modelo" maxlength="80" value="<?= e($trem['modelo']) ?>" required>
    </label>

    <div class="colunas">
        <label class="campo">
            <span>Capacidade (toneladas)</span>
            <input type="number" name="capacidade_t" step="0.01" min="0.01" value="<?= e($trem['capacidade_t']) ?>" required>
        </label>

        <label class="campo">
            <span>Situação</span>
            <select name="situacao">
                <?php foreach (SITUACOES as $valor => $rotulo): ?>
                    <option value="<?= e($valor) ?>" <?= $valor === $trem['situacao'] ? 'selected' : '' ?>><?= e($rotulo) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <div class="botoes">
        <button class="botao botao-forte" type="submit"><?= $id > 0 ? 'Salvar' : 'Cadastrar' ?></button>
        <a class="botao botao-contorno" href="index.php">Cancelar</a>
    </div>
</form>
<?php require 'rodape.php'; ?>
