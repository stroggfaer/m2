<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "apple_services".
 *
 * @property int $id
 * @property int $city_id
 * @property string $title
 * @property string $title_seo
 * @property string $address
 * @property string $metro
 * @property string $time
 * @property string $description
 * @property string $map_lat
 * @property string $map_lon
 * @property string $text
 * @property string $value
 * @property int $status
 */
class AppleServices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'apple_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'status'], 'integer'],
            [['text'], 'string'],
            [['title', 'title_seo', 'time', 'map_lat', 'map_lon', 'value','url'], 'string', 'max' => 68],
            [['address'], 'string', 'max' => 128],
            [['metro','phone'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 328],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'Город',
            'title' => 'Название',
            'title_seo' => 'SEO Название',
            'address' => 'Адресс',
            'metro' => 'Метро',
            'time' => 'Время работы',
            'phone' => 'Телефон',
            'description' => 'Описание',
            'map_lat' => 'Map Lat',
            'map_lon' => 'Map Lon',
            'text' => 'Текст',
            'value' => 'Значение',
            'status' => 'Статус',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }


}
