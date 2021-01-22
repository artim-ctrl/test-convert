<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\UploadedFile;
use app\models\File;
use app\models\Loads;
use app\models\LoadsSearch;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'convert', 'original', 'images', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'convert', 'original', 'images', 'delete'],
                        'allow' => true
                    ],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Страница загрузки файла и отображения всех загруженных
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LoadsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Загружаем файл, разделяем на изображения, все сохраняем
     *
     * @return boolean
     * @throws HttpException 400 валидация на загружаемом файле и на сохраняемом
     * @throws HttpException 405 можем прислать только post
     */
    public function actionConvert()
    {
        $model = new File();
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            $model->upload();
        } else {
            throw new HttpException(405, "Тип запроса должен быть POST");
        }

        return true;
    }

    /**
     * Получаем оригинальный pdf-файл
     *
     * @param integer $id идентификатор записи
     * @throws HttpException 404 не нашли запись/ файл
     */
    public function actionOriginal($id)
    {
        if ($load = Loads::findOne($id)) {
            if (file_exists("$load->path/$load->pdf_file")) {
                Yii::$app->response->sendFile("$load->path/$load->pdf_file");
            } else {
                throw new HttpException(404, 'Такого файла не существует.');
            }
        } else {
            throw new HttpException(404, 'Такой записи не существует.');
        }
    }

    /**
     * Получаем архив изображений
     * Сначала формируем его, затем отправляем и удаляем
     *
     * @param integer $id идентификатор записи
     * @throws HttpException 404 не нашли запись/ файлы
     */
    public function actionImages($id)
    {
        if ($load = Loads::findOne($id)) {
            $imagesList = json::decode($load->images_list);

            if (count($imagesList) !== 0) {
                $files = [];

                foreach ($imagesList as $image) {
                    $files[] = "$load->path/images/$image";
                }

                $zip = Yii::$app->zipper->create("$load->path/archive.zip", $files, true, 'zip');

                Yii::$app->response->sendFile("$load->path/archive.zip")->on(\yii\web\Response::EVENT_AFTER_SEND, function($event) {
                    unlink($event->data);
                }, "$load->path/archive.zip");
            } else {
                throw new HttpException(404, 'Изображений не существует.');
            }
        } else {
            throw new HttpException(404, 'Такой записи не существует.');
        }
    }

    /**
     * Удаляем загрузку
     *
     * @param integer $id идентификатор записи
     * @throws HttpException 404 не нашли запись
     * @return boolean возвращаем true если все в порядке
     */
    public function actionDelete($id)
    {
        if ($load = Loads::findOne($id)) {
            $load->remove();

            return true;
        } else {
            throw new HttpException(404, 'Такой записи не существует.');
        }
    }
}
