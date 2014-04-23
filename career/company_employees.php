<?php
/*---------------------------------------------
SCRIPT: company_employees.php
AUTHOR:info@chrisranjana.com	
UPDATED:3th Oct

DESCRIPTION:
This script displays all employees in the usertable

---------------------------------------------*/
include("../session.php");
include_once("header.php");

class allEmployees

{
	function show_users($db_object,$common,$post_var,$default,$user_id)
	{
		
		while(list($kk,$vv)=@each($post_var))
		{

		$$kk=$vv;
	
		}
	//print_r($post_var);
		
		$xPath=$common->path;
		$returncontent=$xPath."/templates/career/company_employees.html";
		$returncontent=$common->return_file_content($db_object,$returncontent);
		
		$user_table = $common->prefix_table("user_table");
		$rater_group_table = $common->prefix_table("rater_group");
		$position_table = $common->prefix_table("position");
		
		$mysql = "select rater_group_name from $rater_group_table";
		$group_arr = $db_object->get_single_column($mysql);
	
		
		for($i=0;$i<count($group_arr);$i++)
			{
			$group_var = $group_arr[$i];
		
			$name=$group_var;	

			$$name=1;
			}
//==================
			
//boss id
	
	//here the boss' position id is obtained...
	$mysql = "select $position_table.boss_no from $user_table,$position_table where $user_table.position = $position_table.pos_id and $position_table.pos_id='$appraisal_userid'";
	
	$boss_pos_arr = $db_object->get_a_line($mysql);
	
	if($boss_pos_arr != "")
	{
	$boss_pos_id = $boss_pos_arr["boss_no"];
	}
	else
	{
		$boss_pos_id = 0;
	}
	$mysql = "select user_id from $user_table where position = '$boss_pos_id'";
//	echo "sql=$mysql<br>";
	$boss_arr = $db_object->get_a_line($mysql);
	
	
//if the employee doesnt have a boss...
	if($boss_arr != "")
	{
	$boss_no = $boss_arr["user_id"];
	}
	else
	{
		$boss_no = 0;
	}
	
//boss' boss id 
	$mysql = "select $position_table.boss_no from $position_table where $position_table.pos_id = '$boss_no'";
	//echo "sql=$mysql<br>";	
	$boss_boss_arr = $db_object->get_a_line($mysql);  
	
	if($boss_boss_no != "")
	{
	$boss_boss_no = $boss_boss_arr["boss_no"];
	}
	else
	{
		$boss_boss_no = 0;
	}
			
//TEAM MATES
//first obtaining the position id of the current user...
	$mysql = "select position from $user_table where user_id = '$user_id'";
//	echo "sql=$mysql<br>";
	$team_pos_arr = $db_object->get_a_line($mysql);
	
	$position = $team_pos_arr["position"];
	
	
	$mysql = "select user_id from $user_table where position = '$position' and user_id <> '$user_id'";
	
	$team_arr = $db_object->get_single_column($mysql);
	
	$team_full_arr = @implode(",",$team_arr);   //TEAM MATES

//direct reports
//for the person's direct reports find the position id of the current user
	
	$mysql = "select position from $user_table where user_id = '$user_id'";
	
	$pos_arr = $db_object->get_a_line($mysql);
	
	$position_id = $pos_arr["position"];
	
//find direct reports
	$mysql = "select user_table.user_id from user_table,position where user_table.position = position.pos_id and position.boss_no = '$position_id'"; //(pos_id)
	
	$dirrep_arr = $db_object->get_single_column($mysql); //the person's direct reports
	
	
	$user_full_arr = @implode(",",$dirrep_arr);    //full direct reports
	
	
	
//find peers... (persons of the same level as the current user)...
	
	$mysql = "select position from $user_table where user_id = '$user_id'";  //omitted on oct11-- where user_id not in ($boss_no,$boss_boss_no,$user_full_arr)";
	$peer_position_arr = $db_object->get_a_line($mysql);
	
	$peer_pos = $peer_position_arr['position'];
	
	$mysql = "select level_no from $position_table where pos_id = '$peer_pos'";
	$level_arr  = $db_object->get_a_line($mysql);
	
	$level_no = $level_arr['level_no'];
	
	$mysql = "select pos_id from $position_table where level_no in ($level_no) and pos_id <> $peer_pos";
//	echo $mysql;
	$same_level_arr = $db_object->get_single_column($mysql);
	
	
	
	$peerpos_full_arr = @implode(",",$same_level_arr);    //full peers' positions
	
	if($peerpos_full_arr !='')
	{
	$mysql = "select user_id from $user_table where position in ($peerpos_full_arr)";
	$peer_arr = $db_object->get_single_column($mysql);  // peers
	
	$peer_full_arr = @implode("','",$peer_arr); 
	}
	
//OTHERS GROUPS....
	$mysql = "select user_id from $user_table where user_id <> '$user_id'";
	$other_arr = $db_object->get_single_column($mysql);
	$others_full = @implode("','",$other_arr);
//==================
	$sub_query = "";
	if($grpname == "grp_team")
	{
	$sub_query = $team_full_arr;
	
	}
	if($grpname == "grp_dirrep")
	{
		//echo $grpname;
		$sub_query = $user_full_arr;
	}
	if($grpname == "grp_peer")
	{
		$sub_query = $peer_full_arr;
	}
	
	if ($grpname == "grp_other")
	{
		$sub_query = $others_full;
	}
	$sub_query1 = "and user_id in('$sub_query')";
	
	if($sub_query == "")
	{
		$sub_query1 = "";
	}
	
	$boss_no = $common->immediate_boss($db_object,$user_id);
	
	
		$mysql = "select user_id,username,email from $user_table where user_id <> '1' and user_id <> '$boss_no' and user_id <> '$user_id' $sub_query1";
	
		$user_arr = $db_object->get_rsltset($mysql);
	
		$values['userdetails_loop'] = $user_arr;
		
		//$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$values,'');

		$returncontent	= $common->simpleloopprocess($db_object,$returncontent,$values);

		$returncontent	= $common->direct_replace($db_object,$returncontent,$post_var);
		
		echo $returncontent;
	}
		
}
$obj = new allEmployees;
//$post_var	= array_merge($_POST,$_GET);

$obj->show_users($db_object,$common,$post_var,$default,$user_id);	

//include_once("footer.php");
?>
