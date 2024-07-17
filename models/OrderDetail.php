<?php

namespace app\models;

use Yii;
use PDO;
use app\extensions\XModel;

class OrderDetail extends XModel
{
    public $id;
    public $orderNo;
    public $orderId;
    public $itemName;
    public $itemQuantity;
    public $itemPrice;
    public $subtotal;
    public $notes;

    public function rules()
    {
        return [
            ['orderId', 'required', 'on' => ['update', 'delete']],
            ['itemName', 'required', 'on' => 'insert'],
            ['itemName', 'string', 'max' => 128, 'on' => 'insert'],
            ['itemQuantity', 'required', 'on' => 'insert'],
            ['itemQuantity', 'number', 'min' => 1, 'on' => 'insert']
        ];
    }

    public function getAll()
    {
        $data = NULL;

        $sql = "
            SELECT
                iod.id,
                io.order_no,
                iod.item_name,
                iod.item_price,
                iod.item_quantity,
                iod.subtotal,
                iod.notes
            FROM
                indra_order_detail iod,
                indra_order io
            WHERE
                iod.order_id = io.id
                AND io.order_no = :orderNo
                AND iod.owner_id = :ownerId
                AND (iod.status = 1 OR io.order_status = 'Pay')
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':orderNo', $this->orderNo);
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

    public function insert()
    {
        $sql = "
            SELECT
                io.id
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
        $orderId = $st->queryScalar();

        $sql = "
            BEGIN
                sp_indra_order_detail_insert
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_order_id => :orderId,
                    in_item_name => :itemName,
                    in_item_quantity => :itemQuantity,
                    in_item_price => :itemPrice,
                    in_subtotal => :subtotal,
                    in_notes => :notes,
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':orderId', $orderId);
        $st->bindParam(':itemName', $this->itemName);
        $st->bindParam(':itemQuantity', $this->itemQuantity);
        $st->bindParam(':itemPrice', $this->itemPrice);
        $st->bindParam(':subtotal', $this->subtotal);
        $st->bindParam(':notes', $this->notes);
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
                sp_indra_order_detail_update
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
            SELECT
                io.id
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
        $orderId = $st->queryScalar();

        $sql = "
            BEGIN
                sp_indra_order_detail_delete
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
        $st->bindParam(':orderId', $orderId);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }
}