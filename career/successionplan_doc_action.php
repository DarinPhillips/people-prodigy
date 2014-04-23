<?php
/*---------------------------------------------
SCRIPT:gaps_at_glance.php
AUTHOR:info@chrisranjana.com	
UPDATED:29th Dec
DESCRIPTION : Succession Deployment Plan: Document Required Actions
---------------------------------------------*/

include("../session.php");
include_once("header.php");

class documentActions
{
function show_documentactions($db_object,$common,$default,$user_id,$error_msg,$post_var)
	{
		
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/successionplan_doc_action.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);

		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
		
		
		$position 		= $common->prefix_table("position");
		$position_designee1 	= $common->prefix_table('position_designee1');
		$position_designee2 	= $common->prefix_table('position_designee2');
		$user_table 		= $common->prefix_table('user_table');
		$skills 		= $common->prefix_table('skills');
		$model_skills 		= $common->prefix_table('model_skills');
		$deployment_plan 	= $common->prefix_table('deployment_plan');
		$approved_devbuilder	= $common->prefix_table('approved_devbuilder');
		
		$mysql = "select position_name from $position where pos_id = '$pid'";
		$pos_arr = $db_object->get_a_line($mysql);
		
		$position_name 		= $pos_arr['position_name'];
		$values['position_name']= $position_name;
		$values['pid'] 		= $pid;
		
//FIRST DESIGNEE FOR THAT POSITION...
		//$mysql = "select designated_user from $position_designee1 where position = $pid";
		$mysql = "select designated_user as designee1 
				from $position_designee1,$deployment_plan 
				where $position_designee1.plan_id = $deployment_plan.plan_id 
				and $deployment_plan.position='$pid'";

		$firstdes_arr = $db_object->get_a_line($mysql);
		
		$firstdesignee_user = $firstdes_arr['designee1'];
		$mysql = "select username from $user_table where user_id = '$firstdesignee_user'";
		$username_arr = $db_object->get_a_line($mysql);
		$firstdesignee_username = $username_arr['username'];
		
//SECOND DESIGNEE FOR THAT POSITION...
		
	$mysql = "select designated_user as designee2 
			from $position_designee2,$deployment_plan 
			where $position_designee2.plan_id = $deployment_plan.plan_id 
			and $deployment_plan.position='$pid'"; 

		$seconddes_arr = $db_object->get_a_line($mysql);
		
		$seconddesignee_user = $seconddes_arr['designee2'];
		$mysql = "select username from $user_table where user_id = '$seconddesignee_user'";
		$username_arr = $db_object->get_a_line($mysql);
		$seconddesignee_username = $username_arr['username'];

//CHECK IF THE FIRST DESIGNEE AND THE SECOND DESIGNEE SELECTED ARE EMPLOYEES 
//OR EXTERNAL CANDIDATES AND SHOW THE RELEVENT BOXES
		$mysql = "select user_type from $user_table where user_id = '$firstdesignee_user'";
		$type_arr = $db_object->get_a_line($mysql);
		$firstdes_type = $type_arr['user_type'];
		
		if($firstdes_type == 'employee')
		{
		$returncontent = preg_replace("/<{firstdesignee_employee_(.*?)}>/s","",$returncontent);
		$returncontent = preg_replace("/<{firstdesignee_external_start}>(.*?)<{firstdesignee_external_end}>/s","",$returncontent);
		}
		elseif($firstdes_type == 'external')
		{
		$returncontent = preg_replace("/<{firstdesignee_employee_start}>(.*?)<{firstdesignee_employee_end}>/s","",$returncontent);
		$returncontent = preg_replace("/<{firstdesignee_external_(.*?)}>/s","",$returncontent);
		}
		else
		{
		$returncontent = preg_replace("/<{firstdesignee_external_start}>(.*?)<{firstdesignee_external_end}>/s","",$returncontent);
		$returncontent = preg_replace("/<{firstdesignee_employee_start}>(.*?)<{firstdesignee_employee_end}>/s","",$returncontent);
		}		
		
		
		$mysql = "select user_type from $user_table where user_id = '$seconddesignee_user'";
		$type_arr = $db_object->get_a_line($mysql);
		$seconddes_type = $type_arr['user_type'];

