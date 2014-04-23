<?php
include("../session.php");
include("header.php");
include("../includes/admin.class");
class Skills
{
	
//-----This function is just to display the 	skills that are all added
	
   function panel_display($common,$db_object,$form_array,$build_id,$user_id)
   {

	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;
	}
	$skillsdisplay="../templates/career/skills_display.html";
	$displaycontent=$common->return_file_content($db_object,$skillsdisplay);
	$skills_table=$common->prefix_table("temp_skills_for_activities");
	$build_table=$common->prefix_table("temp_skill_builder");
	$obj_table=$common->prefix_table("temp_objectives");
	$act_table=$common->prefix_table("temp_activities");
	$skillsqry="select $skills_table.skill_name,$skills_table.skill_description,$skills_table.ski_act_id,$skills_table.act_id,$build_table.build_id from  $skills_table,$build_table,$obj_table,$act_table where $build_table.build_id='$build_id' and $build_table.build_id=$obj_table.build_id and $obj_table.obj_id=$act_table.obj_id and $act_table.act_id=$skills_table.act_id order by $act_table.act_id,$skills_table.ski_act_id ";
	$skills_set=$db_object->get_rsltset($skillsqry);


	$temp_skill_builder=$common->prefix_table("temp_skill_builder");
	$selqry="select emp_id from $temp_skill_builder where build_id='$build_id'";
	$checkit=$db_object->get_a_line($selqry);
	

	
	
	$skill=$common->prefix_table("skills");
	$query="select skill_name,skill_id from $skill where skill_type='t'";
	$originalskillset=$db_object->get_rsltset($query);




	preg_match("/<{display_loopbegin}>(.*?)<{display_loopexit}>/s",$displaycontent,$outermatch);
	$outerloop=$outermatch[0];
	if($checkit["emp_id"]!=$user_id)
	{
				echo $alert_msg["cPosnotassigned"];
				exit;
	}
		
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
			$checked=$form_array[$sameas];
			if($vv && $checked=="yes")
			{
			$subqry="select skill_name from skills where skill_id='$vv'";
			$value=$db_object->get_a_line($subqry);
			$skillname[$act_id][$skill_id]=$value[0];
			}

			$selectedids[$id]=$vv;
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



//print_r($skill_id);
//exit;
//--------------*******************************************************************************************************************8

//--------------the mistake is in the activity id increment while populating the unapproved_skills_for_activities table;

//*************************************************************************************************************


//------------------Populates the new values in the unapproved tables and then after approval populate the master table---
//-------------populates the new skill_builder table from the old

	$temp_skl=$common->prefix_table("temp_skill_builder");
	$skl_table=$common->prefix_table("unapproved_skill_builder");
	$upqry="insert into $skl_table (emp_id,pos_id,skill_type) select emp_id,pos_id,skill_type from $temp_skl where build_id='$fbuild_id'";
	
	$newbuildid=$db_object->insert_data_id($upqry);
//---------------code for deleteing the old record----------
$selqry="select emp_id from $skl_table where build_id='$newbuildid'";
$employee_id=$db_object->get_a_line($selqry);

	
//-------------populates the objectives table from the old
	$temp_obj=$common->prefix_table("temp_objectives");
	
	$obj_table=$common->prefix_table("unapproved_objectives");
	$upobjqr="insert into $obj_table (build_id,objective_name) select '$newbuildid' as build_id,objective_name from $temp_obj where build_id='$fbuild_id' order by obj_id";
	$newobj_id=$db_object->insert_data_id($upobjqr);	
//--------------------new inseertion-------------------------
	$tempact=$common->prefix_table("temp_activities");
	$act_table=$common->prefix_table("unapproved_activities");
	$objidqry="select obj_id from $obj_table where build_id='$newbuildid' order by obj_id";
	$obj_ids=$db_object->get_single_column($objidqry);


