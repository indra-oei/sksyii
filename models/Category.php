<?php

namespace app\models;

use Yii;
use PDO;
use app\extensions\XModel;

class Category extends XModel
{
    public $id;
    public $name;

    public function rules()
    {
        return [
            ['name', 'required', 'on' => ['insert', 'update']],
            ['name', 'string', 'max' => 128, 'on' => ['insert', 'update']]
        ];
    }

    public function getAll()
    {
        $data = NULL;

        $sql = "
            SELECT
                ic.id,
                ic.name
            FROM
                indra_category ic
            WHERE
                ic.owner_id = :ownerId
                AND ic.status = 1
            ORDER BY
                ic.id DESC
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':ownerId', $this->contactId);
        $data = $st->queryAll();

        if ($data) {
            return [
                'errNum' => 1,
                'errStr' => 'Data not found',
                'data' => $data
            ];
        }
        else
        {
            return [
                'errNum' => 0,
                'errStr' => 'Success',
                'data' => $data
            ];
        }
    }

    public function insert()
    {
        $sql = "
            BEGIN
                sp_indra_category_insert
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_name => :name,
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $db = $this->db;
        $st = $db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':name', $this->name);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }

    public function getCategoryById()
    {
        $data = NULL;

        $sql = "
            SELECT
                ic.name
            FROM
                indra_category ic
            WHERE
                ic.id = :id
                AND ic.owner_id = :ownerId
                AND ic.status = 1
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':id', $this->id);
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

    public function update()
    { 
        $sql = "
            BEGIN
                sp_indra_category_update
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_id => :id,
                    in_name => :name,
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
        $st->bindParam(':name', $this->name);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute(); 

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }

    public function delete()
    {
        $sql = "
            BEGIN
                sp_indra_category_delete
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_id => :id,
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
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }
}