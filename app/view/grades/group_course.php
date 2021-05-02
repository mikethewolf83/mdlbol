<?php include_once VIEW_HEADER; ?>
<?php if (!empty($execResult)) : ?>
    <script>
        alert("<?= $execResult; ?>");
        window.location.href = "<?= BASE_URL . $courseUri . '/' . $course; ?>";
    </script>
<?php endif; ?>

<?php if ($grades) : ?>
    <div class="columns off-print">
        <div class="column">
            <p class="heading">Se encontraron <?= $total; ?> resultados</p>
        </div>
        <div class="column">
            <a title="Vista previa del boletín de <?= $title; ?>" href="<?= $uri . '/pdf/?action=view'; ?>" class="button is-rounded is-inverted is-info is-pulled-right mx-1" target="_blank">
                <div class="icon-box">
                    <div class="mx-1" data-icon="f"></div>
                    <span>Vista Previa</span>
                </div>
            </a>
            <a title="Descargar el boletín de <?= $title; ?>" href="<?= $uri . '/pdf/?action=download'; ?>" class="button is-rounded is-inverted is-info is-pulled-right mx-1">
                <div class="icon-box">
                    <div class="mx-1" data-icon="g"></div>
                    <span>Descargar</span>
                </div>
            </a>
        </div>
    </div>
    <table class="table table_wrapper is-fullwidth is-hoverable box">
        <thead>
            <!-- Si la sede en ESO o BACH se muestran las unidades -->
            <?php if (($campus == 'eso') || ($campus == 'bach')) : ?>
                <tr>
                    <th style="width: 25%;">
                        Nombre y Apellidos
                    </th>
                    <th>
                        U1
                    </th>
                    <th>
                        U2
                    </th>
                    <!-- Se muestra la nota de la unidad 3 si el curso tiene unidad 3 -->
                    <?php if (in_array($course, array_column((array)$coursesWithU3, 'courseid'))) : ?>
                        <th>
                            U3
                        </th>
                    <?php endif; ?>
                    <!-- Se muestra la nota de la unidad 4 si el curso tiene unidad 4 -->
                    <?php if (in_array($course, array_column((array)$coursesWithU4, 'courseid'))) : ?>
                        <th>
                            U4
                        </th>
                    <?php endif; ?>
                    <!-- Se muestra la nota de la unidad 5 si el curso tiene unidad 5 -->
                    <?php if (in_array($course, array_column((array)$coursesWithU5, 'courseid'))) : ?>
                        <th>
                            U5
                        </th>
                    <?php endif; ?>
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
                        Nombre y Apellidos
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
                <!-- Si el estudiante tiene comentario al CIDEAD se muestra -->
                <?php $hasFeedback = $dataFeedback->hasFeedbackCidead($g['userid'], $course); ?>
                <?php $feedback = $dataFeedback->getFeedbackCidead($trim, $campus, $group, $course, $g['userid']); ?>

                <?php if ($hasFeedback['cuenta'] == 1) : ?>
                    <!-- Si la sede en ESO o BACH se muestran las unidades -->
                    <?php if (($campus == 'eso') || ($campus == 'bach')) : ?>
                        <tr>
                            <td rowspan="2" style="width: 25%;">
                                <a href="<?= $studentsUri . '/' . $g['userid']; ?>" target="_blank"><?= $g['firstname'] . ' ' . $g['lastname']; ?></a>
                            </td>
                            <td class="has-text-centered">
                                <?= round($g['U1_NotaFinal'], 1); ?>
                            </td>
                            <td class="has-text-centered">
                                <?= round($g['U2_NotaFinal'], 1); ?>
                            </td>
                            <!-- Se muestra la nota de la unidad 3 si el curso tiene unidad 3 -->
                            <?php if (in_array($course, array_column((array)$coursesWithU3, 'courseid'))) : ?>
                                <td class="has-text-centered">
                                    <?php $colspan = 4; ?>
                                    <?= round($g['U3_NotaFinal'], 1); ?>
                                </td>
                            <?php endif; ?>
                            <!-- Se muestra la nota de la unidad 4 si el curso tiene unidad 4 -->
                            <?php if (in_array($course, array_column((array)$coursesWithU4, 'courseid'))) : ?>
                                <?php $colspan = 5; ?>
                                <td class="has-text-centered">
                                    <?= round($g['U4_NotaFinal'], 1); ?>
                                </td>
                            <?php endif; ?>
                            <!-- Se muestra la nota de la unidad 5 si el curso tiene unidad 5 -->
                            <?php if (in_array($course, array_column((array)$coursesWithU5, 'courseid'))) : ?>
                                <?php $colspan = 6; ?>
                                <td class="has-text-centered">
                                    <?= round($g['U5_NotaFinal'], 1); ?>
                                </td>
                            <?php endif; ?>
                            <td class="has-text-centered" style="width: 15%;">
                                <?= round($g['NotaTrimestre'], 1); ?>
                            </td>
                            <td spellcheck="true" contenteditable="true" title="Al dar clic se marcan los errores ortográficos si hubiera alguno, para editar el comentario de clic en el botón Editar.">
                                <p> <?= $g['TrimestreObservaciones']; ?> </p>
                            </td>
                            <td>
                                <a href="<?= $uri . '/student/' . $g['userid'] . '/edit-feedback'; ?>" title="Editar el comentario del estudiante" class="button is-rounded is-info is-inverted is-small m-1">
                                    <div class="icon-box">
                                        <div data-icon="h"></div>
                                    </div>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="<?= $colspan; ?>" class="has-text-centered is-mobile">
                                <img src="/assets/images/cidead.png">
                            </td>
                            <td spellcheck="true" contenteditable="true" title="Al dar clic se marcan los errores ortográficos si hubiera alguno, para editar el comentario de clic en el botón Editar.">
                                <!-- Se muestra el comentario al CIDEAD -->
                                <p> <?= (isset($feedback)) ? $feedback['feedback_cidead'] : ''; ?> </p>
                            </td>
                            <td>
                                <div class="level">
                                    <div class="level-item">
                                        <a href="<?= $uri . '/student/' . $g['userid'] . '/edit-feedback-cidead'; ?>" title="Editar el comentario al CIDEAD" class="button is-rounded is-info is-inverted is-small m-1">
                                            <div data-icon="h"></div>
                                        </a>
                                    </div>
                                    <div class="level-item">
                                        <!-- Eliminar comentario al CIDEAD -->
                                        <form id="delete-feedback-cidead" name="delete-feedback-cidead" action="<?= $uri; ?>/?action=deleteFeedbackCidead" method="POST">
                                            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="student" id="student" value="<?= $g['userid']; ?>">
                                            <button title="Eliminar el comentario al CIDEAD" type="submit" name="submit" id="submit" class="button is-rounded is-danger is-inverted is-small m-1">
                                                <div data-icon="j"></div>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Si no, entonces no se muestran las unidades -->
                    <?php else : ?>
                        <tr>
                            <td rowspan="2" style="width: 25%;">
                                <a href="<?= $studentsUri . '/' . $g['userid']; ?>" target="_blank"><?= $g['firstname'] . ' ' . $g['lastname']; ?></a>
                            </td>
                            <td class="has-text-centered" style="width: 15%;">
                                <?= round($g['NotaTrimestre'], 1); ?>
                            </td>
                            <td spellcheck="true" contenteditable="true" title="Al dar clic se marcan los errores ortográficos si hubiera alguno, para editar el comentario de clic en el botón Editar.">
                                <p> <?= $g['TrimestreObservaciones']; ?> </p>
                            </td>
                            <td>
                                <a href="<?= $uri . '/student/' . $g['userid'] . '/edit-feedback'; ?>" title="Editar el comentario del estudiante" class="button is-rounded is-info is-inverted is-small m-1">
                                    <div class="icon-box">
                                        <div data-icon="h"></div>
                                    </div>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="has-text-centered is-mobile">
                                <img src="/assets/images/cidead.png">
                            </td>
                            <td spellcheck="true" contenteditable="true" title="Al dar clic se marcan los errores ortográficos si hubiera alguno, para editar el comentario de clic en el botón Editar.">
                                <!-- Se muestra el comentario al CIDEAD -->
                                <p> <?= (isset($feedback)) ? $feedback['feedback_cidead'] : ''; ?> </p>
                            </td>
                            <td>
                                <div class="level">
                                    <div class="level-item">
                                        <a href="<?= $uri . '/student/' . $g['userid'] . '/edit-feedback-cidead'; ?>" title="Editar el comentario al CIDEAD" class="button is-rounded is-info is-inverted is-small m-1">
                                            <div data-icon="h"></div>
                                        </a>
                                    </div>
                                    <div class="level-item">
                                        <!-- Eliminar comentario al CIDEAD -->
                                        <form id="delete-feedback-cidead" name="delete-feedback-cidead" action="<?= $uri; ?>/?action=deleteFeedbackCidead" method="POST">
                                            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="student" id="student" value="<?= $g['userid']; ?>">
                                            <button title="Eliminar el comentario al CIDEAD" type="submit" name="submit" id="submit" class="button is-rounded is-danger is-inverted is-small m-1">
                                                <div data-icon="j"></div>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <!-- Si el estudiante no tiene comentario al CIDEAD se muestra solo el comentario al estudiante -->
                <?php else : ?>
                    <!-- Si la sede en ESO o BACH se muestran las unidades -->
                    <?php if (($campus == 'eso') || ($campus == 'bach')) : ?>
                        <tr>
                            <td>
                                <a href="<?= $studentsUri . '/' . $g['userid']; ?>" target="_blank"><?= $g['firstname'] . ' ' . $g['lastname']; ?></a>
                            </td>
                            <td class="has-text-centered">
                                <?= round($g['U1_NotaFinal'], 1); ?>
                            </td>
                            <td class="has-text-centered">
                                <?= round($g['U2_NotaFinal'], 1); ?>
                            </td>
                            <!-- Se muestra la nota de la unidad 3 si el curso tiene unidad 3 -->
                            <?php if (in_array($course, array_column((array)$coursesWithU3, 'courseid'))) : ?>
                                <td class="has-text-centered">
                                    <?= round($g['U3_NotaFinal'], 1); ?>
                                </td>
                            <?php endif; ?>
                            <!-- Se muestra la nota de la unidad 4 si el curso tiene unidad 4 -->
                            <?php if (in_array($course, array_column((array)$coursesWithU4, 'courseid'))) : ?>
                                <td class="has-text-centered">
                                    <?= round($g['U4_NotaFinal'], 1); ?>
                                </td>
                            <?php endif; ?>
                            <!-- Se muestra la nota de la unidad 5 si el curso tiene unidad 5 -->
                            <?php if (in_array($course, array_column((array)$coursesWithU5, 'courseid'))) : ?>
                                <td class="has-text-centered">
                                    <?= round($g['U5_NotaFinal'], 1); ?>
                                </td>
                            <?php endif; ?>
                            <td class="has-text-centered">
                                <?= round($g['NotaTrimestre'], 1); ?>
                            </td>
                            <td spellcheck="true" contenteditable="true" title="Al dar clic se marcan los errores ortográficos si hubiera alguno, para editar el comentario de clic en el botón Editar.">
                                <p> <?= $g['TrimestreObservaciones']; ?> </p>
                            </td>
                            <td>
                                <div class="level">
                                    <div class="level-item">
                                        <a href="<?= $uri . '/student/' . $g['userid'] . '/edit-feedback'; ?>" title="Editar el comentario del estudiante" class="button is-rounded is-info is-inverted is-small m-1">
                                            <div class="icon-box">
                                                <div data-icon="h"></div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="level-item">
                                        <a href="<?= $uri . '/student/' . $g['userid'] . '/add-feedback-cidead'; ?>" title="Agregar comentario al CIDEAD" class="button is-rounded is-info is-inverted is-small m-1">
                                            <div class="icon-box">
                                                <div data-icon="s"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Si el estudiante no tiene comentario al CIDEAD se muestra solo el comentario al estudiante -->
                    <?php else : ?>
                        <tr>
                            <td>
                                <a href="<?= $studentsUri . '/' . $g['userid']; ?>" target="_blank"><?= $g['firstname'] . ' ' . $g['lastname']; ?></a>
                            </td>
                            <td class="has-text-centered">
                                <?= round($g['NotaTrimestre'], 1); ?>
                            </td>
                            <td spellcheck="true" contenteditable="true" title="Al dar clic se marcan los errores ortográficos si hubiera alguno, para editar el comentario de clic en el botón Editar.">
                                <p> <?= $g['TrimestreObservaciones']; ?> </p>
                            </td>
                            <td>
                                <div class="level">
                                    <div class="level-item">
                                        <a href="<?= $uri . '/student/' . $g['userid'] . '/edit-feedback'; ?>" title="Editar el comentario del estudiante" class="button is-rounded is-info is-inverted is-small m-1">
                                            <div class="icon-box">
                                                <div data-icon="h"></div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="level-item">
                                        <a href="<?= $uri . '/student/' . $g['userid'] . '/add-feedback-cidead'; ?>" title="Agregar comentario al CIDEAD" class="button is-rounded is-info is-inverted is-small m-1">
                                            <div class="icon-box">
                                                <div data-icon="s"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
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