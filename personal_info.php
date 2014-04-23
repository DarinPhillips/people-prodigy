<?php
include("session.php");
include("header.php");
class Personal_info
{
  function display_personalinfo($common,$db_object,$user_id,$default)
  {
//-------------------initialisations--------------------------
  	$name="name_".$default;
  	$type="type_".$default;

  	
//---------template retrival--------------------- 
	$path=$common->path;
	$xFile=$path."templates/personal_info.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
//-------------------------------

//------------------prelimcheck---------------------
	$preqry="select user_id from temp_user_table where user_id='$user_id'";
	$checks=$db_object->get_a_line($preqry);
	if($checks["user_id"] && $user_id!=1)
	{
		$user_table	=$common->prefix_table("temp_user_table");
		$user_eeo	=$common->prefix_table("temp_user_eeo");
	}
	else
	{
		$user_table	=$common->prefix_table("user_table");
		$user_eeo	=$common->prefix_table("user_eeo");
	}




//---------------------------tables---------------------------------


	

	

//	$user_table	=$common->prefix_table("temp_user_table");
//	$user_eeo	=$common->prefix_table("user_eeo");


	
	$name_fields	=$common->prefix_table("name_fields");
	$eeo_main	=$common->prefix_table("eeo_main");
//	$eeo_tags	=$common->prefix_table("eeo_tags");
	
	$eeo_tags	=$common->prefix_table("opportunity_status");

	
	$position	=$common->prefix_table("position");
	$employment_type=$common->prefix_table("employment_type");
	$location	=$common->prefix_table("location");
//-------------------------------------------------------------------	

//-----------------database retrival---------------------------------


	$seluserdetails	="select user_id,
			  password,office_phone,
			  cell_phone,pager,fax,office_mail_address,
			  office_physical_address,
			  location,position,
			  employment_type from $user_table where user_id='$user_id'";
	$user_details	=$db_object->get_a_line($seluserdetails);
	
	$selnamefields	="select $name,field_name from $name_fields where status='YES'";
	$namefieldsrslt	=$db_object->get_rsltset($selnamefields);


	

	$sellevels	="select distinct(level_no) from $position";
	$levels		=$db_object->get_single_column($sellevels);
	
	$selemptype	="select id,$type from $employment_type where status='Yes'";
	$emptype	=$db_object->get_rsltset($selemptype);

//	$seleeo		="select tag_id,tag_name from $eeo_tags";
	$seleeo		="select  $eeo_tags.eeo_id as tag_id,$eeo_tags.tag as tag_name from $eeo_tags";	
	$eeoset		=$db_object->get_rsltset($seleeo);

//	$selusereeo	="select $eeo_tags.tag_name as tag_name,$user_eeo.tag_id as tag_id from $user_eeo,$eeo_tags where $eeo_tags.tag_id=$user_eeo.tag_id and $user_eeo.user_id='$user_id'";
	$selusereeo	="select $eeo_tags.tag as tag_name,$user_eeo.tag_id as tag_id from $user_eeo,$eeo_tags where $eeo_tags.eeo_id=$user_eeo.tag_id and $user_eeo.user_id='$user_id'";	
	$user_eeoset	=$db_object->get_rsltset($selusereeo);

	$seluserlevel	="select $position.level_no as level_no,$position.position_name as position_name,$position.boss_no as boss_number from $position,$user_table where $user_table.position=$position.pos_id and $user_table.user_id='$user_id'";
	$user_level	=$db_object->get_a_line($seluserlevel);


	$temp_boss	=$user_level["boss_number"];
	$seluserboss 	="select position_name from $position where pos_id='$temp_boss'";
	$user_boss	=$db_object->get_a_line($seluserboss);

	$temp_emptype	=$user_details["employment_type"];

	$seluser_emptype="select $type from $employment_type where id='$temp_emptype'";
	$employeetype	=$db_object->get_a_line($seluser_emptype);

	$templocation	=$user_details["location"];
	$seluserlocation="select loc_name from $location where loc_id='$templocation'";
	$location	=$db_object->get_a_line($seluserlocation);
	

//-------------------------------------------------------------------------------------	
//----------------------splice the template--------------------------------------------

	//-----------replaces the fieldname text boxes---------------------------------	
		preg_match("/<{changeable_textstart}>(.*?)<{changeable_textend}>/s",$xTemplate,$match1);
		$replace1=$match1[1];
		for($i=0;$i<count($namefieldsrslt);$i++)
		{

			$field=$namefieldsrslt[$i]["field_name"];
			$nametodis=$namefieldsrslt[$i][$name];
			$selnfvalues="select $field from $user_table where user_id='$user_id'";
			$fieldvalue=$db_object->get_a_line($selnfvalues);
		
		
			$value=$fieldvalue[$field];

			
			$replaced1.=preg_replace("/{{(.*?)}}/e","$$1",$replace1);
		}
$xTemplate=preg_replace("/<{changeable_textstart}>(.*?)<{changeable_textend}>/s",$replaced1,$xTemplate);

	//------------------------replaces levels--------------------------
		preg_match("/<{level_loopstart}>(.*?)<{level_loopend}>/s",$xTemplate,$match2);
		$replace2=$match2[1];

		for($i=0;$i<count($levels);$i++)
		{
			$value=$levels[$i];

			$replaced2.=preg_replace("/<{(.*?)}>/e","$$1",$replace2);
		}

$xTemplate=preg_replace("/<{level_loopstart}>(.*?)<{level_loopend}>/s",$replaced2,$xTemplate);

	//---------------prints location box------------------------------------
		$match_arr=$templocation;
		$replaced3=$common->list_category($db_object,$common,$catid,$app,$match_arr);
		$xTemplate=preg_replace("/<{Location_loopstart}>(.*?)<{Location_loopend}>/s",$replaced3,$xTemplate);
	//------------------------------prints the employment type----------------------------

		preg_match("/<{employement_loopstart}>(.*?)<{employment_loopend}>/s",$xTemplate,$match4);
		$replace4=$match4[1];
		$emp_type_id=$user_details["employment_type"];
		for($i=0;$i<count($emptype);$i++)
		{
			$key=$emptype[$i]["id"];
			$value=$emptype[$i][$type];
			if($key==$emp_type_id)
			{
				$selected="selected";
			}
			else
			{
				$selected="";
			}
			$replaced4.=preg_replace("/<{(.*?)}>/e","$$1",$replace4);
		}
		$selected="";
$xTemplate=preg_replace("/<{employement_loopstart}>(.*?)<{employment_loopend}>/s",$replaced4,$xTemplate);

	//--------replaces all eeo status--------------------------
		preg_match("/<{alleeo_loopstart}>(.*?)<{alleeo_loopend}>/s",$xTemplate,$match5);
		$replace5=$match5[1];
		for($i=0;$i<count($eeoset);$i++)
		{
			$value=$eeoset[$i]["tag_name"];
			$key=$eeoset[$i]["tag_id"];
			$replaced5.=preg_replace("/<{(.*?)}>/e","$$1",$replace5);
		}
$xTemplate=preg_replace("/<{alleeo_loopstart}>(.*?)<{alleeo_loopend}>/s",$replaced5,$xTemplate);
	//----------------- replace user eeo ----------------------------	
		preg_match("/<{eeo_loopstart}>(.*?)<{eeo_loopend}>/s",$xTemplate,$match6);
		$replace6=$match6[1];
		for($i=0;$i<count($user_eeoset);$i++)
		{
			$value=$user_eeoset[$i]["tag_name"];
			$key=$user_eeoset[$i]["tag_id"];
			$replaced6.=preg_replace("/<{(.*?)}>/e","$$1",$replace6);
		}
$xTemplate=preg_replace("/<{eeo_loopstart}>(.*?)<{eeo_loopend}>/s",$replaced6,$xTemplate);
	//---------------------------------------------------------------------------
//-------------------------------prints the position set--------------------- 
$sel_val=$user_details["position"];
$positionset=$this->return_position_values($common,$db_object,0);
$xTemplate=$common->singleloop_replace($db_object,"<{position_loopstart}>","<{position_loopend}>",$xTemplate,$positionset,$sel_val);

//--------------------------------


	$vals["location"]=$location["loc_name"];
	$vals["level"]=$user_level["level_no"];
	$vals["position"]=$user_level["position_name"];
	$vals["bossname"]=$user_boss["position_name"];
	$vals["employment"]=$employeetype[$type];
	$vals["Offphone"]=$user_details["office_phone"];
	$vals["cellphone"]=$user_details["cell_phone"];
	$vals["pager"]=$user_details["pager"];
	$vals["fax"]=$user_details["fax"];
	$vals["password"]=$user_details["password"];
	$vals["mailaddress"]=$user_details["office_mail_address"];
	$vals["physicaladdress"]=$user_details["office_physical_address"];

	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;
  }


