<?php
/**
 * Element.class.php.
 * 2017/08/05
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Formmap
 * @license     http://www.citrus.tk/
 * @
 */

namespace Citrus\Formmap;


use Citrus\CitrusObject;

class CitrusFormmapElement extends CitrusObject
{
    /** @var string var type int */
    const VAR_TYPE_INT = 'int';

    /** @var string var type float */
    const VAR_TYPE_FLOAT = 'float';

    /** @var string var type numeric */
    const VAR_TYPE_NUMERIC = 'numeric';

    /** @var string var type string */
    const VAR_TYPE_STRING = 'string';

    /** @var string var type alphabet */
    const VAR_TYPE_ALPHABET = 'alphabet';

    /** @var string var type alphabet & numeric */
    const VAR_TYPE_ALPHANUMERIC = 'alphanumeric';

    /** @var string var type alphabet & numeric & marks */
    const VAR_TYPE_AN_MARKS = 'an_marks';

    /** @var string var type date */
    const VAR_TYPE_DATE = 'date';

    /** @var string var type time */
    const VAR_TYPE_TIME = 'time';

    /** @var string var type datetime */
    const VAR_TYPE_DATETIME = 'dateime';

    /** @var string var type bool */
    const VAR_TYPE_BOOL = 'bool';

    /** @var string var type file */
    const VAR_TYPE_FILE = 'file';

    /** @var string var type telephone */
    const VAR_TYPE_TELEPHONE = 'telephone';

    /** @var string var type fax */
    const VAR_TYPE_FAX = 'fax';

    /** @var string var type year */
    const VAR_TYPE_YEAR = 'year';

    /** @var string var type month */
    const VAR_TYPE_MONTH = 'month';

    /** @var string var type day */
    const VAR_TYPE_DAY = 'day';

    /** @var string var type email */
    const VAR_TYPE_EMAIL = 'email';

    /** @var string var type text */
    const VAR_TYPE_TEXT = 'text';

    /** @var string var type password */
    const VAR_TYPE_PASSWD = 'password';

    /** @var string var type textarea */
    const VAR_TYPE_TEXTAREA = 'textarea';

    /** @var string var type select */
    const VAR_TYPE_SELECT = 'select';

    /** @var string var type radio */
    const VAR_TYPE_RADIO = 'radio';

    /** @var string var type checkbox */
    const VAR_TYPE_CHECKBOX = 'checkbox';

    /** @var string var type button */
    const VAR_TYPE_BUTTON = 'button';

    /** @var string var type submit */
    const VAR_TYPE_SUBMIT = 'submit';

    /** @var string var type image */
    const VAR_TYPE_IMAGE = 'image';

    /** @var string var type hidden */
    const VAR_TYPE_HIDDEN = 'hidden';

    /** @var string var type element */
    const VAR_TYPE_ELEMENT = 'element';


    /** @var string form type text */
    const FORM_TYPE_TEXT = 'text';

    /** @var string form type password */
    const FORM_TYPE_PASSWD = 'password';

    /** @var string form type submit */
    const FORM_TYPE_SUBMIT = 'submit';


    /** @var string form id */
    public $id;

    /** @var string id prefix */
    public $prefix = '';

    /** @var string form form type */
    public $form_type;

    /** @var string form variable type */
    public $var_type;

    /** @var string form name */
    public $name = '';

    /** @var string form class */
    public $class;

    /** @var bool form input required */
    public $required = false;

    /** @var string[] options */
    public $options = [];

    /** @var string[] style */
    public $style = [];

    /** @var mixed form value */
    public $value;

    /** @var int validate max */
    public $max;

    /** @var int validate min */
    public $min;

    /** @var int property key */
    public $property;

    /** @var string[] filters  */
    public $filters = [];

    /** @var bool html escape */
    public $escape = true;

    /** @var mixed default value */
    public $default;

    /** @var string accesskey */
    public $accesskey;

    /** @var int size */
    public $size;

    /** @var string src */
    public $src;

    /** @var int length, size or value lesser */
    public $lesser;

    /** @var int length, size or value gteater */
    public $greater;

    /** @var bool validate null safe */
    public $validate_null_safe = false;


