<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use app\models\Transaction;

class TransactionController extends XController
{
    public function actionIndex()
    {
        return "Hello World";
    }

    public function actionInsert()
    {
        $transaction = new Transaction();
        $transaction->orderId = $this->getParam(('order_id'));
        $transaction->total = $this->getParam(('total'));
        $transaction->serviceAmount = $this->getParam(('service_amount'));
        $transaction->taxAmount = $this->getParam(('tax_amount'));
        $transaction->grandTotal = $this->getParam(('grand_total'));
        $transaction->scenario = 'insert';

        if ($transaction->validate())
        {
            $transaction->openConnection();
            $result = $transaction->insert();

            if ($result['errNum'] != 0)
            {
                $transaction->rollback();

                return $this->jsonEncode($result);
            }

            $transaction->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($transaction->errors);

            return $error;
        }
    }

    public function actionUpdate()
    {
        $transaction = new Transaction();
        $transaction->orderId = $this->getParam(('order_id'));
        $transaction->scenario = 'update';

        if ($transaction->validate())
        {
            $transaction->openConnection();
            $result = $transaction->update();

            if ($result['errNum'] != 0)
            {
                $transaction->rollback();

                return $this->jsonEncode($result);
            }

            $transaction->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($transaction->errors);

            return $error;
        }
    }

    public function actionDestroy()
    {
        $transaction = new Transaction();
        $transaction->orderId = $this->getParam('order_id');
        $transaction->scenario = 'delete';

        $transaction->openConnection();
        $result = $transaction->destroy();

        if ($result['errNum'] != 0)
        {
            $transaction->rollback();

            return $this->jsonEncode($result);
        }

        $transaction->commit();

        return $this->jsonEncode($result);
    }
}