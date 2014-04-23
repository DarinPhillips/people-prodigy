<?php
include("../session.php");
include("header.php");
class Add_employee_information
{
	function employee_add($common,$db_object,$form_array,$user_id,$default,$employee_id=null)
	{
		$path=$common->path;
	//	extract($form_array);
	//	print_r($form_array);
		$xFile=$path."templates/career/core_data/add_employee_information.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);





		$xTemplate=preg_replace("/{{default}}/s",$default,$xTemplate);
		
		$user_table=$common->prefix_table("user_table");
		$position_table=$common->prefix_table("position");
		$employent_type_table=$common->prefix_table("employment_type");
		$access_rights_table=$common->prefix_table("access_rights");
		$admins_table=$common->prefix_table("admins");




//---returns the positions below the admins position
$selqry="select position from $user_table where user_id='$user_id'";
$admin_position=$db_object->get_a_line($selqry);

$directreports=$common->get_chain_below($admin_position["position"],$db_object,$twodarry);
$positionsbelow=@implode(",",$directreports);


		$field=$common->return_fields($db_object,$user_table);
		$selqry="select $field from $user_table where user_id='$employee_id' ";
		$employeedetail=$db_object->get_a_line($selqry);
		
		$emppos=$employeedetail["position"];


		
		$selqry="select $user_table.username,$user_table.user_id from $position_table left join $user_table on $position_table.boss_no=$user_table.position where $position_table.pos_id='$emppos'";
		$userboss=$db_object->get_a_line($selqry);

		$type="type_".$default;	
		$selqry="select $user_table.user_id,$user_table.username from
		$user_table left join $position_table
		on $user_table.position=$position_table.boss_no
		where $position_table.boss_no is not null group by $user_table.user_id";
//		$selqry="select $admins_table.boss_id,$user_table.username from $admins_table left join $user_table on $user_table.user_id=$admins_table.boss_id where $admins_table.user_id='$user_id' and user_table.user_id is not null group by $admins_table.boss_id";

		$bossset=$db_object->get_rsltset($selqry);
		$values["boss_loop"]=$bossset;
		$sel_arr["boss_loop"]=array($userboss["user_id"]);


		
if($employeedetail["position"]!="" || $user_id==1)
{
		$selqry="select $position_table.pos_id, $position_table.position_name from $position_table left join $admins_table on $admins_table.pos_id=$position_table.pos_id where $admins_table.user_id='$user_id' ";
		
		//----------if neede me can change
		//-where pos_id in ($positionsbelow)
}
else
{
		$selqry="select $position_table.position_name,$position_table.pos_id from $position_table left join $user_table on $user_table.position=$position_table.pos_id left join $admins_table on $admins_table.pos_id=$position_table.pos_id where $user_table.position is null and $admins_table.user_id='$user_id'";
		
		//-----------and pos_id in ($positionsbelow)
}
		$positionset=$db_object->get_rsltset($selqry);
		$values["position_loop"]=$positionset;
		$sel_arr["position_loop"]=array($employeedetail["position"]);

		$selqry="select id,$type from $employent_type_table where status='Yes'";
		$employmenttypeset=$db_object->get_rsltset($selqry);

		$values["employment_loop"]=$employmenttypeset;
		$sel_arr["employment_loop"]=array($employeedetail["employment_type"]);

		$selqry="select id,$type from $access_rights_table where rights='Yes'";
		$accessset=$db_object->get_rsltset($selqry);
		$values["access_loop"]=$accessset;
		$sel_arr["access_loop"]=array($employeedetail["access_rights"]);

		
		$xTemplate=$common->multipleloop_replace($db_object,$xTemplate,$values,$sel_arr);
		
$values["email"]=$employeedetail["email"];			
$values["employee_id"]=$employee_id;
$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
		echo $xTemplate;
		
	}

