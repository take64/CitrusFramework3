<?php
/**
 * Column.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Database
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;


use Citrus\Citrus;
use Citrus\CitrusConfigure;
use Citrus\CitrusObject;

class CitrusDatabaseColumn extends CitrusObject
{
    /** @var string status */
    public $status = 0;

    /** @var string registed_at */
    public $registed_at;

    /** @var string modified_at */
    public $modified_at;

    /** @var string rowid */
    public $rowid;

    /** @var string rev */
    public $rev = 1;

    /** @var string schema */
    public $schema = null;

    /** @var CitrusSqlmapCondition condition */
    public $condition;



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->schema = CitrusConfigure::$CONFIGURE_ITEM->database->schema;
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
     * call condition
     *
     * @return CitrusDatabaseColumn
     */
    public function callCondition()
    {
        if (is_null($this->condition) === true)
        {
            $this->condition = new CitrusDatabaseColumn();
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
            $this->schema = CitrusConfigure::$CONFIGURE_ITEM->database->schema;
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
            $this->schema = CitrusConfigure::$CONFIGURE_ITEM->database->schema;
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
}