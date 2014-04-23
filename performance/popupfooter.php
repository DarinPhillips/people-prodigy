<?
class footer
	{
	function view($common,$db_object)
	{
		$path = $common->path;
		$filename = $path."templates/performance/popupfooter.html";
		$file = $common->return_file_content($db_object,$filename);
		$file = $common->direct_replace($db_object,$file,$res);
		echo $file;
	}
	}//end class
	$ob = new footer;
	$ob->view($common,$db_object);
?>
