<?
include_once("../session.php");
include_once("header.php");
class employee
{
	function view_form($db_object,$common,$user_id,$default,$err,$gbl_date_format)
	{
		$path = $common->path;
		$filename = $path."templates/performance/employee_list.html";
		$user_table = $common->prefix_table("user_table");
		$position_table = $common->prefix_table("position");	
		$performance_feedback = $common->prefix_table("performance_feedback");
		$file = $common->return_file_content($db_object,$filename);
		$approved_feedback = $common->prefix_table("approved_feedback");
		
		//get the users id who are all under his admin chain
		if($user_id!=1)
		{
			$selqry="select user_id from $user_table where admin_id='$user_id' order by user_id";
		}
		else
		{
			$selqry="select $user_table.user_id from $user_table,$position_table where 
			$user_table.position=$position_table.pos_id and ($user_table.position<>NULL or 
			$user_table.position<>0) and $user_table.user_id!=1   order by $user_table.user_id";
		}
		$userres = $db_object->get_single_column($selqry);
		$split = @implode("','",$userres);

		$select = "select user_id,date_format(s_date,'$gbl_date_format') as s_date from $performance_feedback where user_id in ('$split') and 
				request_from=user_id and status='I'  and (to_days(now()) - to_days(s_date)) > 7  group by s_date order by s_date";

		$result = $db_object->get_rsltset($select);
		$pattern = "/<{record_loopstart}>(.*?)<{record_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[1];

		for($i=0;$i<count($result);$i++)
		{
			$uid = $result[$i]['user_id'];
			$u_name = $common->name_display($db_object,$uid);
			$date = $result[$i]['s_date'];
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$match);
		}

		if($str=="")			
			{

				$file = preg_replace("/<{ifnoloop_start}>(.*?)<{ifnoloop_end}>/s","$err[cEmptyrecords]",$file);
			}
		$file = preg_replace($pattern,$str,$file);

		$file = preg_replace("/<{(.*?)}>/e","",$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end function
}//end class
	$ob = new employee;
	$ob->view_form($db_object,$common,$user_id,$default,$error_msg,$gbl_date_format);
include_once("footer.php");
?>
