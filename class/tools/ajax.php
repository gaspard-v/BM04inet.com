<?php
    if(isset($_GET["isok"]))
    {
        //dummy function
        echo strrev($_GET["isok"]);
    }
    if(isset($_GET["jsonfile"]))
    {
        $retour = "";
        require(dirname( dirname(__FILE__) ) . "/databases/session.php");
        require(dirname( dirname(__FILE__) ) . "/databases/bdd.php");
        $session = new Session($db);
        if($session->loginUserCookie())
        {
            try {
                $retour = "";
                if($_GET["jsonfile"] === "all")
                {
                    $retour = file_get_contents("test.json");
                }
                if(is_numeric($_GET["jsonfile"]))
                {
                    $tmp = json_decode(file_get_contents("test.json"));
                    $param = intval($_GET["jsonfile"]);
                    if(count($tmp) > $param)
                    {
                        $retour = json_encode(array_slice($tmp, $param));
                    }
                }
            }
            catch(Exception $e)
            {
                $retour = "Une erreur s'est produite";
            }
        }
        echo $retour;
    }
?>