	function update_employee($common,$db_object,$form_array,$user_id,$default,$employee_id,$error_msg)
	{
		
		extract($form_array);
		$user_table=$common->prefix_table("user_table");
		$family_position=$common->prefix_table("family_position");
		$position=$common->prefix_table("position");
		
		$email_qry="select email from $user_table where user_id<>'$employee_id'";
		
		$email_res=$db_object->get_single_column($email_qry);
		
		$ch_mail=@in_array($fEmail,$email_res);
		
		if($ch_mail!="")
		{
			echo $error_msg['cEmailAlreadyExists'];
			
			include_once("footer.php");
			exit;
		}
	
		$employment_type=$common->prefix_table("employment_type");
		
		if($fPosition)
		{
			$qry="select position from $user_table where user_id <>'$emoployee_id'";
			
			$res_qry=$db_object->get_single_column($qry);
			
			if(@in_array($fPosition,$res_qry))
			{

				$insqry="update $user_table set email='$fEmail',employment_type='$fEmployment',
				
				access_rights='$fAccess' where user_id='$employee_id'";
				
				$db_object->insert($insqry);
				
				echo $error_msg['cPositionFilled'];
				
				include_once("footer.php");
				
				exit;
			}
			
		}
	$selqry="select position from $user_table where position is not null and user_id<>'$employee_id'";
	$alreadyoccupied=$db_object->get_single_column($selqry);
	
	$selqry="select email from $user_table where user_id<>'$employee_id'";
	$emails=$db_object->get_single_column($selqry);
	
$selqry="select position,employment_type,access_rights from $user_table where user_id='$employee_id'";
$user_details=$db_object->get_a_line($selqry);
$errflag=0;
$insqry="update $user_table set email='$fEmail',position='$fPosition',employment_type='$fEmployment',
		access_rights='$fAccess' where user_id='$employee_id'";
		
		$db_object->insert($insqry);
		//echo $error_msg["cEmpDetailsUpdated"];
	if(!@in_array($fPosition,$alreadyoccupied)||!@in_array($fEmail,$emails))
	{	
		if($fAccess!=$user_details["access_rights"])
		{
			
			$errflag=1;
			
		/*	if(($fAccess==1 and $user_details["access_rights"]>1))
			{
				
				echo $error_msg['cCannotChange'];
				
				include_once("footer.php");
				
				exit;
			}*/
		}
		
		
		if($fPosition!=$user_details["position"])
		{
			
			$errflag=1;			

		}
		
		if($fEmployment!=$user_details["employment_type"])
		{
			
			$errflag=1;
		}

		
		if($errflag==1)
		{

			echo $error_msg["cUsersadminmightbechanged"];
				
		
	
		$emp_id=$fEmployment;
		
	$mysql="select level_no,boss_no,location from $position where pos_id='$fPosition'";
	
	$posar=$db_object->get_rsltset($mysql);
	$mysql="select family_id from family_position where family_position.position_id='$fPosition'";

	$fam_arr=$db_object->get_rsltset($mysql);
	$charec["level_no"]=$posar[0]["level_no"];
	$charec["boss_id"]=$posar[0]["boss_no"];
	$charec["location_id"]=$posar[0]["location"];
	$charec["family_id"]=$fam_arr[0]["family_id"];
	$charec["employment_type"]=$emp_id;
	$charec["pos_id"]=$fPosition;

	$admin_check["admin_id"]=$common->return_my_admin($db_object,$charec);
	
			
			//$common->change_admin($db_object,$fAccess,$fEmployment,$fPosition,$employee_id);
				$admin=$admin_check["admin_id"];
			$common->change_admin($db_object,$employee_id,$admin);
		}
		
	}
	else
	{	
		echo $error_msg["cUserforposexists"];
		$this->employee_add($common,$db_object,$form_array,$user_id,$default,$employee_id);
		
	}

	}
	function addnewemp($common,$db_object,$form_array,$user_id,$default,$error_msg)
	{
		$sub="";
		while(list($kk,$vv)=@each($form_array))
		{
			$$kk=$vv;
			if($vv!=''&& !ereg("fUsername",$kk) && !ereg("fNext",$kk))
			{
				$sub.=",$kk='$vv'";
			}
		}
		$user_table=$common->prefix_table("user_table");
$selqry="select username from $user_table where username='$fUsername'";
$existusrnm=$db_object->get_a_line($selqry);
if($existusrnm["username"])
{
	echo $error_msg["cUSernamealreadyexists"];
	include("footer.php");
	exit;
}
		$selqry="insert into $user_table set username='$fUsername',added_by='$user_id',reg_date=now()$sub";
		$emp_id=$db_object->insert_data_id($selqry);
		echo $error_msg["cEmployeeadded"];
$this->employee_add($common,$db_object,$form_array,$user_id,$default,$emp_id);		
		
	}
}
$empobj= new Add_employee_information;



//----------------------------control comes from the also from employee_without_position.php
if($fSubmit)
{
$empobj->update_employee($common,$db_object,$post_var,$user_id,$default,$employee_id,$error_msg);
}
else if($fEmployee=="")
{
$empobj->addnewemp($common,$db_object,$post_var,$user_id,$default,$error_msg);
}
else
{
$employee_id=$fEmployee;
$empobj->employee_add($common,$db_object,$post_var,$user_id,$default,$employee_id);
}
include("footer.php");
?>
