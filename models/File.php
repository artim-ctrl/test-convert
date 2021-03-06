<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\HttpException;
use yii\helpers\Json;
use NcJoes\OfficeConverter\OfficeConverter;

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
            [['file'], 'file', 'extensions' => 'pdf, ppt, pptx'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Загружаемый файл',
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
            $name = $this->file->baseName;
            $extension = $this->file->extension;

            mkdir($path, 0777);

            $this->file->saveAs("$path/$name.$extension");

            // если загрузили не pdf - конвертируем в pdf, сохраняя оба файла
            if ($extension !== 'pdf') {
                $converter = new OfficeConverter("$path/$name.$extension");
    
                $converter->convertTo("$name.pdf");
            }

            $images = json::encode(self::convertPdfToPng($path, "$name.pdf"));

            $load = new Loads();

            $load->path = $path;
            $load->images_list = $images;
            $load->pdf_file = "$name.pdf";

            if ($extension !== 'pdf') {
                $load->pp_file = "$name.$extension";
            }

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
    private function convertPdfToPng($path, $namePdf)
    {
        mkdir("$path/images");

        // количество листов в pdf-файле
        $count = $this->checkCountPage("$path/$namePdf");

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

    /**
     * Определение количества страниц в pdf-файле
     *
     * @param string $path путь к pdf-файлу
     * @return integer количество
     */
    private function checkCountPage($path)
    {
        $f = fopen($path, "r");
        $count = 0;

        while(!feof($f)) {
            $line = fgets($f, 255);

            if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
                preg_match('/[0-9]+/', $matches[0], $matches2);

                if ($count < $matches2[0]) {
                    $count = $matches2[0];
                }
            } 
        }

        fclose($f);

        return $count;
    }
}
