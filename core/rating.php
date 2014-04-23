<?
include_once("../session.php");
include_once("header.php");
class rating
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$path = $common->path;
		$filename = $path."templates/core/rating.html";
		$file = $common->return_file_content($db_object,$filename);
		$rating = $common->prefix_table("rating");
		$qry = "select r_id,rating_$default as rating,rval from $rating order by r_id";
		$res = $db_object->get_rsltset($qry);
		

		$pattern="/<{record_loopstart(.*?)<{record_loopend}>/s";
			preg_match($pattern,$file,$arr);
			$match=$arr[0];
			$str="";			
			for($i=0;$i<count($res);$i++)
			{
				$rating = $res[$i]['rating'];
				$rid = $res[$i]['r_id'];
				$rval = $res[$i]['rval'];
				$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
			}			
			$file=preg_replace($pattern,$str,$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
	function delete($db_object,$common,$default,$user_id,$rid,$min,$err,$gbl_met_value)
	{
		$rating = $common->prefix_table("rating");
		$qry1 = "select count(r_id) from $rating ";
		$res = $db_object->get_single_column($qry1);
		$chk = $res[0];
		$conqry = "select r_id from $rating where rval='$gbl_met_value'";
		$conres = $db_object->get_a_line($conqry);
		$crid = $conres[r_id];

	//check for default rating value
		if($crid!=$rid)
		{
			if($chk > $min)
			{
				$qry = "delete from $rating where r_id='$rid'";
				$db_object->insert($qry);
			}
			else
			{
				echo $err['cSorrymin']." ".$min;
			}
		}		
	}//end delete
}//end class
	$ob = new rating;
	if($rid!="")
	{
		$ob->delete($db_object,$common,$default,$user_id,$rid,$gbl_min_rating,$error_msg,$gbl_met_value);
	}
	$ob->view_form($db_object,$common,$default,$user_id);
	
include_once("footer.php");
?>

