<?php
/*===============================================================
    SCRIPT: dev_solution.php
    AUTHOR: chrisranjana.com
    UPDATED: 1st of September, 2003
    
    DESCRIPTION
     This deals with the analyses Developmental Solution in the ADMIN PANEL.
===============================================================*/

include("../session.php");
include("header.php");

class approval
{
function show_form ($db_object,$common,$default,$array,$error_msg)

{


	$skills=$array["skill_id"];

	$user_id=$array["user_id"];

	$path=$common->path;

	$xFile=$path."templates/learning/approval.html";


	$returncontent=$common->return_file_content($db_object,$xFile);


	$skills_table=$common->prefix_table("skills");

	$config=$common->prefix_table("config");

	$user_table=$common->prefix_table("user_table");
	
	$dev_basic=$common->prefix_table("dev_basic");

	$position_table=$common->prefix_table("position");

	$dev_interbasic=$common->prefix_table("dev_interbasic");

	$unapproved_devbuilder=$common->prefix_table("unapproved_devbuilder");
	
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

		$str="";

		for($i=0;$i<count($arrway);$i++)

		{
			$inner=$myvar;

			$types=$arrway[$i][0];

			$interbasic_id=$arrway[$i][1];

			$ib_id=$ib_array[$j];


				if($interbasic_id==14)
				{
				$Display=$error_msg["cEmailfield"];
//echo "Display=$Display";
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

				$mysql="select title,description,url from $unapproved_devbuilder 

				where skill_id='$skills' and user_id='$user_id'and basic_id='$basic_id' 

				and interbasic_id='$interbasic_id'and key_id='$text'";

				$arr=$db_object->get_a_line($mysql);
//echo "sql=$mysql<br>";
//print_r($arr);
				$title=$arr["title"];

//echo "title=$title";
//echo "<br>";

				$desc=$arr["description"];


				$url=$arr["url"];


				if($interbasic_id=="12")
				{
				$pos="select position_name from $position_table where pos_id='$title'";

				$posarr=$db_object->get_a_line($pos);

				$title=$posarr["position_name"];
//echo $title;
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

		$outer=preg_replace("/<{(.*?)}>/e","$$1",$outer);

		$string.=$outer;
	
	}

	$returncontent=preg_replace("/<{solution_loopstart}>(.*?)<{solution_loopend}>/s",$string,$returncontent);
		
	$replacearray["skill_id"]=$skills;

	$replacearray["user_id"]=$user_id;



	$returncontent=$common->direct_replace($db_object,$returncontent,$replacearray);
		

	echo $returncontent;

}

function resubmit($db_object,$common,$default,$_POST,$error_msg)
{

	
	$skills=$_POST["skill_id"];

	$user_id=$_POST["user_id"];

	$dev_basic=$common->prefix_table("dev_basic");

	$user_table=$common->prefix_table("user_table");

	$dev_interbasic=$common->prefix_table("dev_interbasic");

	$temp_devbuilder=$common->prefix_table("temp_devbuilder");

	$position_table=$common->prefix_table("position");

	$unapproved_devbuilder=$common->prefix_table("unapproved_devbuilder");
	
	$mysql="delete from $temp_devbuilder where skill_id='$skills' and user_id='$user_id'";
	
	$db_object->insert($mysql);

	$mysql="delete from $unapproved_devbuilder where skill_id='$skills' and user_id='$user_id'";
	
	$db_object->insert($mysql);

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
	
					if($value=="$position")
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

				//$this->show_form($db_object,$common,$default,$_POST,$error_msg);
		
				//exit;	
	
				
			}
				
			}
			}

	}

