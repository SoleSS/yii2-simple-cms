<?php

namespace soless\cms\models\base;

use Yii;

/**
 * This is the model class for table "cms_tag".
 *
 * @property int $id
 * @property string $title Наименование
 * @property string $description Описание категории
 * @property int $priority Приоритет
 * @property string $created_at Дата создания
 * @property string $updated_at Дата обновления
 *
 * @property CmsArticleRelated[] $cmsArticleRelateds
 * @property CmsArticle[] $cmsArticles
 * @property CmsArticleTag[] $cmsArticleTags
 * @property CmsArticle[] $cmsArticles0
 */
class CmsTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cms_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'created_at', 'updated_at'], 'required'],
            [['description'], 'string'],
            [['priority'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Наименование',
            'description' => 'Описание категории',
            'priority' => 'Приоритет',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticleRelateds()
    {
        return $this->hasMany(CmsArticleRelated::className(), ['cms_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticles()
    {
        return $this->hasMany(CmsArticle::className(), ['id' => 'cms_article_id'])->viaTable('cms_article_related', ['cms_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticleTags()
    {
        return $this->hasMany(CmsArticleTag::className(), ['cms_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticles0()
    {
        return $this->hasMany(CmsArticle::className(), ['id' => 'cms_article_id'])->viaTable('cms_article_tag', ['cms_tag_id' => 'id']);
    }
}
