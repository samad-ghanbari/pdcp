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

use PhpOffice\PhpSpreadsheet\IOFactory;



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

            $area = [];
            if($user['action_role'] == "design")
                $area = [2, 3, 4, 5, 6, 7, 8];
            else if($user['action_role'] == "district")
            {
                $office = $user["office"];
                if(strpos($office, "منطقه") !== false)
                {
                    if( (strpos($office, "2") !== false) || (strpos($office, "۲") !== false) )
                    {
                        $area = [2];
                    }
                    else if( (strpos($office, "3") !== false) || (strpos($office, "۳") !== false) )
                    {
                        $area = [3];
                    }
                    else if( (strpos($office, "4") !== false) || (strpos($office, "۴") !== false) )
                    {
                        $area = [4];
                    }
                    else if( (strpos($office, "5") !== false) || (strpos($office, "۵") !== false) )
                    {
                        $area = [5];
                    }
                    else if( (strpos($office, "6") !== false) || (strpos($office, "۶") !== false) )
                    {
                        $area = [6];
                    }
                    else if( (strpos($office, "7") !== false) || (strpos($office, "۷") !== false) )
                    {
                        $area = [7];
                    }
                    else if( (strpos($office, "8") !== false) || (strpos($office, "۸") !== false) )
                    {
                        $area = [8];
                    }
                }
            }

            $searchModel = new \app\models\PcViewPurchasesSearch();
            $params = Yii::$app->request->queryParams;
            if ($params)
            {
                $dataProvider = $searchModel->search($params);
                $dataProvider->query->andWhere(['area'=>$area]);
                $dataProvider->query->orderBy(['created_at'=>SORT_DESC]);                
            }
            else
            {
                if($user['admin'] == 1)
                    $qry = \app\models\PcViewPurchases::find()->select('id, title, area, lom, factor, creator_id, creator, created_at, modifier_id, modifier, modified_at, purchase_code, done')->orderBy(['created_at'=>SORT_DESC]);
                else
                    $qry = \app\models\PcViewPurchases::find()->select('id, title, area, lom, factor, creator_id, creator, created_at, modifier_id, modifier, modified_at, purchase_code, done')->where(['area'=>$area])->orderBy(['created_at'=>SORT_DESC]);

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

            $titles0 = \app\models\PcPurchaseTitle::find()->orderBy(['id'=>SORT_ASC])->asArray()->all();
            $titles = [];
            foreach ($titles0 as $title) {
                $titles[$title["title"]] = $title["title"];
            }

            

            return $this->render('new', ['model'=>$model, 'areas'=>$areas, 'creator'=>$creator, "created_at"=>$created_at, 'titles'=>$titles]);

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

    public function actionView($id)
    {
        $session = Yii::$app->session;
        $session->open();
        if(isset($session['user']))
        {
            $model = \app\models\PcViewPurchases::find()->where(['id'=>$id])->one();
            if($model)
            {
                $created_at = $this->getDate($model["created_at"]);
                $modified_at = $this->getDate($model["modified_at"]);
                if(empty($model["modified_at"]))
                    $modified_at = "";

                $searchModel = new \app\models\PcPurchaseDetailSearch();
                $dataProvider = [];
                $params = Yii::$app->request->queryParams;
                if ($params)
                {
                    $dataProvider = $searchModel->search($params);
                    $dataProvider->query->andWhere(['purchase_id' => $id]);
                }
                else
                {
                    $qry = \app\models\PcPurchaseDetail::find()->select('')->where(['purchase_id'=>$model['id']])->orderBy(['id'=>SORT_ASC]);
                    $dataProvider = new \yii\data\ActiveDataProvider(['query' => $qry]);
                }
                $dataProvider->pagination->pageSize = 25;

                return $this->render('view', ['model'=>$model, 'searchModel'=>$searchModel, 'dataProvider'=>$dataProvider, 'created_at'=>$created_at, 'modified_at'=>$modified_at]);
            }
            else
                return $this->redirect(['purchase/index']);
        }
        else
            return $this->redirect(['main/login']);
    }

    public function actionDelete($id)
    {
        $session = Yii::$app->session;
        $session->open();
        if(isset($session['user']))
        {
            $model = \app\models\PcPurchases::find()->where(['id'=>$id])->one();
            $cnt = \app\models\PcPurchaseDetail::find()->where(['purchase_id'=>$id])->count();

            if($model)
            {
                $code = $model->purchase_code;
                if($code != "")
                {
                    Yii::$app->session->setFlash('error','امکان حذف خریدهایی که ثبت نهایی شده‌اند، وجود ندارد.');
                    return $this->redirect(['purchase/view', 'id'=>$id]);
                }
                else if($cnt > 0)
                {
                    Yii::$app->session->setFlash('error','برای حذف خرید، ابتدا باید جزئیات آن را حذف کنید.');
                    return $this->redirect(['purchase/view', 'id'=>$id]);
                }
                else{
                    $lom = $model->lom;
                    $factor = $model->factor;
                    if($lom != "")
                    {
                        $existingFilePath = Yii::getAlias('@webroot/uploads/') . $lom;
                        if (file_exists($existingFilePath)) {
                            unlink($existingFilePath);
                        }
                    }
                    if($factor != "")
                    {
                        $existingFilePath = Yii::getAlias('@webroot/uploads/') . $factor;
                        if (file_exists($existingFilePath)) {
                            unlink($existingFilePath);
                        }
                    }

                    $model->delete();
                    Yii::$app->session->setFlash('success','عملیات با موفقیت انجام شد.');
                }
            }
            else
                Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
        }
        else
            return $this->redirect(['main/login']);
        
        return $this->redirect(['purchase/index']);
    }

    public function actionUpdate_page($id)
    {
        $session = Yii::$app->session;
        $session->open();
        if(isset($session['user']))
        {
                $model = \app\models\PcPurchases::find()->where(['id'=>$id])->one();
                if($model)
                {
                    if($model["done"])
                    {
                        Yii::$app->session->setFlash('error','بعد از ثبت نهایی خرید، امکان ویرایش خرید میسر نمی‌باشد.');
                    }
                    else
                    {
                        $creator = $this->getUserName($model["creator_id"]);
                        $modifier = $this->getUserName($model["modifier_id"]);
    
                        $created_at = $this->getDate($model["created_at"]);
                        $modified_at = $this->getDate($model["modified_at"]);
                        if($model["modified_at"])
                            $modified_at = "";
                        
                        $action_role = $session["user"]["action_role"];

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

                            $titles0 = \app\models\PcPurchaseTitle::find()->orderBy(['id'=>SORT_ASC])->asArray()->all();
                            $titles = [];
                            foreach ($titles0 as $title) {
                                $titles[$title["title"]] = $title["title"];
                            }

                            return $this->render('update', ['model'=>$model, 'areas'=>$areas, 'creator'=>$creator, "created_at"=>$created_at, $modifier=>$modifier, "modified_at"=>$modified_at, 'titles'=>$titles]);
                    }
                }
                else
                    Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
        }
        else
            return $this->redirect(['main/login']);
        
        return $this->redirect(['purchase/index']);
    }

    public function actionUpdate()
    {
        $session = Yii::$app->session;
        $session->open();
        if((Yii::$app->request->isPost) && (isset($session['user'])) )
        {
            $id = Yii::$app->request->post('PcPurchases')["id"];
            $area = Yii::$app->request->post('PcPurchases')["area"];
            $title = Yii::$app->request->post('PcPurchases')["title"];

            if($area < 1)
            {
                Yii::$app->session->setFlash('error','انتخاب منطقه به درستی صورت نگرفته است.');
                return $this->redirect(['purchase/index']);
            }

            $model = \app\models\PcPurchases::findOne($id);
            $model["area"] = $area;
            $model["title"] = $title;
            $model->modifier_id = $session['user']['id'];
            $model->modified_at = time();


            if (isset($_FILES['lom_file']) && $_FILES['lom_file']['error'] == UPLOAD_ERR_OK) {


                if (!empty($model["lom"])) {
                    $existingFilePath = Yii::getAlias('@webroot/uploads/') . $model["lom"];
                    if (file_exists($existingFilePath)) {
                        unlink($existingFilePath);
                    }
                }

                $uploadedFile = $_FILES['lom_file'];
                $uniqueFileName = uniqid() . '_' . $uploadedFile['name'];
                $filePath = Yii::getAlias('@webroot/uploads/') . $uniqueFileName;

                if (move_uploaded_file($uploadedFile['tmp_name'], $filePath))
                    $model->lom = $uniqueFileName;
                else 
                    $model->lom = "";
            }

            if (isset($_FILES['factor_file']) && $_FILES['factor_file']['error'] == UPLOAD_ERR_OK) {

                if (!empty($model["factor"])) {
                    $existingFilePath = Yii::getAlias('@webroot/uploads/') . $model["factor"];
                    if (file_exists($existingFilePath)) {
                        unlink($existingFilePath);
                    }
                }

                $uploadedFile = $_FILES['factor_file'];
                $uniqueFileName = uniqid() . '_' . $uploadedFile['name'];
                $filePath = Yii::getAlias('@webroot/uploads/') . $uniqueFileName;

                if (move_uploaded_file($uploadedFile['tmp_name'], $filePath))
                    $model->factor = $uniqueFileName;
                else 
                    $model->factor = "";

                
            }

            //update
            if($model->update(false))
            {
                Yii::$app->session->setFlash('success','عملیات با موفقیت انجام شد.');
            }
            else
                Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');

            return $this->redirect(['purchase/view', 'id'=>$id]);

        }

        return $this->redirect(['purchase/index']);
    }

    public function getUserName($id)
    {
        $name = "";
        if($id > 0)
        {
            $user = \app\models\PcUsers::findOne($id);
            if($user)
            {
                $name = $user["name"]. " ".$user["lastname"];
            }
        }
        return $name;
    }

    public function getDate($ts)
    {
        return \app\components\Jdf::jdate('Y/m/d', $ts);
    }

    public function actionDone($id)
    {
        $session = Yii::$app->session;
        $session->open();
        if(isset($session['user']))
        {
            $model = \app\models\PcPurchases::find()->where(['id'=>$id])->one();
            if($model)
            {
                $model->done = true;
                $model->modifier_id = $session['user']['id'];
                $model->modified_at = time();
                $model->purchase_code = \app\components\Jdf::jdate('Ym', $model->modified_at) . "-" . $model->area . "-" . $model->id;
                $model->purchase_code = $this->toPersianDigits($model->purchase_code);

                if($model->update(false))
                {
                    Yii::$app->session->setFlash('success','عملیات با موفقیت انجام شد.');
                }
                else
                    Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
            }
            else
                Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
        }
        else
            return $this->redirect(['main/login']);
        
        return $this->redirect(['purchase/view', 'id'=>$id]);
    }

    public function toPersianDigits($str)
    {
        $persianDigits = [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹'
        ];

        return strtr($str, $persianDigits);
    }

    public function actionNew_detail($id)
    {
        $session = Yii::$app->session;
        $session->open();
        if(isset($session['user']))
        {
            $model = new \app\models\PcPurchaseDetail();
            $model->purchase_id = $id;

            $vendors0 = \app\models\PcPurchaseVendor::find()->orderBy(['id'=>SORT_ASC])->asArray()->all();
            $vendors = [];
            foreach ($vendors0 as $vendor) {
                $vendors[$vendor["vendor"]] = $vendor["vendor"];
            }

            $types0 = \app\models\PcPurchaseType::find()->orderBy(['id'=>SORT_ASC])->asArray()->all();
            $types = [];
            foreach ($types0 as $type) {
                $types[$type["type"]] = $type["type"];
            }

            return $this->render('new_detail', ['model'=>$model, 'vendors'=>$vendors, 'types'=>$types]);
        }
        else
            return $this->redirect(['main/login']);
    }

    public function actionInsert_detail()
    {
        $session = Yii::$app->session;
        $session->open();
        if((Yii::$app->request->isPost) && (isset($session['user'])) )
        {
            $model = new \app\models\PcPurchaseDetail();
            if($model->load(Yii::$app->request->post()))
            {
                $modifier_id = $session['user']['id'];
                $modified_at = time();

                if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] == UPLOAD_ERR_OK) {
                    $uploadedFile = $_FILES['photo_file'];
                    $uniqueFileName = uniqid() . '_' . $uploadedFile['name'];
                    $filePath = Yii::getAlias('@webroot/uploads/') . $uniqueFileName;

                    if (move_uploaded_file($uploadedFile['tmp_name'], $filePath))
                        $model->equipment_photo = $uniqueFileName;
                    else 
                        $model->equipment_photo = "";
                }

                //save
                if($model->save())
                {
                    $pmodel = \app\models\PcPurchases::find()->where(['id'=>$model["purchase_id"]])->one();
                    $pmodel->modifier_id = $modifier_id;
                    $pmodel->modified_at = $modified_at;
                    $pmodel->update(false);

                    Yii::$app->session->setFlash('success','عملیات با موفقیت انجام شد.');
                }
                else
                    Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
            }
            else
                Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
        }

        return $this->redirect(['purchase/view', 'id'=>$model["purchase_id"]]);
    }

    public function actionDelete_detail($id)
    {
        $session = Yii::$app->session;
        $session->open();
        $purchase_id = -1;
        if(isset($session['user']))
        {
            $model = \app\models\PcPurchaseDetail::find()->where(['id'=>$id])->one();
            if($model)
            {
                $purchase_id = $model["purchase_id"];

                $photo = $model->equipment_photo;
                if($photo != "")
                {
                    $existingFilePath = Yii::getAlias('@webroot/uploads/') . $photo;
                    if (file_exists($existingFilePath)) {
                        unlink($existingFilePath);
                    }
                }

                $model->delete();
                Yii::$app->session->setFlash('success','عملیات با موفقیت انجام شد.');
            }
            else
                Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
        }
        else
            return $this->redirect(['main/login']);
        
        return $this->redirect(['purchase/view', 'id'=>$purchase_id]);
    }

    public function actionUpdate_detail_page($id)
    {
        $session = Yii::$app->session;
        $session->open();
        if(isset($session['user']))
        {
            $model = \app\models\PcPurchaseDetail::find()->where(['id'=>$id])->one();
            if($model)
            {
                $vendors0 = \app\models\PcPurchaseVendor::find()->orderBy(['id'=>SORT_ASC])->asArray()->all();
                $vendors = [];
                foreach ($vendors0 as $vendor) {
                    $vendors[$vendor["vendor"]] = $vendor["vendor"];
                }
    
                $types0 = \app\models\PcPurchaseType::find()->orderBy(['id'=>SORT_ASC])->asArray()->all();
                $types = [];
                foreach ($types0 as $type) {
                    $types[$type["type"]] = $type["type"];
                }

                return $this->render('update_detail', ['model'=>$model, 'vendors'=>$vendors, 'types'=>$types]);
            }
            else
                Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
        }
        else
            return $this->redirect(['main/login']);
        
        return $this->redirect(['purchase/view', 'id'=>$model["purchase_id"]]);
    }

    public function actionUpdate_detail()
    {
        $session = Yii::$app->session;
        $session->open();
        if((Yii::$app->request->isPost) && (isset($session['user'])) )
        {
            $id = Yii::$app->request->post('PcPurchaseDetail')["id"];
            $model = \app\models\PcPurchaseDetail::findOne($id);
            if($model)
            {
                if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] == UPLOAD_ERR_OK) {

                    if (!empty($model["equipment_photo"])) {
                        $existingFilePath = Yii::getAlias('@webroot/uploads/') . $model["equipment_photo"];
                        if (file_exists($existingFilePath)) {
                            unlink($existingFilePath);
                        }
                    }

                    $uploadedFile = $_FILES['photo_file'];
                    $uniqueFileName = uniqid() . '_' . $uploadedFile['name'];
                    $filePath = Yii::getAlias('@webroot/uploads/') . $uniqueFileName;

                    if (move_uploaded_file($uploadedFile['tmp_name'], $filePath))
                        $model->equipment_photo = $uniqueFileName;
                    else 
                        $model->equipment_photo = "";
                }

                $equipment_type = Yii::$app->request->post('PcPurchaseDetail')["equipment_type"];
                $equipment_brand = Yii::$app->request->post('PcPurchaseDetail')["equipment_brand"];
                $equipment_model = Yii::$app->request->post('PcPurchaseDetail')["equipment_model"];
                $quantity = Yii::$app->request->post('PcPurchaseDetail')["quantity"];
                $provider = Yii::$app->request->post('PcPurchaseDetail')["provider"];
                $descriptions = Yii::$app->request->post('PcPurchaseDetail')["descriptions"];

                $model["equipment_type"] = $equipment_type;
                $model["equipment_brand"] = $equipment_brand;
                $model["equipment_model"] = $equipment_model;
                $model["quantity"] = $quantity;
                $model["provider"] = $provider;
                $model["descriptions"] = $descriptions;


                $purchase_id = $model["purchase_id"];

                //update
                if($model->update(false))
                {
                    $pmodel = \app\models\PcPurchases::find()->where(['id'=>$purchase_id])->one();
                    $pmodel->modifier_id = $session['user']['id'];
                    $pmodel->modified_at = time();
                    $pmodel->update(false);
                    
                    Yii::$app->session->setFlash('success','عملیات با موفقیت انجام شد.');
                }
                else
                    Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
            }
            else
                Yii::$app->session->setFlash('error','ورود اطلاعات با خطا مواجه شد.');
        }

        return $this->redirect(['purchase/view', 'id'=>$model["purchase_id"]]);
    }

    public function actionPurchase_excel()
    {
        $session = Yii::$app->session;
        $session->open();

        if(isset($session['user']))
        {
            $filter = Yii::$app->request->get('PcViewPurchasesSearch');
            $area = null;
            $title = null;
            $creator = null;
            $code = null;
            if($filter)
            {
                $area  = $filter["area"];
                $title = $filter["title"];
                $creator = $filter["creator"];
                $code = $filter["purchase_code"];
            }
            

            $query = \app\models\PcViewPurchases::find();

            if (!empty($area)) {
                $query->andWhere(['area' => (int)$area]);
            }

            if (!empty($title)) {
                $query->andWhere(['like', 'title', $title]);
            }

            if (!empty($creator)) {
                $query->andWhere(['like', 'creator', $creator]);
            }

            if (!empty($code)) {
                $query->andWhere(['like', 'purchase_code', $code]);
            }

            $dataArray = $query->orderBy(['created_at' => SORT_DESC])->asArray()->all();

            $page_header = "گزارش خریدها";
            $table_header = ["ردیف", "منطقه", "عنوان",  "ثبت کننده", "تاریخ ثبت", "کد خرید"];
            $data = []; // [{}, {}, ]   {area, title, creator, ts, purchase_code, details:[]}   details[]  type, brand, model, quantity, provider, desc
            $purchase_id = -1;
            $obj = [];
            $rowNumber = 0;
            
            foreach ($dataArray as $record) {

                $purchase_id = $record["id"];
                $details = \app\models\PcPurchaseDetail::find()->where(['purchase_id'=>$purchase_id])->asArray()->all();
                
                $obj = [];
                $rowNumber++;

                $obj["row"] = $rowNumber;
                $obj['area'] = $record["area"];
                $obj['title'] = $record["title"];
                $obj['created_at'] = \app\components\Jdf::jdate('Y/m/d', $record["created_at"]);
                $obj['creator'] = $record["creator"];
                $obj['purchase_code'] = $record["purchase_code"];
                $obj['details']= [];
                foreach ($details as $detail) {
                    $obj["details"][] = [
                        "equipment_type" => $detail["equipment_type"],
                        "equipment_brand" => $detail["equipment_brand"],
                        "equipment_model" => $detail["equipment_model"],
                        "quantity" => $detail["quantity"],
                        "provider" => $detail["provider"],
                        "descriptions" => $detail["descriptions"],
                    ];
                }

                $data[] = $obj;
            }


            $this->exportExcel($page_header, $filter, $table_header, $data);

            //return $this->redirect(['purchase/index', 'PcViewPurchasesSearch' => $filter]);
        }
        else
            return $this->redirect(['purchase/index']);
    }


    public function exportExcel($page_header, $filter, $table_header, $data)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set page header
        $sheet->setCellValue('A1', $page_header);
        $sheet->mergeCells('A1:' . chr(64 + count($table_header)) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); // Yellow background
        $sheet->getStyle('A1')->getFont()->setName('B Mitra');

        // Set filter information
        $row = 3;
        if($filter)
        foreach ($filter as $key => $value) {
            
            if($key === "area")
                $key = "منطقه";
            if($key === "title")
                $key = "عنوان";
            if($key === "creator")
                $key = "ثبت کننده";
            if($key === "purchase_code")
                $key = "کد خرید";

            if(!empty($value))
            {
                $sheet->setCellValue('A' . $row, $key);
                $sheet->setCellValue('B' . $row, $value);
                $row++;
            }
            
        }

        $sheet->getColumnDimension('A')->setWidth(10); // type
        $sheet->getColumnDimension('B')->setWidth(10); // brand
        $sheet->getColumnDimension('C')->setWidth(10); // model
        $sheet->getColumnDimension('D')->setWidth(10); // quantity
        $sheet->getColumnDimension('E')->setWidth(15); // provider
        $sheet->getColumnDimension('F')->setWidth(25); // desc

        


        // Fill data
        
        foreach ($data as $record) {

            $row++;
            $col = 'A';
            foreach ($table_header as $header) {
                $sheet->getRowDimension($row)->setRowHeight(20);

                $sheet->setCellValue($col . $row, $header);
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('DDDDFF'); // Yellow background
                $sheet->getStyle($col . $row)->getFont()->setName('B Mitra');
                $col++;
            }
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
            $sheet->getRowDimension($row)->setRowHeight(25); 


            $row++;

            $sheet->setCellValue('A' . $row, $record["row"]);
            $sheet->setCellValue('B' . $row, $record["area"]);
            $sheet->setCellValue('C' . $row, $record["title"]);
            $sheet->setCellValue('D' . $row, $record["creator"]);
            $sheet->setCellValue('E' . $row, $record["created_at"]);
            $sheet->setCellValue('F'. $row, $record["purchase_code"]);
            
            $row++;

            // details
            if (isset($record["details"]) && is_array($record["details"])) {
                $sheet->setCellValue('A' . $row, "جزئیات");
                $sheet->mergeCells('A'.$row . ':' . chr(64 + 6) . $row);
                $sheet->getStyle('A'.$row)->getFont()->setBold(true);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A'.$row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('DDDDEE'); // Yellow background
                $sheet->getStyle('A'.$row)->getFont()->setName('B Mitra');
                $sheet->getStyle('A'.$row)->getFont()->setBold(true);
                
                $row++;
                $sheet->setCellValue('A' . ($row ), "نوع تجهیزات");
                $sheet->setCellValue('B' . ($row ), "برند تجهیزات");
                $sheet->setCellValue('C' . ($row ), "مدل تجهیزات");
                $sheet->setCellValue('D' . ($row ), "تعداد");
                $sheet->setCellValue('E' . ($row ), "تامین کننده");
                $sheet->setCellValue('F' . ($row ), "توضیحات");
                $sheet->getStyle('A' . ($row ).':F' . ($row ))->getFont()->setBold(true);
                $sheet->getStyle('A' . ($row ).':F' . ($row ))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($row ).':F' . ($row ))->getAlignment()->setWrapText(true);
                $sheet->getStyle('A' . ($row ).':F' . ($row ))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $sheet->getStyle('A' . ($row).':F' . ($row))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('DDDDEE'); // Yellow background
                $sheet->getStyle('A' . ($row).':F' . ($row))->getFont()->setName('B Mitra');
                $sheet->getStyle('A' . ($row).':F' . ($row))->getFont()->setBold(true);

                
                foreach ($record["details"] as $detail) {
                    $row++;
                    $col = 'A';
                    $sheet->setCellValue('A' . ($row ), $detail["equipment_type"]);
                    $sheet->setCellValue('B' . ($row ), $detail["equipment_brand"]);
                    $sheet->setCellValue('C' . ($row ), $detail["equipment_model"]);
                    $sheet->setCellValue('D' . ($row ), $detail["quantity"]);
                    $sheet->setCellValue('E' . ($row ), $detail["provider"]);
                    $sheet->setCellValue('F' . ($row ), $detail["descriptions"]);
                }
            }

            $row++;
            $sheet->mergeCells('A' . $row . ':' . chr(64 + 6) . $row);

        }

        // Save to file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);


        // Output directly to the browser without saving on the server
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=district_purchase.xlsx');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }




}
