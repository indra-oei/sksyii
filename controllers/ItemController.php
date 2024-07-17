<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use app\models\Item;
use app\models\SubCategory;

class ItemController extends XController
{
    public function actionIndex()
    {
        $item = new Item();
        $subCategory = new SubCategory();
        return $this->render('item', [
            'items' => $item->getAll(),
            'subCategories' => $subCategory->getAll()
        ]);
    }

    public function actionGetAll()
    {
        $item = new Item();
        $itemList = $item->getAll();

        return $this->jsonEncode($itemList);
    }

    public function actionInsert()
    {
        $item = new Item();
        $item->name = $this->getParam('name');
        $item->description = $this->getParam('description');
        $item->price = $this->getParam('price');
        $item->validUntil = $this->getParam('valid_until');
        $item->subCategoryId = $this->getParam('subcategory_id');
        $item->scenario = 'insert';

        if ($item->validate())
        {
            $item->openConnection();
            $result = $item->insert();

            if ($result['errNum'] != 0)
            {
                $item->rollback();

                return $this->jsonEncode($result);
            }

            $item->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($item->errors);

            return $error;
        }
    }

    public function actionGetItemById()
    {
        $item = new Item();
        $item->id = $this->getParam('id');
        return $this->jsonEncode($item->getItemById());
    }

    public function actionGetItemByName()
    {
        $item = new Item();
        $item->name = $this->getParam('name');
        return $this->jsonEncode($item->getItemByName());
    }

    public function actionUpdate()
    {
        $item = new Item(); 
        $item->id = $this->getParam('id');
        $item->name = $this->getParam('name');
        $item->description = $this->getParam('description');
        $item->price = $this->getParam('price');
        $item->validUntil = date('d/M/Y', strtotime($this->getParam('valid_until')));
        $item->subCategoryId = $this->getParam('subcategory_id');
        $item->scenario = 'update';

        if ($item->validate())
        {
            $item->openConnection();
            $result = $item->update();

            if ($result['errNum'] != 0)
            {
                $item->rollback();

                return $this->jsonEncode($result);
            }

            $item->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($item->errors);

            return $error;
        }
    }

    public function actionDelete()
    {
        $item = new Item();
        $item->id = $this->getParam('id');

        if ($item->validate())
        {
            $item->openConnection();
            $result = $item->delete();

            if ($result['errNum'] != 0)
            {
                $item->rollback();

                return $this->jsonEncode($result);
            }

            $item->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($item->errors);

            return $error;
        }
    }
}