<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Sqlmap;

use Citrus\Configure;
use Citrus\Database\Column;
use DOMDocument;
use DOMElement;
use DOMXPath;
use DOMNodeList;

/**
 * Sqlmapパーサー
 */
class Parser
{
    /** @var DOMDocument dom document */
    public $dom;

    /** @var DOMXPath dom xpath */
    public $xpath;

    /** @var Statement statement */
    public $statement;

    /** @var Column parameter */
    public $parameter;

    /** @var array parameters */
    public $parameter_list = [];

    /** @var string sqlmap path */
    public $_path;

    /** @var string transaction name */
    public $_transaction;

    /** @var string sqlmap inner id */
    public $_id;

    /** @var Column sqlmap parameter */
    public $_parameter;



    /**
     * sqlmap xml parser
     *
     * @param string                    $path
     * @param string                    $transaction
     * @param string                    $id
     * @param Column|null $parameter
     */
    public function parse(string $path, string $transaction, string $id, Column $parameter = null)
    {
        $this->_path = $path;
        $this->_transaction = $transaction;
        $this->_id = $id;
        $this->_parameter = $parameter;

        // initialize domdocument
        $this->dom = new DOMDocument();
        $this->dom->load(realpath($path));
        $this->xpath = new DOMXPath($this->dom);
        $nodeList = $this->xpath->query('/sqlMap/'.$transaction."[@id='".$id."']");
        if ($nodeList->length == 0)
        {
            trigger_error(sprintf('Warning: Undefined SQLMAP transaction "%s" in %s', $id, $path), E_USER_WARNING);
        }
        else if ($nodeList->length > 1)
        {
            trigger_error(sprintf('Warning: Duplicate defined SQLMAP transaction "%s" in %s', $id, $path), E_USER_WARNING);
        }
        $element = $this->xpath->query('/sqlMap/'.$transaction."[@id='".$id."']")->item(0);

        // statement
        $this->statement = new Statement($element->attributes);

        // parameter class & map
        $this->parameter = $parameter;

        // select nodes
        $nodes                  = $element->childNodes;
        $this->statement->query = $this->_nodes($nodes);

        // keyword replace
        if (empty($parameter->schema) === true)
        {
            $parameter->schema = Configure::$CONFIGURE_ITEM->database->schema;
        }
        if ($parameter->schema)
        {
            $this->statement->query = str_replace('{SCHEMA}', '"'.$parameter->schema.'".', $this->statement->query);
        }

        // parameters
        $_parameter_list= $this->parameter_list;
        $_query         = $this->statement->query;

        // dynamic parameter
        if (strrpos($_query, '#') !== false)
        {
            preg_match_all('/#[a-zA-Z0-9_\-\>\.]*#/', $_query, $matches, PREG_PATTERN_ORDER);

            foreach ($matches[0] as $one)
            {
                $match_code     = str_replace('#', '', $one);
                $replace_code   = ':'.str_replace('.', '__', $match_code);
                $_parameter_list[$replace_code] = $this->callProperty($this->parameter, $match_code);
                // query in pattern
                if (is_array($_parameter_list[$replace_code]) === true)
                {
                    $array_replace_codes = [];
                    foreach ($_parameter_list[$replace_code] as $ary_ky => $ary_vl)
                    {
                        $array_replace_codes[] = $replace_code.'_'.$ary_ky;
                        $_parameter_list[$replace_code.'_'.$ary_ky] = $ary_vl;
                    }
                    unset($_parameter_list[$replace_code]);
                    $replace_code = implode(', ', $array_replace_codes);
                }
                $_query = str_replace($one, $replace_code, $_query);
            }
        }

        // static parameter
        if (strrpos($_query, '$') !== false)
        {
            preg_match_all('/\$[a-zA-Z0-9_\-\>]*\$/', $_query, $matches, PREG_PATTERN_ORDER);
            foreach ($matches[0] as $one)
            {
                $match_code     = str_replace('$', '', $one);
                $_query = str_replace($one, $this->callProperty($this->parameter, $match_code), $_query);
            }
        }
        $_query = strtr($_query, ["\r"=>' ', "\n"=>' ', "\t"=>' ', '    ' => ' ', '  ' => ' ']);

        // parameters
        $this->parameter_list   = $_parameter_list;
        $this->statement->query = $_query;
    }



