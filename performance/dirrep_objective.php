<?
include_once("../session.php");
include_once("header.php");
class objective
{
	function view_form($db_object,$common,$user_id,$default,$err)
	{
		//table
		$user_table = $common->prefix_table("user_table");
		$approved_selected_objective = $common->prefix_table("approved_selected_objective");

		$path = $common->path;
		$filename = $path."templates/performance/dirrep_objective.html";
		$file = $common->return_file_content($db_object,$filename);
		
		$posqry = "select position from $user_table where user_id='$user_id'";
		$posres = $db_object->get_a_line($posqry);
		$position = $posres['position'];
	
		$down = $common->get_chain_below($position,$db_object,$arr);
		$alluser = $common->get_user_id($db_object,$down);
		
		$alluserid = array();
		$diffarray = array();
		for($i=0;$i<count($alluser);$i++)
		{
			$alluserid[] = $alluser[$i]['user_id'];
			
		}		
		$sl_alluser = implode("','",$alluserid);
		$find = "select user_id from $approved_selected_objective where user_id in ('$sl_alluser')";	
		$findres = $db_object->get_single_column($find);
		$diffarray = array_diff($alluserid,$findres);
		$pattern = "/<{objective_loopstart}>(.*?)<{objective_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[0];
		$diff = @array_values($diffarray);
		for($j=0;$j<count($diff);$j++)
		{
			$uid = $diff[$j];
			$username = $common->name_display($db_object,$uid);
			$mailqry = "select email from $user_table where user_id='$uid'";
			$mailres = $db_object->get_a_line($mailqry);
			$email = $mailres['email'];
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}		
		$file = preg_replace($pattern,$str,$file);
		
		
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
}//end class
	$ob = new objective;
	$ob->view_form($db_object,$common,$user_id,$default,$error_msg);
include_once("footer.php");
?>
