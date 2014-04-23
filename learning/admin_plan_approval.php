<?
include("../session.php");
include("header.php");

class plan_approval
{

	function admin_plan_approval($db_object,$common,$_POST,$user_id,$fUser_id,$error_msg,$learning)
	{
		
		$approved_devbuilder=$common->prefix_table("approved_devbuilder");
		$config		=$common->prefix_table("config");
		$user_table	=$common->prefix_table("user_table");
		$assign_solution_builder=$common->prefix_table("assign_solution_builder");
		
	$keys=array_keys($_POST);
	$res=$_POST;
	$k=0;

	for($i=0;$i<(count($keys)-2);$i++)
	{
	
	$explode_key=explode("_",$keys[$i]);
	

	$skill_id[$k]=$explode_key[1];
		
	$k++;
	}
	
	
	$skill_id=array_unique($skill_id);


	for($i=0;$i<count($skill_id);$i++)
	{
		$skill_key=array_keys($skill_id);
		
		$skill_key=$skill_key[$i];
		
		$skill=$skill_id[$skill_key];
	
		$sql="update $approved_devbuilder set pstatus='a',mailsent_date=curdate(),last_updated_date=curdate() where user_id='$user_id' and skill_id='$skill'";	
		$db_object->insert($sql);

		$mysql	="update $assign_solution_builder set plan_updated_date=curdate(),plan_approved_date=curdate() where user_id='$user_id' and skill_id='$skill'";
		$db_object->insert($mysql);
		
		$learning->send_feedback($db_object,$common,$user_id,$skill,$fUser_id);
	
	}
	
		$mysql	="select username,email from $user_table where user_id='$user_id'";
		$dUser	=$db_object->get_a_line($mysql);
		
		$mysql	="select email from $user_table where user_id='$fUser_id'";
		$dAdmin	=$db_object->get_a_line($mysql);

		$mysql	="select lplan_approved_subject,lplan_approved_message from $config where id=1";
		$dMail	=$db_object->get_a_line($mysql);
		
		$subject	=$dMail["lplan_approved_subject"];
		$message	=$dMail["lplan_approved_message"];
				
		$username =$dUser["username"];
		$to		=$dUser["email"];
		$from	=$dAdmin["email"];
		$message	=preg_replace("/{{(.*?)}}/e","$$1",$message);
	
		$common->send_mail($to,$subject,$message,$from);

		echo $error_msg['cPlanApproved'];
		
		/*$key=$keys[$i];
	
	$date=$res[$key];
	
	$date=$this->changedate_database($date);*/

	
	/*$build=$explode_key[2];

	$qry="select interbasic_id from approved_devbuilder where build_id='$build'";

	$result_qry=$db_object->get_a_line($qry);

	$interbasic_id=$result_qry[interbasic_id];*/

/*	if(($interbasic_id == 13) or ($interbasic_id == 14))
	{


	$k=0;
	for($j=$i;$j<($i+4);$j++)
	{
	
	$key=$keys[$j];

	$arr[$k]=$res[$key];

	$k++;

	}
	$i=$i+3;

	$date=$this->changedate_database($arr[2]);

	if($interbasic_id==14)
	{

	$qry="select user_id from user_table where email='$arr[1]'";

	$qry_result=$db_object->get_a_line($qry);

	$url=$qry_result[user_id];
	}
	else
	{

	$url=$arr[1];
	}

	$sql="update approved_devbuilder set title='$arr[0]',cdate='$date',url='$url',frequency='$arr[3]',pstatus='a' where build_id='$build'";

	}
	if($interbasic_id>=1 and $interbasic_id<=11)
	{
	$sql="update approved_devbuilder set cdate='$date',pstatus='a' where build_id='$build'";
	}*/

	
	/*$db_object->insert($sql);


	}

		$mysql="update approved_devbuilder set pstatus='a' where user_id='$user_id' and skill_id='$skill_id'";

	

	$db_object->insert($mysql);*/
	

	}

function admin_change($db_object,$common,$_POST,$fUser_id)
{

	$approved_devbuilder=$common->prefix_table("approved_devbuilder");
	$assign_solution_builder=$common->prefix_table("assign_solution_builder");
	$config		=$common->prefix_table("config");
	$user_table	=$common->prefix_table("user_table");


	$keys=array_keys($_POST);
	
	$res=$_POST;

	$c=count($keys);

	while(list($kk,$vv)=@each($_POST))
	{
		$$kk=$vv;

		if(ereg("^fActstart",$kk))
		{
			list($fActstart,$skill_id,$t_id)=split("_",$kk);
			$skill_arr=$skill_id;
		}
	}

	for($i=0;$i<count($keys);$i++)
	{
		$explode_key=explode("_",$keys[$i]);
	
		$date=$res[$i];
		
		$build=$explode_key[2];
		
		$sql="select interbasic_id from $approved_devbuilder where build_id='$build'";
	
		$result=$db_object->get_a_line($sql);
	
		if($result[interbasic_id]>=1 and $result[interbasic_id]<=11)
		{

			$key=$keys[$i];

			$ccdate=$res[$key];

			$ccdate=$this->changedate_database($ccdate);
	
			/*$ccdate=explode("/",$ccdate);

			$ccdate=$ccdate[2]."-".$ccdate[0]."-".$ccdate[1];*/
	
			$update_sql="update $approved_devbuilder set cdate='$ccdate' where build_id='$build'";

			//echo $update_sql;

			$db_object->insert($update_sql);
	
		}

		if($result[interbasic_id]==13 or $result[interbasic_id]==14)
		{
			$k=0;
			for($j=$i;$j<($i+4);$j++)
			{

				$key=$keys[$j];
	
				$update_res[$k]=$res[$key];
	
				$k++;
	
			}
	
			$i=$i+3;
	

			$title=$update_res[0];
	
			$url=$update_res[1];

			$freq=$update_res[3];
	
			if($result[interbasic_id]==14)
			{
				$sql="select user_id from $user_table where email='$url'";

				$result=$db_object->get_a_line($sql);

				$url=$result[user_id];
			}

			$date=$update_res[2];

			$date=$this->changedate_database($date);

			$update_sql="update $approved_devbuilder set cdate='$date',url='$url',title='$title',frequency='$freq' where build_id='$build'";


			$db_object->insert($update_sql);
	
	
		}
	}
		$user_id=$_POST['user_id'];
		$mysql	="select username,email from $user_table where user_id='$user_id'";
		$dUser	=$db_object->get_a_line($mysql);
		
		$mysql	="select email from $user_table where user_id='$fUser_id'";
		$dAdmin	=$db_object->get_a_line($mysql);

		$mysql	="select lplan_resubmitted_subject,lplan_resubmitted_message from $config where id=1";
		$dMail	=$db_object->get_a_line($mysql);
		
		$subject	=$dMail["lplan_resubmitted_subject"];
		$message	=$dMail["lplan_resubmitted_message"];
				
		$username =$dUser["username"];
		$to		=$dUser["email"];
		$from	=$dAdmin["email"];
	//	$here     ="<a href='learning_plan.php?linkid=$user_id'>here</a>";
		$message	=preg_replace("/{{(.*?)}}/e","$$1",$message);

		$common->send_mail($to,$subject,$message,$from);
		
		$mysql	="update $assign_solution_builder set plan_updated_date=curdate() where user_id='$user_id' and skill_id='$skill_id'";
		$db_object->insert($mysql);
		
		$mysql	="update $approved_devbuilder set pstatus='r' where user_id='$user_id' and skill_id='$skill_id'";
		$db_object->insert($mysql);
			
}

