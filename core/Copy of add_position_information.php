<?php
include("../session.php");
include("header.php");
class Add_position_information
{
	function display($common,$db_object,$user_id,$pos_id,$default)
	{
		$path=$common->path;
		$xFile=$path."templates/core/add_position_information.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		$position_table=$common->prefix_table("position");
		$family_table=$common->prefix_table("family");
		$family_position=$common->prefix_table("family_position");


		
		
		$flds=$common->return_fields($db_object,$position_table);
		$selqry="select $flds from   $position_table where pos_id='$pos_id'";
		$posdetails=$db_object->get_a_line($selqry);
		$arr=$common->return_levels($db_object);
		$famqry="select distinct($family_table.family_id),$family_table.family_name from $family_table left join $family_position on $family_table.family_id=$family_position.family_id";
		$familyarr=$db_object->get_rsltset($famqry);
		$selqry="select level_no,location  from $position_table where pos_id='$pos_id'";
		$posi_level=$db_object->get_a_line($selqry);
		$poslevel=$posi_level["level_no"];
		$poslocation=$posi_level["location"];	
		$selqry="select family_id from $family_position where position_id='$pos_id'";
		$posfamily=$db_object->get_a_line($selqry);		
		$sel["family_loop"]["family_id"]=array($posfamily["family_id"]);
		$location_arr=$common->return_location_for_display($db_object);
		$xTemplate=$common->singleloop_replace($db_object,"level_loopstart","level_loopend",$xTemplate,$arr,$poslevel);
		$xTemplate=$common->singleloop_replace($db_object,"location_loopstart","location_loopend",$xTemplate,$location_arr,$poslocation);
		$values["family_loop"]=$familyarr;
		
		$xTemplate=$common->multipleselect_replace($db_object,$xTemplate,$values,$sel);

$vasl["pos_id"]=$pos_id;
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vasl);
		echo $xTemplate;
	}
	function add_new_Pos($common,$db_object,$form_array,$user_id,$default,$error_msg)
	{
		extract($form_array);
		$position_table=$common->prefix_table("position");
		$insqry="insert into $position_table set position_name='$fNewPosition',date_added=now(),added_by='$user_id'";
		$pos_id=$db_object->insert_data_id($insqry);
		echo $error_msg["cPositionadded"];
		$this->display($common,$db_object,$user_id,$pos_id,$default);
	}
	function update_position($common,$db_object,$form_array,$error_msg)
	{
		extract($form_array);
		$position_table=$common->prefix_table("position");
		$family_position=$common->prefix_table("family_position");
		$insqry="update $position_table set level_no='$fLevel',location='$fLocation' where pos_id='$position_id'";
		$db_object->insert($insqry);
		$delqry="delete from $family_position where pos_id='$position_id'";
		$db_object->insert($delqry);
		$insqry="insert into $family_position set position_id='$position_id',family_id='$fFamily'";
		$db_object->insert($insqry);
		echo $error_msg["cPositionupdated"];
	}
}
$posobj= new Add_position_information;
//echo "posi=$fPosition";
if($fSubmit)
{
$posobj->update_position($common,$db_object,$post_var,$error_msg);	
}
else if($fPosition)
{
$pos_id=$fPosition;
$posobj->display($common,$db_object,$user_id,$pos_id,$default);
}
else if($fNewPosition!="")
{
$posobj->add_new_Pos($common,$db_object,$post_var,$user_id,$default,$error_msg);
}
include("footer.php");
?>