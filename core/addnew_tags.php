<?php
include("../session.php");
include("header.php");
class Eeo
{
	function add_eeo($common,$db_object,$user_id,$error_msg,$eeo_id=null)
	{
		$path=$common->path;
		$xFile=$path."templates/core/addnew_tags.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		$opp_status=$common->prefix_table("opportunity_status");
		$fields=$common->return_fields($db_object,$opp_status);
		
		$selqry="select $fields from $opp_status where eeo_id='$eeo_id'";
		$eeoset=$db_object->get_a_line($selqry);
		$values["category"]=$eeoset["category"];
		$values["tag"]=$eeoset["tag"];
		$values["eeo_id"]=$eeoset["eeo_id"];
$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
		echo $xTemplate;
	}
	function add_eeo_newly($common,$db_object,$form_array,$default,$cat_id,$error_msg)
	{
		extract($form_array);

$newtags=split(",",$fTag);
		
		$opp_status=$common->prefix_table("opportunity_status");
	for($i=0;$i<count($newtags);$i++)
	{
		$fTagnew=$newtags[$i];
		if($fTagnew!="")
		{
		$insqry="replace into $opp_status set eeo_id='$cat_id',category='$fCategory',tag='$fTagnew'";
		$data_id=$db_object->insert_data_id($insqry);
		}
	}
		if($data_id)
		{
			echo $error_msg["cEeoAdded"];		
		}
		
	}
}

$eeoobj= new Eeo;
if($fSubmit)
{
$eeoobj->add_eeo_newly($common,$db_object,$post_var,$default,$cat_id,$error_msg);
echo "<script>window.location.replace('display_eeo_status.php')</script>";
exit;
}
else
{
$eeoobj->add_eeo($common,$db_object,$user_id,$error_msg,$eeo_id);
}
include("footer.php");

?>