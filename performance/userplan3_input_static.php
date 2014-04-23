<?
include_once("../session.php");
if(($back==""))
{
include_once("header.php");
}
class plan3
	{
	function view($db_object,$common,$default,$user_id,$err)
		{
			$path = $common->path;
			$filename = $path."templates/performance/userplan3_input_static.html";
			$file = $common->return_file_content($db_object,$filename);
		//table declaration
			$user_table = $common->prefix_table("user_table");
			$selected_table = $common->prefix_table("temp_selected_objective");
			$affected_table = $common->prefix_table("temp_affected");
			$qualify = $common->prefix_table("qualification");
			$help_table = $common->prefix_table("temp_help");
			$selected_qualification = $common->prefix_table("temp_selected_qualification");
			$priority_table = $common->prefix_table("priority");
			$xSetTable	= $common->prefix_table("config");
		//user_table
			$qry = "select username from $user_table where user_id='$user_id'";
			$res = $db_object->get_a_line($qry);
			$val['uid'] = $user_id;
			$val['fName'] = $res['username'];
		//selected_table
			$selqry = "select sl_id,objective_$default as objective ,how_to_get_$default as how,committed_no,priority
				from $selected_table where user_id='$user_id' order by sl_id";
			$selres = $db_object->get_rsltset($selqry);
		//qualify table
			$qqry = "select q_id,qualification_$default as qualification from $qualify";
			$qres = $db_object->get_rsltset($qqry);
				
		//selected_qualification
			$qid_array = array();
			$id_array = array();
			$ssqry = "select sl_qid,sl_id,q_id from $selected_qualification where user_id='$user_id'";
			$ssres = $db_object->get_rsltset($ssqry);

			for($i=0;$i<count($ssres);$i++)
			{
				$slid = $ssres[$i]['sl_id'];
				$qid_array[$slid][] = $ssres[$i]['q_id'];
				$id = $ssres[$i]['q_id'];
				$id_array[$slid][$id] = $ssres[$i]['sl_qid'];				
			}
		
		//objective loop
			$pattern = "/<{objective_loopstart}>(.*?)<{objective_loopend}>/s";	
			preg_match($pattern,$file,$arr);
			$match=$arr[0];
			$str="";
		//Qualification loop

		preg_match("/<{tablerow_loopstart}>(.*?)<{tablerow_loopstop}>/s",$file,$xRowmatches);
		$xRowmatch	= $xRowmatches[0];

		preg_match("/<{tablecol_loopstart}>(.*?)<{tablecol_loopstop}>/s",$file,$xColmatches);
		$xColmatch	= $xColmatches[0];

		
		$xQ	 = "SELECT no_of_qualification FROM $xSetTable";
		$xCols	= $db_object->get_a_line($xQ);
		$xCols	= $xCols['no_of_qualification'];

		$pattern1 = "/<!--approval_start/s";
		$pattern2 = "/approval_end-->/s";
		$pattern3 = "/<!--approval_start(.*?)approval_end-->/s";
		$space="";
	//to display approval button
		$selcount = count($selres);
		if($selcount!=0)
		{
			$file = preg_replace($pattern1,$space,$file);
			$file = preg_replace($pattern2,$space,$file);
		}
		else
		{
			$val["norecords"] = $err["cEmptyrecords"];
			$file = preg_replace($pattern3,$space,$file);
		}
			
				for($i=0;$i<count($selres);$i++)
				{
					$str1="";
					$xRowstr="";
					$fObjective = $selres[$i]['objective'];
					$fNumber =$selres[$i]['committed_no'];
					$pid = $selres[$i]['priority'];

					$psel = "select priority_$default as prior from $priority_table where pval='$pid'";
					$pres = $db_object->get_a_line($psel);
					$fPriority = $pres['prior'];
					$fHow = $selres[$i]['how'];
					$slid = $selres[$i]['sl_id'];																			
					for($j=0;$j<=count($qres);$j++)
					{	
						//echo $xRowstr;						
						$dt = $j%($xCols);
						//echo"$j mod $xCols = $dt<br> ";
						$cnt = count($qres);
						$cnt = $cnt-1;
						if($j%($xCols)==0 || $j==count($qres))//-----Controls the rows
						{	
							
							
							$xTemp_rowstr = preg_replace("/<{tablecol_loopstart}>(.*?)<{tablecol_loopstop}>/s",$xColstr,$xRowmatch);
							$xRowstr.= preg_replace("/<{(.*?)}>/e","$$1",$xTemp_rowstr);
							$xTemp_rowstr="";
							$xColstr="";							
							//echo $xRowstr;
							//echo"<hr>";
						}
						$checked = "";
						$edit = "";
						$slqid="";
						$fQualification = $qres[$j]['qualification'];	
						$sid = $slid;
						$qid = $qres[$j]['q_id'];	
						$chek = $qid_array[$sid];						
						if(@in_array($qid,$chek))
						{
							//$edt = $id_array[$sid][$qid];
							//$slqid = "_".$edt;
							$checked = "checked";
							//$edit = "edit";							
						}
						if($j!=count($qres))
						{
							$xColstr.= preg_replace("/<{(.*?)}>/e","$$1",$xColmatch);
						}
					}		
					$temp = preg_replace("/<{tablerow_loopstart}>(.*?)<{tablerow_loopstop}>/s",$xRowstr,$match);
					$str .= preg_replace("/\<\{(.*?)\}\>/e","$$1",$temp);
				}	
			$file=preg_replace($pattern,$str,$file);
			if($user_id==1)
			{
				$val['approval'] = $err['capproved'];
			}
			else
			{
				$val['approval'] = $err['capproval'];
			}
			$file = $common->direct_replace($db_object,$file,$val);
			echo $file;
		}//end view
	}//end class
	$ob = new plan3;
	if($back!="")
	{
		header("location: userplan2_input_static.php?emp_id=$uid");
	}
	$ob->view($db_object,$common,$default,$uid,$error_msg);
include_once("footer.php");
?>
