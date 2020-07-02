<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \soless\cms\models\CmsArticle;
use \kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel soless\cms\models\CmsArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$get = \Yii::$app->request->get('CmsArticleSearch');

$this->title = 'Материалы';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .grid-view td {
        white-space: normal;
    }
</style>
<div class="cms-article-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'contentOptions' => ['style' => 'width:50px'],
            ],
            [
                'attribute' => 'title',
                'format' => 'text',
                'content' => function(CmsArticle $model) {
                    return \yii\helpers\StringHelper::truncate($model->title, 70);
                }
            ],
            //'title_lng1',
            //'title_lng2',
            /*[
                'attribute' => 'type_id',
                'format' => 'text',
                'content' => function(CmsArticle $model){
                    return $model->typeName;
                },
                'filter' => CmsArticle::TYPE_NAME,
            ],*/
            [
                'attribute' => 'category_id',
                'contentOptions' => ['style' => 'width:150px'],
                'label' => 'Категория',
                'content' => function(CmsArticle $model) {
                    return implode(', ', $model->cmsCategoriesList);
                },
                'filter' => \soless\cms\models\CmsCategory::asArray(),
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
                'contentOptions' => ['style' => 'width:120px'],
                'format' => 'text',
                'content' => function(CmsArticle $model){
                    return $model->published ? 'Опубликовано' : 'Не опубликовано';
                },
                'filter' => ['1' => 'Опубликовано', '0' => 'Не опубликовано'],
            ],
            [
                'attribute' => 'publish_up',
                'contentOptions' => ['style' => 'width:150px'],
                'label' => 'Начало публикации',
                'content' => function(CmsArticle $model) {
                    return date('d.m.Y H:i', strtotime($model->publish_up));
                },
                'filter' => DatePicker::widget([
                    'name' => 'CmsArticleSearch[publish_up]',
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
            [
                'attribute' => 'hits',
                'contentOptions' => ['style' => 'width:60px'],
            ],
            //'medias',
            [
                'attribute' => 'created_at',
                'contentOptions' => ['style' => 'width:150px'],
                'label' => 'Дата создания',
                'content' => function(CmsArticle $model) {
                    return date('d.m.Y H:i', strtotime($model->created_at));
                },
                'filter' => DatePicker::widget([
                    'name' => 'CmsArticleSearch[created_at]',
                    'value' => isset($get['created_at']) ? $get['created_at'] : '',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                ]),
            ],
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
