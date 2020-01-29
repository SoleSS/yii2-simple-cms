<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \soless\cms\models\CmsArticle;

/* @var $this yii\web\View */
/* @var $searchModel soless\cms\models\CmsArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$get = \Yii::$app->request->get('CmsArticleSearch');

$this->title = 'Материалы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cms-article-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            //'title_lng1',
            //'title_lng2',
            [
                'attribute' => 'type_id',
                'format' => 'text',
                'content' => function(CmsArticle $model){
                    return $model->typeName;
                },
                'filter' => CmsArticle::TYPE_NAME,
            ],
            //'subtitle',
            //'subtitle_lng1',
            //'subtitle_lng2',
            //'image',
            //'image_width',
            //'image_height',
            //'show_image',
            //'intro',
            //'intro_lng1',
            //'intro_lng2',
            //'full:ntext',
            //'full_lng1:ntext',
            //'full_lng2:ntext',
            //'amp_full:ntext',
            //'amp_full_lng1:ntext',
            //'amp_full_lng2:ntext',
            [
                'attribute' => 'published',
                'format' => 'text',
                'content' => function(CmsArticle $model){
                    return $model->published ? 'Опубликовано' : 'Не опубликовано';
                },
                'filter' => ['1' => 'Опубликовано', '0' => 'Не опубликовано'],
            ],
            [
                'attribute' => 'publish_up',
                'label' => 'Начало публикации',
                'content' => function(CmsArticle $model) {
                    return date('d.m.Y H:i', strtotime($model->publish_up));
                },
                'filter' => DatePicker::widget([
                    'name' => 'ArticleSearch[publish_up]',
                    'value' => isset($get['publish_up']) ? $get['publish_up'] : '',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ]),
            ],
            //'publish_down',
            //'user_id',
            //'user_alias',
            //'meta_keywords',
            //'meta_description',
            'hits',
            //'medias',
            [
                'attribute' => 'created_at',
                'label' => 'Начало публикации',
                'content' => function(CmsArticle $model) {
                    return date('d.m.Y H:i', strtotime($model->created_at));
                },
                /*'filter' => DatePicker::widget([
                    'name' => 'ArticleSearch[created_at]',
                    'value' => isset($get['created_at']) ? $get['created_at'] : '',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ]),*/
                'filter' => false,
            ],
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
