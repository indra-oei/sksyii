<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use app\models\Report;

class ReportController extends XController
{
    public function actionIndex()
    {
        return $this->render('report');
    }

    public function actionGenerateExcelReport()
    {
        $report = new Report();
        $report->transactionDateStart = $this->getParam('transaction_date_start') ? $this->getParam('transaction_date_start') : date("Y-m-d", strtotime("-100 years"));
        $report->transactionDateEnd = $this->getParam('transaction_date_end') ? date('Y-m-d', strtotime("+1 day", strtotime($this->getParam('transaction_date_end')))) : date("Y-m-d", strtotime("+100 years"));
        $report->paymentDateStart = $this->getParam('payment_date_start') ? $this->getParam('payment_date_start') : date("Y-m-d", strtotime("-100 years"));
        $report->paymentDateEnd = $this->getParam('payment_date_end') ? date('Y-m-d', strtotime("+1 day", strtotime($this->getParam('payment_date_end')))) : date("Y-m-d", strtotime("+100 years"));
        $transactionReport = $report->getTransactionReport()['data'];

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $headerStyle = [
            'font' => [
                'bold' => true
            ]
        ];

        $tableHeaderStyle = [
            'font' => [
                'bold' => true
            ],
        ];

        $tableStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];

        // Header
        $activeWorksheet->setCellValue('A1', 'TRANSACTION REPORT');
        $activeWorksheet->setCellValue('A2', 'Transaction Date: ');
        $activeWorksheet->setCellValue('A3', 'Payment Date: ');
        $activeWorksheet->setCellValue('A4', 'Printed By: ');

        // Header Value
        $activeWorksheet->setCellValue('B2', $this->getParam('transaction_date_start') ? date("d/m/Y", strtotime($this->getParam('transaction_date_start'))) . ' - ' . date("d/m/Y", strtotime($this->getParam('transaction_date_end'))) : '-');
        $activeWorksheet->setCellValue('B3', $this->getParam('payment_date_start') ? date("d/m/Y", strtotime($this->getParam('payment_date_start'))) . ' - ' . date("d/m/Y", strtotime($this->getParam('payment_date_end'))) : '-');
        $activeWorksheet->setCellValue('B4', Yii::$app->session->get('contactName'));

        $activeWorksheet->getStyle('A1:A4')->applyFromArray($headerStyle);
        $activeWorksheet->getColumnDimension('A')->setWidth('22');
        $activeWorksheet->getColumnDimension('B')->setWidth('22');
        $activeWorksheet->getColumnDimension('C')->setWidth('22');
        $activeWorksheet->getColumnDimension('D')->setWidth('22');
        $activeWorksheet->getColumnDimension('E')->setWidth('22');
        $activeWorksheet->getColumnDimension('F')->setWidth('22');
        $activeWorksheet->getColumnDimension('G')->setWidth('22');

        // Table Header
        $activeWorksheet->setCellValue('A6', 'Order No');
        $activeWorksheet->setCellValue('B6', 'Transaction Date');
        $activeWorksheet->setCellValue('C6', 'Payment Date');
        $activeWorksheet->setCellValue('D6', 'Total Amount');
        $activeWorksheet->setCellValue('E6', 'Service Charge');
        $activeWorksheet->setCellValue('F6', 'Tax Amount');
        $activeWorksheet->setCellValue('G6', 'Grand Total');
        $activeWorksheet->getStyle('A6:G6')->applyFromArray($tableHeaderStyle);

        $tableRowStart = 7;
        $currentTableRow = $tableRowStart;

        // Render Table Content
        for ($i = 0; $i < COUNT($transactionReport); $i++)
        {
            $activeWorksheet->setCellValue('A' . $currentTableRow, $transactionReport[$i]['ORDER_NO']);
            $activeWorksheet->setCellValue('B' . $currentTableRow, $transactionReport[$i]['TRANSACTION_DATE']);
            $activeWorksheet->setCellValue('C' . $currentTableRow, $transactionReport[$i]['PAYMENT_DATE']);
            $activeWorksheet->setCellValue('D' . $currentTableRow, $transactionReport[$i]['TOTAL']);
            $activeWorksheet->setCellValue('E' . $currentTableRow, $transactionReport[$i]['SERVICE_AMOUNT']);
            $activeWorksheet->setCellValue('F' . $currentTableRow, $transactionReport[$i]['TAX_AMOUNT']);
            $activeWorksheet->setCellValue('G' . $currentTableRow, $transactionReport[$i]['GRAND_TOTAL']);

            $currentTableRow += 1;
            $tableSumRow = $currentTableRow;
        }

        $tableRowEnd = $tableSumRow - 1;
        
        // Render Total Row
        $activeWorksheet->setCellValue('A'.$tableSumRow, 'Total');
        $activeWorksheet->mergeCells('A'.$tableSumRow.':C'.$tableSumRow)->getStyle('A'.$tableSumRow.':C'.$tableSumRow)->getFont()->setBold(true);

        // Calculate Total
        $activeWorksheet->setCellValue('D'.$tableSumRow, '=SUM(D'.$tableRowStart.':D'.$tableRowEnd.')');
        $activeWorksheet->setCellValue('E'.$tableSumRow, '=SUM(E'.$tableRowStart.':E'.$tableRowEnd.')');
        $activeWorksheet->setCellValue('F'.$tableSumRow, '=SUM(F'.$tableRowStart.':F'.$tableRowEnd.')');
        $activeWorksheet->setCellValue('G'.$tableSumRow, '=SUM(G'.$tableRowStart.':G'.$tableRowEnd.')');

        $activeWorksheet->getStyle('A'.($tableRowStart - 1).':G'.$tableSumRow)->applyFromArray($tableStyle);

        $dir = Yii::getAlias('@app') . '/web/tmp/report/';

        dd('../../'.Yii::getAlias('@app') );

        // $xlsx = new Xlsx($spreadsheet);
        // $xlsx->save($dir . 'transaction_report.xlsx');

        // $html = new Html($spreadsheet);
        // $html->save($dir . 'transaction_report.html');

        // exec('C:\wkhtmltopdf-0.12.5-1\bin' . " --quiet --dis able-smart-shrinking --encoding utf-8 --no-outline --margin-top 0 --margin-bottom 0 --margin-left 0 --margin-right 0 --no-outline --dpi 266 --no-stop-slow-scripts \"$dir . transaction_report.html\" \"$dir . transaction_report.pdf\" ");
    }
}