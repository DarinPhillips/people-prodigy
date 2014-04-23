<?php
include("session.php");
include("header.php");
class Externalcandidate
{
  function show_externaldashboard($common,$db_object,$user_id,$default,$error_msg)
  {
  	$path=$common->path;
	$xFile=$path."templates/external_candidate.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$name_fields=$common->prefix_table("name_fields");
	$user_table=$common->prefix_table("user_table");
	$names="name_".$default;

		$appraisal_table = $common->prefix_table("appraisal");
		$external=$common->prefix_table("external");
		$fields = $common->return_fields($db_object,$appraisal_table);
		$mysql = "select $fields from $appraisal_table where user_id='$user_id'";
		$detail_arr = $db_object->get_rsltset($mysql);


	
	$selqry="select $names,field_name from $name_fields where status='YES'";
	$field_names=$db_object->get_rsltset($selqry);

	$userfields=$common->return_fields($db_object,$user_table);
	
	$selqry="select $userfields from $user_table where user_id='$user_id' and user_type='external'";
	$user_detail=$db_object->get_a_line($selqry);
	if($user_detail["username"]=="" && $user_id!=1)
	{
		echo $error_msg['cPleaseRegister'];
		//echo "Please Register For Joining in Our Team";
		include("footer.php");
		exit;
	}
	preg_match("/<{namesfield_loopstart}>(.*?)<{namesfield_loopend}>/s",$xTemplate,$mat);
	$replace=$mat[1];
	for($i=0;$i<count($field_names);$i++)
	{
		$field_name=$field_names[$i][$names];
		$internalname=$field_names[$i]["field_name"];
		$field_value=$user_detail[$internalname];
		$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);		
	}

$xTemplate=preg_replace("/<{namesfield_loopstart}>(.*?)<{namesfield_loopend}>/s",$replaced,$xTemplate);

 
$values["username"]=$user_detail["username"];
$values["password"]=$user_detail["password"];
	$replaced = "";

preg_match("/<{dyna_replace}>(.*?)<{dyna_replace}>/s",$xTemplate,$mat2);
$replace=$mat2[1];
		for($i=0;$i<count($detail_arr);$i++)
		{
			$test_mode = $detail_arr[$i]['test_mode'];
			$test_typevar = $detail_arr[$i]['test_type'];
			$test_type = $gbl_skill_type[$test_typevar];
			
			$user_id = $detail_arr[$i]['user_id'];
			
			//if the testmode is 360 then configure the groups to rate...  
			
			 if(($test_mode == 360) && ($test_typevar == 'i'))
			 {
			 	$values["location"] = "alert_multirater";
			 	$values["message"]=$error_msg["cInterpersonalskillappraisal"];
			 }
			 if(($test_mode == 360) && ($test_typevar == 't'))
			 {
			 	$values["location"] = "tech_multirater";
			 	$values["message"]=$error_msg["cTechnicalskillappraisal"];
			 }
			 if (($test_mode == 'Test') && ($test_typevar == 'i'))
			 {
			 	$values["location"] = "write_test";
			 	$values["message"]=$error_msg["cInterpersonalskillappraisal"];
			 }
			 if (($test_mode == 'Test') && ($test_typevar == 't'))
			 {
			 	$values["location"] = "write_test";
			 	$values["message"]=$error_msg["cTechnicalskillappraisal"];
			 }
			
			$values[user_id] = $user_id;
			$values[test_mode]=$test_mode;
			$values[test_type]=$test_type;
			$values[test_typevar] = $test_typevar;
			
			$replaced .= $common->direct_replace($db_object,$replace,$values);
		}		

$xTemplate=preg_replace("/<{dyna_replace}>(.*?)<{dyna_replace}>/s",$replaced,$xTemplate);


$sql="select * from $external where user_id='$user_id'";
$sql_result=$db_object->get_a_line($sql);

$resume=$sql_result[Resumes];

$letter=$sql_result[Letters];
$certificate=$sql_result[Certificates];
$photo=$sql_result[Photos];
$license=$sql_result[Licenses];

$resume_dir=$common->path."/uploads/externalcandidate/Resumes/$resume";
$resume_letter=$common->path."/uploads/externalcandidate/Letters/$letter";
$resume_photo=$common->path."/uploads/externalcandidate/Photos/$photo";
$resume_license=$common->path."/uploads/externalcandidate/Licenses/$license";
$resume_cert=$common->path."/uploads/externalcandidate/Certificates/$certificate";

if($resume!="" and file_exists($resume_dir))
{

	$values[viewresume]="View Resume";
}

if($letter!="" and file_exists($resume_letter))
{
	$values[viewletter]="View Letter";
}


if($photo!="" and file_exists($resume_photo))
{
	$values[viewphoto]="View Photo";
}

if($license!="" and file_exists($resume_license))
{
	$values[viewlicense]="View License";
}
if($certificate!="" and file_exists($resume_cert))
{
	$values[viewcertificate]="View Certificate";
}

$values[user_id]=$user_id;
$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;
	echo "My Permissions are not assigned properly";
  	
  }
  function upload_files($common,$db_object,$user_id,$error_msg,$form_array,$gbl_upload_file)
  {

	while(list($kk,$vv)=@each($form_array))
  	{
  		$$kk=$vv;
		if($kk=="fPassword")
  		{
  		}
  		else if($kk!="fSubmit")
  		{
  			$sub.="$kk='$vv',";
  		}
  			
  		

  	}
  	$usertable=$common->prefix_table("user_table");
  	$external=$common->prefix_table("external");
  	
  	$insqry="update $usertable set password='$fPassword',$sub user_type='external' where  user_id='$user_id'";
  	$db_object->insert($insqry);
  	$filename=$user_id;
  	
  	

  	
  		while(list($kk,$vv)=@each($gbl_upload_file))
		{		

			$cvfile=$_FILES[$kk]["tmp_name"];
			$type=$_FILES[$kk]["type"];
		


			//if((($cvfile!=none) or (trim($cvfile)!="")) or (($type!=none) or (trim($type)!="")))


			if(($cvfile!=none))// and (trim($cvfile)!=""))
			{

				$sub="/externalcandidate/$vv";
			/*	echo $sub;
			echo $cvfile;
			echo $filename;
			echo $type;*/
			
				$cvfilename=$common->upload_cv($cvfile,$filename,$type,$sub);	
				
				if($cvfilename!="")
				{
					$ch_qry="select * from $external where user_id='$user_id'";
					$ch_res=$db_object->get_a_line($ch_qry);
					if($ch_res[0]!="")
					{
						$upqry="update $external set $vv='$cvfilename' where user_id='$user_id'";

						$db_object->insert($upqry);
					}
					else
					{
						$ins_qry="insert into $external set $vv='$cvfilename',user_id='$user_id'";
						$db_object->insert($ins_qry);
						
					}
					
				}
				
				
			}
		}
		//echo "Candidate Details has been Uploaded";
		echo $error_msg['cUploaded'];
  }

}
$exobj=new Externalcandidate;

$user_table=$common->prefix_table("user_table");
$selqry="select user_type,user_id from $user_table where user_id='$user_id'";
$user_type=$db_object->get_a_line($selqry);

if($user_type["user_type"]=="external"||$user_id==1)
{
	if($fSubmit)
	{
		
		$user_id=$user_type["user_id"];
		$exobj->upload_files($common,$db_object,$user_id,$error_msg,$post_var,$gbl_upload_file);
	}

	$exobj->show_externaldashboard($common,$db_object,$user_id,$default,$error_msg);
}
else
{
	
	//echo "External User`s Area";
	
	echo $error_msg['cExternalUsersArea'];
}
include("footer.php");
?>
