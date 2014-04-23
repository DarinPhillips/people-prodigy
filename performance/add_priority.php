<?
include_once("../session.php");
include_once("header.php");
class addpriority
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$path = $common->path;
		$filename = $path."templates/performance/add_priority.html";
		$file = $common->return_file_content($db_object,$filename);
		$language = $common->prefix_table("language");
		
		$desc = "desc $language";
		$dres = $db_object->get_single_column($desc);
		
		$sp = implode(",",$dres);
		
		$qry = "select $sp from $language where lang_id='$default'";
		$res1 = $db_object->get_a_line($qry);
		
		$qry = "select lang_id,lang_$default from $language";
		$res = $db_object->get_rsltset($qry);	
		
		$lng = "lang_".$default;			
		$pattern = "/<{priority_loopstart}>(.*?)<{priority_loopend}>/s";	
		preg_match($pattern,$file,$arr);
		$match=$arr[0];
		$str="";													
		for($i=0;$i<count($res);$i++)
		{
			$lid = $res[$i][0];							
			$lvar="lang_".$lid;
			$langname = $res[$i][$lng];
			$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
		}	

		$file=preg_replace($pattern,$str,$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
	function save_priority($db_object,$common,$default,$user_id,$post_var,$max,$min,$err)
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
			
			
		$priority = $common->prefix_table("priority");
		$lang_table = $common->prefix_table("language");
		$lqry = "select lang_id from $lang_table";
		$lres = $db_object->get_single_column($lqry);
		
		$qry1= "select p_id from $priority ";
		$res1 = $db_object->get_rsltset($qry1);
		$cnt = count($res1);
		
		if($cnt<$max)
		{	
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
			$qry = "insert into $priority set $con ,pval='$fPval'";
			$db_object->insert($qry);
		}
		else
		{
			echo $err['cSorrypriorityadd']." ".$max;
		}
	}//end save
}//end class
	$ob = new addpriority;
	if($add!="")
	{
		$ob->save_priority($db_object,$common,$default,$user_id,$post_var,$gbl_max_priority,$gbl_min_priority,$error_msg);
	}
	$ob->view_form($db_object,$common,$default,$user_id);
		
include_once("footer.php");
?>

