<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model soless\cms\models\CmsTag */

$this->title = 'Update Cms Tag: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Cms Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cms-tag-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
