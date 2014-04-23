<?php
include("../session.php");
include("header.php");
class Opporutnity
{
	function eeo_display($common,$db_object,$error_msg,$default,$user_id)
	{
		$path=$common->path;
		$xFile=$path."templates/core/display_eeo_status.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);

$opp_status=$common->prefix_table("opportunity_status");
$fields=$common->return_fields($db_object,$opp_status);
$selqry="select $fields from $opp_status";
$oppset=$db_object->get_rsltset($selqry);
$values["eeostatus_loop"]=$oppset;
$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	
		echo $xTemplate;
	}
	function add_eeo($common,$db_object,$form_array,$default,$cat_id,$error_msg)
	{
		extract($form_array);
		$opp_status=$common->prefix_table("opporutnity_stauts");
		$insqry="replace into $opp_status eeo_id='$cat_id',category='$fCategory',tag='$fTag'";
		$data_id=$db_object->insert_data_id($insqry);
		if($data_id)
		{
			echo $error_msg["cEeoAdded"];
			echo "Added";
		}
		
	}
	
 
}
$eeoobj= new Opporutnity;

$eeoobj->eeo_display($common,$db_object,$error_msg,$default,$user_id);

include("footer.php");

?>