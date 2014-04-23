<?php
include("../session.php");
include("header.php");
include("../includes/admin.class");
class Skills
{
   function panel_display($common,$db_object,$form_array,$build_id,$user_id)
   {

	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;
	}
	$skillsdisplay="../templates/career/unapproved_skills_display.html";

//----tables to retrive the values

	$displaycontent=$common->return_file_content($db_object,$skillsdisplay);
	$skills_table=$common->prefix_table("unapproved_skills_for_activities");
	$build_table=$common->prefix_table("unapproved_skill_builder");
	$obj_table=$common->prefix_table("unapproved_objectives");
	$act_table=$common->prefix_table("unapproved_activities");
	$skillsqry="select $skills_table.skill_name,$skills_table.skill_description,$skills_table.ski_act_id,$skills_table.act_id,$build_table.build_id from  $skills_table,$build_table,$obj_table,$act_table where $build_table.build_id='$build_id' and $build_table.build_id=$obj_table.build_id and $obj_table.obj_id=$act_table.obj_id and $act_table.act_id=$skills_table.act_id order by $act_table.act_id,$skills_table.ski_act_id ";
	$skills_set=$db_object->get_rsltset($skillsqry);
	
	$skill=$common->prefix_table("skills");
	$query="select skill_name,skill_id from $skill where skill_type='t'";
	$originalskillset=$db_object->get_rsltset($query);


//--------display the option box

	preg_match("/<{display_loopbegin}>(.*?)<{display_loopexit}>/s",$displaycontent,$outermatch);
	$outerloop=$outermatch[0];

	preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$outerloop,$innermatch);
	$innerloop=$innermatch[1];
	
	for($i=0;$i<count($originalskillset);$i++)	
	{
		$skill_title=$originalskillset[$i]["skill_name"];
		$skill_id=$originalskillset[$i]["skill_id"];
		$reinnerloop.=preg_replace("/<{(.*?)}>/e","$$1",$innerloop);
		
	}
	$outerloop=preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$reinnerloop,$outerloop);


	for($i=0;$i<count($skills_set);$i++)
	{
		$skill_name=$skills_set[$i]["skill_name"];
		$skill_desc=$skills_set[$i]["skill_description"];
		$skill_id=$skills_set[$i]["ski_act_id"]."_".$skills_set[$i]["act_id"];
	//	$skill_id=$i+1;
		$reouterloop.=preg_replace("/<{(.*?)}>/e","$$1",$outerloop);
	}

	$displaycontent=preg_replace("/<{display_loopbegin}>(.*?)<{display_loopexit}>/s",$reouterloop,$displaycontent);

	$values["directreplace"]["build_id"]=$build_id;
	$displaycontent=$common->direct_replace($db_object,$displaycontent,$values);
	echo $displaycontent;


    }//---function for display ends

//-------function to update the master table



   function save_panel($common,$db_object,$form_array,$user_id)
   {

	$i=0;
	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;
	
		if(ereg("^skill_name_",$kk))
		{	
			$id1=split("skill_name_",$kk);
			$id=$id1[1];
			$temp=split("_",$id);
			$act_id=$temp[1];
			$skill_id=$temp[0];	
			$skillname[$act_id][$skill_id]=$vv;
		}
		if(ereg("^sameas_",$kk))
		{
			$checkids[$kk]=$vv;		
			if($vv=="yes")
			{
				$k=split("_",$kk);
				$id=$k[1];
				$temp=split("_",$id);
				$act_id=$temp[1];
				$skill_id=$temp[0];
				$skillids[$id]=$k[1];
			}
		}				
		if(ereg("^skill_select_",$kk))
		{
			$k=split("skill_select_",$kk);
			$id=$k[1];
			$temp=split("_",$id);
			$act_id=$temp[1];
			$skill_id=$temp[0];
			$sameas="sameas_".$id;
		//	echo "$sameas <br>";
			$checked=$form_array[$sameas];
		//	echo " checked=$checked <br>";
			if($vv && $checked=="yes")
			{
				$subqry="select skill_name from skills where skill_id='$vv'";
				$value=$db_object->get_a_line($subqry);
				$skillname[$act_id][$skill_id]=$value[0];
			}

			$selectedids[$skill_id]=$vv;
		}
		if(ereg("^skill_desc_",$kk))
		{
			$id1=split("skill_desc_",$kk);
			$id=$id1[1];
			$temp=split("_",$id);
			$act_id=$temp[1];
			$skill_id=$temp[0];
			$skilldesc[$act_id][$skill_id]=$vv;
			
		}		


	}

