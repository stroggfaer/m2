<?php
namespace app\components;

use app\models\Clients;
use app\models\fitness\UserFitness;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use Yii;

class WMenuRepairs extends Widget{
    public $model;

    public function init() {
        parent::init();
        if ($this->model === null) {
            $this->model = false;
        }
    }
    public function run(){
        if (!$this->model) {
            return false;
        }else {
            ?>
            <div class="menu__devices">
                <div class="items desktop">
                    <?php foreach ($this->model->menuRepairs as $key=>$value ): ?>
                        <?php $active = (!empty($this->model->currentRepair->id) && $this->model->currentRepair->id == $value->id ? 'active' : '') ?>
                        <div class="item <?=$active?>">
                            <a href="/repair/<?=$value->url?>">
                                <div class="icon-menu icon-iphone"></div>
                                <div class="menu"><?=$value->title?></div>
                            </a>
                        </div>
                    <?php endforeach;  ?>
                </div>
                <div class="mobile">
                    <div class="select__mod">
                        <select class="select">
                            <?php foreach ($this->model->menuRepairs as $key=>$value ): ?>
                                <?php $active = (!empty($this->model->currentRepair->id) && $this->model->currentRepair->id == $value->id ? 'active' : '') ?>
                                <option <?=!empty($active) ? 'selected': '' ?>><a href=""><?=$value->title?></a></option>
                            <?php endforeach;  ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}