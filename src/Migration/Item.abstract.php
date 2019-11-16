<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Migration;

abstract class Item
{
    /** @var string object name */
    public $object_name = '';



    /**
     * migration.sh up
     */
    abstract public function up();



    /**
     * migration.sh down
     */
    abstract public function down();



    /**
     * バージョンの取得
     *
     * @return string バージョン取得
     */
    public function version(): string
    {
        // '_' で分割した２番目の要素がバージョン
        $class_names = explode('_', static::class);
        return $class_names[1];
    }
}
