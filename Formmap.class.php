<?php
/**
 * Formmap.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  .
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Citrus\Formmap\CitrusFormmapElement;
use Citrus\Formmap\CitrusFormmapText;

class CitrusFormmap
{

    /** @var bool is cache */
    public static $IS_CACHE = false;

    /** @var bool is initialize */
    public static $IS_INITIALIZED = false;



    /**
     * initialize formmap
     *
     * @param array $default_configure
     * @param array $configure
     */
    public static function initialize($default_configure = [], $configure = [])
    {
        // is initialized
        if (self::$IS_INITIALIZED === true)
        {
            return ;
        }

        // configure
        $formmap = [];
        $formmap = array_merge($formmap, CitrusNVL::ArrayVL($default_configure, 'formmap', []));
        $formmap = array_merge($formmap, CitrusNVL::ArrayVL($configure, 'formmap', []));

        // cache
        self::$IS_CACHE = $formmap['cache'];


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

//        // CitrusCache Use possible
//        if (self::$is_cache === true)
//        {
//            $cache_maps_data        = CitrusCache::get(CitrusConfigure::$APPLICATION_CD.':formMap:maps:'.$namespace);
//            $cache_maps_result      = CitrusCache::getStatus();
//
//            $cache_classes_data     = CitrusCache::get(CitrusConfigure::$APPLICATION_CD.':formMap:classes:'.$namespace);
//            $cache_classes_result   = CitrusCache::getStatus();
//
//            if ($cache_maps_result    === CitrusCache::STATUS_SUCCESS
//                && $cache_classes_result === CitrusCache::STATUS_SUCCESS
//                && $cache_maps_data    !== null
//                && $cache_classes_data !== null)
//            {
//                foreach ($cache_maps_data as $cache_ns_name => $cache_ns_data)
//                {
//                    foreach ($cache_ns_data as $cache_form_id => $cache_form_data)
//                    {
//                        $this->elements[$cache_form_id]                         =  $cache_form_data;
//                        $this->maps[$namespace][$cache_ns_name][$cache_form_id] =& $this->elements[$cache_form_id];
//                    }
//                }
//
//                $this->classes[$namespace] = $cache_classes_data;
//                return ;
//            }
//        }

        // parse xml formmap

        // file exists xmls？
        if (file_exists($path) === false)
        {
            $path = sprintf('%s/%s', Citrus::$DIR_BUSINESS_FORMMAP, basename($path));
            if (file_exists($path) === false)
            {
                return ;
            }
        }

        // load formmap
        $formmaps = include($path);

        // parse formmap
        foreach ($formmaps as $namespace => $formmap)
        {
            $class_name = $formmap['class'];
            $elements = $formmap['elements'];

            // parse element
            foreach ($elements as $form_id => $element)
            {
                $form = null;
                switch ($element['form_type']) {
                    case CitrusFormmapElement::FORM_TYPE_TEXT : $form = new CitrusFormmapText(); break;
                }
            }
        }


        // initialize domdocument
        $dom = new DOMDocument();
        $dom->load(realpath($path));
        $xpath = new DOMXPath($dom);
        $formMapNodeList = $xpath->query("/formMap[@namespace='".$namespace."']/forms");
        $formMapNodeLength = $formMapNodeList->length;


        for($i = 0; $i < $formMapNodeLength; $i++)
        {
            $formElement    = $formMapNodeList->item($i);

            $form_id        = CitrusXmlUtility::getNamedItemValue($formElement->attributes, 'id');
            $form_class     = CitrusXmlUtility::getNamedItemValue($formElement->attributes, 'class');
            $form_prefix    = CitrusXmlUtility::getNamedItemValue($formElement->attributes, 'prefix');

            $this->classes[$namespace][$form_id] = $form_class;

            $formNodeList   = $formElement->childNodes;
            $formNodeLength = $formNodeList->length;

            for($j = 0; $j < $formNodeLength; $j++)
            {
                $item = $formNodeList->item($j);

                if ($item->nodeName == '#text'
                    || $item->nodeName == '#comment')
                {
                    continue;
                }

                $form = null;
                switch($item->tagName)
                {
                    case CitrusFormElement::FORM_TYPE_BUTTON    : $form = new CitrusFormButton();   break;
                    case CitrusFormElement::FORM_TYPE_CHECKBOX  : $form = new CitrusFormCheckbox(); break;
                    case CitrusFormElement::FORM_TYPE_FILE      : $form = new CitrusFormFile();     break;
                    case CitrusFormElement::FORM_TYPE_HIDDEN    : $form = new CitrusFormHidden();   break;
                    case CitrusFormElement::FORM_TYPE_PASSWORD  : $form = new CitrusFormPassword(); break;
//                    case CitrusFormElement::FORM_TYPE_RADIO     : $form = new CitrusForm();   break;
                    case CitrusFormElement::FORM_TYPE_SELECT    : $form = new CitrusFormSelect();   break;
                    case CitrusFormElement::FORM_TYPE_SUBMIT    : $form = new CitrusFormSubmit();   break;
                    case CitrusFormElement::FORM_TYPE_IMAGE     : $form = new CitrusFormImage();    break;
                    case CitrusFormElement::FORM_TYPE_RADIO     : $form = new CitrusFormRadio();    break;
                    case CitrusFormElement::FORM_TYPE_TEXT      : $form = new CitrusFormText();     break;
                    case CitrusFormElement::FORM_TYPE_TEXTAREA  : $form = new CitrusFormTextarea(); break;
                    default                                     : $form = new CitrusFormElement();
                }
                if ($item->hasAttribute('var_type')  === true) { $form->var_type     = $item->getAttribute('var_type');  }
                if ($item->hasAttribute('id')        === true) { $form->id           = $form_prefix.$item->getAttribute('id');        }
                if ($item->hasAttribute('name')      === true) { $form->name         = $item->getAttribute('name');      }
                if ($item->hasAttribute('style')     === true) { $form->style        = $item->getAttribute('style');     }
                if ($item->hasAttribute('class')     === true) { $form->class        = $item->getAttribute('class');     }
                if ($item->hasAttribute('value')     === true) { $form->value        = $item->getAttribute('value');     }
                if ($item->hasAttribute('max')       === true) { $form->max          = $item->getAttribute('max');       }
                if ($item->hasAttribute('min')       === true) { $form->min          = $item->getAttribute('min');       }
                if ($item->hasAttribute('filter')    === true) { $form->filter       = $item->getAttribute('filter');    }
                if ($item->hasAttribute('default')   === true) { $form->default      = $item->getAttribute('default');   }
                if ($item->hasAttribute('accesskey') === true) { $form->accesskey    = $item->getAttribute('accesskey'); }
                if ($item->hasAttribute('size')      === true) { $form->size         = $item->getAttribute('size');      }
                if ($item->hasAttribute('src')       === true) { $form->src          = $item->getAttribute('src');       }
                if ($item->hasAttribute('less')      === true) { $form->less         = $item->getAttribute('less');      }
                if ($item->hasAttribute('greater')   === true) { $form->greater      = $item->getAttribute('greater');   }
                if (empty($form_prefix)              === false){ $form->prefix       = $form_prefix;                     }
                if ($item->hasAttribute('wap_format')=== true) { $form->wap_format   = $item->getAttribute('wap_format');}
                if ($item->hasAttribute('empty')     === true) {
                    if (strtolower($item->getAttribute('empty')) == 'true') {
                        $form->options[''] = '';
                    } else {
                        $form->options['' . $item->getAttribute('empty')] = '';
                    }
                }
                if ($item->hasAttribute('options')   === true) {
                    $form_options = $item->getAttribute('options');
                    if ($form_options == 'empty') {
                        $form->options[''] = '';
                    } else {
                        $options = CitrusDefinition::call($form_options);
                        if (empty($options) === false) {
                            foreach ($options->definitionDetails as $one) {
                                $form->options[$one->definition_detail_cd] = $one->definition_detail_value;
                            }
                        } else {
                            $form->options = $form_options;
                        }
                    }
                }
                if ($item->hasAttribute('property')  === true) {
                    $property = $item->getAttribute('property');
                    if (empty($property) === false) {
                        // $form->property = $form_class.'.'.$item->getAttribute('property');
                        $form->property = $item->getAttribute('property');
                    } else {
                        $form->property = '';
                    }
                }
                if ($item->hasAttribute('required')  === true) {
                    if (strtolower($item->getAttribute('required')) == 'true') {
                        $form->required = true;
                    } else   {
                        $form->required = false;
                    }
                } else {
                    $form->required = false;
                }

                // 携帯用utf8処理で文字列max指定がある場合はmax値を3倍にする
                if ($form->var_type == 'string' && empty($form->max) === false && $form->filter == 'utf8')
                {
                    $form->max = ($form->max * 3);
                }

                $element_id = $form->id;
                $this->elements[$element_id]                    =  $form;
                $this->maps[$namespace][$form_id][$element_id]  =& $this->elements[$element_id];
            }
        }

        // CitrusCache Use possible
        if (self::$is_cache === true)
        {
            CitrusCache::set(CitrusConfigure::$APPLICATION_CD.':formMap:maps:'.$namespace, $this->maps[$namespace]);
            CitrusCache::set(CitrusConfigure::$APPLICATION_CD.':formMap:classes:'.$namespace, $this->classes[$namespace]);
        }
    }

