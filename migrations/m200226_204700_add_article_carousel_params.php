<?php
use yii\db\Migration;

class m200226_204700_add_article_carousel_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cms_article', 'carousel_params', $this->json()->comment('Параметры карусели'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cms_article', 'carousel_params');
    }
}
