<?php

use yii\db\Migration;

/**
 * Class m210121_182635_loads
 */
class m210121_182635_loads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%loads}}', [
            'id' => $this->primaryKey()->comment('Код'),
            'path' => $this->string()->notNull()->comment('Путь к загрузке'),
            'images_list' => $this->string()->notNull()->comment('Упорядоченный список изображений'),
            'pdf_file' => $this->string()->notNull()->comment('Название Pdf-файла'),
            'pp_file' => $this->string()->Null()->comment('Название PowerPoint-файла'),
            'created_at' => $this->integer()->notNull()->comment('Время создания'),
            'updated_at' => $this->integer()->notNull()->comment('Время изменения'),
            'deleted' => $this->boolean()->defaultValue(false)->notNull()->comment('Удален'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%loads}}');
    }
}
