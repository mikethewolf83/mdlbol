<?php
if (!isset($_SESSION)) {
    session_start();
}

$autoloader = include __DIR__ . '/../vendor/autoload.php';
$autoloader->addPsr4('MdlBol\\', __DIR__ . '/../app/src');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

define('APPDIR', dirname(__DIR__));
define('PUBLIC_DIR', __DIR__);
define('BASE_URL', $_ENV['BASE_URL']);
define('VERSION', file_get_contents(APPDIR . '/VERSION'));
define('VIEW_HEADER', APPDIR . '/app/view/layouts/header.php');
define('VIEW_FOOTER', APPDIR . '/app/view/layouts/footer.php');

try {
    $app = new Pop\Application($autoloader, include __DIR__ . '/../app/config/app.http.php');
    $app->register(new MdlBol\Module());
    $app->run();
} catch (\Exception $exception) {
    $app = new MdlBol\Module();
    $app->httpError($exception);
}
