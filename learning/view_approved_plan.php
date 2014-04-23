<?php
include_once("../session.php");

include_once("header.php");

class approved_plan
{

function show_preview($db_object,$common,$default,$user_id,$_GET,$gbl_freq_array,$error_msg)
	{

	$skills_table=$common->prefix_table("skills");
	$config=$common->prefix_table("config");
	$user_table=$common->prefix_table("user_table");
	$dev_basic=$common->prefix_table("dev_basic");
	$feedback=$common->prefix_table("feedback");
	$dev_interbasic=$common->prefix_table("dev_interbasic");
	$approved_devbuilder=$common->prefix_table("approved_devbuilder");

	
	$mysql="select skill_name,$approved_devbuilder.skill_id from $skills_table join $approved_devbuilder  
			where $approved_devbuilder.skill_id=$skills_table.skill_id and  
			$approved_devbuilder.user_id=$user_id and $approved_devbuilder.pstatus='a' group by 
			$approved_devbuilder.skill_id ";
	

	$result=$db_object->get_rsltset($mysql);


	
	if($result[0]=="")
	{
		echo $error_msg['cNoApprovedPlan'];
		
		include_once("footer.php");exit;
	}



	for($i=0;$i<count($result);$i++)
	{
		
		$skill_id=$result[$i][skill_id];
	
		$sql="select * from approved_devbuilder where user_id='$user_id' and skill_id='$skill_id' and pstatus='a'";
		//echo $sql;
		
		$result_sql.=$db_object->get_rsltset($sql);
	}
	

	if($result_sql[0]=="")
	{
		include_once("footer.php");
		exit;
	}	

	
	$path=$common->path;


	$username=$common->name_display($db_object,$user_id);

	$xFile=$path."/templates/learning/view_approved_plan.html";
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
		
		if($titlearr[0]!="")
		{

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
		}
		else
		{
			$first_replace=preg_replace("/<{maintext1_loopstart}>(.*?)<{maintext1_loopend}>/s","",$first_replace);
		}
//echo $first_replace;
		preg_match("/<{text2_loopstart}>(.*?)<{text2_loopend}>/s",$returncontent,$outid);

		$mytitle=$outid[0];
//echo "title=$mytitle";



		$titstr="";

		$sqltitle="select title,description,build_id,cdate,completed_date from $approved_devbuilder where interbasic_id in (10,11,12) and skill_id='$skills' and user_id='$user_id'and title!=''";
		
		$titlearr=$db_object->get_rsltset($sqltitle);
		
		if($titlearr[0]!="")
		{
		
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
		}
		
		else
		{
			
			$first_replace=preg_replace("/<{maintext2_loopstart}>(.*?)<{maintext2_loopend}>/s","",$first_replace);
		}
		
//echo $first_replace;

		preg_match("/<{text3_loopstart}>(.*?)<{text3_loopend}>/s",$returncontent,$outid);

		$mytitle=$outid[0];



		$titstr="";

		$sqltitle="select title,description,build_id,cdate,frequency from $approved_devbuilder where interbasic_id in (13,14) and skill_id='$skills' and user_id='$user_id'and title!=''";

		$titlearr=$db_object->get_rsltset($sqltitle);
//echo $sqltitle;
		if($titlearr[0]!="")
		{
		
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

		}
		else
		{
			$first_replace=preg_replace("/<{maintext3_loopstart}>(.*?)<{maintext3_loopend}>/s","",$first_replace);
		}




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
$obj=new approved_plan();

$obj->show_preview($db_object,$common,$default,$fEmployee_id,$_GET,$gbl_freq_array,$error_msg);	

include_once("footer.php");

?>