    /**
     * replace sqlmap parameter
     *
     * @param Column|null $parameter
     */
    public function replaceParameter(Column $parameter = null)
    {
        $keys = array_keys($this->parameter_list);
        foreach ($keys as $key)
        {
            $column_key = str_replace(':', '', $key);
            $this->parameter_list[$key] = $parameter->get($column_key);
        }
    }



    /**
     * text node parser
     * テキストノード処理
     *
     * @param string $text
     * @return Dynamic
     */
    protected static function _text(string $text) : Dynamic
    {
        $dynamic = new Dynamic();
        $dynamic->query = ' '.trim($text);

        return $dynamic;
    }



    /**
     * cdata node parser
     * CDATAノード処理
     *
     * @param string $cdata
     * @return Dynamic
     */
    protected static function _cdata(string $cdata) : Dynamic
    {
        $dynamic = new Dynamic();
        $dynamic->query = ' '.trim($cdata);

        return $dynamic;
    }



    /**
     * text node parser
     * テキストノード処理
     *
     * @param string $text
     * @return string
     */
    protected static function _textQuery(string $text) : string
    {
        return ' '. trim($text);
    }



    /**
     * cdata node parser
     * CDATAノード処理
     *
     * @param string $cdata
     * @return string
     */
    protected static function _cdataQuery(string $cdata) : string
    {
        return ' '. trim($cdata);
    }



