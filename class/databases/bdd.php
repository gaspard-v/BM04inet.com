<?php
require('mariadb.php');
$db = new mariadb("localhost", "BM04", "PuMm6A8QLYRCtl34", "bm04");
try {
    $db->BDDconnecte();
} catch (Exception $exception) {
    $content = "<p>Une erreur est survenu</p>";
}