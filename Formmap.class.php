<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Citrus\Formmap\CitrusFormmapButton;
use Citrus\Formmap\CitrusFormmapElement;
use Citrus\Formmap\CitrusFormmapHidden;
use Citrus\Formmap\CitrusFormmapPassword;
use Citrus\Formmap\CitrusFormmapSearch;
use Citrus\Formmap\CitrusFormmapSelect;
use Citrus\Formmap\CitrusFormmapSubmit;
use Citrus\Formmap\CitrusFormmapText;
use Citrus\Formmap\CitrusFormmapTextarea;
use Exception;

class CitrusFormmap
{
    /** @var string message tag */
    const MESSAGE_TAG = 'formmap';

    /** @var bool is cache */
    public static $IS_CACHE = false;

    /** @var bool is initialize */
    public static $IS_INITIALIZED = false;

    /** @var array(string::'form id' => CitrusFormElement) */
    private $elements = [];

    /** @var array(string::'namespace' => array(string::'form id' => CitrusFormElement)) map array */
    private $maps = [];

    /** @var array(string::'namespace' => array(string::'form id' => string::'class name')) map array */
    private $classes = [];

    /** @var bool validate null is require safe */
    public $validate_null_safe = false;

    /** @var string[] ファイル読み込みリスト */
    private $loaded_files = [];

    /** @var bool bind済みかどうか */
    private $is_bound = false;


