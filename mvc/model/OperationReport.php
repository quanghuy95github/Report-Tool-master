<?php

require_once './../../PHPExcel/PHPExcel.php';

class OperationReport 
{

	function __construct()
	{
	}

	public function getReportData($issues, $timeEntries)
	{
		$result = array();
		foreach ($timeEntries as $timeEntry) {
			foreach ($issues as $issue) {
				if ($timeEntry['issue_id'] == $issue['issue_id']) {
					$temp = array_merge($issue,$timeEntry);
					array_push($result, $temp);
				}
			}
		}
		return $result;	
	}

	public function getReportDataAll($issues, $timeEntries)
	{
		$result = array();
		foreach ($issues as $issue) {
			$temp = $issue;
			foreach ($timeEntries as $timeEntry) {
				if ($timeEntry['issue_id'] == $issue['issue_id']) {
					$temp['spent_time'] += $timeEntry['spent_time'];
				}
			}
			array_push($result, $temp);
		}
		return $result;
	}

	public function getTotalTimeByUser($projectName, $categoryId, $data)
	{
		$result = array(
						'JP Muramatsu' 		=> 0,
						'JP Saito6694' 		=> 0,
						'Pham Thinh'		=> 0,
						'QA HuongLH6380'	=> 0,
						'Dev Chinhlv6812' 	=> 0,
						'Dev HienTQ-6724'	=> 0
						);
		foreach ($data as $key => $value) {
			if ($value['project_name'] == $projectName && $value['category_id'] == $categoryId) {
				switch ($value['user_name']) {
					case 'JP Muramatsu':
						$result['JP Muramatsu'] 	+= $value['spent_time'];
						break;
					case 'JP Saito6694':
						$result['JP Saito6694'] 	+= $value['spent_time'];
						break;
					case 'Pham Thinh':
						$result['Pham Thinh'] 		+= $value['spent_time'];
						break;
					case 'QA HuongLH6380':
						$result['QA HuongLH6380'] 	+= $value['spent_time'];
						break;
					case 'Dev Chinhlv6812':
						$result['Dev Chinhlv6812'] 	+= $value['spent_time'];
						break;
					case 'Dev HienTQ-6724':
						$result['Dev HienTQ-6724'] 	+= $value['spent_time'];
						break;
					default:
						# code...
						break;
				}
			}
		}
		return $result;
	}

	public function getEstimatedTime($projectName, $categoryId, $data, $userArr)
	{
		$result = 0;
		foreach ($data as $key => $value) {
			if ($value['project_name'] == $projectName && $value['category_id'] == $categoryId && in_array($value['assigned_to'], $userArr) && !isset($value['parent_id'])) {
				$result += $value['estimated_hours'];
			}
		}
		return $result;
	}

	public function getSpentTime($projectName, $categoryId, $data, $userArr)
	{
		$result = 0;
		foreach ($data as $key => $value) {
			if ($value['project_name'] == $projectName && $value['category_id'] == $categoryId && in_array($value['assigned_to'], $userArr)) {
				$result += $value['spent_time'];
			}
		}
		return $result;
	}

