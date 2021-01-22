<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\HttpException;
use yii\helpers\Json;

/**
 * File - загружаемый Pdf-файл
 * 
 * @param Pdf-file $file загружаемый pdf-файл
 */
class File extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'pdf'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Pdf-файл',
        ];
    }

    /**
     * Сохраняем файл
     *
     * @throws HttpException 400 ошибки валидации
     */
    public function upload()
    {
        if (!file_exists(Yii::getAlias('@app') . "/file_loads")) {
            mkdir(Yii::getAlias('@app') . "/file_loads", 0777);
        }

        if ($this->validate()) {
            $path = Yii::getAlias('@app') . "/file_loads/" . Yii::$app->security->generateRandomString(32) . time();
            $name = $this->file->name;

            mkdir($path, 0777);

            $this->file->saveAs($path . "/$name");

            $images = json::encode(self::convert($path, $name));

            $load = new Loads();

            $load->path = $path;
            $load->images_list = $images;
            $load->pdf_file = $name;

            if ($load->validate()) {
                $load->save();
            } else {
                throw new HttpException(400, json::encode($load->errors));
            }
        } else {
            throw new HttpException(400, json::encode($this->errors['file']));
        }
    }

    /**
     * Конвертируем pdf-файл в png-изображения
     *
     * @throws HttpException 400 ошибки валидации
     * @return Array упорядоченный массив названий файлов изображений
     */
    private function convert($path, $namePdf)
    {
        mkdir($path . "/images");

        // количество листов в pdf-файле
        $pdf_content = file_get_contents("$path/$namePdf");
        $count = preg_match_all("/\/Page\W/", $pdf_content, $matches);

        // php-imagick
        $image = new \Imagick();

        $sortedImagesList = [];

        for ($i = 0; $i < $count; $i++) {
            $image->readimage("$path/$namePdf" . "[$i]");

            $name = ($i + 1) . '.png';
            $image->writeImage("$path/images/$name");

            $sortedImagesList[] = $name;
        }

        $image->clear();
        $image->destroy();

        return $sortedImagesList;
    }
}
