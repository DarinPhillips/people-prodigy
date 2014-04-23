<?php
include("../session.php");
include("header.php");
class Skill_name
{
  function skill_display($common,$db_object,$form_array,$build_id)
  {
	while(list($kk,$vv)=@each($form_array))
	{
	$$kk=$vv;
	}
//print_r($form_array);

	$skill_file="../templates/career/unapproved_skills_name.html";
	$skill_content=$common->return_file_content($db_object,$skill_file);
//-------------------selects the objective and activity

//echo "objid=$fObj_id <br>";
//echo "actid=$fAct_id <br>"; 

		


	$obj_table=$common->prefix_table("unapproved_objectives");
//---------------maintains the loop of objectives & activities-----------------

	if($fObj_id!="" && $fActid_next=="")
	{
		
 		$owhereclause=" and obj_id >'$fObj_id' ";
		$fAct_id="";
	}
	else if($fObj_id!="")
	{
		$owhereclause=" and obj_id='$fObj_id' ";
			
	}
	



	
	$objsqr="select obj_id,objective_name from $obj_table where build_id='$build_id' ";
	$objsqr=$objsqr.$owhereclause."order by obj_id limit 0,2";
	$objset=$db_object->get_rsltset($objsqr);

	$obj_name=$objset[0]["objective_name"];
	$obj_id=$objset[0]["obj_id"];
	$objnext_id=$objset[1]["obj_id"];

	if($fAct_id!="")
	{
		$awhereclause=" and act_id >'$fAct_id'";
	}
	
//--------selects the activities from activities table	



	$act_table=$common->prefix_table("unapproved_activities");
	$actsqr="select act_id,activity_name from $act_table where obj_id='$obj_id'";
	$actsqr=$actsqr.$awhereclause." order by act_id limit 0,2";

//	echo $actsqr;
	$actset=$db_object->get_rsltset($actsqr);
	$actname=$actset[0]["activity_name"];
	$act_id=$actset[0]["act_id"];
	$actid_next=$actset[1]["act_id"];
	


	if($act_id)
	{

/*
//----selects the no of skills------
	$config_table=$common->prefix_table("config");
	$countqry="select skills from config";
	$skillcount=$db_object->get_a_line($countqry);
//-----matches the filecontent-----------------
//$sk_act_id==1;	
	preg_match("/<{name_loopbegin}>(.*?)<{name_loopexit}>/s",$skill_content,$skill_match);
	$skill_replace=$skill_match[0];

	for($i=0;$i<$skillcount[0];$i++)
	{
		if($sk_act_id)
		{
		}
		else
		{
		$skill_id=$i+1;
		$skill_name="";
		}	
		$skill_replaced.=preg_replace("/<{(.*?)}>/e","$$1",$skill_replace);
	}
	$skill_content=preg_replace("/<{name_loopbegin}>(.*?)<{name_loopexit}>/s",$skill_replaced,$skill_content);
*/
	$fAct_id=$fActid_next;
	
	$values["directreplace"]["build_id"]=$build_id;
	$values["directreplace"]["objective_name"]=$obj_name;
	$values["directreplace"]["obj_id"]=$obj_id;
	$values["directreplace"]["objid_next"]=$objid_next;
	$values["directreplace"]["activity_name"]=$actname;
	$values["directreplace"]["act_id"]=$act_id;
	$values["directreplace"]["actid_next"]=$actid_next;
	$skill_content=$common->direct_replace($db_object,$skill_content,$values);
		
	$skill_content=$this->check_retrive($common,$db_object,$build_id,$obj_id,$act_id,$skill_content);


	echo $skill_content;

//--------------file contents replaced with respective variables---------------------
	}
	else
	{

		echo "<script language='javascript'>window.location.replace('unapproved_skills_display.php?build_id=$build_id')</script>";
		exit;
	}



  }


