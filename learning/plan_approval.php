<?php
/*===============================================================
    SCRIPT: dev_solution.php
    AUTHOR: chrisranjana.com
    UPDATED: 25th of September, 2003
    
    DESCRIPTION
     This deals with the analyses Developmental Solution.
===============================================================*/

include("../session.php");
include("header.php");
class plan
{
function preview($db_object,$common,$learning,$default,$user_id,$post_var,$gbl_freq_array,$preview)
{

	@extract($post_var);
	$path=$common->path;

	$skills_table=$common->prefix_table("skills");
	$config=$common->prefix_table("config");
	$user_table=$common->prefix_table("user_table");
	$dev_basic=$common->prefix_table("dev_basic");
	$feedback=$common->prefix_table("feedback");
	$dev_interbasic=$common->prefix_table("dev_interbasic");
	$approved_devbuilder=$common->prefix_table("approved_devbuilder");
	$assign_solution_builder=$common->prefix_table("assign_solution_builder");
	$position_table = $common->prefix_table("position");

/*	$sqlname="select first_name,last_name from $user_table where user_id=$user_id";
	$namearr=$db_object->get_a_line($sqlname);

	$first_name=$namearr["first_name"];
	$last_name=$namearr["last_name"];
*/
	$username = $common->name_display($db_object,$user_id);
	
	$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table, $approved_devbuilder ,
			$assign_solution_builder	where $approved_devbuilder.skill_id=$skills_table.skill_id and  
			$approved_devbuilder.user_id=$user_id and $assign_solution_builder.skill_id=$skills_table.skill_id 
			and  $assign_solution_builder.user_id=$user_id and  $assign_solution_builder.pstatus<>'' and ($approved_devbuilder.pstatus='u' or
			 $approved_devbuilder.pstatus='r' or  $approved_devbuilder.pstatus='t') group by 
			$approved_devbuilder.skill_id ";
	$arr=$db_object->get_rsltset($mysql);


	$xFile=$path."/templates/learning/plan_approval.html";

	$returncontent=$common->return_file_content($db_object,$xFile);

	while(list($key,$value)=@each($post_var))
	{
		$$key=$value;
		if(ereg("^fActstart",$key))
		{
			list($fact,$skill_id,$t_id)=split("_",$key);
			$from_post_arr[]=$value;
		}
	}
	@sort($from_post_arr);	

	preg_match("/<{skills_loopstart}>(.*?)<{skills_loopend}>/s",$returncontent,$out);
	$myvar=$out[0];
	

	$str="";

	for($s=0;$s<count($arr);$s++)
	{



		$inn=$myvar;

		$skills_name=$arr[$s]["skill_name"];

		$skills=$arr[$s]["skill_id"];

		preg_match("/<{text1_loopstart}>(.*?)<{text1_loopend}>/s",$returncontent,$outid);

		$mytitle=$outid[0];

		$titstr="";

		$sqlfrom="select cdate from $approved_devbuilder where interbasic_id in (1,2,3,4,5,6,7,8,9) and skill_id='$skills' and user_id='$user_id'and title!=''  order by cdate limit 0,1";
		$fromarr=$db_object->get_a_line($sqlfrom);

		$sqltitle="select title,description,build_id,cdate,completed_date from $approved_devbuilder where interbasic_id in (1,2,3,4,5,6,7,8,9) and skill_id='$skills' and user_id='$user_id'and title!=''  order by build_id";

		$titlearr=$db_object->get_rsltset($sqltitle);

		for($i=0;$i<count($titlearr);$i++)
		{
			$title=$titlearr[$i]["title"];
			$desc=$titlearr[$i]["description"];
	
			$id = $titlearr[$i]["build_id"];
			$temp_date = "fActstart_".$skills."_".$id;
			$cdate =	$$temp_date;
			if($cdate == "" )
			{
				$cdate=$titlearr[$i]["cdate"];		
			}
			else
			{
				$cdate=$learning->date_display_slash($cdate);
			}
			
			$completed_date = $titlearr[$i]["completed_date"]; 
			
			$titstr.=preg_replace("/<{(.*?)}>/e","$$1",$mytitle);

	
		}
		
		$first_replace=preg_replace("/<{text1_loopstart}>(.*?)<{text1_loopend}>/s",$titstr,$inn);


		preg_match("/<{text2_loopstart}>(.*?)<{text2_loopend}>/s",$returncontent,$outid);

		$mytitle=$outid[0];
//echo "title=$mytitle";



		$titstr="";

		$sqltitle="select title,interbasic_id,description,build_id,cdate,completed_date from $approved_devbuilder where interbasic_id in (10,11,12) and skill_id='$skills' and user_id='$user_id'and title!=''  order by build_id";

		$titlearr=$db_object->get_rsltset($sqltitle);

		for($i=0;$i<count($titlearr);$i++)
		{
			$title=$titlearr[$i]["title"];
//echo $title;

			$desc=$titlearr[$i]["description"];
			$id=$titlearr[$i]["build_id"];
			$inter_id = $titlearr[$i]["interbasic_id"];
			if($inter_id=='12')
			{
				$posqry = "select position_name from $position_table where pos_id='$title'";
				$posres = $db_object->get_a_line($posqry);
				$title = $posres['position_name'];
			}
			
			$completed_date=$titlearr[$i]["completed_date"];
			
			$temp_date = "fActstart_".$skills."_".$id;
			$cdate =	$$temp_date;
			if($cdate == "" )
			{
				$cdate=$titlearr[$i]["cdate"];
				$cdate=$learning->date_display($cdate);
			}
			else
			{
				$cdate=$learning->date_display_slash($cdate);
			}
			if($from_post_arr[0] != "" )
			{
				$fromdate = $learning->date_display_slash($from_post_arr['0']);		
			}
			else
			{
				$fromdate = $fromarr['cdate'];
					$fromsdate = $learning->date_display($fromdate);
			}
			$titstr.=preg_replace("/<{(.*?)}>/e","$$1",$mytitle);
//echo $titstr;
	
		}
		
		$first_replace=preg_replace("/<{text2_loopstart}>(.*?)<{text2_loopend}>/s",$titstr,$first_replace);

//echo $first_replace;

		preg_match("/<{text3_loopstart}>(.*?)<{text3_loopend}>/s",$returncontent,$outid);

		$mytitle=$outid[0];



		$titstr="";
		$fromdate="";
		$sqltitle="select title,description,email,build_id,cdate,frequency from $approved_devbuilder,$user_table 
					where $approved_devbuilder.user_id=$user_table.user_id and
					$approved_devbuilder.interbasic_id in (13,14) and 
					$approved_devbuilder.skill_id='$skills' and $approved_devbuilder.user_id='$user_id'
					and $approved_devbuilder.title!='' order by build_id";

		$titlearr=$db_object->get_rsltset($sqltitle);

		for($i=0;$i<count($titlearr);$i++)
		{
			$id =$titlearr[$i]["build_id"];
			$temp_title = "fName_".$skills."_".$id;
			$title =	$$temp_title;
			if($title == "" )
			{
				$title=$titlearr[$i]["title"];		
			}
		
			$desc=$titlearr[$i]["description"];

			$temp_email = "fEmail_".$skills."_".$id;
			$email =	$$temp_email;
			if($email == "" )
			{
				$email=$titlearr[$i]["email"];		
			}

			$temp_date = "fFeedstart_".$skills."_".$id;
			$cdate =	$$temp_date;
			if($cdate == "" )
			{
				$cdate=$titlearr[$i]["cdate"];
				$cdate=$learning->date_display($cdate);		
			}
			else
			{
				$cdate=$learning->date_display_slash($cdate);
			}
			
			$temp_freq = "Ffrequency_".$skills."_".$id;
			$index =	$$temp_freq;
			if($index == "" )
			{
				$index=$titlearr[$i]["frequency"];	
			}
			$frequency = $gbl_freq_array[$index];
			
			if($from_post_arr[0] != "" )
			{
				$fromdate = $learning->date_display_slash($from_post_arr['0']);
			}
			else
			{
				$fromdate = $fromarr['cdate'];
				$fromsdate = $learning->date_display($fromdate);
			}
			$titstr.=preg_replace("/<{(.*?)}>/e","$$1",$mytitle);

	
		}
		
		$first_replace=preg_replace("/<{text3_loopstart}>(.*?)<{text3_loopend}>/s",$titstr,$first_replace);






		$str.=preg_replace("/<{(.*?)}>/e","$$1",$first_replace);


	}

	$returncontent=preg_replace("/<{skills_loopstart}>(.*?)<{skills_loopend}>/s",$str,$returncontent);

	if($preview == "" )
	{
		$returncontent=preg_replace("/<{approval_(.*?)}>/s","",$returncontent);	
	}
	if($preview != "" )
	{
		$returncontent=preg_replace("/<{approval_loopstart}>(.*?)<{approval_loopend}>/s","",$returncontent);	
	}

	$array["skill_id"]=$skills;

	$array["frist_name"]=$first_name;

	$array["last_name"]=$last_name;

	$array["type"]=$type;

	$array["user_id"]=$user_id;

	$array["username"]=$username;

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);

