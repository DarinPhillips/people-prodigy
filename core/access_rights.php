<?php
include("../session.php");
include("header.php");
class Access_rights
{
   function access_rights1($common,$db_object,$default)
   {
   	$path=$common->path;
   	$xFile=$path."templates/core/access_rights.html";
   	$xTemplate=$common->return_file_content($db_object,$xFile);
	$type="type_".$default;
   	$access_rights=$common->prefix_table("access_rights");
   	$acqry="select id,$type,rights from $access_rights";
   	$rslt=$db_object->get_rsltset($acqry);
  //	print_r($rslt);
  //	exit;
   	preg_match("/<{emp_loopstart}>(.*?)<{emp_loopend}>/s",$xTemplate,$match);
  	$replace=$match[1];
  	for($i=0;$i<count($rslt);$i++)
  	{
  		$employeename=$rslt[$i][$type];
  		$id=$rslt[$i]["id"];
  		if($rslt[$i]["rights"]=="yes")
  		{
  			$checkedy="checked";
  		}
  		else if($rslt[$i]["rights"]=="no")
  		{
  			$checkedn="checked";
  		}
  		else
  		{
  			$checkedy="";
  			$checkedn="";
  		}
  		$replaced.=preg_replace("/{{(.*?)}}/e","$$1",$replace);
	  		$checkedy="";
  			$checkedn="";
  	}
$xTemplate=preg_replace("/<{emp_loopstart}>(.*?)<{emp_loopend}>/s",$replaced,$xTemplate);
$vals=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);

	echo $xTemplate;
   	
   	
   }
   function update_rights($common,$db_object,$form_array,$error_msg)
   {
   	$h=0;
   	while(list($kk,$vv)=@each($form_array))
   	{
   		$$k=$vv;

   		if(ereg("^emp_rights_",$kk))
   		{
   			$d=split("emp_rights_",$kk);
   			$id=$d[1];
   			$idset[$h]=$id;
   			$rights[$h]=$vv;
   			$h++;
   			
   		}
   		
   			
   	}

   	$access_rights=$common->prefix_table("access_rights");
   	for($i=0;$i<count($rights);$i++)
   	{
   		$rt=$rights[$i];
   		$id=$idset[$i];
   		$rtqry="update $access_rights set rights='$rt' where id='$id'";
		$db_object->insert($rtqry);
   	}
   	echo $error_msg["cEmpdetailUpdated"];
   //	include("footer.php");
   //	exit;
   }
}
$acobj=new Access_rights;
if($fSubmit)
{
	$acobj->update_rights($common,$db_object,$_POST,$error_msg);
}

$acobj->access_rights1($common,$db_object,$default);
include("footer.php");
?>