	$mysql="select obj_id from $temp_obj where build_id ='$fbuild_id' order by obj_id ";
	$a_temp=$db_object->get_single_column($mysql);

	$relations=array();
		for($y=0;$y<count($a_temp);$y++)
			{
			$relations[$a_temp[$y]]=$obj_ids[$y];	
			}


$array=array_keys($relations);


for($kk=0;$kk<count($array);$kk++)
{
	$old_id=$array[$kk];

	$new_id=$relations[$old_id];
	
	if($old_id)
	{
		$cond=" and $tempact.obj_id = $old_id";
	}


	$actqry="select $tempact.activity_name,$tempact.obj_id  from
	$tempact,$temp_obj where $temp_obj.build_id='$fbuild_id'and
	$tempact.obj_id=$temp_obj.obj_id";
	
	$limit=" order by  $tempact.obj_id,$tempact.act_id";

	$actqry=$actqry.$cond.$limit;


//	echo $actqry;
	$act_name=$db_object->get_rsltset($actqry);


		for($f=0;$f<count($act_name);$f++)
		{
			$aname=$act_name[$f]["activity_name"];
			$oldid=$act_name[$f]["obj_id"];
			$insqry="insert into $act_table set obj_id='$new_id',activity_name='$aname'";
			$db_object->insert($insqry);
		}
		
}



	
//------------populates the activities table from the old
//echo $upactqr;
//exit;
//--------------populates the skills table --------------------
//---------code to insert the selected values of skills----------
	$oldskill=$common->prefix_table("temp_activities");
	$newskill=$common->prefix_table("unapproved_skills_for_activities");
	$temp_skills=$common->prefix_table("temp_skills_for_activities");


$skill_builder=$common->prefix_table("unapproved_skill_builder");
$newqry="select $act_table.act_id from $act_table,$obj_table,$skill_builder
 where $act_table.obj_id=$obj_table.obj_id and
 $skill_builder.build_id=$obj_table.build_id and
 $skill_builder.build_id='$newbuildid' order by $act_table.act_id";

$act_ids=$db_object->get_single_column($newqry);

//print_r($act_ids);
//print_r($skillname);
if(count($act_ids)!=count($skillname))
{
//---here the bug may appear if the count of the skills exceeds the normal count
//---this may appear because of the redundant insertion of skills
//---But it was replaced 
	echo "enters here ";
//	exit;
}


$s=0;
$temp_employee_id=$employee_id["emp_id"];
while(list($k,$v)=@each($skillname))
{

	$act=$act_ids[$s];

	while(list($kk,$vv)=@each($skillname[$k]))
	{
	$desc=$skilldesc[$k][$kk];
	$qry="insert into $newskill set act_id='$act',skill_name='$vv',skill_description='$desc',date_of_addition=now(),added_by='$temp_employee_id'";
	$db_object->insert($qry);
	}
	$s++;
}
//exit;

$sqlqry="select $act_table.act_id from $act_table,$obj_table,$skl_table where $skl_table.build_id=$obj_table.build_id and $obj_table.obj_id=$act_table.obj_id and $skl_table.build_id='$newbuildid' order by $act_table.act_id limit 0,1";
//$newactid=$db_object->get_a_line($sqlqry);
$act=$newactid[0];
//print_r($newactid);	


/*

//-Usually i dont delete my codings hence this waste coding is existing
while(list($k,$v)=@each($skillname))
{


	if($act)
	{
		$subqr=" and $act_table.act_id>$act";
	}
	$limit=" limit 0,1";

	$selqry="select $act_table.act_id from $act_table,
	$obj_table,$skl_table
	where $skl_table.build_id=$obj_table.build_id and
	$obj_table.obj_id=$act_table.obj_id and
	$skl_table.build_id='$newbuildid'";
	
	$selqry=$selqry.$subqr.$limit;
	$actid=$db_object->get_a_line($selqry);

//	echo "<br> $selqry <br>";
	$act=$actid[0];


	while(list($kk,$vv)=@each($skillname[$k]))
	{
		
	$desc=$skilldesc[$k][$kk];	
	$qry="insert into $newskill set act_id='$act',skill_name='$vv',skill_description='$desc'";
	$db_object->insert($qry);
	if(!$act ||!$vv ||!$desc)
	{
		echo " Here is the";
		exit;
	}
	
//	echo "<br> $qry <br>";
	}
}
*/
//exit;

//--------------deleteing the records---------------------------

