<?php
/*---------------------------------------------
SCRIPT:set_admins.php
AUTHOR:info@chrisranjana.com	
UPDATED:24th Sept

DESCRIPTION:


---------------------------------------------*/
include("../session.php");
include_once("header.php");

class setAdmins
{
	function show_admin($db_object,$common,$form_array,$user_id,$default)
	{
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/core/set_admins.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
		$type="type_".$default;
		
		$admins_table = $common->prefix_table("admins");
		$user_table = $common->prefix_table("user_table");
		$family_table = $common->prefix_table("family");
		$location_table = $common->prefix_table("location");
		$position_table = $common->prefix_table("position");
		$employment_table = $common->prefix_table("employment_type");
		$orgmain_table = $common->prefix_table("org_main");
		
		$mysql = "select email,user_id from $user_table where user_id='$user_id'";
		$selemail=$db_object->get_a_line($mysql);

		
		$mysql = "select email,user_id from $user_table";
		$mail_arr = $db_object->get_rsltset($mysql);

		
		$mysql = "select family_name,family_id from $family_table";
		$family_arr = $db_object->get_rsltset($mysql);

		$selqry="select user_id,username from $user_table where user_type='employee' and position is not null";
		$username_arr=$db_object->get_rsltset($selqry);		
		
//		$mysql="select loc_name,loc_id from $location_table";
//		$loc_arr = $db_object->get_rsltset($mysql);
			

		$loc_arr=$common->return_location($db_object);


		$mysql="select position_name,pos_id from $position_table";
		$pos_arr = $db_object->get_rsltset($mysql);
		
		$mysql="select $type as type,id from $employment_table";
		$emp_arr = $db_object->get_rsltset($mysql);
		
		$mysql="select levels,higher_order from $orgmain_table";
		$level_arr = $db_object->get_a_line($mysql);


		$mysql="select distinct($position_table.position_name) as boss_name,$position_table.pos_id as boss_id from $position_table,$position_table as temp_position_table where temp_position_table.boss_no=$position_table.pos_id";
		$boss_arr = $db_object->get_rsltset($mysql);
		
	
		$levels = $level_arr["levels"];

		
		$values['family_loop']=$family_arr;
		$values['location_loop']=$loc_arr;
		$values['position_loop']=$pos_arr;
		$values['employment_loop']=$emp_arr;
		$values['email_loop']=$mail_arr;
		$values['boss_loop']=$boss_arr;
		$values['username_loop']=$username_arr;
		
		$fields = $common->return_fields($db_object,$admins_table);
		$mysql = "select $fields from $admins_table where user_id='$user_id'";
		$sel_arr = $db_object->get_rsltset($mysql);
		
		for($i=0;$i<count($sel_arr);$i++)
		{
			$selfam_arr[] = $sel_arr[$i]["fam_id"];
			$selloc_arr[] = $sel_arr[$i]["location_id"];
			$selpos_arr[] = $sel_arr[$i]["pos_id"];		
			$selemp_arr[] = $sel_arr[$i]["emp_type_id"];
			$sellev_arr[] = $sel_arr[$i]["level_id"];
			$selboss_arr[]= $sel_arr[$i]["boss_id"];
			$selusername_arr[]	=$sel_arr[$i]["username"];
			$seluser_id[]		=$sel_arr[$i]["user_id"];
			


		}

		$sel_arr['family_loop']		= $selfam_arr;
		$sel_arr['location_loop']	= $selloc_arr;
		$sel_arr['position_loop']	= $selpos_arr;
		$sel_arr['employment_loop']	= $selemp_arr;
		$sel_arr['level_loop']		= $sellev_arr;
		$sel_arr['boss_loop']		= $selboss_arr;
		$sel_arr['username_loop']	= $seluser_id;
		$sel_arr['email_loop']["email"]	=$selemail["email"];
		
		
		$str = "";
		
	$levels=$common->return_levels($db_object);
		while(list($kk,$vv)=@each($levels))
		{
			$level_value[]["level_value"] = $levels[$kk];
		}
		
/*
	for($i=0;$i<$levels;$i++)
		{
			$x = $i+1;
			$level_value[$i]["level_value"] = $x;
		}
	
*/
		
	$values['level_loop'] = $level_value;
		
	$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$values,$sel_arr);
	
	$values["directreplace"]["user_id"]=$user_id;

	$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		

	echo $returncontent;
	
	}
	function insert_in_user_table($db_object,$common,$form_array,$user_id,$error_msg,$default)
	{
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		
		}
		$user_table=$common->prefix_table("user_table");
		$location_table=$common->prefix_table("location");
		$position_table=$common->prefix_table("position");
		$employment_type_table=$common->prefix_table("employment_type");
		$family_table=$common->prefix_table("family_position");
		
