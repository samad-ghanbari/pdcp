<?php

namespace app\controllers;

use Codeception\Command\Console;
use phpDocumentor\Reflection\Types\Scalar;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;


class PurchaseController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        $session->open();
        if (isset($session['user']))
        {
            return parent::beforeAction($action);
        }
        else
        {
            return $this->redirect(["main/login"]);
        }
    }

    public function actionIndex()
    {
        $this->layout = "main";
        $session = Yii::$app->session;
        $session->open();
        if(isset($session['user']))
        {
            $user = $session['user'];

            $searchModel = new \app\models\PcViewPurchasesSearch();
            $params = Yii::$app->request->queryParams;
            if ($params)
            {
                $dataProvider = $searchModel->search($params);
                $dataProvider->query->andWhere(['creator'=>$user['id']]);
                $dataProvider->query->orderBy(['create_at'=>SORT_DESC]);                
            }
            else
            {

            if($user['admin'] == 1)
                $qry = \app\models\PcViewPurchases::find()->select('id, title, area, lom, factor, creator_id, creator, created_at, modifier_id, modifier, modified_at, purchase_code, done')->orderBy(['created_at'=>SORT_DESC]);
            else
                $qry = \app\models\PcViewPurchases::find()->select('id, title, area, lom, factor, creator_id, creator, created_at, modifier_id, modifier, modified_at, purchase_code, done')->where(['area'=>[2, 3]])->orderBy(['created_at'=>SORT_DESC]);

            $dataProvider = new \yii\data\ActiveDataProvider(['query' => $qry]);
            }
            $dataProvider->pagination->pageSize = 25;


            return $this->render("index", ['searchModel'=>$searchModel, 'dataProvider'=>$dataProvider]);
        }
        else
            return $this->redirect(['main/login']);
    }

    public function actionNew()
    {
        $session = Yii::$app->session;
        $session->open();
        if(isset($session['user']))
        {
            $action_role = $session["user"]["action_role"];
            $creator = $session["user"]["name"] . " " . $session["user"]["lastname"];
            $ts = time();
            $created_at = \app\components\Jdf::jdate('Y/m/d', $ts);
            $areas = [];
            if($action_role === "design")
                $areas = [1=>'-', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8'];

            if($action_role === "district")
            {
                $office = $session["user"]["office"];
                
                if(strpos($office, "منطقه") !== false)
                {
                    if( (strpos($office, "2") !== false) || (strpos($office, "۲") !== false) )
                    {
                        $areas = [2=>'2'];
                    }
                    else if( (strpos($office, "3") !== false) || (strpos($office, "۳") !== false) )
                    {
                        $areas = [3=>'3'];
                    }
                    else if( (strpos($office, "4") !== false) || (strpos($office, "۴") !== false) )
                    {
                        $areas = [4=>'4'];
                    }
                    else if( (strpos($office, "5") !== false) || (strpos($office, "۵") !== false) )
                    {
                        $areas = [5=>'5'];
                    }
                    else if( (strpos($office, "6") !== false) || (strpos($office, "۶") !== false) )
                    {
                        $areas = [6=>'6'];
                    }
                    else if( (strpos($office, "7") !== false) || (strpos($office, "۷") !== false) )
                    {
                        $areas = [7=>'7'];
                    }
                    else if( (strpos($office, "8") !== false) || (strpos($office, "۸") !== false) )
                    {
                        $areas = [8=>'8'];
                    }
                }
            }
    
            $model = new \app\models\PcPurchases();
            $model->created_at = $ts;
            $model->creator_id = $session['user']['id'];
            $model->done = false;
            $model->purchase_code = "";

            return $this->render('new', ['model'=>$model, 'areas'=>$areas, 'creator'=>$creator, "created_at"=>$created_at]);

        }

        return $this->redirect(['purchase/index']);
    }

    public function actionInsert()
    {
        $session = Yii::$app->session;
        $session->open();
        if((Yii::$app->request->isPost) && (isset($session['user'])) )
        {
            $model = new \app\models\PcPurchases();
            if($model->load(Yii::$app->request->post()))
            {
                    if((int)$model["area"] < 1)
                    {
                        Yii::$app->session->setFlash('error','انتخاب منطقه به درستی صورت نگرفته است.');
                        return $this->redirect(['purchase/index']);
                    }

                    $model->creator_id = $session['user']['id'];
                    $model->done = false;

                    if((int)$model["created_at"] < 1)
                    {
                        Yii::$app->session->setFlash('error','انتخاب زمان ثبت به درستی تعیین نشده است.');
                        return $this->redirect(['purchase/index']);
                    }

                    if (isset($_FILES['lom_file']) && $_FILES['lom_file']['error'] == UPLOAD_ERR_OK) {
                        $uploadedFile = $_FILES['lom_file'];
                        $uniqueFileName = uniqid() . '_' . $uploadedFile['name'];
                        $filePath = Yii::getAlias('@webroot/uploads/') . $uniqueFileName;

                        if (!is_dir(Yii::getAlias('@webroot/uploads/'))) {
                            mkdir(Yii::getAlias('@webroot/uploads/'), 0777, true);
                        }

                        if (move_uploaded_file($uploadedFile['tmp_name'], $filePath))
                            $model->lom = $uniqueFileName;
                        else 
                            $model->lom = "";
                    }

                    if (isset($_FILES['factor_file']) && $_FILES['factor_file']['error'] == UPLOAD_ERR_OK) {
                        $uploadedFile = $_FILES['factor_file'];
                        $uniqueFileName = uniqid() . '_' . $uploadedFile['name'];
                        $filePath = Yii::getAlias('@webroot/uploads/') . $uniqueFileName;

                        if (!is_dir(Yii::getAlias('@webroot/uploads/'))) {
                            mkdir(Yii::getAlias('@webroot/uploads/'), 0777, true);
                        }

                        if (move_uploaded_file($uploadedFile['tmp_name'], $filePath))
                            $model->factor = $uniqueFileName;
                        else 
                            $model->factor = "";
                    }

                    //save
                    if($model->save())
                    {
                        Yii::$app->session->setFlash('success','عملیات با موفقیت انجام شد.');
                    }
                    else
                        Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
            }
            else
                Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
        }

        return $this->redirect(['purchase/index']);
    }

}