  function check_retrive($common,$db_object,$build_id,$obj_id,$act_id,$skill_content)
  {
	$fbuild_id=$build_id;
	$fObj_id=$obj_id;
	$fAct_id=$act_id;

//echo "act_id=$fAct_id";
	

	$skill_table=$common->prefix_table("unapproved_skills_for_activities");
	$skill_qry="select ski_act_id,skill_name,skill_description,act_id from $skill_table
	where act_id='$fAct_id' order by ski_act_id";
	$skill_set=$db_object->get_rsltset($skill_qry);


//echo $skill_qry;
//print_r($skill_set);

	$refact_id=$skill_set[0]["act_id"];
	
//echo "act_id=$refact_id";


	for($i=0;$i<count($skill_set);$i++)
	{	
		$skill_name1[$i]=$skill_set[$i]["skill_name"];
		$skill_desc1[$i]["description"]=$skill_set[$i]["skill_description"];
		$skill_id1[$i]=$skill_set[$i]["ski_act_id"];
	}
//	echo "skill_ids: <br>";
//print_r($skill_id1);


		
	//----selects the no of skills------
	$config_table=$common->prefix_table("config");
	$countqry="select skills from config";
	$skillcount=$db_object->get_a_line($countqry);
if($skillcount[0]!=count($skill_set))
{
	echo "Here is a <br>";
	print_r($skill_set);
//	exit;
}
	

//-----matches the filecontent-----------------
	
	preg_match("/<{name_loopbegin}>(.*?)<{name_loopexit}>/s",$skill_content,$skill_match);
	$skill_replace=$skill_match[0];

	for($i=0;$i<$skillcount[0];$i++)
	{
		
			$skill_name=$skill_name1[$i];
			$skill_desc=$skill_desc1[$i]["description"];
			$skill_id=$skill_id1[$i];
		
		if($skill_id=="")
		{
			$skill_id="#;#;#_".$i+1;
			$skill_name="";
			$skill_desc="";
			echo $skill_id;
		}

	$skill_replaced.=preg_replace("/<{(.*?)}>/e","$$1",$skill_replace);

	
	}
	$skill_content=preg_replace("/<{name_loopbegin}>(.*?)<{name_loopexit}>/s",$skill_replaced,$skill_content);
	return $skill_content;
	}

  function skill_update($common,$db_object,$form_array)
  {


	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;	
		if(ereg("^fskillname_",$kk))
		{
		 list($not,$id)=split("_",$kk);
		if($not=="#;#;#")
		{
			echo "bugg";
			
		}
		 $skillname[$id]=$vv;
			 
		}
		if(ereg("^fskilldesc_",$kk))
		{
			
		 list($not,$id)=split("_",$kk);
		 $skilldesc[$id]=$vv;
		 }
		
	}





	$skact_table=$common->prefix_table("unapproved_skills_for_activities");
	$act_table=$common->prefix_table("unapproved_activities");
	$check_qry="select $skact_table.ski_act_id,$skact_table.act_id from $skact_table,
	$act_table where
	 $skact_table.act_id='$fAct_id' and
	 $skact_table.act_id=$act_table.act_id and
	 $act_table.obj_id='$fObj_id'
	 order by $skact_table.ski_act_id limit 0,1";
	$ref_skill_ref=$db_object->get_a_line($check_qry);
	//echo "<br>chkqry=$check_qry <br>";
	$ref_ski_act_id=$ref_skill_ref["ski_act_id"];

	if($fAct_id==$ref_skill_ref["act_id"])
	{

		//print_r($skilldesc);
		while(list($kk,$vv)=@each($skilldesc))
		{

			$skactqry="replace into $skact_table set ski_act_id='$ref_ski_act_id',act_id='$fAct_id',skill_name='$skillname[$kk]',skill_description='$skilldesc[$kk]'"; 
			$db_object->insert($skactqry);
			
			
			$incr_qry="select ski_act_id from $skact_table where ski_act_id > '$ref_ski_act_id' limit 0,1";
			
			$refkey=$db_object->get_a_line($incr_qry);
				$ref_ski_act_id=$refkey["ski_act_id"];
			
		}

	}
	else
	{

		while(list($kk,$vv)=@each($skilldesc))
		{
		$skactqry="insert into $skact_table set act_id='$fAct_id',skill_name='$skillname[$kk]',skill_description='$skilldesc[$kk]'"; 
		$db_object->insert($skactqry);
		}

	}





  }	



}
$skobj=new skill_name;

if($fNext)
{
$skobj->skill_update($common,$db_object,$_POST);
$build_id=$fbuild_id;

}
$skobj->skill_display($common,$db_object,$_POST,$build_id);
include("footer.php");
?>	
