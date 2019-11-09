<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Migration;

use Citrus\CitrusException;
use Citrus\Command;
use Citrus\Migration;

/**
 * マイグレーションコマンド
 */
class MigrationCommand extends Command
{
    /** @var array command options */
    public $options = [
        'action::',
        'name:',
        'version:',
    ];



    /**
     * {@inheritDoc}
     *
     * @throws CitrusException
     */
    public function execute()
    {
        parent::execute();

        $action = $this->parameter('action');
        $name = $this->parameter('name');
        $version = $this->parameter('version');

        $migration = new Migration($this->configure);

        switch ($action)
        {
            // 生成処理
            case Migration::ACTION_GENERATE:
                $migration->generate($name);
                break;
            // マイグレーションUP実行
            case Migration::ACTION_MIGRATION_UP:
                $migration->up($version);
                break;
            // マイグレーションDOWN実行
            case Migration::ACTION_MIGRATION_DOWN:
                $migration->down($version);
                break;
            // マイグレーションREBIRTH実行
            case Migration::ACTION_MIGRATION_REBIRTH:
                $migration->rebirth($version);
                break;
            default:
        }
    }
}
