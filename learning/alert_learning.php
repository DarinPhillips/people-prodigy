<?php
include("../session.php");
include("header.php");
class Alerts_for_approval
{
  function display($common,$db_object,$user_id)
  {
	if($user_id==1)
	{
		$devbuilder=$common->prefix_table("approved_devbuilder");

		$user=$common->prefix_table("user_table");

		$mysql="select distinct($devbuilder.skill_id),$devbuilder.user_id,$user.username from $devbuilder,$user where $user.user_id=$devbuilder.user_id and $devbuilder.pstatus='u'";

		$solution=$db_object->get_rsltset($mysql);

		$path=$common->path;

		$xFile=$path."/templates/learning/alert.html";

		$xTemplate=$common->return_file_content($db_object,$xFile);


		preg_match("/<{alert_start}>(.*?)<{alert_end}>/s",$xTemplate,$matched);

		$replace=$matched[1];
	
		while(list($kk,$vv)=@each($solution))
		{
			$array["emp_name"]=$solution[$kk]["username"];

			$array["user_id"]=$solution[$kk]["user_id"];

			$array["skill_id"]=$solution[$kk]["skill_id"];

			$replaced.=$common->direct_replace($db_object,$replace,$array);
		}

		
		$xTemplate=preg_replace("/<{alert_start}>(.*?)<{alert_end}>/s",$replaced,$xTemplate);
				
		$xTemplate=$common->direct_replace($db_object,$xTemplate,$array);
				
			echo $xTemplate;
		
		
	}
   }
   
   
  
}

$altobj=new Alerts_for_approval;

if($user_id==1)
{

$altobj->display($common,$db_object,$user_id);

}


include("footer.php");

?>
