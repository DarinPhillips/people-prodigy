<?php
include("../session.php");
include("header.php");
class  Enmployee_contacts
{
  function display_details($common,$db_object,$user_id,$default)
  {
	$path=$common->path;
	$xFile=$path."templates/career/core_data/employees_without_contact.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);
$contact_table=$common->prefix_table("contacts");
$user_table=$common->prefix_table("user_table");
$contact_display="contact_display_".$default;

$selqry="select contact_id,concat('<{',concat(contact_field,'}>')) as contact_field,$contact_display as contact_display from $contact_table where status='Yes' order by contact_id";

$contactset=$db_object->get_rsltset($selqry);
$values["contact1_loop"]=$contactset;
preg_match("/<{contact1_loopstart}>(.*?)<{contact1_loopend}>/s",$xTemplate,$mat);
$replace=$mat[0];
$replaced=$common->simpleloopprocess($db_object,$replace,$values);
$xTemplate=preg_replace("/<{contact1_loopstart}>(.*?)<{contact1_loopend}>/s",$replaced,$xTemplate);

	preg_match("/<{contact_loopstart}>(.*?)<{contact_loopend}>/s",$xTemplate,$match);
	$replace1=$match[0];
	
	$vals["contact_loop"]=$contactset;

$replaced1=$common->simpleloopprocess($db_object,$replace1,$vals);
$xTemplate=preg_replace("/<{contact_loopstart}>(.*?)<{contact_loopend}>/s",$replaced1,$xTemplate);

$flds=$common->return_fields($db_object,$user_table);

$selqry="select contact_field from $contact_table where status='Yes' order by contact_id";
$conflds=$db_object->get_single_column($selqry);

$or=" or";

for($i=0;$i<count($conflds);$i++)
{
	$tempfld=$conflds[$i];
	$subqry.=" $tempfld is null $or";
	$extrasub.="if($tempfld is null,'X',$tempfld) as $tempfld,";
}

$subqry=substr($subqry,0,-strlen($or));

 $extrasub=substr($extrasub,0,-1);
 
$selqry="select email,username,user_id,$extrasub from $user_table where $subqry";
$userset=$db_object->get_rsltset($selqry);

for($i=0;$i<count($userset);$i++)
{
	$temp_id=$userset[$i]["user_id"];
	$userset[$i]["username"]=$common->name_display($db_object,$temp_id);
}


$val["user_loop"]=$userset;



$xTemplate=$common->simpleloopprocess($db_object,$xTemplate,$val);
$vl=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vl);
echo $xTemplate;
   }
}
$cotobj= new Enmployee_contacts;
$cotobj->display_details($common,$db_object,$user_id,$default);
include("footer.php");
?>