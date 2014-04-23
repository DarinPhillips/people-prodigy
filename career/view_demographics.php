<?php
include("../session.php");
include("header.php");
class View_Demo
{
	function view_employees($common,$db_object,$default,$user_id)
	{
		$path=$common->path;
		$xFile=$path."templates/career/core_data/view_demographics.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		$opp_status=$common->prefix_table("opportunity_status");

	$selqry="select eeo_id,category from $opp_status group by category";
	$catset=$db_object->get_rsltset($selqry);
preg_match("/<{outer_loopstart}>(.*?)<{outer_loopend}>/s",$xTemplate,$mat);
$replace=$mat[1];
	for($i=0;$i<count($catset);$i++)
	{
		$id=$catset[$i]["eeo_id"];
		$category=$catset[$i]["category"];
		$selqry="select eeo_id,tag from $opp_status where category='$category'";
		$tagset=$db_object->get_rsltset($selqry);
		$tagedset=$common->return_Keyedarray($tagset,"eeo_id","tag");
		$replace1=$common->singleloop_replace($db_object,"<{inner_loopstart}>","<{inner_loopend}>",$replace,$tagedset,$sel_val);
		$replaced.=$replace1;
		$replaced=preg_replace("/<{(.*?)}>/e","$$1",$replaced);

	}
	$xTemplate=preg_replace("/<{outer_loopstart}>(.*?)<{outer_loopend}>/s",$replaced,$xTemplate);
	
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vale);
		echo $xTemplate;
	}




//----------------function that prints the users with corresponding eeos


function search_for_data($common,$db_object,$default,$error_msg,$form_array,$user_id)
	{

		while(list($kk,$vv)=@each($form_array))
		{
			$$kk=$vv;
			if(ereg("fDemo_",$kk))
			{
				if($vv!="")
				{
					$ids=split("_",$kk);
					$id=$ids[1];
					$idset[$id]=$vv;
				}

			}
		}

$path=$common->path;
$xFile=$path."templates/career/core_data/view_demographic_employee.html";
$xTemplate=$common->return_file_content($db_object,$xFile);

//print_r($idset);		
		$user_eeo=$common->prefix_table("user_eeo");
		$user_table=$common->prefix_table("user_table");
		$opp_status=$common->prefix_table("opportunity_status");
$fields=$common->return_fields($db_object,$user_eeo);
$or="or ";
while(list($kk,$vv)=@each($idset))
{
	$selqry="select user_id,count(tag_id) as cnt from $user_eeo where ";
	$subqry="tag_id in (";
		for($i=0;$i<count($vv);$i++)
		{
			$val=$vv[$i];
			$subqry.="'$val',";
			$sub1.="'$val',";
			$sub2.="eeo_id='$val' ".$or;
		}
			$cnt1+=count($vv);

		$catsel="select category from $opp_status where eeo_id='$kk'";
		$catarr=$db_object->get_a_line($catsel);
		$newcat[]=$catarr["category"];
}

$tempsub=$sub1;
 $sub1="tag_id in (".$sub1;
 $sub1=substr($sub1,0,-1);
		$sub1.=") group by user_id";
		$sub1=$selqry.$sub1;


//-----------This query is collect the users with the particular count of tags		
//echo $sub1;		
		$rslt1=$db_object->get_rsltset($sub1);
		//print_R($rslt1);
$finalrslt1=$common->return_Keyedarray($rslt1,"user_id","cnt");
//------This array prints the count of the charecteristics and the corresponding user id

//		print_r($finalrslt1);
if(count($finalrslt1)>0)
	{
		for($i=0;$i<count($rslt1);$i++)
		{
			$srcnt=$rslt1[$i]["cnt"];
			if($cnt1==$srcnt)
			{
				$searchusrid1[]=$rslt1[$i]["user_id"];
			}
		}
			
	}

//--This array prints the searched userids with the particular match

if($searchusrid1[0]=="")
{
	echo $error_msg["cUsersNotExists"];
	
	include_once("footer.php");exit;
}

	
//	print_R($searchusrid1);
	$userarray=$searchusrid1;
$andadmin="and admin_id='$user_id'";
	
	$selqry="select user_id,username from $user_table where "; 
		for($i=0;$i<count($userarray);$i++)
		{	
			$usr_id=$userarray[$i];
			$sub.="user_id='$usr_id' ".$or;
		}
		if($sub)
		{
	$sub=substr($sub,0,-4);
	$selqry.=$sub.$andadmin;

	$userrslt=$db_object->get_rsltset($selqry);
	
		}
		
//	$toprint=$common->return_Keyedarray($userrslt,"user_id","username");

for($i=0;$i<count($userrslt);$i++)
{
	$temp_id=$userrslt[$i]["user_id"];
	$userrslt[$i]["username"]=$common->name_display($db_object,$temp_id);	
}
//print_R($userrslt);
	
	$strlen=strlen($or);
	$sub2=substr($sub2,0,-$strlen);
	$feds=$common->return_fields($db_object,$opp_status);
	$selqry="select $feds from $opp_status where $sub2";
	$tagset=$db_object->get_rsltset($selqry);
	preg_match("/<{Uselected_loopstart}>(.*?)<{Uselected_loopend}>/s",$xTemplate,$mat);
	$replace=$mat[1];
//---print_r($newcat);	
	for($i=0;$i<count($tagset);$i++)
	{
		$category=$tagset[$i]["category"];
		$tag=$tagset[$i]["tag"];
		$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
	}
$xTemplate=preg_replace("/<{Uselected_loopstart}>(.*?)<{Uselected_loopend}>/s",$replaced,$xTemplate);

$values["employee_loop"]=$userrslt;
$xTemplate=$common->multipleselect_replace($db_object,$xTemplate,$values,$sel_arr);
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vale);
echo $xTemplate;
	}
}
$demobj= new View_Demo;
if($fSubmit)
{
	$demobj->search_for_data($common,$db_object,$default,$error_msg,$post_var,$user_id);
	exit;
}


$demobj->view_employees($common,$db_object,$default,$user_id);
include("footer.php");

?>
