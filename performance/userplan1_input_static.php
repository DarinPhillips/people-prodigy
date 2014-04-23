<?
include_once("../session.php");
if($next=="")
{	
	include_once("header.php");
}
class userplan
	{
		function view($common,$db_object,$user_id,$default,$uid)
		{
		
			$path = $common->path;
			$filename = $path."templates/performance/userplan1_input_static.html";
			$file = $common->return_file_content($db_object,$filename);
			$user_table = $common->prefix_table(user_table);
			$tempcat = $common->prefix_table("approved_category");
			$metrics = $common->prefix_table("approved_metrics");
			$objective = $common->prefix_table("tempuser_objective");
			$language = $common->prefix_table("language");
			$rejected = $common->prefix_table("rejected_category");			
			if($uid!="")
			{
				$user_id=$uid;
			}			

			$posqry = "select position from $user_table where user_id='$user_id'";
			$posres = $db_object->get_a_line($posqry);
			$pos_id = $posres['position'];

			
			$gettopboss = $common->get_chain_above($pos_id,$db_object,$arr);
//			print_r($gettopboss);
			$cgettopboss = count($gettopboss);
		
			if($cgettopboss >= 3)
			{
				$repl = $cgettopboss - 3;
				$topboss = $gettopboss[$repl];


			}
			else if($cgettopboss== 2)
			{
				$repl = $cgettopboss - 2;
				$topboss = $gettopboss[$repl];
				//$topboss = $pos_id;
			}
			else
			{
				$topboss = 1;

			}
			
			//check whether the user is admin or not
			$adqry = "select user_id from $user_table where position='$topboss'";
			$adres = $db_object->get_a_line($adqry);
			$adminid = $adres['user_id'];

			$admin = $common->is_admin($db_object,$adminid);	
				
			if($admin==1)
			{
				$setadmin=1;
			}			
			
			if(($topboss==1)&&($setadmin==1))
			{

				$boss = $user_id;
			}
		
			if($user_id==1)
			{
				$qry = "select username from $user_table where user_id='$user_id'";
				$res = $db_object->get_a_line($qry);
				$user = $res['username'];
				$boss = 1;
			}
			else
			{
				if($setadmin=='1')
				{					
					$qry = "select username,admin_id from $user_table where user_id='$user_id'";
					$res = $db_object->get_a_line($qry);
					$user = $res['username'];
					if($topboss!=1)
					{
						$boss = $adminid;					
					}
					$ckboss = $common->is_admin($db_object,$boss);
					if($ckboss==1)
					{
						$tempchk = "select count(user_id) from $tempcat where user_id='$boss'";						
						$tempres = $db_object->get_single_column($tempchk);
						if($tempres[0] == 0)
						{
							$boss = 1;
						}
					}
				}
				else
					{

						$uqry = "select username from $user_table where user_id='$user_id'";
						$ures  = $db_object->get_a_line($uqry);
						$user  = $ures['username'];
						$fboss = "select admin_id from $user_table where position='$topboss'";
						$fres = $db_object->get_a_line($fboss);
						$boss = $fres['admin_id'];
						
						$tempchk = "select count(user_id) from $tempcat where user_id='$boss'";
						$tempres = $db_object->get_single_column($tempchk);
						if($tempres[0] == 0)
						{
							$boss = 1;
						}
					}
				
			}

			$check = $common->is_admin($db_object,$boss);
			if($check==0)
			{
				$fboss = "select admin_id from $user_table where user_id='$boss'";
				$fres = $db_object->get_a_line($fboss);
				$boss = $fres['admin_id'];
				$chkboss = "select user_id from $user_table where admin_id='$boss'";
				$chkres = $db_object->get_a_line($chkboss);
				$tempchk = "select count(user_id) from $tempcat where user_id='$boss'";
				$tempres = $db_object->get_single_column($tempchk);
				if($tempres[0] == 0)
				{

					$boss = 1;
				}				
			}

			//echo "<br>boss = $boss";
			$user_array['user'] = $user;
			$user_array['uid'] = $uid;
									
			$file = $common->direct_replace($db_object,$file,$user_array);
			/////////////////////////category & Metrics
			$setting = $common->prefix_table("performance_setting");
			$qry = "select category,metrics from $setting";
			$res = $db_object->get_a_line($qry);
			$cat  = $res['category'];
			$met = $res['metrics'];			
												
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
		//get o_id and met_id from tempuser_objective

			$tqry = "select met_id from $objective where user_id='$user_id' order by o_id";

			$tres = $db_object->get_single_column($tqry);
			$ttqry = "select o_id from $objective where user_id='$user_id' order by o_id";
			$ttres = $db_object->get_single_column($ttqry);
					
			$desc = "desc $tempcat";
			$des = $db_object->get_single_column($desc);
			$sp = implode(",",$des);		
			
			$qry = "select $sp from $tempcat where user_id='$boss' order by cat_id";			
			$tcat = $db_object->get_rsltset($qry);
			//echo $qry;
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
			$mct = count($ttres);
			
			$metct=0;

			for($i=0;$i<count($tcat);$i++)
			{
			//to display save and next button
				
				$match1=$arr1[0];
				$new = "";
				$str1 = "";
				$ct = 0;
				$count =  $count + 1;
				
				$c = $count;
				if($tcat[$i][cat_id]!="")
				{
					$cc = $tcat[$i][cat_id];
					$qry = "select metrics_$default,cat_id,met_id from $metrics where user_id='$boss' and cat_id='$cc' order by met_id limit 0,$met";			
					$tmet = $db_object->get_rsltset($qry);

				}
				else	
				{$cc="";}	
				
				//echo "$qry<br>";							
				for($j=0;$j<count($tmet);$j++)
				{
					$old="";
					$oidval="";
					$ct = $j + 1;
					$m = $tmet[$j]['cat_id'];
					//echo "c=$c : m=$m<br>";
					if($cc==$m)
					{
						$mval = $tmet[$j][0];
					}
					else
						{
							$mval="";
						}																										
						
						$me = $tmet[$j]['met_id'];
						if(@in_array($me,$tres))
						{
							$checked = "checked";
							$old= "old";
							$oidval = "_".$ttres[$metct];
							$metct = $metct+1;							
						}
						
					$inc = $inc  + 1;	
					$str1.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match1);
					
					$mnew="";
					$checked="";										
				}
				$temp	= preg_replace($pattern1,$str1,$match);
				$catg = "category_"."$default";
				$cval = $tcat[$i][$catg];
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$temp);
			
			}
				$file=preg_replace($pattern,$str,$file);
			/////////////////////////
				
			$pattern1="/<{category1_loopstart}>(.*?)<{category1_loopend}>/s";
			preg_match($pattern1,$file,$arr);
			$match=$arr[0];
			$str="";
			$count = 0;
			for($i=0;$i<count($tcat);$i++)
			{
				$catcheck="";
				$count = $count + 1;
				$catg = "category_"."$default";
				$cval = $tcat[$i][$catg];
				$cid = $tcat[$i][cat_id];
				$qry = "select cat_id,category_$default as category from $rejected where user_id='$user_id' and cat_id='$cid'";
				$res = $db_object->get_a_line($qry);				
				$chk = $res['cat_id'];
				if($cid==$chk)
				{
					$catcheck = "checked";
				}
				$cat_val = $res['category'];											
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);	
			}
			
			$file=preg_replace($pattern1,$str,$file);




			echo $file;	
		}//end view		
	
		//Rejected Data
		function rejected_plan($db_object,$common,$default,$user_id,$uid,$_POST)
		{
			if($uid!="")
			{
				$user_id = $uid;
			}
			header("location: userplan2_input_static.php?emp_id=$user_id");
		}//end reject	
	}//end class
	$ob = new userplan;
	if($next!="")
	{		
		$ob->rejected_plan($db_object,$common,$default,$user_id,$uid,$_POST);		
	}
	$ob->view($common,$db_object,$user_id,$default,$uid);
	
include_once("footer.php");
?>
