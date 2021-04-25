<?php include_once VIEW_HEADER; ?>
<div class="columns is-mobile">
    <div class="column">
        <a title="Descargar todos los boletines de las asignaturas de este grupo" href="<?= $uri . 'courses-pdf'; ?>" class="button is-rounded is-inverted is-info is-pulled-right">
            <div class="mx-1" data-icon="e"></div>
            Asignaturas
        </a>
        <a title="Descargar todos los boletines de los estudiantes de este grupo" href="<?= $uri . 'students-pdf'; ?>" class="button is-rounded is-inverted is-info is-pulled-right mx-1">
            <div class="mx-1" data-icon="e"></div>
            Estudiantes
        </a>
    </div>
</div>

<div class="columns">
    <div class="column"></div>
    <div class="column is-two-thirds">
        <div class="tabs-wrapper box">
            <div class="tabs is-fullwidth">
                <ul>
                    <li class="is-active">
                        <a>
                            <div class="mx-1" data-icon="d"></div>
                            <span>Estudiantes</span>
                            <span class="tag is-rounded is-info mx-1"><?= $totalStudents; ?></span>
                        </a>
                    </li>
                    <li>
                        <a>
                            <div class="mx-1" data-icon="o"></div>
                            <span>Asignaturas</span>
                            <span class="tag is-rounded is-info mx-1"><?= $totalCourses; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tabs-content">
                <ul>
                    <li class="is-active">
                        <?php if ($students) : ?>
                            <table class="table is-striped is-fullwidth is-hoverable is-narrow has-text-centered">
                                <!--<thead>
                                    <tr>
                                        <th>
                                            Estudiantes
                                        </th>
                                    </tr>
                               </thead>-->
                                <tbody>
                                    <?php foreach ($students as $s) : ?>
                                        <tr>
                                            <td style="width: 40%;">
                                                <a href="<?= $studentsUri . '/' . $s['id']; ?>" target="_blank"><?= $s['username']; ?></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p>No se encontraron Estudiantes.</p>
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if ($courses) : ?>
                            <table class="table is-striped is-fullwidth is-hoverable is-narrow has-text-centered">
                                <!--<thead>
                                    <tr>
                                        <th>
                                            Asignaturas
                                        </th>
                                    </tr>
                               </thead>-->
                                <tbody>
                                    <?php foreach ($courses as $c) : ?>
                                        <tr>
                                            <td style="width: 40%;">
                                                <a href="<?= $courseUri . '/' . $c['CursoId']; ?>" target="_blank"><?= $c['Curso']; ?></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p>No se encontraron Asignaturas.</p>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="column"></div>
</div>
<?php include_once VIEW_FOOTER; ?>