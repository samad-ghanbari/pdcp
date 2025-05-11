<?php

/* @var $this yii\web\View */
/* @var  $model \app\models\PcViewPurchases */
/* @var $model_detail \app\models\PcPurchaseDetail */
/* @var $created_at */
/* @var $modified_at */


$this->title = 'PDCP|View';
use yii\helpers\Html;
Yii::$app->formatter->nullDisplay = "";
use yii\widgets\Pjax;
use yii\grid\GridView;

?>

    <p class="backicon">
        <a href="<?= 'index' ?>"><i class="fa fa-chevron-left fa-2x" style="color:white;position: relative;"></i></a>
    </p>

    <div class="topic-cover bg-gradient">
        <h2 style="text-align: center; color:#fff;">  اطلاعات خرید </h2>

            <table class="table table-hover" style="width:80%; margin: auto;direction:rtl; background-color:#fff ; font-size: 16px;">

                <!-- purchase-->
                <tr>
                        <td  style="color: black; background-color: orange;text-align: center; width: 300px;">
                            منطقه
                        </td>
                        <td>
                            <?= ($model["area"] > 1)? $model["area"] : "-"; ?>
                        </td>
                </tr>

                <tr>
                        <td  style="color: black; background-color: orange;text-align: center;">
                            عنوان خرید
                        </td>
                        <td>
                            <?= $model["title"]; ?>
                        </td>
                </tr>

                <tr>
                        <td  style="color: black; background-color: orange;text-align: center;">
                            ثبت کننده
                        </td>
                        <td>
                            <?= $model["creator"] . " - ".$created_at; ?>
                        </td>
                </tr>

                <tr>
                        <td  style="color: black; background-color: orange;text-align: center;">
                            ویرایش اخیر    
                        </td>
                        <td>
                        <?= $model["modifier"] . " - ".$modified_at; ?>
                        </td>
                </tr>
                <tr>
                        <td  style="color: black; background-color: orange;text-align: center;">
                                فایل لیست تجهیزات
                        </td>
                        <td>
                            <?php
                                $fileUrl = Yii::$app->request->baseUrl . '/uploads/' . $model['lom'];
                                if(empty(!$model["lom"]))
                                {
                                    echo Html::a('<i class="fa fa-download text-success"></i>', $fileUrl, ['title' => 'دانلود فایل', 'target' => '_blank']);
                                }
                                else
                                {
                                    echo "<i class='fa fa-times text-danger'></i>";
                                }
                            ?>
                        </td>
                </tr>
                <tr>
                        <td  style="color: black; background-color: orange;text-align: center;">
                            فاکتور خرید
                        </td>
                        <td>
                        <?php
                                $fileUrl = Yii::$app->request->baseUrl . '/uploads/' . $model['factor'];
                                if(empty(!$model["factor"]))
                                {
                                    echo Html::a('<i class="fa fa-download text-success"></i>', $fileUrl, ['title' => 'دانلود فایل', 'target' => '_blank']);
                                }
                                else
                                {
                                    echo "<i class='fa fa-times text-danger'></i>";
                                }
                            ?>
                        </td>
                </tr>

                <tr>
                        <td  style="color: black;  background-color: orange;text-align: center;">
                            شماره ثبت در سامانه   
                        </td>
                        <td>
                        <?= $model["purchase_code"]; ?>
                        </td>
                </tr>

                <tr><td colspan="2" style="color: black; background-color: orange;">
                    <?php if (empty($model['purchase_code'])): ?>
                        <div style="text-align: center;">
                            <?= Html::a('<i class="fa fa-edit text-success"></i>', Yii::$app->request->baseUrl . '/purchase/update_page?id=' . $model->id, ['title' => 'ویرایش ']); ?>
                        </div>
                    <?php endif; ?>

                </td></tr>
            </table>

            <br />
            <br style="clear: both;" />
            <br />

            <div id="gwcontainer" style=" background-color:#fff; direction:rtl; height: 100%; font-size: 14px;">

                <?php
                if (empty($model["purchase_code"])) {
                    echo Html::a('<i class="fa fa-plus text-white" ></i><span > افزودن مشخصات تجهیزات </span>', ['new_detail', 'id'=>$model['id']], ['style'=>'display:block; margin: auto; padding:0; line-height: 60px; width:200px; height: 60px; border-radius:0; background-color: #c71585; color: white;','class'=>'btn']);
                }
                    echo GridView::widget([
                        'tableOptions'=>['id'=>"perchase-Table", 'class'=>'table table-striped table-bordered table-hover text-center '],
                        //'headerRowOptions'=>['class'=>'bg-info text-center'],
                        'rowOptions' =>function ($model_detail, $key, $index, $grid) {return ['id'=>'row'.$model_detail['id'], 'pid'=>$model_detail["id"], 'class'=>'table_row', 'style'=>"cursor:pointer", 'onclick'=>'activateRow(this.getAttribute("id"));'];},
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'filterRowOptions' =>['style'=>"direction:ltr"],
                        //'summary' => 'نمایش <b>{begin} تا {end}</b> از <b>{totalCount}</b> ',
                        'summary'=>"",
                        //        'pager'=>['options'=>['align'=>"center", 'class'=>"pagination"]],
                        'layout' => "{summary}\n{items}\n<div align='center' >{pager}</div>",
                        'columns' => [
                            //id, purchase_id, equipment_type, equipment_brand, equipment_model, quantity, provider, equipment_photo, descriptions
                            //0
                            [
                                'attribute' =>'id',
                                'visible'=>0,
                            ],
                            //1
                            [
                                'attribute' =>'equipment_type',
                                'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                'contentOptions' => ['class' => 'text-center text-success', 'style'=>"vertical-align: middle;min-width:150px;font-size:16px;", 'title'=>'نوع تجهیز'],
                            ],
                            [
                                'attribute' =>'equipment_brand',
                                'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                'contentOptions' => ['class' => 'text-center text-success', 'style'=>"vertical-align: middle;min-width:150px;font-size:16px;", 'title'=>'برند تجهیز'],
                            ],
                            [
                                'attribute' =>'equipment_model',
                                'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                'contentOptions' => ['class' => 'text-center text-success', 'style'=>"vertical-align: middle;min-width:150px;font-size:16px;", 'title'=>'مدل تجهیز'],
                            ],
                            [
                                'attribute' =>'quantity',
                                'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;  width:50px;'],
                                'contentOptions' => ['class' => 'text-center text-success', 'style'=>"vertical-align: middle;min-width:150px;font-size:16px;", 'title'=>'تعداد'],
                            ],
                            [
                                'attribute' =>'provider',
                                'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                'contentOptions' => ['class' => 'text-center text-success', 'style'=>"vertical-align: middle;min-width:150px;font-size:16px;", 'title'=>'تامین کننده'],
                            ],
                            //3
                            [
                                'attribute' =>'equipment_photo',
                                'filter' => false,
                                'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;  width:100px;'],
                                'contentOptions' => ['class' => 'text-center', 'style'=>"vertical-align: middle;min-width:80px;", 'title'=>'تصویر تجهیز'],
                                'format'=>'html',
                                'value'=>function($data){
                                    if(empty($data['equipment_photo'])) {
                                        return "<i class='fa fa-times text-danger'></i>";
                                    } else {
                                        $fileUrl = Yii::$app->request->baseUrl . '/uploads/' . $data['equipment_photo'];
                                        return Html::a('<i class="fa fa-download text-success"></i>', $fileUrl, ['title' => 'دانلود فایل', 'target' => '_blank']);
                                    }
                                }
                            ],
                            //۴
                            [
                                'attribute' =>'descriptions',
                                'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                'contentOptions' => ['class' => 'text-center text-success', 'style'=>"vertical-align: middle;min-width:150px;font-size:16px;", 'title'=>'کد خرید'],
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{project_detail}&nbsp;&nbsp;&nbsp;&nbsp;{update}&nbsp;&nbsp;&nbsp;&nbsp;{delete}',
                                'header' => "عملیات",
                                'visible' => empty($model["purchase_code"]),
                                'headerOptions' => ['class' => 'bg-success text-center text-info', 'style'=>'height:80px; line-height:80px;'],
                                'buttons' => [
                                    
                                    'update' => function($url, $model, $key) {
                                        return Html::a('<i class="fa fa-edit text-success"></i>', $url, ['title' => 'ویرایش ']);
                                    },
                                    'delete' => function($url, $model, $key) {
                                        return Html::a('<i class="fa fa-trash text-danger"></i>', $url, [
                                            'title' => 'حذف',
                                            'data-confirm' => 'آیا از حذف این گزینه مطمئن هستید؟',
                                            'data-method' => 'post',
                                        ]);
                                    },
                                ],
                                'urlCreator' => function ($action, $model, $key, $index) {
                                    
                                    if ($action === 'update') {
                                        return Yii::$app->request->baseUrl . '/purchase/update_detail_page?id=' . $model->id;
                                    }
                                    if ($action === 'delete') {
                                        return Yii::$app->request->baseUrl . '/purchase/delete_detail?id=' . $model->id;
                                    }
                                }
                            ],
                        ],
                    ]);

                    if (empty($model["purchase_code"])) {
                        echo Html::a('<i class="fa fa-check text-white" ></i><span class="dis-text" style="display: inline-block; vertical-align: middle;"> ثبت نهایی </span>', ['done', 'id' => $model['id']], [
                            'style' => 'display:block; float:left; width:100px; height: 40px; text-align: center; line-height: 40px;padding:0;',
                            'class' => 'btn btn-primary',
                            'data-confirm' => 'آیا از ثبت نهایی این خرید مطمئن هستید؟',
                        ]);
                    }
                ?>
            </div>
            <br /><br />
            <hr style="border: 1px solid #ccc; width: 80%; margin: auto;" />
            <br /><br />
            <?php
            if (empty($model["purchase_code"])) {
                echo Html::a('<i class="fa fa-times text-white" ></i><span > حذف خرید </span>', ['delete', 'id' => $model['id']], [
                    'style' => 'display:block; margin: 0 auto; width:100px; height: 40px; text-align: center; line-height: 40px; padding:0;',
                    'class' => 'btn btn-danger',
                    'data-confirm' => 'آیا از حذف این خرید مطمئن هستید؟',
                ]);
            }
            ?>

            

    </div>


<?php
$script =<<< JS

function activateRow(rowId)
{
    $(".selectedRowPurchase").removeClass("selectedRowPurchase");
    $("#"+rowId).addClass("selectedRowPurchase");
}

JS;
$this->registerJs($script, Yii\web\View::POS_END);
?>
