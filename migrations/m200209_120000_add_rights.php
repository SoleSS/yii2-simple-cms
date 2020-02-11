<?php
use yii\db\Migration;

class m200209_120000_add_rights extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cms_article', 'allowed_access_roles', $this->json()->comment('Группы имеющие право на доступ'));
        $this->addColumn('cms_category', 'allowed_access_roles', $this->json()->comment('Группы имеющие право на доступ'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cms_article', 'allowed_access_roles');
        $this->dropColumn('cms_category', 'allowed_access_roles');
    }
}
