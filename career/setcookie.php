<?php

$lang=$_POST["fLang"];

$t = 0;
setcookie("lang",$lang,$t,"/");
$link=$HTTP_REFERER;
header("Location:$link");
?>