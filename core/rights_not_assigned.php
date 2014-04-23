<?php
include("../session.php");
include("header.php");
class Show_rights
{
 function display_rights($common,$db_object,$user_id,$default)
 {

		$path=$common->path;
		$xFile=$path."templates/core/rights_not_assigned.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		$type="type_".$default;
		$user_table=$common->prefix_table("user_table");
		$access_rights=$common->prefix_table("access_rights");
		$selqry="select $access_rights.$type as access_rights,$access_rights.id,$access_rights.date_added  from $access_rights left join $user_table on $access_rights.id=$user_table.access_rights where $access_rights.rights='yes' and $user_table.access_rights is null"; 
		$accessset=$db_object->get_rsltset($selqry);

		$values["access_loop"]=$accessset;
$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);
		$vals=array();
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);	
		echo $xTemplate;

 }  
}
$strobj= new Show_rights;
$strobj->display_rights($common,$db_object,$user_id,$default);
include("footer.php");
?>