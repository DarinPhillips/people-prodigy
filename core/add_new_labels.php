<?php
include("../session.php");
include("header.php");
class AddnewLabel
{
function Labeldisplay($common,$db_object,$gbl_data_type,$error_msg,$default)
{
	$path=$common->path;
	$xFile=$path."templates/core/add_new_labels.html";
	$xTemplate=$common->return_file_content($db_object,$xFile);


	$language=$common->prefix_table("language");

	$feilds=$common->return_fields($db_object,$language);
	$selqry="select $feilds from $language";
	$langset=$db_object->get_rsltset($selqry);
		
$univlang="lang_".$default;
preg_match("/<{lang_loopstart}>(.*?)<{lang_loopend}>/s",$xTemplate,$mat);
$replace=$mat[1];
for($i=0;$i<count($langset);$i++)
{
	$id=$langset[$i]["lang_id"];
	$langval=$langset[$i][$univlang];
	$replaced.=preg_replace("/<{(.*?)}>/e","$$1",$replace);
}
$xTemplate=preg_replace("/<{lang_loopstart}>(.*?)<{lang_loopend}>/s",$replaced,$xTemplate);
$value["datatype_loop"]=$gbl_data_type;
$loopstart="datatype_loopstart";
$loopend="datatype_loopend";
$xTemplate=$common->singleloop_replace($db_object,$loopstart,$loopend,$xTemplate,$gbl_data_type,$sel_val);

$vals=array();
$xTemplate=$common->direct_replace($db_object,$xTemplate,$vals);
	echo $xTemplate;
	
}
function add_datatype($common,$db_object,$form_array,$default,$error_msg)
{
//	extract($form_array);
	while(list($kk,$vv)=@each($form_array))
	{
		$$kk=$vv;
		if(ereg("fLabelvalue_",$kk))
		{
			$id=split("fLabelvalue_",$kk);
			 $id=$id[1];
			$idset[$id]=$vv;
		}
	}
	
//print_r($form_array);
//print_r($idset);

while(list($kk,$vv)=@each($idset))
{
	$name="name_".$kk;
	$langset.="$name='$vv',";
}

	$user_table=$common->prefix_table("user_table");
	$name_fields=$common->prefix_table("name_fields");

	$name="name_".$default;
	$fieldname=TRIM($fLabel);
$chkarr=preg_split("/ /",$fieldname,-1,PREG_SPLIT_NO_EMPTY);
$ch_cnt=count($chkarr);
if($ch_cnt>1)
{
	echo "Enter Without Space";
//	echo "<script>window.location.replace('add_new_labels.php')</script>";
}
else
{
	$selqry="desc $name_fields";
	$fieldarray=$db_object->get_single_column($selqry);
	$selqry="desc $user_table";
	$userfields=$db_object->get_single_column($selqry);
	if(@in_array($fieldname,$userfields) || @in_array($fieldname,$fieldarray))
	{
		echo "The Field Already Exists";
	}
	else
	{
		$insqry="insert into $name_fields set field_name='$fieldname',$langset status='NO'";
		$db_object->insert($insqry);
		echo "ins=$insqry<br>";
		$alterqry="alter table $user_table add column $fLabel $fDatatype";
		$db_object->insert($alterqry);
		echo "alt=$alterqry<br>";
		echo "<script>window.location.replace('add_lables.php')</script>";
	}
}

}
	
}
$lbobj= new AddnewLabel;
if($fSave)
{
$lbobj->add_datatype($common,$db_object,$post_var,$default,$error_msg);
}
else
{
$lbobj->Labeldisplay($common,$db_object,$gbl_data_type,$error_msg,$default);
}
include("footer.php");


?>