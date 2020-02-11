<?php
use yii\db\Migration;

class m200211_191100_promo_image extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('cms_article', 'promo_image_path', $this->string(512)->comment('Промо изображение'));
        $this->addColumn('cms_article', 'promo_image_width', $this->integer(4)->comment('Ширина промо изображения'));
        $this->addColumn('cms_article', 'promo_image_height', $this->integer(4)->comment('Высота промо изображения'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cms_article', 'promo_image_path');
        $this->dropColumn('cms_article', 'promo_image_width');
        $this->dropColumn('cms_article', 'promo_image_height');
    }
}
