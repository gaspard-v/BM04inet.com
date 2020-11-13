<!DOCTYPE html>
<html lang="fr">

<head>
    <?php
    include("head.php");
    if (isset($head)) {
        echo $head;
    }
    if (isset($extrahead)) {
        echo $extrahead;
    }
    ?>

    <?php
    if (isset($title)) {
        echo "<title>";
        echo $title;
        echo "</title>";
    }
    ?>
</head>

<body>
    <?php
    if (isset($content)) {
        echo $content;
    }
    include("script.php");
    if (isset($script)) {
        echo $script;
    }
    
    if(isset($inlineScript))
    {
        echo "<script>";
        echo $inlineScript;
        echo "</script>";
    }
    ?>
</body>

</html>