<?php
session_start();

$routes = [
    '' => 'home.php',
    '/' => 'home.php',
    'home' => 'home.php',
    'chat' => 'chat.php',
    'help' => 'help.php',
    'index' => 'index.php',
    'login' => 'login.php',
    'register' => 'register.php',
    'settings' => 'settings.php',
    'blocked' => 'blocked.php',
    'process-message' => 'process-message.php',
];

$chatid = $_GET["chat_id"] ?? null;
$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
var_dump($request_uri);

$route_info = route($request_uri, $routes);
var_dump($route_info);


// Add authentication middleware
if (!in_array($route_info['controller'], ['home.php', 'register.php', 'login.php'])) {
    auth_middleware();
}

var_dump($route_info);
require $route_info['controller'];

// route($_SERVER['REQUEST_URI'], $routes);

// header('Location: chat?chat_id=' . $chatid);
function route($uri, $routes)
{
    foreach ($routes as $pattern => $controller) {
        if (preg_match("#^$pattern$#", $uri, $matches)) {
            return ['controller' => $controller, 'params' => array_slice($matches, 1)];
        }
    }
    return ['controller' => '404.php', 'params' => []];
}

function auth_middleware()
{
    if (!isset($_SESSION['userid'])) {
        header('Location: /login');
        exit;
    }
}
