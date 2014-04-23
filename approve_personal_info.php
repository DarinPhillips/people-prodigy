<?php
include("session.php");
include("header.php");
class Personal_info
{
  function display_personalinfo($common,$db_object,$emp_id,$default,$icomefromcore=null)
  {
//-------------------initialisations--------------------------
  	$name="name_".$default;
  	$type="type_".$default;

  	
//---------template retrival--------------------- 
	$path=$common->path;

	$xFile=$path."templates/approve_personal_info.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
//-------------------------------

//------------------prelimcheck---------------------

	if($icomefromcore==yes)
	{
		$user_table	=$common->prefix_table("user_table");		
	}
	else
	{

		$user_table	=$common->prefix_table("temp_user_table");
	}
		$user_eeo	=$common->prefix_table("temp_user_eeo");
	
//---------------------------tables---------------------------------


	$name_fields	=$common->prefix_table("name_fields");
	$eeo_main	=$common->prefix_table("eeo_main");
	$eeo_tags	=$common->prefix_table("eeo_tags");
	$position	=$common->prefix_table("position");
	$employment_type=$common->prefix_table("employment_type");
	$location	=$common->prefix_table("location_table");
//-------------------------------------------------------------------	

//-----------------database retrival---------------------------------

	$seluserdetails	="select user_id,
			  password,office_phone,
			  cell_phone,pager,fax,office_mail_address,
			  office_physical_address,
			  location,position,
			  employment_type from $user_table where user_id='$emp_id'";
	$user_details	=$db_object->get_a_line($seluserdetails);

	
	$selnamefields	="select $name,field_name from $name_fields where status='YES'";
	$namefieldsrslt	=$db_object->get_rsltset($selnamefields);

	$sellevels	="select distinct(level_no) from $position";
	$levels		=$db_object->get_single_column($sellevels);
	
	$selemptype	="select id,$type from $employment_type where status='Yes'";
	$emptype	=$db_object->get_rsltset($selemptype);

	$seleeo		="select tag_id,tag_name from $eeo_tags";
	//$eeoset		=$db_object->get_rsltset($seleeo);
	$eeoset=$common->return_eeo_status($db_object);

	$selusereeo	="select $eeo_tags.tag_name as tag_name,$user_eeo.tag_id as tag_id from $user_eeo,$eeo_tags where $eeo_tags.tag_id=$user_eeo.tag_id and $user_eeo.user_id='$emp_id'";
	//$user_eeoset	=$db_object->get_rsltset($selusereeo);
	$user_eeoset=$common->return_eeo_status($db_object);
	$seluserlevel	="select $position.level_no as level_no,$position.position_name as position_name,$position.boss_no as boss_number from $position,$user_table where $user_table.position=$position.pos_id and $user_table.user_id='$emp_id'";
	$user_level	=$db_object->get_a_line($seluserlevel);

	$temp_boss	=$user_level["boss_number"];
	$seluserboss 	="select position_name from $position where pos_id='$temp_boss'";
	$user_boss	=$db_object->get_a_line($seluserboss);

	$temp_emptype	=$user_details["employment_type"];

	$seluser_emptype="select $type from $employment_type where id='$temp_emptype'";
	$employeetype	=$db_object->get_a_line($seluser_emptype);

	$templocation	=$user_details["location"];
	//$seluserlocation="select loc_name from $location where loc_id='$templocation'";
	//$location	=$db_object->get_a_line($seluserlocation);
	
	$location	=$common->return_location_for_display($db_object);

//-------------------------------------------------------------------------------------	
//----------------------splice the template--------------------------------------------

	//-----------replaces the fieldname text boxes---------------------------------	
		preg_match("/<{changeable_textstart}>(.*?)<{changeable_textend}>/s",$xTemplate,$match1);
		$replace1=$match1[1];

		for($i=0;$i<count($namefieldsrslt);$i++)
		{

			$field=$namefieldsrslt[$i]["field_name"];
			$nametodis=$namefieldsrslt[$i][$name];
			$selnfvalues="select $field from $user_table where user_id='$emp_id'";
			
			$fieldvalue=$db_object->get_a_line($selnfvalues);
			$value=$fieldvalue[$field];

			$replaced1.=preg_replace("/{{(.*?)}}/e","$$1",$replace1);
		}
	
$xTemplate=preg_replace("/<{changeable_textstart}>(.*?)<{changeable_textend}>/s",$replaced1,$xTemplate);

	//----------------- replace user eeo ----------------------------	
		preg_match("/<{eeo_loopstart}>(.*?)<{eeo_loopend}>/s",$xTemplate,$match6);
		$replace6=$match6[1];
		for($i=0;$i<count($user_eeoset);$i++)
		{
			$value=$user_eeoset[$i]["tag_name"];
			$key=$user_eeoset[$i]["tag_id"];
			$selected="selected";
			$replaced6.=preg_replace("/<{(.*?)}>/e","$$1",$replace6);
		}
$xTemplate=preg_replace("/<{eeo_loopstart}>(.*?)<{eeo_loopend}>/s",$replaced6,$xTemplate);
	//---------------------------------------------------------------------------

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
$xTemplate=preg_replace("/<{employement_loopstart}>(.*?)<{employment_loopend}>/s",$replaced4,$xTemplate);






$match_arr=$templocation;
//-----------Location table  to be changed
	
$loc_arr=$common->return_location_for_display($db_object);
		
		preg_match("/<{Location_loopstart}>(.*?)<{Location_loopend}>/s",$xTemplate,$match);
		
		$match=$match[0];
		//echo $match;exit;
		for($i=0;$i<count($loc_arr);$i++)
		{
			$j=$i+1;
			
			$location_name=$loc_arr[$j];
			
			$replaced3.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		
		$xTemplate=preg_replace("/<{Location_loopstart}>(.*?)<{Location_loopend}>/s",$replaced3,$xTemplate);


		//$replaced3=$common->list_category($db_object,$common,$catid,$app,$match_arr);
		//$xTemplate=preg_replace("/<{Location_loopstart}>(.*?)<{Location_loopend}>/s",$replaced3,$xTemplate);
$sel_val=$user_details["position"];
$positionset=$this->return_position_values($common,$db_object,0);
$xTemplate=$common->singleloop_replace($db_object,"<{position_loopstart}>","<{position_loopend}>",$xTemplate,$positionset,$sel_val);



	$vals["level"]=$user_level["level_no"];

	$vals["bossname"]=$user_boss["position_name"];

	$vals["Offphone"]=$user_details["office_phone"];
	$vals["cellphone"]=$user_details["cell_phone"];
	$vals["pager"]=$user_details["pager"];
	$vals["fax"]=$user_details["fax"];
	$vals["password"]=$user_details["password"];
	$vals["mailaddress"]=$user_details["office_mail_address"];
	$vals["physicaladdress"]=$user_details["office_physical_address"];
	$vals["emp_id"]=$emp_id;

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
			

  
  function updatepersonalinfo($common,$db_object,$form_array,$emp_id,$default,$error_msg,$alert_msg)
  {
  

$t=0;
$subqry="";
$temp=0;
  	while(list($kk,$vv)=each($form_array))
  	{
  		$$kk=$vv;
  
  			switch($kk)
			{
				case "fLocation":
					$subqry.=" location='$fLocation',";  				
					break;	
				case "fPosition":
					$subqry.=" position='$fPosition',";
					break;
				case "fEmployment":
					$subqry.=" employment_type='$fEmployment',";
					break;
				case "fOffPhone":
					$subqry.=" office_phone='$fOffPhone',";
					break;
				case "fCellPhone":
					$subqry.=" cell_phone='$fCellPhone',";
					break;
				case "fPager":
					$subqry.=" pager='$fPager',";
					break;
				case "fMailAddress":
					$subqry.=" office_mail_address='$fMailAddress',";
					break;
				case "fPhysicalAddress":
					$subqry.=" office_physical_address='$fPhysicalAddress',";
					break;
				case "fPassword":
					$subqry.=" password='$fPassword',";
					break;
				case "fFax":
					$subqry.=" fax='$fFax',";
					break;
				case "fLevel":
					break;
				case "fEEOStatus":
					break;
				case "fBoss":
				break;
				case "fSubmit":
				break;
				case "emp_id":
				break;
				default:
					$sub[$kk]=$vv;
					break;
			}
  	
  		
  	}
  	$user_eeo=$common->prefix_table("user_eeo");
  	$user_table=$common->prefix_table("user_table");


	
		$insqry="update $user_table set ";
		$end=" where user_id='$emp_id'";
	
	while(list($kk,$vv)=@each($sub))
	{
		if($kk!="")
		{
			$qrystart.="$kk='$vv',";
		}
		
	}
//	echo $qrystart;
	if($qrystart!="")
	{
		$insqry.=$qrystart.$subqry;
		$insqry=substr($insqry,0,-1);
		$insqry.=$end;
		$insqry=trim($insqry);
		$db_object->insert($insqry);
//	echo $insqry;
		$deleeo="delete from $user_eeo where user_id='$emp_id'";
		$db_object->insert($deleeo);
		
	  	 for($i=0;$i<count($fEEOStatus);$i++)
		 {
		 	$tag_id=$fEEOStatus[$i];
		 	$eeoqry="replace into $user_eeo set user_id='$emp_id',tag_id='$tag_id'";
		 	$db_object->insert($eeoqry);
		 }

		$delqry="delete from temp_user_table where user_id='$emp_id'";
		$db_object->insert($delqry);
		$delqry2="delete from temp_user_eeo where user_id='$emp_id'";
		$db_object->insert($delqry2);
		echo $error_msg["cEmpupdated"];

	
/*		  $user_table=$common->prefix_table("user_table");
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
		  */
		  	echo $error_msg["cEmpupdated"];
	}
  }

	function reject_details($common,$db_object,$emp_id,$error_msg)
	{
		$temp_user_table=$common->prefix_table("temp_user_table");
		$temp_user_eeo=$common->prefix_table("temp_user_eeo");
		$mysql="delete from $temp_user_table where user_id='$emp_id'";
		$db_object->insert($mysql);
		$mysql="delete from $temp_user_eeo where user_id='$emp_id'";
		$db_object->insert($mysql);
		echo $error_msg["cEmpdetailsrejected"];
		echo "<br>";
/*$myhtml=<<<EOD
<a href="front_panel.php">$error_msg["cFrontpanel"]</a>
EOD;
echo $myhtml;
*/
		
		
			
	}
 
}
$objperson=new Personal_info;

$user_Table=$common->prefix_table("user_table");
if($fReject)
{
	$objperson->reject_details($common,$db_object,$emp_id,$error_msg);
}
else
{
	$selqry="select admin_id from $user_table where user_id='$emp_id'";
	$admin_check=$db_object->get_a_line($selqry);
	if($admin_check["admin_id"]==$user_id||$user_id==1)
	{
		if($fSubmit)
		{
			$objperson->updatepersonalinfo($common,$db_object,$post_var,$emp_id,$default,$error_msg,$alert_msg);
		}
		else
		{
			$objperson->display_personalinfo($common,$db_object,$emp_id,$default,$icomefromcore);
		}
	}
	else
	{
		echo "The User is not Under Your Control";
	}
}
include("footer.php");
?>
