<?php
include("../session.php");
include("header.php");
Class Add_Position
{
	function add_positions($common,$db_object,$user_id,$default,$pos_id)
	{
		$path=$common->path;
		$xFile=$path."templates/core/add_position.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		$position_table=$common->prefix_table("position");
		$fld=$common->return_fields($db_object,$position_table);
		$selqry="select position_name,pos_id from $position_table";
		$posset=$db_object->get_rsltset($selqry);
		$value["position_loop"]=$posset;
		$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$value);
		$val=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$val);	
		echo $xTemplate;
	}
}
$posobj= new Add_Position;
$posobj->add_positions($common,$db_object,$user_id,$default,$pos_id);
include("footer.php");
?>