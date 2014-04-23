<?php
include("../session.php");
include("header.php");
class Locations
{
	
  function location_display($common,$db_object,$denotes,$form_array,$id,$gbl_loc)
  {

  	while(list($kk,$vv)=each($form_array))
  	{
  		$$kk=$vv;
  	}
	$path=$common->path;
	$xFile=$path."templates/core/location_settings.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);


	
	if($denotes!=7)
	{
		preg_match("/<{name_areastart}>(.*?)<{name_areaend}>/s",$xTemplate,$match);
		$replace=$match[1];
		$xTemplate=preg_replace("/<{name_areastart}>(.*?)<{name_areaend}>/s",$replace,$xTemplate);
	}
	else
  	{
	$xTemplate=preg_replace("/<{name_areastart}>(.*?)<{name_areaend}>/s","<{name}>",$xTemplate);
		
  	}


	

	preg_match("/<{back_loopstart}>(.*?)<{back_loopend}>/s",$xTemplate,$match1);
	$replace1=$match1[1];
	$location_relate=$common->prefix_table("location_relate");
	$subqry="select loc_id from $location_relate where sub_id='$id'";
	$backid=$db_object->get_a_line($subqry);
//	$values["directreplace"]["id"]=$backid["loc_id"];
//	$values["directreplace"]["denotes"]=$denotes-1;

	$values["id"]=$backid["loc_id"];
	$values["denotes"]=$denotes-1;
	$replace1=$common->direct_replace($db_object,$replace1,$values);
	if($denotes!=1)
	{
	$xTemplate=preg_replace("/<{back_loopstart}>(.*?)<{back_loopend}>/s",$replace1,$xTemplate);
	}
	else
	{
		$xTemplate=preg_replace("/<{back_loopstart}>(.*?)<{back_loopend}>/s","",$xTemplate);
	}

  	
	$values["directreplace"]["Location"]=$gbl_loc[$denotes];
	$values["directreplace"]["denotes"]=$denotes;
	$values["directreplace"]["id"]=$id;
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);

	$location_relate=$common->prefix_table("location_relate");
	$location=$common->prefix_table("location");
	$query="select $location_relate.id,$location_relate.sub_id,$location.loc_id,$location.loc_name,$location.denotes from $location_relate,$location where $location.loc_id=$location_relate.sub_id";
	$bit1=" and $location_relate.loc_id='$id' ";
	$bit2=" and $location.denotes='$denotes'";
	if($denotes==1)
	{
		$query=$query.$bit2;
	}
	else
	{
		$query=$query.$bit1.$bit2;
	}

	$locationset=$db_object->get_rsltset($query);

	preg_match("/<{location_loopstart}>(.*?)<{location_loopend}>/s",$xTemplate,$match);
	$replace=$match[1];
	
	for($i=0;$i<count($locationset);$i++)
	{
		$sub_denotes=$locationset[$i]["denotes"]+1;
		$name=$locationset[$i]["loc_name"];
		$id=$locationset[$i]["loc_id"];
		$re.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
	}
	$xTemplate=preg_replace("/<{location_loopstart}>(.*?)<{location_loopend}>/s",$re,$xTemplate);
		

	
	echo $xTemplate;
  }

 function addnew_location($common,$db_object,$form_array,$denotes)
  {

  	while(list($kk,$vv)=each($form_array))
  	{
  		$$kk=$vv;
  	}


  	$location=$common->prefix_table("location");
	$query="insert into $location set loc_name='$fLocation_name',denotes='$denotes'";
	$sub_id=$db_object->insert_data_id($query);


	$location_relate=$common->prefix_table("location_relate");
	$query="insert into $location_relate set loc_id='$id',sub_id='$sub_id'";
	$db_object->insert($query);
  }
  }
$locobj=new Locations;
if($fAdd)
{
$locobj->addnew_location($common,$db_object,$_POST,$denotes);	
}
$locobj->location_display($common,$db_object,$denotes,$_POST,$id,$gbl_loc);
include("footer.php");
?>