<?php
/*===============================================================
    SCRIPT: dev_solution.php
    AUTHOR: chrisranjana.com
    UPDATED: 26th of September, 2003
    
    DESCRIPTION
     This deals with the properties in the Developmental Solution.
===============================================================*/

include("../session.php");
include("header.php");
class editproperties
{

//-----------Coded for display of the form

function show_form($db_object,$common,$default,$user_id)
{
	$path=$common->path;

	$xFile=$path."/templates/learning/edit_solution.html";

	$returncontent=$common->return_file_content($db_object,$xFile);

	$temp_devbuilder=$common->prefix_table("temp_devbuilder");

	$dev_interbasic=$common->prefix_table("dev_interbasic");

	$dev_basic=$common->prefix_table("dev_basic");


	$temp_devbuilder=$common->prefix_table("temp_devbuilder");

	$lang_table=$common->prefix_table("language");

	$langsql="select lang_id,lang_$default as lang from $lang_table";

	$arrlang=$db_object->get_rsltset($langsql);

	preg_match("/<{language_loopstart}>(.*?)<{language_loopend}>/s",$returncontent,$out);

	$mylang=$out[0];

	$str="";

	for($i=0;$i<count($arrlang);$i++)
	{

		
		$myvar=$mylang;

		$lang=$arrlang[$i]["lang"];

		$lang_id=$arrlang[$i]["lang_id"];

		$mysol="select basic_id,coursetype_$lang_id from $dev_basic";

		$arrsol=$db_object->get_rsltset($mysol);

		preg_match("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$returncontent,$out);

		$mysol=$out[0];

		$inn="";

		for($j=0;$j<count($arrsol);$j++)

		{
			$myvar1=$mysol;
			
			$basic_id=$arrsol[$j][0];

			$solution=$arrsol[$j][1];

			$myib="select interbasic_id,coursename_$lang_id from $dev_interbasic where basic_id='$basic_id' ";

			$arrib=$db_object->get_rsltset($myib);

			preg_match("/<{course_loopstart}>(.*?)<{course_loopend}>/s",$returncontent,$out);
			
			$mycor=$out[0];

			$s="";

			for($k=0;$k<count($arrib);$k++)
			{
			
				$ib_id=$arrib[$k]["interbasic_id"];
				
				$course=$arrib[$k]["coursename_$lang_id"];
			
				$s.=preg_replace("/<{(.*?)}>/e","$$1",$mycor);
	 		}


		
			$str=preg_replace("/<{course_loopstart}>(.*?)<{course_loopend}>/s",$s,$myvar1);

			$inn.=preg_replace("/<{(.*?)}>/e","$$1",$str);

		}


	$str=preg_replace("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$inn,$myvar);


	$outer.=preg_replace("/<{(.*?)}>/e","$$1",$str);

	}

	$returncontent=preg_replace("/<{language_loopstart}>(.*?)<{language_loopend}>/s",$outer,$returncontent);

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);

	echo $returncontent;		
}

//-------------This function is coded for editing the details---------------

function onedit($db_object,$common,$default,$user_id,$_POST)
{


	$temp_devbuilder=$common->prefix_table("temp_devbuilder");

	$dev_interbasic=$common->prefix_table("dev_interbasic");

	$dev_basic=$common->prefix_table("dev_basic");

	$temp_devbuilder=$common->prefix_table("temp_devbuilder");

	$lang_table=$common->prefix_table("language");

	while(list($key,$value)=each($_POST))
	{
		$$key=$value;

		if(ereg("^solution",$key))
		{
			list($solution,$lang_id,$basic_id)=split("_",$key);

			$sol_array[$lang_id][$basic_id]=$value;
		}
		if(ereg("^course",$key))
		{
			list($course,$lang_id,$basic_id,$ib_id)=split("_",$key);

			$course_array[$lang_id][$basic_id][$ib_id]=$value;
		}

	}
	




		
		$lang_array=array_keys($sol_array);

		for($j=0;$j<count($lang_array);$j++)

		{

			$lang_id=$lang_array[$j];


			$array=$sol_array[$lang_id];



			$basicid_array=array_keys($array);


			for($k=0;$k<count($basicid_array);$k++)

			{	

				$basic_id=$basicid_array[$k];

				$solution=$sol_array[$lang_id][$basic_id];

				$array2=$course_array[$lang_id][$basic_id];
												
				$interbasicid_array=array_keys($array2);


				for($i=0;$i<count($interbasicid_array);$i++)

				{	

					$ib_id=$interbasicid_array[$i];

					$course=$course_array[$lang_id][$basic_id][$ib_id];


				
				$mysql="update $dev_basic set coursetype_$lang_id='$solution' where basic_id='$basic_id'";

				$db_object->insert($mysql);
				
				$mysql="update $dev_interbasic set coursename_$lang_id='$course' where interbasic_id='$ib_id'";

				//echo $mysql;
				$db_object->insert($mysql);


		
				}


			}	
		}
}

}
$obj=new editproperties ;


switch($action)
{
case null:
$obj->show_form($db_object,$common,$default,$user_id);
break;

case "submit":
$obj->onedit($db_object,$common,$default,$user_id,$_POST);
$obj->show_form($db_object,$common,$default,$user_id);
break;

}

include ("footer.php");

?>
