<?php

use \yii\helpers\Html;
use \yii\widgets\ActiveForm;
use \mihaildev\elfinder\InputFile;
use \mihaildev\elfinder\ElFinder;
use \mihaildev\ckeditor\CKEditor;
use \kartik\datetime\DateTimePicker;
use \soless\tagEditor\TagEditor;
use \yii\helpers\Url;
use \soless\cms\models\CmsArticle;


/* @var $this yii\web\View */
/* @var $model soless\cms\models\CmsArticle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cms-article-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type_id')->dropDownList(\soless\cms\models\CmsArticle::TYPE_NAME) ?>

    <?= $form->field($model, 'subtitle')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'priority')->textInput(['placeholder' => 500]) ?>

    <?php echo $form->field($model, 'image')->widget(InputFile::className(), [
        'language'      => 'ru',
        'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
        'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
        'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
        'options'       => ['class' => 'form-control'],
        'buttonOptions' => ['class' => 'btn btn-default btn-rounded'],
        'buttonName' => 'Обзор',
        'multiple'      => false       // возможность выбора нескольких файлов
    ]); ?>

    <?= $form->field($model, 'show_image')->checkbox([]); ?>

    <?php echo $form->field($model, 'promo_image_path')->widget(InputFile::className(), [
        'language'      => 'ru',
        'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
        'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
        'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
        'options'       => ['class' => 'form-control'],
        'buttonOptions' => ['class' => 'btn btn-default btn-rounded'],
        'buttonName' => 'Обзор',
        'multiple'      => false       // возможность выбора нескольких файлов
    ]); ?>

    <?= $form->field($model, 'intro')->widget(CKEditor::className(), [
        'editorOptions' => ElFinder::ckeditorOptions('elfinder', [
            'preset' => 'basic',
            'inline' => false,
            'height' => '100px',
            'allowedContent' => false,
            'removePlugins' => 'image',
        ]),
    ]); ?>

    <?= $form->field($model, 'full')->widget(CKEditor::className(), [
        'editorOptions' => ElFinder::ckeditorOptions('elfinder', [
            'preset' => 'standard',
            'inline' => false,
            'height' => '300px',
            'allowedContent' => true,
        ]),
    ]); ?>

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#collapse1">Дополнительный язык</a>
                </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body">
                    <?= $form->field($model, 'title_lng1')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'subtitle_lng1')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'intro_lng1')->widget(CKEditor::className(), [
                        'editorOptions' => ElFinder::ckeditorOptions('elfinder', [
                            'preset' => 'basic',
                            'inline' => false,
                            'height' => '100px',
                            'allowedContent' => false,
                            'removePlugins' => 'image',
                        ]),
                    ]); ?>
                    <?= $form->field($model, 'full_lng1')->widget(CKEditor::className(), [
                        'editorOptions' => ElFinder::ckeditorOptions('elfinder', [
                            'preset' => 'standard',
                            'inline' => false,
                            'height' => '300px',
                            'allowedContent' => true,
                        ]),
                    ])->label(false); ?>
                </div>
                <div class="panel-footer"></div>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'selectedCategories')->checkboxList(\soless\cms\models\CmsCategory::asArray()); ?>

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#collapse2">Дополнительный язык 2</a>
                </h4>
            </div>
            <div id="collapse2" class="panel-collapse collapse">
                <div class="panel-body">
                    <?= $form->field($model, 'title_lng2')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'subtitle_lng2')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'intro_lng2')->widget(CKEditor::className(), [
                        'editorOptions' => ElFinder::ckeditorOptions('elfinder', [
                            'preset' => 'basic',
                            'inline' => false,
                            'height' => '100px',
                            'allowedContent' => false,
                            'removePlugins' => 'image',
                        ]),
                    ]); ?>
                    <?= $form->field($model, 'full_lng2')->widget(CKEditor::className(), [
                        'editorOptions' => ElFinder::ckeditorOptions('elfinder', [
                            'preset' => 'standard',
                            'inline' => false,
                            'height' => '300px',
                            'allowedContent' => true,
                        ]),
                    ])->label(false); ?>
                </div>
                <div class="panel-footer">Panel Footer</div>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'published')->dropDownList([
        CmsArticle::UNPUBLISHED_STATE => 'Не опубликовано',
        CmsArticle::PUBLISHED_STATE => 'Опубликовано',
    ]) ?>

    <?= $form->field($model, 'created_at')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Дата создания материала'],
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]); ?>

    <?= $form->field($model, 'publish_up')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Начало публикации ...'],
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]); ?>

    <?= $form->field($model, 'publish_down')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Окончание публикации ...'],
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]); ?>

    <?= $form->field($model, 'user_alias')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'selectedRelatedTags')->widget(TagEditor::className(), [
        'tagEditorOptions' => [
            'forceLowercase' => false,
            'autocomplete' => [
                'source' => Url::toRoute(['/cms/cms-tag/suggest'])
            ],
        ]
    ]) ?>

    <?= $form->field($model, 'selectedTags')->widget(TagEditor::className(), [
        'tagEditorOptions' => [
            'forceLowercase' => false,
            'autocomplete' => [
                'source' => Url::toRoute(['/cms/cms-tag/suggest'])
            ],
        ]
    ]) ?>

    <?php echo $form->field($model, 'batchGallery')->widget(InputFile::className(), [
        'language'      => 'ru',
        'controller'    => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
        'filter'        => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
        'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
        'options'       => ['class' => 'form-control'],
        'buttonOptions' => ['class' => 'btn btn-default btn-rounded'],
        'buttonName' => 'Обзор',
        'multiple'      => true        // возможность выбора нескольких файлов
    ]); ?>

    <?= $form->field($model, 'gallery')->widget(\unclead\multipleinput\MultipleInput::class, [
        //'max' => 4,
        'min' => 0,
        'columns' => [
            [
                'name' => 'path',
                'title' => 'Изображение',
                'type' => InputFile::class,
                'options' => [
                    'language'   => 'ru',
                    'controller' => 'elfinder',
                    'filter'     => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                    'multiple'   => false,
                    'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',

                    'options' => [
                        'class' => 'form-control',
                    ],
                    'buttonOptions' => ['class' => 'btn btn-default btn-rounded'],
                    'buttonName' => 'Обзор',
                ],
            ],
            [
                'name' => 'title',
                'title' => 'Название',
            ],
            [
                'name' => 'caption',
                'title' => 'Описание',
            ],
        ]
    ]);
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">Дополнительные параметры</div>
        <div class="panel-body">
            <?= $form->field($model, 'params[ytvideo]')->checkbox([], false)->label('YouTube видео'); ?>
            <?= $form->field($model, 'params[iframe]')->checkbox([], false)->label('IFrame'); ?>
            <?= $form->field($model, 'params[accordion]')->checkbox([], false)->label('AMP Аккордион'); ?>

            <div class="form-inline">
                <?= $form->field($model, 'params[carousel]')
                    ->checkbox([], false)
                    ->label('AMP Карусель'); ?>
                <a class="btn btn-primary btn-sm" role="button" data-toggle="collapse" href="#carouselParams">
                    <span class="glyphicon glyphicon-cog"></span>
                </a>
            </div>

            <div class="collapse <?= $model->params['carousel'] ? 'in' : '' ?>" id="carouselParams">
                <div class="card card-body">
                    <?= $form->field($model, 'carousel_params[position]')->dropDownList([
                        CmsArticle::CAROUSEL_POSITION_TOP => 'Над материалом',
                        CmsArticle::CAROUSEL_POSITION_BOTTOM => 'Под материалом',
                    ])->label('Позиция карусели') ?>

                    <?= $form->field($model, 'carousel_params[id]')
                        ->textInput(['maxlength' => false])
                        ->label('id карусели') ?>

                    <?= $form->field($model, 'carousel_params[additional_slider_classes]')
                        ->textInput(['maxlength' => false])
                        ->label('Доп. классы карусели') ?>

                    <?= $form->field($model, 'carousel_params[width]')
                        ->textInput(['maxlength' => false])
                        ->label('Ширина карусели') ?>

                    <?= $form->field($model, 'carousel_params[height]')
                        ->textInput(['maxlength' => false])
                        ->label('Ширина карусели') ?>

                    <?= $form->field($model, 'carousel_params[mobile_width]')
                        ->textInput(['maxlength' => false])
                        ->label('Ширина мобильной карусели') ?>

                    <?= $form->field($model, 'carousel_params[mobile_height]')
                        ->textInput(['maxlength' => false])
                        ->label('Ширина мобильной карусели') ?>

                    <?= $form->field($model, 'carousel_params[image_path_prefix]')
                        ->textInput(['maxlength' => false])
                        ->label('Префикс пути к файлу (для ресайза)') ?>

                    <?= $form->field($model, 'carousel_params[slide_image_width]')
                        ->textInput(['maxlength' => false])
                        ->label('Ширина изображения (после ресайза)') ?>

                    <?= $form->field($model, 'carousel_params[slide_image_height]')
                        ->textInput(['maxlength' => false])
                        ->label('Высота изображения (после ресайза)') ?>

                    <?= $form->field($model, 'carousel_slides')->widget(\unclead\multipleinput\MultipleInput::class, [
                        'min' => 0,
                        'columns' => [
                            [
                                'name' => 'background',
                                'title' => 'Изображение',
                                'type' => InputFile::class,
                                'options' => [
                                    'language'   => 'ru',
                                    'controller' => 'elfinder',
                                    'filter'     => 'image',    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                                    'multiple'   => false,
                                    'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',

                                    'options' => [
                                        'class' => 'form-control',
                                    ],
                                    'buttonOptions' => ['class' => 'btn btn-default btn-rounded'],
                                    'buttonName' => 'Обзор',
                                ],
                            ],
                            [
                                'name' => 'title',
                                'title' => 'Название',
                            ],
                            [
                                'name'  => 'description',
                                'type'  => CKEditor::className(),
                                'title' => 'Описание',
                                'options' => [
                                    'editorOptions' => [
                                        'preset' => 'basic',
                                        'inline' => false,
                                        'height' => '100px',
                                        'allowedContent' => true,
                                    ]
                                ],
                            ],
                        ]
                    ])->label('Слайды');
                    ?>
                </div>
            </div>

            <?= $form->field($model, 'params[bind]')->checkbox([], false)->label('AMP Bind'); ?>
            <?php if (isset(\Yii::$app->params['flickr'])) : ?>
                <?= $form->field($model, 'params[flickrAlbumId]')->textInput(['maxlength' => true])->label('Id альбома Flickr') ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset(\Yii::$app->params['cmsRights'])) : ?>
        <div class="panel panel-default">
            <div class="panel-heading">Параметры доступа</div>
            <div class="panel-body">
                <?= $form->field($model, 'allowed_access_roles')->checkboxList(\soless\cms\models\RbacGroup::asArray()); ?>
            </div>
        </div>
    <?php endif; ?>



    <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'meta_description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