if($user_id==1)
{
	

//------------------Populates the new values in the unapproved tables and then after approval populate the master table---


//-------------populates the new skill_builder table from the old

	$temp_skl=$common->prefix_table("unapproved_skill_builder");
	$skl_table=$common->prefix_table("skill_builder");
	$upqry="insert into $skl_table (emp_id,pos_id,skill_type) select emp_id,pos_id,skill_type from $temp_skl where build_id='$fbuild_id'";
	$newbuildid=$db_object->insert_data_id($upqry);
	$upqry="update $skl_table set status='a' where build_id='$newbuildid'";
	$db_object->insert($upqry);
	$selqry="select * from $temp_skl where build_id='$fbuild_id'";
	$selres=$db_object->get_a_line($selqry);
	$user=$selres[emp_id];$pos_res=$selres[pos_id];
$upqry1="update assign_tech_skill_builder set status='h' where user_id='$user' and position_id='$pos_res'";
$db_object->insert($upqry1);
$selqry="select emp_id from $skl_table where build_id='$newbuildid'";
$employee_id=$db_object->get_a_line($selqry);


//---------------code for deleteing the old record----------


	
//-------------populates the objectives table from the old
	$temp_obj=$common->prefix_table("unapproved_objectives");
	$obj_table=$common->prefix_table("objectives");
	$upobjqr="insert into $obj_table (build_id,objective_name) select '$newbuildid' as build_id,objective_name from $temp_obj where build_id='$fbuild_id' order by obj_id";
	$newobj_id=$db_object->insert_data_id($upobjqr);	
	
//------------populates the activities table from the old	
	$tempact=$common->prefix_table("unapproved_activities");
	$act_table=$common->prefix_table("activities");
	$upactqr="insert into $act_table (obj_id,activity_name) select $obj_table.obj_id as obj_id,activity_name from $tempact,$temp_obj,$obj_table where $temp_obj.build_id='$fbuild_id'and $tempact.obj_id=$temp_obj.obj_id and $obj_table.objective_name=$temp_obj.objective_name and $obj_table.build_id='$newbuildid' ";
	$newact_id=$db_object->insert_data_id($upactqr);
//echo $upactqr;
//exit;
	
//--------------populates the skills table --------------------
//---------code to insert the selected values of skills----------
	$oldskill=$common->prefix_table("unapproved_activities");
	$newskill=$common->prefix_table("skills_for_activities");
	$temp_skills=$common->prefix_table("unapproved_skills_for_activities");

	$skills=$common->prefix_table("skills");

$temp_employee_id=$employee_id["emp_id"];
	
  while(list($k,$v)=each($skillname))
  {	while(list($kk,$vv)=each($skillname[$k]))
	{
		
	$selqry="select $act_table.act_id from $act_table,$oldskill where $oldskill.act_id='$k'and  $act_table.activity_name=$oldskill.activity_name";
	$actid=$db_object->get_a_line($selqry);
	$act=$actid[0];
	$desc=$skilldesc[$k][$kk];	
	$skillqry="insert into $skills set skill_name='$vv',skill_description='$desc',skill_type='t',date_of_addition=now(),added_by='$temp_employee_id'";

	if($selectedids[$kk]=="")	
	{
	$sklid=$db_object->insert_data_id($skillqry);
	}
	else
	{
		$sklid=$selectedids[$kk];
	}
	$qry="insert into $newskill set skill_id='$sklid',act_id='$act'";
	$db_object->insert($qry);
	
	}
  }



//--------------deleteing the records---------------------------
	$selectqry="select distinct(obj_id) from $temp_obj where build_id='$fbuild_id'";
	$objidset=$db_object->get_rsltset($selectqry);
	while(list($kk,$vv)=each($objidset))
	{
		$obj_id=$objidset[$kk]["obj_id"];
		$qry="select distinct(act_id) from $tempact where obj_id='$obj_id'";
		$activityset=$db_object->get_rsltset($qry);
		while(list($kk,$vv)=each($activityset))
		{
			$act_id=$activityset[$kk]["act_id"];
			$subqry="delete from $temp_skills where act_id='$act_id'";
			$db_object->insert($subqry);
		}
		$delqry="delete from $tempact where obj_id='$obj_id'";
		$db_object->insert($delqry);
	}

//---------------record from the activities table are not deleted ---
	$delqry=" delete from $temp_obj where build_id='$fbuild_id'";
	$db_object->insert($delqry);
	$delqry="delete from $temp_skl where build_id='$fbuild_id'";
	$db_object->insert($delqry);

}
    }
}//--class ends
$skillobj=new Skills;
$admin= new Admin;
if($fSave)
{ 
	if($user_id==1)
	{
		
		$skillobj->save_panel($common,$db_object,$_POST,$user_id);
	}

	echo $alert_msg["cThank"];
	//echo $alert_msg["cMailsent"];
	
	include("footer.php");
	
exit;
	
}

$skillobj->panel_display($common,$db_object,$_POST,$build_id,$user_id);
include("footer.php");
?>
