<?php

namespace app\modules\cms\controllers;

use app\models\City;
use app\models\DeviceYearDetails;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use dastanaron\translit\Translit;
use \yii\web\Response;
use yii\helpers\Html;
use yiier\helpers\ModelHelper;
use yii\base\Exception;

use app\models\Devices;
use app\modules\cms\models\DevicesSearch;


use app\models\DevicesDetails;

use app\models\DeviceProblems;
use app\modules\cms\models\DeviceProblemsSearch;

use app\models\Prices;
use app\modules\cms\models\PricesSearch;

use app\models\GroupDeviceProblems;
use app\modules\cms\models\GroupDeviceProblemsSearch;

use app\models\DeviceYear;
use app\modules\cms\models\DeviceYearSearch;

use app\models\DeviceDiagonals;
use app\modules\cms\models\DeviceDiagonalsSearch;
/**
 * DevicesController implements the CRUD actions for Devices model.
 */
class DevicesController extends BackendController
{

    /**
     * Lists all Devices models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DevicesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Devices model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Devices model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Devices();
        $translit = new Translit();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->url = mb_strtolower($translit->translit($model->title,true,'ru-en'));
            if(!$model->save(true)) {
                return 'Ошибка save';
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Devices model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $devicesDetails = new DevicesDetails();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($devicesDetails->load(Yii::$app->request->post())) {
                // Пост данные в массиве;
                if(!empty($model->checkbox_copy)) {
                    // Копируем данные из таблиц;
                    if (!empty($devicesDetails->devices_id)) {
                        $rows = [];
                        foreach ($devicesDetails->devices_id as $key => $value) {
                            // Получаем копии данные ;
                            $deviceProblems = DeviceProblems::findOne($value);
                            if(!empty($deviceProblems)) {
                                //Копируем и ставим;
                                $modelCreatDeviceProblems = new DeviceProblems();
                                $modelCreatDeviceProblems->attributes = $deviceProblems->attributes;
                                $modelCreatDeviceProblems->title = $deviceProblems->title.'(копия'.$deviceProblems->id.')';
                                $modelCreatDeviceProblems->position = $key;
                                $modelCreatDeviceProblems->id = null;
                                if($modelCreatDeviceProblems->save()){//сохран ение модели с созданием новой записи в БД
                                    $rows[$key]['device_problems_id'] = $modelCreatDeviceProblems->id;
                                    $rows[$key]['devices_id'] = $model->id;
                                }
                            }
                        }
                        if (!ModelHelper::saveAll($devicesDetails::tableName(), $rows)) {
                            throw new Exception();
                        }
                    }
                }else{
                    // Добавляем все записей;
                    if (!empty($devicesDetails->devices_id)) {
                        $rows = [];
                        foreach ($devicesDetails->devices_id as $key => $value) {
                            $rows[$key]['device_problems_id'] = $value;
                            $rows[$key]['devices_id'] = $model->id;
                        }
                        if (!ModelHelper::saveAll($devicesDetails::tableName(), $rows)) {
                            throw new Exception();
                        }
                    }
                }
            }


            return $this->redirect(['view', 'id' => $model->id]);
        }
        // Удалить проблему;
        if(Yii::$app->request->post('delete')) {
            $id = Yii::$app->request->post('id');
            $devicesDetails = DevicesDetails::findOne($id);
            $devicesDetails->delete();
        }

        return $this->render('update', [
            'model' => $model,
            'devicesDetails' => $devicesDetails,
        ]);
    }

    /**
     * Deletes an existing Devices model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Devices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Devices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Devices::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all GroupDeviceProblems models.
     * @return mixed
     */
    public function actionGroupDeviceProblems()
    {
        $searchModel = new GroupDeviceProblemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('group-device-problems/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GroupDeviceProblems model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewGroupDeviceProblems($id)
    {
        return $this->render('group-device-problems/view', [
            'model' => $this->findModelGroupDeviceProblems($id),
        ]);
    }

    /**
     * Creates a new GroupDeviceProblems model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateGroupDeviceProblems()
    {
        $model = new GroupDeviceProblems();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-group-device-problems', 'id' => $model->id]);
        }

        return $this->render('group-device-problems/create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing GroupDeviceProblems model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateGroupDeviceProblems($id)
    {
        $model = $this->findModelGroupDeviceProblems($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-group-device-problems', 'id' => $model->id]);
        }

        return $this->render('group-device-problems/update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing GroupDeviceProblems model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteGroupDeviceProblems($id)
    {
        $this->findModelGroupDeviceProblems($id)->delete();

        return $this->redirect(['group-device-problems']);
    }

    /**
     * Finds the GroupDeviceProblems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GroupDeviceProblems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelGroupDeviceProblems($id)
    {
        if (($model = GroupDeviceProblems::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all DeviceProblems models.
     * @return mixed
     */
    public function actionDeviceProblems($group_id = false)
    {
        $searchModel = new DeviceProblemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$group_id);

        return $this->render('device-problems/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DeviceProblems model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewDeviceProblems($id)
    {
        return $this->render('device-problems/view', [
            'model' => $this->findModelDeviceProblems($id),
        ]);
    }

    /**
     * Creates a new DeviceProblems model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateDeviceProblems()
    {
        $model = new DeviceProblems();
        $prices = new Prices();
        $devicesDetails = new DevicesDetails();
        $translit = new Translit();

        $transactionBD = \Yii::$app->db->beginTransaction();

        if ($model->load(Yii::$app->request->post()) && $prices->load(Yii::$app->request->post())) {
            try {
                $model->url = mb_strtolower($translit->translit($model->title,true,'ru-en'));
                if ($model->save(true)) {
                    // Список цены;
                    if($prices->load(Yii::$app->request->post())) {
                        $_post = Yii::$app->request->post('Prices');
                        // Добавляем все записей;
                        if(!empty($_post['money'])) {
                            foreach ($_post['money'] as $city => $value) {
                                $pricesOne = Prices::find()->where(['device_problems_id'=>$model->id,'city_id'=>$city])->one();
                                $pricesList =  new Prices();
                                $pricesList->money = $value;
                                $pricesList->device_problems_id = $model->id;
                                $pricesList->city_id = $city;
                                if(!$pricesList->save()) {
                                    print_arr($pricesList->error);
                                    die();
                                }
                            }
                        }
                    }

                    $transactionBD->commit();
                }

                if ($devicesDetails->load(Yii::$app->request->post())) {
                    // Пост данные в массиве;
                    $_post = Yii::$app->request->post('DevicesDetails'); ///
                    // Удаляем все записей по id;
                    if(!empty($model->devicesDetails)) {
                        $devicesDetails->deleteAll(['device_problems_id'=>$model->id]);
                    }
                    // Добавляем все записей;
                    if(!empty($_post['devices_id'])) {
                        $rows = [];
                        foreach ($_post['devices_id'] as $key => $value) {
                            $rows[$key]['devices_id'] = $value;
                            $rows[$key]['device_problems_id'] = $model->id;
                        }
                        if (!ModelHelper::saveAll($devicesDetails::tableName(),$rows)) {
                            throw new Exception();
                        }
                    }
                }

                return $this->redirect(['view-device-problems', 'id' => $model->id]);
            }catch (Exception $e) {
                $transactionBD->rollback();
                return Yii::warning($e->getMessage());
            }
        }

        return $this->render('device-problems/create', [
            'model' => $model,
            'prices' => $prices,
            'devicesDetails'=>$devicesDetails,
        ]);
    }

    /**
     * Updates an existing DeviceProblems model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateDeviceProblems($id)
    {
        $model = $this->findModelDeviceProblems($id);

        $devicesDetails = new DevicesDetails();
        $translit = new Translit();

        $prices = !empty($model->getPrice()->one()) ? $model->getPrice()->one() :  new Prices();

        if ($model->load(Yii::$app->request->post()) &&  $model->validate()) {
            $model->url = (!empty($model->url) ? $model->url : mb_strtolower($translit->translit($model->title,true,'ru-en')));
            $model->save(true);
            // Список цены;
            if($prices->load(Yii::$app->request->post())) {
                $_post = Yii::$app->request->post('Prices');
                // Добавляем все записей;
                if(!empty($_post['money'])) {
                    foreach ($_post['money'] as $city => $value) {
                        $pricesOne = Prices::find()->where(['device_problems_id'=>$id,'city_id'=>$city])->one();
                        $pricesList = !empty($pricesOne) ? $pricesOne : new Prices();
                        $pricesList->money = $value;
                        $pricesList->device_problems_id = $id;
                        $pricesList->city_id = $city;
                        if(!$pricesList->save()) {
                            print_arr($pricesList->error);
                            die();
                        }
                    }
                }
            }

            // Добавить привязка услуги;
            if ($devicesDetails->load(Yii::$app->request->post())) {
                // Пост данные в массиве;
                $_post = Yii::$app->request->post('DevicesDetails');
                // Удаляем все записей по id;
                if(!empty($model->devicesDetails)) {
                    $devicesDetails->deleteAll(['device_problems_id'=>$id]);
                }

                // Добавляем все записей;
                if(!empty($_post['devices_id'])) {
                    $rows = [];
                    foreach ($_post['devices_id'] as $key => $value) {
                        $rows[$key]['devices_id'] = $value;
                        $rows[$key]['device_problems_id'] = $id;
                    }

                    if (!ModelHelper::saveAll($devicesDetails::tableName(),$rows)) {
                        throw new Exception();
                    }
                }
            }

            return $this->redirect(['view-device-problems', 'id' => $model->id]);
        }



        return $this->render('device-problems/update', [
            'model' => $model,
            'prices' => $prices,
            'devicesDetails'=>$devicesDetails,
        ]);
    }

    /**
     * Deletes an existing DeviceProblems model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteDeviceProblems($id,$group_id = false)
    {
        $this->findModelDeviceProblems($id)->delete();

        return $this->redirect(['device-problems', 'group_id' => $group_id]);
    }

    /**
     * Finds the DeviceProblems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DeviceProblems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelDeviceProblems($id)
    {
        if (($model = DeviceProblems::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all Prices models.
     * @return mixed
     */
    public function actionPrices()
    {
        $searchModel = new PricesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('prices/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Prices model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewPrices($id)
    {
        return $this->render('view-prices', [
            'model' => $this->findModelPrices($id),
        ]);
    }

    /**
     * Creates a new Prices model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreatePrices()
    {
        $model = new Prices();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['view-prices', 'id' => $model->id]);
        }

        return $this->render('prices/create', [
            'model' => $model,
        ]);
    }

    public function actionCreateCopyPrices()
    {
        $model = new Prices();

        if ($model->load(Yii::$app->request->post())) {

            $copy_city = City::findOne($model->copy_city);
            if(!empty($copy_city->prices) && !empty($model->city_id)) {
                $i = 0;
                foreach ($copy_city->prices as $copy) {
                    $i++;
                      $_model = new Prices();
                      $_model->city_id = $model->city_id;
                      $_model->device_problems_id = $copy->device_problems_id;
                      $_model->money = $copy->money;
                      $_model->value = $copy->value;
                      $_model->status = 1;
                      $_model->save();
                }

                Yii::$app->session->setFlash('success','Прайсы Успешно скопированы! ('.$i.')');
            }else{
                Yii::$app->session->setFlash('error','Ошибка! не выбран город');
            }

            return $this->redirect(['prices']);
        }

        return $this->render('prices/create_copy', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing Prices model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdatePrices($id)
    {
        $model = $this->findModelPrices($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-prices', 'id' => $model->id]);
        }

        return $this->render('prices/update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Prices model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeletePrices($id)
    {
        $this->findModelPrices($id)->delete();

        return $this->redirect(['prices']);
    }

    /**
     * Finds the Prices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Prices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelPrices($id)
    {
        if (($model = Prices::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all DeviceYear models.
     * @return mixed
     */
    public function actionDeviceYear()
    {
        $searchModel = new DeviceYearSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('device-year/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DeviceYear model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewDeviceYear($id)
    {
        return $this->render('device-year/view', [
            'model' => $this->findModelDeviceYear($id),
        ]);
    }

    /**
     * Creates a new DeviceYear model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateDeviceYear()
    {
        $model = new DeviceYear();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-device-year', 'id' => $model->id]);
        }

        return $this->render('device-year/create', [
            'model' => $model,
        ]);
    }

    // Добавить
    public function actionAddDeviceDiagonal($id)
    {
        if(empty($id)) return false;

        $request = Yii::$app->request;
        $model = new DeviceYearDetails();
        // открыть форма;
        if(Yii::$app->request->isAjax && $request->post('add_diagonal')) {
            return $this->renderAjax('device-year/add_diagonal', [
                'model' => $model
            ]);
        }


        // добавить параметры
        if ($model->load(Yii::$app->request->post())) {
            $device_diagonal_id = abs($model->device_diagonal_id);

            if(!empty($model->device_problem_id)) {
                foreach ($model->device_problem_id as $key => $device_problem_id) {
                    $_model = clone $model;
                    $_model->device_year_id = $id;
                    $_model->device_diagonal_id = ($device_diagonal_id ? $device_diagonal_id : null);
                    $_model->device_problem_id = $device_problem_id;
                    if(!$_model->save(true)) {
                        return print_r($_model->errors);
                    }
                }
            }


            return $this->redirect(['update-device-year', 'id' => $id]);
        }

    }

    /**
     * Updates an existing DeviceYear model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateDeviceYear($id)
    {
        $model = $this->findModelDeviceYear($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-device-year', 'id' => $model->id]);
        }

        return $this->render('device-year/update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DeviceYear model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteDeviceYear($id)
    {
        $this->findModelDeviceYear($id)->delete();

        return $this->redirect(['device-year']);
    }

    /**
     * Finds the DeviceYear model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DeviceYear the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelDeviceYear($id)
    {
        if (($model = DeviceYear::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Lists all DeviceYear models.
     * @return mixed
     */
    public function actionDeviceDiagonals()
    {
        $searchModel = new DeviceDiagonalsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('device-diagonals/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DeviceYear model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewDeviceDiagonals($id)
    {
        return $this->render('device-diagonals/view', [
            'model' => $this->findModelDeviceDiagonals($id),
        ]);
    }

    /**
     * Creates a new DeviceYear model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateDeviceDiagonals()
    {
        $model = new DeviceDiagonals();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-device-diagonals', 'id' => $model->id]);
        }

        return $this->render('device-diagonals/create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DeviceYear model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateDeviceDiagonals($id)
    {
        $model = $this->findModelDeviceDiagonals($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view-device-diagonals', 'id' => $model->id]);
        }

        return $this->render('device-diagonals/update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DeviceYear model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteDeviceDiagonals($id)
    {
        $this->findModelDeviceDiagonals($id)->delete();

        return $this->redirect(['device-diagonals']);
    }

    /**
     * Finds the DeviceYear model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DeviceYear the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelDeviceDiagonals($id)
    {
        if (($model = DeviceDiagonals::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /*Форма удалить сязные данные для параметры*/
    public function actionDeleteDeviceYearDetails($id, $device_year_id = false, $diagonal_id = false)
    {
        if(!empty($diagonal_id) && !empty($device_year_id)) {
            DeviceYearDetails::deleteAll(['device_diagonal_id' => $diagonal_id,'device_year_id' => $device_year_id]);
        }else {
            $this->deleteDeviceYearDetails($id)->delete();
        }
        return $this->redirect(['update-device-year', 'id' => $device_year_id]);
    }

    protected function deleteDeviceYearDetails($id)
    {
        if (($model = DeviceYearDetails::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
