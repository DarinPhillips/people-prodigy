<?
include_once("../session.php");
include_once("header.php");
class priority
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$path = $common->path;
		$filename = $path."templates/performance/priority.html";
		$file = $common->return_file_content($db_object,$filename);
		$priority_table = $common->prefix_table("priority");
		$qry = "select p_id,priority_$default as prior,pval from $priority_table order by p_id";
		$res = $db_object->get_rsltset($qry);
		$pattern = "/<{record_loopstart}>(.*?)<{record_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match=$arr[0];
		$str="";			
		for($i=0;$i<count($res);$i++)
		{
			$pid = $res[$i]['p_id'];
			$priority = $res[$i]['prior'];
			$pval = $res[$i]['pval'];			
			$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
		}			
		$file=preg_replace($pattern,$str,$file);		
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
	function delete($db_object,$common,$default,$user_id,$pid,$min,$err)
	{
		$priority_table = $common->prefix_table("priority");
		$qry1 = "select count(p_id) from $priority_table ";
		$res = $db_object->get_single_column($qry1);
		$chk = $res[0];
		if($chk > $min)
		{
			$qry = "delete from $priority_table where p_id='$pid'";
			$db_object->insert($qry);
		}
		else
		{
			echo $err['cSorryminpriority']." ".$min;
		}
		
	}//end delete
}//end class
	$ob = new priority;
	if($pid!="")
	{
		$ob->delete($db_object,$common,$default,$user_id,$pid,$gbl_min_priority,$error_msg);
	}
	$ob->view_form($db_object,$common,$default,$user_id);
	
include_once("footer.php");
?>