	$selectqry="select distinct(obj_id) from $temp_obj where build_id='$fbuild_id'";
	$objidset=$db_object->get_rsltset($selectqry);
	$selqry="select emp_id,pos_id from $temp_skl where build_id='$fbuild_id'";

//------------------------------employee id to delete the remainings from assign_tech_skill_table

	$employeeid=$db_object->get_a_line($selqry);
	$employid=$employeeid["emp_id"];
	$pstnid=$employeeid["pos_id"];
	$assign_tech_skill_builder=$common->prefix_table("assign_tech_skill_builder");
	/*$upqry="update $assign_tech_skill_builder set status='i' where user_id='$employid' and position_id='$pstnid'";
	$db_object->insert($upqry);*/
	$delqry="delete from $assign_tech_skill_builder where user_id='$employid' and position_id='$pstnid'";
	//$db_object->insert($delqry);
//=========================================

	while(list($kk,$vv)=@each($objidset))
	{
		$obj_id=$objidset[$kk]["obj_id"];
		$qry="select distinct(act_id) from $tempact where obj_id='$obj_id'";
		$activityset=$db_object->get_rsltset($qry);
		while(list($kk,$vv)=@each($activityset))
		{
			$act_id=$activityset[$kk]["act_id"];
			$subqry="delete from $temp_skills where act_id='$act_id'";
			$db_object->insert($subqry);
		}
		$delqry="delete from $tempact where obj_id='$obj_id'";
		$db_object->insert($delqry);
	}
			


	$delqry=" delete from $temp_obj where build_id='$fbuild_id'";
	$db_object->insert($delqry);
	
	$delqry="delete from $temp_skl where build_id='$fbuild_id'";
	$db_object->insert($delqry);

	
	
	
    }
}//--class ends
$skillobj=new Skills;
$admin= new Admin;

if($fSave)
{ 
	
	if($user_id!=1)
	{
	$skillobj->save_panel($common,$db_object,$_POST,$user_id);
	$config=$common->prefix_table("config");
	$subqry="select ssubject,smessage from $config";
	$rslt=$db_object->get_a_line($subqry);
	$ssubject=$rslt["ssubject"];
	$smessage=$rslt["smessage"];
	$user=$common->prefix_table("user");
	$subqry2="select username from $user where user_id='$user_id'";
	$user_name=$db_object->get_a_line($subqry2);


	
	$emailqry="select email from $user where user_id=1";
	$email_id=$db_object->get_a_line($emailqry);
	$email=$email_id["email"];
	$to=$email;
	$from=$user_name["email"];
	$values["directreplace"]["username"]=$user_name["username"];
	$values["directreplace"]["url"]=$common->http_path."/index.php";
	$smessage=$common->direct_replace($db_object,$smessage,$values);
	$sent=$common->send_mail($to,$ssubject,$smessage,$from);
		if($sent)
		{
			echo "<br>";
			echo $alert_msg["cMailsent"];
			echo "<br>";
		}
		else
		{
			echo $alert_msg["cFailmail"];
		}

	
	echo "<br>";
	}
	else
	{
//----This function is in the admin class that is in the includes directory
		
//----This function is used only when the admin comes submits the skills		
	$admin->save_panel($common,$db_object,$_POST,$user_id);
	
	}
echo $alert_msg["cThank"];
include("footer.php");
exit;
	
}

$skillobj->panel_display($common,$db_object,$_POST,$build_id,$user_id);
include("footer.php");
?>
