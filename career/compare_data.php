<?php
include("../session.php");

include("header.php");

class compare
{
	function compare_data($db_object,$common,$default,$user_id)
	{
		$family=$common->prefix_table("family");
		
		$position=$common->prefix_table("position");
		
		$location=$common->prefix_table("location_table");
		
		$org_main=$common->prefix_table("org_main");
		
		$user_table=$common->prefix_table("user_table");
		
		$access_rights=$common->prefix_table("access_rights");
		
		$employment_type=$common->prefix_table("employment_type");
		
		$skills=$common->prefix_table("skills");
		
		$opportunity_status=$common->prefix_table("opportunity_status");
		
		$model_table=$common->prefix_table("model_table");
		
		$model_name_table=$common->prefix_table("model_name_table");
		
		$current_date=date("Y-m-d H:i:s",time());
		
		$current_date=explode(" ",$current_date);
		
		$current_date=explode("-",$current_date[0]);
		
		$disp_date=$current_date[1]."/".$current_date[2]."/".$current_date[0];
		
		$path=$common->path;
		
		$xTemplate=$path."/templates/career/compare_data.html";

		$content=$common->return_file_content($db_object,$xTemplate);
		
		$qry="select pos_id,position_name from $position";
		
		$pos_result=$db_object->get_rsltset($qry);
		
		$selqry="select levels from $org_main";
		
		$level_set1=$db_object->get_a_line($selqry);
		
		$j=$level_set1["levels"];

		for($i=1;$i<=$level_set1["levels"];$i++)
		{
		$j--;
		
		$level_set[$j][level_no]=$i;
		
		}
		
		$sql="select family_name,family_id from $family";
		
		$fam_result=$db_object->get_rsltset($sql);
		
		//$sql1="select loc_name,loc_id from $location";
		
		$loc_result=$common->return_location_for_display($db_object); //$db_object->get_rsltset($sql1);
		
		$boss_qry="select $user_table.username,$user_table.user_id,$user_table.position,$position.boss_no,$position.level_no from 

		$position,$user_table where $position.boss_no=$user_table.position 
		
		and $user_table.position!='0'";
		
		$boss_result=$db_object->get_rsltset($boss_qry);
		
		for($i=0;$i<count($boss_result);$i++)
		{
			$bossset[$i]=$boss_result[$i][boss_no];
		}
		$unique_bossset=@array_unique($bossset);
		
		$keys=@array_keys($unique_bossset);
		
		for($j=0;$j<count($keys);$j++)
		{
			$key=$keys[$j];
			
			$boss_set[$j][boss_name]=$boss_result[$key][username];
			
			$boss_set[$j][boss_id]=$boss_result[$key][level_no];
			
			$boss_set[$j][user_id]=$boss_result[$key][user_id];
			
		}
			
		$emp_qry="select user_id,username from $user_table";
		
		$emp_result=$db_object->get_rsltset($emp_qry);
		
		$type="type_".$default;
		
	   	$acc_qry="select id,$type as type from $employment_type";
	   	
	   	$acc_result=$db_object->get_rsltset($acc_qry);
	   	
	   	$eeo_qry="select eeo_id,tag from $opportunity_status";
	   	
	   	$eeo_result=$db_object->get_rsltset($eeo_qry);
	   	
	   	$skill_qry="select skill_id,skill_name,skill_type from $skills";
	   	
	   	$skill_result=$db_object->get_rsltset($skill_qry);
	   	
	   	$j=0;$k=0;
	   	
	   	for($i=0;$i<count($skill_result);$i++)
	   	{
	   		if($skill_result[$i][skill_type]=='i')
	   		{
	   			$per_skill[$j][skill_name]=$skill_result[$i][skill_name];
	   			
	   			$per_skill[$j][skill_id]=$skill_result[$i][skill_id];
	   			
	   			$inter_skill[$j][name]=$skill_result[$i][skill_name];
	   			
	   			$inter_skill[$j][id]=$skill_result[$i][skill_id];
	   			
	   			$j++;
	   		}
	   		if($skill_result[$i][skill_type]=='t')
	   		{
	   			$tech_skill[$k][skill_name]=$skill_result[$i][skill_name];
	   			
	   			$tech_skill[$k][skill_id]=$skill_result[$i][skill_id];
	   			
	   			$technical_skill[$k][name]=$skill_result[$i][skill_name];
	   			
	   			$technical_skill[$k][id]=$skill_result[$i][skill_id];
	   			
	   			$k++;
	   		}
	   	}
	   	
	   	$rater_label_relate=$common->prefix_table("rater_label_relate");
	   	
		$skill_raters=$common->prefix_table("skill_raters");
	   	
	   	$selqry3="select rater_labelno as label_no,rater_level_$default as rater_label from $rater_label_relate,$skill_raters where
		
		$rater_label_relate.rater_id = $skill_raters.rater_id and rater_type = 't'";
		
		$selres3=$db_object->get_rsltset($selqry3);
		
		for($a=0;$a<count($selres3);$a++)
		{
			$selres3[$a][a]=$a;
		}
		$selqry2="select rater_labelno as label_no,rater_level_$default as rater_level from $rater_label_relate,$skill_raters where
		
		$rater_label_relate.rater_id = $skill_raters.rater_id and rater_type = 'i'";
		
		$selres2=$db_object->get_rsltset($selqry2);
		
		for($a=0;$a<count($selres2);$a++)
		{
			$selres2[$a][a]=$a;
		}
		
		$models_id=$common->viewable_models($db_object,$user_id);

		if(count($models_id)>0)
		{
			$models_id=@implode(",",$models_id);
			
			$models="(".$models_id.")";
			
			$sql="select model_id,model_name from $model_name_table where model_id in $models";
			
			$model_res=$db_object->get_rsltset($sql);
						
		}
				
		$values["model_loop"]=$model_res;
		 
		$values["model1_loop"]=$model_res;
		
		$values["label_loop"]=$selres3;
		
		$values["label1_loop"]=$selres2;
		
	   	$values["inter_loop"]=$per_skill;
	   	
	   	$values["tech_loop"]=$tech_skill;
	   	
	   	$values["per_loop"]=$per_skill;
	   	
	   	$values["technical_loop"]=$tech_skill;
	   	
	   	$values["inter1_loop"]=$per_skill;
	   		   	
	   	$values["eeo_loop"]=$eeo_result;
	   	
	   	$values["acc_loop"]=$acc_result;
		
		$values["family_loop"]=$fam_result;
		
		//$values["location_loop"]=$loc_result;
		
		preg_match("/<{location_loopstart}>(.*?)<{location_loopend}>/s",$content,$match_loc);
		$newmatch_loc 	= $match_loc[1];
		
		while (list($key,$value) = @each($loc_result))
		{
			$loc_id 	= $key;
			$location_name 	= $value;
			$str 		.= preg_replace("/<{(.*?)}>/e","$$1",$newmatch_loc);
		
		}
		$content = preg_replace("/<{location_loopstart}>(.*?)<{location_loopend}>/s",$str,$content);
		
		
		$values["level_loop"]=$level_set;
		
		$values["position_loop"]=$pos_result;
		
		$values["boss_loop"]=$boss_set;
		
		$values["report_loop"]=$boss_set;
		
		$values["emp_loop"]=$emp_result;
		
		$array["date"]=$disp_date;
		
		$array["value0"]="0";
		
		$array["value200"]="200";
		
		$array["value100"]="100";
		
		$array["value99"]="99";
		
		$content=$common->simpleloopprocess($db_object,$content,$values);

		$content=$common->direct_replace($db_object,$content,$array);
		
		
		
		echo $content;
	
	} 
}
$obj=new compare();

$obj->compare_data($db_object,$common,$default,$user_id);

include_once("footer.php");


?>
