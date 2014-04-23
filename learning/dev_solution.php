<?php
/*===============================================================
    SCRIPT: dev_solution.php
    AUTHOR: chrisranjana.com
    UPDATED: 25th of September, 2003
    
    DESCRIPTION
     This deals with the analyses Developmental Solution.
===============================================================*/
include("../session.php");
include("header.php");

class solution
{

//---------------This functon is coded for the front panel of this modulle----


function front_panel($db_object,$common,$default,$user_id)
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


//---------------This functon is coded for the selection of skills

function select_skills($db_object,$common,$default,$user_id,$error_msg)
{

	$path=$common->path;
	
	$xFile=$path."/templates/learning/dev_solution.html";

	$returncontent=$common->return_file_content($db_object,$xFile);

	$skills_table=$common->prefix_table("skills");
	
	$assign_sol_builder=$common->prefix_table("assign_solution_builder");

	$approved_devbuilder=$common->prefix_table("approved_devbuilder");
	
	$mysql  = "select skill_id from $approved_devbuilder where user_id = '$user_id' and pstatus='a' group by skill_id";
	$dSkill =$db_object->get_single_column($mysql);
	if($dSkill[0] != "" )
	{
		$skill_set = implode(",",$dSkill);
		$extraqry  = " and $skills_table.skill_id not in ($skill_set) ";
	}
	else
	{	
		$extraqry = "";
	}

	
	if($user_id == '1' )
	{
		$mysql="select skill_id,skill_name from $skills_table where skill_type='i'";
	}
	else
	{
		$mysql="select $skills_table.skill_id,$skills_table.skill_name from $skills_table,$assign_sol_builder
				where $skills_table.skill_type='i' and $skills_table.skill_id=$assign_sol_builder.skill_id
		     	and $assign_sol_builder.user_id='$user_id' $extraqry group by $skills_table.skill_id ";
	}
	$arr=$db_object->get_rsltset($mysql);
	
	if($arr[0][0] == "")
	{
		echo $error_msg['cIPSkillnotassigned'];	
		$returncontent=preg_replace("/<{inter_start}>(.*?)<{inter_end}>/s","",$returncontent);
	}
	else
	{
		$returncontent=preg_replace("/<{inter_(.*?)}>/s","",$returncontent);
	}
	preg_match("/<{ip_loopstart}>(.*?)<{ip_loopend}>/s",$returncontent,$out);

	$myvar=$out[0];

	$str=" ";
	

	for($i=0;$i<count($arr);$i++)
	{
		$skills_id=$arr[$i]["skill_id"];

		$ip_skill=$arr[$i]["skill_name"];

		$ipstr.=preg_replace("/<{(.*?)}>/e","$$1",$myvar);

	 }

	$returncontent=preg_replace("/<{ip_loopstart}>(.*?)<{ip_loopend}>/s",$ipstr,$returncontent);
	
	
	
	if($user_id == '1' )
	{
		$mysql="select skill_id,skill_name from $skills_table where skill_type='t'";
	}
	else
	{
		$mysql="select $skills_table.skill_id,$skills_table.skill_name from $skills_table,$assign_sol_builder
				where $skills_table.skill_type='t' and $skills_table.skill_id=$assign_sol_builder.skill_id
		     	and $assign_sol_builder.user_id='$user_id' $extraqry group by $skills_table.skill_id";
	}
	$arr1=$db_object->get_rsltset($mysql);
	
	if($arr1[0][0] == "")
	{
		echo $error_msg['cTechSkillnotassigned'];	
		$returncontent=preg_replace("/<{technical_start}>(.*?)<{technical_end}>/s","",$returncontent);
	}
	else
	{
		$returncontent=preg_replace("/<{technical_(.*?)}>/s","",$returncontent);
	}
	preg_match("/<{tech_loopstart}>(.*?)<{tech_loopend}>/s",$returncontent,$out);

	$myvar=$out[0];

	$str=" ";

	for($i=0;$i<count($arr);$i++)
	{
		$skills_id=$arr1[$i]["skill_id"];

		$tech_skill=$arr1[$i]["skill_name"];

		$str.=preg_replace("/<{(.*?)}>/e","$$1",$myvar);

	 }

$returncontent=preg_replace("/<{tech_loopstart}>(.*?)<{tech_loopend}>/s",$str,$returncontent);

$array=array();

$returncontent=$common->direct_replace($db_object,$returncontent,$array);	
	
echo $returncontent;

}


//---------------This functon is coded for saving the contents in the 

//			database when they want to continue later.


function 
save_finish_later($db_object,$common,$default,$_POST,$user_id,$error_msg)
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
	$config=$common->prefix_table("config");
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
					echo $error_msg['cValidPosition'];;
				}	
			}//end of if
		}//end of outer if
				
	
	$user_name=$title_array[5][14];
	
	
		
		$textbox_qry="select dev_textbox from $config";
		
		$textbox_result=$db_object->get_a_line($textbox_qry);
		
		$no_box=$textbox_result[dev_textbox];
			

		if(ereg("^description",$key))
		{
			list($description,$basic_id,$ib_id,$tid)=split("_",$key);

			$desc_array[$basic_id][$ib_id][$tid]=$value;
			
		}
		
		

		if(ereg("^url",$key))
		{
			list($url,$basic_id,$ib_id,$tid)=split("_",$key);

			$url_array[$basic_id][$ib_id][$tid]=$value;
			
		}
	}//end of while
		
		
		$url=$url_array[5][14];
		
		
		
			if($ib_id=='14')
			{
			
			for($j=0;$j<$no_box;$j++)
			{
				$email=$url[$j];
				
			//	$user_name=$user_name[$j];
				
				$mysql="select email from $user_table where email='$email'";

			//	echo $mysql;
				
				$arr=$db_object->get_a_line($mysql);
				
				
				
				if($arr[0]!="")
				{
					if($email!=$arr[email])
					{
						echo $error_msg['cValidemail'];
					}
				}
				else
				{
			
					echo $error_msg['cInvalidemail'];
				}
			}//end of for
				
			}//end of if
		
		
		
	
		

	
