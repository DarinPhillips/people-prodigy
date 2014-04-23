<?
include_once("../session.php");
include_once("header.php");
class report
{
	function view_form($db_object,$common,$user_id,$default,$post_var,$err,$learning,$gbl_date_format)
	{

		while(list($key,$value)=@each($post_var))
		{
			$$key = $value;
			//echo "$key = $value <br>";
		}
		
		$path = $common->path;
		$filename = $path."templates/learning/learning_status_report.html";
		$file = $common->return_file_content($db_object,$filename);
	
	//read report file
		$flname = $path."templates/learning/learning_status_report.txt";
		$file_text = $common->return_file_content($db_object,$flname);
		$open = "status_report/learning_status_report_$user_id.txt";	
		$fp=fopen("$open","w");


		$skill_table 		=$common->prefix_table("skills");
		$plan_table		=$common->prefix_table("approved_devbuilder");
		$feedback_table	=$common->prefix_table("learning_feedback_results");
		$user_table = $common->prefix_table("user_table");
		$solution_table	=$common->prefix_table("assign_solution_builder");
//check for direct reports
		if($dr_id!="")
		{
			$user_id = $dr_id;			
		}
		$pattern_main = "/<{main_loopstart}>(.*?)<{main_loopend}>/s";
		preg_match($pattern_main,$file,$arr);
		$match = $arr[1];
	//text
		preg_match($pattern_main,$file_text,$arr_text);
		$match_text = $arr_text[1];
	

		$pattern_dir = "/<{direct_reports_start}>(.*?)<{direct_reports_end}>/s";
		preg_match($pattern_dir,$file,$arr1);
		$match1 = $arr1[1];
	//text
		preg_match($pattern_dir,$file_text,$arr1_text);
		$match1_text = $arr1_text[1];

	//get the skills

		$mysql="select skill_name,$feedback_table.skill_id,rated_id,rater_id,skill_description from $feedback_table,
			$plan_table,$skill_table where $feedback_table.rated_id=$plan_table.user_id 
			and $feedback_table.skill_id=$plan_table.skill_id 
			and $feedback_table.skill_id=$skill_table.skill_id and $feedback_table.status='1'
			and rated_id='$user_id' and $plan_table.pstatus='a' group by $feedback_table.skill_id";
		$result= $db_object->get_rsltset($mysql);
						
	//get the direct reports
		
		$posqry1 = "select position from $user_table where user_id='$user_id'";
		$posres1 = $db_object->get_a_line($posqry1);		
		$positions = $posres1['position'];
		$dreports = $common->get_chain_below($positions,$db_object,$twodarr);		
		$ddetails = $common->get_user_id($db_object,$dreports);	
		$dir_id = array();
		for($u=0;$u<count($ddetails);$u++)
		{
			$dir_id[] = $ddetails[$u]['user_id'];
		}
		$imp = @implode("','",$dir_id);

	//direct reports detail
		for($i=0;$i<count($result);$i++)
		{
			$skillname = $result[$i][skill_name];
			$skill = $result[$i][skill_id];
			$avg = $learning->accomplished_average($db_object,$common,$user_id,$skill,$dateqry);
			$committed = 100;
			$accomplished = $avg;
			$str1 = "";
			$str1_text = "";
			$avg_array = array();
			$id_array = array();
			$mysql = "select rated_id from $feedback_table where rated_id in ('$imp') and skill_id='$skill' group by rated_id";
			$dir_rep = $db_object->get_single_column($mysql);
			for($j=0;$j<count($dir_rep);$j++)
			{
				$rid = $dir_rep[$j];
				$id_array[] = $rid;
				$ratername = $common->name_display($db_object,$rid);
				$r_avg = $learning->accomplished_average($db_object,$common,$rid,$skill,$dateqry);
				$avg_array[] = $r_avg;
				$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$match1);
				$str1_text .= preg_replace("/<{(.*?)}>/e","$$1",$match1_text);
				
				
			}//j loop
			$temp = preg_replace($pattern_dir,$str1,$match);
			$temp_text = preg_replace($pattern_dir,$str1_text,$match_text);

//from and to date

			$mysql 	 = "select max(date_format(rated_date,'%Y-%m-%d')) as rateddate,plan_approved_date from $plan_table,$feedback_table,
				$solution_table where $plan_table.user_id=$feedback_table.rated_id and 
				$plan_table.skill_id=$feedback_table.skill_id and $plan_table.user_id=$solution_table.user_id 
				and $plan_table.skill_id=$solution_table.skill_id and $plan_table.pstatus='a' and
				$plan_table.user_id in ('$imp') and $plan_table.skill_id='$skill'
				and $feedback_table.status='1' group by $plan_table.skill_id";							
			$dRated  	 =$db_object->get_a_line($mysql);
		
			$from_date = $dRated[1];
			$to_date = $dRated['rateddate'];
			$rater_avg = @implode(",",$avg_array);
			$ids = @implode(",",$id_array);
			$graph = "fGraph_".$skill;
			$ngraph = "fNGraph_".$skill;

			if($$graph=="")
			{
				$temp2 = preg_replace("/<{view_ifgraph_start}>(.*?)<{view_ifgraph_end}>/s","",$temp);								
				$temp2_text = preg_replace("/<{view_ifgraph_start}>(.*?)<{view_ifgraph_end}>/s","",$temp_text);
								
			}
			else 
			{
				$temp2 = preg_replace("/<{view_ifnotgraph_start}>(.*?)<{view_ifnotgraph_end}>/s","",$temp);
				$temp2_text = preg_replace("/<{view_ifnotgraph_start}>(.*?)<{view_ifnotgraph_end}>/s","",$temp_text);
			}
			
			$str.= preg_replace("/<{(.*?)}>/e","$$1",$temp2);			
			$str_text.= preg_replace("/<{(.*?)}>/e","$$1",$temp2_text);
		}//i loop

		$file = preg_replace($pattern_main,$str,$file);				
	//text
		$file_text = preg_replace($pattern_main,$str_text,$file_text);
		$val[username] = $common->name_display($db_object,$user_id);
		$val['dr_id'] = $dr_id;
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;

	//text
		$file_text = $common->direct_replace($db_object,$file_text,$val);
		fwrite($fp,$file_text); 
		fclose ($fp);
	}//end view
}//end class
	$ob = new report;
	$ob->view_form($db_object,$common,$user_id,$default,$post_var,$error_msg,$learning,$gbl_date_format);

include_once("footer.php");
?>
