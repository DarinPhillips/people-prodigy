<?php
/*----------------------------------------------------
SCRIPT:globals.php
AUTHOR:chrisranjana.com
UPDATED:

DESCRIPTION:
This script has all the array constants defined

----------------------------------------------------*/

$gbl_skill_type=array("i"=>"Interpersonal",
		     "t"=>"Technical");

$gbl_test_status=array(
		'p'=>'Unapproved',
		'a'=>'Approved');

$gbl_loc=array("1"=>"Country",
		"2"=>"State",
		"3"=>"Province",
		"4"=>"City",
		"5"=>"District",
		"6"=>"Site",
		"7"=>"area");
$gbl_empcnt["count"]=10;

$gbl_date_format="%m.%d.%Y.%I:%i";

$gbl_arr_for_column=array("User Name","Email","Employee type","Access Rights","Position");


$gbl_test_mode = array("t"=>"Test", "n"=>"360");

$gbl_data_type= array("int(11)"=>"Numeric","text"=>"Text");

$gbl_gr_select = array("1"=>"As Employee",
			"2"=>"As Boss",
			"3"=>"As Admin");



$gbl_super_admin_files=array("career/edit_settings.php",
"career/mail_options.php",
"career/multirater.php",
"career/approval.php",
"career/edit_settings.php",
"career/mail_options.php",
"career/graph_options.php",
"career/posmodelcolors.php",
"career/multirater.php",
"performance/add_setting.php",
"performance/performance_message.php",
"performance/qualification.php",
"performance/addqualification.php",
"performance/editqualification.php",
"performance/rating.php",
"performance/addrating.php",
"performance/editrating.php",
"performance/priority.php",
"performance/add_priority.php ",
"performance/edit_priority.php",
"performance/sowner_defined_text.php",
"learning/edit_solution.php");

$gbl_admin_files=array("pms/fix_employee.php",
"career/select_posmodels.php",
"career/chainofcommandmodel.php",
"career/depth_chart.php",
"career/add_employee.php",
"career/add_employee_information.php",
"career/assign_succession_plan.php",
"career/assign_succession_plan1.php",
"career/appraisal.php",
"career/confirm_appraisal.php",
"career/find_ratings_admin.php",
"career/careergoals_usage.php",
"career/careergoals_detail.php",
"career/pos_without_designee.php",
"career/model_usage.php",
"career/pos_without_model.php",
"career/fam_without_model.php",
"career/mod_without_skills.php",
"career/model_match.php",
"career/appraisal_usage.php",
"career/appraisal_preference.php",
"career/view_models.php",
"career/report_appraisal_results.php",
"career/models_notin_career_goals.php",
"performance/performance_improvement_plan.php",
"performance/view_approved_appraisal_admin.php",
"performance/admin_appraisal.php",
"performance/assign_appraisal.php",
"performance/employee_without_veri_update.php",
"performance/approveplan.php",
"performance/plan1_input.php",
"performance/plan2_input.php",
"performance/find_raters.php",
"performance/userplan1_input_static.php",
"performance/userplan2_input_static.php",
"performance/userplan3_input_static.php",
"performance/find_ratings.php",
"performance/employee_without_approved_obj.php",
"performance/employee_without_veri_update.php",
"performance/view_objectives.php",
"performance/per_setting.php",
"performance/userplan1_input_static.php",
"performance/userplan2_input_static.php",
"performance/userplan3_input_static.php",
"performance/approveplan.php",
"performance/plan1_input.php",
"performance/plan2_input.php",
"performance/find_raters.php",
"performance/find_ratings.php",
"performance/employee_without_approved_obj.php",
"performance/view_objectives.php",
"performance/rating_discrepencies.php",
"performance/rating_discrepencies_1.php",
"learning/edit_solution.php",
"learning/outstanding_assignments.php",
"learning/assign_solution_builder.php",
"learning/alert_plan_builder.php",
"learning/alert_performance.php",
"learning/assign_plan_builder.php",
"learning/dev_solution.php",
"learning/learning_plan.php",
"learning/plan_approval.php",
"learning/send_plan_approval.php");


