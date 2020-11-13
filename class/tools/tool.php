<?php
    class Tool
    {
        static public function redirectIfID($redirect)
        {
            if(isset($_COOKIE["id"]))
            {
                header("Location: $redirect");
            }
        }
        static public function getPath($uri)
        {
            
        }
    }
?>