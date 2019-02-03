<?php

namespace soless\cms\models\base;

use Yii;

/**
 * This is the model class for table "cms_article_related".
 *
 * @property int $cms_article_id id Материала
 * @property int $cms_tag_id id Тега
 *
 * @property CmsArticle $cmsArticle
 * @property CmsTag $cmsTag
 */
class CmsArticleRelated extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cms_article_related';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cms_article_id', 'cms_tag_id'], 'required'],
            [['cms_article_id', 'cms_tag_id'], 'integer'],
            [['cms_article_id', 'cms_tag_id'], 'unique', 'targetAttribute' => ['cms_article_id', 'cms_tag_id']],
            [['cms_article_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmsArticle::className(), 'targetAttribute' => ['cms_article_id' => 'id']],
            [['cms_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmsTag::className(), 'targetAttribute' => ['cms_tag_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cms_article_id' => 'id Материала',
            'cms_tag_id' => 'id Тега',
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
    public function getCmsTag()
    {
        return $this->hasOne(CmsTag::className(), ['id' => 'cms_tag_id']);
    }
}
