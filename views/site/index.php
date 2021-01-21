<?php

/* @var $this \yii\web\View */

$this->title = 'Convert';

$this->registerJsFile('js/main.js', ['depends' => [\yii\web\JqueryAsset::classname()]]);
?>

<div class="index">
    <form id="form" action="">
        <input type="file" name="File[file]">
    </form>

    <button id="send">send</button>
</div>
