<?
include_once("../session.php");
include_once("header.php");
class raters
{
function view_form($db_object,$common,$user_id,$default,$err)
	{
		$path = $common->path;
		$filename = $path."templates/learning/find_raters.html";
		$file = $common->return_file_content($db_object,$filename);		
		$approved_devbuilder = $common->prefix_table("approved_devbuilder");
		$position_table = $common->prefix_table("position");
		$user_table = $common->prefix_table("user_table");
		
		//$seluser = "select user_id from $user_table where admin_id='$user_id'";
		if($user_id!=1)
		{
			$selqry="select user_id from $user_table where admin_id='$user_id' order by user_id";
		}
		else
		{
			$selqry="select $user_table.user_id from $user_table,$position_table where $user_table.position=$position_table.pos_id and ($user_table.position<>NULL or $user_table.position<>0) and $user_table.user_id!=1   order by $user_table.user_id";//$position_table.level_no desc			
		}
		$userres = $db_object->get_single_column($selqry);
		$split = @implode("','",$userres);
		$selqry = "select user_id,count(user_id) as rec from $approved_devbuilder where interbasic_id='14' and (url='' or url is null) and user_id in ('$split') group by user_id";		
		$result = $db_object->get_rsltset($selqry);
		$pattern = "/<{emp_loopstart}>(.*?)<{emp_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[1];
	
		$pattern1 = "/<{ifno_recordsstart}>(.*?)<{ifno_recordsend}>/s";
		preg_match($pattern1,$file,$arr1);
		$match1 =$arr1[1];
		$str="";
		for($i=0;$i<count($result);$i++)
		{
			$uid = $result[$i][user_id];
			$noofplan = $result[$i][rec];
			$empname = $common->name_display($db_object,$uid);
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		$file = preg_replace($pattern,$str,$file);
		if($str=="")		
		{
			$file = preg_replace($pattern1,"",$match1);
			echo "<b>$err[cEmptyrecords]</b>";
		}
		$file = preg_replace("/<{(.*?)}>/e","",$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;		
	}//end view
}//end class
	$ob = new raters;
	$ob->view_form($db_object,$common,$user_id,$default,$error_msg);
include_once("footer.php");
?>
