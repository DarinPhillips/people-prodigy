<?php
include("../session.php");
include("header.php");
class Position_populate
{
	function populate($common,$db_object,$pos_id,$error_msg)
	{
		$path=$common->path;
		$xFile=$path."/templates/core/position_populate.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		
	$position=$common->prefix_table("position");

//-----------------query to select the level nos-------------------------------


	$lvlqry="select position_name,level_no,location from $position where pos_id='$pos_id'";
	$levelno=$db_object->get_a_line($lvlqry);
	$level=$levelno["level_no"];
	$location=$levelno["location"];
	$positionname=$levelno["position_name"];

	$org_main=$common->prefix_table("org_main");
	$qry="select levels,higher_order from $org_main";
	$YRN=$db_object->get_a_line($qry);
	$yrn=$YRN["higher_order"];
	$limitlevel=$YRN["levels"];
if($yrn=="yes")
{
	
	$query1="select distinct(level_no) from $position where level_no<='$level' order by level_no desc";
}
else
{
	$query1="select distinct(level_no) from $position where level_no>='$level' order by level_no asc";
}
	$levelset=$db_object->get_rsltset($query1);


	
//-------------------it works now-------------------------------------------

	
	$query="select position_name,pos_id,level_no from $position order by pos_id,level_no,boss_no";
	$positionset=$db_object->get_rsltset($query);
	
	for($i=0;$i<count($positionset);$i++)
	{
		$in=$positionset[$i]["pos_id"];
		$positionnameset[$in]=$positionset[$i]["position_name"];
	}
	
/*	for($j=0;$j<count($levelset);$j++)
	{
		$in=$levelset[$j]["level_no"];
		$positionlevelset[$in]=$levelset[$j]["level_no"];
	}*/

$positionlevelset=$common->return_levels_belowme($db_object,$pos_id);


	
	$loopstart="<{position_loopstart}>";
	$loopend="<{position_loopend}>";
	//$xTemplate=$common->singleloop_replace($db_object,$loopstart,$loopend,$xTemplate,$positionnameset,$pos_id);

	


	$loopstart="<{level_loopstart}>";
	$loopend="<{level_loopend}>";
	$xTemplate=$common->singleloop_replace($db_object,$loopstart,$loopend,$xTemplate,$positionlevelset,$level);


//------------------prints the location of the position---------------------------------------
/*	$loc_id=0;
	$app="--";
	$match_arr=$location;
//	$tobeprinted=$common->list_category($db_object,$common,$loc_id,$app,$match_arr);
//	$xTemplate=preg_replace("/<{location_loopstart}>(.*?)<{location_loopend}>/s",$tobeprinted,$xTemplate);
*/
	

$locationtobe=$common->return_location_for_display($db_object);
$xTemplate=$common->singleloop_replace($db_object,"location_loopstart","location_loopend",$xTemplate,$locationtobe,$location);
//-----------------------------------------------------------------------------------------------
//------------------displays the text box for --------------new positions--------------------------
	$newqry="select pos_id,position_name,level_no from $position where boss_no='$pos_id'";
	$posresult=$db_object->get_rsltset($newqry);
	
	preg_match("/<{newposition_loop_start}>(.*?)<{newposition_loop_end}>/s",$xTemplate,$match);
	$replace=$match[1];

	preg_match("/<{userlevel_loopstart}>(.*?)<{userlevel_loopend}>/s",$xTemplate,$mt);
	$inrrepla=$mt[1];
	$config=$common->prefix_table("config");
	$qry="select no_at_level from $config";
	$no=$db_object->get_a_line($qry);
	$no=$no[0];
	for($i=0;$i<$no;$i++)
	{
		//$id=$i+1;
		$id=$posresult[$i]["pos_id"];
			if($id=="")
			{
				$k=$i+1;
				$newid="new_".$k;
				$id=$newid;
				
			}
		$cSelect=$error_msg["cSelect"];
		$selval=$posresult[$i]["level_no"];
		$replace1=$common->singleloop_replace($db_object,"<{userlevel_loopstart}>","<{userlevel_loopend}>",$replace,$positionlevelset,$selval);
		$position_name=$posresult[$i]["position_name"];
		$replaced.=preg_replace("/{{(.*?)}}/e","$$1",$replace1);
		$selval="";
	}

//------------------------checks for level end---------------------------------		    	
	$org_main=$common->prefix_table("org_main");
	$qry="select levels,higher_order from $org_main";
	$YRN=$db_object->get_a_line($qry);
	$yrn=$YRN["higher_order"];
	$limitlevel=$YRN["levels"];

	if($yrn=="yes")
	{	if($level==1)
		{	
			$replaced=$error_msg["cLevelexceeds"];
	$xTemplate=preg_replace("/{{cWhatnamesthispos}}/s","",$xTemplate);
		}
	}
	else
	{	if($level==$limitlevel)
		{	
			$replaced=$error_msg["cLevelexceeds"];
	$xTemplate=preg_replace("/{{cWhatnamesthispos}}/s","",$xTemplate);
		}
	}
	
//----------------------------------------------------------------------

	$xTemplate=preg_replace("/<{newposition_loop_start}>(.*?)<{newposition_loop_end}>/s",$replaced,$xTemplate);

	$vals["level"]=$level;
	$vals["position"]=$positionname;
	$vals["pos_id"]=$pos_id;
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	
	echo $xTemplate;
	}
    function update($common,$db_object,$form_array,$error_msg)
    {


$h=0;
$g=0;
	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;
		
		if(ereg("^position_name_",$kk))
		{
			
				$idi=split("position_name_",$kk);
				$id=$idi[1];
				if(ereg("^new_",$id))
				{
					if($vv!="")
					{
						$leveltemp="level_no_".$id;
						$addedposition[$g]=$vv;
						$added_level[$g]=$form_array[$leveltemp];						
						$g++;
					}
				}
				else
				{

					if($vv=="")
					{
						$this->check_for_delete($common,$db_object,$id,$error_msg);
					}
					else
					{
					$newleveltemp="level_no_".$id;	
					$newpositionset[$h]=$vv;
					$newlevelset[$h]=$form_array[$newleveltemp];
					$idset[$h]=$id;
					$h++;}
					
				}
			
		}

		
	}
	$org_main=$common->prefix_table("org_main");
	$qry="select levels,higher_order from $org_main";
	$YRN=$db_object->get_a_line($qry);
	$yrn=$YRN["higher_order"];
	$limitlevel=$YRN["levels"];

//print_r($form_array);
//exit;