	function show_form($db_object,$common,$default,$user_id,$skill_id,$gbl_freq_array,$error_msg,$learning)
	{

	$path=$common->path;
	$skills_table=$common->prefix_table("skills");
	$learning_settings=$common->prefix_table("learning_settings");
	$config=$common->prefix_table("config");
	$user_table=$common->prefix_table("user_table");
	$dev_basic=$common->prefix_table("dev_basic");
	$dev_interbasic=$common->prefix_table("dev_interbasic");
	$approved_devbuilder=$common->prefix_table("approved_devbuilder");
	$assign_solution_builder=$common->prefix_table("assign_solution_builder");
	$position_table = $common->prefix_table(position);

/*	$sql="select build_id from $approved_devbuilder where pstatus='u'";

	$result=$db_object->get_rsltset($sql);*/
	
	



		$xFile=$path."/templates/learning/admin_approval_learning_plan.html";

	
	/*else
	{
		if($result[0] == "")
		{
		
	 	$xFile=$path."/templates/learning/approval_learning_plan.html";
		}
		else	
		{
		
		$xFile=$path."/templates/learning/learning_plan.html";

		}
	}*/

	$returncontent=$common->return_file_content($db_object,$xFile);

	$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table join $approved_devbuilder  
		where $approved_devbuilder.skill_id=$skill_id and $approved_devbuilder.skill_id=$skills_table.skill_id
		and $approved_devbuilder.user_id=$user_id and ($approved_devbuilder.pstatus ='t' or $approved_devbuilder.pstatus ='r') group by 
		$approved_devbuilder.skill_id ";

	$arr=$db_object->get_rsltset($mysql);

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
		
		$mysql	="select plan_updated_date from $assign_solution_builder where user_id='$user_id' and skill_id='$skills'";
		$dDate=$db_object->get_a_line($mysql);

		$updated_date=$learning->date_display($dDate['plan_updated_date']);
		
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

				$sqltitle="select * from $approved_devbuilder where interbasic_id='$ib_id' and skill_id='$skills' and user_id='$user_id'and title!='' and (pstatus='t' or pstatus='r')";

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

					$act_sdate=$this->changedate_display($act_sdate);

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

					$act_date=$this->changedate_display($act_sdate);

				}
				}	
				$cChanges =$error_msg['cChanges'];

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

			$sqltitle="select title,build_id,cdate,interbasic_id from $approved_devbuilder where interbasic_id='$ib_id' and skill_id='$skills' and user_id='$user_id'and title!='' and (pstatus='t' or pstatus='r')";

			$titlearr=$db_object->get_rsltset($sqltitle);

			

			preg_match("/<{apptitle_loopstart}>(.*?)<{apptitle_loopend}>/s",$returncontent,$outtitle);

			$mytitle=$outtitle[0];
			
			$titlestr="";

			for($k=0;$k<count($titlearr);$k++)
			{

				$title=$titlearr[$k]["title"];
				
				
				$text=$titlearr[$k]["build_id"];

				$app_sdate=$titlearr[$k]["cdate"];

				$inter_id = $titlearr[$k]["interbasic_id"];
				
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

			$app_sdate=$this->changedate_display($app_sdate);


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
			
			$mytext=$outid[0];

			//echo $mytext;exit;

			$textstr="";

			$sqltitle="select * from $approved_devbuilder where interbasic_id in('13','14') and skill_id='$skills' and user_id='$user_id'and title!='' and (pstatus='t' or pstatus='r') order by build_id";


			$titlearr=$db_object->get_rsltset($sqltitle);

			$sqltext="select dev_textbox from $config where id='1'";

			$arrtext=$db_object->get_a_line($sqltext);

			$c=$arrtext["dev_textbox"];
			
			$c=$c+$c;
		

			for($k=0;$k<$c;$k++)
			{
				$text=$titlearr[$k]["build_id"];

				$skills=$titlearr[$k]["skill_id"];	
						
				$title=$titlearr[$k]["title"];
				
				$email=$titlearr[$k]["url"];

				$ftype=$titlearr[$k]["f_type"];

				$app_sdate=$titlearr[$k]["cdate"];

				
			if($app_sdate!="")

			{

			if($app_sdate =='0000-00-00')
			{
			
				$sdate="";
			}
			else
			{

			$sdate=$this->changedate_display($app_sdate);


			}
		}


				$ib_id=$titlearr[$k]["interbasic_id"];

				$frequency=$titlearr[$k]["frequency"];

			/*	if($frequency=="Daily")
				{
					$selected_daily="selected";

				}

				else
				{
					$selected_daily="";
				
				}
				 if($frequency=="Weekly")
				{

					$selected_weekly="selected";
				}

				else
				{
					$selected_monthly="";

				}
				if($frequency=="Monthly")
				{

					$selected_monthly="selected";
				}
				
				else
				{
					$selected_monthly="";
				}
			*/	
	
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
					$email=$titlearr[$k]["url"];


				}
				
				$pattern="/<{freq_loopstart}>(.*?)<{freq_loopend}>/s";
	
				preg_match($pattern,$mytext,$out);

				$freqtext=$out[1];

				$freq_array=array_keys($gbl_freq_array);

				$strfreq="";

				$freqselected="";

				$frequency=trim($frequency);
	
				for($m=0;$m<count($freq_array);$m++)
				{
					$index=trim($freq_array[$m]);

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

			
				$mytextr=preg_replace($pattern,$strfreq,$mytext);

			
					$textstr.=preg_replace("/<{(.*?)}>/e","$$1",$mytextr);
			}
			
	$inn=preg_replace("/<{text_loopstart}>(.*?)<{text_loopend}>/s",$textstr,$inn);

	



	$str.=preg_replace("/<{(.*?)}>/e","$$1",$inn);


	}

	

	$returncontent=preg_replace("/<{skills_loopstart}>(.*?)<{skills_loopend}>/s",$str,$returncontent);

	
	$mysql	="select approvaltext_$default from $learning_settings where id=1";
	$dSettings=$db_object->get_a_line($mysql);

	$username = $common->name_display($db_object,$user_id);
	$approvaltext  =preg_replace("/{{(.*?)}}/e","$$1",$dSettings['approvaltext_1']);

	$array["ApprovalMessage"]=$approvaltext;
	$array["user"]=$user_id;
	$array["skill_id"]=$skill_id;
	$array["emp_name"]=$username;

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);		
	
	echo $returncontent;
	
	
	

}

