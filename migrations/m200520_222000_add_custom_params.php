<?php
use yii\db\Migration;

class m200520_222000_add_custom_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cms_article', 'custom_params', $this->json()->comment('Специальные параметры материала'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cms_article', 'custom_params');
    }
}
