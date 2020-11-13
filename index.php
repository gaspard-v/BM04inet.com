<?php
require('tool.php');
Tool::redirectIfID("complete.php");
$title = "eusso";
require('head.php');
ob_start();
?>
<ul>
    <li><a href="login.php">Login</a></li>
    <li><a href="register.php">Register</a></li>
</ul>
<?php
$content = ob_get_clean();
require('template.php');
?>