    /**
     * constructor.
     *
     * @param array|null $element フォーム情報
     */
    public function __construct(array $element = null)
    {
        $this->bindObject($element, true);
    }


    /**
     * generate html tag
     *
     * @param string     $tag
     * @param array|null $elements
     * @return string
     */
    public static function generateTag(string $tag, array $elements = null) : string
    {
        $form_element = [];
        foreach ($elements as $ky => $vl)
        {
            if (is_null($vl) === true)
            {
                continue;
            }
            $form_element[] = sprintf('%s="%s"', $ky, $vl);
        }

        return sprintf('<%s %s />',
            $tag,
            implode(' ', $form_element)
            );
    }



    /**
     * call value
     *
     * @return mixed|string
     */
    public function callValue()
    {
        if($this->escape === true)
        {
            return htmlspecialchars($this->value, ENT_QUOTES);
        }
        return $this->value;
    }



    /**
     * call default value
     *
     * @return mixed|string
     */
    public function callDefault()
    {
        if($this->escape === true)
        {
            return htmlspecialchars($this->default, ENT_QUOTES);
        }
        return $this->default;
    }

//
//    /**
//     * to string accesser
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  string
//     */
//    public function __toString()
//    {
//        return $this->toString();
//    }
//
//    /**
//     * to string accesser with option
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @param   string  $option
//     * @return  string
//     */
//    public function option($option = '')
//    {
//        return $this->toString($option);
//    }
//
//
//
//    /**
//     * to string accesser with option
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  string
//     */
//    public function toString($option = '')
//    {
//        return '';
//    }
//    /**
//     * span tag
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @param   string  $option
//     * @return  string
//     */
//    public function span($option = '')
//    {
//        return "<span type='text' name='".$this->id."'"
//            .(($this->id    === null)    ? "" : " id='".$this->id."'")
//            .(($this->class === null) ? "" : " class='".$this->class."'")
//            .(($this->style === null) ? "" : " style='".$this->style."'")
//            ." ".$option." >"
//            .(($this->value === null) ? $this->callDefault() : $this->callValue())
//            ."</span>";
//    }
//
//    /**
//     * validate
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  integer
//     */
//    public function validate()
//    {
//        try
//        {
//            $result = 0;
//            //validate require
//            if($this->_validateRequired() === false)
//            {
//                $result++;
//            }
//            else
//            {
//                // validate type
//                if($this->_validateVarType() === false)
//                {
//                    $result++;
//                }
//
//                // validate max
//                if($this->_validateMax() === false)
//                {
//                    $result++;
//                }
//
//                // validate min
//                if($this->_validateMin() === false)
//                {
//                    $result++;
//                }
//            }
//            return $result;
//        }
//        catch(CitrusErrorException $ee)
//        {
//            throw $ee;
//        }
//    }
//
//    /**
//     * filter
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  integer
//     */
//    public function filter()
//    {
//        // result value
//        $result = $this->value;
//
//        // empty case
//        if(is_numeric($result) === true)
//        {
//        }
//        else
//            if(isset($result) === false)
//            {
//                return null;
//            }
//
//        // non filter
//        if(is_null($this->filter) === true)
//        {
//            return $result;
//        }
//
//        // filter list
//        $filter_list = explode(',', $this->filter);
//
//        foreach($filter_list as $filter_name)
//        {
//            $filter_method_name = '_filter'.ucfirst(trim($filter_name));
//            $result = $this->$filter_method_name($result);
//        }
//
//        return $result;
//    }
//
//    /**
//     * validate value required
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     */
//    protected function _validateRequired()
//    {
//        try
//        {
//            if($this->required === true)
//            {
//                if(is_numeric($this->value) === true)
//                {
//
//                }
//                else
//                    if(empty($this->value) === true)
//                    {
//                        if($this->validate_null_safe === true && is_null($this->value) === true)
//                        {
//
//                        }
//                        else
//                        {
//                            CitrusMessage::registError(CitrusLocale::message('form_validate_required', array($this->name)), '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    }
//                    else
//                        if(is_array($this->value) === true)
//                        {
//                            if(count($this->value) == 0)
//                            {
//                                if($this->validate_null_safe === true && is_null($this->value) === true)
//                                {
//
//                                }
//                                else
//                                {
//                                    CitrusMessage::registError(CitrusLocale::message('form_validate_required', array($this->name)), '', CitrusFormmap::MESSAGE_TAG);
//                                    return false;
//                                }
//                            }
//                        }
//            }
//            return true;
//        }
//        catch(CitrusErrorException $ee)
//        {
//            throw $ee;
//        }
//    }
//
//    /**
//     * validate value type
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  boolean
//     */
//    protected function _validateVarType()
//    {
//        try
//        {
//            // 入力がある場合のみチェックする。
//            if(is_null($this->value) || $this->value == '')
//            {
//                return true;
//            }
//
//            // filter
//            $value = $this->filter();
//
//            // validate
//            switch($this->var_type)
//            {
//
//                // int
//                case self::VAR_TYPE_INT :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(is_int(intval($vl)) === false || is_numeric($vl) === false || !preg_match('/^-?[0-9]*$/', $vl))
//                            {
//                                CitrusMessage::registError(CitrusLocale::message('form_validate_type_int', array($this->name)), '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                            else
//                                if($vl >= 2147483648)
//                                {
//                                    CitrusMessage::registError(CitrusLocale::message('form_validate_numeric_max', array($this->name, 2147483648)), '', CitrusFormmap::MESSAGE_TAG);
//                                    return false;
//                                }
//                                else
//                                    if($vl <= -2147483648)
//                                    {
//                                        CitrusMessage::registError(CitrusLocale::message('form_validate_numeric_min', array($this->name, -2147483648)), '', CitrusFormmap::MESSAGE_TAG);
//                                        return false;
//                                    }
//                        }
//                    }
//                    else
//                        if(is_int(intval($value)) === false || is_numeric($value) === false || !preg_match('/^-?[0-9]*$/', $value))
//                        {
//                            CitrusMessage::registError(CitrusLocale::message('form_validate_type_int', array($this->name)), '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                        else
//                            if($value >= 2147483648)
//                            {
//                                CitrusMessage::registError(CitrusLocale::message('form_validate_numeric_max', array($this->name, 2147483648)), '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                            else
//                                if($value <= -2147483648)
//                                {
//                                    CitrusMessage::registError(CitrusLocale::message('form_validate_numeric_min', array($this->name, -2147483648)), '', CitrusFormmap::MESSAGE_TAG);
//                                    return false;
//                                }
//                    break;
//
//                // float
//                case self::VAR_TYPE_FLOAT :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(is_float(floatval($vl)) === false)
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には少数を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(is_float(floatval($value)) === false)
//                        {
//                            CitrusMessage::registError('「'.$this->name.'」には少数を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // numeric
//                case self::VAR_TYPE_NUMERIC :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(is_numeric($vl) === false)
//                            {
//                                CitrusMessage::registError(CitrusLocale::message('form_validate_type_numeric', array($this->name)), '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(is_numeric($value) === false)
//                        {
//                            CitrusMessage::registError(CitrusLocale::message('form_validate_type_numeric', array($this->name)), '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // string
//                case self::VAR_TYPE_STRING :
//                    // if(is_array($value))
//                    // {
//                    // foreach($value as $ky => $vl)
//                    // {
//                    // if(is_string($vl) === false)
//                    // {
//                    // CitrusMessage::registError('「'.$this->name.'」には文字列を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                    // return false;
//                    // }
//                    // }
//                    // }
//                    // else
//                    // if(is_string($value) === false)
//                    // {
//                    // CitrusMessage::registError('「'.$this->name.'」には文字列を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                    // return false;
//                    // }
//                    break;
//
//                // alphabet
//                case self::VAR_TYPE_ALPHABET :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(!preg_match('/^[a-zA-Z]/', $vl))
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には半角英字を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(!preg_match('/^[a-zA-Z]/', $this->value))
//                        {
//                            CitrusMessage::registError('「'.$this->name.'」には半角英字を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // alphabet & numeric
//                case self::VAR_TYPE_ALPHANUMERIC :
//                    if(is_array($this->value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(!preg_match('/^[a-zA-Z0-9_.]/', $vl))
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には半角英数字を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(!preg_match('/^[a-zA-Z0-9_.]/', $value))
//                        {
//                            CitrusMessage::registError('「'.$this->name.'」には半角英数字を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // alphabet & numeric & marks
//                case self::VAR_TYPE_AN_MARKS :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(!preg_match('/^[a-zA-Z0-9_.%&#-]/', $vl))
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には半角英数字および記号を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(!preg_match('/^[a-zA-Z0-9_.%&#-]/', $value))
//                        {
//                            CitrusMessage::registError('「'.$this->name.'」には半角英数字および記号を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // date
//                case self::VAR_TYPE_DATE :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            $timestamp = strtotime($vl);
//                            if($timestamp === false)
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には年月日を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//
//                            $year   = date('Y', $timestamp);
//                            $month  = date('n', $timestamp);
//                            $day    = date('j', $timestamp);
//                            if(checkdate($month, $day, $year) === false)
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には年月日を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                            if(!preg_match('/^[0-9]{4}(-|\/)?[0-9]{2}(-|\/)?[0-9]{2}$/', $vl))
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には年月日を「yyyy-mm-dd」「yyyy/mm/dd」「yyyymmdd」のいずれかの形式で入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(strtotime($value) === false)
//                        {
//                            $timestamp = strtotime($value);
//                            if($timestamp === false)
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には年月日を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//
//                            $year   = date('Y', $timestamp);
//                            $month  = date('n', $timestamp);
//                            $day    = date('j', $timestamp);
//                            if(checkdate($month, $day, $year) === false)
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には年月日を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    if(!preg_match('/^[0-9]{4}(-|\/)?[0-9]{2}(-|\/)?[0-9]{2}$/', $value))
//                    {
//                        CitrusMessage::registError('「'.$this->name.'」には年月日を「yyyy-mm-dd」「yyyy/mm/dd」「yyyymmdd」のいずれかの形式で入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                        return false;
//                    }
//                    break;
//
//                // time
//                case self::VAR_TYPE_TIME :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(!preg_match('/^[0-9]{2}[:.]?[0-5][0-9][:.]?([0-5][0-9])?/', $vl))
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には時分秒または時分を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(!preg_match('/^[0-9]{2}[:.]?[0-5][0-9][:.]?([0-5][0-9])?/', $value))
//                        {
//                            CitrusMessage::registError('「'.$this->name.'」には時分秒または時分を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // datetime
//                case self::VAR_TYPE_DATETIME :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(strtotime($vl) === false)
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には年月日時分秒を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(strtotime($value) === false)
//                        {
//                            CitrusMessage::registError('「'.$this->name.'」には年月日時分秒を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // tel
//                case self::VAR_TYPE_TEL :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(!preg_match('/^([0-9]{2,3}-){0,1}[0-9]{1,4}-[0-9]{2,4}-[0-9]{2,4}$/', $vl))
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」には電話番号を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(!preg_match('/^([0-9]{2,3}-){0,1}[0-9]{1,4}-[0-9]{2,4}-[0-9]{2,4}$/', $value))
//                        {
//                            CitrusMessage::registError('「'.$this->name.'」には電話番号を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // fax
//                case self::VAR_TYPE_FAX :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
//                            if(!preg_match('/^([0-9]{2,3}-){0,1}[0-9]{1,4}-[0-9]{2,4}-[0-9]{2,4}$/', $vl))
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」にはFAX番号を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
//                        if(!preg_match('/^([0-9]{2,3}-){0,1}[0-9]{1,4}-[0-9]{2,4}-[0-9]{2,4}$/', $value))
//                        {
//                            CitrusMessage::registError('「'.$this->name.'」にはFAX番号を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // email
//                case self::VAR_TYPE_EMAIL :
//                    if(is_array($value))
//                    {
//                        foreach($value as $ky => $vl)
//                        {
/*                            if(!preg_match('/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?> :-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})$/i', $vl))*/
//                            {
//                                CitrusMessage::registError('「'.$this->name.'」にはメールアドレスを入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                                return false;
//                            }
//                        }
//                    }
//                    else
/*                        if(!preg_match('/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?> :-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})$/i', $value))*/
//                        {
//                            CitrusMessage::registError('「'.$this->name.'」にはメールアドレスを入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//                            return false;
//                        }
//                    break;
//
//                // other
//                default :
//                    return true;
//                    break;
//            }
//        }
//        catch(CitrusErrorException $ee)
//        {
//            throw $ee;
//        }
//    }
//
//    /**
//     * validate max
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     */
//    protected function _validateMax()
//    {
//        try
//        {
//            // 入力がある場合のみチェックする。
//            if(is_null($this->value) === true || $this->value == '')
//            {
//                return true;
//            }
//            if(is_null($this->max) === false)
//            {
//                // numeric
//                if($this->var_type == self::VAR_TYPE_INT
//                    || $this->var_type == self::VAR_TYPE_FLOAT)
//                {
//                    return $this->_validateNumericMax();
//                }
//                else
//
//                    // string
//                    if($this->var_type == self::VAR_TYPE_STRING)
//                    {
//                        return $this->_validateLengthMax();
//                    }
//            }
//        }
//        catch(CitrusErrorException $ee)
//        {
//            throw $ee;
//        }
//    }
//
//    /**
//     * validate min
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  boolean
//     */
//    protected function _validateMin()
//    {
//        try
//        {
//            // 入力がある場合のみチェックする。
//            if(is_null($this->value) === true || $this->value == '')
//            {
//                return true;
//            }
//            if(is_null($this->min) === false)
//            {
//                // numeric
//                if($this->var_type == self::VAR_TYPE_INT
//                    || $this->var_type == self::VAR_TYPE_FLOAT)
//                {
//                    return $this->_validateNumericMin();
//                }
//                else
//
//                    // string
//                    if($this->var_type == self::VAR_TYPE_STRING)
//                    {
//                        return $this->_validateLengthMin();
//                    }
//            }
//        }
//        catch(CitrusErrorException $ee)
//        {
//            throw $ee;
//        }
//    }
//
//    /**
//     * validate max numeric
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  boolean
//     */
//    protected function _validateNumericMax()
//    {
//        if(!($this->value <= $this->max))
//        {
//            CitrusMessage::registError(CitrusLocale::message('form_validate_numeric_max', array($this->name, $this->max)), '', CitrusFormmap::MESSAGE_TAG);
//            return false;
//        }
//        return true;
//    }
//
//    /**
//     * validate min numeric
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  boolean
//     */
//    protected function _validateNumericMin()
//    {
//        if(!($this->value >= $this->min))
//        {
//            CitrusMessage::registError('「'.$this->name.'」には「'.$this->min.'」以上の値を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
////            throw new CitrusErrorException('「'.$this->name.'」には「'.$this->min.'」以上の値を入力してください。');
//            return false;
//        }
//        return true;
//    }
//
//    /**
//     * validate max length
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  boolean
//     */
//    protected function _validateLengthMax()
//    {
//        $length = mb_strwidth($this->value, 'UTF-8');
//        if(!($length <= $this->max))
//        {
//            CitrusMessage::registError(CitrusLocale::message('form_validate_length_max', array($this->name, $this->max)), '', CitrusFormmap::MESSAGE_TAG);
//            return false;
//        }
//        return true;
//    }
//
//    /**
//     * validate min length
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @return  boolean
//     */
//    protected function _validateLengthMin()
//    {
//        $length = mb_strwidth($this->value, 'UTF-8');
//        if(!($length >= $this->min))
//        {
//            CitrusMessage::registError('「'.$this->name.'」には「'.$this->min.'」文字以上で入力してください。', '', CitrusFormmap::MESSAGE_TAG);
////            throw new CitrusErrorException('「'.$this->name.'」には「'.$this->min.'」文字以上で入力してください。');
//            return false;
//        }
//        return true;
//    }
//
//    /**
//     * validate less (this <= less)
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     */
//    public function validateLess($element = null)
//    {
//        // 入力がある場合のみチェックする。
//        if(is_null($this->value) === true || $this->value == '')
//        {
//            return true;
//        }
//        if(is_null($element) === false)
//        {
//            // 日付の場合
//            if(($this->var_type == self::VAR_TYPE_DATE && $element->var_type == self::VAR_TYPE_DATE))
//            {
//                if(strtotime($this->value) > strtotime($element->value))
//                {
//                    CitrusMessage::registError('[ '.$this->name.' ] は ['.$element->name.' ] 以前の日付を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//
//                    return false;
//                }
//            }
//            // 数値系の場合
//            if((
//                ($this->var_type == self::VAR_TYPE_INT
//                    || $this->var_type == self::VAR_TYPE_FLOAT
//                    || $this->var_type == self::VAR_TYPE_NUMERIC
//                )
//                &&
//                ($element->var_type == self::VAR_TYPE_DATE
//                    || $element->var_type == self::VAR_TYPE_FLOAT
//                    || $element->var_type == self::VAR_TYPE_NUMERIC
//                )
//            ))
//            {
//                if(floatVal($this->value) > floatVal($element->value))
//                {
//                    CitrusMessage::registError('[ '.$this->name.' ] は ['.$element->name.' ] 以下の値を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//
//                    return false;
//                }
//            }
//        }
//        return true;
//    }
//
//    /**
//     * validate greater (this >= greater)
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     */
//    public function validateGreater($element = null)
//    {
//        // 入力がある場合のみチェックする。
//        if(is_null($this->value) === true || $this->value == '')
//        {
//            return true;
//        }
//        if(is_null($element) === false)
//        {
//            // 日付の場合
//            if(($this->var_type == self::VAR_TYPE_DATE && $element->var_type == self::VAR_TYPE_DATE))
//            {
//                if(strtotime($this->value) < strtotime($element->value))
//                {
//                    CitrusMessage::registError('[ '.$this->name.' ] は ['.$element->name.' ] 以降の日付を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//
//                    return false;
//                }
//            }
//            // 数値系の場合
//            if((
//                ($this->var_type == self::VAR_TYPE_INT
//                    || $this->var_type == self::VAR_TYPE_FLOAT
//                    || $this->var_type == self::VAR_TYPE_NUMERIC
//                )
//                &&
//                ($element->var_type == self::VAR_TYPE_DATE
//                    || $element->var_type == self::VAR_TYPE_FLOAT
//                    || $element->var_type == self::VAR_TYPE_NUMERIC
//                )
//            ))
//            {
//                if(floatVal($this->value) < floatVal($element->value))
//                {
//                    CitrusMessage::registError('[ '.$this->name.' ] は ['.$element->name.' ] 以上の値を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//
//                    return false;
//                }
//            }
//        }
//        return true;
//    }
//
//    /**
//     * filter date
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @param   string  $value
//     * @return  string
//     */
//    protected function _filterDate($value)
//    {
//        return date('Y-m-d', strtotime($value));
//    }
//
//    /**
//     * filter md5 encrypt
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @param   string  $value
//     * @return  string
//     */
//    protected function _filterMd5($value)
//    {
//        if(empty($value) === false)
//        {
//            return md5($value);
//        }
//        return $value;
//    }
//
//    /**
//     * filter UTF8 encode
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @param   string  $value
//     * @return  string
//     */
//    protected function _filterUtf8($value)
//    {
//        if(empty($value) === false)
//        {
//            $value = mb_convert_encoding(urldecode($value), 'UTF-8', mb_detect_encoding(urldecode($value), 'UTF-8, SJIS-win, eucJP-win, EUC-JP, SJIS, ASCII, JIS'));
//        }
//        return $value;
//    }
//
//    /**
//     * filter to float encode
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @param   string  $value
//     * @return  string
//     */
//    protected function _filterToFloat($value)
//    {
//        $value = str_replace(',', '', $value);
//        $value = floatVal($value);
//        return $value;
//    }
//
//    /**
//     * set validate_null_safe
//     *
//     * @access  public
//     * @since   0.0.5.1 2012.03.19
//     * @version 0.0.5.1 2012.03.19
//     * @param   boolean $validate_null_safe
//     */
//    public function setValidateNullSafe($validate_null_safe)
//    {
//        $this->validate_null_safe = $validate_null_safe;
//    }
}