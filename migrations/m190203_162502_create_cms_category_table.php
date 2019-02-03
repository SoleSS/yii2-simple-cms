<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cms_category`.
 */
class m190203_162502_create_cms_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cms_category', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->comment('Название категории'),
            'description' => $this->text()->comment('Описание категории'),
            'created_at' => $this->datetime()->notNull()->comment('Дата создания'),
            'updated_at' => $this->datetime()->notNull()->comment('Дата обновления'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('cms_category');
    }
}
