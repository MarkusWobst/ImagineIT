<?php
session_start();

$routes = [
    '' => 'home.php',
    '/' => 'home.php',
    'home' => 'home.php',
    'home.php' => 'home.php',
    'chat' => 'chat.php',
    'chat.php' => 'chat.php',
    'help' => 'help.php',
    'help.php' => 'help.php',
    'index' => 'index.php',
    'index.php' => 'index.php',
    'login' => 'login.php',
    'login.php' => 'login.php',
    'register' => 'register.php',
    'register.php' => 'register.php',
    'settings' => 'settings.php',
    'settings.php' => 'settings.php',
    'blocked' => 'blocked.php',
    'blocked.php' => 'blocked.php',
    'process-message' => 'process-message.php',
    'process-message.php' => 'process-message.php',
    'logout' => 'logout.php',
    'logout.php' => 'logout.php',
];

$chatid = $_GET["chat_id"] ?? null;
$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$route_info = route($request_uri, $routes);

// var_dump($request_uri);
// var_dump($route_info);
// die;

// check for pictures
$folderPath = '../pictures';
$pictureExtensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif']; // adjust the extensions you consider as pictures
$files = scandir($folderPath);

$displayPicture = false;
foreach ($files as $file) {
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    if (in_array($extension, $pictureExtensions)) {
        if ($request_uri == $file) {
            $displayPicture = true;
            // Display the picture
            header('Content-Type: image/' . $extension);
            readfile($folderPath . '/' . $file);
            exit;
        }
    }
}

// Add authentication middleware
if (!in_array($route_info['controller'], ['home.php', 'register.php', 'login.php'])) {
    auth_middleware();
}

if (!$displayPicture) {
    require $route_info['controller'];
}

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
