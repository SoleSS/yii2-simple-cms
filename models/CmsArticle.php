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
 * @property string $promo_image_path Промо изображение
 * @property int $promo_image_width Ширина промо изображения
 * @property int $promo_image_height Высота промо изображения
 * @property-read string $authorName Автор
 * @property boolean $forceOverwrite Отключить авто-конвертацию параметров
 * @property-read string|null $introImage Вводное изображение
 * @property array $carousel_params Параметры карусели
 * @property array $carousel_slides Слайды карусели
 * @property array $custom_params Специальные параметры материала
 * @property-read string $ampCarousel
 * @property-read array $cmsCategoriesList
 * @property-read \soless\poll\models\PsPoll $psPoll
 *
 * @property User $user
 * @property CmsCategory[] $cmsCategories
 * @property CmsTag[] $relatedTags
 * @property CmsTag[] $tags
 */
class CmsArticle extends base\CmsArticle
{
    public $forceOverwrite = false;

    const UNPUBLISHED_STATE = false;
    const PUBLISHED_STATE = true;

    const CAROUSEL_POSITION_TOP = 0;
    const CAROUSEL_POSITION_BOTTOM = 1;

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
        if ($this->forceOverwrite) return parent::beforeValidate();

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

        if (!empty($this->promo_image_path)) {
            $imageSize = $this->getPromoImageParams();
            $this->promo_image_width = isset($imageSize[0]) ? $imageSize[0] : ($this->promo_image_width ?? null);
            $this->promo_image_height = isset($imageSize[1]) ? $imageSize[1] : ($this->promo_image_height ?? null);
        } else {
            $this->promo_image_width = null;
            $this->promo_image_height = null;
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
            $this->amp_full_lng2 = $ampized['content'];
        }

