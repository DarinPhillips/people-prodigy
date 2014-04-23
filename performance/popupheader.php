<?
class header
	{
	function view($common,$db_object)
	{
		$path = $common->path;
		$filename = $path."templates/performance/popupheader.html";
		$file = $common->return_file_content($db_object,$filename);
		$file = $common->direct_replace($db_object,$file,$res);
		echo $file;
	}
	}//end class
	$ob = new header;
	$ob->view($common,$db_object);
?>
