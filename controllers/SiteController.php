<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use app\models\Login;

class SiteController extends XController
{
    public function actionLogout()
    {
        $logout = new Login();
        $logout->saveLoginLogoutHistory(1);

        $this->destroyAllSession();

        return 1;
    }
}
