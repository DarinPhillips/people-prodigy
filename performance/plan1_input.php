<?
include_once("../session.php");
if(($next=="")&&($save==""))
{
include_once("header.php");
}
class plan1
	{
	
		function save($db_object,$common,$_POST,$default,$user_id)
		{
			
			$catg = array();
			$index = array();
			$tres = array();
			$lres=array();
			$tempcat = $common->prefix_table("temp_category");
			$tsel = "select cat_id from $tempcat where user_id='$user_id' order by cat_id";
			$tres = $db_object->get_single_column($tsel);
			

			$lang = $common->prefix_table("language");
			$sel = "select lang_id from $lang";
			$lres = $db_object->get_single_column($sel);


			$metrics = $common->prefix_table("temp_metrics");
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
				if(ereg("^fCategorynew_",$key))
				{
					list($not,$lang_id,$cat_id)=split("_",$key);
					//echo "lang = $lang_id<br>";
					//echo "cat = $cat_id<br>";
					//echo "val = $value<br>";
					$newcategory_array[$lang_id][$cat_id]=$value;
					
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
					if(ereg("^fMetricsnew_",$key))
					{
						list($not,$lang_id,$cat_id,$met_id)=split("_",$key);
						$newmet_array[$lang_id][$cat_id][$met_id]=$value;
						//echo "lang = $lang_id<br>";
						//echo "cat = $cat_id<br>";
						//echo "met = $met_id<br>";
						//echo "val = $value<br>";
						//ECHO "<BR>";
						
					}
				
				
			}
		




//Category & Metric update	
		$inc = 0;
		$present_id=array();
		$presentmet_id=array();
		
		for($q=0;$q<count($lres);$q++)
		{
				$lid = $lres[$q];
				$metarr = $met_array["$lid"];
				$metkey = @array_keys($metarr);
				$catarr = $category_array["$lid"];
				$catkey = @array_keys($catarr);						
		}
		
		
		for($k=0;$k<count($catkey);$k++)
			{
				
				
				
				for($p=0;$p<count($lres);$p++)
				{
					$catid = $tres[$k];
					$ld = $lres[$p];
					
						if($category_array[$ld][$catid]!="")
						{
							$con.= "category_".$lres[$p]."="."'".$category_array[$ld][$catid]."',";	
						}
				}				
				
				
				$kat_id=$metkey["$k"];
				
				$met_key=$met_array[$lid][$kat_id];
				
				
				for($i=0;$i<count($met_key);$i++)
				{	
					
						$allmetkeys = @array_keys($met_key);
						//print_r($allmetkeys);
						for($m=0;$m<count($lres);$m++)
						{													
						$mlid = $lres[$m];
						$ld = $lres[$m];
						$met_lang = $met_array["$ld"];
						$arr_key=@array_keys($met_lang);						
						$key = $arr_key[$k];
						$met_lan = $met_array["$ld"][$key];						
						$mid = $mres[$inc];	
						//echo "<br>lid =$mlid <br>";
						//echo "catid=$key <br>";		
						//echo "mid =$mid <br>";
						//echo "allkey = $allmetkeys[$i]<br>";
						//echo "value =".$met_array[$mlid][$key][$mid]."<br>";
						$m_con.="metrics_".$lres[$m]."="."'".$met_array[$mlid][$key][$allmetkeys[$i]]."',";
						
						}
	
						$m_con = substr($m_con,0,-1);				
						//echo "<br><b>mcon=$m_con metid=$allmetkeys[$i]</b><br><br>";		
						//$qry = "update $metrics set user_id='$user_id', cat_id='$catid',$m_con where met_id='$mres[$inc]'";
						if($con!="")
						{
						$qry = "update $metrics set user_id='$user_id', cat_id='$catid',$m_con where met_id='$allmetkeys[$i]'";
						$db_object->insert($qry);
					//get metrics id	
						$presentmet_id[] = $allmetkeys[$i];
						}
						//echo "update met qry = $qry<br>";	
						$inc = $inc + 1;
						$m_con = "";					
					
				}
		
				$con = substr($con,0,-1);
				if($con!="")
				{
					$qry1 = "update $tempcat  set user_id='$user_id',$con where cat_id='$tres[$k]'";				
					//echo "mysql=$qry<br>";
					$db_object->insert($qry1);
			//get category id
					$present_id[] = $tres[$k];
				}
				//echo "update caat=$qry <br><br>";
				$con="";
			}
			




//insert category & metrics

for($i=0;$i<count($lres);$i++)
{
$lid = $lres[$i];	
$lang_cat = $newcategory_array["$lid"];
}


	for($i=0;$i<count($lang_cat);$i++)
		{
						
			for($j=0;$j<count($lres);$j++)
			{
				$lid = $lres[$j];	
				$lang_cat = $newcategory_array["$lid"];
				
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
				$qry = "insert into $tempcat set user_id='$user_id',$inscon";
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
					$lang_met = $newmet_array[$ilid];
					$ar_key = @array_keys($lang_met);
					$ar_key = array_diff($ar_key,$tres);
					$ar_key = @array_values($ar_key);
					$mkey = $ar_key[$i];
					$metid = $newmet_array[$ilid][$mkey];
					
					//echo "lid = $ilid<br>";
					//echo "catid = $mkey<br>";
					$pl = $l+1;
					//echo "met = $pl<br>";
					if($newmet_array[$ilid][$mkey][$l+1]!="")
					{
						$insmet .="metrics_".$lres[$k]."="."'".$newmet_array[$ilid][$mkey][$l+1]."',";
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




			$inc = 0;


			
		
			$ct = count($get_key);
			
	//insert	

	
	
		for($i=0;$i<count($tres);$i++)
			{
				$key = $tres[$i];
				//$lid=$lres[$i];
				$lid=$default;
				//echo "cat=$key<br>";
				//echo "lang = $lid<br>";
				$arr = $newmet_array[$lid][$key];
				$get_key = @array_keys($arr);
				
				$catid = $tres[$i];
				      for($m=0;$m<count($get_key);$m++)
					{
						
						$v = $get_key[$m];
						//print_r($arr);
						//echo "<br>";
						for($j=0;$j<count($lres);$j++)
						{
							$langid = $lres[$j];	
							//echo "lan=$langid<br>";
							//echo "cat=$catid<br>";
							$arr = $newmet_array[$langid][$catid];							
							//echo "met=$v<br>";
							if($arr["$v"]!="")
							{
								$con.="metrics_".$lres[$j]."="."'".$arr["$v"]."',";
							}
						}
						$con = substr($con,0,-1);
						if($con!="")
						{
						$qry = "insert into $metrics set user_id='$user_id',cat_id='$key',$con";
					//get metrics id
						$presentmet_id[]=$db_object->insert_data_id($qry);
						}
						//echo "insert qrylast = $qry<br>";
						$con="";																													
					}			
			
			}
						
			
			$present_id = implode("','",$present_id);
			$presentmet_id = implode("','",$presentmet_id);


			$del_met = "delete from $metrics where met_id not in ('$presentmet_id') and user_id='$user_id'";
			//echo "$del_met<br>";
			$db_object->insert($del_met);
			$del_cat = "delete from $tempcat where cat_id not in ('$present_id')and user_id='$user_id'";
			//echo "$del_cat<br>";
			$db_object->insert($del_cat);
			
		}//end save



	}//end class
	$ob = new plan1;
	if($next!="")
	{
		
		$ob->save($db_object,$common,$_POST,$default,$user_id);
			header("location: plan2_input.php");
	}
	if($save!="")
	{
		$ob->save($db_object,$common,$_POST,$default,$user_id);
		header("location: per_setting.php");
		
	}
	
	
	$temp_name="plan1_input.html";
	$cat_table="temp_category";
	$met_table = "temp_metrics";
	$lang_table = "language";
	$perform_table = "performance_setting";
	$common->view_plan($db_object,$user_id,$default,$temp_name,$cat_table,$met_table,$lang_table,$perform_table);
	include_once("footer.php");
?>
