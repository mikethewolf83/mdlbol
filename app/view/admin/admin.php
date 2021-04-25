<?php include_once VIEW_HEADER; ?>
<?php if (!empty($execResult)) : ?>
    <script>
        alert("<?= $execResult; ?>");
        window.location.href = "<?= BASE_URL . '/admin'; ?>";
    </script>
<?php endif; ?>

<div class="columns is-mobile">
    <div class="column">
        <button title="Agregar un usuario que pueda acceder al sistema o un administrador" id="modal-adduser-trigger" class="button is-rounded is-inverted is-info is-pulled-right">
            <div class="mx-1" data-icon="b"></div>
            Agregar usuario
        </button>
        <a title="Limpiar la caché de las vistas" href="<?= BASE_URL . '/admin/?action=clearCache'; ?>" class="button is-rounded is-inverted is-info is-pulled-right mx-1">
            <div class="mx-1" data-icon="i"></div>
            Limpiar la caché
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
                            <span>Usuarios válidos</span>
                        </a>
                    </li>
                    <li>
                        <a>
                            <div class="mx-1" data-icon="d"></div>
                            <span>Adminsistradores</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tabs-content">
                <ul>
                    <li class="is-active">
                        <div class="columns">
                            <div class="column">
                                <table class="table is-striped is-fullwidth is-hoverable is-narrow has-text-centered">
                                    <!--<thead>
                                    <tr>
                                        <th>
                                            Estudiantes
                                        </th>
                                    </tr>
                                    </thead>-->
                                    <tbody>
                                        <?php foreach ($users as $u) : ?>
                                            <tr>
                                                <td style="width: 40%;">
                                                    <?= $u['username']; ?>
                                                </td>
                                                <td style="width: 40%;">
                                                    <a title="Eliminar usuario válido" href="<?= BASE_URL . '/admin/delete-user/' . $u['id']; ?>" class="button is-rounded is-inverted is-danger is-small">
                                                        <div class="mx-1" data-icon="c"></div>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="columns">
                            <div class="column">
                                <table class="table is-striped is-fullwidth is-hoverable is-narrow has-text-centered">
                                    <!--<thead>
                                    <tr>
                                        <th>
                                            Asignaturas
                                        </th>
                                    </tr>
                                    </thead>-->
                                    <tbody>
                                        <?php foreach ($admins as $a) : ?>
                                            <tr>
                                                <td style="width: 40%;">
                                                    <?= $a['username']; ?>
                                                </td>
                                                <td style="width: 40%;">
                                                    <a title="Eliminar usuario administrador" href="<?= BASE_URL . '/admin/delete-admin/' . $a['id']; ?>" class="button is-rounded is-inverted is-danger is-small">
                                                        <div class="mx-1" data-icon="c"></div>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="column"></div>

    <!--<div class="column">
        <form class="pt-4 pb-6 box" id="add-admin-form" name="add-admin-form" action="<?= BASE_URL; ?>/admin/?action=addUser" method="POST">
            <p class="heading has-text-info has-text-centered">Usuarios que existentes en Moodle CEEH</p>
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <div class="field mx-2">
                <label class="label <?= ($usernameError || $formError) ? 'has-text-danger' : ''; ?>"> Usuario: </label>
                <div class="control">
                    <input type="text" value="<?= (isset($_POST['username'])) ? $_POST['username'] : ''; ?>" name="username" id="username" placeholder="Introduzca el usuario..." class="input <?= ($usernameError || $formError) ? 'is-danger' : ''; ?>">
                    <p class="help is-danger"><?= $usernameError; ?></p>
                </div>
            </div>

            <div class="field mx-2">
                <label class="label <?= ($usertypeError || $formError) ? 'has-text-danger' : ''; ?>"> Rol de usuario: </label>
                <div class="select is-fullwidth <?= ($usertypeError || $formError) ? 'is-danger' : ''; ?>">
                    <select name="usertype" id="usertype">
                        <option value="">Selecciona...</option>
                        <option value="mb_admin_user" <?= (isset($_POST['usertype']) && $_POST['usertype'] == 'mb_admin_user') ? ' selected' : ''; ?>>Administrador</option>
                        <option value="mb_normal_user" <?= (isset($_POST['usertype']) && $_POST['usertype'] == 'mb_normal_user') ? ' selected' : ''; ?>>Usuario</option>
                    </select>
                    <p class="help is-danger"><?= $usertypeError; ?></p>
                    <p class="help is-danger"><?= $formError; ?></p>
                </div>
            </div>

            <div class="field mx-2 <?= ($usertypeError || $formError) ? 'mt-6' : 'mt-4'; ?>">
                <div class="control is-pulled-right">
                    <input type="submit" name="submit" id="submit" value="Agregar usuario" class="button is-outlined is-small is-rounded is-info">
                </div>
            </div>
        </form>
    </div>-->
</div>

<div id="modal-adduser" class="modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title has-text-info has-text-centered">Usuarios que existan en Moodle CEEH</p>
            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <form class="pt-4 pb-6" id="add-admin-form" name="add-admin-form" action="<?= BASE_URL; ?>/admin/?action=addUser" method="POST">
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                <div class="field mx-2">
                    <label class="label"> Usuario: </label>
                    <div class="control">
                        <input type="text" name="username" id="username" placeholder="Introduzca el usuario..." class="input">
                    </div>
                </div>

                <div class="field mx-2">
                    <label class="label"> Rol de usuario: </label>
                    <div class="select is-fullwidth">
                        <select name="usertype" id="usertype">
                            <option value="">Selecciona...</option>
                            <option value="mb_admin_user">Administrador</option>
                            <option value="mb_normal_user">Usuario</option>
                        </select>
                    </div>
                </div>
                <div class="field mx-2 mt-4">
                    <div class="control is-pulled-right">
                        <button type="submit" name="submit" id="submit" class="button is-rounded is-inverted is-info is-pulled-right mx-1">
                            <div data-icon="q"></div> Guardar
                        </button>
                    </div>
                </div>
            </form>
        </section>
        <footer class="modal-card-foot">
        </footer>
    </div>
</div>

<script>
    document.querySelector('#modal-adduser-trigger').addEventListener('click', function(e) {
        var modalAddUser = Bulma('#modal-adduser').modal();
        modalAddUser.open();
    });
</script>
<?php include_once VIEW_FOOTER; ?>