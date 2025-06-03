<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pc.purchases".
 *
 * @property int $id
 * @property int|null $area
 * @property string $lom
 * @property string $factor
 * @property int $creator_id
 * @property int $created_at
 * @property int|null $modifier_id
 * @property int|null $modified_at
 * @property string|null $purchase_code
 * @property bool $done
 * @property string $title
 */
class PcPurchases extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pc.purchases';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area', 'creator_id', 'created_at', 'modifier_id', 'modified_at'], 'default', 'value' => null],
            [['area', 'creator_id', 'created_at', 'modifier_id', 'modified_at'], 'integer'],
            [['lom', 'factor', 'creator_id', 'created_at', 'title'], 'required'],
            [['done'], 'boolean'],
            [['lom', 'factor', 'purchase_code'], 'string', 'max' => 1024],
            [['title'], 'string', 'max' => 512],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => PcUsers::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['modifier_id'], 'exist', 'skipOnError' => true, 'targetClass' => PcUsers::className(), 'targetAttribute' => ['modifier_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'area' => 'منطقه',
            'lom' => 'لیست تجهیزات',
            'factor' => 'فاکتور خرید',
            'creator_id' => 'ثبت کننده',
            'created_at' => 'زمان ثبت',
            'modifier_id' => 'ویرایش',
            'modified_at' => 'زمان ویرایش',
            'purchase_code' => 'شماره ثبت سامانه',
            'done' => 'ثبت نهایی',
            'title' => 'عنوان خرید',
        ];
    }
}
