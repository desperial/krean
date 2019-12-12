<?php
/**
 * Created by PhpStorm.
 * User: chiff
 * Date: 18.04.2019
 * Time: 11:01
 */

namespace console\models;


class ParserConfig extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'parser_configs';
    }
}