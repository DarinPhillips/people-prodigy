<?php
include("../session.php");
include("header.php");
class Positiondisplay
{
	function displayposition($common,$db_object,$user_id)
	{
		$path=$common->path;
		$xFile=$path."templates/core/positionsetting.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		$position_table=$common->prefix_table("position");
		$fields=$common->return_fields($db_object,$position_table);
		$location_table=$common->prefix_table("location_table");
		$locfields=$common->return_fields($db_object,$location_table);
/*
		$selqry="select $locfields from $location_table";
		$locationset=$db_object->get_rsltset($selqry);
*/

		$locationarray=$common->return_location_for_display($db_object);


		$selqry="select $fields from $position_table order by position_name asc ";



		$positionset=$db_object->get_rsltset($selqry);



		preg_match("/<{position_loopstart}>(.*?)<{position_loopend}>/s",$xTemplate,$mat);
		$replace=$mat[1];
		for($i=0;$i<count($positionset);$i++)
		{
			$position_name=$positionset[$i]["position_name"];
			$level=$positionset[$i]["level_no"];
			$lcid=$positionset[$i]["location"];
			$location=$locationarray[$lcid];
			$id=$positionset[$i]["pos_id"];
			$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
		}

$xTemplate=preg_replace("/<{position_loopstart}>(.*?)<{position_loopend}>/s",$replaced,$xTemplate);
			

$vals=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
		
		echo $xTemplate;
		
		
	}
	
}
$posobj=new Positiondisplay;
$posobj->displayposition($common,$db_object,$user_id);
include("footer.php");
?>
