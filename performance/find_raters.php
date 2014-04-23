<?
include_once("../session.php");
include_once("header.php");
class raters
{
	function view_form($db_object,$common,$user_id,$default,$err)
	{
	//table 
		$approved_selected_objective = $common->prefix_table("approved_selected_objective");
		$approved_affected = $common->prefix_table("approved_affected");
		$position_table = $common->prefix_table("position");
		$user_table = $common->prefix_table("user_table");

		$path = $common->path;
		$filename = $path."templates/performance/find_raters.html";
		$file = $common->return_file_content($db_object,$filename);

		//$adminfor = "select user_id from $user_table where admin_id='$user_id'";
		//$adminforres = $db_object->get_single_column($adminfor);

		$posqry = "select position from $user_table where user_id='$user_id'";
		$posres = $db_object->get_a_line($posqry);
		$position = $posres['position'];

		$gt_user = $common->get_chain_below($position,$db_object,$arr);
		$get_user= $common->get_user_id($db_object,$gt_user);
		$below = array();
		for($i=0;$i<count($get_user);$i++)
		{
			$below[] = $get_user[$i]['user_id'];
		}
			$adminforres = $below;
		$sel_user = array();

		for($j=0;$j<count($adminforres);$j++)
		{
			$noofobj=0;
			$selqry = "select sl_id from $approved_selected_objective where user_id='$adminforres[$j]' and status='A'";
			$selres = $db_object->get_single_column($selqry);

			for($i=0;$i<count($selres);$i++)
			{
				$qry = "select aff_id from $approved_affected where sl_id=$selres[$i]";
				$res = $db_object->get_single_column($qry);				 
				if($res=="")
				{
					$sel_user[] = $adminforres[$j];	
					$cnt  = $adminforres[$j];				
					$count[$cnt][] = 1;
				}
			}//i loop
		}//j loop

		$pattern = "/<{record_loopstart}>(.*?)<{record_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[0];

		$seluser = @array_unique($sel_user);
		$seluser = @array_values($seluser);
				
		for($k=0;$k<count($seluser);$k++)
		{
			$uid = $seluser[$k];
			$username = $common->name_display($db_object,$uid);
			$cout = $count[$uid];
			$noofobj = count($cout);
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);				
		}
		
			$file = preg_replace($pattern,$str,$file);
			
			$nopattern = "/<{if_norecord_start}>(.*?)<{if_norecord_end}>/s";
			if($str=="")
			{
				$file = preg_replace($nopattern,"$err[cEmptyrecords]",$file);
			}
			$file = $common->direct_replace($db_object,$file,$val);
			$file = preg_replace("/<{(.*?)}>/e","",$file);
			echo $file;

	}
}//end class
	$ob = new raters;
	$ob->view_form($db_object,$common,$user_id,$default,$error_msg);
include_once("footer.php");
?>
