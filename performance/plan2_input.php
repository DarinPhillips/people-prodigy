<?
include_once("../session.php");
if($back!="")
{
	header("location: plan1_input.php");
}
if($approval=="")
{
include_once("header.php");
}
class plan2
	{

		function view($db_object,$common,$user_id,$default)
		{

			$setting = $common->prefix_table("performance_setting");
			$qry = "select category,metrics from $setting";
			$res = $db_object->get_a_line($qry);
			$cat  = $res['category'];
			$met = $res['metrics'];			
			$path = $common->path;
			$path  = $path."templates/performance/plan2_input.html";
			$file = $common->return_file_content($db_object,$path);
			$uid["uid"]= "$user_id";
			$file = $common->direct_replace($db_object,$file,$uid);
			$top = $common->get_chain_above($user_id,$db_object,$val);
			$user = $common->get_user_id($db_object,$top);
		
			
		
		
			$tempcat = $common->prefix_table("temp_category");
			$metrics = $common->prefix_table("temp_metrics");
			$language = $common->prefix_table("language");
			
			//language
			$desc = "desc $language";
			$des = $db_object->get_single_column($desc);
			$spl = implode(",",$des);
			$lg  = "select $spl from $language";
			$rslt = $db_object->get_rsltset($lg);

	
			$lang = "select $spl from $language where lang_id='$default'";
			$lres = $db_object->get_a_line($lang);
			
			$no = "select lang_id from $language";
			$lno = $db_object->get_single_column($no);
			//----------
			$desc = "desc $tempcat";
			$des = $db_object->get_single_column($desc);
			$sp = implode(",",$des);		

			
			$qry = "select $sp from $tempcat where user_id='$user_id' order by cat_id";

			$tcat = $db_object->get_rsltset($qry);
			
			$pattern="/<{category_loopstart}>(.*?)<{category_loopend}>/s";
			preg_match($pattern,$file,$arr);
			$match=$arr[0];
			$str="";
			$count = 0;
			
			$pattern1="/<{metrics_loopstart}>(.*?)<{metrics_loopend}>/s";
			preg_match($pattern1,$match,$arr1);
			$inc = 0;
			
			$ct = count($lno);
			$dy=0;
			$c= 0;
			$lg = "lang_".$default;

			

			for($l=0;$l<count($lno);$l++)
			{
				$max = "select max(met_id) from $metrics";
			$res = $db_object->get_single_column($max);
			$maxid = $res[0];


			$maxc = "select max(cat_id) from $tempcat";
			$res = $db_object->get_single_column($maxc);
			$maxcid = $res[0];

				$count=0;
				$lang_id = $rslt[$l]['lang_id'];

				$lvar="lang_".$lang_id;
				//$language = $lres["$lvar"];
				$language = $rslt[$l][$lg];
			for($i=0;$i<$cat;$i++)
			{
				$match1=$arr1[0];
				$new = "";
				$str1 = "";
				$ct = 0;
				$count =  $count + 1;

				
					$c = $count;
				if($tcat[$i][cat_id]!="")
				{
					$cc = $tcat[$i][cat_id];
					$qry = "select metrics_$lang_id,cat_id,met_id from $metrics where user_id='$user_id' and cat_id='$cc' order by met_id limit 0,$met";			
					$tmet = $db_object->get_rsltset($qry);

				}
				else	
				{$cc="";}	

				
				//echo "$qry<br>";

			
				
				for($j=0;$j<$met;$j++)
				{
					$ct = $j+1;
					$m = $tmet[$j][cat_id];
					//echo "c=$c : m=$m<br>";
					if($cc==$m)
					{
						$mval = $tmet[$j][0];
					}
					else
						{
							$mval="";
						}
					
						
					
					
					
						$me = $j+1;
					
					$str1.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match1);
					$inc = $inc  + 1;
					$mnew="";
					
				}
				$temp	= preg_replace($pattern1,$str1,$match);
				$catg = "category_"."$lang_id";
				$cval = $tcat[$i][$catg];
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$temp);
				
			}

		}	
			
			
			$file=preg_replace($pattern,$str,$file);
			
			echo $file;
		}//end view

		function save($db_object,$common,$_POST,$default,$user_id,$error_msg)
		{
			
			$catg = array();
			$index = array();
			$tres = array();
			$lres=array();
			$usertable = $common->prefix_table("user_table");
			$userqry = "select admin_id,username from $usertable where user_id ='$user_id'";
			$ures = $db_object->get_a_line($userqry);
			$username = $ures['username'];			
			//$boss_id = $ures['admin_id'];
			$boss_id = 1;
											
			$tempcat = $common->prefix_table("unapproved_category");
			$tsel = "select cat_id from $tempcat where user_id='$user_id' order by cat_id";
			$tres = $db_object->get_single_column($tsel);
			
			$lang = $common->prefix_table("language");
			$sel = "select lang_id from $lang";
			$lres = $db_object->get_single_column($sel);

			$metrics = $common->prefix_table("unapproved_metrics");
			$qry = "select met_id from $metrics where user_id='$user_id' order by met_id";
			$mres = $db_object->get_single_column($qry);

			$in = 1;			
			$setting = $common->prefix_table("performance_setting");
			$qry = "select category,metrics from $setting";
			$res = $db_object->get_a_line($qry);
			$cat  = $res['category'];
			$met = $res['metrics'];
			$id=0;
			
			
			while(list($key,$value)=each($_POST))
			{
				
				$$key = $value;
				$index[$key] = $value;
				if(ereg("^fCategory_",$key))
				{
					list($not,$lang_id,$cat_id)=split("_",$key);
					
					//echo "lang = $lang_id<br>";
					//echo "cat = $cat_id<br>";
					//echo "val = $value<br>";
					$category_array[$lang_id][$cat_id]=$value;
				
		
				}
				
				
					if(ereg("^fMetrics_",$key))
					{
						list($not,$lang_id,$cat_id,$met_id)=split("_",$key);
						$met_array[$lang_id][$cat_id][$met_id]=$value;
						//echo "lang = $lang_id<br>";
						//echo "cat = $cat_id<br>";
						//echo "met = $met_id<br>";
						//echo "val = $value<br><br>";
						
					}
					
				
				
			}
		
//insert category & metrics

for($i=0;$i<count($lres);$i++)
{
$lid = $lres[$i];	
$lang_cat = $category_array["$lid"];
}



$delmet = "delete from $tempcat where user_id='$user_id'";
$db_object->insert($delmet);
$delcat = "delete from $metrics where user_id ='$user_id'";
$db_object->insert($delcat);

	for($i=0;$i<count($lang_cat);$i++)
		{
						
			for($j=0;$j<count($lres);$j++)
			{
				$lid = $lres[$j];	
				$lang_cat = $category_array["$lid"];				
				$arr_key=@array_keys($lang_cat);
				$key = $arr_key[$i];
				if($lang_cat[$key]!="")
				{
				$inscon.="category_".$lres[$j]."="."'".$lang_cat[$key]."',";
				}
				
			}
			$inscon = substr($inscon,0,-1);
			if($inscon!="")
			{
			$qry = "insert into $tempcat set user_id='$user_id',$inscon,status='NP',boss_id='$boss_id',submitted_date=now()";
			$catmid=$db_object->insert_data_id($qry);
		//get category id
			$present_id[] = $catmid;
			//echo "catmid=$catmid<br><br>";
			}
			//echo "insert qrycat = <br>$qry<br>";
			

			if(count($tres)<1)
			{
			$tres=array();
			}

			
			for($l=0;$l<$met;$l++)
			{
				for($k=0;$k<count($lres);$k++)
				{
					$ilid = $lres[$k];		
					$lang_met = $met_array[$ilid];
					$ar_key = @array_keys($lang_met);
					$ar_key = @array_diff($ar_key,$tres);
					$ar_key = @array_values($ar_key);
					$mkey = $ar_key[$i];
					$metid = $met_array[$ilid][$mkey];
					
					//echo "lid = $ilid<br>";
					//echo "catid = $mkey<br>";
					$pl = $l+1;
					//echo "met = $pl<br>";
					if($met_array[$ilid][$mkey][$l+1]!="")
					{
						$insmet .="metrics_".$lres[$k]."="."'".$met_array[$ilid][$mkey][$l+1]."',";
					}
				}
			
			if($inscon!="")
			{
				if($insmet!="")
				{
				$insmet = substr($insmet,0,-1);									
				$qry1 = "insert into $metrics set user_id='$user_id',cat_id='$catmid',$insmet";
			
			//get metrics id
				$presentmet_id[]=$db_object->insert_data_id($qry1);
				}
			}					
			//echo "insert qrymet = $qry1<br>";
			$insmet="";
			}
			$inscon="";	
		}
			
			
			//Mail to admin---------------------------------------------------
			$user = $common->prefix_table("user_table");
			$message = $common->prefix_table("performance_message");

			$qry = "select email from $user where user_id='$boss_id'";
			$res = $db_object->get_a_line($qry);

			$qry1 = "select first_name,last_name,email from $user where user_id='$user_id'";
			$rs = $db_object->get_a_line($qry1);
			$fname = $rs[0];
			$lname = $rs[1];

			$mqry = "select appsub_subject_$default as sub,appsub_message_$default as mess
				from $message";
			$mres = $db_object->get_a_line($mqry);
			$adqry = "select username,password from $user where user_id='1'";
			$adres = $db_object->get_a_line($adqry);
			$adminusername = $adres['username'];
			$password = $adres['password'];
 
			$to = $res[0];
			$from = $rs[2];
			$path = $common->http_path;
			$path=$path."/performance/performance_alert.php";
			$message = $mres['mess'];
			$message = preg_replace("/{{(.*?)}}/e","$$1",$message);
			$subject = $mres['sub'];						
			
			$common->send_mail($to,$subject,$message,$from);			
			//alert
												
		}//end save

		function adminsave($db_object,$common,$_POST,$default,$user_id,$error_msg)
		{

			$catg = array();
			$index = array();
			$tres = array();
			$lres=array();
			
			$category = $common->prefix_table("unapproved_category");
			$tempcat = $common->prefix_table("approved_category");
			$tsel = "select cat_id from $tempcat where user_id='$user_id' order by cat_id";
			$tres = $db_object->get_single_column($tsel);
			

			$lang = $common->prefix_table("language");
			$sel = "select lang_id from $lang";
			$lres = $db_object->get_single_column($sel);


			$metrics = $common->prefix_table("approved_metrics");
			$qry = "select met_id from $metrics where user_id='$user_id' order by met_id";
			$mres = $db_object->get_single_column($qry);


			$in = 1;			
			$setting = $common->prefix_table("performance_setting");
			$qry = "select category,metrics from $setting";
			$res = $db_object->get_a_line($qry);
			$cat  = $res['category'];
			$met = $res['metrics'];
			$id=0;
			
			
			while(list($key,$value)=each($_POST))
			{
				
				$$key = $value;
				$index[$key] = $value;
				if(ereg("^fCategory_",$key))
				{
					list($not,$lang_id,$cat_id)=split("_",$key);
					
					//echo "lang = $lang_id<br>";
					//echo "cat = $cat_id<br>";
					//echo "val = $value<br>";
					$category_array[$lang_id][$cat_id]=$value;
				
		
				}
				
				
					if(ereg("^fMetrics_",$key))
					{
						list($not,$lang_id,$cat_id,$met_id)=split("_",$key);
						$met_array[$lang_id][$cat_id][$met_id]=$value;
						//echo "lang = $lang_id<br>";
						//echo "cat = $cat_id<br>";
						//echo "met = $met_id<br>";
						//echo "val = $value<br><br>";
						
					}
					
				
				
			}
		
//insert category & metrics

for($i=0;$i<count($lres);$i++)
{
$lid = $lres[$i];	
$lang_cat = $category_array["$lid"];
}



$delmet = "delete from $tempcat where user_id='$user_id'";
$db_object->insert($delmet);
$delcat = "delete from $metrics where user_id ='$user_id'";
$db_object->insert($delcat);

	for($i=0;$i<count($lang_cat);$i++)
		{
						
			for($j=0;$j<count($lres);$j++)
			{
				$lid = $lres[$j];	
				$lang_cat = $category_array["$lid"];				
				$arr_key=@array_keys($lang_cat);
				$key = $arr_key[$i];
				if($lang_cat[$key]!="")
				{
				$inscon.="category_".$lres[$j]."="."'".$lang_cat[$key]."',";
				}
				
			}
			$inscon = substr($inscon,0,-1);
			if($inscon!="")
			{
			$qry = "insert into $tempcat set user_id='$user_id',$inscon,approved_date=now()";
			$catmid=$db_object->insert_data_id($qry);
		//get category id
			$present_id[] = $catmid;
			//echo "catmid=$catmid<br><br>";
			}
			//echo "insert qrycat = <br>$qry<br>";
			

			if(count($tres)<1)
			{
			$tres=array();
			}

			
			for($l=0;$l<$met;$l++)
			{
				for($k=0;$k<count($lres);$k++)
				{
					$ilid = $lres[$k];		
					$lang_met = $met_array[$ilid];
					$ar_key = @array_keys($lang_met);
					$ar_key = @array_diff($ar_key,$tres);
					$ar_key = @array_values($ar_key);
					$mkey = $ar_key[$i];
					$metid = $met_array[$ilid][$mkey];
					
					//echo "lid = $ilid<br>";
					//echo "catid = $mkey<br>";
					$pl = $l+1;
					//echo "met = $pl<br>";
					if($met_array[$ilid][$mkey][$l+1]!="")
					{
						$insmet .="metrics_".$lres[$k]."="."'".$met_array[$ilid][$mkey][$l+1]."',";
					}
				}
			
			if($inscon!="")
			{
				if($insmet!="")
				{
				$insmet = substr($insmet,0,-1);									
				$qry1 = "insert into $metrics set user_id='$user_id',cat_id='$catmid',$insmet";
			
			//get metrics id
				$presentmet_id[]=$db_object->insert_data_id($qry1);
				}
			}					
			//echo "insert qrymet = $qry1<br>";
			$insmet="";
			}
			$inscon="";	
		}

		if($user_id!='1')
		{		
		$qry = "update $category set status='AP' where user_id='$user_id'";
		$db_object->insert($qry);
		}	
			
		}//end adminsave
	}//end class
	$ob = new plan2;
	if(($approval!="")&&($user_id!='1'))
	{
		$ob->save($db_object,$common,$_POST,$default,$user_id,$error_msg);
		header("Location: per_setting.php");
		
	}

	if(($approval!="")&&($user_id=='1'))
	{
		$ob->adminsave($db_object,$common,$_POST,$default,$user_id,$error_msg);
		header("Location: per_setting.php");
	}
		
	$ob->view($db_object,$common,$user_id,$default);


include_once("footer.php");
?>
