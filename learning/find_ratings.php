<?
include_once("../session.php");
include_once("header.php");
class ratings
	{
		function view_form($db_object,$common,$user_id,$default,$err)
		{
		//table
			$user_table = $common->prefix_table("user_table");
			$position_table = $common->prefix_table("position");
			$performance_feedback = $common->prefix_table("performance_feedback");
			$approved_feedback = $common->prefix_table("approved_feedback");
			$approved_devbuilder = $common->prefix_table("approved_devbuilder");
			$textqsort_rating = $common->prefix_table("textqsort_rating");
			$other_raters_tech = $common->prefix_table("other_raters_tech");
			
			$path = $common->path;
			$filename = $path."templates/learning/find_ratings.html";
			$file = $common->return_file_content($db_object,$filename);
			$file = $common->is_module_purchased($db_object,$path,$file,$common->lfvar);
			$file = $common->is_module_purchased($db_object,$path,$file,$common->cavar);
			$file = $common->is_module_purchased($db_object,$path,$file,$common->pfvar);
			$qry = "select position	from $user_table where user_id='$user_id'";
			$res = $db_object->get_a_line($qry);			
			$position = $res['position'];
			$performance=0;
			$learning=0;
			$skill=0;

			$qrypos = "select boss_no from $position_table where pos_id='$position'";
			$respos = $db_object->get_a_line($qrypos);
			$adminno = $respos['boss_no'];
			
			$seluser = "select user_id from $user_table where admin_id='$adminno'";
			$selres = $db_object->get_single_column($seluser);
			if($user_id!=1)
			{
				$selqry="select username,user_id from $user_table where admin_id='$user_id' order by user_id";
			}
			else
			{
				$selqry="select $user_table.username,$user_table.user_id from $user_table,$position_table where $user_table.position=$position_table.pos_id and ($user_table.position<>NULL or $user_table.position<>0) and $user_table.user_id!=1   order by $user_table.user_id";//$position_table.level_no desc			
			}
				//$selqry = "select * from $user_table where admin_id='$user_id'";				
				$ressel = $db_object->get_rsltset($selqry);
				$pattern = "/<{user_loopstart}>(.*?)<{user_loopend}>/s";
				preg_match($pattern,$file,$arr);
				$match = $arr[0];

				for($i=0;$i<count($ressel);$i++)
				{
					$uid = $ressel[$i]['user_id'];
					$name_qry = "Select username from $user_table where user_id='$uid'";
					$name_res = $db_object->get_a_line($name_qry);
					$name = $name_res['username'];
					$username = $common->name_display($db_object,$uid);
					$performance_pur = $common->is_module_purchased_check($db_object,$path,$common->pfvar);
				//performance
					if($performance_pur==1)
					{
						$rtqry = "select count(f_id) from $performance_feedback where user_id ='$uid'
								and request_from<>'$uid'";
						$rtres = $db_object->get_single_column($rtqry);	
						$performance = $rtres[0];
					}

				//learning
					$learning_pur  = $common->is_module_purchased_check($db_object,$path,$common->lfvar);
					if($learning_pur==1)
					{
						$ln_qry = "select count(build_id) from approved_devbuilder where 
							basic_id=5 and interbasic_id=14 and title='$name'";
						$ln_res = $db_object->get_single_column($ln_qry);
						$learning = $ln_res[0];
					}									
				//skill
					$career_pur = $common->is_module_purchased_check($db_object,$path,$common->cavar);
					if($career_pur==1)
					{
						$ski_qry = "select count(rater_id) from $textqsort_rating where rater_id='$uid'";
						$ski_res = $db_object->get_single_column($ski_qry);
						$inter = $ski_res[0];

						$skt_qry = "select count(rater_id) from $other_raters_tech where rater_id='$uid'";
						$skt_res = $db_object->get_single_column($skt_qry);
						$tech = $skt_res[0];
						$skill = ($inter + $tech);
					}
				//total
					$total = ($performance+$learning+$skill);

					
					$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
				}//i loop
		
				$file = preg_replace($pattern,$str,$file);
			
				
			$file = $common->direct_replace($db_object,$file,$val);
			echo $file;
		}
	}
	$ob = new ratings;
	$ob->view_form($db_object,$common,$user_id,$default,$err);
include_once("footer.php");
?>
