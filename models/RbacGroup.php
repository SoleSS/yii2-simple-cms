<?php
namespace soless\cms\models;

class RbacGroup extends \yii\db\ActiveRecord {
    public static function tableName()
    {
        return 'auth_item';
    }

    public static function asArray() {
        $result = [];
        foreach (array_merge(static::find()
            ->select('name')
            ->column(), 'all') as $item) {
                $result[$item] = $item;
        }

        return $result;
    }
}