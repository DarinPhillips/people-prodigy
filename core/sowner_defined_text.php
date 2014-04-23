<?
include_once("../session.php");
include_once("header.php");
class text
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$path = $common->path;
		$filename = $path."templates/core/sowner_defined_text.html";
		$file = $common->return_file_content($db_object,$filename);
	//table declaration
		$text_table=$common->prefix_table("owner_defined_text");
		$qry = "select text_$default as text from $text_table where context='feedback'";
		$res = $db_object->get_a_line($qry);
		$val['feedback'] = $res['text'];
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
	
	function save_feedback($db_object,$common,$default,$user_id,$post_var)
	{
		while(list($key,$value)=each($post_var))
		{
			$$key=$value;
		}
		$text_table=$common->prefix_table("owner_defined_text");

		$qry = "select on_id,text_$default as text from $text_table where context='feedback'";
		$res = $db_object->get_a_line($qry);
		$text = $res['on_id'];

		if($text=="")
		{
			$insqry = "insert into $text_table set text_$default='$fFeedbackrequest',context='feedback'";
			$db_object->insert($insqry);
		}
		else
		{
			$upqry = "update $text_table set text_$default='$fFeedbackrequest' where context='feedback'";
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
