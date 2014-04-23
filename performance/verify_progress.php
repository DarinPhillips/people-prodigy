<?
include_once("../session.php");
include_once("header.php");
class verify
{
	function view_form($db_object,$common,$user_id,$default,$err,$date_format)
	{
	//Table Declaration
		$approved_feedback = $common->prefix_table("approved_feedback");
		$path = $common->path;
		$filename = $path."templates/performance/verify_progress.html";
		$file = $common->return_file_content($db_object,$filename);
		$appqry = "select user_id,date_format(approved_date,'$date_format') as approved_date from $approved_feedback where boss_id='$user_id' and status='1' and active='A' group by user_id";	
		$appres = $db_object->get_rsltset($appqry);
		$pattern = "/<{verify_loopstart}>(.*?)<{verify_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[0];
		
		for($i=0;$i<count($appres);$i++)
		{
			$uid = $appres[$i]['user_id'];
			$username = $common->name_display($db_object,$uid);
			$date = $appres[$i]['approved_date'];
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);				
		}
		$file = preg_replace($pattern,$str,$file);
		
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}
}//end class
	$ob = new verify;
	$ob->view_form($db_object,$common,$user_id,$default,$error_msg,$gbl_date_format);
include_once("footer.php");
?>
