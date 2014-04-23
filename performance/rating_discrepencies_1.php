<?
include_once("../session.php");
if($fReturn!="")
{
header("Location: per_setting.php");
}
include_once("header.php");
class rating
{
	function view_form($db_object,$common,$user_id,$default,$post_var,$err,$gbl_met_value)
	{
		while(list($key,$value)=@each($post_var))
		{
			$$key = $value;
		}
//table		
		$user_table = $common->prefix_table("user_table");
		$position_table = $common->prefix_table("position");
		$approved_selected_objective = $common->prefix_table("approved_selected_objective");
		$config_table = $common->prefix_table("config");
		$rating_table = $common->prefix_table("rating");
		$approved_feedback = $common->prefix_table("approved_feedback");
		$approveduser_objective = $common->prefix_table("approveduser_objective");

		$path = $common->path;
		$filename = $path."templates/performance/rating_discrepencies_1.html";
		$filename_text  = $path."templates/performance/rating_discrepencies_1.txt";

		$file = $common->return_file_content($db_object,$filename);
		$file_text = $common->return_file_content($db_object,$filename_text);
		$open = "discrepencies/rating_discrepencies_$user_id.txt";	
		$fp=fopen("$open","w");

		$pattern = "/<{user_loopstart}>(.*?)<{user_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[0];

		preg_match($pattern,$file_text,$arr_text);
		$match_text = $arr_text[0];

		$user_array = array();
		$value_array = array();
	
		if($user_id!=1)
		{
			$selqry="select username,user_id from $user_table where 
			admin_id='$user_id'";
		}
		else
		{
			$selqry="select $user_table.username,$user_table.user_id from
				 $user_table,$position_table where $user_table.position=$position_table.pos_id
				 and ($user_table.position<>NULL or $user_table.position<>0) 
				and $user_table.user_id!=1   order by $position_table.level_no desc";	
		}
		$userset=$db_object->get_rsltset($selqry);

		
		if($fWhat=='W')
		{
			$what_tx = $err['cWhat'];
		}
		elseif($fWhat=='H')
		{
			$what_tx = $err['cHhow'];
		}

		if($fValue=='H')
		{
			$high = $err['cHhigher'];
		}
		elseif($fValue=='L')
		{
			$high = $err['cLower'];
		}		

		$conqry = "select person_affected from $config_table";
		$conres = $db_object->get_a_line($conqry);
		$noofperson = $conres['person_affected'];
		$boss = 1;	
	//Total rater is 4 (without self),noofperson(the raters we have selected) + boss (boss's rating)
		$totalperson = $noofperson + $boss;	
	//from rating
		$ratqry = "select rval from $rating_table where rval='$gbl_met_value'";
		$ratres = $db_object->get_a_line($ratqry);
		$r_val = $ratres['rval'];
	//met expectation value;
		$metexpectation = $r_val * $totalperson;
		for($i=0;$i<count($userset);$i++)
		{
			$what = 0;
			$how=0;
			$uid = $userset[$i]['user_id'];
//calculation for the percentage
			$selobj = "select sl_id,o_id,objective_$default as objective,priority,
				committed_no,percent from $approved_selected_objective 
				where user_id='$uid' and status='A' order by sl_id";
			$selres = $db_object->get_rsltset($selobj);
				
			for($j=0;$j<count($selres);$j++)
			{
				$o_id = $selres[$j]['o_id'];			
				$get = $common->get_fullfilled($db_object,$o_id,$uid,$dates);
				$boss=0;
//=======================
				$what = $what + $get[Cfulfill];
			//get all  metrics for the given o_id
				$oqry = "select met_id from $approveduser_objective where o_id='$o_id' and 
				user_id='$uid'";
				$ores = $db_object->get_a_line($oqry);
				$met_id = $ores['met_id'];
				$mqry = "select o_id from $approveduser_objective where met_id='$met_id' and 
				user_id='$uid'";
				$mres = $db_object->get_single_column($mqry);
				$aver  = count($mres);
				$oid = @implode("','",$mres);
				
				$Ratervalue = "select sum(r_id) from $approved_feedback where o_id in ('$oid')
					 and user_id='$uid' and status<>'1' and status<>'2'";
				$Resvalue = $db_object->get_single_column($Ratervalue);
				$actual = $Resvalue[0];
				$actual = @($actual/$aver);

			//calculation for met expectation value
				$expected = @($actual/$metexpectation);
				$expected = $expected * 100;						
				$how = $how +$expected;
			}//j loop
				if($how!=0)
				{
					$how = @($how/count($selres));
					$how = @sprintf("%01.2f",$how);
				}
				if($what!=0)
				{
					$what = @($what/count($selres));
					$what = @sprintf("%01.2f",$what);
				}
				if($fWhat=='H')
				{
					$performed = $how;
				}
				elseif($fWhat=='W')
				{
					$performed = $what;
				}
				
				if($fValue=='H')
				{
					
					if($performed >= $fPercent)
					{
						$user_array[] = $uid;
						$value_array[] = $performed;
					}
				}
				elseif($fValue=='L')
				{
					if($performed <= $fPercent)
					{
						$user_array[] = $uid;
						$value_array[] = $performed;
					}
					
				}				
			
		}//i loop
		$val['today'] = date("Y-m-d");
		for($k=0;$k<count($value_array);$k++)
		{
			$count = $k+1;
			$uid = $user_array[$k];	
			$bid = $common->immediate_boss($db_object,$uid);
			$bossname = $common->name_display($db_object,$bid);
			$username = $common->name_display($db_object,$uid);
			$performed = $value_array[$k];
			$str.= preg_replace("/<{(.*?)}>/e","$$1",$match);
			$str_text .= preg_replace("/<{(.*?)}>/e","$$1",$match_text);
		}//k loop

		
			$file = preg_replace($pattern,$str,$file);
			$file_text = preg_replace($pattern,$str_text,$file_text);
			if($str=='')
			{
				$file = $err[cEmptyrecords];
			}
		$file = $common->direct_replace($db_object,$file,$val);
		$file_text = $common->direct_replace($db_object,$file_text,$val);
		echo $file;
		fwrite($fp,$file_text); 
		fclose ($fp);
	}//end view
}//end class
	$ob = new rating;
	$ob->view_form($db_object,$common,$user_id,$default,$post_var,$error_msg,$gbl_met_value);

include_once("footer.php");
?>