//
//    /** @var string message tag */
//    const MESSAGE_TAG = 'formmap';
//
//    /** @var array(string::'form id' => CitrusFormElement) */
//    private $elements;
//
//    /** @var array(string::'namespace' => array(string::'form id' => CitrusFormElement)) map array */
//    private $maps;
//
//    /** @var array(string::'namespace' => array(string::'form id' => string::'class name')) map array */
//    private $classes;
//
//    /** @var bool validate null is require safe */
//    public $validate_null_safe = false;
//
//
//
//
//    /**
//     * construct
//     *
//     * @access  public
//     * @since   0.0.4.8 2012.03.19
//     * @version 0.0.4.8 2012.03.19
//     * @param   string  $path
//     */
//    public function __construct($path = null)
//    {
//        // formmap path
//        $this->load($path);
//    }
//
//    /**
//     * form xml definition loader
//     *
//     * @access  public
//     * @since   0.0.4.8 2012.03.19
//     * @version 0.0.4.8 2012.03.19
//     * @param   string  $path
//     * @param   string  $namaspace
//     */
//    public function load($path = null, $namespace = null)
//    {
//        // bad request
//        if ($path === null)
//        {
//            return ;
//        }
//
//        // CitrusCache Use possible
//        if (self::$is_cache === true)
//        {
//            $cache_maps_data        = CitrusCache::get(CitrusConfigure::$APPLICATION_CD.':formMap:maps:'.$namespace);
//            $cache_maps_result      = CitrusCache::getStatus();
//
//            $cache_classes_data     = CitrusCache::get(CitrusConfigure::$APPLICATION_CD.':formMap:classes:'.$namespace);
//            $cache_classes_result   = CitrusCache::getStatus();
//
//            if ($cache_maps_result    === CitrusCache::STATUS_SUCCESS
//                && $cache_classes_result === CitrusCache::STATUS_SUCCESS
//                && $cache_maps_data    !== null
//                && $cache_classes_data !== null)
//            {
//                foreach ($cache_maps_data as $cache_ns_name => $cache_ns_data)
//                {
//                    foreach ($cache_ns_data as $cache_form_id => $cache_form_data)
//                    {
//                        $this->elements[$cache_form_id]                         =  $cache_form_data;
//                        $this->maps[$namespace][$cache_ns_name][$cache_form_id] =& $this->elements[$cache_form_id];
//                    }
//                }
//
//                $this->classes[$namespace] = $cache_classes_data;
//                return ;
//            }
//        }
//
//        // parse xml formmap
//
//        // file exists xmls？
//        if (file_exists($path) === false)
//        {
//            if (file_exists($path = CitrusConfigure::$DIR_BUSINESS_FORM . basename($path)) === false)
//            {
//                return ;
//            }
//        }
//
//
//        // initialize domdocument
//        $dom = new DOMDocument();
//        $dom->load(realpath($path));
//        $xpath = new DOMXPath($dom);
//        $formMapNodeList = $xpath->query("/formMap[@namespace='".$namespace."']/forms");
//        $formMapNodeLength = $formMapNodeList->length;
//
//
//        for($i = 0; $i < $formMapNodeLength; $i++)
//        {
//            $formElement    = $formMapNodeList->item($i);
//
//            $form_id        = CitrusXmlUtility::getNamedItemValue($formElement->attributes, 'id');
//            $form_class     = CitrusXmlUtility::getNamedItemValue($formElement->attributes, 'class');
//            $form_prefix    = CitrusXmlUtility::getNamedItemValue($formElement->attributes, 'prefix');
//
//            $this->classes[$namespace][$form_id] = $form_class;
//
//            $formNodeList   = $formElement->childNodes;
//            $formNodeLength = $formNodeList->length;
//
//            for($j = 0; $j < $formNodeLength; $j++)
//            {
//                $item = $formNodeList->item($j);
//
//                if ($item->nodeName == '#text'
//                    || $item->nodeName == '#comment')
//                {
//                    continue;
//                }
//
//                $form = null;
//                switch($item->tagName)
//                {
//                    case CitrusFormElement::FORM_TYPE_BUTTON    : $form = new CitrusFormButton();   break;
//                    case CitrusFormElement::FORM_TYPE_CHECKBOX  : $form = new CitrusFormCheckbox(); break;
//                    case CitrusFormElement::FORM_TYPE_FILE      : $form = new CitrusFormFile();     break;
//                    case CitrusFormElement::FORM_TYPE_HIDDEN    : $form = new CitrusFormHidden();   break;
//                    case CitrusFormElement::FORM_TYPE_PASSWORD  : $form = new CitrusFormPassword(); break;
////                    case CitrusFormElement::FORM_TYPE_RADIO     : $form = new CitrusForm();   break;
//                    case CitrusFormElement::FORM_TYPE_SELECT    : $form = new CitrusFormSelect();   break;
//                    case CitrusFormElement::FORM_TYPE_SUBMIT    : $form = new CitrusFormSubmit();   break;
//                    case CitrusFormElement::FORM_TYPE_IMAGE     : $form = new CitrusFormImage();    break;
//                    case CitrusFormElement::FORM_TYPE_RADIO     : $form = new CitrusFormRadio();    break;
//                    case CitrusFormElement::FORM_TYPE_TEXT      : $form = new CitrusFormText();     break;
//                    case CitrusFormElement::FORM_TYPE_TEXTAREA  : $form = new CitrusFormTextarea(); break;
//                    default                                     : $form = new CitrusFormElement();
//                }
//                if ($item->hasAttribute('var_type')  === true) { $form->var_type     = $item->getAttribute('var_type');  }
//                if ($item->hasAttribute('id')        === true) { $form->id           = $form_prefix.$item->getAttribute('id');        }
//                if ($item->hasAttribute('name')      === true) { $form->name         = $item->getAttribute('name');      }
//                if ($item->hasAttribute('style')     === true) { $form->style        = $item->getAttribute('style');     }
//                if ($item->hasAttribute('class')     === true) { $form->class        = $item->getAttribute('class');     }
//                if ($item->hasAttribute('value')     === true) { $form->value        = $item->getAttribute('value');     }
//                if ($item->hasAttribute('max')       === true) { $form->max          = $item->getAttribute('max');       }
//                if ($item->hasAttribute('min')       === true) { $form->min          = $item->getAttribute('min');       }
//                if ($item->hasAttribute('filter')    === true) { $form->filter       = $item->getAttribute('filter');    }
//                if ($item->hasAttribute('default')   === true) { $form->default      = $item->getAttribute('default');   }
//                if ($item->hasAttribute('accesskey') === true) { $form->accesskey    = $item->getAttribute('accesskey'); }
//                if ($item->hasAttribute('size')      === true) { $form->size         = $item->getAttribute('size');      }
//                if ($item->hasAttribute('src')       === true) { $form->src          = $item->getAttribute('src');       }
//                if ($item->hasAttribute('less')      === true) { $form->less         = $item->getAttribute('less');      }
//                if ($item->hasAttribute('greater')   === true) { $form->greater      = $item->getAttribute('greater');   }
//                if (empty($form_prefix)              === false){ $form->prefix       = $form_prefix;                     }
//                if ($item->hasAttribute('wap_format')=== true) { $form->wap_format   = $item->getAttribute('wap_format');}
//                if ($item->hasAttribute('empty')     === true) {
//                    if (strtolower($item->getAttribute('empty')) == 'true') {
//                        $form->options[''] = '';
//                    } else {
//                        $form->options['' . $item->getAttribute('empty')] = '';
//                    }
//                }
//                if ($item->hasAttribute('options')   === true) {
//                    $form_options = $item->getAttribute('options');
//                    if ($form_options == 'empty') {
//                        $form->options[''] = '';
//                    } else {
//                        $options = CitrusDefinition::call($form_options);
//                        if (empty($options) === false) {
//                            foreach ($options->definitionDetails as $one) {
//                                $form->options[$one->definition_detail_cd] = $one->definition_detail_value;
//                            }
//                        } else {
//                            $form->options = $form_options;
//                        }
//                    }
//                }
//                if ($item->hasAttribute('property')  === true) {
//                    $property = $item->getAttribute('property');
//                    if (empty($property) === false) {
//                        // $form->property = $form_class.'.'.$item->getAttribute('property');
//                        $form->property = $item->getAttribute('property');
//                    } else {
//                        $form->property = '';
//                    }
//                }
//                if ($item->hasAttribute('required')  === true) {
//                    if (strtolower($item->getAttribute('required')) == 'true') {
//                        $form->required = true;
//                    } else   {
//                        $form->required = false;
//                    }
//                } else {
//                    $form->required = false;
//                }
//
//                // 携帯用utf8処理で文字列max指定がある場合はmax値を3倍にする
//                if ($form->var_type == 'string' && empty($form->max) === false && $form->filter == 'utf8')
//                {
//                    $form->max = ($form->max * 3);
//                }
//
//                $element_id = $form->id;
//                $this->elements[$element_id]                    =  $form;
//                $this->maps[$namespace][$form_id][$element_id]  =& $this->elements[$element_id];
//            }
//        }
//
//        // CitrusCache Use possible
//        if (self::$is_cache === true)
//        {
//            CitrusCache::set(CitrusConfigure::$APPLICATION_CD.':formMap:maps:'.$namespace, $this->maps[$namespace]);
//            CitrusCache::set(CitrusConfigure::$APPLICATION_CD.':formMap:classes:'.$namespace, $this->classes[$namespace]);
//        }
//    }
//
//    /**
//     * validate
//     *
//     * @access  public
//     * @since   0.0.4.8 2012.03.19
//     * @version 0.0.4.8 2012.03.19
//     * @param   string  $form_id
//     */
//    public function validate($form_id = null)
//    {
//        try
//        {
//            $list = [];
//            if (is_null($form_id) === true)
//            {
//                $list = $this->elements;
//            }
//            else
//            {
//                foreach ($this->maps as $ns_name => $ns_data)
//                {
//                    foreach ($ns_data as $data_id => $data)
//                    {
//                        if ($data_id == $form_id)
//                        {
//                            $list = $data;
//                            break 2;
//                        }
//                    }
//                }
//            }
//            $result = 0;
//            foreach ($list as $element)
//            {
//                $element->setValidateNullSafe($this->validate_null_safe);
//                // 比較チェック less than
//                if (empty($list[$element->prefix.$element->less]) === false && $element->validateLess($list[$element->prefix.$element->less]) === false)
//                {
//                    $result++;
//                }
//                // 比較チェック greater than
//                if (empty($list[$element->prefix.$element->greater]) === false && $element->validateGreater($list[$element->prefix.$element->greater]) === false)
//                {
//                    $result++;
//                }
//                $result += $element->validate();
//            }
//            return $result;
//        }
//        catch(CitrusServiceException $se)
//        {
//            throw $se;
//        }
//    }
//
//    /**
//     * form data binder
//     *
//     * @access  public
//     * @since   0.0.4.8 2012.03.19
//     * @version 0.0.4.8 2012.03.19
//     * @param   array   $array
//     */
//    public function bind(array $array = null)
//    {
//        $request_list  = CitrusSession::$request->properties()
//            + CitrusSession::$filedata->properties();
//
//        $json_request_list = json_decode(file_get_contents('php://input'), true);
//        if (is_null($json_request_list) == false)
//        {
//            $request_list += $json_request_list;
//        }
//
//        $prefix = (isset($request_list['prefix']) === true ? $request_list['prefix'] : '');
//
//        // $this->mapsには$this->elementsの参照から渡される。
//        foreach ($request_list as $ky => $vl)
//        {
//            // imageボタン対応
//            if (preg_match('/.*(_y|_x)$/i', $ky) > 0)
//            {
//                $ky = substr($ky, 0, -2);
//
//                if (isset($this->elements[$prefix.$ky]) === true)
//                {
//                    if (is_array($this->elements[$prefix.$ky]->value) === false)
//                    {
//                        $this->elements[$prefix.$ky]->value = [];
//                    }
//                    $this->elements[$prefix.$ky]->value[] = $vl;
//                }
//                else
//                {
//                    $this->elements[$prefix.$ky] = new CitrusFormElement(array('id' => $prefix.$ky, 'value' => array($vl)));
//                }
//            }
//            else
//            {
//                if (isset($this->elements[$prefix.$ky]) === true)
//                {
//                    $this->elements[$prefix.$ky]->value = $vl;
//                }
//                else
//                {
//                    $this->elements[$prefix.$ky] = new CitrusFormElement(array('id' => $prefix.$ky, 'value' => $vl));
//                }
//            }
//        }
//    }
//    /**
//     * form data binder
//     *
//     * @access  public
//     * @since   0.0.4.8 2012.03.19
//     * @version 0.0.4.8 2012.03.19
//     * @param   object   $object
//     */
//    public function bindObject($object = null, $prefix = '')
//    {
//        $request_list  = get_object_vars($object);
//
//        // $this->mapsには$this->elementsの参照から渡される。
//        foreach ($request_list as $ky => $vl)
//        {
//            if (isset($this->elements[$prefix.$ky]) === true)
//            {
//                $this->elements[$prefix.$ky]->value = $vl;
//            }
//            else
//            {
//                $this->elements[$prefix.$ky] = new CitrusFormElement(array('id' => $prefix.$ky, 'value' => $vl));
//            }
//        }
//    }
//
//    /**
//     * generation
//     *
//     * @access  public
//     * @since   0.0.4.8 2012.03.19
//     * @version 0.0.4.8 2012.03.19
//     * @param   string  $namespace
//     * @param   string  $form_id
//     * @return  CitrusObject
//     */
//    public function generate($namespace, $form_id)
//    {
//        $class_name = $this->classes[$namespace][$form_id];
//
//        $object = new $class_name();
//
//        $properties = $this->maps[$namespace][$form_id];
//
//        foreach ($properties as $ky => $vl)
//        {
//            $value = $vl->filter();
//            $object->setFromContext($vl->property, $value);
//        }
//
//        return $object;
//    }
//
//    public function __get($name)
//    {
//        return $this->elements[$name];
//    }
//
//    /**
//     * getter
//     *
//     * @access  public
//     * @since   0.0.4.8 2012.03.19
//     * @version 0.0.4.8 2012.03.19
//     * @param   string  $name
//     * @return  CitrusFormElement
//     */
//    public function get($name)
//    {
//        return $this->elements[$name];
//    }
//
//    /**
//     * setter
//     *
//     * @access  public
//     * @since   0.0.4.8 2012.03.19
//     * @version 0.0.4.8 2012.03.19
//     * @param   string  $name
//     * @param   string  $value
//     */
//    public function set($name, $object)
//    {
//        $this->elements[$name] = $object;
//    }
//
//    /**
//     * caller
//     *
//     * @access  public
//     * @since   0.0.4.8 2012.03.19
//     * @version 0.0.4.8 2012.03.19
//     * @param   string  $name
//     * @return  mixed
//     */
//    public function call($name)
//    {
//        return $this->elements[$name]->value;
//    }
}