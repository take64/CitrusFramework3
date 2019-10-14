<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Citrus;
use Citrus\Configure;
use Citrus\Struct;
use Citrus\Database\Column\Base;
use Citrus\Sqlmap\Condition;

class Column extends Struct
{
    use Base;



    /** @var string schema */
    public $schema = null;

    /** @var Condition condition */
    public $condition;



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->schema = Configure::$CONFIGURE_ITEM->database->schema;
    }



    /**
     * {@inheritdoc}
     */
    public function properties() : array
    {
        $properties = get_object_vars($this);
        unset($properties['schema']);
        unset($properties['condition']);
        foreach ($properties as $ky => $vl)
        {
            if (is_bool($vl) === true)
            {
                unset($properties[$ky]);
            }
        }

        return $properties;
    }



    /**
     * call primary keys
     *
     * @return string[]
     */
    public function callPrimaryKeys() : array
    {
        return [];
    }



    /**
     * call condition
     *
     * @return Column
     */
    public function callCondition()
    {
        if (is_null($this->condition) === true)
        {
            $this->condition = new Column();
        }
        return $this->condition;
    }



    /**
     * to condition
     *
     * @return Column
     */
    public function toCondition()
    {
        $condition_calss_name = get_class($this->callCondition());
        $_condition = new $condition_calss_name();

        $primary_keys = $this->callPrimaryKeys();
        foreach ($primary_keys as $primary_key)
        {
            if (isset($this->$primary_key) === true && is_null($this->$primary_key) === false)
            {
                $_condition->$primary_key = $this->$primary_key;
            }
        }

        return $_condition;
    }



    /**
     * get condition
     *
     * @return Column
     */
    public function getCondition()
    {
        if (is_null($this->condition) === true)
        {
            $this->condition = $this->toCondition();
        }
        return $this->condition;
    }



    /**
     * complete insert column
     *
     * @param string|null $timestamp
     */
    public function completeRegistColumn(string $timestamp = null)
    {
        if (is_null($this->schema) === true)
        {
            $this->schema = Configure::$CONFIGURE_ITEM->database->schema;
        }
        if (is_null($timestamp) === true)
        {
            $timestamp = Citrus::$TIMESTAMP_FORMAT;
        }
        $this->registed_at = $timestamp;
        $this->modified_at = $timestamp;
    }



    /**
     * complete modify column
     *
     * @param string|null $timestamp
     */
    public function completeModifyColumn(string $timestamp = null)
    {
        if (is_null($this->schema) === true)
        {
            $this->schema = Configure::$CONFIGURE_ITEM->database->schema;
        }
        if (is_null($timestamp) === true)
        {
            $timestamp = Citrus::$TIMESTAMP_FORMAT;
        }
        $this->modified_at = $timestamp;
    }



    /**
     * null to blank
     */
    public function null2blank()
    {
        $properties = $this->properties();
        foreach ($properties as $ky => $vl)
        {
            if (is_null($vl) === true)
            {
                $this->$ky = '';
            }
        }
    }



    /**
     * all nullify
     */
    public function nullify()
    {
        $properties = $this->properties();
        foreach ($properties as $ky => $vl)
        {
            if (in_array($ky, ['schema', 'condition']) === false)
            {
                $this->$ky = null;
            }
        }
    }



    /**
     * bind column
     */
    public function bindColumn()
    {
    }



    /**
     * obeject vars getter of (not null property) and (ignore slasses property)
     *
     * @param string[] $class_names 削除したいプロパティーを持つクラスのクラス名配列
     * @return array
     */
    public function notNullPropertyAndIgnoreClassProperties(array $class_names = [])
    {
        // null以外のプロパティー
        $not_nulll_properties = $this->notNullProperties();

        // 指定クラスのプロパティーを削除する
        foreach ($class_names as $class_name)
        {
            // 指定クラスのプロパティーを削除する
            $class_property_keys = array_keys(get_class_vars($class_name));
            foreach ($class_property_keys as $class_property_key)
            {
                if (array_key_exists($class_property_key, $not_nulll_properties) === true)
                {
                    unset($not_nulll_properties[$class_property_key]);
                }
            }
        }

        return $not_nulll_properties;
    }
}