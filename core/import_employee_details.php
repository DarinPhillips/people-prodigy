<?php
include("../session.php");
include("header.php");
class Import_details
{
   function display_panel($common,$db_object,$error_msg)
   {
	$path=$common->path;
	$xFile=$path."templates/core/import_employee_details.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);

//------------------------------
	
	 $user_table=$common->prefix_table("user_table");	
	 $name_fields=$common->prefix_table("name_fields");
	 $rslqry="select name_id,field_name from $name_fields where status='YES'";
	 $resultset=$db_object->get_rsltset($rslqry);

	$dqry="desc $user_table";
	$drslt=$db_object->get_single_column($dqry);




	 $unwqry="select field_name from $name_fields where status='NO'";
	 $unwantedrslt=$db_object->get_single_column($unwqry);


	$position=$common->prefix_table("position");
	$location=$common->prefix_table("location");
	$access_rights=$common->prefix_table("access_rights");
	$newarr=array($position,$location,$access_rights);

	$unwant2=array("0"=>"reg_date","1"=>"status",2=>"added_by");
	$unwantedrslt=array_merge($unwantedrslt,$unwant2);
	//print_r($unwant2);
	//print_r($unwantedrslt);
//	exit;




	 $resultset=array_diff($drslt,$unwantedrslt);
	$resultset=array_merge($resultset,$newarr);

	 

	 for($i=1;$i<count($resultset);$i++)
	 {
	 	$id=$i+1;
	 	if($resultset[$i]!="")
	 	{
	 	$result[$id]=$resultset[$i];
	 	}
	 }

	$no=count($result);
	 preg_match("/<{column_loopstart}>(.*?)<{column_loopend}>/s",$xTemplate,$match);
	 $replace=$match[1];
	 $loopstart="<{inner_loopstart}>";
	 $loopend="<{inner_loopend}>";
	 $bit="</td></tr>";
	 
	for($i=1;$i<=$no;$i++)
	{
		$id=$i+1;
		$replaced.=$common->singleloop_replace($db_object,$loopstart,$loopend,$replace,$result,$sel);
		$replaced=preg_replace("/<{id}>/s",$id,$replaced);
		if($i%4==0)
		{
			$replaced.=$bit;
		}
	}

	$xTemplate=preg_replace("/<{column_loopstart}>(.*?)<{column_loopend}>/s",$replaced,$xTemplate);

	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;
   }

