<?php include_once VIEW_HEADER; ?>
<div class="columns">
    <div class="column"></div>
    <div class="column">
        <div class="panel-heading has-text-info has-text-centered">
            Iniciar sesión
        </div>
        <div class="box">
            <form class="pt-4 pb-6" id="login-form" name="login-form" action="<?= BASE_URL; ?>/users/login" method="POST">
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                <div class="field mx-2">
                    <label class="label <?= ($usernameError || $formError) ? 'has-text-danger' : ''; ?>"> Usuario: </label>
                    <div class="control">
                        <input type="text" value="<?= (isset($_POST['username'])) ? $_POST['username'] : ''; ?>" name="username" id="username" placeholder="Introduzca el usuario..." class="input <?= ($usernameError || $formError) ? 'is-danger' : ''; ?>" autocomplete="username">
                        <p class="help is-danger"><?= $usernameError; ?></p>
                    </div>
                </div>
                <div class="field mx-2">
                    <label class="label <?= ($passwordError || $formError) ? 'has-text-danger' : ''; ?>"> Contraseña: </label>
                    <div class="control">
                        <input type="password" name="password" id="password" placeholder="Introduzca la contraseña..." class="input <?= ($passwordError || $formError) ? 'is-danger' : ''; ?>" autocomplete="current-password">
                        <p class="help is-danger"><?= $passwordError; ?></p>
                        <p class="help is-danger"><?= $formError; ?></p>
                        <p class="help is-danger"><?= $userTokenError; ?></p>
                    </div>
                </div>
                <div class="field mx-2 mt-4">
                    <div class="control is-pulled-right">
                        <button type="submit" name="submit" id="submit" class="button is-outlined is-rounded is-info">
                            <div data-icon="k"></div>
                            <span>Autenticarse</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="block">
            <p class="has-text-centered">
                ¿Prefieres acceder por una versión móvil?
            </p>
            <p class="has-text-centered">
                Descarga la aplicación para dispositivos Android desde aquí...
            </p>
            <p class="has-text-centered">
                <a title="Descargar aplicación para dispositivos Android" href="<?= $_ENV['BASE_URL'] . '/assets/net.ceehabana.mdlbol.apk'; ?>" target="_blank" class="button is-rounded is-inverted is-info" rel="noopener noreferrer"><i class="mx-1" data-icon="g"></i></a>
            </p>
        </div>
    </div>
    <div class="column"></div>
</div>
<?php include_once VIEW_FOOTER; ?>