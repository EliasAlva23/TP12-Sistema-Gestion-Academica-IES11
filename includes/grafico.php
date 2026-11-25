<?php
/* Renderiza el gráfico y conserva sus datos accesibles cuando no hay JavaScript. */
?>
<section class="tarjeta-panel" aria-labelledby="<?= esc($idGrafico) ?>-titulo">
    <h2 id="<?= esc($idGrafico) ?>-titulo"><?= esc($tituloGrafico) ?></h2>
    <?php if (array_sum($valoresGrafico) > 0): ?>
        <div class="grafico">
            <canvas id="<?= esc($idGrafico) ?>" role="img"
                    aria-labelledby="<?= esc($idGrafico) ?>-titulo"
                    data-tipo-grafico="<?= esc($tipoGrafico) ?>"
                    data-etiquetas="<?= esc(json_encode($etiquetasGrafico, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)) ?>"
                    data-valores="<?= esc(json_encode($valoresGrafico)) ?>"></canvas>
        </div>
        <details class="grafico-datos" open>
            <summary>Ver datos del gráfico</summary>
            <dl class="grafico-alternativo mb-0 mt-2">
                <?php foreach ($etiquetasGrafico as $indice => $etiqueta): ?>
                    <div><dt><?= esc($etiqueta) ?></dt><dd><?= (int) $valoresGrafico[$indice] ?></dd></div>
                <?php endforeach; ?>
            </dl>
        </details>
    <?php else: ?>
        <p class="estado-vacio mb-0">No hay registros para mostrar.</p>
    <?php endif; ?>
</section>
