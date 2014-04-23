<?php
include("../session.php");

include("header.php");
class front
{
	function front_display($common,$db_object,$image,$user_id,$gbl_gr_select,$fAs,$fModel)
		{
		$filename="../templates/career/front_panel.html";
		$filecontent=$common->return_file_content($db_object,$filename,$user_id);
		$value			= array();
		$user_table		= $common->prefix_table("user_table");
		$ratergroup_table 	= $common->prefix_table('rater_group');
		$otherraters_table 	= $common->prefix_table('other_raters');
		$tech_references 	= $common->prefix_table('tech_references');		
		$position_table		= $common->prefix_table("position");
		$appraisal_table 	= $common->prefix_table("appraisal");
		$assign_test_table	= $common->prefix_table("assign_test_builder");
		$assign_skill_table	= $common->prefix_table("assign_tech_skill_builder");
		$assign_succession_plan = $common->prefix_table("assign_succession_plan");
		$other_raters		= $common->prefix_table("other_raters");
		$user_tests		= $common->prefix_table("user_tests");
		$assign_succession_plan_sub=$common->prefix_table("assign_succession_plan_sub");
		$assign_test_builder	= $common->prefix_table("assign_test_builder");
		$other_raters		= $common->prefix_table("other_raters");
		$tech_references	= $common->prefix_table("tech_references");
		$appraisal		= $common->prefix_table("appraisal");
		$model_name_table	= $common->prefix_table('model_name_table');
		$model_factors_1    =$common->prefix_table("model_factors_1");
		$career_goals=$common->prefix_table("career_goals");
		$model_percent_fit=$common->prefix_table("models_percent_fit");
	
		
		
//FIRST DETERMINE THE USER IF HE/SHE IS AN ADMIN , BOSS OR JUST AN EMPLOYEE...
	$xUser=$user_id;	

$check_boss = $common->is_boss($db_object,$user_id);
$check_admin = $common->is_admin($db_object,$user_id);

//$filecontent=preg_replace("/<{ifemployee_loop(.*?)/s","",$filecontent);



///*
if($check_boss or  $check_admin)
{
	$filecontent = preg_replace("/<{ifadmin_boss_loop(.*?)}>/s","",$filecontent);
}
else
{
	$filecontent = preg_replace("/<{ifadmin_boss_loopstart}>(.*?)<{ifadmin_boss_loopend}>/s","",$filecontent);
}
if($check_admin)
{
	$filecontent = preg_replace("/<{only_for_admins(.*?)}>/s","",$filecontent);
}
else
{
	$filecontent = preg_replace("/<{only_for_admins_area}>(.*?)<{only_for_admins_area}>/s","",$filecontent);
}
if($check_boss)
{

	$filecontent = preg_replace("/<{ifboss_loop(.*?)}>/s","",$filecontent);
}
else
{	

	$filecontent = preg_replace("/<{ifboss_loopstart}>(.*?)<{ifboss_loopend}>/s","",$filecontent);

}
/*echo "model=$fModel<br>";
if($fModel)
{
	//header("Location:samelevel_fit.php?model_id=$fModel");
	
	$qry="select avg(percent_fit) as percent from $model_percent_fit where model_id ='$fModel'
				
				and user_id ='$user_id'";
				
				$percent=$db_object->get_a_line($qry);
				
				$percent=$percent[0];
				
					$array=array($percent,$total);
					

		$vals=$image->return_Array($array);
		
		$image->init(350,200, $vals);
	
		$image->set_legend_value();

		 $image->display($filename);
		 

}

echo "fas=$fAs<br>";*/	
if($fAs=="" or $fAs==1)
{
	$value["as"]=$fAs;

	$filecontent = preg_replace("/<{ifemp_graphs(.*?)}>/s","",$filecontent);
	
		/*$sql="select same_level from $career_goals where user_id='$user_id' and interest='lot'";

		$res=$db_object->get_single_column($sql);

		if(count($res)>0)
		{
			$fam=@implode(",",$res);
			
			$family_ids="(".$fam.")";
			
			$sql="select $model_factors_1.model_id,model_name from $model_factors_1,
			
			model_name_table where $model_factors_1.model_id=$model_name_table.model_id and family in $family_ids";

			$sql_res=$db_object->get_rsltset($sql);

	preg_match("/<{model_loopstart}>(.*?)<{model_loopend}>/s",$filecontent,$match);
			
	$match = $match[0];
	
	for($j=0;$j<count($sql_res);$j++)
		{
			$model_id=$sql_res[$j][model_id];

			$model_name=$sql_res[$j][model_name];
			
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$match);
			
		}		

	$filecontent = preg_replace("/<{model_loopstart}>(.*?)<{model_loopend}>/s",$str,$filecontent);	
	
		}*/
}
else
{
	$filecontent = preg_replace("/<{ifemp_graphs_loopstart}>(.*?)<{ifemp_graphs_loopend}>/s","",$filecontent);
}
if($fAs==2)
{
	$filecontent = preg_replace("/<{ifboss_graphs(.*?)}>/s","",$filecontent);
}
else
{
	$filecontent = preg_replace("/<{ifboss_graphs_loopstart}>(.*?)<{ifboss_graphs_loopend}>/s","",$filecontent);
}
if($fAs==3)
{
	$filecontent = preg_replace("/<{ifadmin_graphs(.*?)}>/s","",$filecontent);
}
else
{
	$filecontent = preg_replace("/<{ifadmin_graphs_loopstart}>(.*?)<{ifadmin_graphs_loopend}>/s","",$filecontent);
}
if($user_id!=1)
{
	$sql="select user_id from $user_table where admin_id='$user_id'";
	
	$res=$db_object->get_single_column($sql);


	if(count($res)>0)
	{
		$filecontent = preg_replace("/<{ifemp_under_admin(.*?)}>/s","",$filecontent);
	}
	else
	{
		$filecontent = preg_replace("/<{ifemp_under_admin}>(.*?)<{ifemp_under_admin}>/s","",$filecontent);
	}
}

	preg_match("/<{posname_loopstart}>(.*?)<{posname_loopend}>/s",$filecontent,$match);
	$match = $match[0];
	
	$keys=@array_keys($gbl_gr_select);
	
	$values=@array_values($gbl_gr_select);

	for($j=0;$j<count($gbl_gr_select);$j++)
		{
			$key=$keys[$j];

			$value1=$values[$j];
			
			if(($key==3 and $check_admin) or ($key==2 and $check_boss) or ($key==1))
			{
			$str .= preg_replace("/<{(.*?)}>/e","$$1",$match);
			}
		}		

	$filecontent = preg_replace("/<{posname_loopstart}>(.*?)<{posname_loopend}>/s",$str,$filecontent);	
	
$str="";

//*/
if($user_id==1)
{
	$filecontent = preg_replace("/<{edit_settings_(.*?)}>/s","",$filecontent);
}
else
{
	$filecontent = preg_replace("/<{edit_settings_start}>(.*?)<{edit_settings_end}>/s","",$filecontent);
}
if($check_boss)
{
	$filecontent = preg_replace("/<{bosslinks_(.*?)}>/s","",$filecontent);
}
else
{
	$filecontent = preg_replace("/<{bosslinks_start}>(.*?)<{bosslinks_end}>/s","",$filecontent);
}



if($check_admin)
{
	$filecontent = preg_replace("/<{adminlinks_(.*?)}>/s","",$filecontent);
}
else
{
	$filecontent = preg_replace("/<{adminlinks_start}>(.*?)<{adminlinks_end}>/s","",$filecontent);
}




//EMPLOYEE LINKS ARE COMMON TO ALL....
//====================================


//------------DISPLAY OF EMPLOYEE LINKS START

//INTERPERSONAL AND TECHNICAL TESTS...
//-----------------------------------
$fields = $common->return_fields($db_object,$appraisal_table);
		$mysql = "select $fields from $appraisal_table where user_id='$user_id' and test_mode = 'Test'";
		$detail_arr = $db_object->get_rsltset($mysql);
		

		$replaced = "";
		for($i=0;$i<count($detail_arr);$i++)
		{
			
			$test_mode = $detail_arr[$i]['test_mode'];
			
			$test_typevar = $detail_arr[$i]['test_type'];
			$test_type = $gbl_skill_type[$test_typevar];
			
			$user_id = $detail_arr[$i]['user_id'];
			
			if(($test_typevar == 'i') && (test_mode != 'Test'))
			{
			$filecontent=preg_replace("/<{interalert_(.*?)}>/s","",$filecontent);
				
			}
			
			
			
			
			if(($test_typevar == 't') && (test_mode != 'Test')) 
			{
				$filecontent=preg_replace("/<{techalert_(.*?)}>/s","",$filecontent);
			}
			 
			 
		}
	$filecontent=preg_replace("/<{interalert_start}>(.*?)<{interalert_end}>/s","",$filecontent);
	$filecontent=preg_replace("/<{techalert_start}>(.*?)<{techalert_end}>/s","",$filecontent);



//INTERPERSONAL AND TECHNICAL 360
//-------------------------------
//DISPLAYING THE ADD/ASSIGN INTER & TECHNICAL SKILL APPRAISAL RATERS
//NULLIFY THE ALERT IF THE USER HAS COMPLETED ASSIGNING ALL THE REFERENCES...
		$mysql = "select group_belonging from $otherraters_table where cur_userid = '$user_id'";
		$grpbelonging_arr = $db_object->get_single_column($mysql);
	
		if(@in_array("grp_boss",$grpbelonging_arr) && @in_array("grp_topboss",$grpbelonging_arr) && @in_array("grp_self",$grpbelonging_arr) && @in_array("grp_team",$grpbelonging_arr) && @in_array("grp_peer",$grpbelonging_arr) && @in_array("grp_dirrep",$grpbelonging_arr) && @in_array("grp_other",$grpbelonging_arr) && @in_array("grp_incus",$grpbelonging_arr) && @in_array("grp_excus",$grpbelonging_arr) )
		{
			$filecontent=preg_replace("/<{interappraisal_start}>(.*?)<{interappraisal_end}>/s","",$filecontent);

		}
		
//DISPLAY THE ALERT WHEN THE ADMIN HAS ASSIGNED SOME APPRAISAL...
		
		$fields = $common->return_fields($db_object,$appraisal_table);
		$mysql = "select $fields from $appraisal_table where user_id='$user_id' and test_mode = '360'";
		
		$detail_arr = $db_object->get_rsltset($mysql);
		
		for($i=0;$i<count($detail_arr);$i++)
		{
			
			$test_mode = $detail_arr[$i]['test_mode'];
			
			$test_typevar = $detail_arr[$i]['test_type'];
			$test_type = $gbl_skill_type[$test_typevar];
			
			$user_id = $detail_arr[$i]['user_id'];
			
			if(($test_typevar == 'i') && (test_mode != '360'))
			{
			$filecontent=preg_replace("/<{interappraisal_(.*?)}>/s","",$filecontent);
				
			}
			 
			if(($test_typevar == 't') && (test_mode != '360')) 
			{
				$filecontent=preg_replace("/<{techappraisal_(.*?)}>/s","",$filecontent);
			}
			 
		}
 
	$filecontent=preg_replace("/<{techappraisal_start}>(.*?)<{techappraisal_end}>/s","",$filecontent);
	$filecontent=preg_replace("/<{interappraisal_start}>(.*?)<{interappraisal_end}>/s","",$filecontent);

//ALERT FOR RATING OTHERS...
//-------------alert for rating check start
//if no alerts are there for a person, then nullify the tag "ALERT FOR RATING OTHERS"...
//INTERPERSONAL...

$mysql = "select rater_id from $otherraters_table where rater_userid = '$user_id' and status = 'a' and rating_over = 'n'";
$ratingalert_arr =$db_object->get_single_column($mysql);

//TECHNICAL...
$mysql = "select ref_id from $tech_references where ref_userid = '$user_id' and status = 'a'";
$ratingtech_arr = $db_object->get_single_column($mysql);
    

	if(($ratingalert_arr == '') && ($ratingtech_arr == ''))
	{
	$filecontent=preg_replace("/<{alertforrating_start}>(.*?)<{alertforrating_end}>/s","",$filecontent);			
	}
	else
	{
	$filecontent = preg_replace("/<{alertforrating_(.*?)}>/s","",$filecontent);			
	}



//determine if there are any alerts for rating others to the person...
//Check if there is any rejected offers for any interpersonal or technical ratings....
//if there is a rejected offer display them else nullify the link....

	
		$mysql = "select rater_id from $otherraters_table where cur_userid = '$user_id' and status = 'r'";
		$rejected_inter_arr = $db_object->get_single_column($mysql);
		
	
		$mysql = "select ref_id from $tech_references where user_to_rate = '$user_id' and status = 'r'";
		$rejected_tech_arr = $db_object->get_single_column($mysql);
	
		if(($rejected_inter_arr == '') && ($rejected_tech_arr == ''))
		{
			$filecontent=preg_replace("/<{rejectedoffersdisplay_start}>(.*?)<{rejectedoffersdisplay_end}>/s","",$filecontent);							

		}
		else
		{
			$filecontent=preg_replace("/<{rejectedoffersdisplay_(.*?)}>/s","",$filecontent);	
		}
		
		




//------------DISPLAY OF EMPLOYEE LINKS END



//------------DISPLAY OF BOSS LINKS START
//=======================================
	
//VIEW [REPORTS] DASHBOARD...
preg_match("/<{bossdashboard_loopstart}>(.*?)<{bossdashboard_loopend}>/s",$filecontent,$matchold_bosses);	

	$matchnew_bosses = $matchold_bosses[1];

	if($user_id!=1)
		{
			//$mysql = "select username,user_id from $user_table where admin_id='$user_id'";
			$mysql = "select position from $user_table where user_id = '$user_id'";
			$pos_of_user_arr = $db_object->get_a_line($mysql);
			$pos_of_user = $pos_of_user_arr['position'];
			
			$mysql = "select username,user_id from $user_table , $position_table
					where $user_table.position = $position_table.pos_id
					and $position_table.boss_no = '$pos_of_user'";
		}
		else
		{
			$mysql = "select $user_table.username,$user_table.user_id 
				from $user_table,$position_table 
				where $user_table.position=$position_table.pos_id 
				and ($user_table.position<>NULL or $user_table.position<>0) 
				and $user_table.user_id!=1 
				order by $position_table.level_no desc";			

		}
		$userset=$db_object->get_rsltset($mysql);
		
		if($userset[0]["user_id"] != "")
		{	
			for($i=0;$i<count($userset);$i++)
			{
				$temp_id=$userset[$i]["user_id"];
				$userset[$i]["username"]=$common->name_display($db_object,$temp_id);
			}
			
			$userset=$common->return_Keyedarray($userset,"user_id","username");		
			
			$matched_new_bosses=$common->singleloop_replace($db_object,"users_boss_display_loopstart","users_boss_display_loopend",$matchnew_bosses,$userset,$selqrr);
			
			
		}

		$filecontent = preg_replace("/<{bossdashboard_loopstart}>(.*?)<{bossdashboard_loopend}>/s",$matched_new_bosses,$filecontent);	




//------------DISPLAY OF BOSS LINKS END




//------------DISPLAY OF ADMIN LINKS START
//========================================

//VIEW BOSS' CULTURAL PROFILE and EXPERTISE PROFILE...
//SELECT USERS WHO COME UNDER THIS ADMIN'S ADMINISTRATION...
		
		//PULL DOWNS FOR CARRER GOALS,MOBILITY REPORTS...

			if($user_id!=1)
			{
				
				$res1=array();$res2=array();

				if($check_admin)
				{
					$sql="select user_id from $user_table where admin_id='$user_id' and user_type<>'external'";

						$res1=$db_object->get_single_column($sql);


				}
				if($check_boss)
				{
					$sql="select position from $user_table where user_id='$user_id' and user_type<>'external'";
								
					$sql_pos=$db_object->get_a_line($sql);
					
					$pos=$sql_pos[position];
					
					$users_below_pos=$common->get_chain_below($pos,$db_object,$twodarr);
					
					$users_below=$common->get_user_id($db_object,$users_below_pos);
					
					$c=0;
					
					for($a=0;$a<count($users_below);$a++)
					{

						if($users_below[$a][user_id]!="")
						{
						$res2[$c]=$users_below[$c][user_id];	
						
						$c++;
						}
						
					}
					
				}

				$res1=@array_merge($res1,$res2);

			}
		else
		{
			$sql="select user_id from $user_table where user_id<>'$user_id'";
			
			$res1=$db_object->get_single_column($sql);
		
		}

			if($res1[0]=="")
			{
				$filecontent=preg_replace("/<{ifemp_under_admin}>(.*?)<{ifemp_under_admin}>/s","$$1",$filecontent);
			}
			else
			{
				$filecontent=preg_replace("/<{ifemp_under_(.*?)}>/s","",$filecontent);
			}
		
		

	if($user_id != '1')
	{
	$mysql = "select user_id from $user_table where admin_id = '$user_id'";
	}
	if($user_id == '1')
	{
	$mysql = "select user_id from $user_table where user_id <> '1'";
	}
	
	//$user_underadmin_arr = $db_object->get_single_column($mysql);

$user_underadmin_arr=$res1;


	for($i=0;$i<count($user_underadmin_arr);$i++)
		{
			$user_underadmin = $user_underadmin_arr[$i];
			$check_if_boss = $common->is_boss($db_object,$user_underadmin);
			if($check_if_boss)
			{
				$boss_underadmin_arr[] = $user_underadmin;
			}
		}
	
	preg_match("/<{bossunderadmin_loopstart}>(.*?)<{bossunderadmin_loopend}>/s",$filecontent,$matchold);
	$matchadmin_new = $matchold[1];
	
	for($j=0;$j<count($boss_underadmin_arr);$j++)
		{
			$boss_underadmin = $boss_underadmin_arr[$j];
			$name_to_show = $common->name_display($db_object,$boss_underadmin);
			$viewstr .= preg_replace("/<{(.*?)}>/e","$$1",$matchadmin_new);
		}		

	$filecontent = preg_replace("/<{bossunderadmin_loopstart}>(.*?)<{bossunderadmin_loopend}>/s",$viewstr,$filecontent);	
	
//VIEW [EMPLOYEES] APPRAISAL RESULTS...

	preg_match("/<{viewemployeeappraisal_loopstart}>(.*?)<{viewemployeeappraisal_loopend}>/s",$filecontent,$matchold2);
	$matchadmin2_new = $matchold2[1];
	
	for($i=0;$i<count($user_underadmin_arr);$i++)
	{
		$user_under_admin = $user_underadmin_arr[$i];
		$mysql = "select username from $user_table where user_id = '$user_under_admin'";
		$username_empls_arr = $db_object->get_a_line($mysql);
		$username_empls = $username_empls_arr['username'];
		$adminemplsstr .= preg_replace("/<{(.*?)}>/e","$$1",$matchadmin2_new);
	}
	$filecontent = preg_replace("/<{viewemployeeappraisal_loopstart}>(.*?)<{viewemployeeappraisal_loopend}>/s",$adminemplsstr,$filecontent);	




 
//COMPARE [EMPLOYEE] TO [MODEL]
	preg_match("/<{viewmodels_loopstart}>(.*?)<{viewmodels_loopend}>/s",$filecontent,$matchold3);
	$matchadmin3_new = $matchold3[1];
		$allviewablemodels_arr = $common->viewable_models($db_object,$user_id);
		for($i=0;$i<count($allviewablemodels_arr);$i++)
		{
			$modelid = $allviewablemodels_arr[$i];
			$mysql = "select model_name from $model_name_table where model_id = '$modelid'";
			$modelname_arr = $db_object->get_a_line($mysql);
			$modelname = $modelname_arr['model_name'];
			$modelstr .= preg_replace("/<{(.*?)}>/e","$$1",$matchadmin3_new);
		}
	$filecontent = preg_replace("/<{viewmodels_loopstart}>(.*?)<{viewmodels_loopend}>/s",$modelstr,$filecontent);	
	
	
//VIEW [EMPLOYEE'S] DASHBOARD...
	/**/
	
	preg_match("/<{admindashboard_loopstart}>(.*?)<{admindashboard_loopend}>/s",$filecontent,$matchold_admins);	

	$matchnew_admins = $matchold_admins[1];

	if($user_id!=1)
		{
			$mysql = "select username,user_id from $user_table where admin_id='$user_id'";
				
		}
		else
		{
			$mysql = "select $user_table.username,$user_table.user_id 
				from $user_table,$position_table 
				where $user_table.position=$position_table.pos_id 
				and ($user_table.position<>NULL or $user_table.position<>0) 
				and $user_table.user_id!=1 
				order by $position_table.level_no desc";			
			

		}
		$userset=$db_object->get_rsltset($mysql);
		
		if($userset[0]["user_id"] != "")
		{	
			for($i=0;$i<count($userset);$i++)
			{
				$temp_id=$userset[$i]["user_id"];
				$userset[$i]["username"]=$common->name_display($db_object,$temp_id);
			}
			
			$userset=$common->return_Keyedarray($userset,"user_id","username");		
			
			$matched_new_admins=$common->singleloop_replace($db_object,"usersdisplay_loopstart","usersdisplay_loopend",$matchnew_admins,$userset,$selqrr);
			
			
		}

		$filecontent = preg_replace("/<{admindashboard_loopstart}>(.*?)<{admindashboard_loopend}>/s",$matched_new_admins,$filecontent);	

//------------DISPLAY OF ADMIN LINKS END






$selqry="select user_id from $assign_test_table where user_id='$user_id' and status = 'p'";
$useridxistsa=$db_object->get_a_line($selqry);


//echo $user_id;
if($useridxistsa["user_id"]=="")
{
	$filecontent=preg_replace("/<{test_builderalertstart}>(.*?)<{test_builderalertend}>/s","",$filecontent);		

	
}
$selqry="select user_id from $assign_skill_table where user_id='$user_id'";
$useridxistsb=$db_object->get_a_line($selqry);

if($useridxistsb["user_id"]=="")
{

	$filecontent=preg_replace("/<{skill_builderalertstart}>(.*?)<{skill_builderalertend}>/s","",$filecontent);

	
}
$filecontent=preg_replace("/<{skill_builderalert(.*?)}>/s","",$filecontent);
$filecontent=preg_replace("/<{test_builderalert(.*?)}>/s","",$filecontent);		

//VIEW REPORTS COMPLIANCE-SELF
$ch=$common->is_boss($db_object,$user_id);

if($ch!="")
{
	//$users_id=$common->return_direct_reports($db_object,$user_id);
	
		if($user_id!=1)
		{
		$sql="select position from $user_table where user_id='$user_id'";
		
		$res_sql=$db_object->get_a_line($sql);
		
		$pos=$res_sql[position];
		
		$users_under=$common->get_chain_below($pos,$db_object,$twodarr);
		
		$users_under_id=$common->get_user_id($db_object,$users_under);
		
		for($i=0;$i<count($users_under_id);$i++)
		{
			$users_id[$i]=$users_under_id[$i][user_id];
		}
		
		//$users_id=$common->return_direct_reports($db_object,$user_id);
		}
		else
		{
			$sql="select user_id from $user_table where user_id <>'$user_id'";
			
			$users_id=$db_object->get_single_column($sql);
		
		}
		if($users_id[0]!="")
		{
		$users=@implode(",",$users_id);
		}
	if($users_id[0]!="")
	{
	$user_clause1="and rater_userid in "."(".$users.")";
	
	$user_caluse2="and ref_userid in "."(".$users.")";
	
	
$sql="select count(rater_userid) as self_count from $other_raters where cur_userid=rater_userid
		and rating_over='y' ".$user_clause1;

$result1=$db_object->get_single_column($sql);

$self_count=$result1[0];

$sql="select count(user_id) from $user_tests where user_id in" ."(". $users.")". "and test_completed='y'";

$result2=$db_object->get_single_column($sql);
$self_count+=$result2[0];

$sql="select count(ref_userid) as self_count from $tech_references where ref_userid=user_to_rate
	and rating_over='y' ".$user_clause2;

$result3=$db_object->get_single_column($sql);
$self_count+=$result3[0];

$sql="select count(rater_userid) as self_count from $other_raters where cur_userid=rater_userid " .$user_clause1;

$result1=$db_object->get_single_column($sql);
$total_count=$result1[0];

$sql="select count(user_id) from $user_tests where user_id in"."(". $users.")";

$result2=$db_object->get_single_column($sql);
$total_count+=$result2[0];

$sql="select count(ref_userid) from $tech_references where ref_userid=user_to_rate and ref_userid in"."(". $users.")" ;

$result3=$db_object->get_single_column($sql);
$total_count+=$result3[0];


if($total_count!=0)
{
$self=($self_count/$total_count)*100;
}
else
{
	$self=0;
}

$value[self] = @sprintf("%01.2f",$self);

$current_date=time()-(7*24*60*60);

$today = date("Y-m-d H:i:s ",$current_date);                         

$sql="select count(rater_userid) as other from $other_raters where cur_userid<>rater_userid

and date_rating_requested<'$today' and rating_over='y' and rater_id in"."(".$users.")";

$result1=$db_object->get_single_column($sql);

$other=$result1[0];

$sql="select count(ref_userid) as other from $tech_references where ref_userid<>user_to_rate

and date_rating_requested<'$today' and rating_over='y' and ref_userid in"."(".$users.")";

$result2=$db_object->get_single_column($sql);

$other+=$result2[0];

$sql="select count(rater_userid) as total from $other_raters where cur_userid<>rater_userid

and date_rating_requested<'$today' and rater_id in"."(".$users.")";

$result1=$db_object->get_single_column($sql);

$total=$result1[0];

$sql="select count(ref_userid) as total from $tech_references where ref_userid<>user_to_rate

and date_rating_requested<'$today' and ref_userid in"."(".$users.")";

$result2=$db_object->get_single_column($sql);

$total+=$result2[0];

if($total!=0)
{
	$other=($other/$total)*100;
}
else
{
	$other=0;
}

$value[others] = @sprintf("%01.2f",$other);
	}
	else
	{
		$value[self]=0;
		
		$value[others]=0;
	}
}
//REPORTS COMPLIANCE RATING OTHERS



//SEARCH REPORTS BY SKILLS

	$ch=$common->is_boss($db_object,$user_id);
	
	if($ch==1)
	{
		$filecontent=preg_replace("/<{ifboss_(.*?)}>/s","",$filecontent);		
	}
	else
	{
		$filecontent=preg_replace("/<{ifboss_loopstart}>(.*?)<{ifboss_loopend}>/s","",$filecontent);
	}
//ASSIGN SUC PLAN
	$check=$common->is_admin($db_object,$user_id);
	
	$boss_check=$common->is_boss($db_object,$user_id);
	
	$users_under=$common->employees_under_admin_boss($db_object,$fUser);

if($fUser==1)
{
	$value[highlight3]="class=highlight";

	$filecontent=preg_replace("/<{sucplanalert_loop(.*?)}>/s","",$filecontent);		
}

	if($check==1 or $boss_check==1)
	{
		//$qry="select assigned_to from $assign_succession_plan_sub where assigned_on<'$today' and status<>'y'";

		//$qry="select * from $assign_succession_plan_sub where assigned_to='$fUser'";
		
		if($users_under[0]!="")
		{
			$users=@implode(",",$users_under);
			
			$users_ids="(".$users.")";
		
		$qry="select * from $assign_succession_plan where assigned_to in $users_ids and status<>'y'";

		$qry_res=$db_object->get_single_column($qry);
		}
		
		if($qry_res[0]!="")
		{
		
			$value[highlight3]="class=highlight";
		}
		else
		{
			$value[highlight3]="";
		}
		
		//$filecontent=preg_replace("/<{if_admin_boss(.*?)}>/s","",$filecontent);		
$filecontent=preg_replace("/<{sucplanalert(.*?)}>/s","",$filecontent);		
	}
	else
	{
		//$filecontent=preg_replace("/<{if_admin_boss_loopstart}>(.*?)<{if_admin_boss_loopend}>/s","",$filecontent);
$filecontent=preg_replace("/<{sucplanalert_loopstart}>(.*?)<{sucplanalert_loopend}>/s","",$filecontent);
	}

if($check==0)
{

$filecontent=preg_replace("/<{ifanyboss_under_admin}>(.*?)<{ifanyboss_under_admin}>/s","",$filecontent);
}
	if($check==1)
	{
		

		
		//=========================================
		
		$qry="select user_id from $user_table where admin_id='$user_id'";
		
		$users=$db_object->get_single_column($qry);	
		
		if(count($users)>0)
		{
			$users=@implode(",",$users);
			
			$users_set="(".$users.")";
			
			if($user_id!=1)
			{
			
				$sql="select user_id,test_mode,test_type,date_assigned from $appraisal where 
				
				user_id in $users_set";
			}
			else
			{
				$sql="select user_id,test_mode,test_type,date_assigned from $appraisal";
			}
		
			$result=$db_object->get_rsltset($sql);
					
			
		}
	
		$c=0;
				
		for($i=0;$i<count($result);$i++)
		{
		
			
			$user=$result[$i][user_id];
			
			$test_mode=$result[$i][test_mode];
			
			$test_type=$result[$i][test_type];
		
			
			if($test_mode=='Test' )
			{
				$qry="select test_completed from $user_tests where user_id='$user' and test_type='$test_type'";

				$res=$db_object->get_a_line($qry);
				
				$status1=$res[test_completed];
				
				if($status1=='y')
				{
					$status1='a';

					if($status1!='a')
					{

					$status[$c]=$user;

					$c++;
					}
				}
			}
			if($test_mode=='360')
			{
				if($test_type=='i')
				{

					$qry="select status from $other_raters where cur_userid='$user'";
					
					$res=$db_object->get_a_line($qry);
					
					$status1=$res[status];
					
					if($status1!='a')
					{

					$status[$c]=$user;

					$c++;
					}

					
				}
				if($test_type=='t')
				{
					$qry="select status from $tech_references where user_to_rate='$user'";
					
					$res=$db_object->get_a_line($qry);
					
					$status1=$res[status];
					
					if($status1!='a')
					{

					$status[$c]=$user;

					$c++;
					}

				}
				
			}
		}
		
			
			if(count($status)>0)
			{
				$value[highlight1]="class=highlight";
				
			}
			else
			{
				$value[highlight1]="";
			}
		//==========================================	
			
		/*//PULL DOWNS FOR CARRER GOALS,MOBILITY REPORTS...
			
			if($user_id!=1)
			{
				$res1=array();$res2=array();
				
				if($ch_admin)
				{
					$sql="select user_id from $user_table where admin_id='$user_id'";
					
					$res1=$db_object->get_single_column($sql);
				}
				if($ch_boss)
				{
					$sql="select position from $user_table where user_id='$user_id'";
					
					$sql_pos=$db_object->get_a_line($sql);
					
					$pos=$sql_pos[position];
					
					$users_below=$common->get_chain_below($pos,$db_object,$twodarr);
					
					for($a=0;$a<count($users_below);$a++)
					{
						$res2[$a]=$users_below[$a][user_id];
					}
					
				}
				$res1=@array_merge($res1,$res2);
			}
			if($res1[0]=="")
			{
				$filecontent=preg_replace("/<{ifemp_under_admin}>(.*?)<{ifemp_under_admin}>/s","$$1",$filecontent);
			}
			else
			{
				$filecontent=preg_replace("/<{ifemp_under_(.*?)}>/s","",$filecontent);
			}*/
		//-----------------------------------------------------------
			//CULTURAL & EXPERTISE PROFILE

			$a=0;
			
			if($user_id!=1)
			{
			$sql="select user_id from $user_table where admin_id='$user_id'";
			
			$sql_res=$db_object->get_single_column($sql);
			}
			else
			{
				$sql="select user_id from $user_table where user_id<>'$user_id'";
				
				$sql_res=$db_object->get_single_column($sql);
			}

			for($i=0;$i<count($sql_res);$i++)
			{
				$user=$sql_res[$i];
				
				$ch=$common->is_boss($db_object,$user);
				
				if($ch)
				{
					$boss[$a]=$user;
					
					$a++;
				}
			}
		
			if(count($boss)==0)
			{


				$filecontent=preg_replace("/<{ifanyboss_under_admin}>(.*?)<{ifanyboss_under_admin}>/s","",$filecontent);
							
			}
			else
			{
				$filecontent=preg_replace("/<{ifanyboss_under(.*?)}>/s","",$filecontent);
				
				preg_match("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$filecontent,$match);
	
				$match=$match[0];

				for($i=0;$i<count($boss);$i++)
				{
					$boss_id=$boss[$i];
					
					$bossname=$common->name_display($db_object,$boss_id);
					
					$str1.=preg_replace("/<{(.*?)}>/e","$$1",$match);
				}
				$filecontent=preg_replace("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$str1,$filecontent);
			}
			//----------------------------------------
			if($user_id!=1)
			{
	$qry="select user_id from $user_table where admin_id='$user_id'";
	
	$users=$db_object->get_single_column($qry);
			}
			else
			{
	$qry="select user_id from $user_table where user_id<>'$user_id'";
	
	$users=$db_object->get_single_column($qry);
			}
	
	if(count($users)>0)
	{
		$users=@implode(",",$users);
		
		$users="(".$users.")";
	
	$one_week_back=time()-(7*24*60*60);
	
	$date1=date("Y-m-d H:i:s",$one_week_back);
	
	$qry="select user_id from $assign_test_builder where status<>'a' and date<'$date1' and user_id in $users";

	 $qry_res=$db_object->get_rsltset($qry);
	 
	 if($qry_res[0]!="")
	 {
	 	$value["highlight"]="class=highlight";
	 }
	 else
	 {
	 	$value["highlight"]="";
	 }
	 
	 $qry1="select user_id from $assign_skill_table where status<>'h' and date<'$date1' and user_id in $users";
	 
	 $qry_res1=$db_object->get_single_column($qry1);
	 
	 if($qry_res1[0]!="")
	 {
	 	$value[highlight2]="class=highlight";
	 }
	 else
	 {
	 	$value[highlight2]="";
	 }
	 

	}
	
			$pos_qry="select position from $user_table where user_id='$user_id'";
			
			$pos_res=$db_object->get_a_line($pos_qry);
			
			$position=$pos_res[position];
			
			//$users_under=$common->get_chain_below($position,$db_object,$twodarr);
			if($user_id!=1)
			{
			$selqry="select user_id from $user_table where admin_id='$user_id'";
			
			$user_under_id=$db_object->get_single_column($selqry);
			}
			else
			{
				$selqry="select user_id from $user_table ";
			
			$user_under_id=$db_object->get_single_column($selqry);	
			}
			//$user_under_id=$common->get_user_id($db_object,$users_under);
			
			$b=0;
			
			for($a=0;$a<count($user_under_id);$a++)
			{
				$ch_id=$user_under_id[$a];
								
				$ch_boss=$common->is_boss($db_object,$ch_id);
				
				/*$check_under=$common->return_direct_reports($db_object,$ch_id);
				
				$ch_boss="";
				
				for($c=0;$c<count($check_under);$c++)
				{
					$ch=$check_under[$c];
					
					$ch=$common->is_boss($db_object,$ch);
					
					if($ch)
					{
						$ch_boss=1;
					}
					
					
				
				}*/
					
				if($ch_boss)
				{
					
					$users1[$b][user_id]=$ch_id;
					
					$users1[$b][username]=$common->name_display($db_object,$ch_id);
					
					$b++;
				}
			}
			
			if($users1[0]!="")
			{
				
				$filecontent=preg_replace("/<{ifadmin_loop(.*?)}>/s","",$filecontent);
			
			preg_match("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$filecontent,$match);
			
			$match=$match[0];
		
			for($a=0;$a<count($users1);$a++)
			{
				$userid=$users1[$a][user_id];
				
				$username=$users1[$a][username];
				
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			}
			
			$filecontent=preg_replace("/<{boss_loopstart}>(.*?)<{boss_loopend}>/s",$str,$filecontent);
			
			}
			else
			{
				$filecontent=preg_replace("/<{ifadmin_loopstart}>(.*?)<{ifadmin_loopend}>/s","",$filecontent);
			}
	}
	else
	{
		
		$filecontent=preg_replace("/<{ifadmin_loopstart}>(.*?)<{ifadmin_loopend}>/s","",$filecontent);
	}
	
	//UPDATE SUC PLAN ALERT
			
	$sql="select count(assigned_to) from $assign_succession_plan_sub where assigned_to='$user_id'";
	
	$sql_result=$db_object->get_single_column($sql);
	
	if($sql_result[0]>=1)
	{
		$filecontent=preg_replace("/<{sucplanalert_loop(.*?)}>/s","",$filecontent);
		
	}
	else
	{
		$filecontent=preg_replace("/<{sucplanalert_loopstart}>(.*?)<{sucplanalert_loopend}>/s","",$filecontent);
	}
	
		//SUCCESSION PLAN ALERT	
			
			$qry="select plan_id from $assign_succession_plan where assigned_to='$user_id'";

			$res=$db_object->get_single_column($qry);

			if($res[0]=="")
			{
				$filecontent=preg_replace("/<{sucplan_loopstart}>(.*?)<{sucplan_loopend}>/s","",$filecontent);
			}
			else
			{
			
				$filecontent=preg_replace("/<{sucplan_loop(.*?)}>/s","",$filecontent);
			
			}

	
	$selqry="select user_id from $user_table where admin_id='$user_id' limit 0,1";
	
	$user_is_admin=$db_object->get_a_line($selqry);
	$usersworker=$user_is_admin["user_id"];
		
 
		if(!$user_is_admin)
		{
			$filecontent=preg_replace("/<{only_for_admins_area}>(.*?)<{only_for_admins_area}>/s","",$filecontent);
		}
		else
		{
		
		preg_match("/<{only_for_admins_area}>(.*?)<{only_for_admins_area}>/s",$filecontent,$mat);
			
			$replace=$mat[0];
			
		

			if($user_id!=1)
			{
				$selqry="select username,user_id from $user_table where admin_id='$user_id'";
			}
			else
			{
				$selqry="select $user_table.username,$user_table.user_id from $user_table,$position_table where $user_table.position=$position_table.pos_id and ($user_table.position<>NULL or $user_table.position<>0) and $user_table.user_id!=1   order by $position_table.level_no desc";			
			}
			$userset=$db_object->get_rsltset($selqry);
			
			if($userset[0]["user_id"]!="")
			{	for($i=0;$i<count($userset);$i++)
				{
					$temp_id=$userset[$i]["user_id"];
					$userset[$i]["username"]=$common->name_display($db_object,$temp_id);
				}
				
				$userset=$common->return_Keyedarray($userset,"user_id","username");		
				$replaced=$common->singleloop_replace($db_object,"user_loopstart","user_loopend",$replace,$userset,$selqrr);
				
			}
			else
			{
				$replaced=preg_replace("/<{view_dashboard}>(.*)<{view_dashboard}>/s","",$replace);
			}
			
			$replace=preg_replace("/<{(.*?)}>/s","",$replaced);		
			$filecontent=preg_replace("/<{only_for_admins_area}>(.*?)<{only_for_admins_area}>/s",$replace,$filecontent);
			
     
// $filecontent=preg_replace("/<{(.*?)}>/s","",$filecontent);
     
		}
//-if the user is super admin then the alert of skill builderpages should not be shown
		
 	
		$filecontent=$common->direct_replace($db_object,$filecontent,$value);

		echo $filecontent;
		
	}

}


$frobj=new front;

$frobj->front_display($common,$db_object,$image,$user_id,$gbl_gr_select,$fAs,$fModel);

include("footer.php");
?>
