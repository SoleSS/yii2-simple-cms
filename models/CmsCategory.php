<?php

namespace soless\cms\models;

/**
 * This is the model class for table "cms_category".
 *
 * @property int $id
 * @property string $title Название категории
 * @property string $description Описание категории
 * @property string $created_at Дата создания
 * @property string $updated_at Дата обновления
 * @property array $rights Права доступа
 *
 * @property CmsArticleCategory[] $cmsArticleCategories
 * @property CmsArticle[] $cmsArticles
 */
class CmsCategory extends base\CmsCategory
{
    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        $this->updated_at = date('Y-m-d H:i:s');

        return parent::beforeValidate();
    }

    public function isUserAccessable() {
        if (!in_array('all', $this->rights->allowedUsers) && Yii::$app->user->isGuest) return false;

        if (in_array(Yii::$app->user->id, $this->rights->deniedUsers)) {
            return false;
        }
        foreach ($this->rights->deniedGroups as $group) {
            if (Yii::$app->user->can($group)) {
                return false;
            }
        }

        if (in_array('all', $this->rights->allowedUsers) || in_array(Yii::$app->user->id, $this->rights->allowedUsers)) {
            return true;
        }
        if (in_array('all', $this->rights->allowedGroups)) return true;
        foreach ($this->rights->allowedGroups as $group) {
            if (Yii::$app->user->can($group)) {
                return true;
            }
        }
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if (empty($this->rights)) {
                $rights = [
                    'allowedGroups' => ['all',],
                    'allowedUsers' => ['all',],
                    'deniedGroups' => [],
                    'deniedUsers' => [],
                ];
                $this->rights = $rights;
            }

            return true;
        } else {
            return false;
        }
    }

    public static function asArray() {
        return \yii\helpers\ArrayHelper::map(static::find()->select(['id', 'title'])->asArray()->all(), 'id', 'title');
    }
}
