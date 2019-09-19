<?php
use yii\db\Migration;

class m190919_090202_article_priority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $options = null;
        if ($this->db->driverName === 'mysql') {
            $options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->addColumn('cms_article', 'priority', $this->integer(3)->defaultValue(500)->notNull()->comment('Приоритет материала'));
        $this->createIndex('idx-cms_article-priority', 'cms_article', 'priority');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('cms_article', 'priority');
    }
}
