<?php

namespace app\models;

use Yii;
use PDO;
use app\extensions\XModel;

class Order extends XModel
{
    public $id;
    public $orderNo;
    public $customerName;
    public $customerSex;
    public $tableNo;
    public $totalCustomer;
    public $orderStatus;

    public function rules()
    {
        return [
            ['customerName', 'required', 'on' => 'insert'],
            ['customerName', 'string', 'max' => 128, 'on' => 'insert'],
            ['customerSex', 'required', 'on' => 'insert'],
            ['tableNo', 'required', 'on' => 'insert'],
            ['totalCustomer', 'required', 'on' => 'insert'],
            ['orderStatus', 'required', 'on' => 'update'],
        ];
    }

    public function getAll()
    {
        $data = NULL;

        $sql = "
            SELECT
                io.id,
                io.order_no,
                io.customer_name,
                io.customer_sex,
                io.table_no,
                io.total_customer,
                io.transaction_date,
                io.order_status
            FROM
                indra_order io
            WHERE
                io.owner_id = :ownerId
                AND io.status = 1
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':ownerId', $this->ownerId);
        $data = $st->queryAll();

        if ($data) {
            return [
                'errNum' => 0,
                'errStr' => 'Data found',
                'data' => $data
            ];
        }
        else
        {
            return [
                'errNum' => 1,
                'errStr' => 'Data not found',
                'data' => $data
            ];
        }
    }

    public function getOrderByOrderNo()
    {
        $data = NULL;

        $sql = "
            SELECT
                io.id,
                io.order_no,
                io.customer_name,
                io.customer_sex,
                io.table_no,
                io.total_customer,
                io.transaction_date,
                io.order_status
            FROM
                indra_order io
            WHERE
                io.order_no = :orderNo
                AND io.owner_id = :ownerId
                AND io.status = 1
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':orderNo', $this->orderNo);
        $st->bindParam(':ownerId', $this->ownerId);
        $data = $st->queryOne();

        if ($data) {
            return [
                'errNum' => 0,
                'errStr' => 'Data found',
                'data' => $data
            ];
        }
        else
        {
            return [
                'errNum' => 1,
                'errStr' => 'Data not found',
                'data' => $data
            ];
        }
    }

    public function generateTable()
    {
        $sql = "
            SELECT
                fn_indra_generate_table_number() as table_number
            FROM
                DUAL
        ";

        $st = $this->db->createCommand($sql);
        $tableNum = $st->queryScalar();

        return $tableNum;
    }

    public function insert()
    {
        $sql = "
            BEGIN
                sp_indra_order_insert
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    out_order_no => :outOrderNo,
                    in_customer_name => :customerName,
                    in_customer_sex => :customerSex,
                    in_table_no => :tableNo,
                    in_total_customer => :totalCustomer,
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $db = $this->db;
        $st = $db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':outOrderNo', $this->outOrderNo, PDO::PARAM_STR, 6);
        $st->bindParam(':customerName', $this->customerName);
        $st->bindParam(':customerSex', $this->customerSex);
        $st->bindParam(':tableNo', $this->tableNo);
        $st->bindParam(':totalCustomer', $this->totalCustomer);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr,
            'orderNo' => $this->outOrderNo,
        ];
    }

    public function update()
    {
        $sql = "
            BEGIN
                sp_indra_order_update
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_id => :id,
                    in_order_status => :orderStatus,
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $db = $this->db;
        $st = $db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':id', $this->id);
        $st->bindParam(':orderStatus', $this->orderStatus);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }
}