<?php
/*===============================================================
    SCRIPT: feedback.php
    AUTHOR: chrisranjana.com
    UPDATED: 9 of October, 2003
    
    DESCRIPTION
     This deals with the analyses Developmental Solution.
===============================================================*/

include("../session.php");
include("header.php");
class solution
{

function show_form ($db_object,$common,$default,$_GET,$user_id,$error_msg)

{


	$skills=$_GET["skill_id"];
	if($_GET["user_id"])
	{
		$user_id=$_GET["user_id"];
	}

	$path=$common->path;

	$xFile=$path."/templates/learning/feedback.html";

	$returncontent=$common->return_file_content($db_object,$xFile);


	$skills_table=$common->prefix_table("skills");
	$config=$common->prefix_table("config");
	$user_table=$common->prefix_table("user_table");
	$dev_basic=$common->prefix_table("dev_basic");
	$position_table=$common->prefix_table("position");
	$dev_interbasic=$common->prefix_table("dev_interbasic");
	$approved_devbuilder=$common->prefix_table("approved_devbuilder");

	
	$mysql="select skill_name from $skills_table where skill_id='$skills'";
	$arr=$db_object->get_a_line($mysql);

	$skill_name=$arr["skill_name"];

	$array["skill"]=$skill_name;

		

		$ways="select coursename_$default,interbasic_id from $dev_interbasic where basic_id='5'";
		
		$arrway=$db_object->get_rsltset($ways);

		preg_match("/<{ways1_loopstart}>(.*?)<{ways1_loopend}>/s",$returncontent,$out);

		$myvar=$out[0];

		$str=" ";

		for($i=0;$i<count($arrway);$i++)

		{
			$inner=$myvar;

			$types=$arrway[$i][0];

			$interbasic_id=$arrway[$i][1];

			if($interbasic_id==14)
				{
				
				$contents=$error_msg["cAskemail"];
				
				$coments=$error_msg["cFeedback_internal"];

				}
			else
				{
				$coments=$error_msg["cFeedback_generic"];
			

				}
		
			preg_match("/<{text_loopstart}>(.*?)<{text_loopend}>/s",$returncontent,$outin);

			$mytext=$outin[0];
			
			$s=" ";

			$confi="select dev_textbox from $config where id='1'";

			$textbox=$db_object->get_a_line($confi); 


			$nooftext=$textbox["dev_textbox"];


			for($k=0;$k<$nooftext;$k++)
			{

	  		
				$text=$k;

				$mysql="select title,description,url,build_id from $approved_devbuilder 
					where skill_id='$skills'  and user_id='$user_id'and basic_id='5' 
					and interbasic_id='$interbasic_id'and key_id='$text'";

				$arr=$db_object->get_a_line($mysql);
				
				$buildid=$arr["build_id"];
				
				$title=$arr["title"];

				$desc=$arr["description"];

				$url=$arr["url"];

				if($interbasic_id=="14")
				{
			
				$foremail="select email from $user_table where user_id='$url'";

				$foremailarr=$db_object->get_a_line($foremail);

				$url=$foremailarr["email"];

			
				}

				
				$s.=preg_replace("/<{(.*?)}>/e","$$1",$mytext);


			}


			$inner=preg_replace("/<{text_loopstart}>(.*?)<{text_loopend}>/s",$s,$myvar);

			$inner=preg_replace("/<{(.*?)}>/e","$$1",$inner);

			$str.=$inner;
 
		}

		$returncontent=preg_replace("/<{ways1_loopstart}>(.*?)<{ways1_loopend}>/s",$str,$returncontent);

		
	
	
	$array["skill_id"]=$skills;

	$array["nooftext"]=$nooftext;

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);		

	echo $returncontent;
}
}
$obj=new solution;
$obj->show_form($db_object,$common,$default,$_GET,$user_id,$error_msg);
include ("footer.php");
?>
