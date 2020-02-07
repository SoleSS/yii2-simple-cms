<?php

namespace soless\cms\models;

use Yii;
use \soless\cms\helpers\AMP;
use \soless\cms\helpers\Flickr;
use \Spatie\Async\Pool;

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
 * @property array $params Дополнительные параметры материала
 * @property int $priority Приоритет материала
 * @property array $rights Права доступа
 * @property array $allowed_access_roles Группы имеющие право на доступ
 * @property string $batchGallery Список файлов галереи
 * @property boolean $isNewRecord Признак новой записи
 * @property-read string typeName Текстовый вариант типа материала
 * @property-read string typeSchema Схема микроразметки
 *
 * @property User $user
 * @property CmsCategory[] $cmsCategories
 * @property CmsTag[] $relatedTags
 * @property CmsTag[] $tags
 */
class CmsArticle extends base\CmsArticle
{

    const UNPUBLISHED_STATE = 0;
    const PUBLISHED_STATE = 1;

    public $batchGallery = '';

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

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['selectedCategories', 'each', 'rule' => ['integer']];
        $rules[] = [['selectedTags', 'selectedRelatedTags', 'batchGallery', ], 'string'];
        $rules[] = ['allowed_access_roles', 'each', 'rule' => ['string']];

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['selectedCategories'] = 'Категории';
        $labels['selectedTags'] = 'Теги';
        $labels['selectedRelatedTags'] = 'Теги для связывания материалов';
        $labels['batchGallery'] = 'Массовое добавление фото';
        $labels['allowed_access_roles'] = 'Группы имеющие право на доступ';

