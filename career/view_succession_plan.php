<?php

/*---------------------------------------------
SCRIPT:.php
AUTHOR:info@chrisranjana.com	
UPDATED:26th Dec

DESCRIPTION:
This script displays all the models for a particular position selected.

---------------------------------------------*/

include("../session.php");
include("header.php");
class displaymodels
{
function show_models($db_object,$common,$post_var,$default,$user_id)
	{

		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}
		//print_r($post_var);		
	
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/view_succession_plan.html";
		$returncontent	= $common->return_file_content($db_object,$returncontent);

		$model_factors_1 = $common->prefix_table('model_factors_1');
		$family_position = $common->prefix_table('family_position');
		$model_name_table= $common->prefix_table('model_name_table');
		$deployment_plan = $common->prefix_table('deployment_plan');
		
		$user_table=$common->prefix_table("user_table");

//STORE THE DATA REGARDING THE POSITION FOR WHICH THE SUCCESSION & DEPLOYMENT PLAN IS CREATED...
		
		$sql="select position from $user_table where user_id='$user_id'";
		
		$res=$db_object->get_a_line($sql);
		
		$posid=$res[position];
		
		$mysql = "select plan_id from $deployment_plan where position = '$posid'";
		$planid_arr = $db_object->get_a_line($mysql);		
		$planid = $planid_arr['plan_id'];
		if($planid == '')
		{
		$mysql = "insert into $deployment_plan set position = '$posid',
				created_user = '$user_id',
				created_date = now()";
		$db_object->insert($mysql);
		}
		else
		{
		$mysql = "update $deployment_plan set position = '$posid',
				created_user = '$user_id',
				created_date = now()
				where plan_id = '$planid'";	
		$db_object->insert($mysql);
		}
		
		
		
		


		//DETERMINE THE FAMILY OF THE SELECTED POSITION...
		$mysql = "select family_id from $family_position where position_id = '$posid'";
		$familypos_arr = $db_object->get_a_line($mysql);
		$familyofpos = $familypos_arr['family_id'];
		
		
		//THE POSITION IS OBTAINED FROM THE PREVIOUS SCREEN 
		//SHOW THE MODELS RELATING TO THAT POSITION...
		//SELECT THE MODELS THAT THIS PERSON IS CAPABLE OF VIEWING...
		//SELECT THE MODELS RELATING TO THAT POSITIONS' FAMILY...

		$userviewablemodels = $common->viewable_models($db_object,$user_id);
		
		$modelid_all = @implode("','",$userviewablemodels);
		
		if($modelid_all != '')
		{
		$mysql = "select model_id 
				from $model_factors_1 
				where family = '$familyofpos'
				and model_id in ('$modelid_all')";
		$models_arr = $db_object->get_single_column($mysql);
		$models_full = @implode("','",$models_arr);
		}

		if($models_full != '')	
		{
		$mysql = "select model_id,model_name from $model_name_table where model_id in ('$models_full')";
		$models_display_arr = $db_object->get_rsltset($mysql);
		}

		$multipleloopvalues['modelsview_loop'] = $models_display_arr;
		$values['posid'] = $posid;

		$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
		$returncontent = $common->multipleloop_replace($db_object,$returncontent,$multipleloopvalues,'');
		
		
		echo $returncontent;
	
	
	}
	
	//===============================================================//
	function show_documentactions($db_object,$common,$default,$user_id,$error_msg,$post_var,$modelid)
	{
		
		$xPath		= $common->path;
		$returncontent	= $xPath."/templates/career/view_succession_plan1.html";
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
		
		$sql="select position from $user_table where user_id='$user_id'";
		
		$res=$db_object->get_a_line($sql);
		
		$pid=$res[position];
		$pid=43;
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
			if($skills_in_plan!='')
			{
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$largest_new);
			}
			
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


			if($checkboxsel=="checked")
			{
			$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$largesttech_new);
			}
			
		}

		$returncontent = preg_replace("/<{largestgapstech_loopstart}>(.*?)<{largestgapstech_loopend}>/s",$str1,$returncontent);		
		if((count($inter_gaps_arr[1])==0 or $str=="") and (count($tech_gaps_arr[1])==0 or $str1==""))
		{
			$values["notselected"]="No Skills Selected";
		}
		else
		{
			$values["notselected"]="";
		}
		
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
			if($checkboxsel=="checked")
			{
				$str2 .= preg_replace("/<{(.*?)}>/e","$$1",$othergap_new);
			}
			
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
			if($checkboxsel=="checked")
			{

				$str3 .= preg_replace("/<{(.*?)}>/e","$$1",$othergaptech_new);
			}
			
		}


		$returncontent = preg_replace("/<{othergapstech_loopstart}>(.*?)<{othergapstech_loopend}>/s",$str3,$returncontent);		
		
		if((count($inter_gaps_arr[2])==0 or $str2=="") and (count($tech_gaps_arr[2])==0 or $str3==""))
		{
			$values[notselected1]="No Skill Selected";
		}
		else
		{

			$values[notselected1]="";
		}
		

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

			if($checkboxsel=="checked")
			{
			$str4 .= preg_replace("/<{(.*?)}>/e","$$1",$largestgap2_new);
			}
			
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
			if($checkboxsel=="checked")
			{
			$str5 .= preg_replace("/<{(.*?)}>/e","$$1",$largestgaptech2_new);
			}
		}

		$returncontent = preg_replace("/<{largestgapstech2_loopstart}>(.*?)<{largestgapstech2_loopend}>/s",$str5,$returncontent);
		if((count($inter_gaps2_arr[1])==0 or $str4=="") and (count($tech_gaps2_arr[1]==0) or $str5=""))
		{
			$values[notselected2]="No Skill Selected";
		}
		else
		{
			$values[notselected2]="";
		}
		
		
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

			
			if($checkboxsel=="checked")
			{
			$str6 .= preg_replace("/<{(.*?)}>/e","$$1",$othergap2_new);
			}
			
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
			if($checkboxsel=="checked")
			{
			$str7 .= preg_replace("/<{(.*?)}>/e","$$1",$othergaptech2_new);
			}
			
		}

		$returncontent = preg_replace("/<{othergapstech2_loopstart}>(.*?)<{othergapstech2_loopend}>/s",$str7,$returncontent);		

		if((count($inter_gaps2_arr[2])==0 or $str6=="") and (count($tech_gaps2_arr[2])==0 or $str7==""))
		{
			$values[notselected3]="No Skill Selected";
		}
		else
		{
			$values[notselected3]="";
		}
	

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
		$values[model]=$modelid;

		$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);
		echo $returncontent;
		
	}

}
$obj = new displaymodels;


switch($action)
{
	case NULL:
	
	$obj->show_models($db_object,$common,$post_var,$default,$user_id);
	
	break;
	
	case "show":

	$obj->show_documentactions($db_object,$common,$default,$user_id,$error_msg,$post_var,$model_to_view);
	
	break;
	
}
include_once('footer.php');
?>
