<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use app\models\Order;

class OrderController extends XController
{
    public function actionIndex()
    {
        $order = new Order();
        return $this->render('order', [
            'orders' => $order->getAll()
        ]);
    }

    public function actionGetTableNum()
    {
        $order = new Order();
        return $order->generateTable();
    }

    public function actionInsert()
    {
        $order = new Order();
        $order->customerName = $this->getParam('customer_name');
        $order->customerSex = $this->getParam('customer_sex');
        $order->tableNo = $this->getParam('table_no');
        $order->totalCustomer = $this->getParam('total_customer');
        $order->scenario = 'insert';

        if ($order->validate())
        {
            $order->openConnection();
            $result = $order->insert();

            if ($result['errNum'] != 0)
            {
                $order->rollback();

                return $this->jsonEncode($result);
            }

            $order->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($order->errors);

            return $error;
        }
    }

    public function actionUpdate()
    {
        $order = new Order();
        $order->id = $this->getParam('id');
        $order->orderStatus = $this->getParam('order_status');
        $order->scenario = 'update';

        if ($order->validate())
        {
            $order->openConnection();
            $result = $order->update();

            if ($result['errNum'] != 0)
            {
                $order->rollback();

                return $this->jsonEncode($result);
            }

            $order->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($order->errors);

            return $error;
        }
    }
}