    /**
     * dynamic element node parser
     * ダイナミックエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _dynamic(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);
        $this->_nodes($element->childNodes, $dynamic);

        return $dynamic;
    }



    /**
     * isNull element node parser
     * isNullエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isNull(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $property = $this->callProperty($this->parameter, $dynamic->property);
        if (is_null($property) === true || (is_string($property) === true && strtolower($property) == 'null'))
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * isNotNull element node parser
     * isNotNullエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isNotNull(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $property = $this->callProperty($this->parameter, $dynamic->property);

        if (is_null($property) === false)
        {
            if (is_string($property) === true)
            {
                if (strtolower($property) != 'null')
                {
                    $dynamic->query = $this->_nodes($element->childNodes);
                }
            }
            else
            {
                $dynamic->query = $this->_nodes($element->childNodes);
            }
        }
        return $dynamic;
    }



    /**
     * isEmpty element node parser
     * isEmptyエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isEmpty(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $property = $this->callProperty($this->parameter, $dynamic->property);

        if (empty($property) === true)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * isNotEmpty element node parser
     * isNotEmptyエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isNotEmpty(DOMElement $element)
    {
        $dynamic = new Dynamic($element->attributes);

        $property = $this->callProperty($this->parameter, $dynamic->property);

        if (empty($property) === false)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * isEqual element node parser
     * isEqualエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isEqual(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $compare = ($dynamic->compare_property  ? $this->parameter->{$dynamic->compare_property} : $dynamic->compare_value);
        $property = $this->callProperty($this->parameter, $dynamic->property);

        if ($property == $compare)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * isNotEqual element node parser
     * isNotEqualエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isNotEqual(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $compare = ($dynamic->compare_property ? $this->parameter->{$dynamic->compare_property} : $dynamic->compare_value);
        $property = $this->callProperty($this->parameter, $dynamic->property);

        if ($property != $compare)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }

    /**
     * isGreaterThan element node parser
     * isGreaterThanエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isGreaterThan(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $compare = ($dynamic->compare_property ? $this->parameter->{$dynamic->compare_property} : $dynamic->compare_value);

        if ($this->parameter->{$dynamic->property} > $compare)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * isGreaterEqual element node parser
     * isGreaterEqualエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isGreaterEqual(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $compare = ($dynamic->compare_property ? $this->parameter->{$dynamic->compare_property} : $dynamic->compare_value);

        if ($this->parameter->{$dynamic->property} >= $compare)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * isLessThan element node parser
     * isLessThanエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isLessThan(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $compare = ($dynamic->compare_property ? $this->parameter->{$dynamic->compare_property} : $dynamic->compare_value);

        if ($this->parameter->{$dynamic->property} < $compare)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * isLessEqual element node parser
     * isLessEqualエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isLessEqual(DOMElement $element)
    {
        $dynamic = new Dynamic($element->attributes);

        $compare = ($dynamic->compare_property ? $this->parameter->{$dynamic->compare_property} : $dynamic->compare_value);

        if ($this->parameter->{$dynamic->property} <= $compare)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }

        return $dynamic;
    }



    /**
     * isNumeric element node parser
     * isNumericエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isNumeric(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $property = $this->callProperty($this->parameter, $dynamic->property);

        if (is_numeric($property) === true)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * isDatetime element node parser
     * isDatetimeエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isDatetime(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $property = $this->callProperty($this->parameter, $dynamic->property);

        if (strtotime($property) !== false)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * isTrue element node parser
     * isTrueエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _isTrue(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);

        $property = $this->callProperty($this->parameter, $dynamic->property);

        if ($property === true)
        {
            $dynamic->query = $this->_nodes($element->childNodes);
        }
        return $dynamic;
    }



    /**
     * node element general parser
     * node エレメント汎用処理
     *
     * @param DOMNodeList              $nodes
     * @param Dynamic|null $dynamic
     * @return string
     */
    protected function _nodes(DOMNodeList $nodes, Dynamic $dynamic = null)
    {
        $size = $nodes->length;
        for ($i = 0; $i < $size; $i++)
        {
            $item = $nodes->item($i);

            if (is_null($dynamic) === true)
            {
                $dynamic = new Dynamic();
            }

            if ($item->nodeName == '#text')
            {
                $text_query = self::_textQuery($item->nodeValue);
                $dynamic->concatenateString($text_query);
            }
            else if ($item->nodeName == '#cdata-section')
            {
                $cdata_query = self::_cdataQuery($item->nodeValue);
                $dynamic->concatenateString($cdata_query);
            }
            else if ($item->nodeName == '#comment')
            {
                // 処理なし
                continue;
            }
            else
            {
                $item_node = $this->{'_'.$item->nodeName}($item);
                $dynamic->concatenate($item_node);
            }
        }
        return $dynamic->query;
    }



    /**
     * include element node parser
     * includeエレメント処理
     *
     * @param DOMElement $element
     * @return Dynamic
     */
    protected function _include(DOMElement $element) : Dynamic
    {
        $dynamic = new Dynamic($element->attributes);
        $include = new Parser();
        $include->parse($this->_path, $this->_transaction, $dynamic->refid, $this->_parameter);
        $dynamic->query = $include->statement->query;
        $this->parameter_list += $include->parameter_list;

        return $dynamic;
    }



    /**
     * call nested property
     * ネストの深いプロパティーを取得する。
     *
     * @param Column $parameter
     * @param string|null          $property
     * @return string|null
     */
    private function callProperty(Column $parameter, string $property)
    {
        $property_list  = explode('.', $property);
        $result = $parameter;
        foreach ($property_list as $one)
        {
            $result = $result->$one;
        }
        return $result;
    }



    /**
     * generate sqlmap xml parser
     *
     * @param string                    $path
     * @param string                    $transaction
     * @param string                    $id
     * @param Column|null $parameter
     * @return Parser
     */
    public static function generateParser(string $path, string $transaction, string $id, Column $parameter = null) : Parser
    {
        $parser = new static();
        $parser->parse($path, $transaction, $id, $parameter);
        return $parser;
    }
}
