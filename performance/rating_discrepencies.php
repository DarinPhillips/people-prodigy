<?
include_once("../session.php");
include_once("header.php");
class rating
{
	function view_form($db_object,$common,$user_id,$default)
	{
		$path = $common->path;
		$filename = $path."templates/performance/rating_discrepencies.html";
		$file = $common->return_file_content($db_object,$filename);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
}//end class
	$ob = new rating;
	$ob->view_form($db_object,$common,$user_id,$default);

include_once("footer.php");
?>
