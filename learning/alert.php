<?php
include("../session.php");
include("header.php");
class Alerts_for_approval
{
  function display($common,$db_object,$user_id,$err)
  {
	if($user_id==1)
	{
		$devbuilder=$common->prefix_table("unapproved_devbuilder");

		$approved_devbuilder=$common->prefix_table("approved_devbuilder");

		$user=$common->prefix_table("user_table");

		$path=$common->path;

		$mysql="select distinct($devbuilder.user_id),skill_id,$user.username from 
		$devbuilder,$user where $user.user_id=$devbuilder.user_id and 
		$devbuilder.status='u'";		
		$solution=$db_object->get_rsltset($mysql);

	//-------------------Alert to Approve Updated plan progress--------
		$update_qry = "select distinct($approved_devbuilder.user_id) as uid,skill_id,$user.username
			 from $approved_devbuilder,$user where $user.user_id=$approved_devbuilder.user_id 
			and $approved_devbuilder.update_status='u' group by approved_devbuilder.user_id ";
		$update_res = $db_object->get_rsltset($update_qry);

		if(($solution[0]!="")||($update_res!=""))
		{
			$xFile=$path."/templates/learning/alert.html";
			$xTemplate=$common->return_file_content($db_object,$xFile);
			preg_match("/<{alert_start}>(.*?)<{alert_end}>/s",$xTemplate,$matched);
			$replace=$matched[1];
			preg_match("/<{update_plan_start}>(.*?)<{update_plan_end}>/s",$xTemplate,$ar);			
			$match = $ar[1];

			if($solution[0]!="")
			{
				$k=0;
				for($i=0;$i<count($solution);$i++)
				{
				$arr[$k]=$solution[$i][user_id];
				$k++;
				}
	
				$arr=array_unique($arr);
			}	
	
			if($solution[0]!="")
			{
				
	
				while(list($kk,$vv)=@each($solution))
				{
					$array["emp_name"]=$solution[$kk]["username"];
		
					$array["user_id"]=$solution[$kk]["user_id"];

					$array["skill_id"]=$solution[$kk]["skill_id"];
	
					$replaced.=$common->direct_replace($db_object,$replace,$array);
				}				
			}
				$xTemplate=preg_replace("/<{alert_start}>(.*?)<{alert_end}>/s",$replaced,$xTemplate);

				if($replaced=="")
				{
					$xTemplate=preg_replace("/<{approval_startloop}>(.*?)<{approval_endloop}>/s","",$xTemplate);
				}
			if($update_res!="")
			{								
				for($i=0;$i<count($update_res);$i++)
				{
					$uid = $update_res[$i]['uid'];
					$prog_mess = $err['cProgressupdated'];
					$username = $update_res[$i]['username']	;
					$strval .= preg_replace("/<{(.*?)}>/e","$$1",$match);
				}
				$xTemplate = preg_replace("/<{update_plan_start}>(.*?)<{update_plan_end}>/s",$strval,$xTemplate);			
				
			}
				if($strval=="")
				{
					$xTemplate = preg_replace("/<{updated_plan_start}>(.*?)<{updated_plan_end}>/s","",$xTemplate);
				}						
				$xTemplate = preg_replace("/<{(.*?)}>/s","",$xTemplate);
				$xTemplate=$common->direct_replace($db_object,$xTemplate,$array);
			
				echo $xTemplate;



		}
					
	$mysql_plan="select distinct($approved_devbuilder.user_id),skill_id,$user.username
		 from $approved_devbuilder,$user where $user.user_id=$approved_devbuilder.user_id 
		and $approved_devbuilder.pstatus='t'";	

		$solution_plan=$db_object->get_rsltset($mysql_plan);

		if($solution_plan[0]!="")
		{
			$k=0;
			for($i=0;$i<count($solution_plan);$i++)
			{

			$arr[$k]=$solution_plan[$i][user_id];
			$k++;
			}

			$arr=array_unique($arr);
		}
		if($solution_plan[0]!="")
		{
			$path=$common->path;

			$xFile=$path."/templates/learning/alert_plan.html";

			$xTemplate=$common->return_file_content($db_object,$xFile);
			$pattern = "/<{update_plan_start}>(.*?)<{update_plan_end}>/s";
			preg_match($pattern,$xTemplate,$arr);
			$match = $arr[1];
		
			preg_match("/<{alert_plan_start}>(.*?)<{alert_plan_end}>/s",$xTemplate,$match);

			$match=$match[1];
		
			while(list($kk,$vv)=@each($solution_plan))
			{
		
				$array["emp_name1"]=$solution_plan[$kk]["username"];

				$array["user_id1"]=$solution_plan[$kk]["user_id"];

				$array["skill_id1"]=$solution_plan[$kk]["skill_id"];
		
				$str.=$common->direct_replace($db_object,$match,$array);
	
			}
			$xTemplate=preg_replace("/<{alert_plan_start}>(.*?)<{alert_plan_end}>/s",$str,$xTemplate);

			$xTemplate = $common->direct_replace($db_object,$xTemplate,$array);

			echo $xTemplate;
					
		}
		
		
			

					
	}
		
  }
}

$altobj=new Alerts_for_approval;

if($user_id==1)

{

$altobj->display($common,$db_object,$user_id,$error_msg);

}

else

{

	echo $error_msg["cNoPermission"];
}

include("footer.php");

?>
