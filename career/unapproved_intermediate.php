<?php
include("../session.php");
include("header.php");
class Intermediate
{
  function inter($common,$db_object,$emp_id)
  {
	$xFile="../templates/career/unapproved_intermediate.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);

	$postion_table=$common->prefix_table("position");
	$sklbld=$common->prefix_table("unapproved_skill_builder");
	$qry="select $postion_table.position_name,$sklbld.pos_id from $postion_table,
	$sklbld where $sklbld.pos_id=$postion_table.pos_id and
	$sklbld.emp_id='$emp_id'
	order by $postion_table.position_name";
	$positionset=$db_object->get_rsltset($qry);
	preg_match("/<{position_loop_start}>(.*?)<{position_loop_end}>/s",$xTemplate,$match);
	$replace=$match[1];
	while(list($kk,$vv)=@each($positionset))
	{
		$values["directreplace"]["position_name"]=$positionset[$kk]["position_name"];
		$values["directreplace"]["pos_id"]=$positionset[$kk]["pos_id"];
		$values["directreplace"]["emp_id"]=$emp_id;
		$replaced.=$common->direct_replace($db_object,$replace,$values);
	}
	
	$xTemplate=preg_replace("/<{position_loop_start}>(.*?)<{position_loop_end}>/s",$replaced,$xTemplate);
	$values=array();
	$xTemplate=$common->direct_replace($db_object,$xTemplate,$values);
	echo $xTemplate;
	//exit;
		

  }
}
$intobj=new Intermediate;
$intobj->inter($common,$db_object,$emp_id);
include("footer.php");
?>