<?php
require_once __DIR__ . '/config.php';
session_start();
use Illuminate\Database\Capsule\Manager as Capsule;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => $db_file,
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

// Проверка авторизации
function authenticate($name, $password): bool
{
    $user = Capsule::table('users')->where('name', $name)->first();
    if ($user && password_verify($password, $user->password))
        return true;
    return false;
}

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['password'])) {
        $username = $_POST['name'];
        $password = $_POST['password'];
        if (authenticate($username, $password)) {
            $_SESSION['authenticated'] = true;
            header('Location: '.PACKAGE);
            exit;
        } else {
            echo 'Неправильный логин или пароль';
            exit;
        }
    } else {
        echo $twig->render('login.twig', ['path'=>PACKAGE.'/']);
        exit;
    }
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', PACKAGE . "/", \App\Controllers\ClientController::class . '@index');
    $r->addRoute('GET', PACKAGE . "/clients/create", \App\Controllers\ClientController::class . '@create');
    $r->addRoute('POST', PACKAGE . "/clients/store", \App\Controllers\ClientController::class . '@store');
    $r->addRoute('GET', PACKAGE . "/clients/{id:\d+}/edit", \App\Controllers\ClientController::class . '@edit');
    $r->addRoute('POST', PACKAGE . "/clients/{id:\d+}/update", \App\Controllers\ClientController::class . '@update');
    $r->addRoute('POST', PACKAGE . "/clients/{id:\d+}/delete", \App\Controllers\ClientController::class . '@destroy');

    $r->addRoute('GET', PACKAGE . "/tags", \App\Controllers\TagController::class . '@index');
    $r->addRoute('GET', PACKAGE . "/tags/create", \App\Controllers\TagController::class . '@create');
    $r->addRoute('POST', PACKAGE . "/tags/store", \App\Controllers\TagController::class . '@store');
    $r->addRoute('GET', PACKAGE . "/tags/{id:\d+}/edit", \App\Controllers\TagController::class . '@edit');
    $r->addRoute('POST', PACKAGE . "/tags/{id:\d+}/update", \App\Controllers\TagController::class . '@update');
    $r->addRoute('POST', PACKAGE . "/tags/{id:\d+}/delete", \App\Controllers\TagController::class . '@destroy');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($class, $method) = explode("@", $handler, 2);
        $controller = new $class($twig);
        call_user_func_array([$controller, $method], $vars);
        break;
}