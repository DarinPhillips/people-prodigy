<?
include_once("../session.php");
include_once("header.php");

class view
	{
	function viewlang($common,$db_object,$user_id,$default,$error_msg)
		{

			while(list($key,$value)=each($error_msg))
			{
				$$key = $value;
					
			}

			$file=file("../templates/core/view_lang.html");
			$out = join("",$file);
			$did = $default;
			$out = preg_replace("/{{(.*?)}}/e","$$1",$out);


			$lang = $common->prefix_table("language");
			
			$dec = "desc $lang";
			$dres = $db_object->get_single_column($dec);
			
			$sp = implode(",",$dres);
			
			$qry = "select $sp from $lang";
			
			$res = $db_object->get_rsltset($qry);
			$pattern="/<{record_loopstart(.*?)<{record_loopend}>/s";
			preg_match($pattern,$out,$arr);
			$match=$arr[0];
			$str="";
			

			$mysql="select $sp from $lang where lang_id='$default'";
			
			$lang_rslt=$db_object->get_a_line($mysql);
			$l = "lang_".$default;
			for($i=0;$i<count($res);$i++)
			{
				
				$lang_id = $res[$i]['lang_id'];
				$lvar="lang_".$lang_id;
				//$language = $lang_rslt["$lvar"];
				$language = $res[$i][$l];
				
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
				
			}
			
			$out=preg_replace($pattern,$str,$out);
			echo ($out);

		}//End view
	}//End class

	$ob = new view;
	
	$ob->viewlang($common,$db_object,$user_id,$default,$error_msg);

include_once("footer.php");
?>
