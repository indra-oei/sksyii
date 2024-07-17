<?php

namespace app\models;

use Yii;
use PDO;
use app\extensions\XModel;

class Login extends XModel
{
    public $email;
    public $password;

    public function rules()
    {
        return [
            [['email', 'password'], 'required', 'on' => 'login', 'message' => 'Tolong koreksi form']
        ];
    }

    public function login()
    {
        $sql = "
            SELECT
                u.id,
                u.name,
                u.email,
                u.password,
                u.owner_id
            FROM
                indra_user u
            WHERE
                UPPER(u.email) = UPPER(:email)
                AND u.status = 1
        ";
        
        $db = $this->db;
        $st = $db->createCommand($sql);
        $st->bindParam(':email', $this->email);
        $result = $st->queryOne();

        if (!$result)
        {
            return [
                'errNum' => 1,
                'errStr' => 'User not found'
            ];
        }
        
        $hash = $result['PASSWORD'];

        if (!password_verify($this->password, $hash)) 
        {
            return [
                'errNum' => 1,
                'errStr' => 'Login failed. Please check your email and password'
            ];
        }

        $this->contactId = $result['ID'];
        $this->contactName = $result['NAME'];
        $this->ownerId = $result['OWNER_ID'];
        $this->email = $result['EMAIL'];

        $this->saveLoginLogoutHistory();

        return [
            'errNum' => $this->outNum,
            'errStr' => $this->outStr,
            'result' => $result
        ];
    }

    public function saveLoginLogoutHistory($flag = 0)
    {
        $actionType = '';
        $browserVersion = $_SERVER['HTTP_USER_AGENT'];
        $clientIpPublic =  $_SERVER['REMOTE_ADDR'];
        $hostName = $_SERVER['SERVER_ADDR'];

        if ($flag == 0)
        {
            $actionType = 'USER LOGIN';
        }
        else
        {
            $actionType = 'USER LOGOUT';
        }

        $actionDesc = 'NAME : ' . $this->contactName . "\r\n" .
                      'EMAIL : ' . $this->email . "\r\n" .
                      'BROWSER VERSION : ' . $browserVersion . "\r\n" .
                      'CLIENT IP PUBLIC : ' . $clientIpPublic . "\r\n" .
                      'HOST NAME : ' . $hostName;

        $this->openConnection();

        $sql = "
            BEGIN
                sp_peter_history_insert
                (
                    out_num => :outNum,
                    out_str => :outStr,
                    in_action_type => :actionType,
                    in_action_desc => :actionDesc,
                    in_contact_id => :contactId,
                    in_owner_id => :ownerId
                );
            END;
        ";

        $st = $this->db->createCommand($sql);
        $st->bindParam(':outNum', $this->outNum, PDO::PARAM_INT, 3);
        $st->bindParam(':outStr', $this->outStr, PDO::PARAM_STR, 255);
        $st->bindParam(':actionType', $actionType);
        $st->bindParam(':actionDesc', $actionDesc);
        $st->bindParam(':contactId', $this->contactId);
        $st->bindParam(':ownerId', $this->ownerId);
        $st->execute();

        $this->commit();

        return 1;
    }
}