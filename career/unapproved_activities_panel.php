<?php
include("../session.php");
include("header.php");
class activities
{
   function active($common,$db_object,$form_array,$fuser_id,$build_id,$fObj_id)
   {
  	
	while(list($kk,$vv)=@each($form_array))
 	{
		$$kk=$vv;
	}
	
	$objtable=$common->prefix_table("unapproved_objectives");

	if($fObj_id!="")
	{
		$whereclause=" and obj_id > '$fObj_id' ";
	}
	

	$objsql="select obj_id,objective_name from $objtable where build_id='$build_id'";
	$objsql=$objsql.$whereclause." order by obj_id limit 0,1";
	$objset=$db_object->get_rsltset($objsql);

	$values["directreplace"]["Obvs"]=$objset[0]["objective_name"];
	echo "<br>";
	$tablename=$common->prefix_table("config");
	$sqlqry="select * from $tablename";
	$activeset=$db_object->get_a_line($sqlqry);
	$activeno=$activeset["activities"];
	$filename="../templates/career/unapproved_activities_panel.html";
	$filecontent=$common->return_file_content($db_object,$filename);
	preg_match("/<{active_begin}>(.*?)<{active_exit}>/s",$filecontent,$actmatch);
	$actmat=$actmatch[0];
	for($i=1;$i<=$activeno;$i++)
	{
		$index=$i;
		$replace.=preg_replace("/<{(.*?)}>/e","$$1",$actmat);
	}

	$obj_id=$objset[0]["obj_id"];
	if($objset[0]["obj_id"])
	{


		$act_table=$common->prefix_table("unapproved_activities");
		$obj_table=$common->prefix_table("unapproved_objectives");
		$act_qry="select $act_table.activity_name from $act_table,$obj_table where $obj_table.obj_id=$act_table.obj_id and $obj_table.obj_id='$obj_id' order by $act_table.act_id";

	$activities=$db_object->get_rsltset($act_qry);
	
	for($i=0;$i<count($activities);$i++)
	{
		$k=$i+1;
		$actname="active_".$k;
		$values["directreplace"][$actname]=$activities[$i]["activity_name"];
	}

	//print_r($values);
	//echo $replace;	
	$replace=$common->direct_replace($db_object,$replace,$values);
	$filecontent=preg_replace("/<{active_begin}>(.*?)<{active_exit}>/s",$replace,$filecontent);

	$values["directreplace"]["obj_id"]=$objset[0]["obj_id"];
	$values["directreplace"]["build_id"]=$build_id;
	$filecontent=$common->direct_replace($db_object,$filecontent,$values);
	
	echo $filecontent;

}
	else
	{
$bld_id=$build_id;
echo "<script language='javascript'>window.location.replace('unapproved_skills_name.php?build_id=$bld_id')</script>";
exit;
	}

   }


  function active_update($common,$db_object,$form_array,$fuser_id,$build_id,$fObj_id)
  {
	

	while(list($kk,$vv,)=@each($form_array))
	{
		$$kk=$vv;

		if(ereg("^active_",$kk))
		{
			$actarray["$kk"]=$vv;

		}
		
	}
 
	$act_table=$common->prefix_table("unapproved_activities");
	$obj_table=$common->prefix_table("unapproved_objectives");
	$obj_query="select $act_table.obj_id,$act_table.act_id from $act_table,$obj_table where $act_table.obj_id=$obj_table.obj_id and $obj_table.build_id='$build_id' and $act_table.obj_id='$fObj_id' order by $act_table.act_id limit 0,1";
	$obj_id=$db_object->get_a_line($obj_query);
	$objid=$obj_id["obj_id"];
	$actid=$obj_id["act_id"];
	if($objid==$fObj_id)
	{
		while(list($kk,$vv)=@each($actarray))
		{
	
		$act_qry="replace into $act_table set act_id='$actid',obj_id='$fObj_id',activity_name='$vv'";
		$db_object->insert($act_qry);
		$act_subqry="select act_id from $act_table where act_id>$actid limit 0,1";
		$act_id=$db_object->get_a_line($act_subqry);
		$actid=$act_id["act_id"];
		}
	}
	else
	{

		while(list($kk,$vv)=@each($actarray))
		{
		$actquery="insert into $act_table set obj_id='$fObj_id',activity_name='$vv'";
		$db_object->insert($actquery);
		}
	}
$fObj_id=$fObj_id+1;
$this->active($common,$db_object,$form_array,$fuser_id,$build_id,$fObj_id);
//exit;
//return $build_id;	
 }

}
$actobj=new activities;
if($fNextact)
{
$bld_id=$actobj->active_update($common,$db_object,$_GET,$fuser_id,$build_id,$fObj_id);
}
else
{
$actobj->active($common,$db_object,$_POST,$fuser_id,$build_id,$fObj_id);
}
include("footer.php");
?>