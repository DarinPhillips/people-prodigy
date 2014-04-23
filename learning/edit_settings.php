<?php
/*
-----------------------------------------------
SCRIPT:edit_settings.php
AUTHOR:info@chrisranjana.com
DESCRIPTION: This module handles all the settings of the learning program.
--------------------------------------------
*/
include("../session.php");
include("header.php");
class Admin
{
 function edit_display($common,$db_object,$default)
{
	$path=$common->path;

	$xFile=$path."/templates/learning/edit_settings.html";

	$returncontent=$common->return_file_content($db_object,$xFile);

	$config=$common->prefix_table("config");

	$query="select dev_textbox as dev_text from $config";
	$array=$db_object->get_a_line($query);

	
	$learning_settings=$common->prefix_table("learning_settings");

	$mysql="select * from $learning_settings ";
	$ar_settings=$db_object->get_a_line($mysql);

	$array=array_merge($array,$ar_settings);

	//print_R($array);





	$language=$common->prefix_table("language");

	$mysql="select lang_id , lang_$default as language  from $language ";

	$arr=$db_object->get_rsltset($mysql);




		

		$pattern="/<{lang_loopstart}>(.*?)<{lang_loopend}>/is";
	
		preg_match($pattern,$returncontent,$out);

		$myvar=$out[1];

		$str="";

		for($i=0;$i<count($arr);$i++)
			{
				$lang_id=$arr[$i]["lang_id"];
				$language=$arr[$i]["language"];
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$myvar);
			}

		$returncontent=preg_replace($pattern,$str,$returncontent);



	$learning_result=$common->prefix_table("learning_result");
	$language=$common->prefix_table("language");

	$pattern="/<{optionlang_loopstart}>(.*?)<{optionlang_loopend}>/is";

		preg_match($pattern,$returncontent,$out);

		$outer=$out[1];


	$outer=$common->direct_replace($db_object,$outer,$array1);


	

		$str="";


	for($i=0;$i<count($arr);$i++)
		{

			$save_outer=$outer;

			$lang_id=$arr[$i]["lang_id"];
	
			//echo "lang_id=$lang_id";

			$language=$arr[$i]["language"];

			$rpattern="/<{option_loopstart}>(.*?)<{option_loopend}>/is";

			preg_match($rpattern,$outer,$out);

			$inner=$out[1];

			
			$mysql="select value, result_$lang_id from $learning_result ";

			$ar_result=$db_object->get_rsltset($mysql);

			$str1="";

	

				for($j=0;$j<count($ar_result);$j++)
				{
					$value=$ar_result[$j][0];

					$data=$ar_result[$j][1];

				$str1.=preg_replace("/<{(.*?)}>/e","$$1",$inner);



				}

				//echo $str1;



			$save_outer=preg_replace($rpattern,$str1,$save_outer);

			//echo "outer=$outer";



		
			

			$str.=preg_replace("/<{(.*?)}>/e","$$1",$save_outer);

		}

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);


	$returncontent=preg_replace($pattern,$str,$returncontent);
	

	




	
	echo $returncontent;
}


function update_settings($common,$db_object,$form_array,$error_msg)
   {

	$text_array=array();

	$option_array=array();

   	while(list($kk,$vv)=@each($form_array))
   	{
   		$$kk=$vv;
		
		if(ereg("^requesttext",$kk)) // system owner defined text for feedback request
		{
			list($n,$lang_id)=split("_",$kk);
			$text_array[$lang_id]=$vv;
		}
		if(ereg("^approvaltext",$kk)) // system owner defined text for feedback request
		{
			list($n,$lang_id)=split("_",$kk);
			$approval_text_array[$lang_id]=$vv;
		}

		if(ereg("^option",$kk))
			{
				list($gg,$value,$lang)=split("_",$kk);
				$option_array[$value][$lang]=$vv;
			}
   	}
   	 	
   	$config=$common->prefix_table("config");

	//print_r($text_array);

	$learning_settings=$common->prefix_table("learning_settings");

	$array=array_keys($text_array);
	$text_str="";

	for($i=0;$i<count($array);$i++)
		{
			$lang_id=$array[$i];
			$reg=$text_array[$lang_id];
			$text_str.=" requesttext_$lang_id = '$reg' ,";
			
		}

	$text_str=substr($text_str,0,-1);

/*
	$mysql="replace into $learning_settings set 
				id=1 , $text_str ";
	$db_object->insert($mysql);
*/
	$approval_array=array_keys($approval_text_array);
	$approval_text_str="";

	for($i=0;$i<count($approval_array);$i++)
		{
			$lang_id=$approval_array[$i];
			$approval_reg=$approval_text_array[$lang_id];
			$approval_text_str.=" approvaltext_$lang_id = '$approval_reg' ,";
			
		}

	$approval_text_str=substr($approval_text_str,0,-1);


	$mysql="replace into $learning_settings set 
			id=1 , $text_str ,$approval_text_str";
	$db_object->insert($mysql);


	$language=$common->prefix_table("language");

	$learning_result=$common->prefix_table("learning_result");

	$mysql="select lang_id from $language ";

	$arr=$db_object->get_single_column($mysql);

		
		//print_R($option_array);


			for($i=0;$i<count($option_array);$i++)

			{

				$clause="";


				for($j=0;$j<count($arr);$j++)
				{

					$lang=$arr[$j];

					$data=$option_array[$i][$lang];
				
				$clause.=" result_$lang='$data',";

				}

			$clause=substr($clause,0,-1);
		


			$mysql="update $learning_result set $clause where value='$i' ";

			$db_object->insert($mysql);

			//echo $mysql;


			}




  	$insqry="update $config set dev_textbox='$fdev_text' where id=1";
   	$db_object->insert($insqry);

	echo $error_msg["cSettingsedited"];
   	
   }



}
$obj= new Admin;

if($action=="edit")
	{
	$obj->update_settings($common,$db_object,$post_var,$error_msg);
	}


$obj->edit_display($common,$db_object,$default);



include("footer.php");

?>
