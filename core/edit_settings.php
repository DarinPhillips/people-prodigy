<?php
include("../session.php");
include("header.php");
class Edit_setting
{
  function display_setting($common,$db_object)
  {
	$path=$common->path;
	$xFile=$path."templates/core/edit_settings.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$config=$common->prefix_table("config");
	$selqry="select count_of_employees,min_time from $config where id=1";
	$rslt=$db_object->get_a_line($selqry);
	$count=$rslt["count_of_employees"];
	$peraff = $rslt['person_affected'];
	$perhelp  = $rslt['person_help_needed'];
	$vals["count"]=$count;
	$vals["peraff"] = $peraff;
	$vals["perhelp"] = $perhelp;
	$vals["qualification"] = $rslt['no_of_qualification'];
	$vals["time"] = $rslt['min_time'];

	if($rslt['i_boss']=='Y')
	{
		$vals["ichecked"]="checked";
	}
	if($rslt['b_boss']=='Y')
	{
		$vals["bchecked"]="checked";
	}
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;
   }
   function update($common,$db_object,$form_array)
   {
   	while(list($kk,$vv)=each($form_array))
   	{
   		$$kk=$vv;
   	}
//   	print_r($form_array);
   	$config=$common->prefix_table("config");
   	$insqry="update $config set count_of_employees='$fCount',min_time='$fMaxtime' where id=1";
   	$db_object->insert($insqry);
   	
   }
}
$obj=new Edit_setting;
if($fUpdate)
{
	$obj->update($common,$db_object,$post_var);
}
$obj->display_setting($common,$db_object);
include("footer.php");
?>
