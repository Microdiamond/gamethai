<?php

$uri = $_SERVER['REQUEST_URI'];

switch ($uri) {
    case '/':
        include 'index.php';
        break;
    case '/board':
        include 'board.php';
        break;
    case '/randomvocab':
        include 'randomvocab.php';
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}
