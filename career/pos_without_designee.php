<?php
/*---------------------------------------------
SCRIPT:pos_without_designee.php
AUTHOR:info@chrisranjana.com	
UPDATED:7th Jan

DESCRIPTION:
This script shows the positions without designees

---------------------------------------------*/
include("../session.php");
include("header.php");

class positionWithoutDesignee
{
function show_positions($db_object,$common,$post_var,$user_id)
	{
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/pos_without_designee.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);
	
	$position_designee1 = $common->prefix_table('position_designee1');
	$position_designee2 = $common->prefix_table('position_designee2');
	$deployment_plan    = $common->prefix_table('deployment_plan');	
	$user_table	    = $common->prefix_table('user_table');
	$position 	    = $common->prefix_table('position');
	
	
//DETERMINE THE USERS UNDER THIS ADMIN AND THEN DETERMINE THE POSITION OF THOSE USERS AND 
// THEN CHECK THE IF THOSE POSITIONS HAVE ANY DESIGNEES OR NOT

	if($user_id != '1')
	{
	$mysql = "select user_id,position from $user_table where admin_id = '$user_id'";
	}
	if($user_id == '1')
	{
	$mysql = "select user_id,position from $user_table where user_id <> '1'";
	}

	$user_underadmin_arr = $db_object->get_rsltset($mysql);
	
	for($a=0;$a<count($user_underadmin_arr);$a++)
	{
		$users_pos[$a]=$user_underadmin_arr[$a][position];
	}
	
	//print_r($user_underadmin_arr);
	$a=0;$b=0;

	for($i=0;$i<count($user_underadmin_arr);$i++)
	{

	$id_of_user = $user_underadmin_arr[$i]['user_id'];
		
		
	$pos_id = $user_underadmin_arr[$i]['position'];
	
	$mysql = "select designated_user from $deployment_plan,$position_designee1
			where $deployment_plan.plan_id = $position_designee1.plan_id
			and $deployment_plan.position='$pos_id'";
	$pos_designee1_arr = $db_object->get_a_line($mysql);
	
	$pos_designee1 = $pos_designee1_arr['designated_user'];
		

//THE ABOVE QUERY WILL GIVE THE POSITIONS WITH A DESIGNEE FOR THAT PARTICULAR POSITION
//IF THE QUERY IS NULL => MEANS THERE ARE NO DESIGNEES ALLOTED FOR THAT POSITION...

	if($pos_designee1 == '')
	{
	$pos_without_designee1[$a] = $pos_id;
	$a++;
	}
	
	$mysql = "select designated_user from $deployment_plan,$position_designee2
			where $deployment_plan.plan_id = $position_designee2.plan_id
			and $deployment_plan.position='$pos_id'";
	$pos_designee2_arr = $db_object->get_a_line($mysql);
	$pos_designee2 = $pos_designee2_arr['designated_user'];

//THE ABOVE QUERY WILL GIVE THE POSITIONS WITH A DESIGNEE FOR THAT PARTICULAR POSITION
//IF THE QUERY IS NULL => MEANS THERE ARE NO DESIGNEES ALLOTED FOR THAT POSITION...
	
	if($pos_designee2 == '')
	{
	$pos_without_designee2[$b] = $pos_id;
	
	$b++;
	
	}
	
	}


//THE POSITION WITHOUT THE FIRST DESIGNEE AND SECOND DESIGNEE IS OBTAINED...

//echo "Positions without first designee";
//print_r($pos_without_designee1);
//echo "<BR>Positions without second designee";
//print_r($pos_without_designee2);

	preg_match("/<{firstdesignee_loopstart}>(.*?)<{firstdesignee_loopend}>/s",$returncontent,$matchold);
	$matchnew = $matchold[1];
	for($i=0;$i<count($pos_without_designee1);$i++)
	{
		$pos_id_des = $pos_without_designee1[$i];
		
		
		$mysql = "select position_name,boss_no from $position where pos_id='$pos_id_des'";
	
		$posname_arr = $db_object->get_a_line($mysql);
		$posname = $posname_arr['position_name'];

		if(($pos_id_des!="" and $pos_id_des!=0) and $posname!="")
		{
		
		$bossno = $posname_arr['boss_no'];
		$mysql = "select username,user_id from $user_table where position = '$bossno'";
		$bossname_arr = $db_object->get_a_line($mysql);
		$bossname = $bossname_arr['username'];
		$bossid = $bossname_arr['user_id'];
		$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$matchnew);
		}
		
		
	}	
	$returncontent = preg_replace("/<{firstdesignee_loopstart}>(.*?)<{firstdesignee_loopend}>/s",$str1,$returncontent);

//SECOND DESIGNEE...

	preg_match("/<{seconddesignee_loopstart}>(.*?)<{seconddesignee_loopend}>/s",$returncontent,$matchold2);
	$matchnew2 = $matchold2[1];
	for($i=0;$i<count($pos_without_designee2);$i++)
	{
		$pos_id_des2 = $pos_without_designee2[$i];
		$mysql = "select position_name,boss_no from $position where pos_id='$pos_id_des2'";
		$posname2_arr = $db_object->get_a_line($mysql);
		
		if(($pos_id_des2!="" and $pos_id_des2!=0) and $posname2_arr!="")
		{
		$posname2 = $posname2_arr['position_name'];
		$bossno = $posname2_arr['boss_no'];
		$mysql = "select username,user_id from $user_table where position = '$bossno'";
		$bossname2_arr = $db_object->get_a_line($mysql);
		$bossname2 = $bossname2_arr['username'];
		$bossid2 = $bossname2_arr['user_id'];
		$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$matchnew2);
		}
		
	}	
	$returncontent = preg_replace("/<{seconddesignee_loopstart}>(.*?)<{seconddesignee_loopend}>/s",$str2,$returncontent);


$position_with_designees=@array_merge($pos_without_designee1,$pos_without_designee2);

$position_with_designees=@array_unique($position_with_designees);

$position_without_designees=@array_diff($users_pos,$position_with_designees);

$keys=@array_keys($position_without_designees);

//POSITIONS WITHOUT BOTH DESIGNEES

	preg_match("/<{bothdesignee_loopstart}>(.*?)<{bothdesignee_loopend}>/s",$returncontent,$match);
	$match = $match[0];

	for($i=0;$i<count($position_without_designees);$i++)
	{
		$key=$keys[$i];

		$pos_id_des1 = $position_without_designees[$key];

		$mysql = "select position_name,boss_no from $position where pos_id='$pos_id_des1'";
		$posname1_arr = $db_object->get_a_line($mysql);
		
		if(($pos_id_des1!="" and $pos_id_des1!=0) and $posname1_arr!="")
		{
		$posname1 = $posname1_arr['position_name'];
		$bossno = $posname1_arr['boss_no'];
		$mysql = "select username,user_id from $user_table where position = '$bossno'";
		$bossname1_arr = $db_object->get_a_line($mysql);
		$bossname1 = $bossname2_arr['username'];
		$bossid1 = $bossname2_arr['user_id'];
		$replace .= preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
	
		
	}	
	$returncontent = preg_replace("/<{bothdesignee_loopstart}>(.*?)<{bothdesignee_loopend}>/s",$replace,$returncontent);



	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
			
	echo $returncontent;
	}
}
$obj = new positionWithoutDesignee; 
$obj->show_positions($db_object,$common,$post_var,$user_id);

include("footer.php");
?>