//print_r( $url_array);




	$mysql="delete from $temp_devbuilder where skill_id='$skills' and user_id='$user_id'";
	
	$db_object->insert($mysql);



	$basic_array=@array_keys($title_array);


	
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
				

			}//end of for
		

		}//end of j for
		
	}//end of i for
}//end of function

	

	


//---------------This functon is coded for the display of the stored contents  ------------
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

	$assign_solution_builder=$common->prefix_table("assign_solution_builder");
		
	$skills_table=$common->prefix_table("skills");

	$config=$common->prefix_table("config");

	$user_table=$common->prefix_table("user_table");
	
	$dev_basic=$common->prefix_table("dev_basic");

	$position_table=$common->prefix_table("position");

	$dev_interbasic=$common->prefix_table("dev_interbasic");

	$temp_devbuilder=$common->prefix_table("temp_devbuilder");
	
	if($user_id != 1)
	{
	
	$skill_qry="select skill_id from $assign_solution_builder where user_id='$user_id'";

	$skill_result=$db_object->get_rsltset($skill_qry);
	
	$c=0;
	
	for($i=0;$i<count($skill_result);$i++)
	{
		$check_skill=$skill_result[$i][skill_id];
		
		if($skills!=$check_skill)
		{
			$c++;
		}
	}
	
	if($c==count($skill_result))
	{
		echo $error_msg["cSkillnotassigned"];
		
		$this->select_skills($db_object,$common,$default,$user_id,$error_msg);
		
		include_once("footer.php");
		
		exit;
		
	}
	}
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


	$returncontent=preg_replace("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$string,$returncontent);
		
	$array["skill_id"]=$skills;

	$array["nooftext"]=$nooftext;

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);		

	echo $returncontent;

}//end of function show form

