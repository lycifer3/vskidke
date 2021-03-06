<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "discounts".
 *
 * @property integer $discount_id
 * @property integer $user_id
 * @property integer $category_id
 * @property integer $city_id
 * @property string  $discount_view
 * @property string $discount_title
 * @property string $discount_text
 * @property string $discount_date_start
 * @property string $discount_date_end
 * @property integer $discount_app
 * @property integer $discount_view_email
 * @property integer $discount_price
 * @property integer $discount_old_price
 * @property integer $discount_percent
 * @property string  $discount_gift
 * @property string $img
 * @property string $date_create
 *
 * @property Categories $category
 * @property City $city
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
            [['user_id', 'category_id', 'discount_price', 'discount_old_price', 'discount_view', 'discount_app', 'discount_view_email',], 'integer'],
            [['discount_percent'], 'integer', 'max' => 99, 'message' => 'Максимальный процент 99'],
            [['discount_text', 'discount_gift', 'img'], 'string'],
            [['discount_date_start', 'discount_date_end'], 'safe'],
            [['discount_title', 'img'], 'string', 'max' => 255],
            [['date_create'], 'date', 'format' => 'Y-m-d'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::className(), 'targetAttribute' => ['category_id' => 'category_id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'city_id']],
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
            'discount_gift' => 'Подарок'
        ];
    }

    public function fields() {
        return ArrayHelper::merge(
            parent::fields(),
            [
                'img' => function($model, $field) {
                    return $model->getImg('small');
                },
            ]
        );
    }

    public function extraFields() {
        return [
            'address',
            'comments'
        ];
    }

    public function resolveFields() {
//        return $this->fields();
        return ArrayHelper::merge(
            $this->fields(),
            $this->extraFields()
        );
    }

    public function getImg($size = 'original')
    {
        $sizes = ['original', 'big', 'medium', 'small'];
        if (!in_array($size, $sizes)) {
            throw new \yii\web\HttpException(404, 'Изображение не найдено');
        }
        if ($size == 'original') {
            $img = $this->img;
        } else {
            $img = str_replace('/original/', '/thumbs_' . $size . '/', $this->img);
        }
        $newImg = Yii::getAlias('@frontend/web/upload') . $img;

        if (!file_exists($newImg) || is_dir($newImg)) {
            $img = '/../images/error_photo2.png';
        } else {
            $img = Yii::$app->params['uploadUrl'] . $img;
        }

        return $img;
    }

    public static function getColorClass($percent) {
        if($percent >= 0 && $percent <= 10) {
            return 'purple';
        } elseif ($percent >= 11 && $percent <= 20) {
            return 'violet';
        }elseif ($percent >= 21 && $percent <= 30) {
            return 'violet-light';
        }elseif ($percent >= 31 && $percent <= 40) {
            return 'blue';
        }elseif ($percent >= 41 && $percent <= 50) {
            return 'blue-light';
        }elseif ($percent >= 51 && $percent <= 60) {
            return 'green';
        }elseif ($percent >= 61 && $percent <= 70) {
            return 'green-light';
        }elseif ($percent >= 71 && $percent <= 80) {
            return 'orange';
        }elseif ($percent >= 81 && $percent <= 90) {
            return 'orange-light';
        }elseif ($percent >= 91 && $percent <= 100) {
            return 'purple-light';
        }
    }

    public function getCompany() {
        $user = $this->getUser()->with('profile')->one();

        return $user->relatedRecords['profile'];
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getComments() {
        return $this->hasMany(Comment::className(), ['discount_id' => 'discount_id']);
    }

    public function getAddress() {
        return $this->hasMany(CompanyAddresses::className(), ['id' => 'address_id'])->viaTable(DiscountAddresses::tableName(), ['discount_id' => 'discount_id']);
    }

    public function getDiscount_addresses() {
        return $this->hasOne(DiscountAddresses::className(), ['discount_id' => 'discount_id']);
    }
}
