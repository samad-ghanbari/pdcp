<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pc.purchase_vendor".
 *
 * @property int $id
 * @property string $vendor
 */
class PcPurchaseVendor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pc.purchase_vendor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vendor'], 'required'],
            [['vendor'], 'string', 'max' => 512],
            [['vendor'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vendor' => 'Vendor',
        ];
    }
}
