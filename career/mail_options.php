<?php
include("../session.php");
include("header.php");
class Mail_Option
{
 function display_mail_option($common,$db_object,$user_id)
  {

	$path=$common->path;
	$xFile=$path."/templates/career/mail_option.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$config=$common->prefix_table("config");
	$flds=$common->return_fields($db_object,$config);
	$query="select $flds from $config";
	$result=$db_object->get_a_line($query);
	
	$values["directreplace"]["Import_subject"]=$result["isubject"];
	$values["directreplace"]["Import_message"]=$result["imessage"];
	
	$values["directreplace"]["Manual_subject"]=$result["msubject"];
	$values["directreplace"]["Manual_message"]=$result["mmessage"];
	
	$values["directreplace"]["Builder_subject"]=$result["ssubject"];
	$values["directreplace"]["Builder_message"]=$result["smessage"];
	
	$values["directreplace"]["TestBuilder_message"]=$result["tbmessage"];
	$values["directreplace"]["TestBuilder_subject"]=$result["tbsubject"];
	
	$values["directreplace"]["TestAppraisal_subject"]=$result["tasubject"];
	$values["directreplace"]["TestAppraisal_message"]=$result["tamessage"];
	
	$values["directreplace"]["MultiraterAppraisal_message"] = $result["mamessage"];
	$values["directreplace"]["MultiraterAppraisal_subject"] = $result["masubject"];
	
	$values["directreplace"]["MultiraterAppraisaltech_message"] = $result["matechmessage"];
	$values["directreplace"]["MultiraterAppraisaltech_subject"] = $result["matechsubject"];


	$values["directreplace"]["test_message"]=$result["test_message"];
	$values["directreplace"]["test_subject"]=$result["test_subject"];

	$values["directreplace"]["skill_message"]=$result["skill_message"];
	$values["directreplace"]["skill_subject"]=$result["skill_subject"];

	$values["directreplace"]["admin_message"]=$result["admin_message"];
	$values["directreplace"]["admin_subject"]=$result["admin_subject"];

	$values["directreplace"]["boss_message"]=$result["boss_message"];
	$values["directreplace"]["boss_subject"]=$result["boss_subject"];
	
	$values["directreplace"]["admin_to_boss_subject"]=$result["succession_plan_subject"];
	$values["directreplace"]["admin_to_boss_message"]=$result["succession_plan_message"];
	
	$values["directreplace"]["succession_update_subject"]=$result["succession_update_subject"];
	$values["directreplace"]["succession_update_message"]=$result["succession_update_message"];
	
	$values["directreplace"]["mail_to_boss_subject"]=$result["mail_to_boss_subject"];
	$values["directreplace"]["mail_to_boss_message"]=$result["mail_to_boss_message"];
	
	$values["directreplace"]["succession_skillsupdate_subject"]=$result["succession_skillupdate_subject"];
	$values["directreplace"]["succession_skillupdate_message"]=$result["succession_skillupdate_message"];
	
	
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;
   }

   function updatemail($common,$db_object,$form_array)
   {
   	while(list($kk,$vv)=@each($form_array))
   	{
   		$$kk=$vv;
   	}
   	 	
   	$config=$common->prefix_table("config");
   	$insqry="update $config set 
			isubject='$fImport_subject',imessage='$fImport_message',
			msubject='$fManual_subject',mmessage='$fManual_message',
			ssubject='$fBuilder_subject',smessage='$fBuilder_message',
			tbsubject='$fTestBuilder_subject',tbmessage='$fTestBuilder_message',
			tasubject='$fTestAppraisal_subject',tamessage='$fTestAppraisal_message',
			mamessage='$fMultiraterAppraisal_message',masubject='$fMultiraterAppraisal_subject',
			matechmessage = '$fMultiraterAppraisaltech_message',matechsubject='$fMultiraterAppraisaltech_subject',
			test_message='$fTest_message',test_subject='$fTest_subject',
			skill_subject='$fSkill_subject',skill_message='$fSkill_message',
			admin_subject='$fAdmin_subject',admin_message='$fAdmin_message',
			boss_subject='$fBoss_subject',boss_message='$fBoss_message',
			succession_plan_subject='$fFrom_admin_subject',succession_plan_message='$fFrom_admin_message',
			succession_update_subject = '$fSuccesssion_UpdationSubject',succession_update_message='$fSuccesssion_UpdationMessage',
			mail_to_boss_subject='$fMail_to_boss_subject',mail_to_boss_message='$fMail_to_boss_message',
			succession_skillupdate_subject = '$fSuccesssion_SkillUpdationSubject',succession_skillupdate_message = '$fSuccesssion_SkillUpdationMessage'
			where id='1'";
   	$db_object->insert($insqry);
   	
   }

   
}
$moobj=new Mail_Option;
if($fUpdatemail && $user_id==1)
{
$moobj->updatemail($common,$db_object,$_POST);
}
$moobj->display_mail_option($common,$db_object,$user_id);
include("footer.php");
?>
