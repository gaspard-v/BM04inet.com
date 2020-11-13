<?php
require('mariadb.php');
$db = new mariadb("localhost", "web_test", "S80dd9xRBJBuc1Uy", "web_test");
$db_ok = false;
try {
    $db->BDDconnecte();
    $db_ok = true;
} catch (Exception $exception) {
    $content = "<p>Une erreur est survenu</p>";
    $db_ok = false;
}