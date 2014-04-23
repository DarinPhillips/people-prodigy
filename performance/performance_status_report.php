<?
include_once("../session.php");
include_once("header.php");
class status
{
	function view_form($db_object,$common,$user_id,$default,$err,$post_var)
	{
	//Table 
		while(list($key,$value)=@each($post_var))
			{
				$$key = $value;
			}

		$user_table = $common->prefix_table("user_table");
		$approved_selected_objective = $common->prefix_table("approved_selected_objective");
		$approveduser_objective = $common->prefix_table("approveduser_objective");
		$approved_feedback = $common->prefix_table("approved_feedback");
		$verified_user = $common->prefix_table("verified_user");

		$path = $common->path;
		$filename = $path."templates/performance/performance_status_report.html";
		$file = $common->return_file_content($db_object,$filename);

	//read report file
		$flname = $path."templates/performance/performance_status_report.txt";
		$file_text = $common->return_file_content($db_object,$flname);
		$open = "status_report/performance_status_report_$user_id.txt";	
		$fp=fopen("$open","w");

		$val['selfname'] = $common->name_display($db_object,$user_id);
		$posqry = "select position from $user_table where user_id='$user_id'";
		$posres = $db_object->get_a_line($posqry);
		$position = $posres['position'];
//Patterns
		$pattern = "/<{main_loopstart}>(.*?)<{main_loopend}>/s";
		preg_match($pattern,$file,$arr);
		$match = $arr[0];
		preg_match($pattern,$file_text,$arr_text);
		$match_text = $arr_text[0];

		
		$pattern1 = "/<{alluser_loopstart}>(.*?)<{alluser_loopend}>/s";
		preg_match($pattern1,$file,$arr1);
		$match1 = $arr1[0];
		preg_match($pattern1,$file_text,$arr1_text);
		$match1_text = $arr1_text[0];

//Get all the Users under the users chain of command

		$down = $common->get_chain_below($position,$db_object,$arr);
		$allusers = $common->get_user_id($db_object,$down);
		$alluserid = array();
		for($i=0;$i<count($allusers);$i++)
		{
			$alluserid[] = $allusers[$i][user_id];			
		}
		$alluserid[] = $user_id;
		$sl_userids = implode("','",$alluserid);					

//Get all the Objectives of the user
		$selobj = "select sl_id,o_id,objective_$default as objective,priority,
				committed_no,percent from $approved_selected_objective 
				where user_id='$user_id' and status='A' order by sl_id";
		$selres = $db_object->get_rsltset($selobj);
//get raters
		$vqry = "select sl_id,for_user_id from $verified_user where verified_user_id in('$sl_userids') group by verified_user_id ";
		$vres = $db_object->get_rsltset($vqry);

//mainloop starts here

		for($i=0;$i<count($selres);$i++)
		{
			$sno = $i+1;
			$str1="";
			$str1_text="";			
			$fulfilled ="";
			$accomplish ="";
			$committed="";
			$fulfilled1="";
			$accomplish1="";
			$committed1="";
			$mg_fulfill="";
			$mg_date="";
			$fromdate="";
			$todate="";
			$raterarray = array();
			$date = array();
			$rname_array = array();

			$sl_id = $selres[$i]['sl_id'];
			$o_id = $selres[$i]['o_id'];
			$objective = $selres[$i]['objective'];
		
		//get all  metrics for the given o_id
			$oqry = "select met_id from $approveduser_objective where o_id='$o_id' and 
				user_id='$user_id'";
			$ores = $db_object->get_a_line($oqry);
			
			$met_id = $ores['met_id'];
			$mqry = "select o_id from $approveduser_objective where met_id='$met_id' and 
				user_id='$user_id'";
			$mres = $db_object->get_single_column($mqry);
			$aver  = count($mres);
			$oid = implode("','",$mres);				
		
		//get the raters rated value
			$Ratervalue = "select sum(r_id) from $approved_feedback where o_id in ('$oid')
					 and user_id='$user_id' and status<>'1' and status<>'2'";
			$Resvalue = $db_object->get_single_column($Ratervalue);

			$actual = $Resvalue[0];
			$actual = @($actual/$aver);

		//oid - diff o_id with same metrics

		$get = $common->get_fullfilled($db_object,$o_id,$user_id,$dates);
			$fulfilled = $get['Cfulfill'];

		//All form display value
			$fulfilled = @sprintf("%01.2f",$fulfilled);
			$committed = $get['Ccommit'];
			$accomplish = $get['Caccomplish'];

		//Get all verified users				
			for($j=0;$j<count($vres);$j++)
			{

				$username = "";
				$uid="";			
				$sl = $vres[$j]['sl_id'];
				$usid = $vres[$j]['for_user_id'];
				
				$slqry = "select o_id from $approved_selected_objective where sl_id='$sl'";
				$slres = $db_object->get_a_line($slqry);
				
				$s_oid = $slres['o_id'];				
				$metqry = "select met_id from $approveduser_objective where o_id='$s_oid' and 
				user_id='$usid'";
				$metres = $db_object->get_a_line($metqry);
				$metric_id = $metres['met_id'];
				
				$mqry = "select o_id from $approveduser_objective where met_id='$met_id' and 
				user_id='$usid'";								
				$mres = $db_object->get_single_column($mqry);
				$aver  = count($mres);
				$oid1 = @implode("','",$mres);		
				
				if($metric_id==$met_id)
				{
				$username = $common->name_display($db_object,$usid);				
				$uid = $usid;		
			//oid - diff o_id with same metrics
					$rget = $common->get_fullfilled($db_object,$s_oid,$uid,$dates);

					$rfulfilled = $rget['Cfulfill'];

			//All form display value
					$rfulfilled = @sprintf("%01.2f",$rfulfilled);
					$rcommitted = $rget['Ccommit'];
					$raccomplish = $rget['Caccomplish'];

					$rdate = $rget['Call_date'];
					$raterarray[] = $rfulfilled;
					$rname_array[] = $username;
			//get date
				$dt_qry= "select sl_id,o_id,date_format(approved_date,'%Y-%m-%d') as appdate,
					percent,committed_no from $approved_selected_objective 
					where o_id in ('$oid1') and user_id='$uid' and (approved_date <>'' or approved_date <> null) order by sl_id";
				$dt_res = $db_object->get_rsltset($dt_qry);
				$alldate = array();
				$fulfill = array();

				for($k=0;$k<count($dt_res);$k++)
				{
					$k_slid = $dt_res[$k][sl_id];
					$k_oid = $dt_res[$k][o_id];
					$alldate[] = $dt_res[$k][appdate];
					$date[] = $dt_res[$k][appdate];
					$k_percent = $dt_res[$k][percent];
					$k_commit = $dt_res[$k][committed_no];						
					$Fdeliqry = "select idelivered,r_id,fd_id,user_id,accept_date from $approved_feedback where
						 user_id='$uid' and o_id='$k_oid' and status='2'";
					$Fdelires = $db_object->get_a_line($Fdeliqry);				
					$Faccomplish = $Fdelires['idelivered'];
					$fid = $Fdelires['fd_id'];
					$fulfill[]  = $common->fulfilled($db_object,$uid,$k_slid,$k_commit,$Faccomplish,$k_percent);
												
				}//k loop
				$mg_fulfill .= @join(",",$fulfill);
				$mg_fulfill  .= ":";
				$mg_date .= @join("," ,$alldate);
				$mg_date .=":";

				$str1 .= preg_replace("/<{(.*?)}>/e","$$1",$match1);
				$str1_text .= preg_replace("/<{(.*?)}>/e","$$1",$match1_text);
				}
			}//j loop

			$fromdate = @min($date);
			$todate = @max($date);			
			$mg_fulfill = substr($mg_fulfill,0,-1);
			$mg_date = substr($mg_date,0,-1);
			$avg_rater = @join(",",$raterarray);
			$rname  = @join(",",$rname_array);
		
			$pattern_ifnotgp = "/<{show_ifnotgraph_start}>(.*?)<{show_ifnotgraph_end}>/s";
			$pattern_ifgp = "/<{show_ifgraph_start}>(.*?)<{show_ifgraph_end}>/s";
			
			$graph = "fGraph_".$sl_id;
			$temp = preg_replace($pattern1,$str1,$match);
			if($$graph=='on')
			{	

				$temp = preg_replace($pattern_ifnotgp,"",$temp);	
			}
			else
			{	
				$temp = preg_replace($pattern_ifgp,"",$temp);
			}							
			$temp_text = preg_replace($pattern1,$str1_text,$match_text);						
			$str.= preg_replace("/<{(.*?)}>/e","$$1",$temp);
			$str_text.= preg_replace("/<{(.*?)}>/e","$$1",$temp_text);
		}//i loop
		
//mainloop ends here
		$val['today'] = date("d/m/Y");
		$file = preg_replace($pattern,$str,$file);							
		if($str=="")
		{
			$file = preg_replace("/\<form(.*?)\/form\>/s","<center>$err[cEmptyrecords]</center>",$file);
		}
		$file = $common->direct_replace($db_object,$file,$val);
		echo $file;
		
		$file_text = preg_replace($pattern,$str_text,$file_text);		
		$file_text = $common->direct_replace($db_object,$file_text,$val);
		fwrite($fp,$file_text); 
		fclose ($fp);
	}//end view
}//end class
	$ob = new status;
	$ob->view_form($db_object,$common,$user_id,$default,$error_msg,$post_var);
include_once("footer.php");
?>
