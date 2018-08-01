<?php

require_once './../../PHPExcel/PHPExcel.php';
require_once './../../PHPExcel/PHPExcel/IOFactory.php';

class MonthlyReport 
{

	function __construct()
	{
	}

	public function getReportData($issues, $timeEntries)
	{
		$result = array();
		foreach ($timeEntries as $timeEntry) {
			foreach ($issues as $issue) {
				if ($timeEntry['issue_id'] == $issue['issue_id'] && $timeEntry['project_name'] == $issue['project_name'] && $timeEntry['spent_time'] != 0) {
					$temp = array_merge($issue,$timeEntry);
					array_push($result, $temp);
				}
			}
		}
		return $result;	
	}

	public function getReportDataByUser($data, &$reportData)
	{
		foreach ($data as $data) {
			$spentDay = date('j', strtotime($data['spent_on']));
			if ($reportData[$data['user_name']][$spentDay] != null) {
				$reportData[$data['user_name']][$spentDay] .= "\r\n";
			}
			$reportData[$data['user_name']][$spentDay] .= $data['subject'];
		}
	}

	public function formatReportData(&$reportData)
	{
		$reportData['ThinhPQ'] = $reportData['Pham Thinh'];
		$reportData['HuongLH'] = $reportData['QA HuongLH6380'];
		$reportData['ChinhLV'] = $reportData['Dev Chinhlv6812'];
		$reportData['HienTQ']  = $reportData['Dev HienTQ-6724'];
		unset($reportData['Pham Thinh'], $reportData['QA HuongLH6380'], $reportData['Dev Chinhlv6812'], $reportData['Dev HienTQ-6724']);
	}

	public function formatExcelFile($date, $objPHPExcel)
	{
		$day   = date('j', strtotime($date));
		$month = date('m', strtotime($date));
		$year  = date('Y', strtotime($date));
		$objPHPExcel->setActiveSheetIndexByName('集計');
		$objPHPSheet = $objPHPExcel->getActiveSheet();
		$objPHPSheet->setCellValue('F1', $date);
		$objPHPSheet->setCellValue('A4', $year . "年" . $month . "月度 作業報告書（兼納品書）");
	}

	public function exportReportFile($startDate, $dueDate, $data)
	{
		// Create new PHPExcel object
		$objPHPExcel 	= PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel 	= $objPHPExcel->load('./../../template/template.xlsx');
		$startDate 		= date("Ymd", strtotime($startDate));
		$dueDate 		= date("Ymd", strtotime($dueDate));
		$startDay 		= date('j', strtotime($startDate));
		$dueDay 		= date('j', strtotime($dueDate));
		$date 			= date("n/j/Y", strtotime($dueDate));

		$fileName 	 	= 'Co-well 作業報告書_'. date("Ym", strtotime($startDate)) .'.xlsx';

		// Unset ticket not used
    	$userArr 	  = array('Pham Thinh', 'QA HuongLH6380', 'Dev Chinhlv6812', 'Dev HienTQ-6724');
    	$created_on = array();
    	$project_name  = array();
    	foreach ($data as $key => $value) {
    		if ($value['spent_time'] == 0 || !in_array ($value['user_name'], $userArr)) {
    			unset($data[$key]);
    		} else {
			    $created_on[$key]  = $value['created_on'];
			    $project_name[$key] 	= $value['project_name'];
			}
		}
		array_multisort($created_on, SORT_DESC, $project_name, SORT_ASC, $data);

		// Create report data
		$reportData = array();
		foreach ($userArr as $user) {
			for ($i = $startDay; $i <= $dueDay; $i++) {
				$reportData[$user][$i] = '';
			}
		}

		$this->getReportDataByUser($data, $reportData);
		$this->formatReportData($reportData);
		$this->formatExcelFile($date, $objPHPExcel);
		foreach (array_keys($reportData) as $user) {
			$startRow = 12;
			$objPHPExcel->setActiveSheetIndexByName($user);
			$objPHPSheet = $objPHPExcel->getActiveSheet();
			for ($i = $startDay; $i <= $dueDay; $i ++) {
				$row = $startRow + $i;
				$objPHPSheet->setCellValue('H' . $row, $reportData[$user][$i]);
				$objPHPSheet->getStyle('H' . $row)->getAlignment()->setWrapText(true);
				$objPHPSheet->getRowDimension($row)->setRowHeight(-1);
				$objPHPSheet->mergeCells('H'. $row .':I' . $row);
			}
			$objPHPSheet->getColumnDimension('H')->setWidth(50);
			if ($dueDay < 31) {
				$delRowNum = 31 - $dueDay;
				for ($i = 1; $i <= $delRowNum; $i ++) {
					$objPHPSheet->removeRow($dueDay + 13);
				}
			}
		}

	    header('content-type:application/vnd.ms-excel;charset=UTF-8');
		header('Content-Disposition: attachment;filename="' . $fileName . '"');
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
