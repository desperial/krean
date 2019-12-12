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
        
        $countries = $this->loadCountries();
        $currency = 0;
        $model = new SearchForm();
        if ($model->load(\Yii::$app->request->get()) && $model->validate()) {
            
            $searchCond = ["and"];

            if (($model->deal) && ($model->deal > 1)) {
                if ($model->deal == 2)
                    $searchCond[] = ["=","deal","rent"];
                elseif ($model->deal == 3)
                    $searchCond[] = ["=","deal","buy"];
            }
            if ($model->areaFrom > 0) {
                $searchCond[] = [">=",'area',$model->areaFrom];
            }
            if ($model->areaTo > 0) {
                $searchCond[] = ["<=",'area',$model->areaTo];
            }
            if ($model->country != "0") {
                $searchCond[] = ["LIKE",'country',$model->country];
            }
            if ($model->type != "0") {
                $searchCond[] = ["=",'type',$model->type];
            }
            if ($model->subtype != "0") {
                $searchCond[] = ["=",'view',$model->subtype];
            }
            if ($model->group != "0") {
                $searchCond[] = ["=",'group',$model->group];
            }
            if (($model->priceFrom > 0) || ($model->priceTo > 0)) {
                $priceFrom = $model->priceFrom > 0 ? $model->priceFrom : 0;
                $priceTo = $model->priceTo > 0 ? $model->priceTo : 999999999999;
                $session = \Yii::$app->session;
                $session->open();
                $searchCond[] = [
                    "or",
                    [
                        "and",
                        ["=", "currency", "RUR"],
                        [">=","price", $session['curValues'][$session['currency']] * $priceFrom],
                        ["<=","price", $session['curValues'][$session['currency']] * $priceTo]
                    ],
                    [
                        "and",
                        ["=", "currency", "EUR"],
                        [">=","price", ($session['curValues'][$session['currency']] / $session['curValues']['EUR']) * $priceFrom],
                        ["<=","price", ($session['curValues'][$session['currency']] / $session['curValues']['EUR']) * $priceTo]
                    ],
                    [
                        "and",
                        ["=", "currency", "USD"],
                        [">=","price", ($session['curValues'][$session['currency']] / $session['curValues']['USD']) * $priceFrom],
                        ["<=","price", ($session['curValues'][$session['currency']] / $session['curValues']['USD']) * $priceTo]
                    ]
                ];
                $session->close();
            }

            $currency = $model->currency;
            $query = Realty::find()->where($searchCond);

            $pagination = new Pagination([
                'defaultPageSize' => 10,
                'totalCount' => $query->count(),
            ]);
            $realty = $query->orderBy('sort')
                ->where($searchCond)
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();

        }
        else {
            $query = Realty::find();

            $pagination = new Pagination([
                'defaultPageSize' => 10,
                'totalCount' => $query->count(),
            ]);

            $realty = $query->orderBy('sort')
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
        }
        $realty = $this->textMapping($realty);
        $realty = $this->loadCurrency($realty,$currency);
        return $this->renderAjax('index', [
            'model' => $model,
            'realty' => $realty,
            'pagination' => $pagination,
            'countries' => $countries
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
        $realty = $this->loadCurrency($realty,0);
        return $this->renderAjax('vip', [
            'realty' => $realty
        ]);
    }

    public function actionObject()
    {
        $id = \Yii::$app->request->get('id');
        $realty = Realty::findAll($id);


        $realty = $this->textMapping($realty);
        $realty = $this->loadCurrency($realty,0);
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
    public function loadCountries() 
    {
        return Realty::find()->joinWith("countries")
            ->select([
                'COUNT(*) AS cnt', 
                'country',
                '{{%overhill_countries}}.name',
                '{{%overhill_countries}}.latitude',
                '{{%overhill_countries}}.longitude',
                '{{%overhill_countries}}.code'
            ])
            ->groupBy(['{{%overhill_countries}}.code'])
            ->asArray()
            ->all();
    }
    public function actionMenucountries()
    {
        $countries = $this->loadCountries();
        return $this->renderAjax('menu_countries', [
                'countries' => $countries
            ]);
    }
    public function loadCurrency($realty,$curCur)
    {
        $session = \Yii::$app->session;
        $session->open();
        if ($curCur == "0")
            $currency = "EUR";
        else
            $currency = $curCur;

        if (($session->has("currency")) && ($curCur == "0") ) {
            $currency = $session->get("currency");
        }
        else
            $session->set("currency",$currency);
        if (!$session->has("curValues")) {
            $session->set("curValues",$this->getCurrencyRates());
        }
        foreach ($realty as $item) {
            $item->price = $item->price * $session['curValues'][$item->currency] / $session['curValues'][$currency];
            $item->currency = $currency;
        }
        $session->close();
        return $realty;
    }

    public function getCurrencyRates()
    {
        if ($xml = simplexml_load_file('http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . date('d/m/Y')))
        {
            $cur = ["RUR" => 1];
            foreach ($xml as $val)
            {
                if ($val->CharCode == "USD")
                    $cur['USD'] = $val->Value."";
                elseif ($val->CharCode == "EUR")
                    $cur['EUR'] = $val->Value."";
            }
            return $cur;
        }
        return false;
    }
}
