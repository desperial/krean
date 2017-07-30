<?php
namespace frontend\controllers;

use yii\web\Controller;
use yii\data\Pagination;
use yii\web\Request;
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

        $realty = $this->textMapping($realty);
        return $this->renderAjax('index', [
            'realty' => $realty,
            'pagination' => $pagination,
        ]);
    }

    public function actionVip()
    {
        $count = \Yii::$app->request->get('count');
        $query = Realty::find();

        $realty = $query->orderBy('vip_shows DESC')
            ->all();

        $realty = $this->textMapping($realty);
        return $this->renderAjax('vip', [
            'realty' => $realty
        ]);
    }

    public function actionObject()
    {
        $id = \Yii::$app->request->get('id');
        $realty = Realty::findAll($id);


        $realty = $this->textMapping($realty);
        return $this->renderAjax('object', [
            'realty' => $realty
        ]);
    }

    public function actionCoords()
    {
        $query = Realty::find();

        $realty = $query->orderBy('create_timestamp')
            ->all();
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->renderAjax('coords', [
            'realty' => $realty
        ]);
    }

    private function textMapping($realty)
    {
        $mappingArray = [
            "deal" => [
                "buy" => "Продажа", 
                "rent" => "аренда"
            ],
            "type" => [
                "residential" => "Жилой",
                "commercial" => "Коммерческий"
            ],
            "view" => [
                "house" => "Дом",
                "building"  => "Здание",
                "land"  => "Земельный участок",
                "investment"    => "Инвестиционный проект",
                "apartment" => "Квартира",
                "premises"  => "Помещение",
                "others"    => "Прочее",
                "townhouse" => "Таунхаус"
            ],
            "group" => [
                "primary"   => "Первичная",
                "secondary" => "Вторичная"
            ]
        ];
        foreach ($realty as $item) {
            foreach ($mappingArray as $key=>$field) {
                $item[$key] = $field[$item[$key]];
            }
            $item->users->contact_phone = substr($item->users->contact_phone, 0, strlen($item->users->contact_phone) -
                 4) . 'XXXX';
        }
        return $realty;
    }
}
