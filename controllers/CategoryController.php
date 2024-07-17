<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use app\models\Category;

class CategoryController extends XController
{
    public function actionIndex()
    {
        $category = new Category();
        return $this->render('category', [
            'categories' => $category->getAll()
        ]);
    }

    public function actionInsert()
    {
        $category = new Category();
        $category->name = $this->getParam('name');
        $category->scenario = 'insert';

        if ($category->validate())
        {
            $category->openConnection();
            $result = $category->insert();

            if ($result['errNum'] != 0)
            {
                $category->rollback();

                return $this->jsonEncode($result);
            }

            $category->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($category->errors);

            return $error;
        }
    }

    public function actionGetCategoryById()
    {
        $category = new Category();
        $category->id = $this->getParam('id');
        return $this->jsonEncode($category->getCategoryById());
    }

    public function actionUpdate()
    {
        $category = new Category();
        $category->id = $this->getParam('id');
        $category->name = $this->getParam('name');
        $category->scenario = 'update';

        if ($category->validate())
        {
            $category->openConnection();
            $result = $category->update();

            if ($result['errNum'] != 0)
            {
                $category->rollback();

                return $this->jsonEncode($result);
            }

            $category->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($category->errors);

            return $error;
        }
    }

    public function actionDelete()
    {
        $category = new Category();
        $category->id = $this->getParam('id');

        if ($category->validate())
        {
            $category->openConnection();
            $result = $category->delete();

            if ($result['errNum'] != 0)
            {
                $category->rollback();

                return $this->jsonEncode($result);
            }

            $category->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($category->errors);

            return $error;
        }
    }
}