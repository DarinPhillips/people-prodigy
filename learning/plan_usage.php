<?
include_once("../session.php");
include_once("header.php");
class usage
{
function view_form($db_object,$common,$user_id,$default,$err)
	{
		$path = $common->path;
		$filename = $path."templates/learning/plan_usage.html";		
		$approved_devbuilder = $common->prefix_table("approved_devbuilder");
		$position_table = $common->prefix_table("position");
		$user_table = $common->prefix_table("user_table");
		$skill_table = $common->prefix_table("skills");

		$file = $common->return_file_content($db_object,$filename);
		$pattern = "/<{skill_loopstart}>(.*?)<{skill_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[1];

		$pattern1 ="/<{emp_loopstart}>(.*?)<{emp_loopend}>/s";
		preg_match($pattern1,$file,$arr1);
		$match1 = $arr1[1];
		
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
// skills listed in learning plan
		$selsol = "select $approved_devbuilder.skill_id,$skill_table.skill_name,count(build_id) as cnt from 
			$approved_devbuilder,$skill_table where $approved_devbuilder.user_id in ('$split') and 
			$approved_devbuilder.skill_id=$skill_table.skill_id and $approved_devbuilder.status='a' and
			$approved_devbuilder.pstatus='a' group by $approved_devbuilder.skill_id";
			
		$solres = $db_object->get_rsltset($selsol);

		for($i=0;$i<count($solres);$i++)
		{
			$s_name = $solres[$i][skill_name];
			$total = $solres[$i][cnt];
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
			$file = preg_replace($pattern,$str,$file);

//No of skills per learning Plan	
		$empqry = "select $approved_devbuilder.user_id,$approved_devbuilder.skill_id,
				$skill_table.skill_name,count(build_id) as cnt from $approved_devbuilder,$skill_table 
				where $approved_devbuilder.user_id in ('$split') and $approved_devbuilder.skill_id=$skill_table.skill_id and 
				$approved_devbuilder.status='a' and $approved_devbuilder.pstatus='a'
				group by user_id ";

		$empres = $db_object->get_rsltset($empqry);
		for($i=0;$i<count($empres);$i++)
		{
			$uid = $empres[$i][user_id];
			$e_name = $common->name_display($db_object,$uid);
			$skillno = $empres[$i][cnt];
			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$match1);
		}
		$file = preg_replace($pattern1,$str1,$file);		

		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
}//end class
	$ob = new usage;
	$ob->view_form($db_object,$common,$user_id,$default,$error_msg);
include_once("footer.php");
?>
