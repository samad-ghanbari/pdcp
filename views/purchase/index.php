<?php

/* @var $this yii\web\View */
/* @var  $admin */

$this->title = 'PDCP|Purchase';
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
Yii::$app->formatter->nullDisplay = "";


Yii::$app->formatter->nullDisplay = "";
$user = ['name'=>'', 'lastname'=>''];
$sessoin = Yii::$app->session;
if(isset($sessoin['user']))
    $user= $sessoin['user'];

?>

    <div class="topic-cover bg-gradient" >
        <div class="layout-wrapper">
            <div class="layout-wide-dim-panel">
                <img style="width: 80px; height:auto; display:block; margin:auto;" src="<?= Yii::$app->request->baseUrl.'/web/images/cart.png'; ?>">
                <h3 style="text-align: center; color:white;">خرید‌های منطقه</h3>
                <hr />
                <br />
                <!-- flex-wrap:wrap;  -->
                <!-- <div style="display: flex;flex-direction:column; align-items:center; justify-content: center; overflow: hidden; margin-bottom: 50px;"> -->
                <div id="gwcontainer" style="overflow:auto; background-color:#fff; direction:rtl; height: 100%; font-size: 14px;">

                    <?php
                    $url = Yii::$app->request->baseUrl.'/project/index?id=';
                    if(empty($searchModel))
                    {
                        echo "<h3 style='color:#721c24; text-align: center; direction:rtl;'>خریدی ثبت نشده است.</h3>";
                    }
                    else
                    {
                        echo Html::a('<i class="fa fa-plus text-white" ></i><span class="dis-text"> ثبت خرید جدید </span>', ['new'], ['style'=>'display:block; margin-left:0; width:150px; height: 40px','class'=>'btn btn-success']);

                        echo GridView::widget([
                            'tableOptions'=>['id'=>"perchase-Table", 'class'=>'table table-striped table-bordered table-hover text-center '],
                            //'headerRowOptions'=>['class'=>'bg-info text-center'],
                            'rowOptions' =>function ($model, $key, $index, $grid) {return ['id'=>'row'.$model['id'], 'pid'=>$model["id"], 'class'=>'table_row', 'style'=>"cursor:pointer", 'onclick'=>'activateRow(this.getAttribute("id"));', 'ondblclick'=>'showPurchase(this.getAttribute("pid"))'];},
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'filterRowOptions' =>['style'=>"direction:ltr"],
                            //'summary' => 'نمایش <b>{begin} تا {end}</b> از <b>{totalCount}</b> ',
                            'summary'=>"",
                            //        'pager'=>['options'=>['align'=>"center", 'class'=>"pagination"]],
                            'layout' => "{summary}\n{items}\n<div align='center' >{pager}</div>",
                            'columns' => [
                                //id, title, area, lom, factor, creator, create_at, modifier, modified_at, purchase_code, done
                                //0
                                [
                                    'attribute' =>'id',
                                    'visible'=>0,
                                ],
                                //1
                                [
                                    'attribute' =>'title',
                                    'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                    'contentOptions' => ['class' => 'text-center text-success', 'style'=>"vertical-align: middle;min-width:150px;font-size:16px;", 'title'=>'عنوان خرید'],
                                ],
                                [
                                    'attribute' =>'area',
                                    'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                    'contentOptions' => ['class' => 'text-center text-success', 'style'=>"vertical-align: middle;min-width:150px;font-size:16px;", 'title'=>'منطقه'],
                                ],
                                [
                                    'attribute' =>'purchase_code',
                                    'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                    'contentOptions' => ['class' => 'text-center text-success', 'style'=>"vertical-align: middle;min-width:150px;font-size:16px;", 'title'=>'کد خرید'],
                                ],
                                //3
                                [
                                    'attribute' =>'lom',
                                    'filter' => false,
                                    'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                    'contentOptions' => ['class' => 'text-center', 'style'=>"vertical-align: middle;min-width:80px;", 'title'=>'برآورد خرید'],
                                    'format'=>'html',
                                    'value'=>function($data){
                                        if(empty($data['lom'])) {
                                            return "<i class='fa fa-times text-danger'></i>";
                                        } else {
                                            $fileUrl = Yii::$app->request->baseUrl . '/uploads/' . $data['lom'];
                                            return Html::a('<i class="fa fa-download text-success"></i>', $fileUrl, ['title' => 'دانلود فایل', 'target' => '_blank']);
                                        }
                                    }
                                ],
                                //۴
                                [
                                    'attribute' =>'factor',
                                    'filter' => false,
                                    'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                    'contentOptions' => ['class' => 'text-center', 'style'=>"vertical-align: middle;min-width:80px;", 'title'=>'فاکتور خرید'],
                                    'format'=>'html',
                                    'value'=>function($data){
                                        if(empty($data['factor'])) {
                                            return "<i class='fa fa-times text-danger'></i>";
                                        } else {
                                            $fileUrl = Yii::$app->request->baseUrl . '/uploads/' . $data['factor'];
                                            return Html::a('<i class="fa fa-download text-success"></i>', $fileUrl, ['title' => 'مشاهده فاکتور', 'target' => '_blank']);
                                        }
                                    }
                                ],
                                //8
                                [
                                    'attribute' =>'creator',
                                    'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                    'contentOptions' => ['class' => 'text-center', 'style'=>"vertical-align: middle;min-width:80px;", 'title'=>'ثبت کننده'],
                                ],
                                //9
                                
                                [
                                    'attribute' =>'created_at',
                                    'filter' => false,
                                    'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                    'contentOptions' => ['class' => 'text-center', 'style'=>"vertical-align: middle;min-width:80px;", 'title'=>'زمان ثبت'],
                                    'format'=>'html',
                                    'value'=>function($data){if(empty($data['created_at'])) return ''; else return \app\components\Jdf::jdate("Y/m/d", $data["created_at"]);}
                                ],
                                // [
                                //     'attribute' =>'modifier',
                                //     'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                //     'contentOptions' => ['class' => 'text-center', 'style'=>"vertical-align: middle;min-width:80px;", 'title'=>'ویرایش'],
                                // ],
                                // [
                                //     'attribute' =>'modified_at',
                                //     'filter' => false,
                                //     'headerOptions' => ['class' => 'bg-success text-center', 'style'=>'height:80px; line-height:80px;'],
                                //     'contentOptions' => ['class' => 'text-center', 'style'=>"vertical-align: middle;min-width:80px;", 'title'=>'زمان ویرایش'],
                                //     'format'=>'html',
                                //     'value'=>function($data){if(empty($data['modified_at'])) return ''; else return \app\components\Jdf::jdate("Y/m/d", $data["modified_at"]);}
                                // ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{project_detail}&nbsp;&nbsp;&nbsp;&nbsp;{update}&nbsp;&nbsp;&nbsp;&nbsp;{delete}',
                                    'header'=>"عملیات",
                                    'headerOptions' => ['class' => 'bg-success text-center text-info', 'style'=>'height:80px; line-height:80px;'],
                                    'buttons' => [
                                        'project_detail' => function($url, $model, $key) {
                                            return Html::a('<i class="fa fa-info-circle text-info"></i>', $url, ['title' => 'نمایش اطلاعات خرید']);
                                        },
                                        'update' => function($url, $model, $key) {
                                            return Html::a('<i class="fa fa-edit text-success"></i>', $url, ['title' => 'ویرایش ']);
                                        },
                                        'delete' => function($url, $model, $key) {
                                            return Html::a('<i class="fa fa-trash text-danger"></i>', $url, [
                                                'title' => 'حذف خرید',
                                                'data-confirm' => 'آیا از حذف این خرید مطمئن هستید؟',
                                                'data-method' => 'post',
                                            ]);
                                        },
                                    ],
                                    'urlCreator' => function ($action, $model, $key, $index) {
                                        if ($action === 'project_detail') {
                                            return Yii::$app->request->baseUrl . '/purchase/view?id=' . $model->id;
                                        }
                                        if ($action === 'update') {
                                            return Yii::$app->request->baseUrl . '/purchase/update_page?id=' . $model->id;
                                        }
                                        if ($action === 'delete') {
                                            return Yii::$app->request->baseUrl . '/purchase/delete?id=' . $model->id;
                                        }
                                    }
                                ],
                            ],
                        ]);
                        
                    }
                    ?>

                    <br style="clear:both;" />
                </div>
            </div>
        </div>
    </div>


    <?php

$url = Yii::$app->request->baseUrl.'/purchase/view?id=';
$script =<<< JS
function activateRow(rowId)
{
    $(".selectedRowPurchase").removeClass("selectedRowPurchase");
    $("#"+rowId).addClass("selectedRowPurchase");
}

function showPurchase(id)
{

    activateRow("row"+id);
    let url = "$url"+id;
    //window.location.href = url;
    window.open(url, "_self");
}

JS;
$this->registerJs($script, Yii\web\View::POS_END);
?>