<?php
include("../session.php");
include("header.php");
class Alert_for_Test_Assign
{
function  display_Alerts($common,$db_object,$user_id,$error_msg)
	{
	$path=$common->path;
	$xFile=$path.="templates/career/alertfortestbuild.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);

	$assign_test_builder=$common->prefix_table("assign_test_builder");
	$skills_table=$common->prefix_table("skills");

$selqry="select $skills_table.skill_name,$skills_table.skill_id,$skills_table.skill_type,$assign_test_builder.skill_id,date_format($assign_test_builder.date,'%m.%d.%Y.%H.%i') from $assign_test_builder,$skills_table where $skills_table.skill_id=$assign_test_builder.skill_id and $assign_test_builder.user_id='$user_id' and $assign_test_builder.group_id is null and status = 'p'";
$details=$db_object->get_rsltset($selqry);




$selqry="select distinct(group_id) from $assign_test_builder where user_id='$user_id' and  group_id is not null and status = 'p'";
$group_set=$db_object->get_single_column($selqry);



$selqry="select $skills_table.skill_name,$assign_test_builder.skill_id,date_format($assign_test_builder.date,'%m.%d.%Y.%H.%i') as date,$assign_test_builder.group_id from $assign_test_builder,$skills_table where $skills_table.skill_id=$assign_test_builder.skill_id and $assign_test_builder.user_id='$user_id' and $assign_test_builder.group_id is not null";
$details2=$db_object->get_rsltset($selqry);





$details3=$this->group_similar($details2,"group_id","skill_name");




preg_match("/<{skill_loopdisplay}>(.*?)<{skill_loopdisplay}>/s",$xTemplate,$mat);
$replace=$mat[1];

for($i=0;$i<count($details);$i++)
{
	$skill_name=$details[$i]["skill_name"];
	$date=$details[$i]["date"];
	$test_type=$details[$i]["skill_type"];
	$test_skill_id = $details[$i]['skill_id'];
	$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
}



$xTemplate=preg_replace("/<{skill_loopdisplay}>(.*?)<{skill_loopdisplay}>/s",$replaced,$xTemplate);


preg_match("/<{interpersonal_loopstart}>(.*?)<{interpersonal_loopend}>/s",$xTemplate,$mat1);
$replace1=$mat1[1];
//echo $replace1;
preg_match("/<{skillname_loopstart}>(.*?)<{skillname_loopend}>/s",$xTemplate,$mat2);
$innerreplace=$mat2[1];



for($j=0;$j<count($group_set);$j++)
{


	$grp_id=$group_set[$j];
	
	
	for($k=0;$k<count($details3[$grp_id])-1;$k++)
	{
				
		
		$skill_name=$details3[$grp_id][$k];
		$test_type="i";
		$innerreplaced.=preg_replace("/<{(.*?)}>/e","$$1",$innerreplace);
	}
	
	
	$date=$details3[$grp_id]["date"];
	
	$replaced2=preg_replace("/<{skillname_loopstart}>(.*?)<{skillname_loopend}>/s",$innerreplaced,$replace1);
	$replaced3.=preg_replace("/<{(.*?)}>/e","$$1",$replaced2);
	$innerreplaced="";
}
if($replaced3=="")
{

	$replaced=$error_msg["cEmptyrecords"];
}
$xTemplate=preg_replace("/<{interpersonal_loopstart}>(.*?)<{interpersonal_loopend}>/s",$replaced3,$xTemplate);

	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;
	}

function group_similar($db_list,$grp_field,$fieldname)
	{
		
	for($i=0;$i<count($db_list);$i++)
		{
		$catname=$db_list[$i][$grp_field];
		$fieldvalue=$db_list[$i][$fieldname];
		$catlist[$catname][]=$fieldvalue;
		$catlist[$catname]["date"]=$db_list[$i]["date"];
		}
		return $catlist;
	}
	
}
$alertobj=new Alert_for_Test_Assign;
$alertobj->display_Alerts($common,$db_object,$user_id,$error_msg);
include("footer.php");
?>