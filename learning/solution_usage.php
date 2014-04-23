<?
include_once("../session.php");
include_once("header.php");
class solutions
{
	function view_form($db_object,$common,$default,$user_id,$err)
	{
		$path = $common->path;
		$filename = $path."templates/learning/solution_usage.html";
		$user_table = $common->prefix_table("user_table");
		$position_table = $common->prefix_table("position");
		$approved_devbuilder = $common->prefix_table("approved_devbuilder");
		$skill_table = $common->prefix_table("skills");

		$file = $common->return_file_content($db_object,$filename);
		$pattern = "/<{tech_loopstart}>(.*?)<{tech_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[1];
		
		$pattern1 = "/<{inter_loopstart}>(.*?)<{inter_loopend}>/s";
		preg_match($pattern1,$file,$arr1);
		$match1 = $arr1[1];

		$pattern2 = "/<{solution_loopstart}>(.*?)<{solution_loopend}>/s";
		preg_match($pattern2,$file,$arr2);
		$match2 = $arr2[1];

		$pattern3 = "/<{unused_loopstart}>(.*?)<{unused_loopend}>/s";
		preg_match($pattern3,$file,$arr3);
		$match3 = $arr3[1];

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

	//Skills without solution
		$selskill = "select skill_id from $approved_devbuilder where user_id in ('$split') group by skill_id";
		$result = $db_object->get_single_column($selskill);
		$im_result = @implode("','",$result);
		
		$skillsel = "select skill_name,skill_id,skill_type from $skill_table where skill_id not in ('$im_result')";
		$skillres = $db_object->get_rsltset($skillsel);

		for($i=0;$i<count($skillres);$i++)
		{
			$stype = $skillres[$i]['skill_type'];
			if($stype=='t')
			{
				$tech_name = $skillres[$i]['skill_name'];
				$str .=  preg_replace("/<{(.*?)}>/e","$$1",$match);
			}
			else if($stype=='i')
			{
				$inter_name = $skillres[$i]['skill_name'];
				$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$match1);
			}
		}

		$file = preg_replace($pattern,$str,$file);
		$file = preg_replace($pattern1,$str1,$file);
	//Solution in Learning Plan
		$selsol = "select $approved_devbuilder.skill_id,$skill_table.skill_name,count(build_id) as cnt from 
			$approved_devbuilder,$skill_table where $approved_devbuilder.user_id in ('$split') and 
			$approved_devbuilder.skill_id=$skill_table.skill_id and $approved_devbuilder.status='a' and
			$approved_devbuilder.pstatus='a' group by $approved_devbuilder.skill_id";
			
		$solres = $db_object->get_rsltset($selsol);

		for($j=0;$j<count($solres);$j++)
		{
			$sol_name = $solres[$j][skill_name];
			$total = $solres[$j][cnt];
			$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$match2);
		}
				
		$file = preg_replace($pattern2,$str2,$file);	
	//unused development solution 
		$selsol = "select $approved_devbuilder.skill_id,$skill_table.skill_name,count(build_id) as cnt from 
			$approved_devbuilder,$skill_table where $approved_devbuilder.user_id in ('$split') and 
			$approved_devbuilder.skill_id=$skill_table.skill_id and $approved_devbuilder.status='a' and
			($approved_devbuilder.pstatus='u' or $approved_devbuilder.pstatus='t') group by $approved_devbuilder.skill_id";
		$unused = $db_object->get_rsltset($selsol);

		for($k=0;$k<count($unused);$k++)
		{
			$solution = $unused[$k][skill_name];
			$str3 .= preg_replace("/<{(.*)}>/e","$$1",$match3);
		}				
		$file =preg_replace($pattern3,$str3,$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
}//end class
	$ob = new solutions;
	$ob->view_form($db_object,$common,$default,$user_id,$error_msg);

include_once("footer.php");
?>
