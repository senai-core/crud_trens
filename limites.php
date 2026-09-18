<?php

const LIMITES = [
    'velocidade_kmh' => ['rotulo' => 'Velocidade', 'maximo' => 85.00, 'unidade' => 'km/h'],
    'temperatura_c' => ['rotulo' => 'Temperatura do motor', 'maximo' => 100.00, 'unidade' => '°C'],
    'consumo_lh' => ['rotulo' => 'Consumo', 'maximo' => 70.00, 'unidade' => 'L/h'],
    'vibracao_mms' => ['rotulo' => 'Vibração', 'maximo' => 6.50, 'unidade' => 'mm/s'],
];

function passouDoLimite($campo, $valor)
{
    return (float) $valor > LIMITES[$campo]['maximo'];
}

function alertasDaLeitura($leitura)
{
    $alertas = [];

    foreach (LIMITES as $campo => $limite) {
        if (passouDoLimite($campo, $leitura[$campo])) {
            $alertas[] = $limite['rotulo'] . ' acima de ' . numero($limite['maximo']) . ' ' . $limite['unidade'];
        }
    }

    return $alertas;
}
