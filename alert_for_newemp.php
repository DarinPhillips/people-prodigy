<?php
include("session.php");
include("header.php");
class Alert_for_emp
{
   function alert_display($common,$db_object,$user_id)
   {
$path=$common->path;
$xFile=$path."templates/alert_for_newemp.html";
$xTemplate=$common->return_file_content($db_object,$xFile);
//-----tables-----------
$temp_user_table=$common->prefix_table("temp_user_table");
$user_table=$common->prefix_table("user_table");
$selqry="select $temp_user_table.user_id,$user_table.username as username from $temp_user_table,$user_table where $temp_user_table.user_id=$user_table.user_id and $user_table.admin_id='$user_id'";
$empset=$db_object->get_rsltset($selqry);

preg_match("/<{emp_loopstart}>(.*?)<{emp_loopend}>/s",$xTemplate,$match);
$replace=$match[1];
for($i=0;$i<count($empset);$i++)
{
	$emp_name=$empset[$i]["username"];
	$id=$empset[$i]["user_id"];
	$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
}
$xTemplate=preg_replace("/<{emp_loopstart}>(.*?)<{emp_loopend}>/s",$replaced,$xTemplate);
$vals=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;
    }


}
$empobj=new Alert_for_emp;
$empobj->alert_display($common,$db_object,$user_id);
include("footer.php");
?>