		if($seconddes_type == 'employee')
		{
		$returncontent = preg_replace("/<{seconddesignee_employee_(.*?)}>/s","",$returncontent);
		$returncontent = preg_replace("/<{seconddesignee_external_start}>(.*?)<{seconddesignee_external_end}>/s","",$returncontent);
		}
		elseif($seconddes_type == 'external')
		{
		$returncontent = preg_replace("/<{seconddesignee_employee_start}>(.*?)<{seconddesignee_employee_end}>/s","",$returncontent);
		$returncontent = preg_replace("/<{seconddesignee_external_(.*?)}>/s","",$returncontent);
		}
		else
		{
		$returncontent = preg_replace("/<{seconddesignee_external_start}>(.*?)<{seconddesignee_external_end}>/s","",$returncontent);
		$returncontent = preg_replace("/<{seconddesignee_employee_start}>(.*?)<{seconddesignee_employee_end}>/s","",$returncontent);
		}

	

		$values['firstdesignee_username'] = $firstdesignee_username;
		$values['seconddesignee_username'] = $seconddesignee_username;

		//echo "firstdesignee user $firstdesignee_user";
		//echo "modelid $modelid";

		$inter_gaps_arr = $common->gaps_at_a_glance($db_object,$firstdesignee_user,$modelid);
		$tech_gaps_arr = $common->gaps_at_a_glance_technical($db_object,$firstdesignee_user,$modelid);


		
//FIRST DESIGNEE....
		preg_match("/<{largestgaps_loopstart}>(.*?)<{largestgaps_loopend}>/s",$returncontent,$largest_old);
		$largest_new = $largest_old[1];
			
		preg_match("/<{largestgapstech_loopstart}>(.*?)<{largestgapstech_loopend}>/s",$returncontent,$largesttech_old);
		$largesttech_new = $largesttech_old[1];
		
		preg_match("/<{othergaps_loopstart}>(.*?)<{othergaps_loopend}>/s",$returncontent,$othergap_old);
		$othergap_new = $othergap_old[1];
		
		preg_match("/<{othergapstech_loopstart}>(.*?)<{othergapstech_loopend}>/s",$returncontent,$othergaptech_old);
		$othergaptech_new = $othergaptech_old[1];
		
//CHECK IF THE SKILLS SHOWN IS IN THE PERSONS' LEARNING PLAN...
		$mod_l = md5("learning");
		
		
		$check=$common->is_module_purchased_check($db_object,$xPath,$mod_l);
		
//LARGEST GAPS...

		for($i=0;$i<count($inter_gaps_arr[1]);$i++)
		{
		
			$largestgaps_id = $inter_gaps_arr[1][$i];
			$mysql = "select skill_name from $skills where skill_id = '$largestgaps_id'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$largestgap_skillname = $skillname_arr['skill_name'];
			
//IF THE PERSON HAS THE SKILL IN HIS LEARNING PLAN, THEY ARE SELECTED...
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$firstdesignee_user' and skill_id = '$largestgaps_id'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($largestgaps_id == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee1.skill_id from position_designee1,skills_designee1 
				where position_designee1.plan_id = skills_designee1.plan_id
				and designated_user = '$firstdesignee_user'
				and skill_id = '$largestgaps_id'";
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}