  function return_position_values($common,$db_object,$pos_id)
	{
		$position=$common->prefix_table("position");
		if($pos_id!="")
		{
			$sub="where pos_id='$pos_id'";
		}
		else
		{
			$sub="";
		}
			$qry="select pos_id,position_name from $position ";
			$qry.=$sub;
			$positionset=$db_object->get_rsltset($qry);
			for($i=0;$i<count($positionset);$i++)
			{
				$id=$positionset[$i]["pos_id"];
				$newpositionset[$id]=$positionset[$i]["position_name"];
				
			}
		
			return $newpositionset;
	}
			

  
  function updatepersonalinfo($common,$db_object,$form_array,$user_id,$default,$alert_msg,$fRequest)
  {

$t=0;
$subqry="";
  	while(list($kk,$vv)=each($form_array))
  	{
  		$$kk=$vv;
  	/*	if($vv!='')
  		{*/
  			$fldn=split("#;#",$kk);
  			if($fldn[1]=="change")
  			{
  				$temp=$fldn[0];
  				$sub[$temp]=$vv;
  			}
  			if(ereg("^fLocationchange",$kk))
  			{
  				$subqry.=" location='$fLocationchange',";  				
  			}
  			if(ereg("^fPositionchange",$kk))
  			{
  				$subqry.=" position='$fPositionchange',";
  			}
  			if(ereg("^fEmploymentchange",$kk))
  			{
  				$subqry.=" employment_type='$fEmploymentchange',";
  			}
  			if(ereg("^fOffPhonechange",$kk))
  			{
  				$subqry.=" office_phone='$fOffPhonechange',";
  			}
  			if(ereg("^fCellPhonechange",$kk))
  			{
  				$subqry.=" cell_phone='$fCellPhonechange',";
  			}
  			if(ereg("^fPagerchange",$kk))
  			{
  				$subqry.=" pager='$fPagerchange',";
  			}
  			if(ereg("^fMailAddresschange",$kk))
  			{
  				$subqry.=" office_mail_address='$fMailAddresschange',";
  			}
  			if(ereg("^fPhisicalAddresschange",$kk))
  			{
  				$subqry.=" office_physical_address='$fPhisicalAddresschange',";
  			}
  			if(ereg("^fPasswordchange",$kk))
  			{
  				$subqry.=" password='$fPasswordchange',";
  			}
  			if(ereg("^fFaxchange",$kk))
  			{
  				$subqry.=" fax='$fFaxchange',";
  			}
  	//	}
  		
  	}


  	if($user_id!=1)
  	{
    		$tempuser_eeo=$common->prefix_table("temp_user_eeo");
  		$tempuser_table=$common->prefix_table("temp_user_table");
  	}
  	else
  	{
  		$tempuser_eeo=$common->prefix_table("user_eeo");
  		$tempuser_table=$common->prefix_table("user_table");  		
  	}


	$preqry="select user_id from $tempuser_table where user_id='$user_id'";
	$checks=$db_object->get_a_line($preqry);
	if($checks["user_id"])
	{
		$insqry="update $tempuser_table set ";
		$end=" where user_id='$user_id'";
	}
	else
	{
		$insqry="insert into $tempuser_table set user_id='$user_id',";
		$end="";
	}
	
	while(list($kk,$vv)=@each($sub))
	{
		if($kk!="")
		{
			$qrystart.="$kk='$vv',";
		}
		
	}
	if($qrystart!="")
	{
		$insqry.=$qrystart.$subqry;
		$insqry=substr($insqry,0,-1);
		$insqry.=$end;
		$insqry=trim($insqry);
		$db_object->insert($insqry);
	//	echo $insqry;
	  	 for($i=0;$i<count($fEEOStatuschange);$i++)
		 {
		 	$tag_id=$fEEOStatuschange[$i];
		 	$eeoqry="replace into $tempuser_eeo set user_id='$user_id',tag_id='$tag_id'";
		 	$db_object->insert($eeoqry);
		 //	echo $eeoqry;
		  }
		  $user_table=$common->prefix_table("user_table");



$selqry="update $user_table set password='$fPasswordchange' where user_id='$user_id'";
$db_object->insert($selqry);


		  $admin_idsel="select admin_id,username from $user_table where user_id='$user_id'";
		  $admin_id=$db_object->get_a_line($admin_idsel);
		if($user_id!=1)
		{
			  if($admin_id["admin_id"]!=0)
			  {
			  	$adminid=$admin_id["admin_id"];
			  	/*$selemail="select email from $user_table where user_id='$adminid'";
			  	$adminemail=$db_object->get_a_line($selemail);
			  	$admailid=$adminemail["email"];*/
			  }
			  else
			  {
			  	$adminid=1;
			  }
				$selemail="select email from $user_table where user_id='$adminid'";
			  	$adminemail=$db_object->get_a_line($selemail);
			  	$admailid=$adminemail["email"];
	//		  echo "Mail has been sent to ";
	//		  echo $admailid;
				$config=$common->prefix_table("config");
				$mail="select admin_subject,admin_message from $config";
				$mailrslt=$db_object->get_a_line($mail);

			  $to=$admailid;
			  $subject=$mailrslt["admin_subject"];
			  $message=$mailrslt["admin_message"];
			  $vals["emp_name"]=$admin_id["username"];
			  $message=$common->direct_replace($db_object,$message,$vals);
			  $bool=$common->send_mail($to,$subject,$message,$from);
			  if($bool)
			  {
			    echo $alert_msg["cMailsent"];
			  }
			  if($fRequest)
			  {
			  	$message="Iamabossbuticannotseemydirectreports";
			  	$subject="Iamabossbuticannotseemydirectreports";
			  	$bool=$common->send_mail($to,$subject,$message,$from);
	   			  if($bool)
			  	  {
				    echo $alert_msg["cMailsent"];
				  }							  	
			  	
			  }
		}
		else
		{
			echo $alert_msg["cAdmindetailsupdated"];
		}
		
	}
  }
}
$objperson=new Personal_info;
/*if($emp_id)
{
	$user_id=$emp_id;
}
else
{
}*/	
if($fSubmit)
{

$objperson->updatepersonalinfo($common,$db_object,$post_var,$user_id,$default,$alert_msg,$fRequest);
}
else
{
$objperson->display_personalinfo($common,$db_object,$user_id,$default);
}
include("footer.php");
?>