        return parent::beforeValidate();
    }

    public function getAuthorName () {
        return !empty($this->user_alias) ? $this->user_alias : $this->user->profile->name;
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

            if (isset($this->params['carousel']) && $this->params['carousel']) {
                if ($this->carousel_params['position'] == static::CAROUSEL_POSITION_TOP) {
                    $this->amp_full = $this->ampCarousel . $this->amp_full;
                } elseif ($this->carousel_params['position'] == static::CAROUSEL_POSITION_BOTTOM) {
                    $this->amp_full = $this->amp_full . $this->ampCarousel;
                }
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
        //$this->updateCounters(['hits' => 1]);
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

    private function getPromoImageParams() {
        $imageSize = [];
        if (file_exists((\Yii::$app->params['frontendFilesRoot'] ?? '') . $this->promo_image_path)) {
            $imageSize = getimagesize((\Yii::$app->params['frontendFilesRoot'] ?? '') . $this->promo_image_path);
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

    public function getIntroImage() {
        $image = null;

        if ($this->show_image && !empty($this->image)) $image = $this->image;

        return $image;
    }

    public function getCmsCategoriesList () {
        return \yii\helpers\ArrayHelper::map($this->cmsCategories, 'id', 'title');
    }

    public function getPsPoll () {
        if (!\Yii::$app->hasModule('ps') || empty($this->params['poll_id'])) return null;

        return \soless\poll\models\PsPoll::findOne((int)$this->params['poll_id']);
    }


    public function getAmpCarousel () {
        if (!$this->params['carousel'] || empty($this->carousel_slides)) return '';

        $slides = [];
        $mobileSlides = [];
        $dots = [];
        foreach ($this->carousel_slides as $i => $slide) {
            $showBackground = false;
            $backgroundImage = null;
            if (file_exists(\Yii::getAlias('@frontend/web'. $slide['background']))) {
                $showBackground = true;
                try {
                    $imageSize = getimagesize(\Yii::getAlias('@frontend/web' . $slide['background']));
                    $slideBgPath = $this->carousel_params['image_path_prefix'] . $slide['background'];
                    $slideBgWidth = !empty($this->carousel_params['slide_image_width']) ? $this->carousel_params['slide_image_width'] : $imageSize[0];
                    $slideBgHeight = !empty($this->carousel_params['slide_image_height']) ? $this->carousel_params['slide_image_height'] : $imageSize[1];
                    $backgroundImage = "<amp-img src=\"{$slideBgPath}\" width=\"{$slideBgWidth}\" height=\"{$slideBgHeight}\" layout=\"responsive\" class=\"slide-background\"></amp-img>";
                } catch (\Exception $exception) {
                    \Yii::error($exception);
                    $backgroundImage = null;
                }
            }

            $ampized = AMP::encode($slide['description'], (\Yii::$app->params['frontendFilesRoot'] ?? null));
            $description = $ampized['content'];

            $slides[] = '
                        <div class="slide desktop-slide-wrap">
                            <div class="background-wrap">
                            '.
                            $backgroundImage
                            .'</div>
                            <div class="inner-wrap">
                                <div class="slide-content">
                                    <div class="title-wrap">'. $slide['title'] .'</div>
                                    <div class="description-wrap">'. $description .'</div>
                                </div>
                            </div>
                        </div>
            ';

            $mobileSlides[] = '
                        <div class="slide mobile-slide-wrap">
                            <div class="image-wrap">'.
                $backgroundImage
                            .'</div>
                            <div class="slide-content">
                                    <div class="title-wrap">'. $slide['title'] .'</div>
                                    <div class="description-wrap">'. $description .'</div>
                            </div>
                        </div>
            ';

            $mobileDots[] = '<div 
                class="dot'. ($i == 0 ? ' active' : '') .'" 
                [class]="carousel_'. $this->carousel_params['id'] .'.activeMobileSlide == '. ($i) .' ? \'dot active\' : \'dot\'" 
                on="tap:AMP.setState({ carousel_'. $this->carousel_params['id'] .': { activeMobileSlide: '. ($i) .' } }),mobile-'. $this->carousel_params['id'] .'.goToSlide(index='. ($i) .')" 
                role="button" 
                tabindex="'. ($i) .'"></div>';

            $dots[] = '<div 
                class="dot'. ($i == 0 ? ' active' : '') .'" 
                [class]="carousel_'. $this->carousel_params['id'] .'.activeSlide == '. ($i) .' ? \'dot active\' : \'dot\'" 
                on="tap:AMP.setState({ carousel_'. $this->carousel_params['id'] .': { activeSlide: '. ($i) .' } }),'. $this->carousel_params['id'] .'.goToSlide(index='. ($i) .')" 
                role="button" 
                tabindex="'. ($i) .'"></div>';
        }

        return '
        <amp-state id="carousel_'. $this->carousel_params['id'] .'">
            <script type="application/json">
                {
                    "activeMobileSlide": 0,
                    "activeSlide": 0
                }
            </script>
        </amp-state>
        <div class="clearfix slider-container desktop-slider xs-hide sm-hide">
            <div class="relative slider-wrap '. $this->carousel_params['additional_slider_classes'] .'">
               <div class="dots-wrap center">'.
                    implode("\n", $dots)
                .'</div>
                <amp-carousel
                        id="'. $this->carousel_params['id'] .'"
                        width="'. $this->carousel_params['width'] .'"
                        height="'. $this->carousel_params['height'] .'"
                        layout="responsive"
                        type="slides"
                        on="slideChange:AMP.setState({ carousel_'. $this->carousel_params['id'] .': { activeSlide: event.index } })"
                >
        '. implode("\n", $slides) .'
                </amp-carousel>
            </div>
        </div>
        
        <div class="clearfix mobile-slider slider-container md-hide lg-hide">
            <div class="relative slider-wrap '. $this->carousel_params['additional_slider_classes'] .'">
               <div class="dots-wrap center">'.
                    implode("\n", $mobileDots)
               .'</div>
                <amp-carousel
                        id="mobile-'. $this->carousel_params['id'] .'"
                        width="'. $this->carousel_params['mobile_width'] .'"
                        height="'. ($this->carousel_params['mobile_height']) .'"
                        layout="responsive"
                        type="slides"
                        on="slideChange:AMP.setState({ carousel_'. $this->carousel_params['id'] .': { activeMobileSlide: event.index } })"
                >
        '. implode("\n", $mobileSlides) .'
                </amp-carousel>
            </div>
        </div>
        ';
    }


}
