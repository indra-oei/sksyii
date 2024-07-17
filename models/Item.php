<?php

namespace app\models;

use Yii;
use PDO;
use app\extensions\XModel;

class Item extends XModel
{
    public $id;
    public $name;
    public $description;
    public $price;
    public $validUntil;
    public $subCategoryId;

    // Ini emang salah cmn biar codenya ga repetitive aja :))
    public $contactId = 1;
    public $ownerId = 1;

    public function rules()
    {
        return [
            ['name', 'required', 'on' => ['insert', 'update']],
            ['name', 'string', 'max' => 128, 'on' => ['insert', 'update']],
            ['description', 'required', 'on' => ['insert', 'update']],
            ['description', 'string', 'max' => 256, 'on' => ['insert', 'update']],
            ['price', 'required', 'on' => ['insert', 'update']],
            ['price', 'number', 'min' => 0, 'max' => 10000000, 'on' => ['insert', 'update']],
            ['validUntil', 'required', 'on' => ['insert', 'update']],
            // ['validUntil', 'required', 'on' => ['insert', 'update']], FORMAT DATE DD/
            ['subCategoryId', 'required', 'on' => ['insert', 'update']]
        ];
    }

    public function getAll()
    {
        $data = NULL;

        $sql = "
            SELECT
                it.id,
                it.name,
                it.description,
                it.price,
                it.valid_until,
                isc.name as sub_category,
                ic.name as category
            FROM
                indra_item it,
                indra_subcategory isc,
                indra_category ic
            WHERE
                it.subcategory_id = isc.id
                AND isc.category_id = ic.id
                AND it.owner_id = :ownerId
                AND it.status = 1
                AND isc.status = 1
                AND ic.status = 1
        ";
        $st = $this->db->createCommand($sql);
        $st->bindParam(':ownerId', $this->contactId);
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
                sp_indra_item_insert
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_subcategory_id => :subCategoryId,
                    in_name => :name,
                    in_description => :description,
                    in_price => :price,
                    in_valid_until => TO_DATE(:validUntil, 'YYYY-MM-DD'),
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $db = $this->db;
        $st = $db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':subCategoryId', $this->subCategoryId);
        $st->bindParam(':name', $this->name);
        $st->bindParam(':description', $this->description);
        $st->bindParam(':price', $this->price);
        $st->bindParam(':validUntil', $this->validUntil);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }

    public function getItemById()
    {
        $data = NULL;

        $sql = "
            SELECT
                it.name,
                it.description,
                it.price,
                it.valid_until,
                it.subcategory_id
            FROM
                indra_item it
            WHERE
                it.id = :id
                AND it.owner_id = :ownerId
                AND it.status = 1
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

    public function getItemByName()
    {
        $data = NULL;

        $sql = "
            SELECT
                it.name,
                it.price
            FROM
                indra_item it
            WHERE
                UPPER(it.name) = UPPER(:name)
                AND it.owner_id = :ownerId
                AND it.status = 1
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':name', $this->name);
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
                sp_indra_item_update
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_id => :id,
                    in_subcategory_id => :subCategoryId,
                    in_name => :name,
                    in_description => :description,
                    in_price => :price,
                    in_valid_until => :validUntil,
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
        $st->bindParam(':subCategoryId', $this->subCategoryId);
        $st->bindParam(':name', $this->name);
        $st->bindParam(':description', $this->description);
        $st->bindParam(':price', $this->price);
        $st->bindParam(':validUntil', $this->validUntil);
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
                sp_indra_item_delete
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