<?php
namespace app\controllers;

use app\models\Call;
use app\models\City;
use app\models\Devices;
use app\models\Functions;
use app\models\Repair;
use Yii;
use yii\web\Controller;
use yii\bootstrap\ActiveForm;

class AjaxController extends Controller
{
    public function actionCity()
    {
        // Параметры пост данные;
        $request = Yii::$app->request;
        if ($request->post('geo')) {
            $model = City::findAll(['status'=>1]);
            return \app\components\WCityGeo::widget(['model'=>$model]);
        }
    }

    public function actionCityOne()
    {
        $cookies = Yii::$app->response->cookies;
        // Параметры пост данные;
        $request = Yii::$app->request;
        if ($request->post('city_id')) {
            $city_id = abs($request->post('city_id'));
            $model = City::findOne($city_id);
            $time = time() + 3600 * 24 * 30;

//            // добавление новой куки в HTTP-ответ
//            $cookies->add(new \yii\web\Cookie([
//                'name' => 'city_id',
//                'value' => $city_id,
//                'domain'=> 'http://127.0.0.1/',
//                'expire'=>$time,
//            ]));
            //header('Set-Cookie: city_id='.$city_id.'; Expires='.$time.'; path=/');
            return false;
        }
    }

    // Предварительная диагностика
    function actionDiagnostics()
    {
        $request = Yii::$app->request;
        $devices =  new Devices();

        // Выбор девайс;
        if ($request->post('select_devices_form')) {
            $device_id = abs($request->post('device_id'));
            $devices->setDeviceIdSession($device_id);
            $devices->setDeviceProblemIdSession(false);
            return \app\components\WDiagnosticsForm::widget();
        }
        // Удалить сессия;
        if ($request->post('select_devices_remove')) {
            unset($_SESSION['devices']);
            return \app\components\WDiagnosticsForm::widget();
        }

        // Выбор проблемы;
        if ($request->post('select_devices_problems_form')) {
            $device_problem_id = abs($request->post('device_problem_id'));
            $devices->setDeviceProblemIdSession($device_problem_id);
            return \app\components\WDiagnosticsForm::widget();
        }

        // Удалить сессия
        if ($request->post('select_devices_problems_remove')) {

            $devices->setDeviceProblemIdSession(false);
            return \app\components\WDiagnosticsForm::widget();
        }
    }

    // Заявки из формы;
    function actionCallCenter() {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $call =  new Call();

        // Отправить данные из формы;
        if ($request->post('call_form')) {
            $group_id = abs($request->post('group_id'));
            $call->load(Yii::$app->request->post());
            $call_title = !empty($request->post('call_title')) ? $request->post('call_title') : 'Нет';

            if($call->validate()) {
                $call->phone = Functions::phone($call->phone);
                $call->group_id = !empty($group_id) ? $group_id : 1001;
                $call->value = $call_title;
                if (!$call->save(false)) {
                    print_arr($call->errors);
                    die('ERROR');
                }
                return $response->data = ['success'=>'ok','message'=>'Ваша заявка отправлена! В ближайшее время с вами свяжется менеджер!'];
            }else {
                return ActiveForm::validate($call);
            }
            return false;
            //  Yii::$app->getSession()->setFlash('success', 'Ваша заявка отправлена!<br> В ближайшее время с вами свяжется менеджер!');
            //  return Yii::$app->response->redirect(Yii::$app->request->referrer);
        }
    }

    // Call;
    function actionCall() {
        $call =  new Call();
        if(Yii::$app->request->isAjax) {

        return $this->renderAjax('/site/call-form',[
                'call'=>$call
            ]);
        }
    }

    // Выбор девайс;
    function actionSelectDevices() {
        $request = Yii::$app->request;
        $id = abs($request->post('id'));
        //
        if(Yii::$app->request->isAjax && !empty($id)) {

            return \app\components\WDevicesProblemsList::widget();
        }
    }

    // Ближающие салон;
    function actionSalonList() {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $_post = $request->post();
        // Обработка данные;
        $device_id = abs($_post['device']);
        $devicesProblem_id = abs($_post['devicesProblem']);
        $region_id = abs($_post['region']);

        $model = new Repair();

        if(Yii::$app->request->isAjax) {
           // $model->getCurrentDevices(false,$device_id);
            if(!empty($region_id)) {
                $region = $model->getRegionsOne($region_id);
            }
            $region = !empty($region->appleServices) ? $region->appleServices : false;
//            print_arr($region->appleServices);
//            print_arr($region);
//            die();
            return $response->data = [
                'appleServices'=> \app\components\WAppleServices::widget(['model'=>$region]),
                'salonForm'=> \app\components\WSalonForm::widget(['model'=>$model])
            ];
        }
    }


}

