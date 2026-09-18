<?php

require 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover'])) {
    $stmt = $pdo->prepare('DELETE FROM trens WHERE id = ?');
    $stmt->execute([(int) $_POST['remover']]);

    if ($stmt->rowCount() > 0) {
        avisar('Trem removido da frota.');
    } else {
        avisar('Trem não encontrado.', 'erro');
    }

    irPara('index.php');
}

$trens = $pdo->query(
    'SELECT t.*, (SELECT COUNT(*) FROM leituras l WHERE l.trem_id = t.id) AS total_leituras
     FROM trens t
     ORDER BY t.prefixo'
)->fetchAll();

$titulo = 'Trens';
require 'topo.php';
?>
<div class="cabecalho">
    <div>
        <h1>Frota cadastrada</h1>
        <p class="legenda"><?= count($trens) ?> trem(ns) no sistema</p>
    </div>
    <a class="botao botao-forte" href="trem.php">Cadastrar trem</a>
</div>

<?php if (!$trens): ?>
    <p class="sem-dados">Nenhum trem cadastrado. <a href="trem.php">Cadastre o primeiro.</a></p>
<?php else: ?>
    <div class="rolagem">
        <table class="grade-dados">
            <thead>
                <tr>
                    <th>Prefixo</th>
                    <th>Modelo</th>
                    <th>Ano</th>
                    <th class="direita">Capacidade</th>
                    <th>Situação</th>
                    <th class="direita">Leituras</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trens as $trem): ?>
                    <tr>
                        <td class="prefixo"><?= e($trem['prefixo']) ?></td>
                        <td><?= e($trem['modelo']) ?></td>
                        <td><?= (int) $trem['ano_fabricacao'] ?></td>
                        <td class="direita"><?= numero($trem['capacidade_t']) ?> t</td>
                        <td><span class="status status-<?= e($trem['situacao']) ?>"><?= e(SITUACOES[$trem['situacao']]) ?></span></td>
                        <td class="direita">
                            <a href="leituras.php?trem=<?= (int) $trem['id'] ?>"><?= (int) $trem['total_leituras'] ?></a>
                        </td>
                        <td class="botoes">
                            <a class="botao botao-contorno" href="trem.php?id=<?= (int) $trem['id'] ?>">Editar</a>
                            <form method="post" onsubmit="return confirm(<?= e(json_encode('Remover o trem ' . $trem['prefixo'] . ' e todas as leituras dele?', JSON_UNESCAPED_UNICODE)) ?>);">
                                <input type="hidden" name="remover" value="<?= (int) $trem['id'] ?>">
                                <button class="botao botao-alerta" type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require 'rodape.php'; ?>
