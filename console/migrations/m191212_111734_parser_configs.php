<?php

use yii\db\Migration;

/**
 * Class m191212_111734_parser_configs
 */
class m191212_111734_parser_configs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("parser_configs",[
            "id" => $this->primaryKey(),
            "site_name" => $this->string(255),
            "cur_city" => $this->string(255),
            "cur_type" => $this->string(255),
            "cur_page" => $this->integer(8),
        ]);
        $this->createIndex("parser_configs_site","parser_configs","site_name",true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191212_111734_parser_configs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191212_111734_parser_configs cannot be reverted.\n";

        return false;
    }
    */
}
