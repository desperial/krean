<?php
namespace frontend\controllers;

use yii\web\Controller;
use yii\data\Pagination;
use frontend\models\Realty;

class RealtyController extends Controller
{
    public function actionIndex()
    {
        $query = Realty::find();

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
        ]);

        $realty = $query->orderBy('create_timestamp')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->renderAjax('index', [
            'realty' => $realty,
            'pagination' => $pagination,
        ]);
    }
}
