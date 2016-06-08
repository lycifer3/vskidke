<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "discounts".
 *
 * @property integer $discount_id
 * @property integer $user_id
 * @property integer $category_id
 * @property integer $city_id
 * @property string $discount_title
 * @property string $discount_text
 * @property string $discount_date_start
 * @property string $discount_date_end
 * @property integer $discount_app
 * @property integer $discount_view_email
 * @property integer $discount_price
 * @property integer $discount_old_price
 * @property integer $discount_percent
 * @property string $img
 *
 * @property Categories $category
 * @property Sities $city
 * @property User $user
 */
class Discounts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'discounts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'category_id', 'discount_title', 'discount_text', 'discount_date_start', 'discount_date_end'], 'required', 'message' => 'Поле не может быть пустым'],
            [['user_id', 'category_id', 'city_id', 'discount_price', 'discount_old_price', 'discount_percent'], 'integer'],
            [['discount_text', 'discount_app', 'discount_view_email'], 'string'],
            [['discount_date_start', 'discount_date_end'], 'safe'],
            [['discount_title', 'img'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::className(), 'targetAttribute' => ['category_id' => 'category_id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sities::className(), 'targetAttribute' => ['city_id' => 'city_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'discount_id' => 'Discount ID',
            'user_id' => 'User ID',
            'category_id' => 'Рубрика',
            'city_id' => 'Город',
            'discount_title' => 'Название скидки',
            'discount_text' => 'Текст скидки',
            'discount_date_start' => 'Дата начала скидки',
            'discount_date_end' => 'Дата окончания скидки',
            'discount_app' => 'Скидка в приложении',
            'discount_view_email' => 'Скрыть email',
            'discount_price' => 'Новая цена',
            'discount_old_price' => 'Старая цена',
            'discount_percent' => 'Процент скидки',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Categories::className(), ['category_id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Sities::className(), ['city_id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
