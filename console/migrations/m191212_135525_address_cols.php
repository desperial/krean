<?php

use yii\db\Migration;

/**
 * Class m191212_135525_address_cols
 */
class m191212_135525_address_cols extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%realty}}","region",$this->string(255));
        $this->addColumn("{{%realty}}","settlement",$this->string(255));
        $this->addColumn("{{%realty}}","throughfare",$this->string(255));
        $this->addColumn("{{%realty}}","premise",$this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191212_135525_address_cols cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191212_135525_address_cols cannot be reverted.\n";

        return false;
    }
    */
}
