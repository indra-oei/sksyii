<?php

namespace app\models;

use Yii;
use PDO;
use app\extensions\XModel;

class User extends XModel
{
    public $name;
    public $email;
    public $password;
    public $reconfirm;

    // Ini emang salah cmn biar codenya ga repetitive aja :))
    public $contactId = 1;
    public $ownerId = 1;

    public function rules()
    {
        return [
            ['name', 'required', 'on' => ['insert', 'update']],
            ['name', 'string', 'max' => 128, 'on' => ['insert', 'update']],
            ['email', 'required', 'on' => ['insert', 'update']],
            ['email', 'email', 'on' => ['insert', 'update']],
            ['email', 'string', 'max' => 256, 'on' => ['insert', 'update']],
            ['password', 'required', 'on' => 'insert'],
            ['password', 'compare', 'compareAttribute' => 'reconfirm', 'operator' => '==', 'on' => 'insert']
        ];
    }

    public function getAll()
    {
        $data = NULL;

        $sql = "
            SELECT
                iu.name,
                iu.email
            FROM
                indra_user iu
            WHERE
                iu.owner_id = :ownerId
                AND iu.status = 1
            ORDER BY
                iu.id DESC
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
        $password = password_hash($this->password, PASSWORD_BCRYPT);

        $sql = "
            BEGIN
                sp_indra_user_insert
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_name => :name,
                    in_email => :email,
                    in_password => :password,
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
        $st->bindParam(':email', $this->email);
        $st->bindParam(':password', $password);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }

    public function getUserByEmail()
    {
        $data = NULL;

        $sql = "
            SELECT
                iu.name,
                iu.email
            FROM
                indra_user iu
            WHERE
                UPPER(iu.email) = UPPER(:email)
                AND iu.owner_id = :ownerId
                AND iu.status = 1
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':email', $this->email);
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
        $password = "";

        $sql = "
            SELECT
                iu.id
            FROM
                indra_user iu
            WHERE
                UPPER(iu.email) = UPPER(:email)
                AND iu.owner_id = :ownerId
                AND iu.status = 1
        ";
        $st = $this->db->createCommand($sql);
        $st->bindParam(':email', $this->email);
        $st->bindParam(':ownerId', $this->contactId);
        $id = $st->queryScalar();

        if ($this->password)
        {
            $password = password_hash($this->password, PASSWORD_BCRYPT);
        }
        else
        {
            $sql = "
                SELECT
                    iu.password
                FROM
                    indra_user iu
                WHERE
                    UPPER(iu.email) = UPPER(:email)
                    AND iu.owner_id = :ownerId
                    AND iu.status = 1
            ";

            $st = $this->db->createCommand($sql);
            $st->bindParam(':email', $this->email);
            $st->bindParam(':ownerId', $this->contactId);
            $password = $st->queryScalar();
        }

        $sql = "
            BEGIN
                sp_indra_user_update
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_id => :id,
                    in_name => :name,
                    in_email => :email,
                    in_password => :password,
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $db = $this->db;
        $st = $db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':id', $id);
        $st->bindParam(':name', $this->name);
        $st->bindParam(':email', $this->email);
        $st->bindParam(':password', $password);
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
            SELECT
                iu.id
            FROM
                indra_user iu
            WHERE
                UPPER(iu.email) = UPPER(:email)
                AND iu.owner_id = :ownerId
                AND iu.status = 1
        ";
        $st = $this->db->createCommand($sql);
        $st->bindParam(':email', $this->email);
        $st->bindParam(':ownerId', $this->contactId);
        $id = $st->queryScalar();

        $sql = "
            BEGIN
                sp_indra_user_delete
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
        $st->bindParam(':id', $id);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr
        ];
    }
}