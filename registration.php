<?php
require('class/tools/tool.php');
Tool::redirectIfID("complete.php");
require("class/databases/bdd.php");
if (empty($_POST["identifiant"]) || empty($_POST["password"])) {
    header('Location: register.php?error=empty');
}
try {
    $db->registerUser($_POST["identifiant"], $_POST["password"]);
}
catch (Exception $exception)
{
    if($exception->getMessage() == "Bad Password")
    {
        header('Location: register.php?error=password');
    }
    else if($exception->getMessage() == "User already present in the database")
    {
        header('Location: register.php?error=user_db');
    }
    else if ($exception->getMessage() == "Bad Username") {
        header('Location: register.php?error=username');
    }
    else {
        header('Location: register.php?error=1');
    }
}
$content = "";
$content .= "<h1>Enregistrement complété !</h1>";
$content .= "<p>vuillez vous connecter avec votre nom d'utilisateur \"" . $_POST["identifiant"] . "\"";
require('class/templates/template.php');
?>