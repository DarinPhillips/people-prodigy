<?
include_once("../session.php");
include_once("header.php");
class assigned
{
function view_form($db_object,$common,$default,$user_id,$err,$gbl_date_format)
	{
		$path = $common->path;
		$filename = $path."templates/learning/add_data_to_assd_skill.html";
		$file = $common->return_file_content($db_object,$filename);
		$skill_table = $common->prefix_table("skills");
		$user_table = $common->prefix_table("user_table");
		$position_table = $common->prefix_table("position");
		$assign_solution = $common->prefix_table("assign_solution_builder");
	
		$pattern = "/<{record_loopstart}>(.*?)<{record_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[1];

		$approved_devbuilder = $common->prefix_table("approved_devbuilder");

		$select = "select admin_id,$assign_solution.skill_id,date_format(date,'$gbl_date_format') as date,
			$skill_table.skill_name from $assign_solution,$skill_table where user_id='$user_id' and 
			 $assign_solution.skill_id=$skill_table.skill_id and ($assign_solution.pstatus is not NULL or
			$assign_solution.pstatus <>'') group by $assign_solution.skill_id";
		$result = $db_object->get_rsltset($select);

		$selsol = "select skill_id from $approved_devbuilder where user_id='$user_id' and status='a' and
				(pstatus='a' or pstatus='t')group by skill_id";	

		$solres = $db_object->get_single_column($selsol);

		for($i=0;$i<count($result);$i++)
		{
			$uid = $result[$i]['admin_id'];
			$name = $common->name_display($db_object,$uid);
			$sid = $result[$i]['skill_id'];
			$s_name = $result[$i]['skill_name'];
			$date = $result[$i]['date'];
			if(!@in_array($sid,$solres))
			{
				$improve = $err['cNotinurplan'];
				$href = "<a href='learning_plan.php?linkid=$user_id&action=plan&skill=$sid'>";
			}
			else
			{				
				$improve = $err['cAlreadyinurlearning'];
				$href = "";
			}			
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$match);			
		}
		$file = preg_replace($pattern,$str,$file);		
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}// end view
}//end class
	$ob = new assigned;
	$ob->view_form($db_object,$common,$default,$user_id,$error_msg,$gbl_date_format);
include_once("footer.php");
?>
