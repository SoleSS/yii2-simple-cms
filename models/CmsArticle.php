<?php

namespace soless\cms\models;


/**
 * This is the model class for table "cms_article".
 *
 * @property int $id
 * @property string $title Заголовок
 * @property string $title_lng1 Заголовок (язык #1)
 * @property string $title_lng2 Заголовок (язык #2)
 * @property int $type_id Тип документа
 * @property string $subtitle Подзаголовок
 * @property string $subtitle_lng1 Подзаголовок (язык #1)
 * @property string $subtitle_lng2 Подзаголовок (язык #2)
 * @property string $image Изображение
 * @property int $image_width Ширина изображения
 * @property int $image_height Высота изображения
 * @property int $show_image Отображать изображение?
 * @property string $intro Вводный текст
 * @property string $intro_lng1 Вводный текст (язык #1)
 * @property string $intro_lng2 Вводный текст (язык #2)
 * @property string $full Полный текст
 * @property string $full_lng1 Полный текст (язык #1)
 * @property string $full_lng2 Полный текст (язык #2)
 * @property string $amp_full Полный текст (AMP версия)
 * @property string $amp_full_lng1 Полный текст (AMP версия) (язык #1)
 * @property string $amp_full_lng2 Полный текст (AMP версия) (язык #2)
 * @property int $published Опубликован?
 * @property string $publish_up Дата начала публикации
 * @property string $publish_down Дата окончания публикации
 * @property int $user_id id Автора
 * @property string $user_alias Алиас автора
 * @property string $meta_keywords Meta keywords
 * @property string $meta_description Meta description
 * @property int $hits Кол-во просмотров
 * @property array $medias Медиа контент
 * @property string $created_at Дата создания
 * @property string $updated_at Дата обновления
 *
 * @property User $user
 * @property CmsArticleCategory[] $cmsArticleCategories
 * @property CmsCategory[] $cmsCategories
 * @property CmsArticleRelated[] $cmsArticleRelateds
 * @property CmsTag[] $cmsTags
 * @property CmsArticleTag[] $cmsArticleTags
 * @property CmsTag[] $cmsTags0
 */
class CmsArticle extends base\CmsArticle
{

}
