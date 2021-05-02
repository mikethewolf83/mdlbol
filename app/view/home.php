<?php include_once VIEW_HEADER; ?>
<div class="columns">
    <div class="column is-one-fifth"></div>
    <div class="column">
        <div class="box">
            <p class="heading has-text-primary-dark">Estudiantes de Primaria</p>
            <div class="level is-mobile">
                <div class="level-left">
                    <div class="level-item">
                        <div class="mx-1 has-text-primary-dark" data-icon="d"></div>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item">
                        <p class="title has-text-primary-dark"><?= $countStudEP['cuenta']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="column">
        <div class="box">
            <p class="heading has-text-info-dark">Estudiantes de ESO</p>
            <div class="level is-mobile">
                <div class="level-left">
                    <div class="level-item">
                        <div class="mx-1 has-text-info-dark" data-icon="d"></div>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item">
                        <p class="title has-text-info-dark"><?= $countStudESO['cuenta']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="column">
        <div class="box">
            <p class="heading has-text-warning-dark">Estudiantes de BACH</p>
            <div class="level is-mobile">
                <div class="level-left">
                    <div class="level-item">
                        <div class="mx-1 has-text-warning-dark" data-icon="d"></div>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item">
                        <p class="title has-text-warning-dark"><?= $countStudBACH['cuenta']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="column is-one-fifth"></div>
</div>

<div class="columns">
    <div class="column is-one-fifth"></div>
    <div class="column">
        <div class="box">
            <p class="heading has-text-primary-dark">Asignaturas de Primaria</p>
            <div class="level is-mobile">
                <div class="level-left">
                    <div class="level-item">
                        <div class="mx-1 has-text-primary-dark" data-icon="o"></div>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item">
                        <p class="title has-text-primary-dark"><?= $countCoursesEP['cuenta']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="column">
        <div class="box">
            <p class="heading has-text-info-dark">Asignaturas de ESO</p>
            <div class="level is-mobile">
                <div class="level-left">
                    <div class="level-item">
                        <div class="mx-1 has-text-info-dark" data-icon="o"></div>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item">
                        <p class="title has-text-info-dark"><?= $countCoursesESO['cuenta']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="column">
        <div class="box">
            <p class="heading has-text-warning-dark">Asignaturas de BACH</p>
            <div class="level is-mobile">
                <div class="level-left">
                    <div class="level-item">
                        <div class="mx-1 has-text-warning-dark" data-icon="o"></div>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item">
                        <p class="title has-text-warning-dark"><?= $countCoursesBACH['cuenta']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="column is-one-fifth"></div>
</div>
<?php include_once VIEW_FOOTER; ?>