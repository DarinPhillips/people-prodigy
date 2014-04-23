<?php
	include("../session.php");
	include("header.php");

	class Requests
	{

	

	function feedback_request($db_object,$common,$user_id,$default,$ratedid,$error_msg,$learning,$mode)
	{

		$settings_table		=$common->prefix_table("learning_settings");
		$result_table 			=$common->prefix_table("learning_result");
		$skill_table 			=$common->prefix_table("skills");
		$plan_table			=$common->prefix_table("approved_devbuilder");
		$feedback_table		=$common->prefix_table("learning_feedback_results");

		$path		=$common->path;
		$template		=$path."/templates/learning/feedback_request_form.html";

		$returncontent	=$common->return_file_content($db_object,$template);
		
		preg_match("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$returncontent,$match1);
		$skillcon = $match1[1];

		preg_match("/<{answer_loopstart}>(.*?)<{answer_loopend}>/s",$returncontent,$match2);
		$answercon = $match2[1];
			
		$mysql 	= "select requesttext_$default as rtext from $settings_table where id='1'";
		$dSettings= $db_object->get_a_line($mysql);
		$rtext      =$dSettings['rtext'];
		
		$mysql 	= "select id,value,result_$default as result from $result_table ";
		$dResult  = $db_object->get_rsltset($mysql);
	
		$dSkill=$learning->skills_to_be_rated($db_object,$common,$user_id,$ratedid,$mode);

	/*	$mysql = "select $skill_table.skill_name,$plan_table.skill_id from $plan_table left join
				$feedback_table on $plan_table.user_id=$feedback_table.rated_id,$skill_table 
				where $plan_table.skill_id=$skill_table.skill_id and pstatus='a'
				and user_id='$ratedid' and ( ( to_days(curdate()) - to_days(rated_date)>=frequency )
				or $feedback_table.status <> '1' )  group by $plan_table.skill_id";
		$dSkill= $db_object->get_rsltset($mysql);
	*/
		$username  = $common->name_display($db_object,$ratedid);
		$cChooseAnswer = $error_msg['cChooseAnswer'];
		
		for($i=0;$i<count($dSkill);$i++)
		{
			
			$skill_name = $dSkill[$i]['skill_name'];
			$skill_id   = $dSkill[$i]['skill_id'];
			$requesttext= preg_replace("/{{(.*?)}}/e","$$1",$rtext);
			$cEffectiveAt=$error_msg['cEffectiveAt'];
			$cEffectiveAt= preg_replace("/{{(.*?)}}/e","$$1",$cEffectiveAt);
			
			$mysql 		= "select value,feedback_text from $feedback_table where rated_id='$ratedid' and rater_id='$user_id'
							and skill_id='$skill_id'";
			$dFeedback 	= $db_object->get_a_line($mysql);
			$feedback_text = $dFeedback['feedback_text'];
			
			$answerrepl = "";
				for($j=0;$j<count($dResult);$j++)
				{
					$id		= $dResult[$j]['id'];
					$value 	= $dResult[$j]['value'];
					if($value == $dFeedback['value'])
					{
						$checked="checked";
					}
					else
					{
						$checked="";
					}
					$result 	= $dResult[$j]['result'];
					$result   = preg_replace("/{{(.*?)}}/e","$$1",$result);
		
					$answerrepl.= preg_replace("/<{(.*?)}>/e","$$1",$answercon);
				}
				
			$skillcon1 =preg_replace("/<{answer_loopstart}>(.*?)<{answer_loopend}>/s",$answerrepl,$skillcon); 
			$skillrepl.= preg_replace("/<{(.*?)}>/e","$$1",$skillcon1);			
			
		}

		$returncontent =preg_replace("/<{skill_loopstart}>(.*?)<{skill_loopend}>/s",$skillrepl,$returncontent); 
		$array['ratedid']=$ratedid;
		$returncontent=$common->direct_replace($db_object,$returncontent,$array);
		echo $returncontent;
	}

	function update($db_object,$common,$post_var,$user_id,$error_msg,$status)
	{

		while(list($kk,$vv)=each($post_var))
		{
			$$kk=$vv;
			if(ereg("^ans",$kk))
			{
				list($ans,$skill)=split("_",$kk);
				if($vv != "" )
				{
					$skillarr[$skill]['value']=$vv;
				}
			}
			if(ereg("^text",$kk))
			{
				list($text,$skill)=split("_",$kk);
				if($vv != "" )
				{
					$skillarr[$skill]['text']=$vv;
				}
			}
		}
		
		$result_table 			=$common->prefix_table("learning_result");
		$skill_table 			=$common->prefix_table("skills");
		$plan_table			=$common->prefix_table("approved_devbuilder");
		$feedback_table		=$common->prefix_table("learning_feedback_results");
		
		$keys  = array_keys($skillarr);
		$skill_str=@implode(",",$keys);

			for($i=0;$i<count($keys);$i++)
			{
				$skill_id = $keys[$i];
				$value    = $skillarr[$skill_id]['value'];
				$text    = $skillarr[$skill_id]['text'];
				
				$mysql = "select f_id from $feedback_table where rated_id='$rated_id' 
			 				and rater_id='$user_id' and skill_id='$skill_id'";
			 			
				$dResult = $db_object->get_a_line($mysql);

				if($dResult[0] == "" )
				{
					$mysql = "insert into $feedback_table set rater_id='$user_id',rated_id='$rated_id',
							skill_id='$skill_id',value='$value',feedback_text='$text',status='$status',rated_date=now()";
						
				}
				else
				{
					$fid  = $dResult['f_id'];
					$mysql = "update  $feedback_table set rater_id='$user_id',rated_id='$rated_id',
							skill_id='$skill_id',value='$value',feedback_text='$text',status='$status',
							rated_date=now() where f_id='$fid'";
				}
				$db_object->insert($mysql);
				
			}
	
		if($status == '1' )
		{
			echo $error_msg['cFeedbackSubmitted'];
		}
		if($status == '0' )
		{
			echo $error_msg['cFeedbackSaved'];
		}
		
	include("footer.php");
	exit;


	}
	}
	$obj=new Requests;

$save = $post_var['save'];
$submit = $post_var['submit'];
if($save)
{
	$status="0";
	$obj->update($db_object,$common,$post_var,$user_id,$error_msg,$status);
}
if($submit)
{
	$status="1";
	$obj->update($db_object,$common,$post_var,$user_id,$error_msg,$status);
}

	$obj->feedback_request($db_object,$common,$user_id,$default,$ratedid,$error_msg,$learning,$mode);


	include("footer.php");
?>
