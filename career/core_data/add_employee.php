<?php
include("../session.php");
include("header.php");
Class Add_employee
{
function add_employees($common,$db_object,$user_id,$default,$emp_id=null)
{
$path=$common->path;
$xFile=$path."templates/career/core_data/add_employee.html";
$xTemplate=$common->return_file_content($db_object,$xFile);
$user_table=$common->prefix_table("user_table");
/*
if($emp_id!=null)
{
	$xTemplate=preg_replace("/{{targetfilename}}/s","add_employee_information",$xTemplate);
}
else
{
	$xTemplate=preg_replace("/{{targetfilename}}/s","add_employee",$xTemplate);
}
*/

$name="name_".$default;
$name_fields=$common->prefix_table("name_fields");
$selqry="select field_name,$name from $name_fields where status='YES'";
$fields=$db_object->get_rsltset($selqry);
$fieldofuser=$common->return_fields($db_object,$user_table);

$selqry="select $fieldofuser from $user_table where user_id='$emp_id'";
$user_details=$db_object->get_a_line($selqry);

preg_match("/<{dyna_field_loopstart}>(.*?)<{dyna_field_loopend}>/s",$xTemplate,$mat);
$replace=$mat[1];

for($i=0;$i<count($fields);$i++)
{
	$field_value=$fields[$i][$name];
	$field_name=$fields[$i]["field_name"];
	$fielduser_value=$user_details[$field_name];
$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
	
}
$xTemplate=preg_replace("/<{dyna_field_loopstart}>(.*?)<{dyna_field_loopend}>/s",$replaced,$xTemplate);
$selqry="select user_id,username from $user_table  order by user_id";
$usernames=$db_object->get_rsltset($selqry);


$values["employee_loop"]=$usernames;
$sel_arr["employee_loop"]=array($emp_id);
$xTemplate=$common->multipleloop_replace($db_object,$xTemplate,$values,$sel_arr);
$vals["employee_id"]=$emp_id;
$vals["username"]=$user_details["username"];
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;
}
}
$empobj= new Add_employee;
if($fEmployee)
{
	$emp_id=$fEmployee;

}
echo $employee_id;
$empobj->add_employees($common,$db_object,$user_id,$default,$emp_id);
include("footer.php");
?>