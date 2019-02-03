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

}