			$str .= preg_replace("/<{(.*?)}>/e","$$1",$largest_new);
			
		}
		
		$returncontent = preg_replace("/<{largestgaps_loopstart}>(.*?)<{largestgaps_loopend}>/s",$str,$returncontent);		

		for($i=0;$i<count($tech_gaps_arr[1]);$i++)
		{
			$largestgapstech_id = $tech_gaps_arr[1][$i];
			$mysql = "select skill_name from $skills where skill_id = '$largestgapstech_id'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$largestgaptech_skillname = $skillname_arr['skill_name'];
	
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$firstdesignee_user' and skill_id = '$largestgapstech_id'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($largestgapstech_id == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee1.skill_id from position_designee1,skills_designee1 
				where position_designee1.plan_id = skills_designee1.plan_id
				and designated_user = '$firstdesignee_user'
				and skill_id = '$largestgapstech_id'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}



			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$largesttech_new);
			
		}

		$returncontent = preg_replace("/<{largestgapstech_loopstart}>(.*?)<{largestgapstech_loopend}>/s",$str1,$returncontent);		
		
//OTHER GAPS...
		
		for($j=0;$j<count($inter_gaps_arr[2]);$j++)
		{
			$othergap_skillid = $inter_gaps_arr[2][$j];
			$mysql = "select skill_name from $skills where skill_id = '$othergap_skillid'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$othergap_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$firstdesignee_user' and skill_id = '$othergap_skillid'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($othergap_skillid == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee1.skill_id from position_designee1,skills_designee1 
				where position_designee1.plan_id = skills_designee1.plan_id
				and designated_user = '$firstdesignee_user'
				and skill_id = '$othergap_skillid'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}

			$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$othergap_new);
			
		}
		
		$returncontent = preg_replace("/<{othergaps_loopstart}>(.*?)<{othergaps_loopend}>/s",$str2,$returncontent);		

		
		for($j=0;$j<count($tech_gaps_arr[2]);$j++)
		{
			$othergaptech_skillid = $tech_gaps_arr[2][$j];
			$mysql = "select skill_name from $skills where skill_id = '$othergaptech_skillid'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$othergaptech_skillname = $skillname_arr['skill_name'];
				
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$firstdesignee_user' and skill_id = '$othergaptech_skillid'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($othergaptech_skillid == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}
			
//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee1.skill_id from position_designee1,skills_designee1 
				where position_designee1.plan_id = skills_designee1.plan_id
				and designated_user = '$firstdesignee_user'
				and skill_id = '$othergaptech_skillid'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}

			$str3 .= preg_replace("/<{(.*?)}>/e","$$1",$othergaptech_new);
			
		}

		$returncontent = preg_replace("/<{othergapstech_loopstart}>(.*?)<{othergapstech_loopend}>/s",$str3,$returncontent);		
		
//GAPS FOR SECOND DESIGNEE...
//LARGEST GAPS...	

		$inter_gaps2_arr = $common->gaps_at_a_glance($db_object,$seconddesignee_user,$modelid);
		$tech_gaps2_arr = $common->gaps_at_a_glance_technical($db_object,$seconddesignee_user,$modelid);
	

		preg_match("/<{largestgaps2_loopstart}>(.*?)<{largestgaps2_loopend}>/s",$returncontent,$largestgap2_old);
		$largestgap2_new = $largestgap2_old[1];
		
		preg_match("/<{largestgapstech2_loopstart}>(.*?)<{largestgapstech2_loopend}>/s",$returncontent,$largestgaptech2_old);
		$largestgaptech2_new = $largestgaptech2_old[1];
		




		for($i=0;$i<count($inter_gaps2_arr[1]);$i++)
		{
			$largestgaps2_id = $inter_gaps2_arr[1][$i];
			$mysql = "select skill_name from $skills where skill_id = '$largestgaps2_id'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$largestgap2_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$seconddesignee_user' and skill_id = '$largestgaps2_id'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($largestgaps2_id == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee2.skill_id from position_designee2,skills_designee2 
				where position_designee2.plan_id = skills_designee2.plan_id
				and designated_user = '$seconddesignee_user'
				and skill_id = '$largestgaps2_id'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}

			
			$str4 .= preg_replace("/<{(.*?)}>/e","$$1",$largestgap2_new);
			
		}
		
		$returncontent = preg_replace("/<{largestgaps2_loopstart}>(.*?)<{largestgaps2_loopend}>/s",$str4,$returncontent);


		for($i=0;$i<count($tech_gaps2_arr[1]);$i++)
		{
			$largestgapstech2_id = $tech_gaps2_arr[1][$i];
			$mysql = "select skill_name from $skills where skill_id = '$largestgapstech2_id'";
			$skillname_arr = $db_object->get_a_line($mysql);
			$largestgaptech2_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$seconddesignee_user' and skill_id = '$largestgapstech2_id'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($largestgapstech2_id == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee2.skill_id from position_designee2,skills_designee2 
				where position_designee2.plan_id = skills_designee2.plan_id
				and designated_user = '$seconddesignee_user'
				and skill_id = '$largestgapstech2_id'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}

			$str5 .= preg_replace("/<{(.*?)}>/e","$$1",$largestgaptech2_new);
			
		}

		$returncontent = preg_replace("/<{largestgapstech2_loopstart}>(.*?)<{largestgapstech2_loopend}>/s",$str5,$returncontent);
		
