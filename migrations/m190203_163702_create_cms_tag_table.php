<?php
use yii\db\Migration;

/**
 * Handles the creation of table `cms_tag`.
 */
class m190203_163702_create_cms_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('cms_tag', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->comment('Наименование'),
            'description' => $this->text()->comment('Описание категории'),
            'priority' => $this->integer(2)->notNull()->defaultValue(10)->comment('Приоритет'),
            'created_at' => $this->datetime()->notNull()->comment('Дата создания'),
            'updated_at' => $this->datetime()->notNull()->comment('Дата обновления'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('cms_tag');
    }
}
