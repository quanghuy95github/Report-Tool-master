<?php

require_once './../../PHPExcel/PHPExcel.php';

class ProductivityReport 
{

	function __construct()
	{
	}

	public function getReportData($startDate, $dueDate, $issues, $timeEntries)
	{
		$result = array();
		foreach ($issues as $issue) {
			if (!isset($issue['parent_id']) && $issue['tracker_id'] == 2) {
				$parentId 		= $issue['issue_id'];
				$childIssues	= array_filter($issues, function ($issues) use ($parentId) {
									if (isset($issues['parent_id'])) {
										return $issues['parent_id'] == $parentId;
									} else {
										return false;
									}
								});
				array_push($childIssues, $issue);
				foreach ($childIssues as $childIssue) {
					foreach ($timeEntries as $timeEntry) {
						if ($timeEntry['issue_id'] == $childIssue['issue_id']) {
							$issue['spent_time'] += $timeEntry['spent_time'];
						}
					}
				}
				if ((strtotime($startDate) <= strtotime($issue['actual_end_date']) && strtotime($dueDate) >= strtotime($issue['actual_end_date'])) || (empty($issue['actual_end_date']) && $issue['status'] != 'Closed')) {
					array_push($result, $issue);
				}
			}
		}
		return $result;	
	}

	public function buildTableData($objPHPSheet, $startRow, $title, $data, $status = '', $type)
	{
		$objPHPSheet->getStyle('B' . $startRow . ':K' . $startRow)
						->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()
						->setRGB('E2EFD9');
		$style = array('font' => array('size' => 10,'bold' => true, 'name'  => 'Arial'));
		$objPHPSheet->getStyle('B' . $startRow . ':K' . $startRow)->applyFromArray($style);
		$objPHPSheet->getStyle('B' . $startRow . ':K' . $startRow)->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		);
		$objPHPSheet->getRowDimension($startRow)->setRowHeight(22.5);
		$titleRow = $startRow - 1;
    	$objPHPSheet->setCellValue('B' . $titleRow, $title);
    	$objPHPSheet->setCellValue('B' . $startRow,"項番");
    	$objPHPSheet->setCellValue('C' . $startRow,"作業種別");
    	$objPHPSheet->setCellValue('D' . $startRow,"案件");
    	$objPHPSheet->setCellValue('E' . $startRow,"初期見積（h）");
    	$objPHPSheet->setCellValue('F' . $startRow,"DH様対応工数");
    	$objPHPSheet->setCellValue('G' . $startRow,"CW作業初期");
    	$objPHPSheet->setCellValue('H' . $startRow,"実作業工数");
    	$objPHPSheet->setCellValue('I' . $startRow,"作業時間差異");
    	$objPHPSheet->setCellValue('J' . $startRow,"終了日");
    	$objPHPSheet->setCellValue('K' . $startRow,"備考");

    	usort($data, function($a, $b) {
		    return strtotime($a['actual_end_date']) - strtotime($b['actual_end_date']);
		});
    	$stt = 1;
    	$categoryArr = array();
    	if ($status == 'Closed') {
	    	foreach ($data as $key => $value) {
	    		if ($value['status'] == 'Closed') {
		    		$row = $stt + $startRow;
		    		if (!empty($value['actual_end_date'])) {
		    			$actual_end_date = date("Y/m/d", strtotime($value['actual_end_date']));
		    		} else {
		    			$actual_end_date = '';
		    		}
		    		$objPHPSheet->setCellValue('B' . $row, $stt);
			    	$objPHPSheet->setCellValue('C' . $row, $value['subject']);
			    	$objPHPSheet->setCellValue('D' . $row, 'SCS');
		    		$objPHPSheet->setCellValue('E' . $row, $value['estimated_hours']);
		    		if ($type) {
			    		$objPHPSheet->setCellValue('F' . $row, 0);
			    		$objPHPSheet->setCellValue('G' . $row, "=E" . $row . "-F" . $row);
			    		$objPHPSheet->setCellValue('H' . $row, $value['spent_time']);
			    		$objPHPSheet->setCellValue('I' . $row, "=G" . $row . "-H" . $row);
			    		$objPHPSheet->setCellValue('J' . $row, $actual_end_date);
			    		$objPHPSheet->setCellValue('K' . $row, '');
		    		}
		    		$stt ++;
		    	}
	    	}
	    } else {
	    	foreach ($data as $key => $value) {
	    		if ($value['status'] != 'Closed') {
		    		$row = $stt + $startRow;
		    		if (!empty($value['actual_end_date'])) {
		    			$actual_end_date = date("Y/m/d", strtotime($value['actual_end_date']));
		    		} else {
		    			$actual_end_date = '';
		    		}
		    		$objPHPSheet->setCellValue('B' . $row, $stt);
			    	$objPHPSheet->setCellValue('C' . $row, $value['subject']);
			    	$objPHPSheet->setCellValue('D' . $row, 'SCS');
		    		$objPHPSheet->setCellValue('E' . $row, $value['estimated_hours']);
		    		if ($type) {
			    		$objPHPSheet->setCellValue('F' . $row, 0);
			    		$objPHPSheet->setCellValue('G' . $row, "=E" . $row . "-F" . $row);
			    		$objPHPSheet->setCellValue('H' . $row, $value['spent_time']);
			    		$objPHPSheet->setCellValue('I' . $row, "=G" . $row . "-H" . $row);
			    		$objPHPSheet->setCellValue('J' . $row, $actual_end_date);
			    		$objPHPSheet->setCellValue('K' . $row, '');
		    		}
		    		$stt ++;
		    	}
	    	}
	    }

