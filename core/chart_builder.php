<?php
include("../session.php");

class Chart_builder
{
 function display_chart_builder($common,$db_object,$form_array,$user_id)
  {
	$path=$common->path;
	$xFile=$path."templates/core/chart_builder.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$config=$common->prefix_table("config");
	$qry="select no_at_level from config";
	$no_at_level=$db_object->get_a_line($qry);
	$no=$no_at_level["no_at_level"];

	$org_main=$common->prefix_table("org_main");
	$simpleqry="select dummyid,levels,higher_order from $org_main";
	$exists=$db_object->get_a_line($simpleqry);
	$exist=$exists["dummyid"];
	$yrn=$exists["higher_order"];
	$no_of_levels=$exists["levels"];



	$position=$common->prefix_table("position");
	$qry="select pos_id,position_name,level_no from $position where boss_no=0";
	$chief=$db_object->get_a_line($qry);
	$chief_name=$chief["position_name"];
	$chief_level=$chief["level_no"];
	$chief_id=$chief["pos_id"];

	if($yrn=="yes")
	{
		$emp_level=$chief_level-1;
		$values["directreplace"]["yeschecked"]="checked";
	}
	else
	{
		$emp_level=$chief_level+1;
		$values["directreplace"]["nochecked"]="checked";
	}
	$qry="select pos_id,position_name from $position where boss_no='$chief_level'";
	$emp_postns=$db_object->get_rsltset($qry);


	
	preg_match("/<{outer_loopstart}>(.*?)<{outer_loopend}>/s",$xTemplate,$match1);
	$replace2=$match1[1];

	$values["directreplace"]["level_no"]=$no_of_levels;
	$values["directreplace"]["chief_name"]=$chief_name;
	$values["directreplace"]["chief_id"]=$chief_id;
	$values["directreplace"]["dummyid"]=$exist;
	

	$replaced1=$common->direct_replace($db_object,$replace2,$values);
	$xTemplate=preg_replace("/<{outer_loopstart}>(.*?)<{outer_loopend}>/s",$replaced1,$xTemplate);
	
	


	preg_match("/<{position_loop_start}>(.*?)<{position_loop_end}>/s",$xTemplate,$match);
	$replace=$match[1];
	




	
	for($i=0;$i<$no;$i++)
	{
		$values["directreplace"]["position_name"]=$emp_postns[$i]["position_name"];
		if($emp_postns[$i]["pos_id"])
		{
			
			$values["directreplace"]["id"]=$emp_postns[$i]["pos_id"];
		}
		else
		{

			$in=$i+1;
			$newid="new_".$in;
			//echo $newid;
			$values["directreplace"]["id"]=$newid;
		}
			$values["directreplace"]["i"]=$i+1;	
		$replace1=$common->direct_replace($db_object,$replace,$values);
			$replaced.=$replace1;
	}
	

	 $xTemplate=preg_replace("/<{position_loop_start}>(.*?)<{position_loop_end}>/s",$replaced,$xTemplate);




//exit;
	 $vals=array();
	 $xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	 
		echo $xTemplate;
   }


 function update_chart($common,$db_object,$form_array,$user_id)
   {
   	
   	$positions=array();
   	$newpositions=array();
   	$i=0;
   	while(list($kk,$vv)=each($form_array))
	{	$$kk=$vv;

		if($vv!="")
		{
			if(ereg("^position_name_",$kk))
			{
				$id=split("name_",$kk);
			        $d=$id[1];
			    
			        
	   // $qry="select pos_id from position where pos_id='$d'";
	    //$reslt=$db_object->get_a_line($qry);
//    			if($rslt["pos_id"]=="")
	             if(ereg("^new_",$d))
    			{
			        $newpositions[$i++]=$vv;
    			}
    			else
    			{
			        $positions[$d]=$vv;
    			}
			}
		}
	}

	if($fChief_id)
	{
	}
	else
	{
		$fChief_id=1;
	}

//---------------------------org_main insertion------------------

	$org_main=$common->prefix_table("org_main");
	

	if($fDummyid)
	{
	$omqry="replace into $org_main set dummyid='$fDummyid',levels='$fLevelCount',higher_order='$fYesorNo'";
	}
	else
   	{
	$omqry="insert into $org_main set levels='$fLevelCount',higher_order='$fYesorNo'";
   	}
	$dummy_id=$db_object->insert_data_id($omqry);
	
//-------------------org_chart insertion---------------------------------------------
	if($fYesorNo=="yes")
	{
		$count=$fLevelCount;
		$position=$common->prefix_table("position");
		$insqry="replace into $position set pos_id='$fChief_id',level_no='$count',position_name='$fTitle',status='a',date_added=curdate(),boss_no='0'";
		$newboss_no=$db_object->insert_data_id($insqry);
		$count--;
		while(list($kk,$vv)=each($positions))
		{
			$title=$vv;
			$insqry="replace into $position set pos_id='$kk',level_no='$count',position_name='$title',status='a',date_added=curdate(),boss_no='$newboss_no'";
			$db_object->insert($insqry);
		}
		while(list($kk,$vv)=each($newpositions))
		{
			$title=$vv;
			$insqry="insert into $position set level_no='$count',position_name='$title',status='a',date_added=curdate(),boss_no='$newboss_no'";
			$db_object->insert($insqry);
		
		}

	}
	else
	{
		$count=1;
		$position=$common->prefix_table("position");
		$insqry="replace into $position set pos_id='$fChief_id',level_no='$count',position_name='$fTitle',status='a',date_added=curdate(),boss_no='0'";
		$newboss_no=$db_object->insert_data_id($insqry);
		$count++;
		while(list($kk,$vv)=each($positions))
		{
			$title=$vv;
			$insqry="replace into $position set pos_id='$kk',level_no='$count',position_name='$title',status='a',date_added=curdate(),boss_no='$newboss_no'";
			$db_object->insert($insqry);
		}

		while(list($kk,$vv)=each($newpositions))
		{
			$title=$vv;
			$insqry="insert into $position set level_no='$count',position_name='$title',status='a',date_added=curdate(),boss_no='$newboss_no'";
			$db_object->insert($insqry);
		
		}
  	
	}

//	exit;
	
   }
  
}
$chartobj=new Chart_builder;
$org_main=$common->prefix_table("org_main");
$simpleqry="select dummyid,levels from $org_main";
$exists=$db_object->get_a_line($simpleqry);
$exist=$exists["dummyid"];
if($exists)
{
$flag_for_step2=1;
//header('Location:position_populate.php');	
}

if($fReturn)
{
	header('Location:front_panel.php');
}
include("header.php");
if($fConfirm)
{
	$chartobj->update_chart($common,$db_object,$_POST,$user_id);
	$flag_for_step2=1;
}
if($flag_for_step2)
{
	echo "<script language='javascript'>window.location.replace('chart_tree.php')</script>";
}
$chartobj->display_chart_builder($common,$db_object,$form_array,$user_id);
include("footer.php");
?>