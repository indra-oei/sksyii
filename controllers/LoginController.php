<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use app\models\Login;

class LoginController extends XController
{
    public function actionIndex()
    {
        $this->layout = 'login-layout';
        // die(print_r(Yii::$app->session->get('contactId')));
        return $this->render('login');
    }
    
    public function actionLogin()
    {
        $login = new Login();
        $login->email = $this->getParam('email');
        $login->password = $this->getParam('password');
        $login->scenario = 'login';

        if ($login->validate())
        {
            $result = $login->login();

            $this->refreshSession($result['result']);

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($login->errors);

            return $error;
        }
    }

}