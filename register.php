<?php
require('class/tools/tool.php');
Tool::redirectIfID("complete.php");
$title = "Enregistrer";
$content = "";
if (isset($_GET['error'])) {
    $content .= "<p>";
    switch ($_GET['error']) {
        case 'empty':
            $content .= "Veuillez écrire un identifiant et votre mot de passe";
            break;
        case 'password':
            $content .= "Veuillez rentrer un autre mot de passe\n il doit contenir une majuscule et minuscule, un chiffre et doit faire 8 charactère de long";
            break;
        case 'user_db':
            $content .= "L'utilisateur donné est déjà enregistré !";
            break;
        case 'username':
            $content .= "veuillez utiliser un autre nom d'utilisateur \nil doit faire 4 charactère de long, doit contenir uniquement des lettres, des chiffres, ainsi que - _ et .";
            break;
        default:
            $content .= "Une erreur s'est produite";
            break;
    }
    $content .= "</p>";
}
ob_start();
?>
<h1>s'enregister</h1>
<form action="registration.php" method="post">
    <p>Identifiant : <input type="text" name="identifiant" id="identifiant" required/></p>
    <p>Mot de passe : <input type="password" name="password" id="password" required/></p>
    <p><input type="submit" value="OK"></p>
</form>
<?php
$content .= ob_get_clean();
require('class/templates/template.php');
?>