<?php
namespace frontend\controllers;

use yii\web\Controller;
use yii\data\Pagination;
use yii\web\Request;
use frontend\models\Realty;
use frontend\models\SearchForm;

class RealtyController extends Controller
{
    public function actionIndex()
    {
        $model = new SearchForm();

        $query = Realty::find();

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
        ]);
        $temp = \Yii::$app->request->post('SearchForm');
        //$model->attributes = \Yii::$app->request->post('SearchForm');
        if ($model->load(\Yii::$app->request->post()) ) {
            
            $searchCond = ["and"];

            if (($model->deal) && ($model->deal > 1)) {
                if ($model->deal == 2)
                    $searchCond[] = ["=","deal","rent"];
                elseif ($model->deal == 3)
                    $searchCond[] = ["=","deal","buy"];
            }

            $realty = $query->orderBy('sort')
                ->where($searchCond)
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
        }
        else {
            $realty = $query->orderBy('sort')
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
        }
        $realty = $this->textMapping($realty);
        return $this->renderAjax('index', [
            'model' => $model,
            'realty' => $realty,
            'pagination' => $pagination,
            'temp'  => $temp
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

    public function actionSearch()
    {
        $model = new SearchForm();

        if ($model->load(\Yii::$app->request->post()) ) {
            $query = Realty::find();

            $searchCond = ["and"];

            if (($model->deal) && ($model->deal > 1)) {
                if ($model->deal == 2)
                    $searchCond[] = ["=","deal","rent"];
                elseif ($model->deal == 3)
                    $searchCond[] = ["=","deal","buy"];
            }

            $pagination = new Pagination([
                'defaultPageSize' => 10,
                'totalCount' => $query->count(),
            ]);

            $realty = $query->orderBy('sort')
                ->where($searchCond)
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();

            $realty = $this->textMapping($realty);
            return $this->renderAjax('index', [
                'model' => $model,
                'realty' => $realty,
                'pagination' => $pagination,
            ]);

        } else {
            // either the page is initially displayed or there is some validation error
            return $this->renderAjax('index', [
                'model' => $model,
                'realty' => $realty,
                'pagination' => $pagination
            ]);
        }

    }
}