$gbl_boss_files=array("career/mobility_reports.php",
"career/mobility_reports2.php",
"career/mobility_graph.php",
"career/chainofcommandmodel.php",
"career/depth_chart.php",
"career/position_model2.php",
"career/position_model3.php",
"career/position_model4.php",
"career/upload_skills.php",
"career/skills_data.php",
"career/viewing_appraisal.php",
"career/appraisal_results.php",
"career/search_reports_skills.php",
"career/succession_plan_alert.php",
"career/gaps_at_glance.php",
"career/show_models.php",
"career/successionplan_doc_action.php",
"career/find_ratings_boss.php",
"career/outstanding_assignments.php",
"career/reports_compliance_self.php",
"career/reports_compliance_others.php",
"career/assign_succession_plan.php",
"career/assign_succession_plan1.php",
"career/update_succession_plan.php",
"performance/approve_objective.php",
"performance/performance_verification.php",
"performance/dirrep_objective.php",
"performance/approve_objective_list.php",
"performance/verify_progress.php",
"performance/performance_status_report.php",
"performance/status_report.php",
"performance/status_report1.php",
"performance/approve_objective2.php",
"performance/approve_objective3.php",
"performance/performance_plan_approval.php",
"performance/complete_employee_appraisal.php",
"performance/view_approved_appraisal.php",
"performance/view_performance_plan.php",
"performance/fix_nonapproved_appraisals.php",
"performance/boss_comments.php",
"performance/build_performance_plan.php",
"learning/learning_progress_summary_reports.php");

$gbl_admin_boss_files=array("career/mobility_reports.php",
"career/mobility_reports2.php",
"career/mobility_graph.php",
"career/succession_depl_plan.php",
"career/position_model.php",
"career/assign_test_builder.php",
"career/test_usage.php",
"career/view_tests.php",
"career/view_skills.php",
"career/change_dashboard.php",
"career/select_posmodels.php",
"career/core_data.php",
"career/assign_tech_skill.php",
"career/chainofcommandmodel.php",
"career/depth_chart.php",
"career/position_model2.php",
"career/position_model3.php",
"career/position_model4.php",
"career/upload_skills.php",
"career/skills_data.php",
"career/viewing_appraisal.php",
"career/appraisal_results.php",
"career/show_models.php",
"career/gaps_at_glance.php",
"career/successionplan_doc_action.php",
"career/search_reports_skills.php",
"career/compare_data_results.php",
"performance/performance_improvement_plan.php",
"performance/change_dashboard.php",
"performance/performance_status_report.php",
"performance/status_report.php",
"performance/status_report1.php",
"performance/boss_comments.php",
"performance/build_performance_plan.php",
"performance/verify_progress.php",
"performance/approve_objective_list.php",
"learning/learning_status_report.php",
"learning/graph_learning_status.php",
"learning/learning_progress_summary_reports.php",
"learning/change_dashboard.php",
"learning/view_learning_solution.php"
);

$gbl_emp_files=array("career/skills_data.php",
"career/gaps_at_glance.php",
"career/manual_skill_entry.php",
"career/skills_builder.php",
"career/front_panel.php",
"career/test_builder.php",
"career/compare_data.php",
"career/account_settings.php",
"career/graphpms.php",
"career/graphpms_tech.php",
"career/technical_ratings.php",
"career/career_goals.php",
"career/alert_multirater.php",
"career/multirater_appraisal.php",
"career/write_test.php",
"career/tech_multirater.php",
"career/alert_ratings.php",
"career/q_sortratings.php",
"career/textonly_qsort.php",
"career/view_appraisal.php",
"career/test_builder_edit.php",
"career/person_position.php",
"career/company_employees.php",
"career/selected_techrating_skills.php",
"career/rate_others_tech.php",
"career/replace_rej_tech.php",
"career/activities_panel.php",
"career/skills_name.php",
"career/skills_display.php",
"career/update_unapproved_test.php",
"career/store_unapproved.php",
"career/show_unapproved.php",
"performance/performance_improvement_plan.php",
"performance/view_approved_appraisal.php",
"performance/view_recent_appraisal.php",
"performance/performance_plan_approval.php",
"performance/view_performance_plan.php",
"performance/boss_comments.php",
"pms/outer.php",
"performance/per_setting.php",
"performance/build_performance_plan.php",
"performance/performance_alert.php",
"performance/userplan1_input.php",
"performance/userplan2_input.php",
"performance/userplan3_input.php",
"performance/selected_for_feedback.php",
"performance/performance_feedback.php",
"performance/performance_summary1.php",
"performance/performance_summary2.php",
"performance/save_plan.php",
"performance/summary_result.php",
"performance/summary_result_1.php",
"performance/summary_result_2.php",
"performance/download_performance_summary.php",
"performance/outer.php",
"learning/front_panel.php",
"learning/learning_progress_summary.php",
"learning/progress_summary.php",
"learning/graph_results.php",
"learning/graph_results1.php",
"learning/update_learning_plan.php",
"learning/graph_ratingsby.php",
"learning/rating_com_graph.php",
"learning/download_progress_summary.php",
"learning/front_panel.php",
"learning/download_learning_status.php",
"learning/feedback_request_form.php");

