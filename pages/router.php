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
];

$chatid = $_GET["chat_id"] ?? null;
$request_uri = trim($_SERVER['REQUEST_URI'], '/');
$route_info = route($request_uri, $routes);

// Add authentication middleware
if (!in_array($route_info['controller'], ['home.php', 'register.php', 'login.php'])) {
    auth_middleware();
}

// if (isset($_GET['route'])) {
//     $route = $_GET['route'];
//     if (array_key_exists($route, $routes)) {
//         $route_info = ['controller' => $routes[$route], 'params' => []]; // Update $route_info
//     }
// }


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
