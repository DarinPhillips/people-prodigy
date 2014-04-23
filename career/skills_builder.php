<?php
include("../session.php");

//include("header.php");
class skills_builder
{
   function skills_display($common,$db_object,$form_array,$user_id,$alert_msg,$error_msg)
   {
//--------------displays the  option box and prints the rest when selected--------------------


	while(list($kk,$vv)=@each($form_array))
	{
   		$$kk=$vv;
	}
	$filename="../templates/career/skills_builder.html";
	$filecontent=$common->return_file_content($db_object,$filename);
	$tablename=$common->prefix_table("position");
	$assign_tech_skill_builder=$common->prefix_table("assign_tech_skill_builder");
	if($user_id!=1)
	{
	$query="select $tablename.pos_id,$tablename.position_name from $tablename,$assign_tech_skill_builder where $assign_tech_skill_builder.position_id=$tablename.pos_id and $assign_tech_skill_builder.user_id='$user_id' and $assign_tech_skill_builder.status<>'h'";
	
	$positionset=$db_object->get_rsltset($query);
	}
	else
	{
		$query="select pos_id,position_name from $tablename";
		$positionset=$db_object->get_rsltset($query);
	}
	
	if(count($positionset)==0)
	{
		echo $error_msg['cNoSkillAssigned'];include_once("footer.php");exit;
	}
	for($i=0;$i<count($positionset);$i++)
	{
		$in=$positionset[$i]["pos_id"];
		$positionnameset[$in]=$positionset[$i]["position_name"];
	}
	
	$loopstart="<{position_loopstart}>";
	$loopend="<{position_loopend}>";
	$filecontent=$common->singleloop_replace($db_object,$loopstart,$loopend,$filecontent,$positionnameset,$fPosition);
//-----------------after selecting the position displays the corresponding objectives----------------------

		if($fPosition)
		{
			
			$unapprove=$common->prefix_table("unapproved_skill_builder");
			$preqry="select pos_id from $unapprove where emp_id='$user_id'";

			$unposid=$db_object->get_single_column($preqry);
			$mainsklbld=$common->prefix_table("skill_builder");
			$preqry1="select pos_id from $mainsklbld where emp_id='$user_id' and status<>'a'";

			$unposid1=$db_object->get_single_column($preqry1);
			if($user_id!=1)
			{
			
				if(@in_array($fPosition,$unposid) || @in_array($fPosition,$unposid1))
				{	
					
					echo $alert_msg["cAlreadyexists"];
					include("footer.php");
					exit;
				}


			}

		$selqry="select user_id from $assign_tech_skill_builder where position_id='$fPosition'";
		$table_user_id=$db_object->get_single_column($selqry);
		
		  if(@in_array($user_id,$table_user_id)||$user_id==1)
		  {
		  	 				
			$this->context_display($common,$db_object,$form_array,$filecontent,$user_id);
		  }
		  else
		  {
		  	echo $alert_msg["cPosnotassigned"];
		  }
		}
		else
		{	
		$filecontent=preg_replace("/<{replace1_loopbegin}>(.*?)<{replace1_loopexit}>/s","",$filecontent);
		preg_match("/<{replace1_loopbegin}>(.*?)<{replace1_loopexit}>/s",$filecontent,$mat);

		$values=array();
		$filecontent=$common->direct_replace($db_object,$filecontent,$values);
		
		echo $filecontent;
		}
   }

