<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\Xml;
use DOMNamedNodeMap;

class Dynamic
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
            $items = Xml::toList($attributes);
            foreach ($items as $name => $value)
            {
                switch ($name)
                {
                    case 'id'               :$this->id              = $value; break;
                    case 'refid'            :$this->refid           = $value; break;
                    case 'prepend'          :$this->prepend         = $value; break;
                    case 'property'         :$this->property        = $value; break;
                    case 'compareProperty'  :$this->compare_property= $value; break;
                    case 'compareValue'     :$this->compare_value   = $value; break;
                    default:
                }
            }
        }
    }



    /**
     * concatenate this
     *
     * @param Dynamic $dynamic
     */
    public function concatenate(Dynamic $dynamic)
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
     * @param Dynamic $dynamic
     * @param Dynamic $var
     * @return string
     */
    public static function combine(Dynamic $dynamic, Dynamic $var) : string
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