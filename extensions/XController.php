<?php

namespace app\extensions;

use Yii;
use yii\web\Controller;
use yii\helpers\Json;
use yii\helpers\Url;

class XController extends Controller
{
    public $startTime;

    public function init()
    {
        $this->startTime = microtime(true);

        parent::init();
    }

    public function beforeAction($action)
    {
        $controllerId = Yii::$app->controller->id;
        $this->enableCsrfValidation = false;

        if ($controllerId != 'login')
        {
            if (!$this->getSession('contactId'))
            {
                return $this->redirect($this->createUrl(['login/']));
            }
        }
        else
        {
            if ($this->getSession('contactId'))
            {
                return $this->redirect($this->createUrl(['user/']));
            }
        }

        return parent::beforeAction($action);
    }

    public function createUrl($param, $scheme = false)
    {
        return Url::toRoute($param, $scheme);
    }

    public function getParam($paramKey, $flag = 1)
    {
        $paramValue = '';

        if (Yii::$app->request->isGet)
        {
            $paramValue = Yii::$app->request->getQueryParam($paramKey);
        }
        else if (Yii::$app->request->isPost)
        {
            $paramValue = Yii::$app->request->getBodyParam($paramKey);
        }

        if (is_string($paramValue))
        {
            return trim($paramValue);
        }

        return $paramValue;
    }

    public function jsonEncode($data, $flag = false)
    {
        if (!$flag)
        {
            $endtime = microtime(true);
            $data['processtime'] = $endtime - $this->startTime;
        }

        return Json::encode($data);
    }

    public function getSession($name)
    {
        $session = Yii::$app->session;
        $value = $session->get($name);

        $session->close();

        return $value;
    }

    public function setSession($value, $sessionName)
    {
        $session = Yii::$app->session;

        $session->set($sessionName, $value);
        $session->close();
    }

    public function destroyAllSession()
    {
        $session = Yii::$app->session;
         
        $session->open();
        $session->destroy();
        $session->close();
    }

    public function refreshSession($data)
    {
        $this->setSession($data['NAME'], 'contactName');
        $this->setSession($data['EMAIL'], 'email');
        $this->setSession($data['ID'], 'contactId');
        $this->setSession($data['OWNER_ID'], 'ownerId');
    }
}