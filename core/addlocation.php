<?php
include("../session.php");

class AddLocation
{
	function add_locationdisplay($common,$db_object,$form_array,$error_msg)
	{
			$location_table=$common->prefix_table("location_table");

		$values["flag"]=$error_msg["cAddtext"];

		while(list($kk,$vv)=@each($form_array))
		{
			$$kk=$vv;
			if(ereg("fDelete_",$kk))
			{
				if($vv!="")
				{
					$location_id=split("_",$kk);
					$loc_id1=$location_id[1];
					$delqry="delete from $location_table where location_id='$loc_id1'";
					$db_object->insert($delqry);
					echo "<script>window.location.replace('locationsetting.php')</script>";
				}
			}
			else	if(ereg("^fEdit_",$kk))
			{

				$values["flag"]=$error_msg["cEdittext"];
				if($vv!="")
				{
					$location_id=split("_",$kk);
					$loc_id=$location_id[1];
				}
			}
		}


		$path=$common->path;
		$xFile=$path."templates/core/addlocation.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
	
		$fields=$common->return_fields($db_object,$location_table);
		$selqry="select $fields from $location_table where location_id='$loc_id'";
		$locationset=$db_object->get_a_line($selqry);

$values["first_level"]=$locationset["first_level"];
$values["second_level"]=$locationset["second_level"];
$values["third_level"]=$locationset["third_level"];
$values["fourth_level"]=$locationset["fourth_level"];
$values["fifth_level"]=$locationset["fifth_level"];
$values["sixth_level"]=$locationset["sixth_level"];
$values["seventh_level"]=$locationset["seventh_level"];
$values["location_id"]=$loc_id;
$xTemplate=$common->direct_replace($common,$xTemplate,$values);
		echo $xTemplate;
	
	}
	function add_location($common,$db_object,$form_array)
	{

		while(list($kk,$vv)=@each($form_array))
		{
			$$kk=$vv;
		}
		$location_table=$common->prefix_table("location_table");

		if($fLocation_id!="")
		{
			$insqry="update $location_table set first_level='$fFirst_level',second_level='$fSecond_level',third_level='$fThird_level',fourth_level='$fFourth_level',fifth_level='$fFifth_level',sixth_level='$fSixth_level',seventh_level='$fSeventh_level' where location_id='$fLocation_id'";
						
			
		}
		else
		{
			$insqry="insert into $location_table set first_level='$fFirst_level',second_level='$fSecond_level',third_level='$fThird_level',fourth_level='$fFourth_level',fifth_level='$fFifth_level',sixth_level='$fSixth_level',seventh_level='$fSeventh_level'";
		}
		
		$db_object->insert($insqry);
		
		
	}
}
$locobj=new AddLocation;
if($fSave)
{
	$locobj->add_location($common,$db_object,$post_var);
	header("Location:locationsetting.php");

}
else
{
include("header.php");
$locobj->add_locationdisplay($common,$db_object,$post_var,$error_msg);
}

include("footer.php");
?>
