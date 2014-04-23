<?php
include("../session.php");

class Addposition
{
	function add_position($common,$db_object,$form_array,$error_msg,$pos_id=null)
	{

		$position_table=$common->prefix_table("position");
		$vals["flag"]=$error_msg["cAddtext"];
		while(list($kk,$vv)=@each($form_array))
		{
			$$kk=$vv;
			if(ereg("fDelete_",$kk))
				{
					if($vv!="")
					{
						$position_id=split("_",$kk);
						$pos_id1=$position_id[1];
						$selqry="select pos_id from $position_table where boss_no='$pos_id1'";
						$user_exists=$db_object->get_a_line($selqry);
						if($user_exists["pos_id"]=="")
						{
						$delqry="delete from $position_table where pos_id='$pos_id1'";
						$db_object->insert($delqry);
						echo "<script>window.location.replace('positionsetting.php')</script>";
						}
						else
						{
							echo $error_msg["cCantdel"];
							$pos_id=$pos_id1;
						}
						
					}
				}
				else	if(ereg("^fEdit_",$kk))
				{
					$vals["flag"]=$error_msg["cEdittext"];
					if($vv!="")
					{
						$position_id=split("_",$kk);
						$pos_id=$position_id[1];
					}
				}
		}



	$path=$common->path;
	$xFile=$path."templates/core/addposition.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);

	$location_table=$common->prefix_table("location_table");
	$org_main=$common->prefix_table("org_main");
		
	$selqry="select levels from $org_main";
	$level_set1=$db_object->get_a_line($selqry);

	for($i=1;$i<=$level_set1["levels"];$i++)
	{
		
		$level_set[$i]=$i;
	}

	$field=$common->return_fields($db_object,$position_table);
	$selqry="select $field from $position_table where pos_id='$pos_id'";
	$positionset=$db_object->get_a_line($selqry);


	$locationset=$common->return_location_for_display($db_object);

	$values["level_loop"]=$level_set;
	$loopstart="level_loopstart";
	$loopend="level_loopend";
	if($fLevel)
	{
		$sel_val=$fLevel;
	}
	else
	{
	$sel_val=$positionset["level_no"];
	$user_level=$positionset["level_no"];
	}
	$level_set=$common->return_levels($db_object);
	$xTemplate=$common->singleloop_replace($db_object,$loopstart,$loopend,$xTemplate,$level_set,$sel_val);



	$values["location_loop"]=$locationset;
	$loopstart="location_loopstart";
	$loopend="location_loopend";
	$sel_val1=$positionset["location"];;
	$xTemplate=$common->singleloop_replace($db_object,$loopstart,$loopend,$xTemplate,$locationset,$sel_val1);


	if($fLevel)
	{
	
		preg_match("/<{bossdisplay_loopstart}>(.*?)<{bossdisplay_loopend}>/s",$xTemplate,$mat);
		$replace=$mat[1];
		$xTemplate=preg_replace("/<{bossdisplay_loopstart}>(.*?)<{bossdisplay_loopend}>/s",$replace,$xTemplate);
		$org_main=$common->prefix_table("org_main");
		$selqry="select higher_order from $org_main";
		$highness=$db_object->get_a_line($selqry);
	
	$field=$common->return_fields($db_object,$position_table);
		if($highness["higher_order"]=="yes")
		{
	$selqry="select $field from $position_table where level_no =$fLevel+1";
		}
		else
		{
	$selqry="select $field from $position_table where level_no =$fLevel-1";
		}
		$boss_set=$db_object->get_rsltset($selqry);
		$bossset=$common->return_Keyedarray($boss_set,"pos_id","position_name");
	$loopstart="boss_loopstart";
	$loopend="boss_loopend";

	$xTemplate=$common->singleloop_replace($db_object,$loopstart,$loopend,$xTemplate,$bossset,$sel_val);
		if($prev_position=="")
		{
			$vals["position_val"]=$pos_id;
		}
		else
		{
			$vals["position_val"]=$prev_position;
		}
	$vals["position_name"]=$fPosition;

	}
	else if($pos_id=="")
	{
		$xTemplate=preg_replace("/<{bossdisplay_loopstart}>(.*?)<{bossdisplay_loopend}>/s","",$xTemplate);
	}
	else
	{
		preg_match("/<{bossdisplay_loopstart}>(.*?)<{bossdisplay_loopend}>/s",$xTemplate,$mat);
		$replace=$mat[1];
		$xTemplate=preg_replace("/<{bossdisplay_loopstart}>(.*?)<{bossdisplay_loopend}>/s",$replace,$xTemplate);
	
		$selqry="select higher_order from $org_main";
		$highness=$db_object->get_a_line($selqry);
	
		$field=$common->return_fields($db_object,$position_table);
		if($highness["higher_order"]=="yes")
		{
	$selqry="select $field from $position_table where level_no =$user_level+1";
		}
		else
		{
	$selqry="select $field from $position_table where level_no =$user_level-1";
		}
		$boss_set=$db_object->get_rsltset($selqry);
		$bossset=$common->return_Keyedarray($boss_set,"pos_id","position_name");
	$loopstart="boss_loopstart";
	$loopend="boss_loopend";
	$sel_val=$positionset["boss_no"];
	
	$xTemplate=$common->singleloop_replace($db_object,$loopstart,$loopend,$xTemplate,$bossset,$sel_val);


	$vals["position_val"]=$pos_id;
	$vals["position_name"]=$positionset["position_name"];
	}
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;
	}
	function add_to_database($common,$db_object,$form_array,$user_id)
	{
	
		while(list($kk,$vv)=@each($form_array))
		{
			$$kk=$vv;
		}
		$position_table=$common->prefix_table("position");
		
		if($prev_position)
		{
		$insqry="update $position_table set position_name='$fPosition',level_no='$fLevel',boss_no='$fBoss',location='$fLocation' where pos_id='$prev_position'"; 
		}
		else
		{
		$insqry="insert into $position_table set position_name='$fPosition',level_no='$fLevel',boss_no='$fBoss',location='$fLocation',date_added=now(),added_by='$user_id',status='a'";
		}
		
		$db_object->insert($insqry);
		
	}



}
$addobj= new Addposition;

//--------------------control also comes from position_without_location.php

if($fSubmit)
{
	$addobj->add_to_database($common,$db_object,$post_var,$user_id);
header("Location:positionsetting.php");	
}
else
{
include("header.php");
$addobj->add_position($common,$db_object,$post_var,$error_msg,$pos_id);
}




include("footer.php");
?>
