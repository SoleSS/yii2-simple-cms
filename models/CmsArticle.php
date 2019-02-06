<?php

namespace soless\cms\models;

use \soless\cms\helpers\AMP;
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
 * @property CmsTag[] $tags
 * @property CmsTag[] $relatedTags
 * @property CmsArticleTag[] $cmsArticleTags
 * @property CmsTag[] $cmsTags0
 */
class CmsArticle extends base\CmsArticle
{
    const TYPE_WEBPAGE              = 1;
    const TYPE_NEWS                 = 2;
    const TYPE_ARTICLE              = 3;

    const TYPE_NAME = [
        self::TYPE_WEBPAGE          => 'Страница',
        self::TYPE_NEWS             => 'Новость',
        self::TYPE_ARTICLE          => 'Статья',
    ];

    const TYPE_SCHEMA = [
        self::TYPE_WEBPAGE          => 'WebPage',
        self::TYPE_NEWS             => 'NewsArticle',
        self::TYPE_ARTICLE          => 'Article',
    ];

    const MEDIA_TYPE_IMAGE          = 1;
    const MEDIA_TYPE_YTVIDEO        = 2;
    const MEDIA_TYPE_IFRAME         = 3;

    const MEDIA_TYPE_NAME = [
        self::MEDIA_TYPE_IMAGE      => 'Изображение',
        self::MEDIA_TYPE_YTVIDEO    => 'Видео YouTube',
        self::MEDIA_TYPE_IFRAME     => 'iFrame',
    ];

    public $selectedCategories = [];
    public $selectedTags;
    public $selectedRelatedTags;

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['selectedCategories'] = 'Категории';
        $labels['selectedTags'] = 'Теги';
        $labels['selectedRelatedTags'] = 'Теги для связывания материалов';

        return $labels;
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->user_id = \Yii::$app->user->id;
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');
        $imageSize = $this->getImageParams();
        $this->image_width = $imageSize[0];
        $this->image_height = $imageSize[1];
        $this->amp_full = AMP::encode($this->full);

        if (!empty($this->full_lng1)) {
            $this->amp_full_lng1 = AMP::encode($this->full_lng1);
        }

        if (!empty($this->full_lng2)) {
            $this->amp_full_lng1 = AMP::encode($this->full_lng2);
        }

        parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        $this->setCategories();
        $this->setTags();
        $this->setRelatedTags();
    }

    private function getImageParams() {
        $imageSize = [];
        if (file_exists(\Yii::getAlias('@app') .'/web'. $this->image)) {
            $imageSize = getimagesize(\Yii::getAlias('@app') .'/web'. $this->image);
        }

        return $imageSize;
    }

    public function afterFind()
    {
        $this->updateCounters(['hits' => 1]);
        $this->selectedCategories = \yii\helpers\ArrayHelper::getColumn($this->cmsArticleCategories, 'id');
        $this->selectedRelatedTags = \yii\helpers\ArrayHelper::getColumn($this->relatedTags, 'title');
        $this->selectedTags = \yii\helpers\ArrayHelper::getColumn($this->tags, 'title');

        parent::afterFind();
    }

    public function getRelatedTags()
    {
        return $this->hasMany(CmsTag::className(), ['id' => 'cms_tag_id'])->viaTable('cms_article_related', ['cms_article_id' => 'id']);
    }

    public function getTags()
    {
        return $this->hasMany(CmsTag::className(), ['id' => 'cms_tag_id'])->viaTable('cms_article_tag', ['cms_article_id' => 'id']);
    }

    public function getTypeSchema() {
        return static::TYPE_SCHEMA[$this->type_id];
    }

    public function getTypeName() {
        return static::TYPE_NAME[$this->type_id];
    }

    private function setCategories() {
        $this->unlinkAll('cmsCategories', true);
        if (!empty($this->selectedCategories)) foreach ($this->selectedCategories as $id) {
            $category = CmsCategory::findOne($id);
            $this->link('cmsCategories', $category);
        }

        return true;
    }

    private function setTags() {
        $this->unlinkAll('tags', true);
        $tags = $this->selectedTags;
        if (!is_array($this->selectedTags)) {
            $tags = explode(',', $this->selectedTags);
        }

        if (!empty($tags)) foreach ($tags as $title) {
            if (!empty($title)) {
                if ( CmsTag::find()->where( [ 'title' => $title ] )->exists() )
                    $tag = CmsTag::find()->where(['title' => $title])->limit(1)->one();
                else {
                    $tag = new CmsTag();
                    $tag->title = $title;
                    $tag->description = '';
                    $tag->priority = 10;
                    $tag->save();
                }

                $this->link('tags', $tag);
            }
        }

        return true;
    }

    private function setRelatedTags() {
        $this->unlinkAll('relatedTags', true);
        $tags = $this->selectedRelatedTags;
        if (!is_array($this->selectedRelatedTags)) {
            $tags = explode(',', $this->selectedRelatedTags);
        }

        if (!empty($tags)) foreach ($tags as $title) {
            if (!empty($title)) {
                if ( CmsTag::find()->where( [ 'title' => $title ] )->exists() )
                    $tag = CmsTag::find()->where(['title' => $title])->limit(1)->one();
                else {
                    $tag = new CmsTag();
                    $tag->title = $title;
                    $tag->description = '';
                    $tag->priority = 10;
                    $tag->save();
                }

                $this->link('relatedTags', $tag);
            }
        }

        return true;
    }


}
