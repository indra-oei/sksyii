<?php

namespace app\extensions;

use Yii;
use PDO;
use yii\base\Model;

class XModel extends Model
{
    public $db;
    public $tr;
    public $outNum = 0;
    public $outStr = '';
    public $outOrderNo = '';

    public $email;
    public $contactName;
    public $contactId;
    public $ownerId;

    public function init()
    {
        $this->db = Yii::$app->db;

        $session = Yii::$app->session;
        $this->email = $session->get('email');
        $this->contactName = $session->get('contactName');
        $this->contactId = $session->get('contactId');
        $this->ownerId = $session->get('ownerId');
    }

    public function setDb($db = 'db')
    {
        if ($db == 'dbLive')
        {
            $this->db = Yii::$app->dbLive;
        }
    }

    public function closeDb()
    {
        $this->db->close();
    }

    public function openConnection()
    {
        $this->db->open();
        $this->tr = $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->tr->commit();
    }

    public function rollback()
    {
        $this->tr->rollback();
    }
}