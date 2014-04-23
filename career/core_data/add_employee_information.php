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
		$xFile=$path."templates/core/add_employee_information.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);

		$xTemplate=preg_replace("/{{default}}/s",$default,$xTemplate);
		
		$user_table=$common->prefix_table("user_table");
		$position_table=$common->prefix_table("position");
		$employent_type_table=$common->prefix_table("employment_type");
		$access_rights_table=$common->prefix_table("access_rights");

		
		$field=$common->return_fields($db_object,$user_table);
		$selqry="select $field from $user_table where user_id='$employee_id'";
		$employeedetail=$db_object->get_a_line($selqry);
		$emppos=$employeedetail["position"];
		$selqry="select $user_table.username,$user_table.user_id from $position_table left join $user_table on $position_table.boss_no=$user_table.position where $position_table.pos_id='$emppos'";
		$userboss=$db_object->get_a_line($selqry);



		$type="type_".$default;	
		$selqry="select $user_table.user_id,$user_table.username from
		$user_table left join $position_table
		on $user_table.position=$position_table.boss_no
		where $position_table.boss_no is not null group by $user_table.user_id";
		$bossset=$db_object->get_rsltset($selqry);
		$values["boss_loop"]=$bossset;
		$sel_arr["boss_loop"]=array($userboss["user_id"]);
if($employeedetail["position"]!="")
{
		$selqry="select pos_id,position_name from $position_table";
}
else
{
		$selqry="select $position_table.position_name,$position_table.pos_id from $position_table left join $user_table on $user_table.position=$position_table.pos_id where $user_table.position is null";
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
	$selqry="select position from $user_table where position is not null";
	$alreadyoccupied=$db_object->get_single_column($selqry);


	if(!@in_array($fPosition,$alreadyoccupied))
	{		
		$insqry="update $user_table set password='$fEmail',email='$fEmail',position='$fPosition',employment_type='$fEmployment',
		access_rights='$fAccess' where user_id='$employee_id'";
		$db_object->insert($insqry);
		echo $error_msg["cEmpDetailsUpdated"];
		
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
