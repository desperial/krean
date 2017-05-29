<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "articles".
 *
 * @property integer $id_articles
 * @property string $title
 * @property string $type
 * @property string $announce
 * @property string $text
 * @property string $date
 * @property integer $author
 *
 * @property User $author0
 */
class Articles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'articles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'type', 'announce', 'text'], 'required'],
            [['type', 'announce', 'text'], 'string'],
            [['date'], 'safe'],
            [['author'], 'integer'],
            [['title'], 'string', 'max' => 45],
            [['author'], 'default', 'value'=> Yii::$app->user->identity->id],
            [['author'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_articles' => 'ID статьи',
            'title' => 'Заголовок',
            'type' => 'Тип',
            'announce' => 'Анонс',
            'text' => 'Текст',
            'date' => 'Время создания',
            'author' => 'Автор',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor0()
    {
        return $this->hasOne(User::className(), ['id' => 'author']);
    }
}
