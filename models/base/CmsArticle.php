<?php

namespace soless\cms\models\base;

use Yii;

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
 * @property array $gallery Галерея
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
class CmsArticle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cms_article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'intro', 'full', 'publish_up', 'publish_down', 'created_at', 'updated_at'], 'required'],
            [['type_id', 'image_width', 'image_height', 'show_image', 'published', 'user_id', 'hits'], 'integer'],
            [['full', 'full_lng1', 'full_lng2', 'amp_full', 'amp_full_lng1', 'amp_full_lng2'], 'string'],
            [['publish_up', 'publish_down', 'medias', 'gallery', 'created_at', 'updated_at'], 'safe'],
            [['title', 'title_lng1', 'title_lng2', 'subtitle', 'subtitle_lng1', 'subtitle_lng2', 'user_alias'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 512],
            [['intro', 'intro_lng1', 'intro_lng2', 'meta_keywords'], 'string', 'max' => 1024],
            [['meta_description'], 'string', 'max' => 2048],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'title_lng1' => 'Заголовок (язык #1)',
            'title_lng2' => 'Заголовок (язык #2)',
            'type_id' => 'Тип документа',
            'subtitle' => 'Подзаголовок',
            'subtitle_lng1' => 'Подзаголовок (язык #1)',
            'subtitle_lng2' => 'Подзаголовок (язык #2)',
            'image' => 'Изображение',
            'image_width' => 'Ширина изображения',
            'image_height' => 'Высота изображения',
            'show_image' => 'Отображать изображение?',
            'intro' => 'Вводный текст',
            'intro_lng1' => 'Вводный текст (язык #1)',
            'intro_lng2' => 'Вводный текст (язык #2)',
            'full' => 'Полный текст',
            'full_lng1' => 'Полный текст (язык #1)',
            'full_lng2' => 'Полный текст (язык #2)',
            'amp_full' => 'Полный текст (AMP версия)',
            'amp_full_lng1' => 'Полный текст (AMP версия) (язык #1)',
            'amp_full_lng2' => 'Полный текст (AMP версия) (язык #2)',
            'published' => 'Опубликован?',
            'publish_up' => 'Дата начала публикации',
            'publish_down' => 'Дата окончания публикации',
            'user_id' => 'id Автора',
            'user_alias' => 'Алиас автора',
            'meta_keywords' => 'Meta keywords',
            'meta_description' => 'Meta description',
            'hits' => 'Кол-во просмотров',
            'medias' => 'Медиа контент',
            'gallery' => 'Галерея',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticleCategories()
    {
        return $this->hasMany(CmsArticleCategory::className(), ['cms_article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsCategories()
    {
        return $this->hasMany(CmsCategory::className(), ['id' => 'cms_category_id'])->viaTable('cms_article_category', ['cms_article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticleRelateds()
    {
        return $this->hasMany(CmsArticleRelated::className(), ['cms_article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsTags()
    {
        return $this->hasMany(CmsTag::className(), ['id' => 'cms_tag_id'])->viaTable('cms_article_related', ['cms_article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsArticleTags()
    {
        return $this->hasMany(CmsArticleTag::className(), ['cms_article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsTags0()
    {
        return $this->hasMany(CmsTag::className(), ['id' => 'cms_tag_id'])->viaTable('cms_article_tag', ['cms_article_id' => 'id']);
    }
}
