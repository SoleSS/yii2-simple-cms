<?php
use yii\db\Migration;

class m190203_164002_create_cms_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cms_article', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->comment('Заголовок'),
            'title_lng1' => $this->string(255)->comment('Заголовок (язык #1)'),
            'title_lng2' => $this->string(255)->comment('Заголовок (язык #2)'),
            'type_id' => $this->integer(2)->notNull()->defaultValue(1)->comment('Тип документа'),
            'subtitle' => $this->string(255)->comment('Подзаголовок'),
            'subtitle_lng1' => $this->string(255)->comment('Подзаголовок (язык #1)'),
            'subtitle_lng2' => $this->string(255)->comment('Подзаголовок (язык #2)'),
            'image' => $this->string(512)->comment('Изображение'),
            'image_width' => $this->integer(4)->comment('Ширина изображения'),
            'image_height' => $this->integer(4)->comment('Высота изображения'),
            'show_image' => $this->boolean()->notNull()->defaultValue(false)->comment('Отображать изображение?'),
            'intro' => $this->string(1024)->notNull()->comment('Вводный текст'),
            'intro_lng1' => $this->string(1024)->comment('Вводный текст (язык #1)'),
            'intro_lng2' => $this->string(1024)->comment('Вводный текст (язык #2)'),
            'full' => $this->text()->notNull()->comment('Полный текст'),
            'full_lng1' => $this->text()->comment('Полный текст (язык #1)'),
            'full_lng2' => $this->text()->comment('Полный текст (язык #2)'),
            'amp_full' => $this->text()->comment('Полный текст (AMP версия)'),
            'amp_full_lng1' => $this->text()->comment('Полный текст (AMP версия) (язык #1)'),
            'amp_full_lng2' => $this->text()->comment('Полный текст (AMP версия) (язык #2)'),
            'published' => $this->boolean()->notNull()->defaultValue(false)->comment('Опубликован?'),
            'publish_up' => $this->datetime()->notNull()->comment('Дата начала публикации'),
            'publish_down' => $this->datetime()->notNull()->comment('Дата окончания публикации'),
            'user_id' => $this->integer()->comment('id Автора'),
            'user_alias' => $this->string(255)->comment('Алиас автора'),
            'meta_keywords' => $this->string(1024)->comment('Meta keywords'),
            'meta_description' => $this->string(2048)->comment('Meta description'),
            'hits' => $this->integer()->notNull()->defaultValue(0)->comment('Кол-во просмотров'),
            'medias' => $this->json()->comment('Медиа контент'),
            'created_at' => $this->datetime()->notNull()->comment('Дата создания'),
            'updated_at' => $this->datetime()->notNull()->comment('Дата обновления'),
        ]);

        $this->createIndex('idx-cms_article-type_id', 'cms_article', 'type_id');
        $this->createIndex('idx-cms_article-published', 'cms_article', 'published');
        $this->createIndex('idx-cms_article-publish_up', 'cms_article', 'publish_up');
        $this->createIndex('idx-cms_article-publish_down', 'cms_article', 'publish_down');
        $this->createIndex('idx-cms_article-user_id', 'cms_article', 'user_id');
        $this->createIndex('idx-cms_article-user_alias', 'cms_article', 'user_alias');
        $this->createIndex('idx-cms_article-hits', 'cms_article', 'hits');
        $this->createIndex('idx-cms_article-created_at', 'cms_article', 'created_at');
        $this->createIndex('idx-cms_article-updated_at', 'cms_article', 'updated_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('cms_article');
    }
}
