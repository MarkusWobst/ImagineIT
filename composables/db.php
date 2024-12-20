<?php
 
function db(): PDO {
    static $pdo = new PDO('sqlite:../db/identifier.sqlite');
 
    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    return $pdo;
}
 