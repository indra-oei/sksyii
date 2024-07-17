<?php

namespace app\models;

use Yii;
use PDO;
use app\extensions\XModel;

class SubCategory extends XModel
{
    public $id;
    public $name;
    public $categoryId;

    public function rules()
    {
        return [
            ['name', 'required', 'on' => ['insert', 'update']],
            ['name', 'string', 'max' => 128, 'on' => ['insert', 'update']],
            ['categoryId', 'required', 'on' => ['insert', 'update']]
        ];
    }

    public function getAll()
    {
        $data = NULL;

        $sql = "
            SELECT
                isc.id,
                isc.name,
                ic.name as category
            FROM
                indra_subcategory isc,
                indra_category ic
            WHERE
                isc.category_id = ic.id
                AND isc.owner_id = :ownerId
                AND isc.status = 1
            ORDER BY
                isc.name, ic.name
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

    public function insert()
    {
        $sql = "
            BEGIN
                sp_indra_subcategory_insert
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_category_id => :categoryId,
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
        $st->bindParam(':categoryId', $this->categoryId);
        $st->bindParam(':name', $this->name);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }

    public function getSubCategoryById()
    {
        $data = NULL;

        $sql = "
            SELECT
                isc.name,
                isc.category_id
            FROM
                indra_subcategory isc
            WHERE
                isc.id = :id
                AND isc.owner_id = :ownerId
                AND isc.status = 1
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':id', $this->id);
        $st->bindParam(':ownerId', $this->contactId);
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
                sp_indra_subcategory_update
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_id => :id,
                    in_name => :name,
                    in_category_id => :categoryId,
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
        $st->bindParam(':categoryId', $this->categoryId);
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
                sp_indra_subcategory_delete
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