$and=") and (";
$or=" or "; 
//$selqry="select distinct(user_id) from $user_table,$family_table,$position_table where $family_table.position_id=$user_table.position and $position_table.pos_id=$user_table.position and (";
$selqry="select distinct(user_id) from $user_table,$family_table,$position_table where";
$entered=0;
$selqryapp="(";
		for($i=0;$i<count($employment_type);$i++)
		{
			
			
			$emp_type=$employment_type[$i];
			$selqryapp.="employment_type='$emp_type' ".$or;
			$entered=1;

		}
	if($entered==1)
	{
	$selqryapp=substr($selqryapp,0,-4);
	$selqryapp.=$and;
	$entered=0;
	}
	
		for($m=0;$m<count($level);$m++)
		{
			$emp_level=$level[$m];
			$selqryapp.="$position_table.level_no=$emp_level".$or;
			$enteredlevel=1;
	        }



if($enteredlevel==1)
{
	$selqryapp=substr($selqryapp,0,-4);
	$selqryapp.=$and;
}
	    	for($n=0;$n<count($boss);$n++)
	    	{
	    		$boss_id=$boss[$n];
		    	$selqryapp.="$position_table.boss_no='$boss_id'".$or;
		    	$enteredboss=1;
	    	}
	    
//	if($enteredlevel==1||$enteredboss==1)
	if($enteredboss==1)	    	
	{
		$join1=" $position_table.pos_id=$user_table.position";
		$selqryapp=substr($selqryapp,0,-4);
		$selqryapp.=$and;
		$selqryapp.=$join1;
		$selqryapp.=$and;
		$entered=0;
	}
            	
	    	
	
		for($j=0;$j<count($location);$j++)
		{
			$loc=$location[$j];
			$selqry3="$user_table.location='$loc'".$or;
			$selqryapp.=$selqry3;
			$entered=1;
		}
		if($entered==1)
		{
		$selqryapp=substr($selqryapp,0,-4);
		$selqryapp.=$and;
		$entered=0;
		}
//echo $selqryapp;
//exit;	
		for($k=0;$k<count($position);$k++)
		{
			$pos=$position[$k];
			$selqry2="$user_table.position='$pos'".$or;
			$selqryapp.=$selqry2;
			$entered=1;
		}
		if($entered==1)
		{
		$selqryapp=substr($selqryapp,0,-4);
		$selqryapp.=$and;
		$entered=0;
		}
			for($l=0;$l<count($families);$l++)
			{
				$fml_type=$families[$l];
				$selqry1="$family_table.family_id='$fml_type'".$or;
				$selqryexec.=$selqry1;
				$entered=1;
			}
		//	 $selqryexec=$selqry.$selqryapp.$selqryexec;

			if($entered==1)
			{
				$join2=" $family_table.position_id=$user_table.position) ";
		$selqryexec=substr($selqryexec,0,-4);
		//$selqryexec.=")";
		$selqryexec.=$and;
		$selqryexec.=$join2;
	//	$selqryexec.=")";
			}
			else
			{
				$selqryexec=substr($selqryexec,0,-5);
					$selqryexec.="1)";
			}
			$selqryexec=$selqry.$selqryapp.$selqryexec;
//			echo $selqryexec;

$user_idset=$db_object->get_rsltset($selqryexec);


$test=$user_idset[0]["user_id"];
//print_r($user_idset);
//exit;
if($test)
{
		$updqry="update $user_table set admin_id=0 where admin_id='$user_id'";
		$db_object->insert($updqry);
	//	echo $updqry;

	for($f=0;$f<count($user_idset);$f++)
	{
		$emp_id=$user_idset[$f]["user_id"];
	 	$updateqry="update $user_table set admin_id='$user_id' where user_id='$emp_id' and user_id<>'$user_id'";
		$db_object->insert($updateqry); 	
	//echo $updateqry;
	}
}
else
{
	echo $error_msg["cEmpdontexists"];
}
	
 }



function check_for_validation($db_object,$common,$form_array,$user_id,$error_msg,$default)
{
	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;
	}
	$family_position=$common->prefix_table("family_position");
	$position_table=$common->prefix_table("position");

	$selqry="select pos_id from $position_table where ";
	for($i=0;$i<count($level);$i++)
	{
		$level_no=$level[$i];		
		$extra.=" level_no='$level_no' or";		
	}
	$selqry.=$extra;
	$selqry=substr($selqry,0,-3);
	$result=$db_object->get_single_column($selqry);
	


	$selqry="select position_id from $family_position where ";
	for($k=0;$k<count($families);$k++)
	{
		$fam_id=$families[$k];
		$app1.=" family_id='$fam_id' or";
	}
	$selqry.=$app1;
	$selqry=substr($selqry,0,-3);
	$result1=$db_object->get_single_column($selqry);

	$selqry="select pos_id from $position_table where ";
	for($j=0;$j<count($location);$j++)
	{
		$loc_id=$location[$j];
		$app2.=" location=$loc_id or";
	}
	$selqry.=$app2;
	$selqry=substr($selqry,0,-3);
	$result3=$db_object->get_single_column($selqry);

	$selqry="select pos_id from $position_table where ";
	for($k=0;$k<count($boss);$k++)
	{
		$boss_no=$boss[$k];
		$app3.=" boss_no='$boss_no' or";
	}
	$selqry.=$app3;
	$selqry=substr($selqry,0,-3);
	$result4=$db_object->get_single_column($selqry);

		
	
