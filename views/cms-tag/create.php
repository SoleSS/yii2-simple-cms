<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model soless\cms\models\CmsTag */

$this->title = 'Создать новый тег';
$this->params['breadcrumbs'][] = ['label' => 'Теги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cms-tag-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
