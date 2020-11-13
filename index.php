<?php
define(ROOT_PATH, dirname(__FILE__) );
define(CLASS_PATH, ROOT_PATH . "/class");

require('class/tools/tool.php');
Tool::redirectIfID("complete.php");
$title = "eusso";
ob_start();
?>
<ul>
    <li><a href="login.php">Login</a></li>
    <li><a href="register.php">Register</a></li>
</ul>
<?php
$content = ob_get_clean();
require('class/templates/template.php');
?>