<?php
/**
 * Created by PhpStorm.
 * User: chiff
 * Date: 15-Oct-18
 * Time: 13:07
 */

namespace common\widgets;

use frontend\models\Realty;
use yii\data\Pagination;

class RealtyWigdet extends \yii\bootstrap\Widget
{
    private $realty;
    private $pages;

    public function init()
    {
        parent::init();
        $this->realty = Realty::find();
        //$countRealty = clone $this->realty;
        $this->pages = new Pagination(['totalCount' => $this->realty->count()]);
        $this->realty = $this->realty->offset($this->pages->offset)
            ->limit($this->pages->limit)
            ->all();

    }
    public function run()
    {
        $this->render('list',[
            'realty' => $this->realty,
            'pages' => $this->pages,
        ]);
    }
}