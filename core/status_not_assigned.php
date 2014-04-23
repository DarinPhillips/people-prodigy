<?php
include("../session.php");
include("header.php");
class Show_status
{
	function display_status($common,$db_object,$user_id,$default)
	{
		$path=$common->path;
		$xFile=$path."templates/core/status_not_assigned.html";
		$xTemplate=$common->return_file_content($db_object,$xFile);
		$type="type_".$default;
		$employment_type=$common->prefix_table("employment_type");
		$user_table=$common->prefix_table("user_table");
		$fds=$common->return_fields($db_object,$employment_type);
		$selqry="select $employment_type.id ,$employment_type.$type,

date_format($employment_type.date_added,'%m.%d.%Y.%i:s') as date_added 

,$user_table.employment_type from $employment_type left join $user_table  on $employment_type.id=$user_table.employment_type where $employment_type.status='Yes' and  $user_table.employment_type is null";

	//	echo $selqry ;
		
		$statusset=$db_object->get_rsltset($selqry);

		preg_match("/<{status_loopstart}>(.*?)<{status_loopend}>/s",$xTemplate,$mat);
	$replace=$mat[1];

	for($i=0;$i<count($statusset);$i++)
	{
		$employment_status=$statusset[$i][$type];
		$date_added=$statusset[$i]["date_added"];
		$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
	}
$xTemplate=preg_replace("/<{status_loopstart}>(.*?)<{status_loopend}>/s", $replaced,$xTemplate);

$vals=array();
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);	
		echo $xTemplate;
	}

}
$stobj= new Show_status;
$stobj->display_status($common,$db_object,$user_id,$default);
include("footer.php");
?>
