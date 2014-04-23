<?
include_once("../session.php");
include_once("header.php");
class fix
{
	function view_form($db_object,$common,$default,$user_id,$err)
	{
		$path = $common->path;
		$filename = $path."templates/learning/fix_nonapproved_lr.html";
		$file = $common->return_file_content($db_object,$filename);
		$approved_devbuilder = $common->prefix_table("approved_devbuilder");
		$assign_solution = $common->prefix_table("assign_solution_builder");
		$skill_table = $common->prefix_table("skills");

		$pattern = "/<{record_loopstart}>(.*?)<{record_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[1];
		
		$mysql_plan="select distinct($approved_devbuilder.user_id),$approved_devbuilder.skill_id,$skill_table.skill_name
		from $approved_devbuilder,$skill_table where $approved_devbuilder.skill_id=$skill_table.skill_id and 
		$approved_devbuilder.user_id='$user_id' and ($approved_devbuilder.pstatus='t' or $approved_devbuilder.pstatus='r' or $approved_devbuilder.update_status='u')";

		$result = $db_object->get_rsltset($mysql_plan);
		
		for($i=0;$i<count($result);$i++)
		{
			$skill = $result[$i]['skill_name'];
			$sid = $result[$i]['skill_id'];
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$match);
		}	
		$file = preg_replace($pattern,$str,$file);
		if($str=="")
		{
			$file = preg_replace("/<{ifnorecord_loopstart}>(.*?)<{ifnorecord_loopend}>/s","<center><b>$err[cEmptyrecords]</b></center>",$file);;
		}
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}
}//end class
	$ob = new fix;
	$ob->view_form($db_object,$common,$default,$user_id,$error_msg);
include_once("footer.php");
?>