$gbl_full_array=$gbl_emp_files;

$gbl_part_array=$gbl_emp_files;

$gbl_full_array=$gbl_emp_files;

$gbl_external_array=array("outer.php","external_candidate.php");

$gbl_ext_array=$gbl_emp_files;

$gbl_personal_array=array("pms/personal_info.php","pms/outer.php");

$gbl_upload_file=array("fResume"=>"Resumes","fPhoto"=>"Photos","fLetter"=>"Letters","fCertificate"=>"Certificates","fLicense"=>"Licenses");

$gbl_max_rating = 7;
$gbl_min_rating = 3;
$gbl_max_raters = 3;

$gbl_tech_skill=2;
$gbl_inter_skill=2;

$gbl_max_priority = 7;
$gbl_min_priority = 3;

$gbl_met_value = 1;

$gbl_result=array(
		'0'=>'Not As Effective',
		'1'=>'Same',
		'2'=>'Improved');
		
// THIS IS FOR DELETING RECORDS OF AN EMPLOYEE



$gbl_delete_table['career']["appraisal"]="user_id";
$gbl_delete_table['career']["appraisal_results"]="user_id";
$gbl_delete_table['career']["assign_succession_plan"]="assigned_to";
$gbl_delete_table['career']["assign_tech_skill_builder"]="user_id";
$gbl_delete_table['career']["assign_test_builder"]="user_id";
$gbl_delete_table['career']["feedback"]="user_id";
$gbl_delete_table['career']["feedback_copy"]="user_id";
$gbl_delete_table['career']["label_for_model"]="user_id";
$gbl_delete_table['career']["model_table"]="user_id";
$gbl_delete_table['career']["models_percent_fit"]="user_id";
$gbl_delete_table['career']["other_raters"]="rater_userid";
$gbl_delete_table['career']["other_raters_tech"]="rated_user";
$gbl_delete_table['career']["skill_builder"]="emp_id";
$gbl_delete_table['career']["skills_for_rating"]="usr_id";
$gbl_delete_table['career']["tech_rating"]="rating_user";
$gbl_delete_table['career']["tech_references"]="user_to_rate";
//$gbl_delete_table["temp_feedback"]="user_id";
$gbl_delete_table['career']["temp_skill_builder"]="emp_id";
$gbl_delete_table['career']["temp_tech_rating"]="rating_user";
$gbl_delete_table['career']["temp_tech_references"]="user_to_rate";
$gbl_delete_table['career']["temp_tech_references"]="ref_userid";
$gbl_delete_table['career']["temp_tests"]="user_id";
$gbl_delete_table['career']["temp_textqsort"]="rating_user";
$gbl_delete_table['career']["temp_textqsort"]="user_rated";
$gbl_delete_table['career']["temp_user_eeo"]="user_id";
$gbl_delete_table['career']["temp_tests"]="user_id";
$gbl_delete_table['career']["tests"]="user_id";
$gbl_delete_table['career']["textqsort_rating"]="rated_user";
$gbl_delete_table['career']["temp_questions"]="user_id";
$gbl_delete_table['career']["unapproved_questions"]="user_id";
$gbl_delete_table['career']["unapproved_tests"]="user_id";
$gbl_delete_table['career']["user_tests"]="user_id";
$gbl_delete_table['career']["reject_rating"]="user_id";
$gbl_delete_table['career']["unapproved_skills"]="emp_id";


