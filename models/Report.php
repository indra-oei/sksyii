<?php

namespace app\models;

use Yii;
use PDO;
use app\extensions\XModel;

class Report extends XModel
{
    public $transactionDateStart;
    public $transactionDateEnd;
    public $paymentDateStart;
    public $paymentDateEnd;

    public function rules()
    {
        return [
            ['transactionDateStart', 'required', 'on' => ['insert', 'update', 'delete']],
            ['transactionDateEnd', 'required', 'on' => 'insert'],
            ['serviceAmount', 'required', 'on' => 'insert'],
            ['taxAmount', 'required', 'on' => 'insert'],
            ['grandTotal', 'required', 'on' => 'insert']
        ];
    }

    public function getTransactionReport()
    {
        $sql = "
            SELECT
                io.order_no,
                TO_CHAR(io.transaction_date, 'DD/MM/YYYY') as transaction_date,
                TO_CHAR(it.payment_date, 'DD/MM/YYYY') as payment_date,
                it.total,
                it.service_amount,
                it.tax_amount,
                it.grand_total
            FROM
                indra_transaction it,
                indra_order io
            WHERE
                it.order_id = io.id
                AND io.transaction_date >= TO_DATE(:transactionDateStart, 'YYYY-MM-DD')
                AND io.transaction_date < TO_DATE(:transactionDateEnd, 'YYYY-MM-DD')
                AND it.payment_date >= TO_DATE(:paymentDateStart, 'YYYY-MM-DD')
                AND it.payment_date < TO_DATE(:paymentDateEnd, 'YYYY-MM-DD')
                AND io.order_status = 'Pay'
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':transactionDateStart', $this->transactionDateStart);
        $st->bindParam(':transactionDateEnd', $this->transactionDateEnd);
        $st->bindParam(':paymentDateStart', $this->paymentDateStart);
        $st->bindParam(':paymentDateEnd', $this->paymentDateEnd);
        $data = $st->queryAll();

        return [
            'data' => $data
        ];
    }
}