<?php
session_start();
route($_SERVER['$request_uri'], $routes);

if (in_array($route_info['controller'], ['dashboard.php', 'settings.php'])) {
    auth_middleware();
    blocked();
}

$routes = [
    '' => 'home.php',
    'home' => 'home.php',
    'about' => 'about.php',
    'contact' => 'contact.php',
    'article/(\d+)' => 'article.php',
];

function route($uri, $routes) {
    foreach ($routes as $pattern => $controller) {
        if (preg_match("#^$pattern$#", $uri, $matches)) {
            return ['controller' => $controller, 'params' => array_slice($matches, 1)];
        }
    }
    return ['controller' => '404.php', 'params' => []];
}

$request_uri = trim($_SERVER['REQUEST_URI'], '/');
$route_info = route($request_uri, $routes);
require 'controllers/' . $route_info['controller'];


function auth_middleware() {
    if (!isset($_SESSION['userid'])) {
        header('Location: /login');
        exit;
    }
}

function blocked() {
    
}
