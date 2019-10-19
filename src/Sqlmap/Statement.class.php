<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\Xml;
use DOMNamedNodeMap;

class Statement
{
    /** @var string element id */
    public $id = '';

    /** @var string element parameter class */
    public $parameter_class = null;

    /** @var string element parameter map */
    public $parameter_map = null;

    /** @var string element result class */
    public $result_class = null;

    /** @var string element result map */
    public $result_map = null;

    /** @var string element query */
    public $query = '';



    /**
     * constructor.
     *
     * @param DOMNamedNodeMap|null $attributes
     */
    public function __construct(DOMNamedNodeMap $attributes = null)
    {
        if (is_null($attributes) === false)
        {
            $this->id               = Xml::getNamedItemValue($attributes, 'id');
            $this->result_class     = Xml::getNamedItemValue($attributes, 'resultClass');
            $this->result_map       = Xml::getNamedItemValue($attributes, 'resultMap');
            $this->parameter_class  = Xml::getNamedItemValue($attributes, 'parameterClass');
            $this->parameter_map    = Xml::getNamedItemValue($attributes, 'parameterMap');
        }
    }



    /**
     * statement parameter getter
     * statement パラメータ格納方法取得
     *
     * @return null|string
     */
    public function getParameter()
    {
        if (is_null($this->parameter_class) === false)
        {
            return $this->parameter_class;
        }
        else if (is_null($this->parameter_map) === false)
        {
            return $this->parameter_map;
        }
        return null;
    }



    /**
     * statement result getter
     * statement 結果格納方法取得
     *
     * @return null|string
     */
    public function getResult()
    {
        if (is_null($this->result_class) === false)
        {
            return $this->result_class;
        }
        else if (is_null($this->result_map) === false)
        {
            return $this->result_map;
        }
        return null;
    }
}