<?php
include("../session.php");
class update_plan
{

	

	function display($db_object,$common,$user_id,$default,$error_msg,$learning)
	{

		$settings_table		=$common->prefix_table("learning_settings");
		$result_table 		=$common->prefix_table("learning_result");
		$skill_table 		=$common->prefix_table("skills");
		$plan_table		=$common->prefix_table("approved_devbuilder");
		$feedback_table	=$common->prefix_table("learning_feedback_results");
		$dev_interbasic	=$common->prefix_table("dev_interbasic");
		$dev_basic		=$common->prefix_table("dev_basic");

		$path		=$common->path;
		$template		=$path."/templates/learning/update_learning_plan.html";

		$returncontent	=$common->return_file_content($db_object,$template);
		
		preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$returncontent,$match1);
		$skillcon = $match1[1];

		preg_match("/<{activity_loopstart}>(.*?)<{activity_loopend}>/s",$returncontent,$match2);
		$activitycon = $match2[1];
		
		preg_match("/<{subact_loopstart}>(.*?)<{subact_loopend}>/s",$returncontent,$match3);
		$subactcon=$match3[1];
		
		preg_match("/<{title_loopstart}>(.*?)<{title_loopend}>/s",$returncontent,$match4);
		$titlecon=$match4[1];

		$mysql 	= "select $skill_table.skill_name,$skill_table.skill_type,$plan_table.skill_id,$plan_table.title,
			      	$plan_table.cdate from $plan_table,$skill_table where 
					$plan_table.skill_id=$skill_table.skill_id and $plan_table.pstatus='a' and 
					$plan_table.user_id='$user_id'
				 	group by $plan_table.skill_id ";
		$dPlan= $db_object->get_rsltset($mysql);

		$mysql	= "select basic_id,coursetype_$default from $dev_basic where basic_id between 1 and 4";
		$dBasic	= $db_object->get_rsltset($mysql);
			
		for($i=0;$i<count($dPlan);$i++)
		{
		
				$skill_name	=$dPlan[$i]["skill_name"];
				$skills		=$dPlan[$i]["skill_id"];
				$skill_type 	=$dPlan[$i]['skill_type']; 
				$activityrepl = "";
			
				for($j=0;$j<count($dBasic);$j++)
				{

					$basic_id=$dBasic[$j]["basic_id"];
					$sol_names=$dBasic[$j]["coursetype_$default"];

					$mysql	="select interbasic_id,coursename_$default from $dev_interbasic where basic_id='$basic_id'";
					$dInterbasic=$db_object->get_rsltset($mysql);

					$subactrepl="";

					for($k=0;$k<count($dInterbasic);$k++)
					{
						$ib_id=$dInterbasic[$k]["interbasic_id"];
						$subactivities_names=$dInterbasic[$k]["coursename_$default"];
		
						$mysql="select * from $plan_table where interbasic_id='$ib_id' 
							and skill_id='$skills' and user_id='$user_id'and title!=''
							 and pstatus='a'";
						$dTitle=$db_object->get_rsltset($mysql);

						$titlerepl ="";
						for($l=0;$l<count($dTitle);$l++)
						{

							$title	=$dTitle[$l]["title"];
							$buildid	=$dTitle[$l]["build_id"];
							$cdate	=$dTitle[$l]["cdate"];
							$completed_date=$dTitle[$l]["completed_date"];
							$cdate=$learning->changedate_display($cdate);
							
							if($completed_date !="")
							{
								if($completed_date == '0000-00-00' )
								{
									$completed_date = "";
								}
								else
								{
									$completed_date=$learning->changedate_datetime($completed_date);
								}
							}	
			
							$titlerepl.=preg_replace("/<{(.*?)}>/e","$$1",$titlecon);
						}

						$subact     =preg_replace("/<{title_loopstart}>(.*?)<{title_loopend}>/s",$titlerepl,$subactcon);
						$subactrepl.=preg_replace("/<{(.*?)}>/e","$$1",$subact);
					
					}

					$activity=preg_replace("/<{subact_loopstart}>(.*?)<{subact_loopend}>/s",$subactrepl,$activitycon);
					$activityrepl.=preg_replace("/<{(.*?)}>/e","$$1",$activity);
				}
	
			$skillreplace = preg_replace("/<{activity_loopstart}>(.*?)<{activity_loopend}>/s",$activityrepl,$skillcon);
			if( $skill_type == 'i' )
			{
				$skillreplace = preg_replace("/<{request_loopstart}>(.*?)<{request_loopend}>/s","",$skillreplace);	 
			}
			$skillrepl.=preg_replace("/<{(.*?)}>/e","$$1",$skillreplace);
		
		}

