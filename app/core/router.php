<?php

require_once __DIR__ . '/BaseController.php';

// Route configuratie
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Routes definieren
$routes = [
    'GET' => [
        '/' => ['HomeController', 'index'],
        '/login' => ['AuthController', 'loginForm'],
        '/register' => ['AuthController', 'registerForm'],
        '/profile' => ['ProfileController', 'show'],
        '/profile/edit' => ['ProfileController', 'edit'],
        '/search' => ['SearchController', 'index'],
        '/posts/create' => ['PostController', 'create'],
        '/posts/(\d+)' => ['PostController', 'show'],
    ],
    'POST' => [
        '/login' => ['AuthController', 'login'],
        '/register' => ['AuthController', 'register'],
        '/logout' => ['AuthController', 'logout'],
        '/profile/update' => ['ProfileController', 'update'],
        '/profile/password' => ['ProfileController', 'updatePassword'],
        '/posts/create' => ['PostController', 'store'],
        '/posts/like' => ['PostController', 'like'],
        '/comments/create' => ['CommentController', 'store'],
        '/follow' => ['ProfileController', 'follow'],
    ]
];

// Route vinden en controller aanroepen
$matchedRoute = null;
$params = [];

foreach ($routes[$method] as $pattern => $handler) {
    // Eenvoudige route zonder parameters
    if ($pattern === $uri) {
        $matchedRoute = $handler;
        break;
    }

    // Route met parameters (ID's)
    $patternRegex = str_replace('/', '\/', $pattern);
    if (preg_match('/^' . $patternRegex . '$/', $uri, $matches)) {
        $matchedRoute = $handler;
        // Verwijder de volledige match
        array_shift($matches);
        $params = $matches;
        break;
    }
}

if ($matchedRoute) {
    $controllerName = $matchedRoute[0];
    $action = $matchedRoute[1];

    require_once __DIR__ . "/../controllers/$controllerName.php";
    $controller = new $controllerName();

    // Methode aanroepen met parameters
    call_user_func_array([$controller, $action], $params);
} else {
    // 404 pagina
    header("HTTP/1.0 404 Not Found");
    require_once __DIR__ . '/../views/errors/404.twig';
    exit();
}
