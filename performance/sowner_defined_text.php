<?
include_once("../session.php");
include_once("header.php");
class text
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$path = $common->path;
		$filename = $path."templates/performance/sowner_defined_text.html";
		$file = $common->return_file_content($db_object,$filename);
	//table declaration
		$text_table=$common->prefix_table("owner_defined_text");
		$language = $common->prefix_table("language");
		
		$desc = "desc $language";
		$dres = $db_object->get_single_column($desc);
		
		$sp = implode(",",$dres);
		
		$qry = "select $sp from $language where lang_id='$default'";
		$res1 = $db_object->get_a_line($qry);

		$qry = "select lang_id,lang_$default from $language";
		$res = $db_object->get_rsltset($qry);

		$desc = "desc $text_table";
		$rs = $db_object->get_single_column($desc);
		$split = implode(",",$rs);
		
		$qry = "select $split from $text_table where context='feedback'";
		//echo $qry;
		$rres = $db_object->get_a_line($qry);	


		$pattern = "/<{text_loopstart}>(.*?)<{text_loopend}>/s";	
		preg_match($pattern,$file,$arr);
		$match=$arr[0];
		$str="";
		$lng = "lang_".$default;
		for($i=0;$i<count($res);$i++)
		{
			$lid = $res[$i][0];							
			$lvar="lang_".$lid;
			$langname = $res[$i][$lng];			
			$text = "text_".$lid;
			$ctext = $rres[$text];
			$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
		}
		$file = preg_replace($pattern,$str,$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
	
	function save_feedback($db_object,$common,$default,$user_id,$post_var)
	{
		$feedback_array = array();
		$lang_array = array();
		while(list($key,$value)=each($post_var))
		{
			$$key = $value;
					
			if(ereg("^fFeedbackrequest",$key))
			{
				if($value!="")
				{					
					list($name,$id)=split("_",$key);
					$feedback_array[$id] = $value;
				}
			}		
		}
		$text_table=$common->prefix_table("owner_defined_text");
		$lang_table = $common->prefix_table("language");
		$lqry = "select lang_id from $lang_table";
		$lres = $db_object->get_single_column($lqry);

		$qry = "select on_id,text_$default as text from $text_table where context='feedback'";
		$res = $db_object->get_a_line($qry);
		$text = $res['on_id'];
		for($i=0;$i<count($feedback_array);$i++)
		{
			$con="";
			for($j=0;$j<count($lres);$j++)
			{
				$lang = $lres[$j];
				$con.="text_".$lang."="."'".$feedback_array[$lang]."',";
			}			
		}
			$con = substr($con,0,-1);
			
		if($text=="")
		{
			$insqry = "insert into $text_table set $con,context='feedback'";
			$db_object->insert($insqry);
		}
		else
		{
			$upqry = "update $text_table set $con where context='feedback'";
			$db_object->insert($upqry);
		}
				
	}//end save	
}//end class
	$ob  = new text;
	if($submit!="")
	{
		$ob->save_feedback($db_object,$common,$default,$user_id,$post_var);
	}
	$ob->view_form($db_object,$common,$default,$user_id);
include_once("footer.php");
?>
