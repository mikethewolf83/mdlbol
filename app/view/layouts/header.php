<?php
ob_start('minifier');
function minifier($code)
{
    $search = array(
        // Quitar espacios en blanco despues de las etiquetas
        '/\>[^\S ]+/s',
        // Quitar espacios en blanco antes de las etiquetas
        '/\>[^\S ]+\</s',
        // Quitar multiples espacios en blanco en secuencia
        '/(\s)+/s',
        // Quitar los comentarios
        '/<!--(.|\s)*?-->/'
    );

    $replace = array('>', '<', '\\1');
    $code = preg_filter($search, $replace, $code);
    return $code;
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="MdlBol: Boletines de calificaciones de Moodle CEEH">
    <meta name="keywords" content="CEEH, Moodle CEEH, Boletines de calificaciones CEEH">
    <meta name="author" content="Centro Educativo Español de La Habana">
    
    <script src="/assets/js/mdlbol-loader.min.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <link rel="stylesheet" type="text/css" href="/assets/css/mdlbol.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/mdlbol-custom.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/mdlbol-icons.min.css">
    <script src="/assets/js/mdlbol.min.js"></script>
    <title>MdlBol - <?= $title; ?></title>
</head>

<body <?= (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) ? 'class="has-navbar-fixed-top"' : '' ?>>
    <div class="preloader">
        <span><img src="/assets/images/loading.gif" alt=""></span>
    </div>
    <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] == true) : ?>
        <nav class="navbar <?= ($title == '403' || $title == '404' || $title == 'Excepción') ? 'is-danger' : 'is-info'; ?> is-fixed-top" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="/">
                    <img src="/assets/images/logo.png" width="112" height="28">
                </a>

                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="topNavbar">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>

            </div>

            <div id="topNavbar" class="navbar-menu">
                <div class="navbar-start">
                    <!-- BEGIN 1er Trimestre -->
                    <div class="navbar-item has-dropdown is-hoverable is-mega">
                        <?= (str_contains($_SERVER['REQUEST_URI'], '/first-trim'))  ? '<a class="navbar-link is-active"><div data-icon="p"></div> 1er Trimestre</a>' : '<a class="navbar-link"><div data-icon="p"></div> 1er Trimestre</a>' ?>
                        <div class="navbar-dropdown">
                            <div class="container is-fluid">
                                <div class="has-text-centered has-text-info-dark title is-6 heading">1er trimestre</div>
                                <hr class="navbar-divider">
                                <div class="columns">
                                    <div class="column">
                                        <h1 class="title is-6 is-mega-menu-title">Primaria</h1>
                                        <hr class="navbar-divider">
                                        <?php
                                        for ($x = 1; $x <= 6; $x++) {
                                            echo (str_contains($_SERVER['REQUEST_URI'], '/first-trim/primaria/' . $x . '-EP/')) ? '<a class="navbar-item is-active" href="/first-trim/primaria/' . $x . '-EP/"> <div class="navbar-content">' . $x . 'EP </div> </a>' : '<a class="navbar-item" href="/first-trim/primaria/' . $x . '-EP/"> <div class="navbar-content">' . $x . 'EP </div> </a>';
                                        }
                                        ?>
                                    </div>
                                    <div class="column">
                                        <h1 class="title is-6 is-mega-menu-title">ESO</h1>
                                        <hr class="navbar-divider">
                                        <?php
                                        for ($x = 1; $x <= 4; $x++) {
                                            echo (str_contains($_SERVER['REQUEST_URI'], '/first-trim/eso/' . $x . '-ESO/')) ? '<a class="navbar-item is-active" href="/first-trim/eso/' . $x . '-ESO/">' . $x . 'ESO </a>' : '<a class="navbar-item" href="/first-trim/eso/' . $x . '-ESO/">' . $x . 'ESO </a>';
                                        }
                                        ?>
                                    </div>
                                    <div class="column">
                                        <h1 class="title is-6 is-mega-menu-title">BACH</h1>
                                        <hr class="navbar-divider">
                                        <?php
                                        for ($x = 1; $x <= 2; $x++) {
                                            echo (str_contains($_SERVER['REQUEST_URI'], '/first-trim/bach/' . $x . '-BACH/')) ? '<a class="navbar-item is-active" href="/first-trim/bach/' . $x . '-BACH/">' . $x . 'BACH </a>' : '<a class="navbar-item" href="/first-trim/bach/' . $x . '-BACH/">' . $x . 'BACH </a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END 1er Trimestre -->

                    <!-- BEGIN 2do Trimestre -->
                    <div class="navbar-item has-dropdown is-hoverable is-mega">
                        <?= (str_contains($_SERVER['REQUEST_URI'], '/second-trim'))  ? '<a class="navbar-link is-active"><div data-icon="p"></div> 2do Trimestre</a>' : '<a class="navbar-link"><div data-icon="p"></div> 2do Trimestre</a>' ?>

                        <div class="navbar-dropdown">
                            <div class="container is-fluid">
                                <div class="has-text-centered has-text-info-dark title is-6 heading">2do trimestre</div>
                                <hr class="navbar-divider">
                                <div class="columns">
                                    <div class="column">
                                        <h1 class="title is-6 is-mega-menu-title">Primaria</h1>
                                        <hr class="navbar-divider">
                                        <?php
                                        for ($x = 1; $x <= 6; $x++) {
                                            echo (str_contains($_SERVER['REQUEST_URI'], '/second-trim/primaria/' . $x . '-EP/')) ? '<a class="navbar-item is-active" href="/second-trim/primaria/' . $x . '-EP/"> <div class="navbar-content">' . $x . 'EP </div> </a>' : '<a class="navbar-item" href="/second-trim/primaria/' . $x . '-EP/"> <div class="navbar-content">' . $x . 'EP </div> </a>';
                                        }
                                        ?>
                                    </div>
                                    <div class="column">
                                        <h1 class="title is-6 is-mega-menu-title">ESO</h1>
                                        <hr class="navbar-divider">
                                        <?php
                                        for ($x = 1; $x <= 4; $x++) {
                                            echo (str_contains($_SERVER['REQUEST_URI'], '/second-trim/eso/' . $x . '-ESO/')) ? '<a class="navbar-item is-active" href="/second-trim/eso/' . $x . '-ESO/">' . $x . 'ESO </a>' : '<a class="navbar-item" href="/second-trim/eso/' . $x . '-ESO/">' . $x . 'ESO </a>';
                                        }
                                        ?>
                                    </div>
                                    <div class="column">
                                        <h1 class="title is-6 is-mega-menu-title">BACH</h1>
                                        <hr class="navbar-divider">
                                        <?php
                                        for ($x = 1; $x <= 2; $x++) {
                                            echo (str_contains($_SERVER['REQUEST_URI'], '/second-trim/bach/' . $x . '-BACH/')) ? '<a class="navbar-item is-active" href="/second-trim/bach/' . $x . '-BACH/">' . $x . 'BACH </a>' : '<a class="navbar-item" href="/second-trim/bach/' . $x . '-BACH/">' . $x . 'BACH </a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END 2do Trimestre -->

                    <!-- BEGIN 3er Trimestre -->
                    <div class="navbar-item has-dropdown is-hoverable is-mega">
                        <?= (str_contains($_SERVER['REQUEST_URI'], '/third-trim'))  ? '<a class="navbar-link is-active"><div data-icon="p"></div> 3er Trimestre</a>' : '<a class="navbar-link"><div data-icon="p"></div> 3er Trimestre</a>' ?>

                        <div class="navbar-dropdown">
                            <div class="container is-fluid">
                                <div class="has-text-centered has-text-info-dark title is-6 heading">3er trimestre</div>
                                <hr class="navbar-divider">
                                <div class="columns">
                                    <div class="column">
                                        <h1 class="title is-6 is-mega-menu-title">Primaria</h1>
                                        <hr class="navbar-divider">
                                        <?php
                                        for ($x = 1; $x <= 6; $x++) {
                                            echo (str_contains($_SERVER['REQUEST_URI'], '/third-trim/primaria/' . $x . '-EP/')) ? '<a class="navbar-item is-active" href="/third-trim/primaria/' . $x . '-EP/"> <div class="navbar-content">' . $x . 'EP </div> </a>' : '<a class="navbar-item" href="/third-trim/primaria/' . $x . '-EP/"> <div class="navbar-content">' . $x . 'EP </div> </a>';
                                        }
                                        ?>
                                    </div>
                                    <div class="column">
                                        <h1 class="title is-6 is-mega-menu-title">ESO</h1>
                                        <hr class="navbar-divider">
                                        <?php
                                        for ($x = 1; $x <= 4; $x++) {
                                            echo (str_contains($_SERVER['REQUEST_URI'], '/third-trim/eso/' . $x . '-ESO/')) ? '<a class="navbar-item is-active" href="/third-trim/eso/' . $x . '-ESO/">' . $x . 'ESO </a>' : '<a class="navbar-item" href="/third-trim/eso/' . $x . '-ESO/">' . $x . 'ESO </a>';
                                        }
                                        ?>
                                    </div>
                                    <div class="column">
                                        <h1 class="title is-6 is-mega-menu-title">BACH</h1>
                                        <hr class="navbar-divider">
                                        <?php
                                        for ($x = 1; $x <= 2; $x++) {
                                            echo (str_contains($_SERVER['REQUEST_URI'], '/third-trim/bach/' . $x . '-BACH/')) ? '<a class="navbar-item is-active" href="/third-trim/bach/' . $x . '-BACH/">' . $x . 'BACH </a>' : '<a class="navbar-item" href="/third-trim/bach/' . $x . '-BACH/">' . $x . 'BACH </a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END 3er Trimestre -->
                </div>
                <div class="navbar-end">
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link is-rounded"><div data-icon="a"></div> <?= $_SESSION['user_fullname']; ?></a>
                        <div class="navbar-dropdown">
                            <?php if (isset($_SESSION['user_admin']) && ($_SESSION['user_admin'] == true)) : ?>
                                <a class="navbar-item" href="<?= BASE_URL; ?>/admin">
                                    <p class="heading"><div class="mx-1" data-icon="m"></div> Administración</p>
                                </a>
                            <?php endif; ?>

                            <a class="navbar-item" href="<?= BASE_URL; ?>/users/logout">
                                <p class="heading"><div class="mx-1" data-icon="l"></div> Salir de MdlBol</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    <?php endif; ?>
    <section class="<?= ($title == '403' || $title == '404' || $title == 'Excepción') ? 'hero is-danger has-text-centered' : 'hero is-small is-info has-text-centered'; ?>">
        <div class="hero-body">
            <h4 class="title is-4"><?= $title; ?></h4>
        </div>
    </section>
    <section id="wrapper" class="section">
