<?php

namespace mvc\model;
require_once './../../php-redmine-api-master/lib/autoload.php';

class MonthlyReportRedmineData 
{

	function __construct($redmine_url, $key)
	{
		$this->client = new \Redmine\Client($redmine_url, $key);
	}

	public function getIssue($issueParam1, $issueParam2, $issueClosedParam1, $issueClosedParam2, &$result)
	{
		$issue = array();
		$issue1 = $this->client->issue->all($issueParam1);
		$issue2 = $this->client->issue->all($issueParam2);
		$issueClosed1 = $this->client->issue->all($issueClosedParam1);
		$issueClosed2 = $this->client->issue->all($issueClosedParam2);
		foreach ($issue1['issues'] as $value) {
			array_push($issue, $value);
		}
		foreach ($issue2['issues'] as $value) {
			if (isset($value['parent'])) {
				$value['parent_id'] 	= $value['parent']['id'];
			} else {
				$value['parent_id']  = 0;
			}
			array_push($issue, $value);
		}
		foreach ($issueClosed1['issues'] as $issueClosed) {
			array_push($issue, $issueClosed);
		}
		foreach ($issueClosed2['issues'] as $issueClosed) {
			if (isset($issueClosed['parent'])) {
				$issueClosed['parent_id'] 	= $issueClosed['parent']['id'];
			} else {
				$issueClosed['parent_id']  = 0;
			}
			array_push($issue, $issueClosed);
		}

		foreach ($issue as $issue) {
			$temp = array();
			$temp['issue_id'] 		= $issue['id'];
			$temp['subject'] 		= $issue['subject'];
			$temp['project_name'] 	= $issue['project']['name'];
			$temp['assigned_to'] 	= $issue['assigned_to']['name'];
			if (isset($issue['category'])) {
				$temp['category_id'] 	= $issue['category']['id'];
			} else {
				$temp['category_id'] 	= 0;
			}
			$temp['created_on'] 	= $issue['created_on'];
			array_push($result, $temp);
		}
		if (count($issue1['issues']) == $issueParam1['limit'] || count($issue2['issues']) == $issueParam2['limit'] || count($issueClosed1['issues']) == $issueClosedParam1['limit'] || count($issueClosed2['issues']) == $issueClosedParam2['limit']) {
            $issueParam1['offset'] += $issueParam1['limit'];
            $issueParam2['offset'] += $issueParam2['limit'];
            $issueClosedParam1['offset'] += $issueClosedParam1['limit'];
            $issueClosedParam2['offset'] += $issueClosedParam2['limit'];
            $this->getIssue($issueParam1, $issueParam2, $issueClosedParam1, $issueClosedParam2, $result);
        }
	}

	public function getTimeEntries($timeEntryParam1, $timeEntryParam2, &$result)
	{
		$timeEntries 	= array();
		$timeEntries1 = $this->client->time_entry->all($timeEntryParam1);
		$timeEntries2 = $this->client->time_entry->all($timeEntryParam2);
		foreach ($timeEntries1['time_entries'] as $timeEntry1) {
			array_push($timeEntries, $timeEntry1);
		}
		foreach ($timeEntries2['time_entries'] as $timeEntry2) {
			array_push($timeEntries, $timeEntry2);
		}
		foreach ($timeEntries as $time_entry) {
			$temp = array();
			$temp['project_name'] 	= $time_entry['project']['name'];
			$temp['issue_id']	 	= $time_entry['issue']['id'];
			$temp['user_id'] 		= $time_entry['user']['id'];
			$temp['user_name'] 		= $time_entry['user']['name'];
			$temp['spent_time'] 	= $time_entry['hours'];
			$temp['spent_on'] 		= $time_entry['spent_on'];
			array_push($result, $temp);
		}
		if (count($timeEntries1['time_entries']) == $timeEntryParam1['limit'] || count($timeEntries2['time_entries']) == $timeEntryParam2['limit']) {
            $timeEntryParam1['offset'] += $timeEntryParam1['limit'];
            $timeEntryParam2['offset'] += $timeEntryParam2['limit'];
            $this->getTimeEntries($timeEntryParam1, $timeEntryParam2, $result);
        }
	}
}