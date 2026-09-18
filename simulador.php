<?php

require 'funcoes.php';

$trens = listarTrens($pdo);
$idsTrens = array_column($trens, 'id');
$erros = [];
$tremId = 0;
$quantidade = 30;

function sortear($minimo, $maximo)
{
    return mt_rand((int) ($minimo * 100), (int) ($maximo * 100)) / 100;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tremId = (int) ($_POST['trem'] ?? 0);
    $quantidade = (int) ($_POST['quantidade'] ?? 0);

    if (!in_array($tremId, $idsTrens)) {
        $erros[] = 'Escolha um trem da lista.';
    }

    if ($quantidade < 1 || $quantidade > 200) {
        $erros[] = 'A quantidade precisa ficar entre 1 e 200.';
    }

    if (!$erros) {
        $stmt = $pdo->prepare(
            'INSERT INTO leituras (trem_id, registrada_em, velocidade_kmh, temperatura_c, consumo_lh, vibracao_mms)
             VALUES (?, ?, ?, ?, ?, ?)'
        );

        $intervaloSegundos = 5 * 60;
        $momento = time() - ($quantidade - 1) * $intervaloSegundos;

        $pdo->beginTransaction();

        for ($i = 0; $i < $quantidade; $i++) {
            $stmt->execute([
                $tremId,
                date('Y-m-d H:i:s', $momento),
                sortear(0, 95),
                sortear(55, 112),
                sortear(18, 85),
                sortear(0.4, 8.5),
            ]);

            $momento += $intervaloSegundos;
        }

        $pdo->commit();

        avisar("$quantidade leituras simuladas com sucesso.");
        irPara('leituras.php?trem=' . $tremId);
    }
}

$titulo = 'Simulador';
require 'topo.php';
?>
<div class="cabecalho">
    <div>
        <h1>Simulador de sensores IoT</h1>
        <p class="legenda">Gera leituras falsas de 5 em 5 minutos até agora, para testar os alertas.</p>
    </div>
</div>

<?php if ($erros): ?>
    <ul class="recado recado-erro">
        <?php foreach ($erros as $erro): ?>
            <li><?= e($erro) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!$trens): ?>
    <p class="sem-dados">Cadastre um trem antes de simular. <a href="trem.php">Cadastrar trem</a></p>
<?php else: ?>
    <form class="ficha" method="post">
        <div class="colunas">
            <label class="campo">
                <span>Trem</span>
                <select name="trem" required>
                    <option value="">Selecione</option>
                    <?php foreach ($trens as $trem): ?>
                        <option value="<?= (int) $trem['id'] ?>" <?= $tremId === (int) $trem['id'] ? 'selected' : '' ?>><?= e($trem['prefixo'] . ' / ' . $trem['modelo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="campo">
                <span>Quantidade de leituras</span>
                <input type="number" name="quantidade" min="1" max="200" value="<?= (int) $quantidade ?>" required>
            </label>
        </div>

        <div class="botoes">
            <button class="botao botao-forte" type="submit">Simular</button>
        </div>
    </form>
<?php endif; ?>
<?php require 'rodape.php'; ?>
