<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model soless\cms\models\CmsArticle */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Материалы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cms-article-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Уверены что хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            //'title_lng1',
            //'title_lng2',
            //'type_id',
            'subtitle',
            //'subtitle_lng1',
            //'subtitle_lng2',
            'image',
            //'image_width',
            //'image_height',
            //'show_image',
            'intro:html',
            //'intro_lng1',
            //'intro_lng2',
            'full:html',
            //'full_lng1:ntext',
            //'full_lng2:ntext',
            //'amp_full:ntext',
            //'amp_full_lng1:ntext',
            //'amp_full_lng2:ntext',
            'published',
            'publish_up',
            'publish_down',
            'user_id',
            'user_alias',
            'meta_keywords',
            'meta_description',
            'hits',
            [
                'attribute' => 'medias',
                'format' => 'html',
                'value' => '<pre>'. print_r($model->medias, 1) .'</pre>',
            ],
            [
                'attribute' => 'gallery',
                'format' => 'html',
                'value' => '<pre>'. print_r($model->gallery, 1) .'</pre>',
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