$brk=0;
	for($j=0;$j<count($position);$j++)
	{
		$temp_pos=$position[$j];
		if(@in_array($temp_pos,$result))
		{
			continue;
		}
		else
		{
			$brk=1;
			echo "The Position $temp_pos is beyond the Levels Selected";
			break;
		}
		if(in_array($temp_pos,$result1))
		{
			
			continue;
		}
		else
		{
			$brk=1;
			echo "The Position $temp_pos is Beyond the family selected";
			break;
		}
		if(in_array($temp_pos,$result3))
		{
			continue;
		}
		else
		{
			$brk=1;
			echo "The Position $temp_pos  is not in the Locations Selected";
			break;
		}
		if(in_array($temp_pos,$resutl4))
		{

			continue;
		}
		else
		{
			$brk=1;
			echo "The Position $temp_pos is not a Position";
			break;		
		}
	}

	if($brk==1)
	{
		echo "<br>";
		echo $error_msg["cCharecmismat"];
	}
	else
	{
	$this->update_admin($db_object,$common,$form_array,$user_id,$error_msg,$default);
	}
//	print_r($result1);
//	print_r($level);
//	print_r($position);
//	exit;

	
}
		
	function update_admin($db_object,$common,$form_array,$user_id,$error_msg,$default)
	{
	
//$this->check_for_validation($db_object,$common,$form_array,$user_id,$error_msg,$default);
$this->insert_in_user_table($db_object,$common,$form_array,$user_id,$error_msg,$default);
		
		while(list($kk,$vv)=@each($form_array))
		{
		$$kk=$vv;
		}
		
		$admins_table = $common->prefix_table("admins");
			
		$mysql = "delete from $admins_table where user_id='$user_id'";
		$db_object->insert($mysql);
		
		for($i=0;$i<count($level);$i++)
		{
			
			$level_id = $level[$i];
			$mysql = "insert into $admins_table set user_id='$user_id',level_id='$level_id'";
			$db_object->insert($mysql);
		}
		for($j=0;$j<count($families);$j++)
		{
			
			$fam_id = $families[$j];
			$mysql = "insert into $admins_table set user_id='$user_id',fam_id='$fam_id'";
			$db_object->insert($mysql);
		}
		for($k=0;$k<count($location);$k++)
		{
			
			$location_id = $location[$k];
			$mysql = "insert into $admins_table set user_id='$user_id',location_id='$location_id'";
			$db_object->insert($mysql);
		}
		for($l=0;$l<count($position);$l++)
		{
			
			$pos_id = $position[$l];
			$mysql = "insert into $admins_table set user_id='$user_id',pos_id='$pos_id'";
			$db_object->insert($mysql);
		}
		for($m=0;$m<count($employment_type);$m++)
		{
			
			$emp_type_id = $employment_type[$m];
			$mysql = "insert into $admins_table set user_id='$user_id',emp_type_id='$emp_type_id'";
			$db_object->insert($mysql);
		}
		
		for($n=0;$n<count($boss);$n++)
		{
			$boss_id=$boss[$n];
			$mysql="insert into $admins_table set user_id='$user_id',boss_id='$boss_id'";
			$db_object->insert($mysql);
		
		}
		
		$message = $error_msg["cSetadmin_useradminister"];
		echo $message;
	$this->show_admin($db_object,$common,$form_array,$user_id,$default);
	}



	function delete_the_admin($common,$db_object,$user_id,$error_msg,$default)
	{
		$admins=$common->prefix_table("admins");
		$user_table=$common->prefix_table("user_table");
		
		$mysql="delete from admins where user_id='$user_id'";
		$db_object->insert($mysql);

		$mysql="update $user_table set admin_id=0 where admin_id='$user_id'";
		$db_object->insert($mysql);

		echo $error_msg["cAdmindel"];
		
	}
	
}
$obj = new setAdmins;

	while(list($kk,$vv)=@each($_POST))
		{
		$$kk=$vv;
		$form_array["$kk"]=$vv;
		
		}

	while(list($kk,$vv)=@each($_GET))
		{
		$$kk=$vv;
		$form_array["$kk"]=$vv;
		}

if($fDelete)
{
$obj->delete_the_admin($common,$db_object,$user_id,$error_msg,$default);

}
else
{
		if($fEditadmin)
		{
/*
//$obj->insert_in_user_table($db_object,$common,$form_array,$user_id,$error_msg,$default);
//$obj->update_admin($db_object,$common,$form_array,$user_id,$error_msg,$default);
*/

		$obj->check_for_validation($db_object,$common,$form_array,$user_id,$error_msg,$default);
		}
		else
		{
		$obj->show_admin($db_object,$common,$form_array,$user_id,$default);
		}
}
include("footer.php");
?>