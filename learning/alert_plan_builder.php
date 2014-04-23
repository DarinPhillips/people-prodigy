<?php
include_once("../session.php");

include_once("header.php");

class alert_plan_builder
{
 function display_plan($common,$db_object,$user_id)
   {
   	$assign_solution_builder=$common->prefix_table("assign_solution_builder");
   	
   	$skills=$common->prefix_table("skills");
  	   	
    	$qry="select $assign_solution_builder.skill_id,$skills.skill_name from 
    	
    	$assign_solution_builder,$skills where $assign_solution_builder.skill_id=$skills.skill_id
    	
    	 and $assign_solution_builder.user_id='$user_id' and $assign_solution_builder.pstatus='i'";
    	
    	   	
    	$result=$db_object->get_rsltset($qry);
	    	   

    	$c=count($result);

    	$path=$common->path;
    	
    	$xTemplate=$path."/templates/learning/plan_alert.html";
    	
    	$xTemplate=$common->return_file_content($db_object,$xTemplate);
    	
    	for($i=0;$i<count($result);$i++)
   	{
   		
   		$skill[$i][skill_name]=$result[$i][skill_name];
   		
   		$skill[$i][skill_id]=$result[$i][skill_id];
   		
   		$skill[$i][user_id]=$user_id;
   	}
  


		$values["alert_loop"]=$skill;


   $xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);	
   
   $xTemplate=$common->direct_replace($db_object,$xTemplate,$array);
		
	echo $xTemplate;
   
   }
}
$obj=new alert_plan_builder();

$obj->display_plan($common,$db_object,$user_id);

include_once("footer.php");


?>