	$position=$common->prefix_table("position");
	$qry2="select pos_id,position_name,level_no,boss_no from $position where pos_id='$fPosition'";
	$positionset=$db_object->get_a_line($qry2);
//print_r($positionset);
//exit;
	if($yrn=="yes")
	{
		
		$leveltemp=$positionset["level_no"]-1;
		
		$level=$positionset["level_no"]-1;
	
	}
	else
	{
		$leveltemp=$positionset["level_no"]+1;
		$level=$positionset["level_no"]+1;
	}

	
//-----------location updation for Highere Position--------------------
	$locqry="update $position set location='$fLocation',position_name='$fPosition_name' where pos_id='$fPosition'";
	$db_object->insert($locqry);
//---------------------------------------------------------------------
			
	for($i=0;$i<count($newpositionset);$i++)
	{
		$positionname=$newpositionset[$i];
		$id=$idset[$i];
		$level=$newlevelset[$i];

		if($level==""||$level==0)
		{
			$level=$leveltemp;
		}
			
		//$qry3="replace into $position set pos_id='$id',position_name='$positionname',status='a',date_added=curdate(),level_no='$level',boss_no='$fPosition',location='$fLocation'";
		$qry3="update $position set position_name='$positionname',status='a',level_no='$level',boss_no='$fPosition' where pos_id='$id'";
		$db_object->insert($qry3);
		
	}
	for($j=0;$j<count($addedposition);$j++)
	{
		$positionname=$addedposition[$j];
		$level=$added_level[$j];
	
		if($level==""||$level==0)
		{
			$level=$leveltemp;
		}
		$qry5="insert into $position set position_name='$positionname',status='a',date_added=curdate(),level_no='$level',boss_no='$fPosition',location='$fLocation'";
		$db_object->insert($qry5);		
	}
    }

    function check_for_delete($common,$db_object,$pos_id,$error_msg)
    {
    	$user_table=$common->prefix_table("user_table");
    	$position=$common->prefix_table("position");
    	$chkqry="select pos_id from $position where boss_no='$pos_id'";
    	$exists=$db_object->get_rsltset($chkqry);
    	$cnt=count($exists);
    	$bosqry="select boss_no from $position where pos_id='$pos_id'";
    	$boss_id=$db_object->get_a_line($bosqry);
      	$bossid=$boss_id["boss_no"];


      	$selqry="select user_id from $user_table where position='$pos_id'";
      	$userexists=$db_object->get_a_line($selqry);
    	if($cnt>0)
    	{
    		echo $error_msg["cCantdel"];
    		$this->populate($common,$db_object,$bossid,$error_msg);
    		include("footer.php");
    		exit;
 		    		
    	}
    	else if($userexists["user_id"])
    	{
    		echo $error_msg["cCantdelUser"];
    		$this->populate($common,$db_object,$bossid,$error_msg);
    		include("footer.php");
    		exit;
    	}    	
    	else
    	{
    		$delqry="delete from $position where pos_id='$pos_id'";
    		$db_object->insert($delqry);
    		
    	}
    }
    
}
$posobj=new Position_populate;
if($fUpdate)
{
$posobj->update($common,$db_object,$_POST,$error_msg);
echo "<script language='javascript'>window.location.replace('chart_tree.php')</script>";
exit;
}
$posobj->populate($common,$db_object,$pos_id,$error_msg);

include("footer.php");

?>