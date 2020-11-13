<?php
require("class/databases/bdd.php");
require("class/databases/session.php");
$content = "";
$error = false;
try {
    $session = new Session($db);
    $user = $session->loginUserCookie();
    if ($user) {
        $content = "<p>";
        $content .= "hello ";
        $content .= $user["username"];
        ob_start();
    } else {
        $session->deleteCookie("id");
        $inlineScript = "\"use strict\";";
        $inlineScript .= "window.location.replace(\"login.php?error=1\");";
        $error = true;
    }
} catch (Exception $exception) {
    $session->deleteCookie("id");
    $inlineScript = "\"use strict\";";
    $inlineScript .= "console.error($exception->getMessage());
    window.location.replace(\"login.phpp?error=1\");";
    $error = true;
}
?>
</p>
<form>
    <input type="button" value="Se Déconnecter" id="logout">
    <input type="button" value="Tous Déconnecter" id="allLogout">
</form>
<p>Test AJAX: <span id="txtajax"></span></p>
<p>Test Vue.js: <span id="testvuejs">{{ message }}</span></p>
<p>Lecture Fichier: <span id="readFile"></span></p>
<?php
$content .= ob_get_clean();
if($error) 
{
    $content = null;
}
ob_start();
?>
const data = { message: "OK !" };
new Vue({
    el: "#testvuejs",
    data: data
});
<?php
if(!$error) $inlineScript = ob_get_clean();
else ob_end_clean();
require('class/templates/template.php');
?>