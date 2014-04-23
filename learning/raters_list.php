<?
include_once("../session.php");
include_once("header.php");
class raters
{
	function view_form($db_object,$common,$user_id,$default,$err,$learning)
	{
		$path = $common->path;
		$filename = $path."templates/learning/raters_list.html";
		$learning_feedback = $common->prefix_table("learning_feedback_results");
		$approved_devbuilder = $common->prefix_table("approved_devbuilder");
		$user_table = $common->prefix_table("user_table");
		$position_table = $common->prefix_table("position");

		$file  = $common->return_file_content($db_object,$filename);
		
		if($user_id!=1)
		{
			$selqry="select user_id from $user_table where admin_id='$user_id' order by user_id";
		}
		else
		{
			$selqry="select $user_table.user_id from $user_table,$position_table where $user_table.position=$position_table.pos_id and ($user_table.position<>NULL or $user_table.position<>0) and $user_table.user_id!=1   order by $user_table.user_id";//$position_table.level_no desc			
		}
		$userres = $db_object->get_single_column($selqry);
		$split = @implode("','",$userres);
		
		$dateclause = "and $learning_feedback.status <> '1'  ";
	
		$joinclause="left join 	$learning_feedback on $approved_devbuilder.user_id=$learning_feedback.rated_id";



	$mysql="select  user_id,$approved_devbuilder.skill_id,cdate  from $approved_devbuilder $joinclause 
			$dateclause where pstatus='a' and user_id in ('$split') and (to_days(date_format(now(),'%Y-%m-%d')) - to_days(cdate)) > 7
			group by $approved_devbuilder.user_id,$approved_devbuilder.skill_id  order by cdate";
	$ar_self=$db_object->get_rsltset($mysql);


		$pattern = "/<{record_loopstart}>(.*?)<{record_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[1];

		$str="";
	for($i=0;$i<count($ar_self);$i++)
			{

				$user_id=$ar_self[$i]["user_id"];
		
				$skill_id=$ar_self[$i]["skill_id"];

				$mysql="select status from $learning_feedback
					where rater_id in ('$split') and skill_id='$skill_id' and 
					rated_id='$user_id'";

				$dFeed=$db_object->get_a_line($mysql);
				if($dFeed['0']==''  || $dFeed['status'] != '1')
				{
					
					$rated_array["self"][$user_id]=1;
					$uid = $ar_self[$i]['user_id'];		
					$u_name = $common->name_display($db_object,$uid);
					$date = $learning->date_display($ar_self[$i]['cdate']);

					$str .= preg_replace("/<{(.*?)}>/e","$$1",$match);
				}
			
			}
			if($str=="")			
			{

				$file = preg_replace("/<{ifnoloop_start}>(.*?)<{ifnoloop_end}>/s","$err[cEmptyrecords]",$file);
			}
		$file = preg_replace($pattern,$str,$file);
		$file = preg_replace("/<{(.*?)}>/","",$file);
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
	}
}
	$ob= new raters;
	$ob->view_form($db_object,$common,$user_id,$default,$error_msg,$learning);
include_once("footer.php");
?>
