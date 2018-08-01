<?php

use mvc\model\MonthlyReportRedmineData;
use mvc\model\MonthlyReport;
require_once("./../../mvc/model/MonthlyReportRedmineData.php");
require_once("./../../mvc/model/MonthlyReport.php");

$today = date("Ymd");
$config = include('./../../config/config.php');
if (isset($_GET["from-date"])) {
	$startDate 	= $_GET["from-date"];
} else {
	$startDate	= "";
}

if (isset($_GET["to-date"])) {
	$dueDate 	= $_GET["to-date"];
} else {
	$dueDate	= "";
}

$redmineData 	= new \mvc\model\MonthlyReportRedmineData($config['redmine_url'], $config['redmine_api_key']);
$report 		= new \MonthlyReport();

$project1Id 	= $config['project_id_1'];
$project2Id 	= $config['project_id_2'];

// Get time entries

$timeEntryParam1 = array('from' 		=> $startDate,
				    	'to' 			=> $dueDate,
				    	'project_id' 	=> $project1Id,
				    	'offset'		=> 0,
				    	'limit' 		=> 100);
$timeEntryParam2 = array('from' 		=> $startDate,
				    	'to' 			=> $dueDate,
				    	'project_id' 	=> $project2Id,
				    	'offset'		=> 0,
				    	'limit' 		=> 100);
$timeEntries 	= array();
$redmineData->getTimeEntries($timeEntryParam1, $timeEntryParam2, $timeEntries);

// Get Issue

$issueParam1 	= array('project_id' 	=> $project1Id,
						'offset'		=> 0,
				    	'limit' 		=> 100,
				    	'sort' 			=> 'id');
$issueParam2 	= array('project_id' 	=> $project2Id,
						'offset'		=> 0,
				    	'limit' 		=> 100,
				    	'sort' 			=> 'id');
$issueClosedParam1 	= array('project_id' => $project1Id,
						'offset'		=> 0,
				    	'limit' 		=> 100,
				    	'status_id' 	=> 'closed',
				    	'sort' 			=> 'id');
$issueClosedParam2 	= array('project_id' => $project2Id,
						'offset'		=> 0,
				    	'limit' 		=> 100,
				    	'status_id' 	=> 'closed',
				    	'sort' 			=> 'id');
$issues = array();
$redmineData->getIssue($issueParam1, $issueParam2, $issueClosedParam1, $issueClosedParam2, $issues);

// Get report data

$reportData 	= array();
$reportData 	= $report->getReportData($issues, $timeEntries);

// Export report

$report->exportReportFile($startDate, $dueDate, $reportData);
