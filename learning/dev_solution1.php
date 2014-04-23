
<?php
/*===============================================================
    SCRIPT: dev_solution1.php
    AUTHOR: chrisranjana.com
    UPDATED: 25th of September, 2003
    
    DESCRIPTION
     This deals with the analyses Developmental Solution.
===============================================================*/

include("../session.php");
include("header.php");
class solution

{
function save_finish_later($db_object,$common,$default,$_POST,$user_id,$error_msg)
{

	$dummy=$_POST["tech"];

	if($dummy=="")
	{
		$skills=$_POST["ip"];
	}
	else
	{
		$skills=$dummy;
	}

if(($_POST["tech"] && $_POST["ip"])=="")
	{
	$skills=$_POST["skill_id"];

	}
	
	$dev_basic=$common->prefix_table("dev_basic");

	$position_table=$common->prefix_table("position");

	$user_table=$common->prefix_table("user_table");

	$dev_interbasic=$common->prefix_table("dev_interbasic");

	$temp_devbuilder=$common->prefix_table("temp_devbuilder");
	
	
	while(list($key,$value)=each($_POST))
	{
		$$key=$value;

		if(ereg("^title",$key))
		{
			list($title,$basic_id,$ib_id,$tid)=split("_",$key);

			$title_array[$basic_id][$ib_id][$tid]=$value;

			$p=0;

			if($ib_id=="12" and $value!="")
			{
				$positionsql="select position_name from $position_table";

				$positionarr=$db_object->get_rsltset($positionsql);	

				for($i=0;$i<count($positionarr);$i++)
				{
					$position=$positionarr[$i]["position_name"];
	
					if($position==$value)
					{
						break;
					}
					else
					{
						$p=$p+1;

						continue;
					}
				}

				if($p==count($positionarr))
			
				{
					echo $error_msg['cValidPosition'];
				}	
				
			
				
			}
				
				
		}

		if(ereg("^description",$key))
		{
			list($description,$basic_id,$ib_id,$tid)=split("_",$key);

			$desc_array[$basic_id][$ib_id][$tid]=$value;
		}

		if(ereg("^url",$key))
		{
			list($url,$basic_id,$ib_id,$tid)=split("_",$key);

			$url_array[$basic_id][$ib_id][$tid]=$value;
			
			if($ib_id=='14' and $value!="")

			{

			$mysql="select email from $user_table ";

			$arr=$db_object->get_rsltset($mysql);

			$c=0;
			
			for($i=0;$i<count($arr);$i++)
			{

				$email=$arr[$i]["email"];

				
				if($value=="$email")
				{
				
					break;
									
				}
				else
				{
					
					$c=$c+1;

					continue;
				}


			}
			



			if($c==count($arr))
			{
				echo $error_msg['cValidemail'];
				//$this->show_form($db_object,$common,$default,$_POST,$user_id,$error_msg);
		
				//exit;	
	
				
			}
				
			}
	}
}





	$mysql="delete from $temp_devbuilder where skill_id='$skills' and user_id='$user_id'";
	
	$db_object->insert($mysql);



	$basic_array=array_keys($title_array);


	
	for($i=0;$i<(count($basic_array));$i++)
	{
		$basic_id=$basic_array[$i];


		$array2=$title_array[$basic_id];


		$ib_array=array_keys($array2);

		for($j=0;$j<count($ib_array);$j++)

		{

			$ib_id=$ib_array[$j];


			$array3=$title_array[$basic_id][$ib_id];

			$tid_array=array_keys($array3);

			for($k=0;$k<count($tid_array);$k++)

			{	

				$t_id=$tid_array[$k];
//echo "t_ID=$t_id";

				$title=$title_array[$basic_id][$ib_id][$t_id];

				$desc=$desc_array[$basic_id][$ib_id][$t_id];

				$url=$url_array[$basic_id][$ib_id][$t_id];

				if($ib_id=="12")
				{
					$possql="select pos_id from $position_table where position_name='$title'";


					$posarr=$db_object->get_a_line($possql);

					$title=$posarr["pos_id"];

				}

				if($ib_id=="14")
			
				{
				$email="select user_id from $user_table where email='$url'";

				$arr=$db_object->get_a_line($email);

				$url=$arr["user_id"];

		
				}


				$mysql="insert into $temp_devbuilder set title='$title',

				description='$desc',url='$url',

				basic_id='$basic_id',interbasic_id='$ib_id',skill_id='$skill_id',user_id='$user_id',key_id='$t_id'";

				$insert=$db_object->insert($mysql);

			}
		

		}	
		
	}


}

function show_form ($db_object,$common,$default,$_POST,$user_id,$error_msg)

{


	$dummy=$_POST["tech"];


	if($dummy=="")
	{
		$skills=$_POST["ip"];
	}
	else
	{
		$skills=$dummy;
	}

	if($_POST["tech"]=="" && $_POST["ip"]=="")
	{
	$skills=$_POST["skill_id"];

	}

	$path=$common->path;

	$xFile=$path."/templates/learning/dev_solution2.html";

	$returncontent=$common->return_file_content($db_object,$xFile);


	$skills_table=$common->prefix_table("skills");

	$config=$common->prefix_table("config");

	$user_table=$common->prefix_table("user_table");
	
	$dev_basic=$common->prefix_table("dev_basic");

	$position_table=$common->prefix_table("position");

	$dev_interbasic=$common->prefix_table("dev_interbasic");

	$temp_devbuilder=$common->prefix_table("temp_devbuilder");

	
	$mysql="select skill_name from $skills_table where skill_id='$skills'";


	$arr=$db_object->get_a_line($mysql);

	$skill_name=$arr["skill_name"];

	$array["skill"]=$skill_name;

	preg_match("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$returncontent,$outid);

	$myid=$outid[0];

	$string=" ";

	$mysqlid="select basic_id,coursetype_$default from $dev_basic";

	$arrid=$db_object->get_rsltset($mysqlid);

	

	for($j=0;$j<count($arrid);$j++)
	{
	
		$outer=$myid;

		$basic_id=$arrid[$j]["basic_id"];

		$sol_names=$arrid[$j]["coursetype_$default"];

		$mysqlsolution="select coursetype_$default from $dev_basic where

		basic_id=$basic_id";

		$arrsol=$db_object->get_rsltset($mysqlsolution);	

		$ways="select coursename_$default,interbasic_id from $dev_interbasic where basic_id='$basic_id'";
		
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
				$Display=$error_msg["cEmailfield"];
				}
			else
				{
				$Display=$error_msg["cUrl"];
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

				$mysql="select title,description,url from $temp_devbuilder 

				where skill_id='$skills'  and user_id='$user_id'and basic_id='$basic_id' 

				and interbasic_id='$interbasic_id'and key_id='$text'";

				$arr=$db_object->get_a_line($mysql);

				
					
				$title=$arr["title"];

				$desc=$arr["description"];

				$url=$arr["url"];

				if($interbasic_id=="12")
				{
				$pos="select position_name from $position_table where pos_id='$title'";

				$posarr=$db_object->get_a_line($pos);

				$title=$posarr["position_name"];
				}
				

				
				if($interbasic_id=="14")
				{
			
				$foremail="select email from $user_table where user_id='$url'";

				$foremailarr=$db_object->get_a_line($foremail);

				$url=$foremailarr["email"];

			
				}

				$s.=preg_replace("/<{(.*?)}>/e","$$1",$mytext);


			}


			$inner=preg_replace("/<{text_loopstart}>(.*?)<{text_loopend}>/s",$s,$inner);

			$inner=preg_replace("/<{(.*?)}>/e","$$1",$inner);

			$str.=$inner;
 
		}

		$outer=preg_replace("/<{ways1_loopstart}>(.*?)<{ways1_loopend}>/s",$str,$outer);

		$hidib_id=$hidib_id+$i;


		$outer=preg_replace("/<{(.*?)}>/e","$$1",$outer);

		$string.=$outer;
	
	}


	$returncontent=preg_replace("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$string,$returncontent);
		
	$array["skill_id"]=$skills;

	$array["nooftext"]=$nooftext;

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);		

	echo $returncontent;

}
	
}
$obj=new solution;
$obj->show_form($db_object,$common,$default,$_POST,$user_id,$error_msg);	

$obj->save_finish_later($db_object,$common,$default,$_POST,$user_id,$error_msg);
include ("footer.php");

?>
