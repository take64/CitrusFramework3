<?php
/**
 * Dynamic.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Sqlmap
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;


use Citrus\CitrusXml;
use DOMNamedNodeMap;

class CitrusSqlmapDynamic
{
    /** @var string element id */
    public $id;

    /** @var string element refid */
    public $refid;

    /** @var string element prepend */
    public $prepend;

    /** @var string element property */
    public $property;

    /** @var string element compare property */
    public $compare_property;

    /** @var string element compare value */
    public $compare_value;

    /** @var string element query */
    public $query;



    /**
     * constructor.
     *
     * @param DOMNamedNodeMap|null $attributes
     */
    public function __construct(DOMNamedNodeMap $attributes = null)
    {
        if (is_null($attributes) === false)
        {
            $this->id               = CitrusXml::getNamedItemValue($attributes, 'id');
            $this->refid            = CitrusXml::getNamedItemValue($attributes, 'refid');
            $this->prepend          = CitrusXml::getNamedItemValue($attributes, 'prepend');
            $this->property         = CitrusXml::getNamedItemValue($attributes, 'property');
            $this->compare_property = CitrusXml::getNamedItemValue($attributes, 'compareProperty');
            $this->compare_value    = CitrusXml::getNamedItemValue($attributes, 'compareValue');
        }
    }



    /**
     * concatenate this
     *
     * @param CitrusSqlmapDynamic $dynamic
     */
    public function concatenate(CitrusSqlmapDynamic $dynamic)
    {
        $_prepend   = $dynamic->getPrepend();
        $_query     = $dynamic->getQuery();

        $this_query = trim($this->query);
        $param_query= trim($_query);

        if (empty($param_query) === false)
        {
            // this と arg のオブジェクト内に query が存在したら、 prepend でコンカチする
            if (empty($this_query) === false)
            {
                if ($_prepend)
                {
                    $_prepend = ' '.$_prepend.' ';
                }
                $this->concatenateString($_prepend . $_query);
            }
            else
            {
                $this->concatenateString($_query);
            }
        }
    }



    /**
     * concatenate this
     *
     * @param string $query
     */
    public function concatenateString(string $query)
    {
        $param_query= trim($query);

        if (empty($param_query) === false)
        {
            $this->query .= $query;
        }
    }



    /**
     * combine other to other
     *
     * @param CitrusSqlmapDynamic $dynamic
     * @param CitrusSqlmapDynamic $var
     * @return string
     */
    public static function combine(CitrusSqlmapDynamic $dynamic, CitrusSqlmapDynamic $var) : string
    {
        if (trim($dynamic->query) && trim($var->query))
        {
            if (empty($var->prepend) === false)
            {
                $var->prepend = ' '.$var->prepend.' ';
            }
            return $dynamic->query . $var->prepend . $var->query;
        }
        return '';
    }



    /**
     * getter property prepend
     *
     * @return string
     */
    public function getPrepend() : string
    {
        if (isset($this->prepend) === true)
        {
            return $this->prepend;
        }
        return '';
    }



    /**
     * getter property query
     *
     * @return string
     */
    public function getQuery() : string
    {
        if (isset($this->query) === true)
        {
            return $this->query;
        }
        return '';
    }
}