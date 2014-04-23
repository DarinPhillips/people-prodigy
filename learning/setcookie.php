<?php

include_once("../includes/database.class");
include_once("../includes/common.class");
$db_object=new database;
$common=new common;

$lang=$_POST["fLang"];

$t = 0;
setcookie("lang",$lang,$t,"/");
$link=$HTTP_REFERER;

//$query_string=$_SERVER["QUERY_STRING"];

$link=$link;



header("Location:$link");

?>
