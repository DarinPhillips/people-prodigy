<?php
/*---------------------------------------------
SCRIPT:graph_options.php
AUTHOR:info@chrisranjana.com	
UPDATED:5th Nov

DESCRIPTION:
This script is used to set the options for the graphs.

---------------------------------------------*/
include("../session.php");
include_once('header.php');

class graphOptions
{
	function view_graph_options($db_object,$common,$post_var,$user_id,$default,$gbl_grouprater_inter)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		}
		
		$xPath=$common->path;
		$xTemplate=$xPath."/templates/career/graph_options.html";
		$returncontent=$common->return_file_content($db_object,$xTemplate);
	 
		$career_colors = $common->prefix_table('career_colors');
	
		
		$mysql = "select career_bgcolor,career_border,career_grp_self,career_grp_team,career_grp_incus,career_grp_boss,career_grp_peer,career_grp_excus,career_grp_topboss,career_grp_dirrep,career_grp_other from $career_colors where color_id = '1'";
		$val_arr = $db_object->get_a_line($mysql);
		
		$values["directreplace"]["career_bgcolor"]=$val_arr["career_bgcolor"];
		$values["directreplace"]["career_border"]=$val_arr["career_border"];
		$values["directreplace"]["career_grp_self"]=$val_arr["career_grp_self"];
		$values["directreplace"]["career_grp_team"]=$val_arr["career_grp_team"];
		$values["directreplace"]["career_grp_incus"]=$val_arr["career_grp_incus"];
		$values["directreplace"]["career_grp_boss"]=$val_arr["career_grp_boss"];
		$values["directreplace"]["career_grp_peer"]=$val_arr["career_grp_peer"];
		$values["directreplace"]["career_grp_excus"]=$val_arr["career_grp_excus"];
		$values["directreplace"]["career_grp_topboss"]=$val_arr["career_grp_topboss"];	
		$values["directreplace"]["career_grp_dirrep"]=$val_arr["career_grp_dirrep"];		
		$values["directreplace"]["career_grp_other"]=$val_arr["career_grp_other"];		
		
		

		$returncontent = $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;

		
		
	}	//end of function view_graph_options()
	
	function update_options($db_object,$common,$post_var,$user_id,$default)
	{
		while(list($kk,$vv)=@each($post_var))
		{
		$$kk=$vv;
		
		}
		
		
		//print_r($post_var);exit;
$career_colors = $common->prefix_table('career_colors');
		
if($career_bgcolor=="" ||$career_border=="" ||$career_grp_self==""||$career_grp_team==""||$career_grp_incus==""||$career_grp_boss==""||$career_grp_peer==""||$career_grp_excus=="" || $career_grp_topboss == "" || $career_grp_dirrep == "" || $career_grp_other == "")
{
	echo  "Some fields are entered as Null";
}
else
{

  	$selqry="select color_id from $career_colors where color_id='1'";
  	$idrslt=$db_object->get_a_line($selqry);
    	if($idrslt["color_id"] != '1')
  	{
  	$updqry="insert into $career_colors set color_id='1',
  					career_bgcolor='$career_bgcolor',
  					career_border='$career_border',
  					career_grp_self='$career_grp_self',
  					career_grp_team='$career_grp_team',
  					career_grp_incus='$career_grp_incus',
  					career_grp_boss='$career_grp_boss',
  					career_grp_peer='$career_grp_peer',
  					career_grp_excus='$career_grp_excus',
  					career_grp_topboss = '$career_grp_topboss',
  					career_grp_dirrep='$career_grp_dirrep',
  					career_grp_other='$career_grp_other'";
  	}
  	else
  	{
  	$updqry="update $career_colors set
  					career_bgcolor='$career_bgcolor',
  					career_border='$career_border',
  					career_grp_self='$career_grp_self',
  					career_grp_team='$career_grp_team',
  					career_grp_incus='$career_grp_incus',
  					career_grp_boss='$career_grp_boss',
  					career_grp_peer='$career_grp_peer',
  					career_grp_excus='$career_grp_excus',
  					career_grp_topboss = '$career_grp_topboss',
  					career_grp_dirrep='$career_grp_dirrep',
  					career_grp_other='$career_grp_other' where color_id = '1'";
  	}
  	$db_object->insert($updqry);
}
		
		
} 	//end of function update_options()
	
	
}		//end of class


$obj = new graphOptions;

//$post_var = @array_merge($_POST,$_GET);

if($fSubmit)
{
	$obj->update_options($db_object,$common,$post_var,$user_id,$default);
}

if($user_id==1)
{
$obj->view_graph_options($db_object,$common,$post_var,$user_id,$default,$gbl_grouprater_inter);
}
else
{
echo $error_msg["cNoPermission"];
}

include_once('footer.php');
?>
