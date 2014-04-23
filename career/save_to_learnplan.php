<?php
include_once("../session.php");
include_once("header.php");
class saveplan
{
	function save($db_object,$common,$user_id,$skill,$err)
	{
		$assign_solution = $common->prefix_table("assign_solution_builder");
		$chqry = "select dummy_id from $assign_solution where user_id='$user_id' and 
				skill_id='$sid' ";
		$chres  = $db_object->get_single_column($chqry);
		if($chres=="")
		{
			$qry = "insert into $assign_solution set user_id='$user_id',admin_id='$user_id',
	skill_id='$sid',date=now(),type='$type',status='i',pstatus='i'";
			
			$res = $db_object->insert($qry);
$str=<<<EOD
		<script>
			alert ('$err[cSavesuccessfully]');			
			window.location="front_panel.php";
		</script>
EOD;
echo $str;
		}
		else
		{
			
$str=<<<EOD
		<script>
			alert('$err[cRecordexist]');			
			window.location="front_panel.php";
		</script>
EOD;
echo $str;
		}

	}	//end save
}
	$ob = new saveplan;

	$ob->save($db_object,$common,$user_id,$skill_id,$error_msg);

include_once('footer.php');
?>
