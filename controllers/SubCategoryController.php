<?php

namespace app\controllers;

use Yii;
use app\extensions\XController;
use app\models\SubCategory;
use app\models\Category;

class SubCategoryController extends XController
{
    public function actionIndex()
    {
        $subCategory = new SubCategory();
        $category = new Category();
        return $this->render('sub-category', [
            'subCategories' => $subCategory->getAll(),
            'categories' => $category->getAll()
        ]);
    }

    public function actionInsert()
    {
        $subCategory = new SubCategory();
        $subCategory->name = $this->getParam('name');
        $subCategory->categoryId = $this->getParam('category_id');
        $subCategory->scenario = 'insert';

        if ($subCategory->validate())
        {
            $subCategory->openConnection();
            $result = $subCategory->insert();

            if ($result['errNum'] != 0)
            {
                $subCategory->rollback();

                return $this->jsonEncode($result);
            }

            $subCategory->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($subCategory->errors);

            return $error;
        }
    }

    public function actionGetCategoryById()
    {
        $subCategory = new SubCategory();
        $subCategory->id = $this->getParam('id');
        return $this->jsonEncode($subCategory->getSubCategoryById());
    }

    public function actionUpdate()
    {
        $subCategory = new SubCategory(); 
        $subCategory->id = $this->getParam('id');
        $subCategory->name = $this->getParam('name');
        $subCategory->categoryId = $this->getParam('category_id');
        $subCategory->scenario = 'update';

        if ($subCategory->validate())
        {
            $subCategory->openConnection();
            $result = $subCategory->update();

            if ($result['errNum'] != 0)
            {
                $subCategory->rollback();

                return $this->jsonEncode($result);
            }

            $subCategory->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($subCategory->errors);

            return $error;
        }
    }

    public function actionDelete()
    {
        $subCategory = new SubCategory();
        $subCategory->id = $this->getParam('id');

        if ($subCategory->validate())
        {
            $subCategory->openConnection();
            $result = $subCategory->delete();

            if ($result['errNum'] != 0)
            {
                $subCategory->rollback();

                return $this->jsonEncode($result);
            }

            $subCategory->commit();

            return $this->jsonEncode($result);
        }
        else
        {
            $error = $this->jsonEncode($subCategory->errors);

            return $error;
        }
    }
}