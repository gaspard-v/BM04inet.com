<?php
require("bdd.php");
require("session.php");
if (empty($_POST["identifiant"]) || empty($_POST["password"])) {
    header('Location: login.php?error=empty');
} else {
    try {
        if (!$db->loginUser($_POST["identifiant"], $_POST["password"])) {
            header('Location: login.php?error=unknown');
        } else {
            $session = new Session($db);
            if (!isset($_COOKIE["id"])) {
                try {
                    $session->setCookie($_POST["identifiant"], $_POST["password"]);
                    header('Location: complete.php');
                } catch (Exception $exception) {
                    header('Location: login.php?error=1');
                }
            } else header('Location: complete.php');
        }
    } catch (Exception $exception) {
        switch ($exception->getMessage()) {
            case 'Bad Username':
                header('Location: login.php?error=username');
                break;
            default:
                header('Location: login.php?error=1');
                break;
        }
    }
}
