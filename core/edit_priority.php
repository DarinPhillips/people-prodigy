<?
include_once("../session.php");
include_once("header.php");
class editpriority
{
	function view_form($db_object,$common,$default,$user_id,$pid)
	{
		$path = $common->path;
		$filename = $path."templates/core/edit_priority.html";
		$file = $common->return_file_content($db_object,$filename);
		$language = $common->prefix_table("language");
		$priority_table = $common->prefix_table("priority");
		
		$desc = "desc $language";
		$dres = $db_object->get_single_column($desc);
		
		$sp = implode(",",$dres);
		
		$qry = "select $sp from $language where lang_id='$default'";
		$res1 = $db_object->get_a_line($qry);
		
		$qry = "select lang_id,lang_$default from $language";
		$res = $db_object->get_rsltset($qry);	
		
		$desc = "desc $priority_table";
		$rs = $db_object->get_single_column($desc);
		$split = implode(",",$rs);
		
		$qry = "select $split from $priority_table where p_id='$pid'";
		$rres = $db_object->get_a_line($qry);
					
		$pattern = "/<{priority_loopstart}>(.*?)<{priority_loopend}>/s";	
		preg_match($pattern,$file,$arr);
		$match=$arr[0];
		$str="";
		$lng = "lang_".$default;													
		for($i=0;$i<count($res);$i++)
		{
			$lid = $res[$i][0];							
			$lvar="lang_".$lid;
			$langname = $res[$i][$lng];
			$crating = "frating_".$lid;
			$prior = "priority_".$lid;
			$cpriority = $rres[$prior];
			$pval = $rres['pval'];
			$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
		}	
		$val['pid'] = $pid;
		$file=preg_replace($pattern,$str,$file);
			
		$val['pval'] = $pval;
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
	function save_rating($db_object,$common,$default,$user_id,$post_var,$max,$min,$err)
	{
		$priority_array = array();
		$lang_array = array();
		while(list($key,$value)=each($post_var))
		{
			$$key = $value;
					
			if(ereg("^fPriority",$key))
			{
				if($value!="")
				{					
					list($name,$id)=split("_",$key);
					$priority_array[$id] = $value;
				}
			}		
		}
			
			
		$priority_table = $common->prefix_table("priority");
		$lang_table = $common->prefix_table("language");
		$lqry = "select lang_id from $lang_table";
		$lres = $db_object->get_single_column($lqry);
				
			for($i=0;$i<count($priority_array);$i++)
			{
				$con="";
				for($j=0;$j<count($lres);$j++)
				{
					$lang = $lres[$j];
					$con.="priority_".$lang."="."'".$priority_array[$lang]."',";
				}			
			}
			$con = substr($con,0,-1);
			$qry = "update $priority_table set $con,pval='$fPval' where p_id='$pid'";
			$db_object->insert($qry);

	}//end save
}//end class
	$ob = new editpriority;
	if($add!="")
	{
		$ob->save_rating($db_object,$common,$default,$user_id,$post_var,$gbl_max_priority,$gbl_min_priority,$error_msg);
	}
	$ob->view_form($db_object,$common,$default,$user_id,$pid);
		
include_once("footer.php");
?>