    	$firstRow 	= $startRow + 1;
    	$lastRow 	= $stt + $startRow -1;
    	$totalRow1 	= $lastRow + 1;
    	$totalRow2 	= $lastRow + 2;
    	$objPHPSheet->setCellValue('D' . $totalRow1 , "合計");
    	$objPHPSheet->setCellValue('E' . $totalRow1 , "=SUM(E" . $firstRow. ":E". $lastRow .")");
    	$objPHPSheet->setCellValue('D' . $totalRow2 , "人月");
    	$objPHPSheet->setCellValue('E' . $totalRow2 , "=E" . $totalRow1 . "/160");
    	if ($type) {
	    	$objPHPSheet->setCellValue('F' . $totalRow1 , "=SUM(F" . $firstRow. ":F". $lastRow .")");
	    	$objPHPSheet->setCellValue('G' . $totalRow1 , "=SUM(G" . $firstRow. ":G". $lastRow .")");
	    	$objPHPSheet->setCellValue('H' . $totalRow1 , "=SUM(H" . $firstRow. ":H". $lastRow .")");
	    	$objPHPSheet->setCellValue('I' . $totalRow1 , "=SUM(I" . $firstRow. ":I". $lastRow .")");
	    	$objPHPSheet->setCellValue('J' . $totalRow1 , "");
	    	$objPHPSheet->setCellValue('K' . $totalRow1 , "");
	    	$objPHPSheet->setCellValue('G' . $totalRow2 , "=G" . $totalRow1 . "/160");
    	}
    	$objPHPSheet->getStyle("B" . $firstRow. ":B" . $lastRow)->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		);
		$objPHPSheet->getStyle("B" . $startRow. ":K" . $lastRow)->applyFromArray(
		    array(
		        'borders' => array(
		            'allborders' => array(
		                'style' => PHPExcel_Style_Border::BORDER_THIN,
		                'color' => array('rgb' => '000000')
		            )
		        )
		    )
		);

		$style = array('font' => array('size' => 10, 'name'  => 'Arial'));
		$objPHPSheet->getStyle("D" . $totalRow1 . ":K" . $totalRow2)->applyFromArray($style);
		$objPHPSheet->getStyle("D" . $firstRow. ":K" . $totalRow2)->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
		);
		$objPHPSheet->getStyle("D" . $totalRow1 . ":K" . $totalRow2)->applyFromArray(
		    array(
		        'borders' => array(
		            'allborders' => array(
		                'style' => PHPExcel_Style_Border::BORDER_THIN,
		                'color' => array('rgb' => '000000')
		            )
		        )
		    )
		);
		$objPHPSheet->getStyle("E" . $firstRow. ":I" . $totalRow2)->getNumberFormat()->setFormatCode('0.00');
		return $totalRow2;
	}

	public function exportReportFile($startDate, $dueDate, $data)
	{
		// Create new PHPExcel object
		$objPHPExcel 	= new PHPExcel();
		$startDate 		= date("Ymd", strtotime($startDate));
		$dueDate 		= date("Ymd", strtotime($dueDate));
		$fileName 	 	= '生産性_'. date("Ym", strtotime($startDate)) .'_DH様.xlsx';

		// Read your Excel workbook
		$objPHPSheet = $objPHPExcel->getActiveSheet();
		$objPHPSheet->setTitle('生産性' . $startDate . '-' . $dueDate);
		$objPHPSheet->setCellValue('B1',"Co-well 生産性報告");
    	$objPHPSheet->setCellValue('B2',"集計期間：" . date("Y-m-d", strtotime($startDate)) . " 〜 " . date("Y-m-d", strtotime($dueDate)));
		$objPHPSheet->getColumnDimension('A')->setWidth(3);
		$objPHPSheet->getColumnDimension('B')->setWidth(7);
		$objPHPSheet->getColumnDimension('C')->setWidth(65);
		$objPHPSheet->getColumnDimension('D')->setWidth(15);
		$objPHPSheet->getColumnDimension('E')->setWidth(15);
		$objPHPSheet->getColumnDimension('F')->setWidth(15);
		$objPHPSheet->getColumnDimension('G')->setWidth(15);
		$objPHPSheet->getColumnDimension('H')->setWidth(15);
		$objPHPSheet->getColumnDimension('I')->setWidth(15);
		$objPHPSheet->getColumnDimension('J')->setWidth(15);
		$objPHPSheet->getColumnDimension('K')->setWidth(40);
		
		$endRow = $this->buildTableData($objPHPSheet, 5, '■今週完了チケット', $data, 'Closed', 1);
		$endRow = $this->buildTableData($objPHPSheet, $endRow + 3, '■次週以降完了予定チケット及びタスク', $data, '', 0);
		$style = array('font' => array('size' => 10, 'name'  => 'Arial'));
		$objPHPSheet->getStyle("A1:K" . $endRow)->applyFromArray($style);

	    header('content-type:application/csv;charset=UTF-8');
		header('Content-Disposition: attachment;filename="' . $fileName . '"');
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
