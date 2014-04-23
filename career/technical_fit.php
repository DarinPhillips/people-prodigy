<?php

include_once("../session.php");

class fit
{
	function technical_fit($db_object,$common,$image,$user_id)
	{
		$models_percent_fit=$common->prefix_table("models_percent_fit");
		
		$sql="select avg(percent_fit) as percent from 
		
		$models_percent_fit where user_id='$user_id' and skill_type='t'";
		
		$res=$db_object->get_a_line($sql);
		
		$percent=$res[0];
		
		if($percent=="")
		{
			$percent=0;
		}
		
		$total=100;
		
			$heads = array(
    array("Technical Fit", 3, "c"),  
    );
		
		$array=array($percent,$total);
				
		$vals=$image->return_Array($array);
		
		$image->init(150,150, $vals);
		
		$image->draw_heading($heads);
	
		$image->set_legend_percent();

		 $image->display($filename);

	}
}

$obj=new fit();

$obj->technical_fit($db_object,$common,$image,$user_id);

?>
