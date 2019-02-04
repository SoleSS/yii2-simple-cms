<?php

namespace soless\cms\models;

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
class CmsTag extends base\CmsTag
{
    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');

        parent::beforeValidate();
    }


}
