<?php

namespace soless\cms\models;

/**
 * This is the model class for table "cms_category".
 *
 * @property int $id
 * @property string $title Название категории
 * @property string $description Описание категории
 * @property string $created_at Дата создания
 * @property string $updated_at Дата обновления
 *
 * @property CmsArticleCategory[] $cmsArticleCategories
 * @property CmsArticle[] $cmsArticles
 */
class CmsCategory extends base\CmsCategory
{
    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');

        parent::beforeValidate();
    }

    public static function asArray() {
        return \yii\helpers\ArrayHelper::map(static::find()->select(['id', 'title'])->asArray()->all(), 'id', 'title');
    }
}
