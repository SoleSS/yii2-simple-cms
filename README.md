# yii2-simple-cms

## Installation

composer require --prefer-dist soless/yii2-simple-cms "*"

php yii migrate/up --migrationPath=@vendor/soless/yii2-simple-cms/migrations

add to config:
```
    'modules' => [
        'cms' => [
            'class' => '\soless\cms\Module',
        ]
    ],
```

## Available actions:

cms/cms-article

cms/cms-category

cms/cms-tag