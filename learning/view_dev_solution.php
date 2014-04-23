<?
include("../session.php");
include("header.php");

class view
{

	function show_skill_links($db_object,$common,$default,$fEmployee_id,$error_msg)
	{
		
		
		$path=$common->path;
		
		$xTemplate=$path."/templates/learning/view_devsolution_links.html";
		
		$content=$common->return_file_content($db_object,$xTemplate);
		
		$assign_solution_builder=$common->prefix_table("assign_solution_builder");
		
		$skills=$common->prefix_table("skills");
		
		$user_table=$common->prefix_table("user_table");
		
		$qry="select username from $user_table where user_id='$fEmployee_id'";
		
		$user=$db_object->get_a_line($qry);
		
		$username=$user[username];
		
		$sql="select skill_id from $assign_solution_builder where user_id='$fEmployee_id'";		
		
		$result=$db_object->get_rsltset($sql);
		
		if($result[0] == "")
		{
			echo $error_msg["cnoSolution"];
			
			include_once("footer.php");
			
			exit;
		}
		
		for($i=0;$i<count($result);$i++)
		{
			$arr_id[$i]=$result[$i][skill_id];
			
		}
		
		$arr_unique=@array_unique($arr_id);
		
		preg_match("/<{link_loopstart}>(.*?)<{link_loopstop}>/s",$content,$match);
		
		$match=$match[0];
		
		$keys=@array_keys($arr_unique);
		
		for($i=0;$i<count($arr_unique);$i++)
		{
			$key=$keys[$i];
			
			$skill_id=$result[$key][skill_id];
			
			$name_qry="select skill_name from $skills where skill_id='$skill_id'";
			
			$result_name=$db_object->get_a_line($name_qry);
			
			$skill_name=$result_name[skill_name];
			
			//$user_name=$username;
			
			$user_name=$common->name_display($db_object,$fEmployee_id);
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			
		}
		
		$content=preg_replace("/<{link_loopstart}>(.*?)<{link_loopstop}>/s",$str,$content);
		
		$content=$common->direct_replace($db_object,$content,$array);
		
		echo $content;
		
		
	}

	function view_solution($db_object,$common,$default,$skill_id,$error_msg)
	{

	$skills=$skill_id;
	
	$path=$common->path;
	
	$xFile=$path."/templates/learning/view_devsolution.html";

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

				$mysql="select title,description,url from $approved_devbuilder

				where skill_id='$skills' and basic_id='$basic_id' 

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


	$returncontent1=preg_replace("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$string,$returncontent);
		
	$array["skill_id"]=$skills;

	$array["nooftext"]=$nooftext;

	$returncontent1=$common->direct_replace($db_object,$returncontent1,$array);		

	echo $returncontent1;

	

	}
}
$obj=new view();

switch($action)
{
case NULL:
	$obj->show_skill_links($db_object,$common,$default,$fEmployee_id,$error_msg);
	
	break;

	
case "show":

	$obj->view_solution($db_object,$common,$default,$skill_id,$error_msg);
	
	break;
}
include_once("footer.php");
?>
