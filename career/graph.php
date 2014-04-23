<?
include_once("../session.php");
include("header.php");

$path = $common->path;
$filename = $path."templates/learning/test.html";
$file = $common->return_file_content($db_object,$filename);
echo $file;

include("footer.php");
?>
