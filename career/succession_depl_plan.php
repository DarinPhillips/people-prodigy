<?php
/*---------------------------------------------
SCRIPT:career_goals.php
AUTHOR:info@chrisranjana.com	
UPDATED:13th Dec

DESCRIPTION:
This script sets the career goals for the users

---------------------------------------------*/
include("../session.php");
include("header.php");

class successsionplan
{
function select_components($db_object,$common,$default,$user_id,$post_var)
	{
		while(list($kk,$vv) = @each($post_var))
		{
		$$kk = $vv;
		}	

	$xPath		= $common->path;
	$returncontent	= $xPath."/templates/career/succession_depl_plan.html";
	$returncontent	= $common->return_file_content($db_object,$returncontent);

	$opportunity_status = $common->prefix_table('opportunity_status');
	$employment_type = $common->prefix_table('employment_type');	

	preg_match("/<{maincategory_loopstart}>(.*?)<{maincategory_loopend}>/s",$returncontent,$mainmatchold);
	$mainmatch = $mainmatchold[1];
	
	preg_match("/<{subcategory_loopstart}>(.*?)<{subcategory_loopend}>/s",$mainmatch,$submatchold);
	$submatch = $submatchold[1];

//CHECK IF THE LEARNING PLAN IS PURCHASED...
	$returncontent = $common->is_module_purchased($db_object,$xPath,$returncontent,$common->lfvar);

	$mysql = "select distinct(category) from $opportunity_status";
	$main_category_arr = $db_object->get_single_column($mysql);
	
	for($i=0;$i<count($main_category_arr);$i++)
		{
			$categoryname = $main_category_arr[$i];
			$categoryid 	= $i+1;
				
			$mysql = "select eeo_id,tag from $opportunity_status where category = '$categoryname'";
			$subcat_arr = $db_object->get_rsltset($mysql);
			
			$str_in = '';

			for($j=0;$j<count($subcat_arr);$j++)	
			{
				$subcat_name = $subcat_arr[$j]['tag'];
				$subcat_id = $subcat_arr[$j]['eeo_id'];
				$str_in .= preg_replace("/<{(.*?)}>/e","$$1",$submatch);
			}
			$innermatchfull = preg_replace("/<{subcategory_loopstart}>(.*?)<{subcategory_loopend}>/s",$str_in,$mainmatch);

			$str_out .= preg_replace("/<{(.*?)}>/e","$$1",$innermatchfull);

		}

	$returncontent = preg_replace("/<{maincategory_loopstart}>(.*?)<{maincategory_loopend}>/s",$str_out,$returncontent);	
	
	$mysql = "select id,type_$default as empltypename from $employment_type";	
	$employmenttype_arr = $db_object->get_rsltset($mysql);
	
	$simpleloopvalues['empltypes_loop'] = $employmenttype_arr;
	
	$returncontent  = $common->simpleloopprocess($db_object,$returncontent , $simpleloopvalues);
	$returncontent 	= $common->direct_replace($db_object,$returncontent,$values);	
	echo $returncontent;
	
	}
}
$obj = new successsionplan;

$obj->select_components($db_object,$common,$default,$user_id,$post_var);


include("footer.php");
?>
