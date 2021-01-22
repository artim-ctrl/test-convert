<?php

/* @var $this \yii\web\View */

use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Convert';

$this->registerJsFile('js/main.js', ['depends' => [\yii\web\JqueryAsset::classname()]]);
?>

<div class="index">
    <form id="form" action="">
        <input type="file" name="File[file]">

        <button id="send" type="button">send</button>

        <span id="statusText"></span>
    </form>

    <? Pjax::begin([
        'id' => 'pjaxTableLoads',
    ]) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'format' => 'raw',
                    'attribute' => 'pdf_file',
                    'value' => function ($model) {
                        return Html::a('Скачать pdf-файл', "/pdf?id=$model->id", [
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'pp_file',
                    'value' => function ($model) {
                        if (!!$model->pp_file) {
                            return Html::a('Скачать powerpoint-файл', "/pp?id=$model->id", [
                                'target' => '_blank',
                                'data-pjax' => '0',
                            ]);
                        } else {
                            return 'Файла нет';
                        }
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'images_list',
                    'value' => function ($model) {
                        return Html::a('Скачать изображения', "/images?id=$model->id", [
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                    }
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            return Html::button('Удалить', [
                                'onclick' => "remove($model->id)",
                            ]);
                        },
                    ],
                ],
            ]
        ]) ?>
    <? Pjax::end() ?>
</div>
