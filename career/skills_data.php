<?php
/*---------------------------------------------
SCRIPT:skills_data.php
AUTHOR:info@chrisranjana.com	
UPDATED:1st Sept

DESCRIPTION:
This script uploads the skill sets of the person...

---------------------------------------------*/
include("../session.php");
include("header.php");
//include("../lang/eng.php");
class skill_data
{

  function skill_display($common,$db_object,$user_id)
  {
	$xPath=$common->path;
	$xTemplate=$xPath."templates/career/skills_data.html";
	$returncontent=$common->return_file_content($db_object,$xTemplate);
	$returncontent=$common->replace_templatecontent($db_object,$returncontent,$values);
	echo $returncontent;
  }
//--first saves the file in local directory namely uploads
  function upload_file($common,$db_object,$form_array,$user_id,$error_msg,$alert_msg)
  {

	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;
	}
//print_r($form_array);
//exit;

  		$filename=$user_id;
		$cvfile=$_FILES["fcsv"]["tmp_name"];
		$type=$_FILES["fcsv"]["type"];
$sub="";
	$cvfilename=$common->upload_cv($cvfile,$filename,$type,$sub);
	if($cvfilename!=0)
	{	
	$this->file_open($common,$db_object,$cvfilename,$user_id,$fskill_type,$error_msg);
	}
	else
	{
		echo $error_msg["cFileMismatch"];
	}
		
  }

//--opens saved file and then manipultes the file if you add anyu column to the table
//--just add the column name  at the first row of the file then it will automatically insert into the table
  
  function file_open($common,$db_object,$filename,$user_id,$fskill_type,$error_msg)
  {
  	$path=$common->path;
  	$directory=$path."uploads/";
  	$filename=$directory.$filename;
  	$fd = fopen($filename,"r");
	$contents = fread($fd,filesize($filename));
	fclose ($fd);
	$lines=preg_split("/\\n/",$contents,-1,PREG_SPLIT_NO_EMPTY);


	
$data=array_slice($lines,1);

$config=$common->prefix_table("config");
$query="select delimiter from $config";
$delimit=$db_object->get_a_line($query);
$delimiter=$delimit["delimiter"];
$fieldnamearray=explode($delimiter,$lines[0]);


if($user_id!=1)
{
$unapskill=$common->prefix_table("unapproved_skills");
$sub2=" emp_id='$user_id',date_posted=curdate()";

}
else
{
	$unapskill=$common->prefix_table("skills");
	$sub2="";
}
$sqlqry="desc $unapskill";
$check=$db_object->get_rsltset($sqlqry);
while(list($kk,$vv)=@each($check))
{
	$headings[$kk]=$check[$kk]["Field"];
}

/* Leaving out unskilled_desc for technical skill builder */

	for($l=0;$l<count($fieldnamearray);$l++)
		{
		$fieldnamearray[$l]=trim($fieldnamearray[$l]);
		}

		

	foreach($fieldnamearray as $fieldname)
	{
		$fi="";
		if(!in_array(trim($fieldname),$headings))
		{
			$fi=$fieldname;
			break;
		}
	
	}
	if($fi!="")
	{
		echo $error_msg["cInvalidcolumn"];
		echo "$fi <br>";
		include("footer.php");
			exit;	

	}

	/*

	checking out fields required for T and I skills

if(($fskill_type=="t" && !in_array("skill_description",$fieldnamearray))||($fskill_type=="i" && !in_array("unskilled_desc",$fieldnamearray) )
{
	echo $error_msg["cColumnError"];
	include("footer.php");
	exit;
}
	*/


if( ($fskill_type=="t" && !in_array("skill_description",$fieldnamearray))

||($fskill_type=="i" && ( !in_array("unskilled_desc",$fieldnamearray)   or  !in_array("skill_description",$fieldnamearray)    )  ))
{
	echo $error_msg["cColumnError"];
	include("footer.php");
	exit;
}
	
//print_r($fieldnamearray);
//print_r($data);

for($i=0;$i<count($data);$i++)
{
	$valuerow=$data[$i];
	$valuerowarray=explode($delimiter,$valuerow);
		$temp=array_values($valuerowarray);
	/*	if(count($temp)!=1&&$temp[0]!="")
		{*/
			if(count($valuerowarray)!=count($fieldnamearray))
			{
				echo $error_msg["cFieldmis"];
				include("footer.php");
				exit;
			}
		//closeshere
	
}

//exit;
	
	for($i=0;$i<count($data);$i++)
	{
		$query="";
		$sub="";
		$valuerow=$data[$i];
		$valuerowarray=explode($delimiter,$valuerow);
			
			$query="insert into $unapskill set skill_type='$fskill_type',";
			for($j=0;$j<count($fieldnamearray);$j++)
			{
		
			$fieldname=$fieldnamearray[$j];
			$fieldvalue=$valuerowarray[$j];
			$fieldvalue=addslashes($fieldvalue);
				
				if($fskill_type=="t" && $fieldname!="unskilled_desc" || $fskill_type=="i")
				{

					
				$sub.=" $fieldname='".$fieldvalue."',";

			
				
				}
		
				
			}
			$sub2=$sub2;
			if($sub2=="")
			{
				$sub=substr($sub,0,-1);
			}
			$query=$query.$sub.$sub2;
			$db_object->insert($query);
		
		}

echo $error_msg["cSuccess"];
if($user_id!=1)
{

$config=$common->prefix_table("config");
$subqry="select isubject,imessage from $config";
$rslt=$db_object->get_a_line($subqry);
$isubject=$rslt["isubject"];
$imessage=$rslt["imessage"];
$user=$common->prefix_table("user");


$subqry2="select username,email from $user where user_id='$user_id'";
$user_name=$db_object->get_a_line($subqry2);


$emailqry="select username,password,email from $user where user_id=1";
$email_id=$db_object->get_a_line($emailqry);
$email=$email_id["email"];
$values["directreplace"]["logininfo"]=$email_id["username"];
$values["directreplace"]["password"]=$email_id["password"];
$to=$email;
$from=$user_name["email"];

$values["directreplace"]["username"]=$user_name["username"];
$values["directreplace"]["url"]=$common->http_path."/index.php";
$imessage=$common->direct_replace($db_object,$imessage,$values);
$sent=$common->send_mail($to,$isubject,$imessage,$from);
	if($sent)
	{
		echo "<br>";
		echo $alert_msg["cMailsent"];
		echo "<br>";
	}
	else
	{
		echo $alert_msg["cFailmail"];
	}
}
include("footer.php");	
exit;
  }
  
}
$skobj=new skill_data;

if($submit)
{
	$skobj->upload_file($common,$db_object,$_POST,$user_id,$error_msg,$alert_msg);
}
$skobj->skill_display($common,$db_object,$user_id);

include("footer.php");
?>
