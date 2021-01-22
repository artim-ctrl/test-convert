<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "loads".
 *
 * @property int $id Код
 * @property string $path Путь к загрузке
 * @property string $images_list Упорядоченный список изображений
 * @property string|null $pdf_file Название Pdf-файла
 * @property int $created_at Время создания
 * @property int $updated_at Время изменения
 * @property bool $deleted Удален
 */
class Loads extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loads';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['path', 'images_list'], 'required'],
            [['deleted'], 'boolean'],
            [['path', 'images_list', 'pdf_file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Код',
            'path' => 'Путь к загрузке',
            'images_list' => 'Упорядоченный список изображений',
            'pdf_file' => 'Название Pdf-файла',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
            'deleted' => 'Удален',
        ];
    }

    /**
     * Удаляем текущую загрузку
     *
     */
    public function remove()
    {
        $this->deleted = true;

        $this->save();
    }
}