//OTHER GAPS...


		preg_match("/<{othergaps2_loopstart}>(.*?)<{othergaps2_loopend}>/s",$returncontent,$othergap2_old);
		$othergap2_new = $othergap2_old[1];
	
		preg_match("/<{othergapstech2_loopstart}>(.*?)<{othergapstech2_loopend}>/s",$returncontent,$othergaptech2_old);
		$othergaptech2_new = $othergaptech2_old[1];

		for($j=0;$j<count($inter_gaps2_arr[2]);$j++)
		{
			$othergap2_skillid = $inter_gaps2_arr[2][$j];
			$mysql = "select skill_name from $skills where skill_id = '$othergap2_skillid'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$othergap2_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$seconddesignee_user' and skill_id = '$othergap2_skillid'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($othergap2_skillid == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee2.skill_id from position_designee2,skills_designee2 
				where position_designee2.plan_id = skills_designee2.plan_id
				and designated_user = '$seconddesignee_user'
				and skill_id = '$othergap2_skillid'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}

			

			$str6 .= preg_replace("/<{(.*?)}>/e","$$1",$othergap2_new);
			
		}
		
		$returncontent = preg_replace("/<{othergaps2_loopstart}>(.*?)<{othergaps2_loopend}>/s",$str6,$returncontent);		

		
		for($j=0;$j<count($tech_gaps2_arr[2]);$j++)
		{
			$othergaptech2_skillid = $tech_gaps2_arr[2][$j];
			$mysql = "select skill_name from $skills where skill_id = '$othergaptech2_skillid'";	
			$skillname_arr = $db_object->get_a_line($mysql);
			$othergaptech2_skillname = $skillname_arr['skill_name'];
			
			if($check)
			{
			$mysql = "select distinct(skill_id) from $approved_devbuilder where user_id = '$seconddesignee_user' and skill_id = '$othergaptech2_skillid'";
			$skillpresent_arr = $db_object->get_a_line($mysql);		
			$skillpresent = $skillpresent_arr['skill_id'];
			if($othergaptech2_skillid == $skillpresent)
				{
					$checkboxsel = "checked";
				}
			else
				{
					$checkboxsel = "";	
				}
			}

//IF ANOTHER BOSS OR ADMIN HAS ALREADY ASSIGNED SOME SKILLS FOR THIS POSITION...
			$mysql = "select skills_designee2.skill_id from position_designee2,skills_designee2 
				where position_designee2.plan_id = skills_designee2.plan_id
				and designated_user = '$seconddesignee_user'
				and skill_id = '$othergaptech2_skillid'";
			
			$skills_in_plan_arr = $db_object->get_a_line($mysql);
			$skills_in_plan = $skills_in_plan_arr['skill_id'];
			if($skills_in_plan != '')
			{
				$checkboxsel = "checked";
			}
			else
			{
				$checkboxsel = "";	
			}

			$str7 .= preg_replace("/<{(.*?)}>/e","$$1",$othergaptech2_new);
			
		}

		$returncontent = preg_replace("/<{othergapstech2_loopstart}>(.*?)<{othergapstech2_loopend}>/s",$str7,$returncontent);		
	

