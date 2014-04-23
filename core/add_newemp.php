<?php
include("../session.php");
include("header.php");
class Addnewemployee
{
 function newemp($common,$db_object,$default,$error_msg,$gbl_arr_for_column)
 {
	$path=$common->path;
	$xFile=$path."templates/core/add_newemp.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
	$namefileds=$common->prefix_table("name_fields");
	$name="name_".$default;
	$selqry="select $name,field_name from $namefileds where status='YES'";
	$fieldnamearray=$db_object->get_rsltset($selqry);
$user_table=$common->prefix_table("user_table");
for($i=0;$i<count($fieldnamearray);$i++)
{
	$fieldname[$i]=$fieldnamearray[$i][$name];
	$fieldvalue[$i]=$fieldnamearray[$i]["field_name"];
}
	
//	$addarray=array("User Name","Email","Employee type","Access Rights","Position");
	$addarray=$gbl_arr_for_column;
	$addarray1=array("username","email");
	//,"employment_type","access_rights","position");
	$fieldname=array_merge($fieldname,$addarray);
	$fieldvalue=array_merge($fieldvalue,$addarray1);

	preg_match("/<{field_namestart}>(.*?)<{field_nameend}>/s",$xTemplate,$match);
	$replace=$match[1];
	for($i=0;$i<count($fieldname);$i++)
	{
		$field_name=$fieldname[$i];
		$replaced.=preg_replace("/{{field_name}}/s",$field_name,$replace);
	}
	$xTemplate=preg_replace("/<{field_namestart}>(.*?)<{field_nameend}>/s", $replaced,$xTemplate);
	preg_match("/<{values_namestart}>(.*?)<{values_nameend}>/s",$xTemplate,$match1);
	$repl=$match1[1];
	$bit="</tr><tr>";

	preg_match("/<{tobe_replaced}>(.*?)<{tobe_replaced}>/s",$repl,$mat1);
	$inrrepl=$mat1[1];
	
	$type="type_".$default;
	$employment_type=$common->prefix_table("employment_type");
	$empsel="select $type,id from $employment_type where status='Yes'";
	$emprslt=$db_object->get_rsltset($empsel);
	for($i=0;$i<count($emprslt);$i++)
	{
		$id=$emprslt[$i]["id"];
		$employset[$id]=$emprslt[$i][$type];
	}

	$loopstart="<{inner_loopstart}>";
	$loopend="<{inner_loopend}>";
	$employmentreplace=$common->singleloop_replace($db_object,$loopstart,$loopend,$inrrepl,$employset,0);
	$employmentreplace=preg_replace("/{{column_name}}/s","fEmployment",$employmentreplace);

	$access_rights=$common->prefix_table("access_rights");
	$acsel="select id,$type from $access_rights where rights='yes'";
	$accset=$db_object->get_rsltset($acsel);
	for($i=0;$i<count($accset);$i++)
	{
		$id=$accset[$i]["id"];
		$accessset[$id]=$accset[$i][$type];
	}
	$accessreplace=$common->singleloop_replace($db_object,$loopstart,$loopend,$inrrepl,$accessset,0);
	$accessreplace=preg_replace("/{{column_name}}/s","fAccess_rights",$accessreplace);



	$position=$common->prefix_table("position");
	$possel="select $position.position_name,$position.pos_id from $position left join $user_table on $user_table.position=$position.pos_id where $user_table.position is null";
	$posit=$db_object->get_rsltset($possel);
	for($i=0;$i<count($posit);$i++)
	{
		$id=$posit[$i]["pos_id"];
		$posiset[$id]=$posit[$i]["position_name"];
	}
	$positionreplace=$common->singleloop_replace($db_object,$loopstart,$loopend,$inrrepl,$posiset,0);
	$positionreplace=preg_replace("/{{column_name}}/s","fPosition",$positionreplace);
	$selection=$employmentreplace.$accessreplace.$positionreplace;
	$repl=preg_replace("/<{tobe_replaced}>(.*?)<{tobe_replaced}>/s","",$repl);



		$config=$common->prefix_table("config");
		$selk="select count_of_employees from config";
		$countrslt=$db_object->get_a_line($selk);
		$count_of_employees=$countrslt["count_of_employees"];


		
	for($i=1;$i<=$count_of_employees;$i++)
	{	for($j=0;$j<count($fieldvalue);$j++)
		{
			$temp=$i;
			$field_name1=$fieldvalue[$j]."#;#";
			$field_name1=$field_name1.$temp;
			$repled.=preg_replace("/{{field_name}}/s",$field_name1,$repl);

		}
		$selection1=preg_replace("/{{id}}/s",$temp,$selection);
		$repled1.=$repled.$selection1.$bit;
		$repled="";
		
	}

$xTemplate=preg_replace("/<{values_namestart}>(.*?)<{values_nameend}>/s", $repled1,$xTemplate);
$vals=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
echo $xTemplate;
  }