		$returncontent =preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$skillrepl,$returncontent); 				
				
		$returncontent=$common->direct_replace($db_object,$returncontent,$array);
		echo $returncontent;
	}

//-----------------------------------------------------------------------------------
	function update($db_object,$common,$post_var,$user_id,$error_msg,$default,$learning)
	{
	
		$plan_table		=$common->prefix_table("approved_devbuilder");
		$unapproved_table	=$common->prefix_table("unapproved_devbuilder");
		$temp_table 		=$common->prefix_table("temp_devbuilder");
		$feedback_table	=$common->prefix_table("learning_feedback_results");
		$solution_table	=$common->prefix_table("assign_solution_builder");

		while(list($kk,$vv)=each($post_var))
		{
			$$kk=$vv;
			if(ereg("^fActstart",$kk))
			{
				list($act,$skill,$buildid)=split("_",$kk);
				$buildarr[$buildid]=$vv;
			}
			if(ereg("^request",$kk))
			{
				list($req,$rskill)=split("_",$kk);
				$requestarr[]=$rskill;
			}
			if(ereg("^delete",$kk))
			{
				list($del,$dskill)=split("_",$kk);
				$deletearr[]=$dskill;
			}
		}
			
		$keys  = array_keys($buildarr);			
		/* FOR UPDATING COMPLETED BY DATE */
			for($i=0;$i<count($keys);$i++)
			{
				$build_id = $keys[$i];				
				$completed_date = $buildarr[$build_id];
				$completed_date = $learning->change_date($completed_date);
				$mysql = "update  $plan_table set completed_date='$completed_date',update_status='u'
						   where build_id='$build_id' and user_id='$user_id' and pstatus='a'";				

				$db_object->insert($mysql);
			}
	
		/* TO DELETE PLAN */
			for($j=0;$j<count($deletearr);$j++)
			{
				$skill_id		 = $deletearr[$j];			
				/*   DELETE FROM APPROVED DEV BUILDER  TABLE */
				$mysql = "delete from $plan_table where skill_id='$skill_id'
						   			 and user_id='$user_id'";
				$db_object->insert($mysql);
				
				/*   DELETE FROM UNAPPROVED DEV BUILDER  TABLE */
				$mysql = "delete from $unapproved_table where skill_id='$skill_id'
						   			 and user_id='$user_id'";
				$db_object->insert($mysql);
				
				/*   DELETE FROM TEMP DEV BUILDER  TABLE */
				$mysql = "delete from $temp_table where skill_id='$skill_id'
						   			 and user_id='$user_id'";
				$db_object->insert($mysql);
				
				/*   DELETE FROM LEARNING FEEDBACK  TABLE */
				$mysql = "delete from $feedback_table where skill_id='$skill_id'
						   			 and rated_id='$user_id'";
				$db_object->insert($mysql);
				
				/*   DELETE FROM ASSIGN SOLUTION BUILDER TABLE */
				$mysql = "delete from $solution_table where skill_id='$skill_id'
						   			 and user_id='$user_id'";
				$db_object->insert($mysql);
			}
			
		/* TO REQUEST FEEDBACK */			
		
			
			echo $error_msg['cPlanUpdated'];
		
			include("footer.php");
			exit;
		

	}
}
	$obj=new update_plan;
	
$update = $post_var['update'];
$return = $post_var['return'];
if($update)
{
	include("header.php");
	$obj->update($db_object,$common,$post_var,$user_id,$error_msg,$default,$learning);
}
if($return)
{
	header("location:front_panel.php");	
}

	include("header.php");
	$obj->display($db_object,$common,$user_id,$default,$error_msg,$learning);


	include("footer.php");
?>
