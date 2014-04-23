<?
include_once("../session.php");
class download
{
function download_file($db_object,$common,$approved_on,$default,$error_msg,$_POST)
{
	
	$user_id=$_POST[fUser_id];
	
	$skills=$_POST[fSkill_id];
	
		$path = $common->path;
		
		$xTemplate=$path."templates/learning/download_learning_solution.txt";
		
		$file=$common->return_file_content($db_object,$xTemplate);
		
		
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

	preg_match("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$file,$outid);

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

		preg_match("/<{ways1_loopstart}>(.*?)<{ways1_loopend}>/s",$file,$out);

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
		
			preg_match("/<{text_loopstart}>(.*?)<{text_loopend}>/s",$file,$outin);

			$mytext=$outin[0];
			
			$s=" ";

			$confi="select dev_textbox from $config where id='1'";

			$textbox=$db_object->get_a_line($confi); 


			$nooftext=$textbox["dev_textbox"];


			for($k=0;$k<$nooftext;$k++)
			{
	  		
				$text=$k;

				$mysql="select title,description,url from $approved_devbuilder

				where skill_id='$skills' and basic_id='$basic_id' and user_id='$user_id'  

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
				

			}//end of k for
			

			$inner=preg_replace("/<{text_loopstart}>(.*?)<{text_loopend}>/s",$s,$inner);

			$inner=preg_replace("/<{(.*?)}>/e","$$1",$inner);

			$str.=$inner;
 
		}//end of i for
		$outer=preg_replace("/<{ways1_loopstart}>(.*?)<{ways1_loopend}>/s",$str,$outer);

		$hidib_id=$hidib_id+$i;


		$outer=preg_replace("/<{(.*?)}>/e","$$1",$outer);

		$string.=$outer;
	
	}//end of j for


	$content=preg_replace("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$string,$file);
		
	$array["skill_id"]=$skills;

	$array["nooftext"]=$nooftext;
	
	$array["user_id"]=$user_id;

	$content=$common->direct_replace($db_object,$content,$array);		


		
		$file = $path."learning/learning_solution/solution_$user_id.txt";
		
		$fp=fopen($file,"w");
		
		fwrite($fp,$content);
	
		fclose($fp);
		
		if(file_exists($file))
		{

			$len  = filesize($file);
			$filename = "learning_solution_$user_id.txt";
			header("content-type: application/stream");
			header("content-length: $len");
			header("content-disposition: attachment; filename=$filename");
			$fp=fopen($file,"r");			
			fpassthru($fp);
			exit;
		}
		else
		{
			
$str=<<<EOD
		<script>
			alert( '$err[cEmptyrecords]' );
			window.location=document.referrer;
		</script>
EOD;
echo $str;

			
		}
	
	}
	
}//end class
	$ob  = new download;
	
	$ob->download_file($db_object,$common,$approved_on,$default,$err,$_POST);

?>
