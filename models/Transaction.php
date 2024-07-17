<?php

namespace app\models;

use Yii;
use PDO;
use app\extensions\XModel;

class Transaction extends XModel
{
    public $id;
    public $orderId;
    public $total;
    public $serviceAmount;
    public $taxAmount;
    public $grandTotal;

    public function rules()
    {
        return [
            ['orderId', 'required', 'on' => ['insert', 'update', 'delete']],
            ['total', 'required', 'on' => 'insert'],
            ['serviceAmount', 'required', 'on' => 'insert'],
            ['taxAmount', 'required', 'on' => 'insert'],
            ['grandTotal', 'required', 'on' => 'insert']
        ];
    }

    public function insert()
    {
        $sql = "
            BEGIN
                sp_indra_transaction_insert
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_order_id => :orderId,
                    in_total => :total,
                    in_service_amount => :serviceAmount,
                    in_tax_amount => :taxAmount,
                    in_grand_total => :grandTotal,
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $db = $this->db;
        $st = $db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':orderId', $this->orderId);
        $st->bindParam(':total', $this->total);
        $st->bindParam(':serviceAmount', $this->serviceAmount);
        $st->bindParam(':taxAmount', $this->taxAmount);
        $st->bindParam(':grandTotal', $this->grandTotal);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }

    public function update()
    {
        $sql = "
            BEGIN
                sp_indra_transaction_update
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_order_id => :orderId,
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $db = $this->db;
        $st = $db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':orderId', $this->orderId);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }

    public function destroy()
    {
        $sql = "
            BEGIN
                sp_indra_transaction_delete
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_order_id => :orderId,
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':orderId', $this->orderId);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }
}