$gbl_delete_table["performance"]["approved_affected"]="user_id";
$gbl_delete_table['performance']["approved_appraisal_results"]="user_id";
$gbl_delete_table["performance"]["approved_category"]="user_id";
$gbl_delete_table["performance"]["approved_feedback"]="user_id";
$gbl_delete_table["performance"]["approved_help"]="user_id";
$gbl_delete_table["performance"]["approved_metrics"]="user_id";
$gbl_delete_table['performance']["approved_performance_appraisal"]="user_id";
$gbl_delete_table["performance"]["approved_rejected_category"]="user_id";
$gbl_delete_table["performance"]["approved_selected_objective"]="user_id";
$gbl_delete_table["performance"]["approved_selected_qualification"]="user_id";
$gbl_delete_table["performance"]["approveduser_objective"]="user_id";
$gbl_delete_table['performance']["assign_performance_appraisal"]="user_id";
$gbl_delete_table["performance"]["performance_alert"]="user_id";
$gbl_delete_table['performance']["performance_appraisal"]="user_id";
$gbl_delete_table["performance"]["performance_comments"]="user_id";
$gbl_delete_table["performance"]["performance_feedback"]="user_id";
$gbl_delete_table['performance']["plan"]="employee_id";
$gbl_delete_table['performance']["plan_improvement"]="employee_id";
$gbl_delete_table["performance"]["rejected_category"]="user_id";
$gbl_delete_table["performance"]["rejected_objective"]="user_id";
$gbl_delete_table["performance"]["selected_qualification"]="user_id";
$gbl_delete_table['performance']["temp_affected"]="user_id";
$gbl_delete_table['performance']["temp_plan_improvement"]="employee_id";
$gbl_delete_table['performance']["temp_planbuilder"]="user_id";
$gbl_delete_table['performance']["temp_selected_objective"]="user_id";
$gbl_delete_table["performance"]["unapproved_affected"]="user_id";
$gbl_delete_table["performance"]["unapproved_category"]="user_id";
$gbl_delete_table["performance"]["unapproved_category"]="boss_id";
$gbl_delete_table["performance"]["unapproved_help"]="user_id";
$gbl_delete_table["performance"]["unapproved_metrics"]="user_id";
$gbl_delete_table["performance"]["unapproved_plan_improvement"]="employee_id";
$gbl_delete_table['performance']["tempuser_objective"]="user_id";
$gbl_delete_table["performance"]["unapproved_selected_objective"]="user_id";
$gbl_delete_table["performance"]["unapproved_selected_qualification"]="user_id";
$gbl_delete_table["performance"]["user_table"]="user_id";
$gbl_delete_table["performance"]["verified_user"]="for_user_id";
$gbl_delete_table["performance"]["unapproveduser_objective"]="user_id";




$gbl_delete_table["learning"]["approved_devbuilder"]="user_id";
$gbl_delete_table["learning"]["approved_devsolution"]="emp_id";
$gbl_delete_table['learning']["assign_solution_builder"]="user_id";
$gbl_delete_table["learning"]["learning_feedback_results"]="rated_id";
$gbl_delete_table["learning"]["temp_devbuilder"]="user_id";
$gbl_delete_table['learning']["temp_devsolution"]="emp_id";
$gbl_delete_table["learning"]["unapproved_devbuilder"]="user_id";
$gbl_delete_table['learning']["unapproved_rejected_category"]="user_id";
$gbl_delete_table['learning']["unapproved_devsolution"]="emp_id";
$gbl_delete_table['learning']["unapproved_skill_builder"]="emp_id";



$gbl_delete_table['core']["admins"]="user_id";
$gbl_delete_table['core']["user_eeo"]="user_id";



/*$gbl_delete_table["temp_help"]="user_id";
$gbl_delete_table["temp_metrics"]="user_id";*/
//$gbl_delete_table["temp_selected_qualification"]="user_id";
//$gbl_delete_table["temp_user_table"]="user_id";

//$gbl_delete_table["user_session"]="user_id";
//$gbl_delete_table["temp_category"]="user_id";

?>
