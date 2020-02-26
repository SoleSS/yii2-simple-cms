<?php
use yii\db\Migration;

class m200226_225200_add_article_carousel_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cms_article', 'carousel_slides', $this->json()->comment('Слайды карусели'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cms_article', 'carousel_slides');
    }
}
