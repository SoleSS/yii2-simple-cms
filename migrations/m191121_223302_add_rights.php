<?php
use yii\db\Migration;

class m191121_223302_add_rights extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cms_article', 'rights', $this->json()->comment('Права доступа'));
        $this->addColumn('cms_category', 'rights', $this->json()->comment('Права доступа'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cms_article', 'rights');
        $this->dropColumn('cms_category', 'rights');
    }
}
