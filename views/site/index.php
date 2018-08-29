<?php
/* @var $this yii\web\View */
use app\models\Options;
/* @var $this yii\web\View */
$options = Options::find()->where(['id'=>1000,'status'=>1])->one();
$pageTrainer = \app\models\Pages::find()->where(['id'=>1005,'status'=>1])->one();


?>
<div class="container size">
    <?=  app\components\WMenuRepairs::widget(['model'=>$model])?>

    <div class="devices">
        <div class="text-center title-main"><h2>Выберите ваше устройство</h2></div>
        <?=  app\components\WDevices::widget(['model'=>$model])?>

        <div class="description-seo">
            <div class="text-center title-main"><h2>Выберите ваше устройство</h2></div>
            <div class="text">
                Неврология (невропатология) — область медицины, которая занимается профилактикой, диагностикой и лечением заболеваний нервной системы, а также разрабатывает схемы реабилитации после перенесенных неврологических заболеваний. Учитывая то, что нервная система регулирует и контролирует деятельность практических всех органов и процессов в организме, становится понятной необходимость приема у невролога при первых же признаках патологии.
            </div>
        </div>
        <div class="description-seo">
            <div class="text-center title-main"><h2>Выберите ваше устройство</h2></div>
            <div class="text">
                Неврология (невропатология) — область медицины, которая занимается профилактикой, диагностикой и лечением заболеваний нервной системы, а также разрабатывает схемы реабилитации после перенесенных неврологических заболеваний. Учитывая то, что нервная система регулирует и контролирует деятельность практических всех органов и процессов в организме, становится понятной необходимость приема у невролога при первых же признаках патологии.
            </div>
        </div>
        <?=  app\components\WDevicesProblemsList::widget(['model'=>$model])?>
    </div>
</div>
