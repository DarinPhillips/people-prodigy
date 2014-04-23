<?php
include("../session.php");
include("header.php");
class Display_Skills
{
	function display_skill($common,$db_object,$gbl_date_format)
	{
		$path=$common->path;
		$xFile=$path."templates/career/skills_without_test.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);

		$user_table=$common->prefix_table("user_table");
		$skills=$common->prefix_table("skills");
		$user_tests=$common->prefix_table("user_tests");
		$questions=$common->prefix_table("questions");
		
$selqry="select $skills.skill_id,$skills.skill_name,
date_format($skills.DATE_OF_ADDITION,'$gbl_date_format') as date_added,
$user_table.user_id as added_by,$user_table.email
 from $skills left join $questions on $questions.skill_id=$skills.skill_id
  left join $user_table on $skills.added_by=$user_table.user_id
   where $questions.skill_id is null order by $skills.DATE_OF_ADDITION";
		$skillset=$db_object->get_rsltset($selqry);

for($i=0;$i<count($skillset);$i++)
{
	$temp_id=$skillset[$i]["added_by"];
	$skillset[$i]["added_by"]=$common->name_display($db_object,$temp_id);
	
}

		
		$pattern="/<{skill_loopstart}>(.*?)<{skill_loopend}>/s";
					
		preg_match($pattern,$xTemplate,$match);
		
		$match=$match[0];
		
		for($a=0;$a<count($skillset);$a++)
		{
			$skill_name=$skillset[$a][skill_name];
			
			$date_added=$skillset[$a][date_added];
						
			$added_by=$skillset[$a][added_by];
			
			$email=$skillset[$a][email];
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
	

		//$values["skill_loop"]=$skillset;
		
		//$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$values);
		
		$xTemplate=preg_replace($pattern,$str,$xTemplate);
		
		$xTemplate=$common->direct_replace($db_object,$xTemplate,$valps);
		echo $xTemplate;
	}
}
$skobj= new Display_Skills;
$skobj->display_skill($common,$db_object,$gbl_date_format);

include("footer.php");

?>
