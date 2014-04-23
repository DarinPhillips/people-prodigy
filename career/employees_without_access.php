<?php
include("../session.php");
include("header.php");
class View_EmployeeRights
{
	function display_employee($common,$db_object,$user_id,$error_msg)
	{
		$path=$common->path;
		$xFile=$path."templates/career/core_data/employees_without_access.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);

		$user_table=$common->prefix_table("user_table");
		$fields=$common->return_fields($db_object,$user_table); 
		if($user_id!=1)
		{
		$selqry="select $fields, date_format(reg_date,'%m.%d.%Y.%i:%s') as reg_date 

 from $user_table where user_type='employee' and admin_id='$user_id' and access_rights is null";
		}
		else
		{
				$selqry="select $fields, date_format(reg_date,'%m.%d.%Y.%i:%s') as reg_date 

 from $user_table where user_type='employee' and user_id<>'$user_id' and access_rights is null";
		}
		


		$userset=$db_object->get_rsltset($selqry);
if($userset[0]=="")
{
	echo $error_msg['cNoEmployeeNoAccess'];
	
	include_once("footer.php");exit;
}
for($i=0;$i<count($userset);$i++)
{
	$temp_user_id=$userset[$i]["added_by"];
	$userset[$i]["added_by"]=$common->name_display($db_object,$temp_user_id);
}
	$values["user_loop"]=$userset;
	$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);

	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;
	}
}
$empobj1=new View_EmployeeRights;
$empobj1->display_employee($common,$db_object,$user_id,$error_msg);

include("footer.php");
