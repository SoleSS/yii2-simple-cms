<?php

namespace soless\cms\models\base;

use Yii;

/**
 * This is the model class for table "cms_article_category".
 *
 * @property int $cms_article_id id Материала
 * @property int $cms_category_id id Категории
 *
 * @property CmsArticle $cmsArticle
 * @property CmsCategory $cmsCategory
 */
class CmsArticleCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cms_article_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cms_article_id', 'cms_category_id'], 'required'],
            [['cms_article_id', 'cms_category_id'], 'integer'],
            [['cms_article_id', 'cms_category_id'], 'unique', 'targetAttribute' => ['cms_article_id', 'cms_category_id']],
            [['cms_article_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmsArticle::className(), 'targetAttribute' => ['cms_article_id' => 'id']],
            [['cms_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmsCategory::className(), 'targetAttribute' => ['cms_category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cms_article_id' => 'id Материала',
            'cms_category_id' => 'id Категории',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticle()
    {
        return $this->hasOne(CmsArticle::className(), ['id' => 'cms_article_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsCategory()
    {
        return $this->hasOne(CmsCategory::className(), ['id' => 'cms_category_id']);
    }
}
