<?php
/*===============================================================
    SCRIPT: learning_plan.php
    AUTHOR: chrisranjana.com
    UPDATED: 6th of October, 2003
    
    DESCRIPTION
     This deals with the LEARNING PLAN BUILDER
===============================================================*/

include "../session.php";
include "header.php";

class learning_plan
{
function show_form($db_object,$common,$learning,$default,$user_id,$action,$gbl_freq_array,$linkid)
{

	if($linkid != "")
	{
		$user_id = $linkid;	
	}
	$path=$common->path;
	$skills_table=$common->prefix_table("skills");
	$config=$common->prefix_table("config");
	$user_table=$common->prefix_table("user_table");
	$dev_basic=$common->prefix_table("dev_basic");
	$dev_interbasic=$common->prefix_table("dev_interbasic");
	$approved_devbuilder=$common->prefix_table("approved_devbuilder");
	$position_table = $common->prefix_table("position");
	$assign_solution_builder=$common->prefix_table("assign_solution_builder");


//	$sql="select build_id from $approved_devbuilder where pstatus='u'";

	$sql="select build_id from $approved_devbuilder where (pstatus='u' or pstatus='r')";
	$result=$db_object->get_rsltset($sql);

	if($action=="alert")
	{

		$xFile=$path."/templates/learning/admin_approval_learning_plan.html";

	}
	else
	{
		if($result[0] == "")
		{

		 	$xFile=$path."/templates/learning/approval_learning_plan.html";
		}
		else	
		{
			$xFile=$path."/templates/learning/learning_plan.html";
		}
	}

	$returncontent=$common->return_file_content($db_object,$xFile);
	
/*	$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table join $approved_devbuilder  
			where $approved_devbuilder.skill_id=$skills_table.skill_id and  
			$approved_devbuilder.user_id=$user_id and ($approved_devbuilder.pstatus='u' or $approved_devbuilder.pstatus='r' ) group by 
			$approved_devbuilder.skill_id ";
*/
	$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table,$approved_devbuilder,$assign_solution_builder  
					where $approved_devbuilder.skill_id=$skills_table.skill_id and  $approved_devbuilder.user_id=$user_id 
					and $assign_solution_builder.skill_id=$skills_table.skill_id and  $assign_solution_builder.user_id=$user_id 
					and $assign_solution_builder.pstatus<>'' and ($approved_devbuilder.pstatus='u' or $approved_devbuilder.pstatus='r') 
					group by 	$approved_devbuilder.skill_id "; 

	$arr=$db_object->get_rsltset($mysql);
//echo "sql=$mysql<bR>";
	for($i=0;$i<count($arr);$i++)
	{
	$skill=$arr[$i][skill_id];	

//	$sql="select * from $approved_devbuilder where skill_id='$skill' and user_id='$user_id' and pstatus!='a'";
	$sql="select * from $approved_devbuilder where skill_id='$skill' and user_id='$user_id' and (pstatus='u' or pstatus='r')";
//echo "sql=$sql<br>";	
	$res.=$db_object->get_rsltset($sql);

	}

	if($res[0]== " ")
	{

	include_once("footer.php");

	exit;
	}
//echo $mysql;
	//print_r($arr);

	preg_match("/<{skills_loopstart}>(.*?)<{skills_loopend}>/s",$returncontent,$out);

	$myvar=$out[0];


	$str="";

	for($s=0;$s<count($arr);$s++)
	{

		$inn=$myvar;

		$skills_name=$arr[$s]["skill_name"];

		$skills=$arr[$s]["skill_id"];

		preg_match("/<{activities_loopstart}>(.*?)<{activities_loopend}>/s",$returncontent,$outid);

		$myact=$outid[0];

		$string="";

		$sqlact="select basic_id,coursetype_$default from $dev_basic where basic_id between 1 and 3";

		$arract=$db_object->get_rsltset($sqlact);
		
		for($i=0;$i<count($arract);$i++)
		{

			$mysact=$myact;
			
			$basic_id=$arract[$i]["basic_id"];

			$sol_names=$arract[$i]["coursetype_$default"];

			$sqlact="select interbasic_id,coursename_$default from $dev_interbasic where basic_id='$basic_id'";

			$arrsubact=$db_object->get_rsltset($sqlact);
	
			preg_match("/<{subact_loopstart}>(.*?)<{subact_loopend}>/s",$returncontent,$out);

			$mycourse=$out[0];

			$mystr="";

			for($j=0;$j<count($arrsubact);$j++)
			{
				$mycou=$mycourse;
			
				$ib_id=$arrsubact[$j]["interbasic_id"];

				$subactivities_names=$arrsubact[$j]["coursename_$default"];
			
			//	$sqltitle="select * from $approved_devbuilder where interbasic_id='$ib_id' and skill_id='$skills' and user_id='$user_id'and title!='' and pstatus !='a'";
				$sqltitle="select * from $approved_devbuilder where interbasic_id='$ib_id' and skill_id='$skills' and user_id='$user_id'and title!='' and (pstatus ='u' or pstatus='r')";

				$titlearr=$db_object->get_rsltset($sqltitle);
				
				

				preg_match("/<{title_loopstart}>(.*?)<{title_loopend}>/s",$returncontent,$outtitle);

				$mytitle=$outtitle[0];

				$titlestr="";

				if($act_sdate!="")
				{

				if($act_sdate =='0000-00-00')
				{
			
					$act_sdate="";
				}
				else
				{

					$act_sdate=$learning->changedate_datetime($act_sdate);

				}
				}	

				for($k=0;$k<count($titlearr);$k++)
				{

				$title=$titlearr[$k]["title"];


				$text=$titlearr[$k]["build_id"];

				$act_sdate=$titlearr[$k]["cdate"];

				if($act_sdate!="")
				{

				if($act_sdate =='0000-00-00')
				{
			
					$act_date="";
				}
				else
				{

					$act_date=$learning->changedate_datetime($act_sdate);

				}
				}	


				$titlestr.=preg_replace("/<{(.*?)}>/e","$$1",$mytitle);
			}

			$inner=preg_replace("/<{title_loopstart}>(.*?)<{title_loopend}>/s",$titlestr,$mycou);


			$mystr.=preg_replace("/<{(.*?)}>/e","$$1",$inner);

			}
	


			$outer=preg_replace("/<{subact_loopstart}>(.*?)<{subact_loopend}>/s",$mystr,$mysact);


			$string.=preg_replace("/<{(.*?)}>/e","$$1",$outer);


		}


	$inn=preg_replace("/<{activities_loopstart}>(.*?)<{activities_loopend}>/s",$string,$inn);


	$act_date=$act_sdate;


	

	preg_match("/<{application_loopstart}>(.*?)<{application_loopend}>/s",$returncontent,$outid);

		$myapp=$outid[0];


		$appstr="";

		$sqlact="select interbasic_id,coursename_$default from $dev_interbasic where interbasic_id between 10 and 12";

		$arract=$db_object->get_rsltset($sqlact);


		for($j=0;$j<count($arract);$j++)
		{
			$myapp1=$myapp;
		
			$ib_id=$arract[$j]["interbasic_id"];

			$app_names=$arract[$j]["coursename_$default"];
	
		//	$sqltitle="select title,build_id,cdate from $approved_devbuilder where interbasic_id='$ib_id' and skill_id='$skills' and user_id='$user_id'and title!='' and pstatus!='a'";
			$sqltitle="select title,interbasic_id,build_id,cdate from $approved_devbuilder where interbasic_id='$ib_id' and skill_id='$skills' and user_id='$user_id'and title!='' and (pstatus='u' or pstatus='r')";

			$titlearr=$db_object->get_rsltset($sqltitle);

			

			preg_match("/<{apptitle_loopstart}>(.*?)<{apptitle_loopend}>/s",$returncontent,$outtitle);

			$mytitle=$outtitle[0];
			
			$titlestr="";
	
			for($k=0;$k<count($titlearr);$k++)
			{
				

				$title=$titlearr[$k]["title"];
				$text=$titlearr[$k]["build_id"];
				$app_sdate=$titlearr[$k]["cdate"];				
				$inter_id = $titlearr[$k]['interbasic_id'];
					
				if($inter_id=='12')
				{
					$posqry = "select position_name from $position_table where pos_id='$title'";
					$posres = $db_object->get_a_line($posqry);
					$title = $posres['position_name'];
				}
			if($app_sdate!="")

			{

			if($app_sdate =='0000-00-00')
			{
			
				$app_sdate="";
			}
			else
			{

			$app_sdate=$learning->changedate_datetime($app_sdate);


			}
			}	

				
				$titlestr.=preg_replace("/<{(.*?)}>/e","$$1",$mytitle);

			}


			$inner=preg_replace("/<{apptitle_loopstart}>(.*?)<{apptitle_loopend}>/s",$titlestr,$myapp1);


		

			$appstr.=preg_replace("/<{(.*?)}>/e","$$1",$inner);
 
		}

			$app_date=$app_sdate;


			$inn=preg_replace("/<{application_loopstart}>(.*?)<{application_loopend}>/s",$appstr,$inn);

	
		


	preg_match("/<{text_loopstart}>(.*?)<{text_loopend}>/s",$returncontent,$outid);
			
	$mytext=$outid[1];

	//echo "mytext=$mytext";

	

	//		$sqltitle="select * from $approved_devbuilder where interbasic_id in('13','14') and skill_id='$skills' and user_id='$user_id'and title!='' and pstatus!='a' order by build_id";
			$sqltitle="select * from $approved_devbuilder where interbasic_id in('13','14') and skill_id='$skills' and user_id='$user_id'and title!='' and (pstatus='u' or pstatus='r') order by build_id";

			//echo $sqltitle;exit;

			$titlearr=$db_object->get_rsltset($sqltitle);

			//print_r($titlearr);exit;



			$mytext1=$outid[1];

			//echo $mytext;exit;

			$strfeed="";

			for($l=0;$l<count($titlearr);$l++)

			{

				$text=$titlearr[$l]["build_id"];

				$skills=$titlearr[$l]["skill_id"];	
						
				$title=$titlearr[$l]["title"];
				
				$email=$titlearr[$l]["url"];

				$ftype=$titlearr[$l]["f_type"];

				$app_sdate=$titlearr[$l]["cdate"];

				if($app_sdate!="")
				{
					if($app_sdate =='0000-00-00')
					{
						$sdate="";
					}
					else
					{
						$sdate=$learning->changedate_datetime($app_sdate);
					}
				}
				$ib_id=$titlearr[$l]["interbasic_id"];

				$frequency=$titlearr[$l]["frequency"];




	if($ib_id=='14')
	
				{
					$userquerry="select username,email from $user_table where 

					user_id='$email'";

					$userarr=$db_object->get_a_line($userquerry);

					$email=$userarr["email"];
	
					$name=$userarr["username"];
				}

				else
				{
					$email=$titlearr[$l]["url"];

				}

			$textstr="";
		
				$pattern="/<{freq_loopstart}>(.*?)<{freq_loopend}>/s";
				preg_match($pattern,$mytext,$out);

				$freqtext=$out[1];

			//echo "freqtext=$freqtext";exit;

				

				//print_R($gbl_freq_array);

				$freq_array=array_keys($gbl_freq_array);

				$strfreq="";

				//print_R($gbl_freq_array);

				$freqselected="";

				$frequency=trim($frequency);
	
				for($k=0;$k<count($freq_array);$k++)
				{
					$index=trim($freq_array[$k]);

					$freq=$gbl_freq_array[$index];

					//echo "$index , $frequency<br>";

					if($index == $frequency )
					{
						$freqselected = "selected";
					}
					else
					{
						$freqselected = "";
					}
				
				$strfreq.=preg_replace("/<{(.*?)}>/e","$$1",$freqtext);

				}

				//echo "strfreq=$strfreq";
				

				$mytextr=preg_replace($pattern,$strfreq,$mytext);

				//echo "mytext=$mytextr";

				//$mytext=preg_replace($pattern,$mytextr,$mytext);

				$strfeed.=preg_replace("/<{(.*?)}>/e","$$1",$mytextr);

			}

	$inn=preg_replace("/<{text_loopstart}>(.*?)<{text_loopend}>/s",$strfeed,$inn);
		
	$str.=preg_replace("/<{(.*?)}>/e","$$1",$inn);


	}

	

	$returncontent=preg_replace("/<{skills_loopstart}>(.*?)<{skills_loopend}>/s",$str,$returncontent);

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);		

	echo $returncontent;

}

		

	

	function update($db_object,$common,$user_id,$default,$skill_id)
	{
		$assign_solution_builder=$common->prefix_table("assign_solution_builder");
		
		$qry="update $assign_solution_builder set pstatus='a' where user_id='$user_id' and skill_id='$skill_id'";
		
		$db_object->insert($qry);
	}
}
$obj= new learning_plan();

if($save)
{
$action="submit";
}

switch($action)
{
case null:

$obj->show_form($db_object,$common,$learning,$default,$user_id,$action,$gbl_freq_array,$linkid);

break;

case "plan":

//$obj->update($db_object,$common,$user_id,$default,$skill);
$obj->show_form($db_object,$common,$learning,$default,$user_id,$action,$gbl_freq_array,$linkid);
break;
case "submit":

$learning->Save_finishlater($db_object,$common,$default,$user_id,$post_var,$gbl_freq_array);

$learning->show_aftersave($db_object,$common,$default,$user_id,$gbl_freq_array);
break;


}

include "footer.php";

?>
