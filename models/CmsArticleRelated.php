<?php

namespace soless\cms\models;

/**
 * This is the model class for table "cms_article_related".
 *
 * @property int $cms_article_id id Материала
 * @property int $cms_tag_id id Тега
 *
 * @property CmsArticle $cmsArticle
 * @property CmsTag $cmsTag
 */
class CmsArticleRelated extends base\CmsArticleRelated
{

}
