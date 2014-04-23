<?php
include("../session.php");
include("header.php");
class Edit_location
{ 
  function display($common,$db_object,$id)
  {

  	$path=$common->path;
	$xFile=$path."templates/core/edit_location.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$location=$common->prefix_table("location");
	$qry="select loc_id,loc_name,denotes from $location where loc_id='$id'";
	$result=$db_object->get_a_line($qry);

	//echo $qry;
	
	$location_relate=$common->prefix_table("location_relate");
	$qry2="select loc_id from $location_relate where sub_id='$id'";
	$subresult=$db_object->get_a_line($qry2);

//	$values["directreplace"]["location"]=$result["loc_name"];
//	$values["directreplace"]["id"]=$result["loc_id"];
//	$values["directreplace"]["front_id"]=$subresult["loc_id"];
//	$values["directreplace"]["denotes"]=$result["denotes"];


	$values["location"]=$result["loc_name"];
	$values["id"]=$result["loc_id"];
	$values["front_id"]=$subresult["loc_id"];
	$values["denotes"]=$result["denotes"];
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	
	echo $xTemplate;
   }

  function update($common,$db_object,$form_array)
  {
  	while(list($kk,$vv)=each($form_array))
  	{
  		$$kk=$vv;
  	}
  	$location=$common->prefix_table("location");
  	$qry="replace into $location set loc_id='$id',loc_name='$fLocation',denotes='$denotes'";
  	$db_object->insert($qry);
  	// echo $qry;
  	 //exit;
  }


}
$edobj=new Edit_location;
//echo $id;
if($fSubmit)
{
	$edobj->update($common,$db_object,$_POST);
}
$edobj->display($common,$db_object,$id);
include("footer.php");
?>