   function check_confirm($common,$db_object,$form_array,$error_msg)
   {
   	$g=0;

	while(list($key,$value)=each($form_array))
	{
		$$key=$value;
		
		if(ereg("^fColumn_",$key))
		{
			
			if($value!="")
			{
				$d=split("_",$key);
				$id=$d[1];
				$selectcolumns[$g]=$value;
				$idset[$g]=$id;
				$g++;
			}
		
		}
	}
 	$empfile=$_FILES["fEmp"]["tmp_name"];
	$type=$_FILES["fEmp"]["type"];
	$sub="/employee";
	$filename="empdetails";
	$empfilename=$common->upload_cv($empfile,$filename,$type,$sub);
	if($empfilename)
	{
		$path=$common->path;
	  	$directory=$path."uploads/employee/";
		$filename=$directory.$empfilename;
		$fd = fopen($filename,"r");
		$contents = fread($fd,filesize($filename));
		fclose($fd);
	
		$lines=preg_split("/\\n/",$contents,-1,PREG_SPLIT_NO_EMPTY);
		$data=array_slice($lines,0);
		$config=$common->prefix_table("config");
		$qry="select delimiter from $config";
		$delimit=$db_object->get_a_line($qry);
		$delimiter=$delimit["delimiter"];
		$cnt=count($selectcolumns);
		

		
		$k=0;
		
		for($j=0;$j<count($data);$j++)
		{
			$valuerow=trim($data[$j]);
			
	
			if($valuerow=="")
				{
				continue;
				}
			$valuerowarray[$k]=explode($delimiter,$valuerow);
			$tem=$valuerowarray[$k];
			$k++;
	
			$nt=count($tem);
			if($cnt!=$nt)
			{
					echo $error_msg["cFieldmis"];
					include("footer.php");
					exit;
			}
			
		}
		
		for($i=0;$i<count($selectcolumns);$i++)
		{
			if($selectcolumns[$i]=="username")
			{
				$selval=$i;
				break;
			}
		}
/*	$user_table=$common->prefix_table("user_table");
	$selqry="select username from $user_table";
	$userset=$db_object->get_single_column($selqry);
	
	$f=0;
		for($i=0;$i<count($valuerowarray);$i++)
		{
			$user_name=trim($valuerowarray[$i][$selval]);
			
			for($j=0;$j<count($userset);$j++)
			{
				
				if($userset[$j]==$username)
				{
				//	$overwrite[$f]=$user_name;
				//	$over[$f]=$i;
				//	$f++;
					$del=$user_name;//data to be over written
					break;

				}
			}
			
		}
		*/

		
	
	$path=$common->path;
	$xFile=$path."templates/core/import_alert.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);


	preg_match("/<{column_namestart}>(.*?)<{column_nameend}>/s",$xTemplate,$match);
	$replace=$match[1];
	for($i=0;$i<count($selectcolumns);$i++)
	{
		$column_name=$selectcolumns[$i];
		$replace1=preg_replace("/{{column_name}}/s",$column_name,$replace);
		$replaced=$replaced.$replace1;
	}
	$xTemplate=preg_replace("/<{column_namestart}>(.*?)<{column_nameend}>/s",$replaced,$xTemplate);
	$replaced="";

	
	preg_match("/<{value_namestart}>(.*?)<{value_nameend}>/s",$xTemplate,$match);
	$replace=$match[1];
	$bit="</td><tr>";
	
	
	for($i=0;$i<count($valuerowarray);$i++)
	{
		for($j=0;$j<count($valuerowarray[$i]);$j++)
		{$value_name=$valuerowarray[$i][$j];
		$replace1=preg_replace("/{{value_name}}/s",$value_name,$replace);
	
		$replaced=$replaced.$replace1;
		}
		$replaced=$replaced.$bit;
	}

	$xTemplate=preg_replace("/<{value_namestart}>(.*?)<{value_nameend}>/s",$replaced,$xTemplate);
	
	preg_match("/<{hidden_fieldnamestart}>(.*?)<{hidden_fieldnameend}>/s",$xTemplate,$match);
	$repla=$match[1];


	for($i=0;$i<count($selectcolumns);$i++)
	{
		$fieldname=$selectcolumns[$i];
		$id=$i+1;
		$replaed.=preg_replace("/{{(.*?)}}/e","$$1",$repla);
	}

	$xTemplate=preg_replace("/<{hidden_fieldnamestart}>(.*?)<{hidden_fieldnameend}>/s",$replaed,$xTemplate);

	if($fReplace=="yes")
	{
		$vals["replc"]=$error_msg["will"];
		$vals["ovrwrte"]="overwrite";
	}
	else
	{
		$vals["replc"]=$error_msg["cNot"];
		$vals["ovrwrte"]="";
	}


	$vals["filename"]=$empfilename;
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);



	
	echo $xTemplate;
	include("footer.php");
	exit;
        }
	else
	{
		echo $error_msg["cFileMismatch"];
	}
   }
   
   function savedbfile($common,$db_object,$form_array,$error_msg,$default)
   {
   	$r=0;
   	$type="type_".$default;
   	while(list($kk,$vv)=each($form_array))
   	{
   		$$kk=$vv;
   		if(ereg("^fFieldname_",$kk))
   		{
   				$fieldname[$r]=$vv;
   				$r++;
   		}
   	}     	


   	$user_table=$common->prefix_table("user_table");
   	$path=$common->path;
	  	$directory=$path."uploads/employee/";
		$filename=$directory.$fFilename;
		$fd = fopen($filename,"r");
		$contents = fread($fd,filesize($filename));
		fclose($fd);
		$lines=preg_split("/\\n/",$contents,-1,PREG_SPLIT_NO_EMPTY);
		$data=array_slice($lines,0);
		$config=$common->prefix_table("config");
		$qry="select delimiter from $config";
		$delimit=$db_object->get_a_line($qry);
		$delimiter=$delimit["delimiter"];
		$cnt=count($selectcolumns);
		$k=0;
		for($j=0;$j<count($data);$j++)
		{
		/*	$valuerow=$data[$j];
			$valuerowarray[$j]=explode($delimiter,$valuerow);
			$tem=$valuerowarray[$j];*/
			$valuerow=trim($data[$j]);
			if($valuerow=="")
				{
				continue;
				}
			$valuerowarray[$k]=explode($delimiter,$valuerow);
		//	$tem=$valuerowarray[$k];
			$k++;
	
		}

		$posidn="empty";
		$locidn="empty";
		$acrdn="empty";
			for($i=0;$i<count($fieldname);$i++)
			{
				if($fieldname[$i]=="position")
				{
					$idneeded=1;
					$posidn=$i;
					
				}
				else if($fieldname[$i]=="location")
				{
					$idneeded=1;
					$locidn=$i;
					
				}
				else if($fieldname[$i]=="access_rights")
				{
					$idneeded=1;
					$acrdn=$i;
					
				}
				if($fieldname[$i]=="username")
				{
					$selval=$i;
					
				}
			}

										/*		for($i=0;$i<count($valuerowarray);$i++)
												{	$user_name=$valuerowarray[$i][$selval];
													$selqry="select user_id,username from $user_table where username='$username'";
													$user_result=$db_object->get_a_line($selqry);
													if($user_result["username"])
													{
														$overids[$f]=$userset[$j]["user_id"];
																	$over[$f]=$i;
																		$f++;
													}
	
												}*/

$location=$common->prefix_table("location");
$position=$common->prefix_table("position");
$access_rights=$common->prefix_table("access_rights");
//print_r($valuerowarray);
//exit;

		for($i=0;$i<count($valuerowarray);$i++)
		{

			if($idneeded)
			{
				if($posidn!="empty"||$posidn=="0")
				{
					
					$position_name=trim($valuerowarray[$i][$posidn]);
					$selpos="select pos_id from $position where position_name='$position_name'";
					$posid=$db_object->get_a_line($selpos);
					$valuerowarray[$i][$posidn]=$posid["pos_id"];
				}
				if($locidn!="empty"||$locidn=="0")
				{

					$location_name=trim($valuerowarray[$i][$locidn]);
					$selloc="select loc_id from $location where loc_name='$location_name'";
					$locid=$db_object->get_a_line($selloc);
					$valuerowarray[$i][$locidn]=$locid["loc_id"];
				}
				if($acrdn!="empty"||$acrdn=="0")
				{
					$access=trim($valuerowarray[$i][$acrdn]);
					$selacr="select id from $access_rights where $type='$access'";
					$acid=$db_object->get_a_line($selacr);
					$valuerowarray[$i][$acrdn]=$acid["id"];
				}
				
				
				
			}
			$username=$valuerowarray[$i][$selval];
			$sel="select user_id from $user_table where username='$username'";
			$rslt=$db_object->get_a_line($sel);
				
			if($fOverwrite && $rslt["user_id"])
			{	$user_id=$rslt["user_id"];
				$qry="update $user_table set ";
				$end=" where user_id='$user_id'";
				
			}
			else if(!$fOverwrite && $rslt["user_id"])
			{
				continue;
			}
			else
			{
			$qry="insert into $user_table set ";
			$end="";
			}
			for($j=0;$j<count($fieldname);$j++)
			{
				$vn=trim($valuerowarray[$i][$j]);
				$fn=trim($fieldname[$j]);
				$sub.="$fn='$vn',";
			}
//			$sub=substr($sub,0,-1);
			$sub.="reg_date=now() ";
			$qry.=$sub.$end;
			$db_object->insert($qry);
			$qry="";
			$sub="";
			$end="";
			
		}
		
	echo $error_msg["EmpImported"] ;
	include("footer.php");	
   exit;	
   }
     
}
$impobj=new Import_details;
if($fYes)
{
	$impobj->savedbfile($common,$db_object,$_POST,$error_msg,$default);
}
if($fNext)
{
	$impobj->check_confirm($common,$db_object,$_POST,$error_msg);

}
$impobj->display_panel($common,$db_object,$error_msg);
include("footer.php");

?>