	echo $returncontent;
}

	function approve_plan($db_object,$common,$_POST)
	{
		
	$result=array_keys($_POST);

	print_r($result);

	}


	function approval_form($db_object,$common,$_POST,$error_msg)
	{

	$skills=$common->prefix_table("skills");

	$user_table=$common->prefix_table("user_table");

	$skill_id=$_POST["skill_id"];

	$user_id=$_POST["user_id"];
	
	$mysql="select username from $user_table where user_id='$user_id'";

	$arr=$db_object->get_a_line($mysql);

	$user_name=$arr["username"];

	$mysql="select skill_name from $skills where skill_id='$skill_id'";

	$arr=$db_object->get_a_line($mysql);

	$skill_name=$arr["skill_name"];
	
	$path=$common->path;

	$xFile=$path."/templates/learning1/admin_approvalform.html";

	$returncontent=$common->return_file_content($db_object,$xFile);
	
	$array["user_name"]=$user_name;

	$array["skill"]=$skill_name;

	$msg=$error_msg["cDevApproved"];

	$content=$common->direct_replace($db_object,$msg,$array);

	$replace["Approved"]=$content;
	
	$returncontent=$common->direct_replace($db_object,$returncontent,$replace);

	echo $returncontent;


}


	

	
	function Save_finishlater($db_object,$common,$learning,$default,$user_id,$post_var)
	{

	$skills_table=$common->prefix_table("skills");
	$config=$common->prefix_table("config");
	$feedback=$common->prefix_table("feedback");
	$user_table=$common->prefix_table("user_table");
	$dev_basic=$common->prefix_table("dev_basic");
	$position_table=$common->prefix_table("position");
	$temp_planbuilder=$common->prefix_table("temp_planbuilder");
	$approved_devbuilder=$common->prefix_table("approved_devbuilder");
	
	while(list($key,$value)=each($post_var))
	{
		$$key=$value;

		if(ereg("^Ffrequency",$key))
		{
			list($Ffrequency,$skill_id,$t_id)=split("_",$key);

			$frequency_array[$skill_id][$t_id]=$value;

				

		}	
						

		if(ereg("^fFeedstart",$key))
		{
			list($fFeedstart,$skill_id,$t_id)=split("_",$key);

			$feed_array[$skill_id][$t_id]=$value;	
			
			//print_r($fFeedstart);
		}

		if(ereg("^fName",$key))
		{
			list($fName,$skill_id,$t_id)=split("_",$key);

			$name_array[$skill_id][$t_id]=$value;	

		}

		if(ereg("^fEmail",$key))
		{
			list($fEmail,$skill_id,$t_id)=split("_",$key);

			$email_array[$skill_id][$t_id]=$value;	
		}


		if(ereg("^fActstart",$key))
		{
			list($fActstart,$skill_id,$t_id)=split("_",$key);

			$datestart_array[$skill_id][$t_id]=$value;	
		}

	}


	$skill_array=array_keys($feed_array);


	for($i=0;$i<count($skill_array);$i++)
	{
		$skills=$skill_array[$i];


		$array=$datestart_array[$skill_id];

		//print_r($datestart_array);exit;		
		$feedback_array=$feed_array[$skill_id];

		$t_array=array_keys($array);
	

		for($j=0;$j<count($t_array);$j++)
		{
			$t_id=$t_array[$j];

			$dates=$datestart_array[$skills][$t_id];

			$dates=$learning->changedate_database($dates);

			$dev_build_sql="update  $approved_devbuilder set cdate='$dates' where build_id='$t_id'";


			$db_object->insert($dev_build_sql);

		}
			$tfeed_array=array_keys($feedback_array);
			
		

		for($j=0;$j<count($tfeed_array);$j++)
			{
				$t_id=$tfeed_array[$j];

				$start_date=$feed_array[$skills][$t_id];

				$frequency=$frequency_array[$skills][$t_id];

				$email=$email_array[$skills][$t_id];

				$name=$name_array[$skills][$t_id];

				$sdate=$learning->changedate_database($start_date);
						
				$sqlemail="select user_id,email from $user_table";

				$mailarr=$db_object->get_rsltset($sqlemail);
		
				$check=0;

				for($k=0;$k<count($mailarr);$k++)
					{
						$cmail=$mailarr[$k]["email"];

						$id=$mailarr[$k]["user_id"];
				
						if($email==$cmail)
							{

								$email=$id;

								$name=$id;
				
								$type='i';
					
								break;
							}
						else
							{
								$check++;

								continue;
							

						
							}
		
						
					}
	
				if($check==count($mailarr))
					{
				
				
	
						$type='g';
				}
	
			

			$insertsql="update $approved_devbuilder set cdate='$sdate',

			feed_type='$type',frequency='$frequency',pstatus='u' where url='$email'";

			$db_object->insert($insertsql);
		
		}
}

	
}

	function show_preview($db_object,$common,$default,$user_id,$_GET,$gbl_freq_array,$mode)
	{
	$skills_table=$common->prefix_table("skills");
	$config=$common->prefix_table("config");
	$user_table=$common->prefix_table("user_table");
	$dev_basic=$common->prefix_table("dev_basic");
	$feedback=$common->prefix_table("feedback");
	$dev_interbasic=$common->prefix_table("dev_interbasic");
	$approved_devbuilder=$common->prefix_table("approved_devbuilder");

	if($mode == "show" )
	{
	$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table join $approved_devbuilder  
			where $approved_devbuilder.skill_id=$skills_table.skill_id and  
			$approved_devbuilder.user_id=$user_id and $approved_devbuilder.pstatus='a' group by 
			$approved_devbuilder.skill_id ";
	}
	if($mode == "view" )
	{
	$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table join $approved_devbuilder  
			where $approved_devbuilder.skill_id=$skills_table.skill_id and  
			$approved_devbuilder.user_id=$user_id and $approved_devbuilder.pstatus='t' group by 
			$approved_devbuilder.skill_id ";
	}
//	echo $mysql;exit;
	$result=$db_object->get_rsltset($mysql);
//	print_r($result);
//	$xFile=$path."/templates/learning/plan_preview.html";
	


	//print_r($result);exit;

	for($i=0;$i<count($result);$i++)
	{
		$skill_id=$result[$i][skill_id];
		if($mode == "show" )
		{
		$sql="select * from approved_devbuilder where user_id='$user_id' and skill_id='$skill_id' and pstatus='a'";
		}
		if($mode == "view" )
		{
		$sql="select * from approved_devbuilder where user_id='$user_id' and skill_id='$skill_id' and pstatus='t'";
		}
		$result_sql.=$db_object->get_rsltset($sql);
	}
	
	if($result_sql[0]=="")
	{
		include_once("footer.php");
		exit;
	}	

	
	$path=$common->path;

/*	$sqlname="select first_name,last_name from $user_table where user_id=$user_id";
	$namearr=$db_object->get_a_line($sqlname);
	$first_name=$namearr["first_name"];
	$last_name=$namearr["last_name"];
*/
	$username=$common->name_display($db_object,$user_id);

	$xFile=$path."/templates/learning/plan_preview.html";
	$returncontent=$common->return_file_content($db_object,$xFile);
	
	$arr=$db_object->get_rsltset($mysql);

	preg_match("/<{skills_loopstart}>(.*?)<{skills_loopend}>/s",$returncontent,$out);

	$myvar=$out[0];


	$str="";

	for($s=0;$s<count($arr);$s++)
	{



		$inn=$myvar;

		$skills_name=$arr[$s]["skill_name"];

		$skills=$arr[$s]["skill_id"];

		preg_match("/<{text1_loopstart}>(.*?)<{text1_loopend}>/s",$returncontent,$outid);

		$mytitle=$outid[0];

		$sqlfrom="select cdate from $approved_devbuilder where interbasic_id in (1,2,3,4,5,6,7,8,9) and skill_id='$skills' and user_id='$user_id'and title!=''  order by cdate limit 0,1";
		$fromarr=$db_object->get_a_line($sqlfrom);

		$titstr="";

		$sqltitle="select title,description,build_id,cdate,completed_date from $approved_devbuilder where interbasic_id in (1,2,3,4,5,6,7,8,9) and skill_id='$skills' and user_id='$user_id'and title!=''";

		$titlearr=$db_object->get_rsltset($sqltitle);

		for($i=0;$i<count($titlearr);$i++)
		{
			$title=$titlearr[$i]["title"];

			$desc=$titlearr[$i]["description"];

			$cdate=$titlearr[$i]["cdate"];
			
			$completed_date=$titlearr[$i]["completed_date"];

			$titstr.=preg_replace("/<{(.*?)}>/e","$$1",$mytitle);


		}
//echo $inn;			
		$first_replace=preg_replace("/<{text1_loopstart}>(.*?)<{text1_loopend}>/s",$titstr,$inn);

//echo $first_replace;
		preg_match("/<{text2_loopstart}>(.*?)<{text2_loopend}>/s",$returncontent,$outid);

		$mytitle=$outid[0];
//echo "title=$mytitle";



		$titstr="";

		$sqltitle="select title,description,build_id,cdate,completed_date from $approved_devbuilder where interbasic_id in (10,11,12) and skill_id='$skills' and user_id='$user_id'and title!=''";

		$titlearr=$db_object->get_rsltset($sqltitle);

		for($i=0;$i<count($titlearr);$i++)
		{
			$title=$titlearr[$i]["title"];
//echo $title;

			$desc=$titlearr[$i]["description"];

			$cdate=$titlearr[$i]["cdate"];

			$id=$titlearr[$i]["build_id"];
			
			$fromdate = $fromarr['cdate'];

			$completed_date=$titlearr[$i]["completed_date"];
//echo "freq=$frequency";
		
			$titstr.=preg_replace("/<{(.*?)}>/e","$$1",$mytitle);
//echo $titstr;
	
		}
		
		$first_replace=preg_replace("/<{text2_loopstart}>(.*?)<{text2_loopend}>/s",$titstr,$first_replace);

//echo $first_replace;

		preg_match("/<{text3_loopstart}>(.*?)<{text3_loopend}>/s",$returncontent,$outid);

		$mytitle=$outid[0];



		$titstr="";

		$sqltitle="select title,description,build_id,cdate,frequency from $approved_devbuilder where interbasic_id in (13,14) and skill_id='$skills' and user_id='$user_id'and title!=''";

		$titlearr=$db_object->get_rsltset($sqltitle);
//echo $sqltitle;

		for($i=0;$i<count($titlearr);$i++)
		{
			$title=$titlearr[$i]["title"];

			$desc=$titlearr[$i]["description"];

			$cdate=$titlearr[$i]["cdate"];
			$fromdate = $fromarr['cdate'];
			$index=$titlearr[$i]["frequency"];	
			$frequency = $gbl_freq_array[$index];
			$titstr.=preg_replace("/<{(.*?)}>/e","$$1",$mytitle);

	
		}
		
		$first_replace=preg_replace("/<{text3_loopstart}>(.*?)<{text3_loopend}>/s",$titstr,$first_replace);






		$str.=preg_replace("/<{(.*?)}>/e","$$1",$first_replace);


	}

	$returncontent=preg_replace("/<{skills_loopstart}>(.*?)<{skills_loopend}>/s",$str,$returncontent);

	$array["skill_id"]=$skills;
//	$array["frist_name"]=$first_name;
//	$array["last_name"]=$last_name;
	$array["type"]=$type;
	$array["user_id"]=$user_id;
	$array["username"]=$username;

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);

	echo $returncontent;
}	
}
$obj=new plan;


if($save)
{
	$action="save";
}
if($submit)
{
	$action="approve";

}
if($resubmit)
{
	$action="resubmit";
}
if($preview)
{
	$status="preview";
}

switch($action)
{
	
	case NULL:
		
			$obj->preview($db_object,$common,$learning,$default,$user_id,$post_var,$gbl_freq_array,$status);
			break;
	
	case "approve":
			$obj->approve_plan($db_object,$common,$_POST);
			break;

	case "show":
			$mode="show";
			$obj->show_preview($db_object,$common,$default,$user_id,$_GET,$gbl_freq_array,$mode);
			break;
			
	case "view":
			$mode="view";
			$obj->show_preview($db_object,$common,$default,$user_id,$_GET,$gbl_freq_array,$mode);
			break;
	
	case "save":	

			$learning->Save_finishlater($db_object,$common,$default,$user_id,$post_var,$gbl_freq_array);
			$learning->show_aftersave($db_object,$common,$default,$user_id,$gbl_freq_array);
			break;
	
}
include ("footer.php");
?>



