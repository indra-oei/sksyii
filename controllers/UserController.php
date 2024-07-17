<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use app\models\User;

class UserController extends XController
{
    public function actionIndex()
    {
        $users = new User();
        return $this->render('user', [
            'users' => $users->getAll()
        ]);
    }
    
    public function actionInsert()
    {
        $user = new User();
        $user->name = $this->getParam('name');
        $user->email = $this->getParam('email');
        $user->password = $this->getParam('password');
        $user->reconfirm = $this->getParam('reconfirm');
        $user->scenario = 'insert';


        if ($user->validate())
        {
            $user->openConnection();
            $result = $user->insert();

            if ($result['errNum'] != 0)
            {
                $user->rollback();

                return $this->jsonEncode($result);
            }

            $user->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($user->errors);

            return $error;
        }
    }

    public function actionGetUserByEmail()
    {
        $user = new User();
        $user->email = $this->getParam('email');
        return $this->jsonEncode($user->getUserByEmail());
    }

    public function actionUpdate()
    {
        $user = new User();
        $user->name = $this->getParam('name');
        $user->email = $this->getParam('email');
        $user->password = $this->getParam('password');
        $user->reconfirm = $this->getParam('reconfirm');
        $user->scenario = 'update';
        
        if ($user->validate())
        {
            $user->openConnection();
            $result = $user->update();

            if ($result['errNum'] != 0)
            {
                $user->rollback();

                return $this->jsonEncode($result);
            }

            $user->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($user->errors);

            return $error;
        }
    }

    public function actionDelete()
    {
        $user = new User();
        $user->email = $this->getParam('email');
        $user->scenario = 'delete';
        
        if ($user->validate())
        {
            $user->openConnection();
            $result = $user->delete();

            if ($result['errNum'] != 0)
            {
                $user->rollback();

                return $this->jsonEncode($result);
            }

            $user->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($user->errors);

            return $error;
        }
    }

}