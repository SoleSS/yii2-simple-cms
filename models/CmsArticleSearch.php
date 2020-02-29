<?php

namespace soless\cms\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use soless\cms\models\CmsArticle;

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
 * @property integer $category_id
 *
 * @property User $user
 * @property CmsCategory[] $cmsCategories
 * @property CmsTag[] $relatedTags
 * @property CmsTag[] $tags
 */
class CmsArticleSearch extends CmsArticle
{
    public $category_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'image_width', 'image_height', 'promo_image_width', 'promo_image_height', 'show_image', 'published', 'user_id', 'hits', 'priority', 'category_id', ], 'integer'],
            [['title', 'title_lng1', 'title_lng2', 'full', 'full_lng1', 'full_lng2', 'amp_full', 'amp_full_lng1', 'amp_full_lng2'], 'string'],
            [['publish_up', 'publish_down', 'created_at', 'updated_at', ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function beforeValidate()
    {
        return true;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CmsArticle::find()
            ->joinWith(['cmsCategories']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            CmsArticle::tableName() . '.id' => $this->id,
            CmsArticle::tableName() . '.type_id' => $this->type_id,
            CmsArticle::tableName() . '.published' => $this->published,
            CmsArticle::tableName() . '.hits' => $this->hits,
            CmsArticle::tableName() . '.user_id' => $this->user_id,
            CmsArticle::tableName() . '.priority' => $this->priority,
            CmsArticle::tableName() . '.show_image' => $this->show_image,
        ]);

        $query->andFilterWhere(['like', CmsArticle::tableName() . '.title', $this->title])
            ->andFilterWhere(['like', CmsArticle::tableName() . '.title_lng1', $this->title_lng1])
            ->andFilterWhere(['like', CmsArticle::tableName() . '.title_lng2', $this->title_lng1])
            ->andFilterWhere(['like', CmsArticle::tableName() . '.full', $this->full])
            ->andFilterWhere(['like', CmsArticle::tableName() . '.full_lng1', $this->full_lng1])
            ->andFilterWhere(['like', CmsArticle::tableName() . '.full_lng2', $this->full_lng2])
            ->andFilterWhere(['like', CmsArticle::tableName() . '.amp_full', $this->amp_full])
            ->andFilterWhere(['like', CmsArticle::tableName() . '.amp_full_lng1', $this->amp_full_lng1])
            ->andFilterWhere(['like', CmsArticle::tableName() . '.amp_full_lng2', $this->amp_full_lng2]);

        if (!empty($this->created_at)) $query->andFilterWhere(['>=', CmsArticle::tableName() . '.created_at', date('Y-m-d', strtotime($this->created_at))]);
        if (!empty($this->updated_at)) $query->andFilterWhere(['>=', CmsArticle::tableName() . '.updated_at', date('Y-m-d', strtotime($this->updated_at))]);
        if (!empty($this->publish_up)) $query->andFilterWhere(['>=', CmsArticle::tableName() . '.publish_up', date('Y-m-d', strtotime($this->publish_up))]);
        if (!empty($this->publish_down)) $query->andFilterWhere(['>=', CmsArticle::tableName() . '.publish_down', date('Y-m-d', strtotime($this->publish_down))]);
        if (!empty($this->category_id)) $query->andFilterWhere([CmsCategory::tableName().'.id' => $this->category_id]);

        return $dataProvider;
    }
}
