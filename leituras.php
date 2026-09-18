<?php

require 'funcoes.php';
require 'limites.php';

$tremId = (int) ($_GET['trem'] ?? 0);
$apenasAlertas = isset($_GET['alertas']);

$trens = listarTrens($pdo);

$sql = 'SELECT l.*, t.prefixo
        FROM leituras l
        INNER JOIN trens t ON t.id = l.trem_id';
$parametros = [];

if ($tremId > 0) {
    $sql .= ' WHERE l.trem_id = ?';
    $parametros[] = $tremId;
}

$sql .= ' ORDER BY l.registrada_em DESC, l.id DESC LIMIT 200';

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);

$leituras = [];
$totalComAlerta = 0;

foreach ($stmt->fetchAll() as $leitura) {
    $leitura['alertas'] = alertasDaLeitura($leitura);

    if ($leitura['alertas']) {
        $totalComAlerta++;
    } elseif ($apenasAlertas) {
        continue;
    }

    $leituras[] = $leitura;
}

$titulo = 'Leituras';
require 'topo.php';
?>
<div class="cabecalho">
    <div>
        <h1>Leituras dos sensores</h1>
        <p class="legenda">Últimas 200 leituras, <?= $totalComAlerta ?> com alerta</p>
    </div>
    <a class="botao botao-forte" href="simulador.php">Gerar leituras</a>
</div>

<ul class="limites">
    <?php foreach (LIMITES as $limite): ?>
        <li><span><?= e($limite['rotulo']) ?></span> até <?= numero($limite['maximo']) ?> <?= e($limite['unidade']) ?></li>
    <?php endforeach; ?>
</ul>

<form class="filtros" method="get">
    <label class="campo">
        <span>Trem</span>
        <select name="trem">
            <option value="0">Todos</option>
            <?php foreach ($trens as $trem): ?>
                <option value="<?= (int) $trem['id'] ?>" <?= $tremId === (int) $trem['id'] ? 'selected' : '' ?>><?= e($trem['prefixo'] . ' / ' . $trem['modelo']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="marcador">
        <input type="checkbox" name="alertas" value="1" <?= $apenasAlertas ? 'checked' : '' ?>>
        Só leituras com alerta
    </label>

    <div class="botoes">
        <button class="botao botao-forte" type="submit">Aplicar</button>
        <a class="botao botao-contorno" href="leituras.php">Limpar</a>
    </div>
</form>

<?php if (!$leituras): ?>
    <p class="sem-dados">Nada para mostrar com esse filtro. Use o <a href="simulador.php">simulador</a> para gerar leituras.</p>
<?php else: ?>
    <div class="rolagem">
        <table class="grade-dados">
            <thead>
                <tr>
                    <th>Registrada em</th>
                    <th>Trem</th>
                    <?php foreach (LIMITES as $limite): ?>
                        <th class="direita"><?= e($limite['rotulo']) ?></th>
                    <?php endforeach; ?>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leituras as $leitura): ?>
                    <tr>
                        <td><?= e(date('d/m/Y H:i', strtotime($leitura['registrada_em']))) ?></td>
                        <td class="prefixo"><?= e($leitura['prefixo']) ?></td>
                        <?php foreach (LIMITES as $campo => $limite): ?>
                            <td class="direita <?= passouDoLimite($campo, $leitura[$campo]) ? 'estourou' : '' ?>">
                                <?= numero($leitura[$campo]) ?> <?= e($limite['unidade']) ?>
                            </td>
                        <?php endforeach; ?>
                        <td>
                            <?php if ($leitura['alertas']): ?>
                                <span class="status status-oficina" title="<?= e(implode(' | ', $leitura['alertas'])) ?>"><?= count($leitura['alertas']) ?> alerta(s)</span>
                            <?php else: ?>
                                <span class="status status-operando">Normal</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require 'rodape.php'; ?>