function show_form_direct($db_object,$common,$default,$user_id,$error_msg,$skills)//comes directly from the front panel
{

	$assign_solution_builder=$common->prefix_table("assign_solution_builder");

	/*$status_qry="update $assign_solution_builder set status='a' where user_id='$user_id' and skill_id='$skills'";
	
	$db_object->insert($status_qry);*/
	
	/*$dummy=$_POST["tech"];

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

	}*/

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


	$returncontent=preg_replace("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$string,$returncontent);
		
	$array["skill_id"]=$skills;

	$array["nooftext"]=$nooftext;

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);		

	echo $returncontent;

}//end of function show form

//---------------This functon is coded for the approval of the admin
	
function submit_forapproval($db_object,$common,$default,$user_id,$_POST,$error_msg)
{
	
	$skill_id=$_POST["skill_id"];
	
	$user_table=$common->prefix_table("user_table");

	$config=$common->prefix_table("config");

	$skills=$common->prefix_table("skills");

	$dev_basic=$common->prefix_table("dev_basic");

	$dev_interbasic=$common->prefix_table("dev_interbasic");

	$temp_devbuilder=$common->prefix_table("temp_devbuilder");

	$position_table=$common->prefix_table("position");

	$unapproved_devbuilder=$common->prefix_table("unapproved_devbuilder");

	$approved_devbuilder=$common->prefix_table("approved_devbuilder");
	
	$assign_solution_builder=$common->prefix_table("assign_solution_builder");

	$mysql_temp="delete from $temp_devbuilder where skill_id='$skill_id' and user_id='$user_id'";
		
	$db_object->insert($mysql_temp);

	$mysql_unapp="delete from $unapproved_devbuilder where skill_id='$skill_id' and user_id='$user_id'";
	
	$db_object->insert($mysql_unapp);

	while(list($key,$value)=each($_POST))
	{
		$$key=$value;

		if(ereg("^title",$key))
		{
			list($title,$basic_id,$ib_id,$tid)=split("_",$key);

			$title_array[$basic_id][$ib_id][$tid]=$value;
			
			$user_name=$title_array[5][14];
			
					
			$p=0;

			if($ib_id==12 and $value!="")
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
				}//end of i for

				if($p==count($positionarr))
			
				{
					echo $error_msg['cValidPosition'];
					exit;
		
				}	
				
			
				
			}//end of if
				
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
		}
		
	}		$url=$url_array[5][14];
	
			
			
			$text_qry="select dev_textbox from $config";
			
			$text_result=$db_object->get_a_line($text_qry);
			
			$text_no=$text_result[dev_textbox];
			
			
									
			for($j=0;$j<$text_no;$j++)
			{
				
				$email=$url[$j];
				
			//	$user_name=$user_name[$j];
				
				$check_qry="select email from $user_table where email='$email'";
				
						
				$check_result=$db_object->get_a_line($check_qry);
				
				
				if($check_result[0]!="")
				{
					if($email!=$check_result[email])
					{
						echo $error_msg['cValidemail'];
						exit;			
											
					}
				}
				else
				{
					echo $error_msg['cInvalidemail'];
					exit;
				}
					
			}//end of for
		



