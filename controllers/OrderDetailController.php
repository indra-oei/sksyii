<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use app\models\Order;
use app\models\OrderDetail;

class OrderDetailController extends XController
{
    public function actionIndex()
    {
        $order = new Order();
        $order->orderNo = $this->getParam('ORDER_NUMBER');
        $orderInfo = $order->getOrderByOrderNo()['data'];

        return $this->render('order-detail', [
            'order' => $orderInfo
        ]);
    }

    public function actionGetAll()
    {
        $orderDetail = new OrderDetail();
        $orderDetail->orderNo = $this->getParam('order_no');
        
        return $this->jsonEncode($orderDetail->getAll());
    }

    public function actionInsert()
    {
        $orderDetail = new OrderDetail();
        $orderDetail->orderNo = $this->getParam('order_no');
        $orderDetail->itemName = $this->getParam('item_name');
        $orderDetail->itemQuantity = $this->getParam('item_quantity');
        $orderDetail->itemPrice = $this->getParam('item_price');
        $orderDetail->subtotal = $this->getParam('subtotal');
        $orderDetail->notes = $this->getParam('notes');
        $orderDetail->scenario = 'insert';

        if ($orderDetail->validate())
        {
            $orderDetail->openConnection();
            $result = $orderDetail->insert();

            if ($result['errNum'] != 0)
            {
                $orderDetail->rollback();

                return $this->jsonEncode($result);
            }

            $orderDetail->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($orderDetail->errors);

            return $error;
        }
    }

    public function actionUpdate()
    {
        $orderDetail = new OrderDetail();
        $orderDetail->orderId = $this->getParam('order_id');
        $orderDetail->scenario = 'update';

        if ($orderDetail->validate())
        {
            $orderDetail->openConnection();
            $result = $orderDetail->update();

            if ($result['errNum'] != 0)
            {
                $orderDetail->rollback();

                return $this->jsonEncode($result);
            }

            $orderDetail->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($orderDetail->errors);

            return $error;
        }
    }

    public function actionDestroy()
    {
        $orderDetail = new OrderDetail();
        $orderDetail->orderNo = $this->getParam('order_no');
        $orderDetail->openConnection();
        $result = $orderDetail->destroy();

        if ($result['errNum'] != 0)
        {
            $orderDetail->rollback();

            return $this->jsonEncode($result);
        }

        $orderDetail->commit();

        return $this->jsonEncode($result);
    }
}