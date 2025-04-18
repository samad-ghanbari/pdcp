<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pc.purchase_detail".
 *
 * @property int $id
 * @property int $purchase_id
 * @property string $equipment_type
 * @property string $equipment_brand
 * @property string $equipment_model
 * @property int $quantity
 * @property string $provider
 * @property string|null $equipment_photo
 * @property string|null $descriptions
 */
class PcPurchaseDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pc.purchase_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_id', 'equipment_type', 'equipment_brand', 'equipment_model', 'quantity', 'provider'], 'required'],
            [['purchase_id', 'quantity'], 'default', 'value' => null],
            [['purchase_id', 'quantity'], 'integer'],
            [['equipment_type'], 'string', 'max' => 256,  'message'=>'ورود فیلد الزامی است'],
            [['equipment_brand', 'equipment_model'], 'string', 'max' => 512,  'message'=>'ورود فیلد الزامی است'],
            [['provider', 'equipment_photo'], 'string', 'max' => 1024],
            [['descriptions'], 'string', 'max' => 2048],
            [['purchase_id'], 'exist', 'skipOnError' => true, 'targetClass' => PcViewPurchases::className(), 'targetAttribute' => ['purchase_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchase_id' => 'Purchase ID',
            'equipment_type' => 'نوع تجهیز',
            'equipment_brand' => 'برند تجهیز',
            'equipment_model' => 'مدل تجهیز',
            'quantity' => 'تعداد',
            'provider' => 'تامین کننده',
            'equipment_photo' => 'عکس تجهیز',
            'descriptions' => 'توضیحات فنی',
        ];
    }
}
