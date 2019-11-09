<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Command;

/**
 * データベースエンティティ生成コマンド
 */
class GenerateCommand extends Command
{
    /** @var array command options */
    public $options = [
        'type::',
        'table_name::',
        'class_prefix:',
    ];



    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        parent::execute();

        $type = $this->parameter('type');
        $table_name = $this->parameter('table_name');
        $class_prefix = $this->parameter('class_prefix');

        $generate = new Generate($this->configure);

        // 実行
        switch ($type)
        {
            // Property生成処理
            case Generate::TYPE_PROPERTY:
                $generate->property($table_name, $class_prefix);
                break;
            // Dao生成処理
            case Generate::TYPE_DAO:
                $generate->dao($table_name, $class_prefix);
                break;
            // Condition生成処理
            case Generate::TYPE_CONDITION:
                $generate->condition($table_name, $class_prefix);
                break;
            // Property,Dao,Condition生成処理
            case Generate::TYPE_ALL:
                $generate->all($table_name, $class_prefix);
                break;
            default:
        }
    }
}