function changedate_database($date)
{

	list($month,$date,$year)=explode("/",$date);

	$newdate="";

	$newdate=$year.'-'.$month.'-'.$date;


	return ($newdate);

}
		
function changedate_display($date)
{
list($year,$month,$date)=explode("-",$date);

	//$newdate="";

	$newdate=$month.'/'.$date.'/'.$year;


	return ($newdate);

}

	function front_display($db_object,$common,$user_id)
	{
	$path=$common->path;

	$filename=$path."/templates/learning/front_panel.html";

	$filecontent=$common->return_file_content($db_object,$filename,$user_id);


	$yes=$common->is_admin($db_object,$user_id);
	if(isset($yes))
	{
	
	$filecontent=preg_replace("/<{(.*?)}>/s","",$filecontent);
	}
	else
	{
	
	$filecontent=preg_replace("/<{adminarea_loopstart}>(.*?)<{adminarea_loopend}>/s","",$filecontent);
	
	}
	$value=array();

	$filecontent=$common->direct_replace($db_object,$filecontent,$value);

	echo $filecontent;
	}

		
	
}

$obj=new plan_approval();


if($submit)
{
$action="approve";

}

if($resubmit)
{

$action="resubmit";
}
$fUser_id=$user_id;
switch($action)
{
	case  "approve":
	

	$user_id=$_POST["user_id"];
	
	$obj->admin_plan_approval($db_object,$common,$_POST,$user_id,$fUser_id,$error_msg,$learning);

	$user_id='1';
	
//	$obj->front_display($db_object,$common,$user_id);

	

	break;

	case "resubmit":
	
	$user_id=$_POST["user_id"];
	
	$skill_id=$_POST["skill_id"];
	

	$obj->admin_change($db_object,$common,$_POST,$fUser_id);


	$obj->show_form($db_object,$common,$default,$user_id,$skill_id,$gbl_freq_array,$error_msg,$learning);
	
	break;

	case "alert":
	
	$user_id=$_GET["user_id"];
	
	$skill_id=$_GET["skill_id"];
	
	$obj->show_form($db_object,$common,$default,$user_id,$skill_id,$gbl_freq_array,$error_msg,$learning);
		
	break;


}

include_once("footer.php");

?>
