<?php
include("session.php");
include("header.php");
class Outer
{
 function display($common,$db_object,$user_id)
	{
 	$xFile="templates/outer.html";	
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$user_table=$common->prefix_table("user_table");
	$otherraters_table 	= $common->prefix_table('other_raters');

 $temp_user_table=$common->prefix_table("temp_user_table");

	$user_table=$common->prefix_table("user_table");
	$appraisal_table=$common->prefix_table("appraisal");
	if($user_id!=1)
{
$selqry="select count(*) from $temp_user_table,$user_table where $temp_user_table.user_id=$user_table.user_id and $user_table.admin_id='$user_id'";
}
else
{
$selqry="select count(*) from $temp_user_table,$user_table where
$temp_user_table.user_id=$user_table.user_id and 
$user_table.admin_id='$user_id' and $user_table.admin_id is null";
}

	//echo $selqry;
	$empset=$db_object->get_a_line($selqry);

	//print_R($empset);

	$count=$empset[0];

	if($count>0)
		{
	$xTemplate=preg_replace("/<{employeealert_(.*?)}>>/s","",$xTemplate);
		}
	else
		{
	$xTemplate=preg_replace("/<{employeealert_start}>(.*?)<{employeealert_end}>/s","",$xTemplate);
		}
	

//INTERPERSONAL AND TECHNICAL TESTS...
//-----------------------------------
$fields = $common->return_fields($db_object,$appraisal_table);

		$mysql = "select $fields from $appraisal_table where user_id='$user_id' and test_mode = 'Test'";
		$detail_arr = $db_object->get_rsltset($mysql);
		

		$replaced = "";
		for($i=0;$i<count($detail_arr);$i++)
		{
			
			$test_mode = $detail_arr[$i]['test_mode'];
			
			$test_typevar = $detail_arr[$i]['test_type'];
			$test_type = $gbl_skill_type[$test_typevar];
			
			$user_id = $detail_arr[$i]['user_id'];
			
			if(($test_typevar == 'i') && (test_mode != 'Test'))
			{
			$filecontent=preg_replace("/<{interalert_(.*?)}>/s","",$filecontent);
				
			}
			
			
			
			
			if(($test_typevar == 't') && (test_mode != 'Test')) 
			{
				$filecontent=preg_replace("/<{techalert_(.*?)}>/s","",$filecontent);
			}
			 
			 
		}
	$filecontent=preg_replace("/<{interalert_start}>(.*?)<{interalert_end}>/s","",$filecontent);
	$filecontent=preg_replace("/<{techalert_start}>(.*?)<{techalert_end}>/s","",$filecontent);










	$selqry="select user_type from $user_table where user_id='$user_id'";
	$emp_type=$db_object->get_a_line($selqry);


	if($emp_type["user_type"]=="external")
	{
		preg_match("/<{external_candidate_area}>(.*?)<{external_candidate_area}>/s",$xTemplate,$mat);
		$xTemplate=$mat[1];
	}
	if($user_id!=1)
	{
		$xTemplate=preg_replace("/<{admin_area}>(.*?)<{admin_area}>/s","",$xTemplate);
		$xTemplate=preg_replace("/<{external_candidate_area}>(.*?)<{external_candidate_area}>/s","",$xTemplate);
		$xTemplate=preg_replace("/<{(.*?)}>/s","",$xTemplate);	
	}
	else
	{
		$xTemplate=preg_replace("/<{external_candidate_area}>(.*?)<{external_candidate_area}>/s","",$xTemplate);
		$xTemplate=preg_replace("/<{(.*?)}>/s","",$xTemplate);		
	}
	
	
	//INTERPERSONAL AND TECHNICAL 360
//-------------------------------
//DISPLAYING THE ADD/ASSIGN INTER & TECHNICAL SKILL APPRAISAL RATERS
//NULLIFY THE ALERT IF THE USER HAS COMPLETED ASSIGNING ALL THE REFERENCES...
		$mysql = "select group_belonging from $otherraters_table where cur_userid = '$user_id'";

		$grpbelonging_arr = $db_object->get_single_column($mysql);
	
		if(@in_array("grp_boss",$grpbelonging_arr) && @in_array("grp_topboss",$grpbelonging_arr) && @in_array("grp_self",$grpbelonging_arr) && @in_array("grp_team",$grpbelonging_arr) && @in_array("grp_peer",$grpbelonging_arr) && @in_array("grp_dirrep",$grpbelonging_arr) && @in_array("grp_other",$grpbelonging_arr) && @in_array("grp_incus",$grpbelonging_arr) && @in_array("grp_excus",$grpbelonging_arr) )
		{
			$filecontent=preg_replace("/<{interappraisal_start}>(.*?)<{interappraisal_end}>/s","",$filecontent);

		}
		
//DISPLAY THE ALERT WHEN THE ADMIN HAS ASSIGNED SOME APPRAISAL...
		
		$fields = $common->return_fields($db_object,$appraisal_table);
		$mysql = "select $fields from $appraisal_table where user_id='$user_id' and test_mode = '360'";
		
		$detail_arr = $db_object->get_rsltset($mysql);
		
		for($i=0;$i<count($detail_arr);$i++)
		{
			
			$test_mode = $detail_arr[$i]['test_mode'];
			
			$test_typevar = $detail_arr[$i]['test_type'];
			$test_type = $gbl_skill_type[$test_typevar];
			
			$user_id = $detail_arr[$i]['user_id'];
			
			if(($test_typevar == 'i') && (test_mode != '360'))
			{
			$filecontent=preg_replace("/<{interappraisal_(.*?)}>/s","",$filecontent);
				
			}
			 
			if(($test_typevar == 't') && (test_mode != '360')) 
			{
				$filecontent=preg_replace("/<{techappraisal_(.*?)}>/s","",$filecontent);
			}
			 
		}
 
	$filecontent=preg_replace("/<{techappraisal_start}>(.*?)<{techappraisal_end}>/s","",$filecontent);
	$filecontent=preg_replace("/<{interappraisal_start}>(.*?)<{interappraisal_end}>/s","",$filecontent);

	$pos = "select position from $user_table where user_id='$user_id'";
	$posres = $db_object->get_a_line($pos);
	$position = $posres['position'];
	$pattern="/<!--performance_start-->(.*?)<!--performance_end-->/s";
	preg_match($pattern,$file,$arr);
	$match = $arr[0];
	
	if($position==0)
	{
		$xTemplate = preg_replace($pattern,"",$xTemplate);	
	}
$temp_user_table=$common->prefix_table("temp_user_table");
$user_table=$common->prefix_table("user_table");



$selqry="select user_id from $temp_user_table";

//	echo $selqry;

$temp_ids=$db_object->get_single_column($selqry);
$ids=@implode(",",$temp_ids);

	
	if($ids)
	{
		$selqry="select admin_id from $user_table where user_id in ($ids)";
		$adminids=$db_object->get_single_column($selqry);
	}

	if(!@in_array($user_id,$adminids))
	{
	$xTemplate=preg_replace("/<!--alertforempupdate-->(.*?)<!--alertforempupdate-->/s","",$xTemplate);
	}



	$values=array();
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;
	}
}
	

$otrobj= new Outer;
$otrobj->display($common,$db_object,$user_id);
include("footer.php");
?>