	public function exportReportFile($startDate, $dueDate, $data, $issues)
	{
		// Create new PHPExcel object
		$objPHPExcel 	= new PHPExcel();
		$startDate 		= date("Ymd", strtotime($startDate));
		$dueDate 		= date("Ymd", strtotime($dueDate));
		$fileName 	 	= '稼働報告_'. date("Ym", strtotime($startDate)) .'_DH様.xlsx';

		// Read your Excel workbook
		$objPHPSheet = $objPHPExcel->getActiveSheet();
		$objPHPSheet->setTitle($startDate . '-' . $dueDate);
		$objPHPSheet->getColumnDimension('A')->setWidth(6);
		$objPHPSheet->getColumnDimension('B')->setWidth(30);
		$objPHPSheet->getColumnDimension('D')->setWidth(15);
		$objPHPSheet->getColumnDimension('E')->setWidth(15);
		$objPHPSheet->getColumnDimension('F')->setWidth(15);
		$objPHPSheet->getColumnDimension('G')->setWidth(15);
		$objPHPSheet->getColumnDimension('H')->setWidth(15);
		$objPHPSheet->getColumnDimension('I')->setWidth(15);
		$objPHPSheet->getColumnDimension('J')->setWidth(15);
		$objPHPSheet->getColumnDimension('L')->setWidth(15);
		$objPHPSheet->getColumnDimension('K')->setWidth(15);
		$objPHPSheet->getColumnDimension('L')->setWidth(15);
		$objPHPSheet->getStyle('A4:L4')
						->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()
						->setRGB('E2EFD9');
		$style = array('font' => array('size' => 10,'bold' => true, 'name'  => 'Yu gothic'));
		$objPHPSheet->getStyle('A4:L4')->applyFromArray($style);
		$objPHPSheet->getStyle('A4:L4')->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		);
		$objPHPSheet->freezePane('A5');
		$objPHPSheet->getRowDimension('4')->setRowHeight(30);
    	$objPHPSheet->setCellValue('A1',"Co-well 稼働報告");
    	$objPHPSheet->setCellValue('A2',"集計期間：" . date("Y-m-d", strtotime($startDate)) . "（金）〜 " . date("Y-m-d", strtotime($dueDate)) . "（木）");
    	$objPHPSheet->setCellValue('A4',"項番");
    	$objPHPSheet->setCellValue('B4',"種別/名前");
    	$objPHPSheet->setCellValue('C4',"案件名");
    	$objPHPSheet->setCellValue('D4',"初期見積");
    	$objPHPSheet->setCellValue('E4',"実績");
    	$objPHPSheet->setCellValue('F4',"合計(今週分)");
    	$objPHPSheet->setCellValue('G4',"村松(JP)");
    	$objPHPSheet->setCellValue('H4',"齋藤(JP)");
    	$objPHPSheet->setCellValue('I4',"ThinhPQ(BSE)");
    	$objPHPSheet->setCellValue('J4',"HuongLH(QAL)");
    	$objPHPSheet->setCellValue('K4',"ChinhLV(Dev)");
    	$objPHPSheet->setCellValue('L4',"HienTQ(Dev)");
    	$userArr 	  = array('JP Muramatsu', 'JP Saito6694', 'Pham Thinh', 'QA HuongLH6380', 'Dev Chinhlv6812', 'Dev HienTQ-6724');
    	$project_name = array();
    	$category_name  = array();
    	foreach ($data as $key => $value) {
    		if ($value['spent_time'] == 0 || !in_array ($value['user_name'], $userArr)) {
    			unset($data[$key]);
    		} else {
			    $project_name[$key]  = $value['project_name'];
			    $category_name[$key] 	= $value['category_name'];
			}
		}
		array_multisort($project_name, SORT_ASC, $category_name, SORT_ASC, $data);
    	$stt = 1;
    	$categoryArr = array();
    	foreach ($data as $key => $value) {
    		$projectCategory = $value['project_name'] . '-' . $value['category_name'];
    		if (!in_array ($projectCategory, $categoryArr)) {
	    		$row = $stt + 4;
	    		$projectName = '';
	    		if ($value['project_name'] =='DH-Management') {
	    			$projectName = '全体';
	    		} else if ($value['project_name'] =='DH-SCS') {
	    			$projectName = 'SCS';
	    		}
	    		$spentTime 	= $this->getTotalTimeByUser($value['project_name'], $value['category_id'], $data);
	    		$objPHPSheet->setCellValue('A' . $row, (string)$stt);
	    		$objPHPSheet->setCellValue('B' . $row, $value['category_name']);
	    		$objPHPSheet->setCellValue('C' . $row, $projectName);
	    		$objPHPSheet->setCellValue('D' . $row, $this->getEstimatedTime($value['project_name'], $value['category_id'], $issues, $userArr));
	    		$objPHPSheet->setCellValue('E' . $row, $this->getSpentTime($value['project_name'], $value['category_id'], $issues, $userArr));
	    		$objPHPSheet->setCellValue('F' . $row, "=SUM(G". $row . ":L". $row .")");
	    		$objPHPSheet->setCellValue('G' . $row, $spentTime['JP Muramatsu']);
	    		$objPHPSheet->setCellValue('H' . $row, $spentTime['JP Saito6694']);
	    		$objPHPSheet->setCellValue('I' . $row, $spentTime['Pham Thinh']);
	    		$objPHPSheet->setCellValue('J' . $row, $spentTime['QA HuongLH6380']);
	    		$objPHPSheet->setCellValue('K' . $row, $spentTime['Dev Chinhlv6812']);
	    		$objPHPSheet->setCellValue('L' . $row, $spentTime['Dev HienTQ-6724']);
	    		array_push($categoryArr, $projectCategory);
	    		$stt ++;
	    	}
    	}

