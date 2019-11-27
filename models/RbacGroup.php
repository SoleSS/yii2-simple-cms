<?php
namespace soless\cms\models;

class RbacGroup extends \yii\db\ActiveRecord {
    public static function tableName()
    {
        return 'auth_item';
    }

    public static function asArray() {
        return static::find()
            ->select('name')
            ->column();
    }
}