//IF AN EXTERNAL HIRE WAS SELECTED ....
	
//approved_devbuilder is the table where the skills in the learning plan can be taken...
	

//HIGH PERFORMERS SKILLS DISPLAYED FOR EXTERNAL CANDIDATES (SAME SCRIPT USED TO DISPLAY 
	//BOTH FIRST DESIGNEE AND SECOND DESIGNEE)...

	$mysql = "select $model_skills.skill_id,skill_name from $skills,$model_skills
			where $skills.skill_id = $model_skills.skill_id
			and model_id = '$modelid' and skill_type = 'i'";
	$modelskills_arr = $db_object->get_rsltset($mysql);
	

	$mysql = "select $model_skills.skill_id,skill_name from $skills,$model_skills
			where $skills.skill_id = $model_skills.skill_id
			and model_id = '$modelid' and skill_type = 't'";
	$modelskillstech_arr = $db_object->get_rsltset($mysql);
	
	$multipleloopvalues['highperformerskills1_loop'] 	= $modelskills_arr;
	$multipleloopvalues['highperformerskills1tech_loop'] 	= $modelskillstech_arr;

 	$multipleloopvalues['highperformerskills2_loop'] 	= $modelskills_arr;
	$multipleloopvalues['highperformerskills2tech_loop'] 	= $modelskillstech_arr;


		$returncontent	= $common->multipleloop_replace($db_object,$returncontent,$multipleloopvalues,'');
		$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
		
	}
function save_contents($db_object,$common,$default,$user_id,$error_msg,$post_var)
	{
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		
		if(ereg("^skills_designee1_",$kk))
			{
			$sid = ereg_replace("skills_designee1_","",$kk);
			$designee1skills[] = $sid;
			}
		if(ereg("^skills_designee2_",$kk))
			{
			$sid = ereg_replace("skills_designee2_","",$kk);
			$designee2skills[] = $sid;
			}
		
		}
		
		$deployment_plan 	= $common->prefix_table('deployment_plan');
		$skills_designee1 	= $common->prefix_table('skills_designee1');
		$skills_designee2 	= $common->prefix_table('skills_designee2');
		$assign_succession_plan_sub = $common->prefix_table('assign_succession_plan_sub');
		$update_plan=$common->prefix_table("update_plan");
		