    	$lastRow = $stt + 3;
    	$totalRow = $lastRow + 1;
    	$objPHPSheet->setCellValue('C' . $totalRow , "合計");
    	$objPHPSheet->setCellValue('D' . $totalRow , "=SUM(D5:D". $lastRow .")");
    	$objPHPSheet->setCellValue('E' . $totalRow , "=SUM(E5:E". $lastRow .")");
    	$objPHPSheet->setCellValue('F' . $totalRow , "=SUM(F5:F". $lastRow .")");
    	$objPHPSheet->setCellValue('F' . $totalRow , "=SUM(F5:F". $lastRow .")");
    	$objPHPSheet->setCellValue('F' . $totalRow , "=SUM(F5:F". $lastRow .")");
    	$objPHPSheet->setCellValue('G' . $totalRow , "=SUM(G5:G". $lastRow .")");
    	$objPHPSheet->setCellValue('H' . $totalRow , "=SUM(H5:H". $lastRow .")");
    	$objPHPSheet->setCellValue('I' . $totalRow , "=SUM(I5:I". $lastRow .")");
    	$objPHPSheet->setCellValue('J' . $totalRow , "=SUM(J5:J". $lastRow .")");
    	$objPHPSheet->setCellValue('K' . $totalRow , "=SUM(K5:K". $lastRow .")");
    	$objPHPSheet->setCellValue('L' . $totalRow , "=SUM(L5:L". $lastRow .")");

    	$objPHPSheet->getStyle('A4:A' . $lastRow)->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		);
		$objPHPSheet->getStyle("A4:L" . $lastRow)->applyFromArray(
		    array(
		        'borders' => array(
		            'allborders' => array(
		                'style' => PHPExcel_Style_Border::BORDER_THIN,
		                'color' => array('rgb' => '000000')
		            )
		        )
		    )
		);

		$style = array('font' => array('size' => 10, 'name'  => 'Yu gothic'));
		$objPHPSheet->getStyle("A1:L" . $lastRow)->applyFromArray($style);

		$objPHPSheet->getStyle('F5:F' . $lastRow)
						->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()
						->setRGB('DEEAF6');
		$objPHPSheet->getStyle('C'. $totalRow .':L' . $totalRow)
						->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()
						->setRGB('B4C6E7');
		$objPHPSheet->getStyle('C'. $totalRow .':L' . $totalRow)->applyFromArray(
		    array(
		        'borders' => array(
		            'allborders' => array(
		                'style' => PHPExcel_Style_Border::BORDER_THIN,
		                'color' => array('rgb' => '000000')
		            )
		        )
		    )
		);
		$objPHPSheet->getStyle('C'. $totalRow .':L' . $totalRow)->applyFromArray($style);

		$objPHPSheet->getStyle('D5:L' . $totalRow)->getNumberFormat()->setFormatCode('0.00'); 

	    header('content-type:application/csv;charset=UTF-8');
		header('Content-Disposition: attachment;filename="' . $fileName . '"');
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