  function context_display($common,$db_object,$form_array,$mainfilecontent,$user_id)
  {


//--------prints  the text boxes  neccessary for the input---------------------

	while(list($kk,$vv)=@each($form_array))
	{
	$$kk=$vv;
	}

	

	$subtable1=$common->prefix_table("position");
	$sqlqry="select * from $subtable1 where pos_id='$fPosition'";
	$positioname=$db_object->get_a_line($sqlqry);
	
	

	$tablename=$common->prefix_table("config");
	$query="select * from $tablename";
	$objectset=$db_object->get_a_line($query);
	$objectives=$objectset["objectives"];
	$challenges=$objectset["challenges"];
	
	

	$sub_skill=$common->prefix_table("temp_skill_builder");
	$sub_obj=$common->prefix_table("temp_objectives");
	$skill_table="select $sub_skill.build_id,$sub_obj.obj_id from $sub_skill,$sub_obj where $sub_skill.pos_id='$fPosition' and $sub_skill.emp_id='$user_id' and $sub_skill.build_id=$sub_obj.build_id order by $sub_obj.obj_id"; 
	$obj_idset=$db_object->get_rsltset($skill_table);

	$maintablename=$common->prefix_table("position");
	preg_match("/<{replace1_loopbegin}>(.*?)<{replace1_loopexit}>/s",$mainfilecontent,$matter);
	$matter=$matter[1];
	$mainfilecontent=preg_replace("/<{replace1_loopbegin}>(.*?)<{replace1_loopexit}>/s","<{objectives}>",$mainfilecontent);
	preg_match("/<{obj_loopbegin}>(.*?)<{obj_loopexit}>/s",$matter,$match);
	$replaced=$match[1];
	$replace="";

	for($i=0; $i<$objectives;$i++)
	{
		if($obj_idset[$i]["obj_id"])
		{
		$obj_id=$obj_idset[$i]["obj_id"];
		}
		else
		{$obj_id=$i+1;}
		$replace.=preg_replace("/{{(.*?)}}/",$obj_id,$replaced);
	}	


	$matter=preg_replace("/<{obj_loopbegin}>(.*?)<{obj_loopexit}>/s",$replace,$matter);
	preg_match("/<{chg_loopbegin}>(.*?)<{chg_loopexit}>/s",$matter,$match1);
	$chgreplaced=$match1[1];
	

	
//	$challenges=1;
	for($i=$objectives;$i<$objectives+$challenges;$i++)
	{
		if($obj_idset[$i]["obj_id"])
		{
		$obj_id=$obj_idset[$i]["obj_id"];
		}
		else
		{$obj_id=$i+1;}
		$chgreplace.=preg_replace("/{{(.*?)}}/",$obj_id,$chgreplaced);
	}
	$matter=preg_replace("/<{chg_loopbegin}>(.*?)<{chg_loopexit}>/s",$chgreplace,$matter);
	$mainfilecontent=preg_replace("/<{objectives}>/",$matter,$mainfilecontent);
	$value["directreplace"]["position_name"]=$positioname["position_name"];
	$value["directreplace"]["pos_id"]=$positioname["pos_id"];
	$mainfilecontent=$common->direct_replace($db_object,$mainfilecontent,$value);
	
	$mainfilecontent=$this->check_retrive($common,$db_object,$form_array,$mainfilecontent,$user_id);

	
	echo $mainfilecontent;
  	
   }

