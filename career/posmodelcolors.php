<?php
/*---------------------------------------------
SCRIPT:posmodelcolors.php
AUTHOR:info@chrisranjana.com	
UPDATED:11th Dec

DESCRIPTION:
This script is used to set the colors for the position model links.

---------------------------------------------*/
include("../session.php");
include_once('header.php');

class model_colors
{
	function show_options($common,$db_object,$post_var)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/career/posmodelcolors.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);

		$posmodel_colors = $common->prefix_table('posmodel_colors');
	
		
		$mysql = "select key_1,key_2,key_3,key_4,key_5 from $posmodel_colors where posmodel_id = '1'";
		$val_arr = $db_object->get_a_line($mysql);
		
		$values["directreplace"]["key_1"]=$val_arr["key_1"];
		$values["directreplace"]["key_2"]=$val_arr["key_2"];
		$values["directreplace"]["key_3"]=$val_arr["key_3"];
		$values["directreplace"]["key_4"]=$val_arr["key_4"];
		$values["directreplace"]["key_5"]=$val_arr["key_5"];
		
		
		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
	 
	}
function update_options($common,$db_object,$post_var,$default)
{
	while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
	//print_r($post_var);

		$posmodel_colors = $common->prefix_table('posmodel_colors');

		$selqry="select posmodel_id from $posmodel_colors where posmodel_id='1'";
	  	$idrslt=$db_object->get_a_line($selqry);

		$pm_id = $idrslt['posmodel_id'];
		if($pm_id != '')
		{
		$mysql = "update $posmodel_colors set key_1 = '$key_1' , key_2 = '$key_2' ,key_3 = '$key_3',key_4 = '$key_4',key_5 = '$key_5' where posmodel_id = '1'";

		$db_object->insert($mysql);

		
		}
		else
		{

		$mysql = "insert into $posmodel_colors set key_1 = '$key_1' , key_2 = '$key_2' ,key_3 = '$key_3',key_4 = '$key_4',key_5 = '$key_5'";
		$db_object->insert($mysql);

		}
		
		

}
}
$obj = new model_colors;


if($fUpdate)
{

$obj->update_options($common,$db_object,$post_var,$default);
echo $error_msg['cPosmodelcolorupdated'];

}
if($user_id == 1)
{
$obj->show_options($common,$db_object,$post_var);
}
else
{
echo $error_msg["cNoPermission"];
}
include_once('footer.php');
?>
