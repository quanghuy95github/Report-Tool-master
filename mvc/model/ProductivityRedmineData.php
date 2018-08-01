<?php

namespace mvc\model;
require_once './../../php-redmine-api-master/lib/autoload.php';

class ProductivityRedmineData 
{

	function __construct($redmine_url, $key)
	{
		$this->client = new \Redmine\Client($redmine_url, $key);
	}

	public function getIssue($issueParam, $issueClosedParam, &$result)
	{
		$issues = array();
		$issue1 = $this->client->issue->all($issueParam);
		$issueClosed1 = $this->client->issue->all($issueClosedParam);
		foreach ($issue1['issues'] as $value) {
			array_push($issues, $value);
		}
		foreach ($issueClosed1['issues'] as $issueClosed) {
			array_push($issues, $issueClosed);
		}

		foreach ($issues as $issue) {
			$temp = array();
			$temp['issue_id'] 			= $issue['id'];
			$temp['project_name'] 		= $issue['project']['name'];
			$temp['assigned_to'] 		= $issue['assigned_to']['name'];
			$temp['status'] 			= $issue['status']['name'];
			$temp['subject'] 			= $issue['subject'];
			$temp['tracker_id']			= $issue['tracker']['id'];
			$temp['spent_time'] 		= 0;
			$temp['actual_end_date'] 	= $issue['custom_fields'][1]['value'];
			foreach ($issue['custom_fields'] as $custom_fields) {
				if ($custom_fields['name'] == 'Actual end date') {
					$temp['actual_end_date'] 	= $custom_fields['value'];
				}
			}
			if (isset($issue['estimated_hours'])) {
				$temp['estimated_hours'] 	= $issue['estimated_hours'];
			} else {
				$temp['estimated_hours'] 	= 0;
			}
			if (isset($issue['parent'])) {
				$temp['parent_id'] 	= $issue['parent']['id'];
			}
			array_push($result, $temp);
		}
		if (count($issue1['issues']) == $issueParam['limit'] || count($issueClosed1['issues']) == $issueClosedParam['limit']) {
            $issueParam['offset'] += $issueParam['limit'];
            $issueClosedParam['offset'] += $issueClosedParam['limit'];
            $this->getIssue($issueParam, $issueClosedParam, $result);
        }
	}

	public function getTimeEntries($timeEntryParam, &$result)
	{
		$timeEntries 		= array();
		$timeEntriesData 	= $this->client->time_entry->all($timeEntryParam);
		$timeEntries 		= $timeEntriesData['time_entries'];
		foreach ($timeEntries as $time_entry) {
			$temp = array();
			$temp['project_name'] 	= $time_entry['project']['name'];
			$temp['issue_id']	 	= $time_entry['issue']['id'];
			$temp['user_id'] 		= $time_entry['user']['id'];
			$temp['user_name'] 		= $time_entry['user']['name'];
			$temp['spent_time'] 	= $time_entry['hours'];
			array_push($result, $temp);
		}
		if (count($timeEntriesData['time_entries']) == $timeEntryParam['limit']) {
            $timeEntryParam['offset'] += $timeEntryParam['limit'];
            $this->getTimeEntries($timeEntryParam, $result);
        }
	}
}