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

        $realty = $query->orderBy('sort')
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
            ->where([">","vip_shows",0])
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

        $realty = $query->orderBy('created_at')
            ->all();
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->renderAjax('coords', [
            'realty' => $realty
        ]);
    }

    public function actionList()
    {
        $realty = Realty::searchRealty();
        //$countRealty = clone $this->realty;
        $data = \Yii::$app->request->post();
        $pages = new Pagination(['totalCount' => $realty->count(), 'pageSize' => 10]);
        $realty = $realty->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        if (\Yii::$app->request->isPjax)
//            return $this->asJson($realty);
            return $this->renderAjax('list', [
                'realty' => $realty,
                'pages' => $pages
            ]);
        else {
            $realtyCountries = Realty::find()->groupBy("country")->all();
            $realtyTypes = Realty::find()->groupBy("type")->all();
            return $this->render('/site/index', [
                'realty' => $realty,
                'pages' => $pages,
                'countries' => $realtyCountries,
                'types' => $realtyTypes
            ]);
        }

        //$data = ['realty' => $realty, 'count' => count($realty)];
        //return $this->asJson($data);
    }
    private function textMapping($realty)
    {
        $mappingArray = [
            "deal" => [
                "buy" => "Продажа", 
                "rent" => "аренда"
            ],
            /*"type" => [
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
            ]*/
        ];
        foreach ($realty as $item) {
            foreach ($mappingArray as $key=>$field) {
                $item[$key] = $field[$item[$key]];
            }

        }
        return $realty;
    }
}
