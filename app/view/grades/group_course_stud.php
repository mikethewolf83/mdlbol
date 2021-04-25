<?php include_once VIEW_HEADER; ?>
<?php if ($grades) : ?>
    <div class="columns off-print">
        <div class="column">
            <p class="heading">Se encontraron <?= $total; ?> resultados</p>
        </div>
        <div class="column">
            <a title="Vista previa del boletín de <?= $title; ?>" href="<?= $uri . '/pdf/?action=view'; ?>" class="button is-rounded is-inverted is-info is-pulled-right mx-1" target="_blank">
                <div class="mx-1" data-icon="f"></div>
                Vista Previa
            </a>

            <a title="Descargar el boletín de <?= $title; ?>" href="<?= $uri . '/pdf/?action=download'; ?>" class="button is-rounded is-inverted is-info is-pulled-right mx-1">
                <div class="mx-1" data-icon="g"></div>
                Descargar
            </a>
        </div>
    </div>
    <table class="table table_wrapper is-fullwidth is-hoverable box">
        <thead>
            <tr>
                <th style="width: 25%;">
                    Asignatura
                </th>
                <th style="width: 15%;" class="has-text-centered">
                    Calificación
                </th>
                <th style="width: 60%;" class="has-text-centered">
                    Comentario
                </th>
                <th class="has-text-centered">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grades as $g) : ?>
                <tr>
                    <td>
                        <a href="<?= $courseUri . '/' . $g['courseid']; ?>" target="_blank"><?= $g['Curso']; ?></a>
                    </td>
                    <td class="has-text-centered">
                        <?= round($g['NotaTrimestre'], 1); ?>
                    </td>
                    <td spellcheck="true" contenteditable="true" title="Al dar clic se marcan los errores ortográficos si hubiera alguno, para editar el comentario de clic en el botón Editar.">
                        <p> <?= $g['TrimestreObservaciones']; ?> </p>
                    </td>
                    <td class="has-text-centered">
                        <a href="<?= $uri . '/course/' . $g['courseid'] . '/edit-feedback'; ?>" title="Editar el comentario del estudiante" id="editFeedback" name="editFeedback" class="button is-rounded is-info is-inverted is-small m-1">
                            <div class="icon-box">
                                <div data-icon="h"></div>
                            </div>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="columns filldown">
        <div class="column"></div>
        <div class="column is-half">
            <div class="notification is-danger is-light has-text-centered box">
                <button class="delete"></button>
                <h4 class="title is-4"> No se encontraron calificaciones</h4>
            </div>
        </div>
        <div class="column"></div>
    </div>
<?php endif; ?>
<?php include_once VIEW_FOOTER; ?>