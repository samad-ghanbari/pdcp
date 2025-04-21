<?php

/* @var $this yii\web\View */
/* @var $model */

use yii\bootstrap\ActiveForm;

$this->title = 'PDCP|New Purchase Detail';
use yii\helpers\Html;
Yii::$app->formatter->nullDisplay = "";
$session = Yii::$app->session;


?>
    <p class="backicon">
        <a href="<?= Yii::$app->request->referrer ?>"><i class="fa fa-chevron-left fa-2x" style="color:white;position: relative;"></i></a>
    </p>
    <div class="topic-cover bg-gradient">
            <img src="<?= Yii::$app->request->baseUrl.'/web/images/cart.png'; ?>" style="width:200px;display:block; margin:5px auto;">
            <h3 style="text-align: center; color:#fff;direction: rtl;">ثبت جزئیات خرید تجهیزات</h3>
    <br />


    <hr style="border-top:1px solid white;"/>
    <?php
    $form = ActiveForm::begin(['action'=>Yii::$app->request->baseUrl."/purchase/insert_detail",
        'id'=>"recForm",
        'options'=>['style'=>'direction:rtl;max-width:500px;min-width:100px; margin:auto']
    ]); ?>

    <!-- id, purchase_id, equipment_type, equipment_brand, equipment_model, quantity, provider, equipment_photo, descriptions -->


    <?= $form->field($model, 'purchase_id')->hiddenInput()->label(false); ?>

    <?= $form->field($model, 'equipment_type', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;']])->dropDownList($types, ['style' => "direction:rtl"]) ?>
    <?= $form->field($model, 'equipment_brand', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;']])->dropDownList($vendors, ['style' => "direction:rtl"]) ?>
    <?= $form->field($model, 'equipment_model', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;']])->textInput(['maxlength' => true, 'style'=>"direction:rtl"]) ?>
    <?= $form->field($model, 'quantity', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;']])->input('number', ['style' => "direction:rtl"]) ?>
    <?= $form->field($model, 'provider', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;']])->textInput(['maxlength' => true, 'style'=>"direction:rtl"]) ?>
    <?= $form->field($model, 'equipment_photo', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;']])->fileInput(['name' => 'photo_file']); ?>
    <?= $form->field($model, 'descriptions', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;']])->textarea(['rows' => 4, 'style' => "direction:rtl"]) ?>


    <div class="form-group" style="clear:both;">
        <?= Html::submitButton('تایید', ['class' => 'btn btn-success pull-left', 'style'=>"width: 200px;"]) ?>
    </div>
    <?php ActiveForm::end(); ?>

        <br style="clear:both; margin-bottom:50px;" />
    </div>

<?php

$script =<<< JS

JS;
$this->registerJs($script, Yii\web\View::POS_END);
?>