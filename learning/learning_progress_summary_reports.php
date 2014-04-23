<?php
	include("../session.php");
	include("header.php");

class summary

{	

	function display($db_object,$common,$user_id,$default,$error_msg,$learning)
	{

		
		$settings_table	=$common->prefix_table("learning_settings");
		$result_table 		=$common->prefix_table("learning_result");
		$skill_table 		=$common->prefix_table("skills");
		$plan_table		=$common->prefix_table("approved_devbuilder");
		$feedback_table	=$common->prefix_table("learning_feedback_results");
		$dev_interbasic	=$common->prefix_table("dev_interbasic");
		$dev_basic		=$common->prefix_table("dev_basic");
		$user_table = $common->prefix_table("user_table");		
		$path		=$common->path;
		$template	=$path."/templates/learning/learning_progress_summary_reports.html";

		$returncontent	=$common->return_file_content($db_object,$template);
		
		preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$returncontent,$match1);
		$skillcon = $match1[1];

		preg_match("/<{activity_loopstart}>(.*?)<{activity_loopend}>/s",$returncontent,$match2);
		$activitycon = $match2[1];
		
		preg_match("/<{subact_loopstart}>(.*?)<{subact_loopend}>/s",$returncontent,$match3);
		$subactcon=$match3[1];
		
		preg_match("/<{title_loopstart}>(.*?)<{title_loopend}>/s",$returncontent,$match4);
		$titlecon=$match4[1];

		$mysql="select skill_name,$feedback_table.skill_id,rated_id,rater_id,skill_description from $feedback_table,
					$plan_table,$skill_table where $feedback_table.rated_id=$plan_table.user_id 
					and $feedback_table.skill_id=$plan_table.skill_id 
					and $feedback_table.skill_id=$skill_table.skill_id and $feedback_table.status='1'
					and rated_id='$user_id' and $plan_table.pstatus='a' group by $feedback_table.skill_id";
		$dFeed= $db_object->get_rsltset($mysql);

		$mysql	= "select basic_id,coursetype_$default from $dev_basic where basic_id between 1 and 4";
		$dBasic	= $db_object->get_rsltset($mysql);

		for($i=0;$i<count($dFeed);$i++)
		{
		
				$skill_name	=$dFeed[$i]["skill_name"];
				$skills		=$dFeed[$i]["skill_id"];
				$skill_description	=$dFeed[$i]["skill_description"];
				$activityrepl = "";
			
				$mysql 	= "select count(*) as improvedno from $feedback_table where rated_id='$user_id'
							 and rater_id <> rated_id and skill_id='$skills' and value='2'";
				$dImprove = $db_object->get_a_line($mysql);
				$improved_no = $dImprove['improvedno'];
				
				$mysql 	= "select count(*) as total from $feedback_table where rated_id='$user_id'
							 and rater_id <> rated_id and skill_id='$skills'";
				$dTotal 	= $db_object->get_a_line($mysql);
				$total 	=$dTotal['total'];
				
				if( ( $total != 0 ))
				{
					$improved_percentage = $improved_no / $total ;
				}
				else
				{
					$improved_percentage = 0;
				}
					
				for($j=0;$j<count($dBasic);$j++)
				{

					$basic_id	=$dBasic[$j]["basic_id"];
					$sol_names=$dBasic[$j]["coursetype_$default"];

					$mysql	="select interbasic_id,coursename_$default from $dev_interbasic
									 where basic_id='$basic_id'";
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
							/*	if($completed_date == '0000-00-00' )
								{
									$completed_date = "";
								}
								else
								{
									$completed_date=$learning->changedate_display($completed_date);
								}
							*/
									$completed_date=$learning->changedate_display($completed_date);
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
			
			$skillrepl.=preg_replace("/<{(.*?)}>/e","$$1",$skillreplace);
		
		}

		$returncontent =preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$skillrepl,$returncontent); 				
		
		$username=$common->name_display($db_object,$user_id);
		$array['username']=$username;
		$array['user_id'] = $user_id;
		$mail = "select email from $user_table where user_id='$user_id'";
		$res = $db_object->get_a_line($mail);
		$array['mailto'] = $res['email'];
		if($skillrepl=="")
		{
			$returncontent = preg_replace("/<{no_records_start}>(.*?)<{no_records_end}>/s",$error_msg[cEmptyrecords],$returncontent);
		}
		$returncontent = preg_replace("/<{(.*?)}>/s","",$returncontent);
		$returncontent=$common->direct_replace($db_object,$returncontent,$array);
		echo $returncontent;
	}

}
	$obj=new summary;

	$user_id = $fUser_id;

	$obj->display($db_object,$common,$user_id,$default,$error_msg,$learning);

	include("footer.php");
?>
