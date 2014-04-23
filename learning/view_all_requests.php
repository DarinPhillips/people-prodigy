<?php
	include("../session.php");
	include("header.php");

	class Requests
	{

	

	function view_feedback_request($db_object,$common,$user_id,$all_ids)
	{

		$path=$common->path;

		$template=$path."/templates/learning/view_all_requests.html";

		$returncontent=$common->return_file_content($db_object,$template);

		$returncontent=$common->direct_replace($db_object,$returncontent,$array);

		preg_match("/<{people_loopstart}>(.*?)<{people_loopend}>/s",$returncontent,$match1);
		$internalcon = $match1[1];

		preg_match("/<{reports_loopstart}>(.*?)<{reports_loopend}>/s",$returncontent,$match2);
		$directcon = $match1[1];
		
		preg_match("/<{self_loopstart}>(.*?)<{self_loopend}>/s",$returncontent,$match3);
		$selfcon = $match1[1];

		while(list($val,$userarr)=@each($all_ids))
		{
			while(list($userid,$chkval)=@each($userarr))
			{
				$emp_name = $common->name_display($db_object,$userid);
				$repl = $val."repl";
				$con  = $val."con";
				$mode = $val;
				$$repl.=preg_replace("/<{(.*?)}>/e","$$1",$$con);
			}
		}
	
		
		$returncontent =preg_replace("/<{people_loopstart}>(.*?)<{people_loopend}>/s",$internalrepl,$returncontent);
		$returncontent =preg_replace("/<{reports_loopstart}>(.*?)<{reports_loopend}>/s",$directrepl,$returncontent); 
		$returncontent =preg_replace("/<{self_loopstart}>(.*?)<{self_loopend}>/s",$selfrepl,$returncontent); 
		
		
			echo $returncontent;
	}



	


	}

	$obj=new Requests;


	$all_ids=$learning->persons_to_be_rated($db_object,$common,$user_id);

	$obj->view_feedback_request($db_object,$common,$user_id,$all_ids);





	include("footer.php");
?>