//AFTER UPDATING THE POSITION'S SUCCESSION PLAN, CHANGE STATUS IN ASSIGN TABLE TO COMPLETED...
		$mysql = "update $assign_succession_plan_sub set status = 'y' where assigned_to = '$user_id' and position = '$fPosid'";

		//$mysql="insert into $update_plan set position='$fPosid',user_id='$user_id',date=now(),status='h'";

		$db_object->insert($mysql);


		$mysql = "select plan_id from $deployment_plan where position = '$fPosid'";
		$planid_arr = $db_object->get_a_line($mysql);
		$plan_id = $planid_arr['plan_id'];
		
		$mysql = "select $skills_designee1.plan_id 
				from $deployment_plan,$skills_designee1
				where $skills_designee1.plan_id = $deployment_plan.plan_id
				and $deployment_plan.position = '$fPosid'";
			$checkplan_arr = $db_object->get_a_line($mysql);
			$checkplan_id = $checkplan_arr['plan_id'];
			
			if($checkplan_id != '')
			{
//DELETE THE PREVIOUS CONTENTS OF THE EXISTING PLAN...
			$mysql = "delete from $skills_designee1 where plan_id = '$plan_id'";
			$db_object->insert($mysql);
//DELETE THE TEXT IF PRESENT...
			$mysql = "update $deployment_plan set designee1_text = '' where plan_id = '$plan_id'";			
			$db_object->insert($mysql);

			}		


		for($i=0;$i<count($designee1skills);$i++)
		{
			$skill_req = $designee1skills[$i];
			
			
			
			$mysql = "insert into $skills_designee1 set plan_id = '$plan_id' ,
					skill_id = '$skill_req'";
			$db_object->insert($mysql);
			
			
			
		}
		
		$mysql = "select $skills_designee2.plan_id 
				from $deployment_plan,$skills_designee2
				where $skills_designee2.plan_id = $deployment_plan.plan_id
				and $deployment_plan.position = '$fPosid'";
			$checkplan2_arr = $db_object->get_a_line($mysql);
			$checkplan2_id = $checkplan2_arr['plan_id'];

			if($checkplan2_id != '')
			{
			$mysql = "delete from $skills_designee2 where plan_id = '$plan_id'";
			$db_object->insert($mysql);
//DELETE THE TEXT IF PRESENT...
			$mysql = "update $deployment_plan set designee2_text = '' where plan_id = '$plan_id'";			
			$db_object->insert($mysql);
		
			}


		for($j=0;$j<count($designee2skills);$j++)
		{
			$skill_req2 = $designee2skills[$j];
			
			
		 
			$mysql = "insert into $skills_designee2 set plan_id = '$plan_id' ,
					skill_id = '$skill_req2'";
			$db_object->insert($mysql);
			 


		}
		if($actionplan_designee1)
		{
			$mysql = "update $deployment_plan set designee1_text = '$actionplan_designee1'
					where plan_id = '$plan_id'";
			$db_object->insert($mysql);
		}	
		if($actionplan_designee2)
		{
			$mysql = "update $deployment_plan set designee2_text = '$actionplan_designee2'
					where plan_id = '$plan_id'";
			$db_object->insert($mysql);
		}
	

		



		
	}
