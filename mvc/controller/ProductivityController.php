<?php

use mvc\model\ProductivityRedmineData;
use mvc\model\ProductivityReport;
require_once("./../../mvc/model/ProductivityRedmineData.php");
require_once("./../../mvc/model/ProductivityReport.php");

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

$redmineData 	= new \mvc\model\ProductivityRedmineData($config['redmine_url'], $config['redmine_api_key']);
$report 		= new \ProductivityReport();

$projectId 		= $config['project_id_1'];

// Get time entries

$timeEntryParam = array(
				    	'project_id' 	=> $projectId,
				    	'offset'		=> 0,
				    	'limit' 		=> 100);
$timeEntries 	= array();
$redmineData->getTimeEntries($timeEntryParam, $timeEntries);

// Get Issue

$issueParam 	= array(
						'project_id' 	=> $projectId,
						'offset'		=> 0,
				    	'limit' 		=> 100,
				    	'sort' 			=> 'id');
$issueClosedParam 	= array(
						'project_id' => $projectId,
						'offset'		=> 0,
				    	'limit' 		=> 100,
				    	'status_id' 	=> 'closed',
				    	'sort' 			=> 'id');
$issues = array();
$redmineData->getIssue($issueParam, $issueClosedParam, $issues);

// Get report data

$reportData 	= array();
$reportData 	= $report->getReportData($startDate, $dueDate, $issues, $timeEntries);

// Export report

$report->exportReportFile($startDate, $dueDate, $reportData);