    /**
     * initialize formmap
     *
     * @param array $default_configure
     * @param array $configure_domain
     */
    public static function initialize($default_configure = [], $configure_domain = [])
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return ;
        }

        // configure
        $configure = CitrusConfigure::configureMerge('formmap', $default_configure, $configure_domain);

        // cache
        self::$IS_CACHE = $configure['cache'];

        // initialized
        self::$IS_INITIALIZED = true;
    }



    /**
     * formmap definition loader
     *
     * @param string|null $path
     */
    public function load(string $path = null)
    {
        // bad request
        if (is_null($path) === true)
        {
            return ;
        }

        // parse xml formmap

        // file exists xmls？
        if (file_exists($path) === false)
        {
            $path = sprintf('%s/%s', CitrusConfigure::$DIR_BUSINESS_FORMMAP, basename($path));
            if (file_exists($path) === false)
            {
                return ;
            }
        }

        // 多重読み込み防止
        if (in_array($path, $this->loaded_files) === true)
        {
            return ;
        }

        // load formmap
        $formlist = include($path);

        // parse formmap
        foreach ($formlist as $namespace => $formmaps)
        {
            foreach ($formmaps as $form_id => $formmap)
            {
                $class_name = $formmap['class'];
                $prefix     = CitrusNVL::ArrayVL($formmap, 'prefix', '');
                $_elements   = $formmap['elements'];

                // parse element
                foreach ($_elements as $element_id => $element)
                {
                    $form = null;
                    switch ($element['form_type']) {
                        case CitrusFormmapElement::FORM_TYPE_ELEMENT    : $form = new CitrusFormmapElement($element);   break;
                        case CitrusFormmapElement::FORM_TYPE_HIDDEN     : $form = new CitrusFormmapHidden($element);    break;
                        case CitrusFormmapElement::FORM_TYPE_PASSWD     : $form = new CitrusFormmapPassword($element);  break;
                        case CitrusFormmapElement::FORM_TYPE_SELECT     : $form = new CitrusFormmapSelect($element);    break;
                        case CitrusFormmapElement::FORM_TYPE_SUBMIT     : $form = new CitrusFormmapSubmit($element);    break;
                        case CitrusFormmapElement::FORM_TYPE_BUTTON     : $form = new CitrusFormmapButton($element);    break;
                        case CitrusFormmapElement::FORM_TYPE_TEXT       : $form = new CitrusFormmapText($element);      break;
                        case CitrusFormmapElement::FORM_TYPE_TEXTAREA   : $form = new CitrusFormmapTextarea($element);  break;
                        case CitrusFormmapElement::FORM_TYPE_SEARCH     : $form = new CitrusFormmapSearch($element);    break;
                        default                                         :                                               break;
                    }
                    // 外部情報の設定
                    $form->id = $element_id;
                    $form->prefix = $prefix;
                    // element_idの設定
                    $element_id = $form->prefix . $form->id;


                    $this->elements[$element_id]                    =  $form;
                    $this->maps[$namespace][$form_id][$element_id]  =& $this->elements[$element_id];
                    $this->classes[$namespace][$form_id]            = $class_name;
                }
                
            }
        }

        // 多重読み込み防止
        $this->loaded_files[] = $path;
        // 多重バインド防止
        $this->is_bound = false;
    }



    /**
     * form data binder
     *
     * @param bool $force 強制バインド
     */
    public function bind(bool $force = false)
    {
        // 多重バインド防止
        if ($this->is_bound === true && $force === false)
        {
            return ;
        }

        $request_list = CitrusSession::$request->properties()
                      + CitrusSession::$filedata->properties();

        $json_request_list = json_decode(file_get_contents('php://input'), true);
        if (is_null($json_request_list) === false)
        {
            $request_list += $json_request_list;
        }
        // CitrusRouterからのリクエストを削除
        if (isset($request_list['url']) === true)
        {
            unset($request_list['url']);
        }

        $prefix = CitrusNVL::ArrayVL($request_list, 'prefix', '');

        // $this->mapsには$this->elementsの参照から渡される。
        foreach ($request_list as $ky => $vl)
        {
            // imageボタン対応
            if (preg_match('/.*(_y|_x)$/i', $ky) > 0)
            {
                $ky = substr($ky, 0, -2);

                if (isset($this->elements[$prefix.$ky]) === true)
                {
                    if (is_array($this->elements[$prefix.$ky]->value) === false)
                    {
                        $this->elements[$prefix.$ky]->value = [];
                    }
                    $this->elements[$prefix.$ky]->value[] = $vl;
                }
                else
                {
                    $this->elements[$prefix.$ky] = new CitrusFormmapElement(array('id' => $prefix.$ky, 'value' => array($vl)));
                }
            }
            else
            {
                if (isset($this->elements[$prefix.$ky]) === true)
                {
                    $this->elements[$prefix.$ky]->value = $vl;
                }
                else
                {
                    $this->elements[$prefix.$ky] = new CitrusFormmapElement(array('id' => $prefix.$ky, 'value' => $vl));
                }
            }
        }

        // 多重バインド防止
        $this->is_bound = true;
    }



    /**
     * form data binder
     *
     * @param mixed|null $object
     * @param string     $prefix
     */
    public function bindObject($object = null, string $prefix = '')
    {
        $request_list  = get_object_vars($object);

        // $this->mapsには$this->elementsの参照から渡される。
        foreach ($request_list as $ky => $vl)
        {
            if (isset($this->elements[$prefix.$ky]) === true)
            {
                $this->elements[$prefix.$ky]->value = $vl;
            }
            else
            {
                $this->elements[$prefix.$ky] = new CitrusFormmapElement(['id' => $prefix.$ky, 'value' => $vl]);
            }
        }
    }



    /**
     * validate
     *
     * @param string|null $form_id
     * @return int
     * @throws CitrusException
     */
    public function validate(string $form_id = null) : int
    {
        try
        {
            $list = [];
            if (is_null($form_id) === true)
            {
                $list = $this->elements;
            }
            else
            {
                foreach ($this->maps as $ns_data)
                {
                    foreach ($ns_data as $data_id => $data)
                    {
                        if ($data_id === $form_id)
                        {
                            $list = $data;
                            break 2;
                        }
                    }
                }
            }
            $result = 0;
            /** @var CitrusFormmapElement $element */
            foreach ($list as $element)
            {
                $element->validate_null_safe = $this->validate_null_safe;
                // 比較チェック less than
                if (empty($list[$element->prefix.$element->lesser]) === false && $element->validateLess($list[$element->prefix.$element->less]) === false)
                {
                    $result++;
                }
                // 比較チェック greater than
                if (empty($list[$element->prefix.$element->greater]) === false && $element->validateGreater($list[$element->prefix.$element->greater]) === false)
                {
                    $result++;
                }
                $result += $element->validate();
            }
            return $result;
        }
        catch (Exception $e)
        {
            throw CitrusException::convert($e);
        }
    }



    /**
     * generation
     *
     * @param string $namespace
     * @param string $form_id
     * @return CitrusObject
     */
    public function generate(string $namespace, string $form_id)
    {
        $class_name = $this->classes[$namespace][$form_id];

        /** @var CitrusObject $object */
        $object = new $class_name();

        /** @var CitrusFormmapElement[] $properties */
        $properties = $this->maps[$namespace][$form_id];
        foreach ($properties as $one)
        {
            // object生成対象外はnullが設定されている
            if (is_null($one->property) === true)
            {
                continue;
            }
            $one->convertType();
            $value = $one->filter();
            $object->setFromContext($one->property, $value);
        }

        return $object;
    }

    public function __get($name)
    {
        return $this->elements[$name];
    }
}