<?php
include("../session.php");
include("header.php");
class Approval
{
  function display($common,$db_object,$emp_id)
  {
	$xFile="../templates/career/approval.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$manualskills=$common->prefix_table("unapproved_skills");

$flds=$common->return_fields($db_object,$manualskills);

//	$mnlqry="select skill_id,skill_name,skill_type,skill_description,unskilled_desc from $manualskills where emp_id='$emp_id'";

	$mnlqry="select $flds from $manualskills where emp_id='$emp_id'";
	$skillset=$db_object->get_rsltset($mnlqry);
//	print_r($skillset);
	preg_match("/<{skill_loop_start}>(.*?)<{skill_loop_end}>/s",$xTemplate,$match);
	$replace=$match[1];
	preg_match("/<{popup_loopstart}>(.*?)<{popup_loopend}>/s",$replace,$mats);
	$inner=$mats[1];
		$skls=$common->prefix_table("skills");

		$subqry="select skill_id,skill_name from $skls";
		$vals=$db_object->get_rsltset($subqry);
//	print_r($vals);
		for($l=0;$l<count($vals);$l++)
		{
			$skill_name=$vals[$l]["skill_name"];
			$skill_id=$vals[$l]["skill_id"];
			$reinner.=preg_replace("/<{(.*?)}>/e","$$1",$inner);

		}

		$replace=preg_replace("/<{popup_loopstart}>(.*?)<{popup_loopend}>/s",$reinner,$replace);
	
	while(list($kk,$vv)=@each($skillset))
	{
		$values["directreplace"]["skill_id"]=$skillset[$kk]["skill_id"];
		$values["directreplace"]["skill_name"]=$skillset[$kk]["skill_name"];
		if($skillset[$kk]["skill_type"]=="t")
		{
			$values["directreplace"]["skill_desc"]=$skillset[$kk]["skill_description"];
			
			$values["directreplace"]["skill_type"]="Technical";
		}
		else
		{
			$values["directreplace"]["skill_desc"]=$skillset[$kk]["unskilled_desc"];
			$values["directreplace"]["skill_type"]="Inter Personal";
		}
		$values["directreplace"]["over_used"]=$skillset[$kk]["over_used"];
		$values["directreplace"]["career_killer"]=$skillset[$kk]["career_killer"];
		$values["directreplace"]["compensator"]=$skillset[$kk]["compensator"];
		$replaced.=$common->direct_replace($db_object,$replace,$values);
//	$replaced.="</table><hr><table>";
		$replaced.="</tr><tr><td colspan=12><hr></td></tr><tr>";
		
		
	}
	
	$xTemplate=preg_replace("/<{skill_loop_start}>(.*?)<{skill_loop_end}>/s",$replaced,$xTemplate);
	$values["directreplace"]["emp_id"]=$emp_id;
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;
	

   }

function approve($common,$db_object,$form_array,$emp_id,$gbl_skill_type,$alert_msg)
{
	extract($form_array);
	while(list($kk,$vv)=@each($form_array))
	{
		
		if(ereg("^approve_",$kk))
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

		if(ereg("^fskill_name_",$kk))
		{
			$id1=split("fskill_name_",$kk);
			$id=$id1[1];
			$approve="approve_".$id;
			$checked=$form_array[$approve];
			if($checked=="yes")
			{
				$skill_set[$id]["skill_name"]=$vv;
				$vv;
			}
		}
		if(ereg("^fskill_desc_",$kk))
		{
			$id1=split("fskill_desc_",$kk);
			$id=$id1[1];
			$approve="approve_".$id;
			$checked=$form_array[$approve];
			$skill_type="fskill_type_".$id;
			if($checked=="yes")
			{
				if($form_array[$skill_type]==$gbl_skill_type["t"])
				{
					$skill_set[$id]["skill_desc"]=$vv;
					$skill_set[$id]["skill_type"]="t";
					$tempvar1="fOverused_".$id;
					$tempvar2="fCareerkiller_".$id;
					$tempvar3="fCompensator_".$id;
					$skill_set[$id]["over_used"]=$$tempvar1;
					$skill_set[$id]["career_killer"]=$$tempvar2;
					$skill_set[$id]["compensator"]=$$tempvar3;
				}
				else
				{
					$skill_set[$id]["skill_desc"]=$vv;
					$skill_set[$id]["skill_type"]="i";
					$tempvar1="fOverused_".$id;
					$tempvar2="fCareerkiller_".$id;
					$tempvar3="fCompensator_".$id;
					$skill_set[$id]["over_used"]=$$tempvar1;
					$skill_set[$id]["career_killer"]=$$tempvar2;
					$skill_set[$id]["compensator"]=$$tempvar3;
				}

			}
		}
		if(ereg("^sameas_",$kk))
		{
			
			$id1=split("sameas_",$kk);
			$id=$id1[1];
			$sameas="sameas_".$id;
			$checkedids[$id]=$id;
			$leaveit[]=$id;
			$tempvar="skill_select_".$id;
			$tobeleft[$id]=$$tempvar;
		}
			

	}
//This gives the all the id  that are checked  in approve
//print_r($checkedids);

//This gievs the all the drop down boxes selects with the ids of the corresponding skill_id added newly
//print_r($tobeleft);

// This gives the all the ids of skills in which the same as check box are clicked
//print_R($leaveit);

//print_r($skill_set);
	


$skills_table=$common->prefix_table("skills");
$unapskill=$common->prefix_table("unapproved_skills");
	while(list($kk,$vv)=@each($skill_set))
	{

		$v=$skill_set[$kk];
		$skname="skill_name";
		$sktp="skill_type";
		$skdes="skill_desc";
		$ovused="over_used";
		$crklr="career_killer";
		$cmptr="compensator";
		if(@!in_array($kk,$leaveit))
		{
			$insqry="insert into $skills_table set skill_name='$v[$skname]',DATE_OF_ADDITION=now(),added_by='$emp_id',over_used='$v[$ovused]',career_killer='$v[$crklr]',compensator='$v[$cmptr]',";
			if($v[$sktp]=="t")
			{

				$subqry="skill_description='$v[$skdes]',unskilled_desc=NULL,skill_type='$v[$sktp]'";
			}
			else
			{
				$subqry="skill_description=NULL,unskilled_desc='$v[$skdes]',skill_type='$v[$sktp]'";
			}
			$insqry=$insqry.$subqry;
		//	echo $insqry;
			$db_object->insert($insqry);
			$insqry="";
			$subqry="";
		}
		
		
	}
	if(count($skillids)>0)
	{
		$skills_id=@implode(",",$skillids);
		$skill_ids="(".$skills_id.")";
		//$delqry="delete from $unapskill where emp_id='$emp_id'";
		$delqry="delete from $unapskill where skill_id in $skill_ids";
		$db_object->insert($delqry);
	}
echo $alert_msg["cApproved"];
	
//print_r($skill_set);

	
}
   
}
$aprobj=new Approval;
if($fApproval)
{
	$aprobj->approve($common,$db_object,$_POST,$emp_id,$gbl_skill_type,$alert_msg);	
}
else
{
	$aprobj->display($common,$db_object,$emp_id);
}

include("footer.php");
?>
