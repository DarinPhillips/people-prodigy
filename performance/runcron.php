<?
include_once("../session.php");
include_once("header.php");
class run
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$path = $common->path;
		$filename = $path."templates/performance/runcron.html";
		$file = $common->return_file_content($db_object,$filename);
		$file  = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
}//end class
	$ob = new run;
	$ob->view_form($db_object,$common,$default,$user_id);	
include_once("footer.php");
?>
