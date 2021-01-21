<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\HttpException;
use yii\helpers\json;

/**
 * File - загружаемый Pdf-файл
 */
class File extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => 'pdf'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Pdf-файл',
        ];
    }

    public function upload()
    {
        // $excel = 'null';

        // if ($this->excel != '') {
        //     if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/frontend/web/imports')) {
        //         mkdir($_SERVER['DOCUMENT_ROOT']."/frontend/web/imports", 0777);                    
        //     }

        //     if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/frontend/web/imports/uploads')) {
        //         mkdir($_SERVER['DOCUMENT_ROOT']."/frontend/web/imports/uploads", 0777);
        //     }
            
        //     $excel='/frontend/web/imports/uploads/' .Users::generatePassword(). $this->excel->baseName . '.' . $this->excel->extension;
        //     $this->excel->saveAs($_SERVER['DOCUMENT_ROOT'].$excel);
        // }

        // return $excel;

        if (!file_exists(Yii::getAlias('@app') . "/file_loads")) {
            mkdir(Yii::getAlias('@app') . "/file_loads", 755);
        }

        if ($this->validate()) {
            $path = Yii::getAlias('@app') . "/file_loads/" . Yii::$app->security->generateRandomString(32) . time();
            $name = $this->file->name;

            mkdir($path, 755);

            $this->file->saveAs($path . "/$name");

            self::convert($path, $name);
        } else {
            throw new HttpException(400, json::encode($this->errors['file']));
        }
    }

    private function convert($path, $namePdf)
    {
        mkdir($path . "/images");

        $image = new Imagick();

        // $image->readimage($path . "/$name[0]");
        // $image->setImageFormat('png');
        // $image->writeImage('1.png');
        // $image->clear();
        // $image->destroy();
    }
}
