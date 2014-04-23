<?php
/*---------------------------------------------
SCRIPT:boss_cultural_prof.php
AUTHOR:info@chrisranjana.com	
UPDATED:6th Jan

DESCRIPTION:
This script displays cultural profile of bosses for ADMIN view.

---------------------------------------------*/
include("../session.php");
include("header.php");
class profile
{
	function cultural_profile($db_object,$common,$user_id,$default,$post_var)
	{

		while(list($kk,$vv) = @each($post_var))
		{
			$$kk = $vv;
		}

		$path=$common->path;
		$xtemplate=$path."templates/career/boss_cultural_prof.html";
		$file=$common->return_file_content($db_object,$xtemplate);
		


		$textqsort_rating=$common->prefix_table("textqsort_rating");
		$user_table=$common->prefix_table("user_table");
		$skills=$common->prefix_table("skills");
		$rater_label_relate=$common->prefix_table("rater_label_relate");
		$skill_raters=$common->prefix_table("skill_raters");
		
//all the users under the boss are displayed...
		$users=$common->return_direct_reports($db_object,$fBoss_id);
		
		if($users[0]!="")
		{
			if(count($users)>1)
			{
				$users_id=@implode(",",$users);
				
				$users_id="(".$users_id.")";
			}
			if(count($users)==1)
			{
				$users_id="(".$users[0].")";
			}
		}
		
		$qry="select round(avg(rater_label_no)) as rater_label_no,
			rater_type,rated_user,skill_id 
			from $textqsort_rating where rated_user in $users_id 
			group by rated_user,skill_id";
		
		$result=$db_object->get_rsltset($qry);
		
		$selqry2="select rater_labelno from $rater_label_relate,$skill_raters where
			$rater_label_relate.rater_id = $skill_raters.rater_id and rater_type = 'i' order by
			rater_labelno desc limit 1";
				
		$selres2=$db_object->get_single_column($selqry2);
		
		$max_rate=$selres2[0];

		$skill_arr=array();
		
		for($a=0;$a<count($result);$a++)
		{
			$skill_arr[$a]=$result[$a][skill_id];
		}
		
		$unq_array=@array_unique($skill_arr);
		
		$values=@array_values($unq_array);
		
		$count=array();
		
		$total=array();

		for($b=0;$b<count($unq_array);$b++)
		{
			$value=$values[$b];
			
			for($c=0;$c<count($result);$c++)
			{
				
					$ch_val=$result[$c][skill_id];
					
					if($value==$ch_val)
					{
						$rate=$result[$c][rater_label_no];
						
						if($rate==$max_rate)
						{
							$count1[$b]++;
						}
						$total[$b]++;
					}
			}
								
		}
		
		@arsort($count1);
		
		$keys1=@array_keys($count1);
		
		$keys2=@array_keys($total);
		
		$rem=@array_diff($keys2,$keys1);
		
		
		$pattern="/<{skill_loopstart}>(.*?)<{skill_loopend}>/s";
		
		preg_match($pattern,$file,$match);
		
		$match=$match[0];
		
		$keys2=@array_keys($unq_array);
		
		$skill_name1=array();
		
		$percent1=array();
		
		for($i=0;$i<count($keys1);$i++)
		{
			$key=$keys1[$i];
			
			$skill_id=$result[$key][skill_id];
			
			$qry="select skill_name from $skills where skill_id='$skill_id'";
			
			$qry_res=$db_object->get_a_line($qry);
			
			$skill_name1[$i]=$qry_res[skill_name];
			
			$percent1[$i]=($count1[$key]/$total[$key])*100;
			
			$percent1[$i] = @sprintf("%01.2f",$percent1[$i]);
			
		}
		
			@arsort($percent1);
			
			$keys=@array_keys($percent1);
			
			for($a=0;$a<count($percent1);$a++)
			{
				$key=$keys[$a];
				
				$skill_name=$skill_name1[$a];
				
				$percent=$percent1[$a];
				
				$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
			}
			@sort($rem);
			
		for($j=0;$j<count($rem);$j++)
		{
			$key=$rem[$j];
			
			//$key1=$keys2[$key];
	
			$skill_id=$result[$key][skill_id];
			
			$qry="select skill_name from $skills where skill_id='$skill_id'";
			
			$qry_res=$db_object->get_a_line($qry);
			
			$skill_name=$qry_res[skill_name];

			//$percent=($count1[$key1]/$total[$key1])*100;
			
			$percent=0;
			
			$str.=preg_replace("/<{(.*?)}>/e","$$1",$match);
		}
		
		$file=preg_replace($pattern,$str,$file);
		
		$file=$common->direct_replace($db_object,$file,$xArray);
		
		echo $file;
	}
}
$obj=new profile();

$obj->cultural_profile($db_object,$common,$user_id,$default,$post_var);

include_once("footer.php");

?>
