<?
include_once("../session.php");
include_once("header.php");
class setting
{
	function view_form($db_object,$common,$default,$user_id)
	{
		$config_table = $common->prefix_table("config");
		$path = $common->path;
		$filename = $path."templates/performance/graph_setting.html";
		$file = $common->return_file_content($db_object,$filename);
		$sel_qry = "select commit_color,accomplish_color,admin_color,rater1_color,
				rater2_color,rater3_color,self_color,overall_color from $config_table";
		$res = $db_object->get_a_line($sel_qry);
		$val[vAccomplish] = $res['accomplish_color'];
		$val[vCommit] = $res['commit_color'];
		$val[vAdmin] = $res['admin_color'];
		$val[vRater1] = $res['rater1_color'];
		$val[vRater2] = $res['rater2_color'];
		$val[vRater3] = $res['rater3_color'];
		$val[vSelf] = $res['self_color'];
		$val[vOverall]  = $res['overall_color'];
		
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}//end view
	function save_color($db_object,$common,$default,$user_id,$post_var)
	{
		$config_table = $common->prefix_table("config");
		if($user_id=='1')
		{
			while(list($key,$value)=each($post_var))
			{
				$$key = $value;
			}
			$insert = "update $config_table set commit_color='$fCommitment',accomplish_color='$fAccomplish',
				admin_color='$fAdmin',rater1_color='$fRater1',rater2_color='$fRater2',
				rater3_color='$fRater3',self_color='$fSelf',overall_color='$fOverall' where id = '1'";
			$db_object->insert($insert);
		}//check admin
	}
}//end class
	$ob  = new setting;
	if($submit!="")
	{
	$ob->save_color($db_object,$common,$default,$user_id,$post_var);
	}
	$ob->view_form($db_object,$common,$default,$user_id);
include_once("footer.php");
?>