//print_r( $url_array);

		
	$basic_array=array_keys($title_array);

	
	for($i=0;$i<count($basic_array);$i++)
	{
		$basic_id=$basic_array[$i];

		$array2=$title_array[$basic_id];


		$ib_array=array_keys($array2);

		for($j=0;$j<count($ib_array);$j++)

		{

			$ib_id=$ib_array[$j];

			$array3=$title_array[$basic_id][$ib_id];

			$tid_array=@array_keys($array3);

			for($k=0;$k<count($tid_array);$k++)

			{	

				$t_id=$tid_array[$k];

				$title=$title_array[$basic_id][$ib_id][$t_id];

				$desc=$desc_array[$basic_id][$ib_id][$t_id];

				$url=$url_array[$basic_id][$ib_id][$t_id];

				if($ib_id=="12")
				{
				$pos="select pos_id from $position_table where position_name='$title'";

				$posarr=$db_object->get_a_line($pos);

				$title=$posarr["pos_id"];
				
				}
				
				if($ib_id=="14")
			
				{
				$email="select user_id from $user_table where email='$url'";

				$arr=$db_object->get_a_line($email);

				$url=$arr["user_id"];

		
				}
				
				if($user_id!="1")
	
				{

				$mysqltemp="insert into $temp_devbuilder set title='$title',

				description='$desc',url='$url',

				basic_id='$basic_id',interbasic_id='$ib_id',skill_id='$skill_id',user_id='$user_id',key_id='$t_id'";

				$db_object->insert($mysqltemp);

				$mysql="insert into $unapproved_devbuilder set title='$title',

				description='$desc',url='$url',

				basic_id='$basic_id',interbasic_id='$ib_id',skill_id='$skill_id',user_id='$user_id',key_id='$t_id',status='u'";
				
				$db_object->insert($mysql);
				
				

				}
					
				else

				{
				
				$mysql="insert into $approved_devbuilder set title='$title',

				description='$desc',url='$url',

				basic_id='$basic_id',interbasic_id='$ib_id',skill_id='$skill_id',user_id='$user_id',key_id='$t_id',status='u'";
				
				$db_object->insert($mysql);
					
				}
					
				}//end of k for
		

		}//end of i for	
		
	}//end of j for
		
	

	$mysql="update $unapproved_devbuilder set status='u' where user_id='$user_id' and skill_id='$skill_id' ";

	$db_object->insert($mysql);

	if($user_id!='1')
	{

	$msgsql="select dev_subject,dev_message from $config ";

	$arr2=$db_object->get_rsltset($msgsql);

	$subject=$arr2[0][0];	

	$message=$arr2[0][1];

	$mailsql="select first_name,last_name,email from $user_table where user_id='$user_id'";

	$mailarr=$db_object->get_a_line($mailsql);

	$name=$mailarr["first_name"];

	$name.=$mailarr["last_name"];

	$from=$mailarr["email"];

	$from=$name." <".$from.">";

	$admin="select email from $user_table where user_id='1'";

	$arradmin=$db_object->get_a_line($admin);

	$to=$arradmin["email"];

	$skillsql="select skill_name from $skills where skill_id='$skill_id'";

	$arrskill=$db_object->get_a_line($skillsql);

	$skill_name=$arrskill["skill_name"];

	$array=array($from,$skill_name);

	$message=preg_replace("/\[(.*?)\]/e","$$1",$message);

	$common->send_mail($to,$subject,$message,$from);
	
	$sql="update $assign_solution_builder set status='a' where user_id='$user_id' and skill_id='$skill_id'";

	$db_object->insert($sql);
	}
	}

function approval_form($db_object,$common,$_POST)
{
	$path=$common->path;

	$xFile=$path."/templates/learning/approval_form.html";

	$returncontent=$common->return_file_content($db_object,$xFile);

	$returncontent=$common->direct_replace($db_object,$returncontent,$array);

	echo $returncontent;


}

}
$obj=new solution();



if ($submit1)
{

$action="submit";
}
else if($save)
{
$action="save";
}
else if($back)
{
$action="back";
}
else if($back1)
{
$action="";
}
else if($show)
{
$action="show";
}

switch($action)
{
case null:

$obj->front_panel($db_object,$common,$default,$user_id);
break;

case "insert":
$obj->select_skills($db_object,$common,$default,$user_id,$error_msg);

break;

case "show":

$obj->show_form($db_object,$common,$default,$_POST,$user_id,$error_msg);

break;

case "solution":

$obj->show_form_direct($db_object,$common,$default,$user_id,$error_msg,$skill);

break;

case "back":

$obj->select_skills($db_object,$common,$default,$user_id,$error_msg);

break;

case "save":

$obj->save_finish_later($db_object,$common,$default,$_POST,$user_id,$error_msg);
$obj->show_form($db_object,$common,$default,$_POST,$user_id,$error_msg);	
break;

case "submit":

	$obj->submit_forapproval($db_object,$common,$default,$user_id,$_POST,$error_msg);

if($user_id=="1")
{
	echo $error_msg['cDevsolApproved'];
	exit;
}
$obj->approval_form($db_object,$common,$_POST);
$obj->select_skills($db_object,$common,$default,$user_id,$error_msg);

break;

}
include ("footer.php");
?>
