<?php

use mvc\model\OperationRedmineData;
use mvc\model\OperationReport;
require_once("./../../mvc/model/OperationRedmineData.php");
require_once("./../../mvc/model/OperationReport.php");

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

$redmineData 	= new \mvc\model\OperationRedmineData($config['redmine_url'], $config['redmine_api_key']);
$report 		= new \OperationReport();

$project1Id 	= $config['project_id_1'];
$project2Id 	= $config['project_id_2'];

// Get all time entries

$allTimeEntryParam1 = array(
				    	'project_id' 	=> $project1Id,
				    	'offset'		=> 0,
				    	'limit' 		=> 100);
$allTimeEntryParam2 = array(
				    	'project_id' 	=> $project2Id,
				    	'offset'		=> 0,
				    	'limit' 		=> 100);
$allTimeEntries 	= array();
$redmineData->getTimeEntries($allTimeEntryParam1, $allTimeEntryParam2, $allTimeEntries);

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

$issuesAll = $report->getReportDataAll($issues, $allTimeEntries);

// Export report

$report->exportReportFile($startDate, $dueDate, $reportData, $issuesAll);
