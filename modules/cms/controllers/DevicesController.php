<?php

namespace app\modules\cms\controllers;

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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
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

        $transactionBD = \Yii::$app->db->beginTransaction();

        if ($model->load(Yii::$app->request->post()) && $prices->load(Yii::$app->request->post())) {
            try {
                if ($model->save(true)) {
                    $prices->device_problems_id = $model->id;
                    $prices->status = 1;
                    $prices->save(true);
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
        $prices = $model->getPrice()->one();
        $devicesDetails = new DevicesDetails();



        if ($model->load(Yii::$app->request->post()) && $prices->load(Yii::$app->request->post()) && $model->save(true)) {
            $prices->save(true);
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
    public function actionDeleteDeviceProblems($id)
    {
        $this->findModelDeviceProblems($id)->delete();

        return $this->redirect(['device-problems']);
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
}
