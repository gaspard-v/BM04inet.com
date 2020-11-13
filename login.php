<?php
require('tool.php');
Tool::redirectIfID("complete.php");
$title = "Connecter";
$content = "";
ob_start();
?>
<h1>Se connecter</h1>
<form action="loginon.php" method="post">
    <p>Identifiant : <input type="text" name="identifiant" id="identifiant" required/></p>
    <p>Mot de passe : <input type="password" name="password" id="password" required/></p>
    <p><input type="submit" value="OK"></p>
</form>
<?php
$content .= ob_get_clean();
require('template.php');
?>