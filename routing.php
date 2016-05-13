<?php
// www/routing.php
if (preg_match('/\.(?:png|jpg|jpeg|gif|html|ico)$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    include __DIR__ . '/index.php';
}
