<?php

namespace mvc\model;
require_once './../../php-redmine-api-master/lib/autoload.php';

class OperationRedmineData 
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
			array_push($issue, $value);
		}
		foreach ($issueClosed1['issues'] as $issueClosed) {
			array_push($issue, $issueClosed);
		}
		foreach ($issueClosed2['issues'] as $issueClosed) {
			array_push($issue, $issueClosed);
		}

		foreach ($issue as $issue) {
			$temp = array();
			$temp['issue_id'] 		= $issue['id'];
			$temp['project_name'] 	= $issue['project']['name'];
			$temp['assigned_to'] 	= $issue['assigned_to']['name'];
			$temp['spent_time'] 	= 0;
			if (isset($issue['estimated_hours'])) {
				$temp['estimated_hours'] 	= $issue['estimated_hours'];
			} else {
				$temp['estimated_hours'] 	= 0;
			}
			if (isset($issue['parent'])) {
				$temp['parent_id'] 	= $issue['parent']['id'];
			}
			if (isset($issue['category'])) {
				$temp['category_id'] 	= $issue['category']['id'];
				$temp['category_name'] 	= $issue['category']['name'];
			} else {
				$temp['category_id'] 	= 0;
				$temp['category_name'] 	= '[none]';
			}
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
			array_push($result, $temp);
		}
		if (count($timeEntries1['time_entries']) == $timeEntryParam1['limit'] || count($timeEntries2['time_entries']) == $timeEntryParam2['limit']) {
            $timeEntryParam1['offset'] += $timeEntryParam1['limit'];
            $timeEntryParam2['offset'] += $timeEntryParam2['limit'];
            $this->getTimeEntries($timeEntryParam1, $timeEntryParam2, $result);
        }
	}
}