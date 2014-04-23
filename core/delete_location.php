<?php
include("../session.php");
include("header.php");
class Delete
{
	function Delete($common,$db_object,$id,$denotes)
	{
		$location_relate=$common->prefix_table("location_relate");
		$qry="select loc_id from $location_relate where sub_id='$id'";
		$main_id=$db_object->get_a_line($qry);
		$this->mainid=$main_id["loc_id"];
		
	}
	
  function delete_location($common,$db_object,$id,$denotes)
  {
	$location=$common->prefix_table("location");
	$location_relate=$common->prefix_table("location_relate");
	
	$qry="delete from $location where loc_id='$id'";
	$db_object->insert($qry);
	
		
	$qry2="select sub_id from $location_relate where loc_id='$id'";
	$result=$db_object->get_rsltset($qry2);
	

	$qry1="delete from $location_relate where sub_id='$id'";
	$db_object->insert($qry1);


	for($i=0;$i<count($result);$i++)
	{
	$this->delete_location($common,$db_object,$result[$i]["sub_id"],$denotes);
	}
	$id=$this->mainid;

echo "<script language='javascript'>window.location.replace('location_settings.php?id=$id&denotes=$denotes')</script>";

  }
}
$delobj=new Delete($common,$db_object,$id,$denotes);
$delobj->delete_location($common,$db_object,$id,$denotes);
include("footer.php");
?>