function mail_to_admin($db_object,$common,$default,$user_id,$post_var)
	{
		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
		
		$config			= $common->prefix_table("config");
		$user_table 		= $common->prefix_table('user_table');
		$position 		= $common->prefix_table('position');
		$skills			= $common->prefix_table('skills');
		$deployment_plan	= $common->prefix_table('deployment_plan');
		$position_designee1 	= $common->prefix_table('position_designee1');
		$position_designee2 	= $common->prefix_table('position_designee2');
		$skills_designee1 	= $common->prefix_table('skills_designee1');
		$skills_designee2 	= $common->prefix_table('skills_designee2');
		
//IF ANY OF THE USERS ARE EXTERNAL, THEN MAILS GO TO THE ADMIN OF THAT PERSON CURRENTLY IN THAT POSITION.
		
		$mysql="select succession_update_subject,succession_update_message from $config";
		$rslt_arr=$db_object->get_a_line($mysql);
		
		$succession_update_subject = $rslt_arr["succession_update_subject"];
		$succession_update_message = $rslt_arr["succession_update_message"];
				
//DETERMINE THE RECIEVER (ADMIN FOR THAT POSITION)		
		
		$mysql = "select user_id from $user_table where position = '$fPosid'";
		$userid_arr = $db_object->get_a_line($mysql);
		$user_id_of_pos = $userid_arr['user_id'];
		
		$mysql = "select position_name from $position where pos_id = '$fPosid'";
		$pos_arr = $db_object->get_a_line($mysql);
		$posname = $pos_arr['position_name'];
		$values["directreplace"]["posname"] = $posname;
//User to whom the mail is to be sent...

		$mysql = "select admin_id from $user_table where user_id = '$user_id_of_pos'";
		$admin_id_arr = $db_object->get_a_line($mysql);
		$admin_id = $admin_id_arr['admin_id'];

//IF THE SELECTED PERSON IS EMPLOYEE THEN THOSE EMPLOYEES ARE SENT MAIL TO IMPROVE
//THEIR SKILLS AND DEVELOP A LEARNING PLAN FOR THOSE SKILLS...

//FIRST DESIGNEE...	
		$mysql = "select plan_id from $deployment_plan where position = '$fPosid'";
		$planarr = $db_object->get_a_line($mysql);
		$plan_id_emp = $planarr['plan_id'];
		
		$mysql = "select designated_user from $position_designee1 where plan_id = '$plan_id_emp'";
		$designateduser1_arr = $db_object->get_a_line($mysql);
		$designateduser1 = $designateduser1_arr['designated_user'];
			
		$mysql = "select user_type from $user_table where user_id = '$designateduser1'";
		$designateduser1_type_arr = $db_object->get_a_line($mysql);
		$designateduser1_type = $designateduser1_type_arr['user_type'];
			
//SECOND DESIGNEE...
		$mysql = "select designated_user from $position_designee2 where plan_id = '$plan_id_emp'";		
		$designateduser2_arr = $db_object->get_a_line($mysql);
		$designateduser2 = $designateduser2_arr['designated_user'];
		
		$mysql = "select user_type from $user_table where user_id = '$designateduser2'";
		$designateduser2_type_arr = $db_object->get_a_line($mysql);
		$designateduser2_type = $designateduser2_type_arr['user_type'];
		
			
		if($designateduser1_type == 'employee')
			{
				$mysql = "select skill_id from $skills_designee1 where plan_id = '$plan_id_emp'";
				$skills_to_improve_arr = $db_object->get_single_column($mysql);
				
				$skillid_improve = @implode("','",$skills_to_improve_arr);
				if($skillid_improve != '')
				{	
				$mysql = "select skill_name from $skills where skill_id in ('$skillid_improve')";
				$skillimpname_arr = $db_object->get_single_column($mysql);
				}
				$allskills_listed = @implode(",",$skillimpname_arr);

				$val['allskills_listed'] = $allskills_listed;
//FROM
			$mysql = "select email from $user_table where user_id = '$designateduser1'";
			$email_arr = $db_object->get_a_line($mysql);
			$to_user_email = $email_arr['email'];
//TO			
			$mysql = "select email from $user_table where user_id = '$user_id'";
			$fromemail_arr = $db_object->get_a_line($mysql);
			$from_user_email = $fromemail_arr['email'];
//SUBJECT & MESSAGE			
			$mysql = "select succession_skillupdate_subject,succession_skillupdate_message from $config";	
			$employee_sub_message_arr = $db_object->get_a_line($mysql);
//posname avaiable...
			$subject = $employee_sub_message_arr['succession_skillupdate_subject'];
			$message = $employee_sub_message_arr['succession_skillupdate_message'];

			$val['position_name'] = $posname;

			$message = $common->direct_replace($db_object,$message,$val);
				if($skillid_improve != '')
				{
			$sent=$common->send_mail($to_user_email,$subject,$message,$from_user_email);				
				}
			}


//IF THE DESIGNATED USER 2 IS AN EMPLOYEE....


		if($designateduser2_type == 'employee')
			{
				$mysql = "select skill_id from $skills_designee2 where plan_id = '$plan_id_emp'";
				$skills_to_improve_arr = $db_object->get_single_column($mysql);
				
				$skillid_improve2 = @implode("','",$skills_to_improve_arr);
				if($skillid_improve2 != '')
				{	
				$mysql = "select skill_name from $skills where skill_id in ('$skillid_improve2')";
				$skillimpname_arr = $db_object->get_single_column($mysql);
				}
				$allskills_listed = @implode(",",$skillimpname_arr);

				$val2['allskills_listed'] = $allskills_listed;
//FROM
			$mysql = "select email from $user_table where user_id = '$designateduser2'";
			$email_arr = $db_object->get_a_line($mysql);
			$to_user_email = $email_arr['email'];
//TO			
			$mysql = "select email from $user_table where user_id = '$user_id'";
			$fromemail_arr = $db_object->get_a_line($mysql);
			$from_user_email = $fromemail_arr['email'];
//SUBJECT & MESSAGE			
			$mysql = "select succession_skillupdate_subject,succession_skillupdate_message from $config";	
			$employee_sub_message_arr = $db_object->get_a_line($mysql);
//posname avaiable...
			$subject = $employee_sub_message_arr['succession_skillupdate_subject'];
			$message = $employee_sub_message_arr['succession_skillupdate_message'];

			$val2['position_name'] = $posname;

			$message = $common->direct_replace($db_object,$message,$val2);
			if($skillid_improve2 != '')
				{	
			$sent=$common->send_mail($to_user_email,$subject,$message,$from_user_email);				
				}
			}









//THE MAIL IS SENT ONLY IF THERE IS AN ADMIN FOR THAT POSITION 
//(PROVIDED AN EXTERNAL HIRE WAS CHOSEN)...

		if($admin_id != 0 && $admin_id != '')
		{
			$mysql = "select email,username from $user_table where user_id ='$admin_id'";
			$email_arr = $db_object->get_a_line($mysql);
			$to_email  = $email_arr['email'];	
			$to_username  = $email_arr['username'];	

			$mysql = "select email,username from $user_table where user_id = '$user_id'";
			$fromemail_arr = $db_object->get_a_line($mysql);
			$from_email = $fromemail_arr['email'];
			$from_username = $fromemail_arr['username'];
	//SOURCE SELECT PLAN 
			$mysql = "select designee1_text,designee2_text from $deployment_plan 
					where position = '$fPosid'";
			$designeetext_arr = $db_object->get_a_line($mysql);
			$textplan1 = $designeetext_arr['designee1_text'];
			$textplan2 = $designeetext_arr['designee2_text'];
		
			if($textplan1 != '')
			{
			$succession_update_message = preg_replace("/<{firstdesignee_(.*?)}>/s","",$succession_update_message);
			$values["directreplace"]["textplan1"] = $textplan1;
			}
			else
			{
			$succession_update_message = preg_replace("/<{firstdesignee_start}>(.*?)<{firstdesignee_end}>/s","",$succession_update_message);
			}
			if($textplan2 !='')
			{
			$succession_update_message = preg_replace("/<{seconddesignee_(.*?)}>/s","",$succession_update_message);
			$values["directreplace"]["textplan2"] = $textplan2;
			}
			else
			{
			$succession_update_message = preg_replace("/<{seconddesignee_start}>(.*?)<{seconddesignee_end}>/s","",$succession_update_message);
			}

		
		
		
			//echo "<br><br>$succession_update_message<br><br>";

			$values["directreplace"]["to_username"] = $to_username;
			$values["directreplace"]["from_username"] = $from_username;

			$succession_update_message=$common->direct_replace($db_object,$succession_update_message,$values);

	//echo "<br>Reciever $to_email<br>";
	//echo "<br>Subject $succession_update_subject<br>";
	//echo "<br>Message $succession_update_message<br>";
	//echo "<br>Sender $from_email<br>";

//MAIL WILL BE SENT ONLY IF THE SELECTED CANDIDATE IS A EXTERNAL FELLOW...

		if($textplan1 != '' || $textplan2 != '')
			{	
				$sent=$common->send_mail($to_email,$succession_update_subject,$succession_update_message,$from_email);
	
				if($sent)
				{
		
					echo $alert_msg["cMailsent"];
		
				}
				else
				{
					echo $alert_msg["cFailmail"];
				}

			}
		}

	}		
		
}		

$obj = new documentActions;

if($fSave)
{
	$obj->save_contents($db_object,$common,$default,$user_id,$error_msg,$post_var);
	$message = $error_msg['cSuccessionplanSavedsuccessfully'];
	echo $message;
	$obj->mail_to_admin($db_object,$common,$default,$user_id,$post_var);
}
else
{
	$obj->show_documentactions($db_object,$common,$default,$user_id,$error_msg,$post_var);
}

include_once("footer.php");
?>
