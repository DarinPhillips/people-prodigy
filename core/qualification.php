<?
include_once("../session.php");
include_once("header.php");
class view
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$path = $common->path;
		$filename = $path."templates/core/qualification.html";
		$file = $common->return_file_content($db_object,$filename);
		
		$qualify = $common->prefix_table("qualification");
		$qry = "select q_id,qualification_$default as qualify from $qualify order by q_id";
		$res = $db_object->get_rsltset($qry);
		$pattern = "/<{qualification_loopstart}>(.*?)<{qualification_loopend}>/s";	
		preg_match($pattern,$file,$arr);
		$match=$arr[0];
		$str="";													
		for($i=0;$i<count($res);$i++)
		{
			$fQualification = $res[$i]['qualify'];
			$qid = $res[$i]['q_id'];
			$str.=preg_replace("/\<\{(.*?)\}\>/e","$$1",$match);
		}	

		$file=preg_replace($pattern,$str,$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
	function delete($db_object,$common,$default,$user_id,$qid)
	{
		$qualify = $common->prefix_table("qualification");
		$qry = "delete from $qualify where q_id='$qid'";
		$db_object->insert($qry);
	}//end delete
}//end class
	$ob = new view;
	if($qid!="")
	{
		$ob->delete($db_object,$common,$default,$user_id,$qid);
	}
	$ob->view_form($db_object,$common,$default,$user_id);
include_once("footer.php");
?>
