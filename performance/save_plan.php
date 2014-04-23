<?
include_once("../session.php");

class saveplan
{
	function save($db_object,$common,$uid,$adminid,$skill,$err,$type)
	{
		$assign_solution = $common->prefix_table("assign_solution_builder");
		$chqry = "select dummy_id from $assign_solution where user_id='$uid' and 
				skill_id='$skill' ";
		$chres  = $db_object->get_single_column($chqry);
		if($chres=="")
		{
			$qry = "insert into $assign_solution set user_id='$uid',admin_id='$adminid',
				skill_id='$skill',date=now(),type='$type',status='i',pstatus='i'";
			$res = $db_object->insert($qry);
$str=<<<EOD
		<script>
			alert ('$err[cSavesuccessfully]');			
			window.location=document.referrer+"?next=Next";
		</script>
EOD;
echo $str;
		}
		else
		{
			
$str=<<<EOD
		<script>
			alert('$err[cRecordexist]');			
			window.location=document.referrer+"?next=Next";
		</script>
EOD;
echo $str;
		}

	}//end save
}
	$ob = new saveplan;
	$ob->save($db_object,$common,$u_id,$admin_id,$skill_id,$error_msg,$type)
?>