   function savedbfile($common,$db_object,$form_array,$error_msg,$default,$user_id)
   {

   //	print_r($form_array);
   	$user_table=$common->prefix_table("user_table");
   	while(list($kk,$vv)=each($form_array))
   	{
   		$$kk=$vv;
   		if($vv!="")
   		{
   			
   			$fldn=split("#;#",$kk);
   			$fieldname=$fldn[0];
   			$id=$fldn[1];
           	        			
  	 			$useri="username#;#".$id;
   				$checkv=$form_array[$useri];
   				$usersemail1="email#;#".$id;
   				$usersemail=$form_array[$usersemail1];
   				$sechk="select user_id from $user_table where username='$checkv' || email='$usersemail'";
   				$userxist=$db_object->get_a_line($sechk);
   				
   				if($userxist["user_id"]=="")
		   		{
   						if(ereg("^fEmployment#;#",$kk))
						{
							$fldn=split("#;#",$kk);
							$id=$fldn[1];
							$sub[$id].="employment_type='$vv',";
							$admin_check[$id]["emp_type_id"]=$vv;
						}
						else if(ereg("^fAccess_rights#;#",$kk))
						{
							$fldn=split("fAccess_rights#;#",$kk);
							$id=$fldn[1];
							$sub[$id].="access_rights='$vv',";
						}
						else if(ereg("^fPosition#;#",$kk))
						{
							$fldn=split("#;#",$kk);
							$id=$fldn[1];
							$sub[$id].="position='$vv',";
							$admin_check[$id]["pos_id"]=$vv;
						}
		   				else if($id)
						{
							if($fieldname=="email")
								{
							$sub[$id].="password='$vv',";
								}
				   			$sub[$id].="$fieldname='$vv',";

						}
				}
				else
				{
					$unadded[$id]=$checkv;
				//	echo $error_msg["cAlready exists"];
				}
				
   					   			
   		}
  	
      	}

$position_table=$common->prefix_table("position");
$family_position=$common->prefix_table("family_position");
while(list($kk,$vv)=@each($admin_check))
{
	$pos_idforr=$vv["pos_id"];
	$mysql="select level_no,boss_no,location from $position_table where pos_id='$pos_idforr'";
	$posar=$db_object->get_rsltset($mysql);
	$mysql="select family_id from $family_position where $family_position.position_id='$pos_idforr'";
	$fam_arr=$db_object->get_rsltset($mysql);
	$charec["level_no"]=$posar[0]["level_no"];
	$charec["boss_id"]=$posar[0]["boss_no"];
	$charec["location_id"]=$posar[0]["location"];
	$charec["family_id"]=$fam_arr[0]["family_id"];
	$charec["employment_type"]=$admin_check[$kk]["emp_type_id"];
	$charec["pos_id"]=$admin_check[$kk]["pos_id"];
	$admin_check[$kk]["admin_id"]=$common->return_my_admin($db_object,$charec);
	$admin_check[$kk]["admin_id"];
	$admin_check[$kk]["location"]=$posar[0]["location"];
	//print_r($charec);
//	exit;
	
}


/*
print_r($posar);
print_r($fam_arr);
print_r($admin_check);
exit;
*/
	$config=$common->prefix_table("config");
      	$configqry="select emp_subject,emp_message from $config";
      	$mailrslt=$db_object->get_a_line($configqry);
      	$subject=$mailrslt["emp_subject"];
      	$message=$mailrslt["emp_message"];

     	
	$employment_type=$common->prefix_table("employment_type");
        $user_table=$common->prefix_table("user_table");
        $type="type_".$default;

	$seladmin="select email from $user_table where user_id='1'";
	$emailid=$db_object->get_a_line($seladmin);
	$from=$emailid["email"];

        
      	while(list($kk,$vv)=@each($sub))
      	{
      		$ad_id=$admin_check[$kk]["admin_id"];
      		$exabit2="admin_id='$ad_id'";
      		$loctn=$admin_check[$kk]["location"];
      		$extrabit="reg_date=now(),location='$loctn',added_by='$user_id',";
      		$insqry="insert into $user_table set ";
      		$insqry.=$sub[$kk].$extrabit.$exabit2;
      		$userid=$db_object->insert_data_id($insqry);


      		
//      		echo $insqry;


      		
      		$insqry="";
      		$selqry="select first_name,email,employment_type,username from $user_table where user_id='$userid'";
		$empset=$db_object->get_a_line($selqry);
      		$to=$empset["email"];
      		$emptypeno=$empset["employment_type"];
		$selemptyp="select $type from $employment_type where id='$emptypeno'";
		$emptype=$db_object->get_a_line($selemptyp);
		
      		$vals["employmenttype"]=$emptype[$type];
      		$vals["emp_name"]=$empset["username"];
      		$sentmessage=$common->direct_replace($db_object,$message,$vals);
	     	$sent=$common->send_mail($to,$subject,$sentmessage,$from);
      	      	if($sent)
      		{
		echo $empset["first_name"];
        	echo $error_msg['Employeeinformed'];
      		}
 		
      	}
      	while(list($kk,$vv)=@each($unadded))
      	{
	      	echo $unadded[$kk];
	      	echo $error_msg["cAlready exists"];
      	}
      	
      //	print_r($unadded);
      	
  	
   }
 
}
$empobj=new Addnewemployee;
if($fSave)
{
$empobj->savedbfile($common,$db_object,$_POST,$error_msg,$default,$user_id);
}
else
{
$empobj->newemp($common,$db_object,$default,$error_msg,$gbl_arr_for_column);
}
include("footer.php");

?>