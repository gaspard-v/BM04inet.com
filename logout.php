<?php
    require("bdd.php");
    include("session.php");
    $session = new Session($db);
    if(isset($_GET["type"]))
    {
        try {
            if($_GET["type"] === "single")
            {
                $session->deleteSessionCookie();
                header('Location: login.php');
            }
            else if($_GET["type"] === "all")
            {
                $session->deleteSessionCookie("all");
                header('Location: login.php');
            }
            else header('Location: complete.php');
        }catch(Exception $execption)
        {
            header('Location: login.php?error=1');
        }
    }

?>