//print_r( $title_array);


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

			$tid_array=array_keys($array3);

			for($k=0;$k<count($tid_array);$k++)

			{	

				$t_id=$tid_array[$k];

				$title=$title_array[$basic_id][$ib_id][$t_id];
//echo $title;
//echo "<br>";

				$desc=$desc_array[$basic_id][$ib_id][$t_id];

				$url=$url_array[$basic_id][$ib_id][$t_id];

				if($ib_id=="12")
				{
					$pos="select pos_id from $position_table where position_name='$title'";

					$posarr=$db_object->get_a_line($pos);

					$title=$posarr["pos_id"];
//echo "title=$title";
//echo "<br>";
				}

				if($ib_id=="14")
			
				{

				$email="select user_id from $user_table where email='$url'";

				$arr=$db_object->get_a_line($email);

				$url=$arr["user_id"];

				
				}

				$mysqltemp="insert into $temp_devbuilder set title='$title',

				description='$desc',url='$url',

				basic_id='$basic_id',interbasic_id='$ib_id',skill_id='$skill_id',user_id='$user_id',key_id='$t_id'";

				 $db_object->insert($mysqltemp);

				$mysql="insert into $unapproved_devbuilder set title='$title',

				description='$desc',url='$url',

				basic_id='$basic_id',interbasic_id='$ib_id',skill_id='$skill_id',user_id='$user_id',key_id='$t_id',status='u'";
				

				
				$db_object->insert($mysql);
			}
		

		}	
		
	}

}
function onapproval($db_object,$common,$default,$_POST,$error_msg)
{


	$skills=$_POST["skill_id"];

	$user_id=$_POST["user_id"];

	$dev_basic=$common->prefix_table("dev_basic");

	$position_table=$common->prefix_table("position");

	$user_table=$common->prefix_table("user_table");

	$dev_interbasic=$common->prefix_table("dev_interbasic");

	$temp_devbuilder=$common->prefix_table("temp_devbuilder");

	$unapproved_devbuilder=$common->prefix_table("unapproved_devbuilder");
	
	$approved_devbuilder=$common->prefix_table("approved_devbuilder");
	
	$mysql="delete from $temp_devbuilder where skill_id='$skills' and user_id='$user_id'";
	
	$db_object->insert($mysql);

	$mysql="delete from $unapproved_devbuilder where skill_id='$skills' and user_id='$user_id'";
	
	$db_object->insert($mysql);

	while(list($key,$value)=each($_POST))
	{
		$$key=$value;

		if(ereg("^title",$key))
			{
			list($title,$basic_id,$ib_id,$tid)=split("_",$key);

			$title_array[$basic_id][$ib_id][$tid]=$value;

			$p=0;

			if($ib_id==12 and $value!="")
			{
				$positionsql="select position_name from $position_table";

				$positionarr=$db_object->get_rsltset($positionsql);	

				for($i=0;$i<count($positionarr);$i++)
				{
					$position=$positionarr[$i]["position_name"];
	
					if($value=="$position")
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
					//exit;
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

				$this->show_form($db_object,$common,$default,$_POST,$error_msg);
		
				//exit;	
	
				
			}
		}		
		
			}

	}

//print_r( $url_array);
//print_r($title_array);
//echo "<br>";


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

			$tid_array=array_keys($array3);

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

				$title = $posarr[pos_id];
				}
				


				if($ib_id=="14")
			
				{
				$email="select user_id from $user_table where email='$url'";

				$arr=$db_object->get_a_line($email);

				$url=$arr["user_id"];
				 									
				}

			$mysqltemp="insert into $temp_devbuilder set title='$title',

				description='$desc',url='$url',

				basic_id='$basic_id',interbasic_id='$ib_id',skill_id='$skill_id',user_id='$user_id',key_id='$t_id'";

				$db_object->insert($mysqltemp);

				$mysqlunapp="insert into $unapproved_devbuilder set title='$title',

				description='$desc',url='$url',
				
				basic_id='$basic_id',interbasic_id='$ib_id',skill_id='$skill_id',user_id='$user_id',key_id='$t_id',status='a'";

				$db_object->insert($mysqlunapp);
				
				$mysqlapp="insert into $approved_devbuilder set title='$title',

				description='$desc',url='$url',

				basic_id='$basic_id',interbasic_id='$ib_id',skill_id='$skill_id',user_id='$user_id',key_id='$t_id',status='a',pstatus='u',cdate='0000-00-00'";
				
				$db_object->insert($mysqlapp);

			}
		

		}	
		
	}


}
function approval_form($db_object,$common,$_POST,$error_msg)
{	

	$skills=$common->prefix_table("skills");

	$user_table=$common->prefix_table("user_table");

	$skill_id=$_POST["skill_id"];

	$user_id=$_POST["user_id"];
	
	$mysql="select username from $user_table where user_id='$user_id'";

	$arr=$db_object->get_a_line($mysql);

	$user_name=$arr["username"];

	$mysql="select skill_name from $skills where skill_id='$skill_id'";

	$arr=$db_object->get_a_line($mysql);

	$skill_name=$arr["skill_name"];
	
	$path=$common->path;

	$xFile=$path."/templates/learning/admin_approvalform.html";

	$returncontent=$common->return_file_content($db_object,$xFile);
	
	$array["user_name"]=$user_name;

	$array["skill"]=$skill_name;

	$msg=$error_msg["cDevApproved"];

	$content=$common->direct_replace($db_object,$msg,$array);

	$replace["Approved"]=$content;
	
	$returncontent=$common->direct_replace($db_object,$returncontent,$replace);

	echo $returncontent;


}
}

$obj= new approval;

if($resubmit)
{
$action="resubmit";
}
if($submit)
{
$action="onapproval";
}

switch($action)
{
case null:

$obj->show_form($db_object,$common,$default,$_GET,$error_msg);


break;

case resubmit:

$obj->resubmit($db_object,$common,$default,$_POST,$user_id,$error_msg);
$err=$error_msg['cResubmitted'];
$string="alert('$err');";

?>
<script language="javascript">
	<?php echo $string;?>
</script>
<?php
$obj->show_form($db_object,$common,$default,$_POST,$error_msg);

break;

case onapproval:


$obj->onapproval($db_object,$common,$default,$_POST,$error_msg);
$obj->approval_form($db_object,$common,$_POST,$error_msg);
break;
}

include ("footer.php");
?>
