<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pc.view_purchases".
 *
 * @property int|null $id
 * @property string|null $title
 * @property int|null $area
 * @property string|null $lom
 * @property string|null $factor
 * @property int|null $creator_id
 * @property string|null $creator
 * @property int|null $created_at
 * @property int|null $modifier_id
 * @property string|null $modifier
 * @property int|null $modified_at
 * @property string|null $purchase_code
 * @property bool|null $done
 */
class PcViewPurchases extends \yii\db\ActiveRecord
{

    public static function primaryKey()
    {
        return ['id'];
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pc.view_purchases';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'area', 'creator_id', 'created_at', 'modifier_id', 'modified_at'], 'default', 'value' => null],
            [['id', 'area', 'creator_id', 'created_at', 'modifier_id', 'modified_at'], 'integer'],
            [['creator', 'modifier'], 'string'],
            [['done'], 'boolean'],
            [['title'], 'string', 'max' => 512,  'message'=>'ورود فیلد الزامی است'],
            [['lom', 'factor', 'purchase_code'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [            
            'id' => 'ID',
            'title' => 'عنوان خرید',
            'area' => 'منطقه',
            'lom' => 'برآورد خرید',
            'factor' => 'فاکتور خرید',
            'creator_id' => 'Creator ID',
            'creator' => 'ثبت کننده',
            'created_at' => 'تاریخ ثبت',
            'modifier_id' => 'Modifier ID',
            'modifier' => 'ویرایش کننده',
            'modified_at' => 'تاریخ ویرایش',
            'purchase_code' => 'شماره ثبت سامانه',
            'done' => 'ثبت نهایی'
        ];
    }
}
