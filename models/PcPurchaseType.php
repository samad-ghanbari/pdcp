<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pc.purchase_type".
 *
 * @property int $id
 * @property string $types
 */
class PcPurchaseType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pc.purchase_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['types'], 'required'],
            [['types'], 'string', 'max' => 512],
            [['types'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'types' => 'Types',
        ];
    }
}
