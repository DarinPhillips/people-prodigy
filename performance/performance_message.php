<?
include_once("../session.php");
include_once("header.php");
class message
	{
	function view($db_object,$common,$default)
		{
			$path = $common->path;
			$message = $common->prefix_table("performance_message");
			
			
			$default=1;//hard coded to avoid multi lingual option in mail .If u want to have multilingual option comment this line.
			
			
			
			$qry = "select appsub_subject_$default as appsubject ,appsub_message_$default as appmessage,
				approved_subject_$default as apsubject,approved_message_$default as apmessage,
				resubmit_subject_$default as resubject,resubmit_message_$default as remessage,
				reject_subject_$default as reject_subject,reject_message_$default as reject_message,
				obj_subject_$default as objsub, obj_message_$default as objmes,
				obj_app_subject_$default as objappsubject, obj_app_message_$default as 
				objappmessage,verification_submit_sub_$default as vsubmit_subject,
				verification_submit_message_$default as vsubmit_message,verification_rej_sub_$default as 
				vreject_subject,verification_rej_message_$default as vreject_message ,verification_remind_sub_$default as
				remind_subject,verification_remind_message_$default as remind_message,app_feedback_subject_$default as
				feedback_subject,app_feedback_message_$default as feedback_message,plan_subject_$default as plan_subject,
				plan_message_$default as plan_message,plan_approved_subject_$default as plan_approved_subject,
				plan_approved_message_$default as plan_approved_message,appraisal_approved_subject_$default as appraisal_approved_subject,
				
				appraisal_approved_message_$default as appraisal_approved_message,appraisal_approved_subject_$default as appraisal_approved_subject_final,
				
				appraisal_approved_message_final_$default as appraisal_approved_message_final,
				
				plan_expiry_message_1 as plan_expiry_message,plan_expiry_subject_1 as plan_expiry_subject from $message";
				
			$res = $db_object->get_a_line($qry);
			
		
			
			$file = "$path"."templates/performance/performance_message.html";
			$content = $common->return_file_content($db_object,$file);
			$content = $common->direct_replace($db_object,$content,$res);
			echo $content;

		}//end view
		function save($db_object,$common,$default,$_POST)
		{
			while(list($key,$value)=each($_POST))
			{
				$$key = $value;
			}
			
			$message = $common->prefix_table("performance_message");
		/*	$qry = "replace into $message set m_id='1',appsub_subject_$default='$fAppsub_subject',
				appsub_message_$default='$fAppsub_message',approved_subject_$default='$fApproved_subject',
				approved_message_$default='$fApproved_message',resubmit_subject_$default='$fResubmit_subject',
				resubmit_message_$default='$fResubmit_message',reject_subject_$default='$fReject_subject',
				reject_message_$default='$fReject_message'";
				*/


			$default=1;//hard coded to avoid multi lingual option in mail .If u want to have multilingual option comment this line.
			
			
			
			$qry="update $message set appsub_subject_$default='$fAppsub_subject',
				appsub_message_$default='$fAppsub_message',approved_subject_$default='$fApproved_subject',
				approved_message_$default='$fApproved_message',resubmit_subject_$default='$fResubmit_subject',
				resubmit_message_$default='$fResubmit_message',reject_subject_$default='$fReject_subject',
				reject_message_$default='$fReject_message',obj_subject_$default='$fObjective_subject',
				obj_message_$default='$fObjective_message',obj_app_subject_$default='$fobjapp_subject',
				obj_app_message_$default='$fobjapp_message',verification_submit_sub_$default='$fVsubmit_subject',
				verification_submit_message_$default ='$fVsubmit_message', verification_rej_sub_$default='$fVReject_subject',
				verification_rej_message_$default='$fVReject_message',verification_remind_sub_$default='$fRemind_subject',
				verification_remind_message_$default='$fRemind_message',app_feedback_subject_$default='$fFeedback_subject',
				app_feedback_message_$default='$fFeedback_message',plan_subject_$default='$fPlan_subject',
				
				plan_message_$default='$fPlan_message',plan_approved_subject_$default='$fPlan_Approved_subject',
				
				plan_approved_message_$default='$fPlan_Approved_message',appraisal_approved_subject_$default='$fAppraisal_Approved_subject',
				
				appraisal_approved_message_$default='$fAppraisal_Approved_message',appraisal_approved_subject_final_$default='$fAppraisal_Approved_subject_final',
				
				appraisal_approved_message_final_$default='$fAppraisal_Approved_message_final',
				
				plan_expiry_message_1='$fPIP_Exp_message',plan_expiry_subject_1='$fPIP_Exp_subject'  where m_id='1'";
				
				//echo $qry;
	
			$db_object->insert($qry);
		}
	}//end class
		$ob= new message;
		if($submit!="")
		{
			$ob->save($db_object,$common,$default,$_POST);
		}
		$ob->view($db_object,$common,$default,$_POST);
	

include_once("footer.php");
?>
