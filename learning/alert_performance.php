<?php
include_once("../session.php");

include_once("header.php");

class alert_performance
{
 function display_solution($common,$db_object,$user_id)
   {
   	$assign_solution_builder=$common->prefix_table("assign_solution_builder");
   	
   	$skills=$common->prefix_table("skills");
   	
   	$temp_devbuilder=$common->prefix_table("temp_devbuilder");
   	
   	/*$app_qry="select skill_id from $assign_solution_builder where user_id='$user_id' and status='i'";
   	
    	$app_result=$db_object->get_rsltset($app_qry);*/
    	
    	
   	$sol_qry2="select skill_id from $temp_devbuilder where user_id='$user_id' group by skill_id";
    	
    //$sol_qry2="select skill_id from $assign_solution_builder where user_id='$user_id' and status='i'";


	$sol_result2=$db_object->get_single_column($sol_qry2);
	

	if(count($sol_result2)>0)
		{
	$un_arr=@implode(",",$sol_result2);

	//$sol_qry1="select skill_id from $assign_solution_builder where user_id='$user_id' and skill_id not in ($un_arr)";
	$sol_qry1="select skill_id from $assign_solution_builder where user_id='$user_id' and status='i'";

	$app_result=$db_object->get_rsltset($sol_qry1);
		}
	else
	{
		$sol_qry="select skill_id from $assign_solution_builder where user_id='$user_id'";
		
		$app_result=$db_object->get_rsltset($sol_qry);
	}
    	
    	$path=$common->path;
    	
    	$xTemplate=$path."/templates/learning/alert_solution.html";
    	
    	$xTemplate=$common->return_file_content($db_object,$xTemplate);
    	
    	for($i=0;$i<count($app_result);$i++)
   	{
   		$skill=$app_result[$i][skill_id];
   		
   		$name_qry="select skill_name from $skills where skill_id='$skill'";
			
   		$name_result=$db_object->get_a_line($name_qry);
   		
   		$name[$i][skill_name]=$name_result[skill_name];
   		
   		$name[$i][skill_id]=$skill;
   	
   }
  
   
   $values["alert_loop"]=$name;

   $xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);	
   
   $xTemplate=$common->direct_replace($db_object,$xTemplate,$array);
		
	echo $xTemplate;
   
   }
}
$obj=new alert_performance();
	
$obj->display_solution($common,$db_object,$user_id);

include_once("footer.php");


?>
