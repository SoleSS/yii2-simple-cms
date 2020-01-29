<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel soless\cms\models\CmsArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
            'type_id',
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
            'published',
            'publish_up',
            //'publish_down',
            //'user_id',
            //'user_alias',
            //'meta_keywords',
            //'meta_description',
            'hits',
            //'medias',
            'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