  function update_obj($common,$db_object,$form_array,$user_id,$alert_msg)
  {


	$assign_tech_skill_builder=$common->prefix_table("assign_tech_skill_builder");
	
		
	$filename="../templates/career/skills_builder.html";
	$filecontent=$common->return_file_content($db_object,$filename);
	while(list($kk,$vv)=@each($form_array))
 	{
		$$kk=$vv;
		if(ereg("^objective_",$kk))
		{
			$obj_array["$kk"]=$vv;
		}

	}



	$selqry="select position_id from $assign_tech_skill_builder where user_id='$user_id'";
	$table_pos_id=$db_object->get_rsltset($selqry);

	for($i=0;$i<count($table_pos_id);$i++)
	{
		$table_position[$i]=$table_pos_id[$i]["position_id"];
	}


if((!@in_array($fPos_id,$table_position) ||$fPos_id==0) && $user_id!=1)
{
	echo $alert_msg["cPosnotassigned"];
	include("footer.php");
	exit;
}

	
	$tablename=$common->prefix_table("temp_skill_builder");

	if($fbuild_id)
	{
	$obj_table=$common->prefix_table("temp_objectives");
	$delsql="delete from $obj_table where build_id='$fbuild_id'";
	$selqry="select obj_id from $obj_table where build_id='$fbuild_id' order by obj_id limit 0,1";
	$obj_id=$db_object->get_a_line($selqry);
	$objid=$obj_id["obj_id"];
	$db_object->insert($delsql);
		while(list($kk,$vv)=@each($obj_array))
		{
			
			$objquery="replace into $obj_table set obj_id='$objid',build_id='$fbuild_id',objective_name='$vv'";
			$objid+=1;
			$db_object->insert($objquery);
		}

		$build_id=$fbuild_id;
		
	 }
	else
	{		

		$selqry="select date from $assign_tech_skill_builder where user_id='$user_id'
		and position_id='$fPos_id' and status is null";
		
		$selres=$db_object->get_a_line($selqry);
		
		$date_assigned=$selres[date];
		
		$sqlqry="insert into $tablename set pos_id='$fPos_id',emp_id='$user_id'";
		
		$build_id=$db_object->insert_data_id($sqlqry);
		$objtable=$common->prefix_table("temp_objectives");
		while(list($kk,$vv)=@each($obj_array))
		{
			
			$objquery="insert into $objtable set build_id='$build_id',objective_name='$vv'";
			$db_object->insert($objquery);
		}
			
 	}
	return $build_id;
  }

  function check_retrive($common,$db_object,$form_array,$filecontent,$user_id)
  {

//---------------chechks whether there is already a data for the position--------------
	while(list($kk,$vv)=@each($form_array))
	{
   		$$kk=$vv;
	}

	$skilltable=$common->prefix_table("temp_skill_builder");
	$skqry="select build_id from $skilltable where emp_id='$user_id' and pos_id='$fPosition'";

		$buildid=$db_object->get_a_line($skqry);
		$build_id=$buildid["build_id"];
	if($build_id)
	{

		$objtable=$common->prefix_table("temp_objectives");
		$objqry="select objective_name,obj_id from $objtable where build_id='$build_id' order by obj_id asc";
		$objectiveset=$db_object->get_rsltset($objqry);
		preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$filecontent,$match);
		$objfind=$match[0];
		$ini=1;
		while(list($kk,$vv)=@each($objectiveset))
		{
		$ini=$objectiveset[$kk]["obj_id"];
		$temp="objective_".$ini."_name";
		$objnameset[$temp]=$objectiveset[$kk]["objective_name"];	
		$$kk=$vv;
		$ini=$ini+1;
		$$temp=$objnameset[$temp];
		$temp1=$objnameset[$temp];
			
		}
		$objfind =preg_replace("/<{(.*?)}>/e","$$1",$objfind);
		$objfind=$objfind."<input type=hidden name=fbuild_id value={{build_id}}>";
		$filecontent=preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$objfind,$filecontent);
	//	echo "build_id=$build_id";
		$val["directreplace"]["build_id"]=$build_id;
	
	}
	else
	{
		$filecontent=preg_replace("/<{(.*?)}>/","",$filecontent);
		$val["directreplace"]["build_id"]="";
	}
		$filecontent=$common->direct_replace($db_object,$filecontent,$val);

		return $filecontent;
	
	
   }
}

$skills=new skills_builder;

if($fNext)
{
	
$xId= $skills->update_obj($common,$db_object,$post_var,$user_id,$alert_msg);

header("Location:activities_panel.php?build_id=$xId");
}
include("header.php");
//------------------control also comes from the page PMS/career/alertfortechskillbuild.php
$skills->skills_display($common,$db_object,$post_var,$user_id,$alert_msg,$error_msg);
//$skills->skills_display($common,$db_object,$_GET,$alert_msg);
include("footer.php");
?>
