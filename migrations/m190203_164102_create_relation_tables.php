<?php
use yii\db\Migration;

class m190203_164102_create_relation_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cms_article_category', [
            'cms_article_id' => $this->integer()->notNull()->comment('id Материала'),
            'cms_category_id' => $this->integer()->notNull()->comment('id Категории'),
            'PRIMARY KEY(cms_article_id, cms_category_id)',
        ]);

        $this->createIndex('idx-cms_article_category-cms_article_id', 'cms_article_category', 'cms_article_id');
        $this->createIndex('idx-cms_article_category-cms_category_id', 'cms_article_category', 'cms_category_id');

        $this->createTable('cms_article_tag', [
            'cms_article_id' => $this->integer()->notNull()->comment('id Материала'),
            'cms_tag_id' => $this->integer()->notNull()->comment('id Тега'),
            'PRIMARY KEY(cms_article_id, cms_tag_id)',
        ]);

        $this->createIndex('idx-cms_article_tag-cms_article_id', 'cms_article_tag', 'cms_article_id');
        $this->createIndex('idx-cms_article_tag-cms_tag_id', 'cms_article_tag', 'cms_tag_id');

        $this->createTable('cms_article_related', [
            'cms_article_id' => $this->integer()->notNull()->comment('id Материала'),
            'cms_tag_id' => $this->integer()->notNull()->comment('id Тега'),
            'PRIMARY KEY(cms_article_id, cms_tag_id)',
        ]);

        $this->createIndex('idx-cms_article_related-cms_article_id', 'cms_article_related', 'cms_article_id');
        $this->createIndex('idx-cms_article_related-cms_tag_id', 'cms_article_related', 'cms_tag_id');

        $this->addForeignKey('fk-cms_article_category-cms_article_id', 'cms_article_category', 'cms_article_id', 'cms_article', 'id', 'CASCADE');
        $this->addForeignKey('fk-cms_article_category-cms_category_id', 'cms_article_category', 'cms_category_id', 'cms_category', 'id', 'CASCADE');

        $this->addForeignKey('fk-cms_article_tag-cms_article_id', 'cms_article_tag', 'cms_article_id', 'cms_article', 'id', 'CASCADE');
        $this->addForeignKey('fk-cms_article_tag-cms_tag_id', 'cms_article_tag', 'cms_tag_id', 'cms_tag', 'id', 'CASCADE');

        $this->addForeignKey('fk-cms_article_related-cms_article_id', 'cms_article_related', 'cms_article_id', 'cms_article', 'id', 'CASCADE');
        $this->addForeignKey('fk-cms_article_related-cms_tag_id', 'cms_article_related', 'cms_tag_id', 'cms_tag', 'id', 'CASCADE');

        $this->addForeignKey('fk-cms_article-user_id', 'cms_article', 'user_id', 'user', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-cms_article-user_id', 'cms_article');

        $this->dropForeignKey('fk-cms_article_category-cms_article_id', 'cms_article_category');
        $this->dropForeignKey('fk-cms_article_category-cms_category_id', 'cms_article_category');

        $this->dropForeignKey('fk-cms_article_tag-cms_article_id', 'cms_article_tag');
        $this->dropForeignKey('fk-cms_article_tag-cms_tag_id', 'cms_article_tag');

        $this->dropForeignKey('fk-cms_article_related-cms_article_id', 'cms_article_related');
        $this->dropForeignKey('fk-cms_article_related-cms_tag_id', 'cms_article_related');

        $this->dropTable('cms_article_category');
        $this->dropTable('cms_article_related');
        $this->dropTable('cms_article_tag');
    }
}