        return $labels;
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->user_id = \Yii::$app->user->id;
            $this->created_at = $this->created_at ?? date('Y-m-d H:i:s');
        }

        $this->updated_at = date('Y-m-d H:i:s');
        $this->priority = $this->priority ?? 500;

        if (!empty($this->image)) {
            $imageSize = $this->getImageParams();
            $this->image_width = isset($imageSize[0]) ? $imageSize[0] : ($this->image_width ?? null);
            $this->image_height = isset($imageSize[1]) ? $imageSize[1] : ($this->image_height ?? null);
        } else {
            $this->image_width = null;
            $this->image_height = null;
        }
        $ampized = AMP::encode($this->full, (\Yii::$app->params['frontendFilesRoot'] ?? null));
        $this->amp_full = $ampized['content'];
        $this->medias = $ampized['medias'];

        if (!empty($this->full_lng1)) {
            $ampized = AMP::encode($this->full_lng1, (\Yii::$app->params['frontendFilesRoot'] ?? null));
            $this->amp_full_lng1 = $ampized['content'];
        }

        if (!empty($this->full_lng2)) {
            $ampized = AMP::encode($this->full_lng2, (\Yii::$app->params['frontendFilesRoot'] ?? null));
            $this->amp_full_lng1 = $ampized['content'];
        }

        return parent::beforeValidate();
    }

    public static function publishedQuery () {
        return static::find()
            ->where(['cms_article.published' => static::PUBLISHED_STATE])
            ->andWhere(['<=', 'cms_article.publish_up', date('Y-m-d H:i:s')])
            ->andWhere(['>=', 'cms_article.publish_down', date('Y-m-d H:i:s')]);
    }

    /**
     * Check if article is published and available to user
     * @return bool
     */
    public function isPublished () {
        return $this->published == static::PUBLISHED_STATE &&
            strtotime($this->publish_up) < time() &&
            strtotime($this->publish_down) > time() &&
            $this->isUserAccessable();
    }

    /**
     * Check if article is available to user
     * @return bool
     */
    public function isUserAccessable() {
        if (!in_array('all', $this->allowed_access_roles) && Yii::$app->user->isGuest) return false;
        if (in_array('all', $this->allowed_access_roles)) return true;
        foreach ($this->allowed_access_roles as $group) {
            if (Yii::$app->user->can($group)) {
                return true;
            }
        }
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            /*if (empty($this->rights)) {
                $rights = [
                    'allowedGroups' => ['all', ],
                    'allowedUsers' => ['all', ],
                    'deniedGroups' => [],
                    'deniedUsers' => [],
                ];
                $this->rights = $rights;
            }*/

            if (!empty($this->gallery)) {
                $result = [];
                foreach ($this->gallery as $photo) {
                    $imageSize = [];
                    try {
                        if (file_exists((\Yii::$app->params['frontendFilesRoot'] ?? '') . $photo['path'])) {
                            $imageSize = getimagesize((\Yii::$app->params['frontendFilesRoot'] ?? '') . $photo['path']);
                        }

                        $newData = $photo;
                        $newData['image_width'] = isset($imageSize[0]) ? $imageSize[0] : ($photo['image_width'] ?? null);
                        $newData['image_height'] = isset($imageSize[1]) ? $imageSize[1] : ($photo['image_height'] ?? null);

                        $result[] = $newData;
                    } catch (\Exception $exception) {
                        \Yii::error($exception);
                    }
                }

                $this->gallery = $result;
            }

            if (!empty($this->batchGallery)) {
                $result = !empty($this->gallery) ? $this->gallery : [];

                foreach (explode(', ', $this->batchGallery) as $imagePath) {
                    if (file_exists((\Yii::$app->params['frontendFilesRoot'] ?? '') . $imagePath)) {
                        try {
                            $imageSize = getimagesize((\Yii::$app->params['frontendFilesRoot'] ?? '') . $imagePath);
                            $result[] = [
                                'path' => $imagePath,
                                'title' => '',
                                'caption' => '',
                                'image_width' => isset($imageSize[0]) ? $imageSize[0] : null,
                                'image_height' => isset($imageSize[1]) ? $imageSize[1] : null,
                            ];
                        } catch (\Exception $exception) {
                            \Yii::error($exception);
                        }
                    }
                }

                $this->gallery = $result;
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        $this->setCategories();
        $this->setTags();
        $this->setRelatedTags();
    }

    public function afterFind()
    {
        $this->updateCounters(['hits' => 1]);
        $this->selectedCategories = \yii\helpers\ArrayHelper::getColumn($this->cmsCategories, 'id');
        $this->selectedRelatedTags = implode(',', \yii\helpers\ArrayHelper::getColumn($this->relatedTags, 'title'));
        $this->selectedTags = implode(',', \yii\helpers\ArrayHelper::getColumn($this->tags, 'title'));

        parent::afterFind();
    }

    private function getImageParams() {
        $imageSize = [];
        if (file_exists((\Yii::$app->params['frontendFilesRoot'] ?? '') . $this->image)) {
            $imageSize = getimagesize((\Yii::$app->params['frontendFilesRoot'] ?? '') . $this->image);
        }

        return $imageSize;
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
        return static::TYPE_SCHEMA[$this->type_id] ?? null;
    }

    public function getTypeName() {
        return static::TYPE_NAME[$this->type_id] ?? null;
    }


    /**
     * @return array|bool
     * @deprecated
     */
    public function getFlickrAlbumImagesOld() {
        if (!isset(\Yii::$app->params['flickr']) ||
            empty($this->params['flickrAlbumId']) ||
            empty(\Yii::$app->params['flickr']['enabled']) ||
            empty(\Yii::$app->params['flickr']['apiKey']) ||
            empty($this->params['flickrAlbumId']) ||
            empty(\Yii::$app->params['flickr']['endpoint']))
                return [];

        $cache = \Yii::$app->cache;
        $cacheKey = 'cmsArticle'. $this->id .'FlickrPhotos';
        $result = $cache->get($cacheKey);
        if ($result === false) {
            $result = [];
            foreach (Flickr::albumPhotos($this->params['flickrAlbumId']) as $photo) {
                $result[] = Flickr::photo($photo['id']);
            }

            $cache->set($cacheKey, $result, 600);
        }


        return $result;
    }

    /**
     * @return array
     * @deprecated
     */
    public function getFlickrAlbumImagesInPool() {
        if (!isset(\Yii::$app->params['flickr']) ||
            empty($this->params['flickrAlbumId']) ||
            empty(\Yii::$app->params['flickr']['enabled']) ||
            empty(\Yii::$app->params['flickr']['apiKey']) ||
            empty($this->params['flickrAlbumId']) ||
            empty(\Yii::$app->params['flickr']['endpoint']))
            return [];

        $cache = \Yii::$app->cache;
        $cacheKey = 'cmsArticle'. $this->id .'FlickrPhotos';
        $result = $cache->get($cacheKey);
        if (empty($result)) { // if ($result === false) {
            $result = [];
            $pool = Pool::create(100);
            $logger = \Yii::getLogger();
            foreach (Flickr::albumPhotos($this->params['flickrAlbumId']) as $photo) {
                $pool->add(function() use ($photo) {
                    return \soless\cms\helpers\Flickr::photo($photo['id']);
                })->then(function($output) use (&$result) {
                    $result[] = $output;
                })->catch(function (\Exception $exception) use (&$logger) {
                    $logger->log($exception->getMessage(), 1);
                });
            }

            $cache->set($cacheKey, $result, 3600);
        }


        return $result;
    }

    public function getFlickrAlbumImages() {
        if (!isset(\Yii::$app->params['flickr']) ||
            empty($this->params['flickrAlbumId']) ||
            empty(\Yii::$app->params['flickr']['enabled']) ||
            empty(\Yii::$app->params['flickr']['apiKey']) ||
            empty($this->params['flickrAlbumId']) ||
            empty(\Yii::$app->params['flickr']['endpoint']))
            return [];

        $cache = \Yii::$app->cache;
        $cacheKey = 'cmsArticle'. $this->id .'FlickrPhotos';
        $result = $cache->get($cacheKey);
        if (empty($result)) { // if ($result === false) {
            $mh = curl_multi_init();
            $requests = [];
            $i = 0;
            foreach (Flickr::albumPhotos(
                $this->params['flickrAlbumId'],
                \Yii::$app->params['flickr']['apiKey'],
                \Yii::$app->params['flickr']['endpoint'] . '/rest') as $photo) {
                    $requests[$i] = Flickr::curlPhotoRequest($photo['id'], \Yii::$app->params['flickr']['apiKey'], \Yii::$app->params['flickr']['endpoint'] . '/rest');
                    curl_multi_add_handle($mh, $requests[$i]);
                    $i++;
            }

            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running);

            foreach ($requests as $request) {
                curl_multi_remove_handle($mh, $request);
            }
            curl_multi_close($mh);

            $result = [];
            foreach ($requests as $id => $request) {
                foreach (json_decode(curl_multi_getcontent($request), true)['sizes']['size'] as $size) {
                    $result[$id][$size['label']] = $size;
                }
                $result[$id]['index'] = $id;
            }

            $cache->set($cacheKey, $result, 3600);
        }


        return $result;
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
