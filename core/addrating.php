<?
include_once("../session.php");
include_once("header.php");
class addrating
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$path = $common->path;
		$filename = $path."templates/core/addrating.html";
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
		$pattern = "/<{rating_loopstart}>(.*?)<{rating_loopend}>/s";	
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
	function save_rating($db_object,$common,$default,$user_id,$post_var,$max,$min,$err)
	{
		$rating_array = array();
		$lang_array = array();
		while(list($key,$value)=each($post_var))
		{
			$$key = $value;
					
			if(ereg("^fRating",$key))
			{
				if($value!="")
				{					
					list($name,$id)=split("_",$key);
					$rating_array[$id] = $value;
				}
			}		
		}
			
			
		$rating = $common->prefix_table("rating");
		$lang_table = $common->prefix_table("language");
		$lqry = "select lang_id from $lang_table";
		$lres = $db_object->get_single_column($lqry);
		
		$qry1= "select r_id from $rating ";
		$res1 = $db_object->get_rsltset($qry1);
		$cnt = count($res1);
		
		if($cnt<$max)
		{	
			for($i=0;$i<count($rating_array);$i++)
			{
				$con="";
				for($j=0;$j<count($lres);$j++)
				{
					$lang = $lres[$j];
					$con.="rating_".$lang."="."'".$rating_array[$lang]."',";
				}			
			}
			$con = substr($con,0,-1);
			$qry = "insert into $rating set $con,rval='$fRatingval'";
			$db_object->insert($qry);
		}
		else
		{
			echo $err['cSorrycontadd']." ".$max;
		}
	}//end save
}//end class
	$ob = new addrating;
	if($add!="")
	{
		$ob->save_rating($db_object,$common,$default,$user_id,$post_var,$gbl_max_rating,$gbl_min_rating,$error_msg);
	}
	$ob->view_form($db_object,$common,$default,$user_id);
		
include_once("footer.php");
?>

