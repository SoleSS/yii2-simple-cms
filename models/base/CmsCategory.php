<?php

namespace soless\cms\models\base;

use Yii;

/**
 * This is the model class for table "cms_category".
 *
 * @property int $id
 * @property string $title Название категории
 * @property string $description Описание категории
 * @property string $created_at Дата создания
 * @property string $updated_at Дата обновления
 * @property array $rights Права доступа
 * @property array $allowed_access_roles Группы имеющие право на доступ
 *
 * @property CmsArticleCategory[] $cmsArticleCategories
 * @property CmsArticle[] $cmsArticles
 */
class CmsCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cms_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'created_at', 'updated_at'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            ['allowed_access_roles', 'each', 'rule' => ['string']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название категории',
            'description' => 'Описание категории',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
            'rights' => 'Права доступа',
            'allowed_access_roles' => 'Группы имеющие право на доступ',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticleCategories()
    {
        return $this->hasMany(CmsArticleCategory::className(), ['cms_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticles()
    {
        return $this->hasMany(CmsArticle::className(), ['id' => 'cms_article_id'])->viaTable('cms_article_category', ['cms_category_id' => 'id']);
    }
}
