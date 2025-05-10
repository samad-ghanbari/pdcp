<?php

/* @var $this yii\web\View */
/* @var $model */

use yii\bootstrap\ActiveForm;

$this->title = 'PDCP|update Purchase';
use yii\helpers\Html;
Yii::$app->formatter->nullDisplay = "";
$session = Yii::$app->session;


?>
    <p class="backicon">
        <a href="<?= Yii::$app->request->referrer ?>"><i class="fa fa-chevron-left fa-2x" style="color:white;position: relative;"></i></a>
    </p>
    <div class="topic-cover bg-gradient">
            <img src="<?= Yii::$app->request->baseUrl.'/web/images/cart.png'; ?>" style="width:200px;display:block; margin:5px auto;">
            <h3 style="text-align: center; color:#fff;direction: rtl;">ویرایش خرید تجهیزات</h3>
    <br />


    <hr style="border-top:1px solid white;"/>
    <?php
    $form = ActiveForm::begin(['action'=>Yii::$app->request->baseUrl."/purchase/update",
        'id'=>"recForm",
        'options'=>['style'=>'direction:rtl;max-width:500px;min-width:100px; margin:auto']
    ]); ?>

    <!-- id, title, area, lom, factor, creator, create_at, modifier, modified_at, purchase_code, done -->

    <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>

    <?= $form->field($model, 'area', ['labelOptions' => ['style' => 'color:white;'],'options'=>['style'=>'display:block; width: 30%;']])->dropDownList($areas, ['onchange'=>"areaChanged();",'id'=>'areaCB']); ?>

    <?= $form->field($model, 'title', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;']])->dropDownList($titles, ['style' => 'direction:rtl']) ?>

    <?= $form->field($model, 'lom', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;']])->fileInput(['name' => 'lom_file']); ?>
    <?= $form->field($model, 'factor', ['labelOptions' => ['style' => 'color:white;'], 'options'=>['style'=>'display:block; width: 100%;', 'class'=>'']])->fileInput(["name" => "factor_file"]); ?>

    <div class="form-group" style="clear:both;">
        <?= Html::submitButton('تایید', ['class' => 'btn btn-success pull-left', 'style' => 'width: 200px;']) ?>
    </div>
    <?php ActiveForm::end(); ?>

        <br style="clear:both; margin-bottom:50px;" />
    </div>

<?php

$script =<<< JS

JS;
$this->registerJs($script, Yii\web\View::POS_END);
?>
