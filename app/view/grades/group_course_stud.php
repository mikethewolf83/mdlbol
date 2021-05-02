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
            <!-- Si la sede en ESO o BACH se muestran las unidades -->
            <?php if (($campus == 'eso') || ($campus == 'bach')) : ?>
                <tr>
                    <th style="width: 25%;">
                        Asignatura
                    </th>
                    <th>
                        U1
                    </th>
                    <th>
                        U2
                    </th>
                    <th>
                        U3
                    </th>
                    <th>
                        U4
                    </th>
                    <th>
                        U5
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
                <!-- Si no, entonces no se muestran las unidades -->
            <?php else : ?>
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
            <?php endif; ?>
        </thead>
        <tbody>
            <?php foreach ($grades as $g) : ?>
                <!-- Si la sede en ESO o BACH se muestran las unidades -->
                <?php if (($campus == 'eso') || ($campus == 'bach')) : ?>
                    <tr>
                        <td>
                            <a href="<?= $courseUri . '/' . $g['courseid']; ?>" target="_blank"><?= $g['Curso']; ?></a>
                        </td>
                        <td class="has-text-centered">
                            <?= round($g['U1_NotaFinal'], 1); ?>
                        </td>
                        <td class="has-text-centered">
                            <?= round($g['U2_NotaFinal'], 1); ?>
                        </td>
                        <!-- Se muestra la nota de la unidad 3 si el curso tiene unidad 3 -->
                        <?php if (NULL != $g['U3_NotaFinal']) : ?>
                            <td class="has-text-centered">
                                <?= round($g['U3_NotaFinal'], 1); ?>
                            </td>
                        <?php else : ?>
                            <td class="has-text-centered">
                                -
                            </td>
                        <?php endif; ?>
                        <!-- Se muestra la nota de la unidad 4 si el curso tiene unidad 4 -->
                        <?php if (NULL != $g['U4_NotaFinal']) : ?>
                            <td class="has-text-centered">
                                <?= round($g['U4_NotaFinal'], 1); ?>
                            </td>
                        <?php else : ?>
                            <td class="has-text-centered">
                                -
                            </td>
                        <?php endif; ?>
                        <!-- Se muestra la nota de la unidad 5 si el curso tiene unidad 5 -->
                        <?php if (NULL != $g['U5_NotaFinal']) : ?>
                            <td class="has-text-centered">
                                <?php $colspan = 6; ?>
                                <?= round($g['U5_NotaFinal'], 1); ?>
                            </td>
                        <?php else : ?>
                            <td class="has-text-centered">
                                -
                            </td>
                        <?php endif; ?>
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
                <?php else : ?>
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
                <?php endif; ?>
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