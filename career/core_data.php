<?php
include("../session.php");
include("header.php");

//----this core data files are also in the core module
//----the difference is that the here the data displayed here in career module are associated
//----with the admins table.
//--data displayed here are the data that are associated with that particular admin in the admins table
//---But in the core module all the dat are displayed




class Core_data
{
	function display_core_data($common,$db_object,$user_id)
	{
		$path=$common->path;
$xFile=$path."templates/career/core_data/core_data.html";
$xTemplate=$common->return_file_content($db_object,$xFile);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;
	}
}
$coreobj= new Core_data;
$yes=$common->is_admin($db_object,$user_id);
if($yes||$user_id==1)
{
$coreobj->display_core_data($common,$db_object,$user_id);
}
else
{
	echo "This is Administrators Area";
}
include("footer.php");
?>
