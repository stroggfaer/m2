<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\DeviceProblems */
/* @var $form yii\widgets\ActiveForm */


// Загрузка пользователи;
$parent = \app\models\GroupDeviceProblems::find()->orderBy('id ASC')->all();
$items = ArrayHelper::map(array_merge($parent),'id', 'title');
$params = ['prompt' => 'Выберите Группы проблемы', 'options' => [$model->group_id=>['selected'=>'selected']]];

$city = \app\models\City::find()->orderBy('id ASC')->all();
$items1 = ArrayHelper::map(array_merge($city),'id', 'name');
$params1 = ['prompt' => 'Выберите город', 'options' => [$prices->city_id=>['selected'=>'selected']]];

$devices = ArrayHelper::map(array_merge(\app\models\Devices::find()->where(['status'=>1])->orderBy('id ASC')->all()),'id','title');

?>

<div class="device-problems-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <?=$form->field($model, 'group_id',['options'=>['class'=>'form-group col-sm-6']])->DropDownList($items, $params);  ?>

        <?=$form->field($prices, 'city_id',['options'=>['class'=>'form-group col-sm-6']])->DropDownList($items1, $params1)->label('Город');  ?>
    </div>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'time')->textInput(['maxlength' => true]) ?>

    <?php $tags = !empty($model->devicesDetails) ? ArrayHelper::map(array_merge($model->devicesDetails),'id','devices_id') : '';
          $devicesDetails->devices_id = $tags
    ?>

    <?= $form->field($devicesDetails, 'devices_id')->widget(Select2::classname(), [
        'data' => $devices,
        'maintainOrder' => true,
        'options' => ['placeholder' => 'Введите устройства ...', 'multiple' => true],
        'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [',', ' '],
            'maximumInputLength' => 30
        ],
    ])->label('Добавить устройств');
    ?>

    <?= $form->field($prices, 'money')->textInput(['maxlength' => true]) ?>

    <?php if($model->isNewRecord): ?>
        <?php $position = \app\models\DeviceProblems::find()->select('position')->where(['status'=>1])->orderBy('id DESC')->one();
        $positionValue =  !empty($position) ? $position->position + 1 : 1 ?>
        <?= $form->field($model, 'position')->textInput(['value'=>$positionValue]) ?>
    <?php else: ?>
        <?= $form->field($model, 'position')->textInput() ?>
    <?php endif; ?>

    <?= $form->field($model, 'status')->checkbox(['disabled' => false,]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
