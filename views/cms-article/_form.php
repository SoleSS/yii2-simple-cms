<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model soless\cms\models\CmsArticle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cms-article-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title_lng1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title_lng2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type_id')->textInput() ?>

    <?= $form->field($model, 'subtitle')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subtitle_lng1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subtitle_lng2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image_width')->textInput() ?>

    <?= $form->field($model, 'image_height')->textInput() ?>

    <?= $form->field($model, 'show_image')->textInput() ?>

    <?= $form->field($model, 'intro')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'intro_lng1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'intro_lng2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'full')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'full_lng1')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'full_lng2')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'amp_full')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'amp_full_lng1')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'amp_full_lng2')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'published')->textInput() ?>

    <?= $form->field($model, 'publish_up')->textInput() ?>

    <?= $form->field($model, 'publish_down')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'user_alias')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'meta_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hits')->textInput() ?>

    <?= $form->field($model, 'medias')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
