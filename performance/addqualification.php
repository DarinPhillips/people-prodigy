<?
include_once("../session.php");
include_once("header.php");
class addqualification
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$path = $common->path;
		$filename = $path."templates/performance/addqualification.html";
		$file = $common->return_file_content($db_object,$filename);
		$language = $common->prefix_table("language");
		
		$desc = "desc $language";
		$dres = $db_object->get_single_column($desc);
		
		$sp = implode(",",$dres);
		
		$qry = "select $sp from $language where lang_id='$default'";
		$res1 = $db_object->get_a_line($qry);
		
		$qry = "select lang_id,lang_$default from $language";
		$res = $db_object->get_rsltset($qry);	
		
					
		$pattern = "/<{qualification_loopstart}>(.*?)<{qualification_loopend}>/s";	
		preg_match($pattern,$file,$arr);
		$match=$arr[0];
		$str="";
		$lng = "lang_".$default;													
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
	function save_qualification($db_object,$common,$default,$user_id,$post_var)
	{
		$qualify_array = array();
		$lang_array = array();
		while(list($key,$value)=each($post_var))
		{
			$$key = $value;
					
			if(ereg("^fQualification",$key))
			{
				if($value!="")
				{					
					list($name,$id)=split("_",$key);
					$qualify_array[$id] = $value;
				}
			}		
		}
			
			
		$qualify = $common->prefix_table("qualification");
		$lang_table = $common->prefix_table("language");
		$lqry = "select lang_id from $lang_table";
		$lres = $db_object->get_single_column($lqry);
		
		for($i=0;$i<count($qualify_array);$i++)
		{
			$con="";
			for($j=0;$j<count($lres);$j++)
			{
				$lang = $lres[$j];
				$con.="qualification_".$lang."="."'".$qualify_array[$lang]."',";
			}			
		}
		$con = substr($con,0,-1);
		$qry = "insert into $qualify set $con";
		$db_object->insert($qry);
	}//end save
}//end class
	$ob = new addqualification;
	if($save!="")
	{
		$ob->save_qualification($db_object,$common,$default,$user_id,$post_var);
		echo $error_msg['cQsuccess'];
	}
	$ob->view_form($db_object,$common,$default,$user_id);
include_once("footer.php");
?>
