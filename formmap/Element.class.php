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
 */

namespace Citrus\Formmap;


use Citrus\CitrusException;
use Citrus\CitrusFormmap;
use Citrus\CitrusLogger;
use Citrus\CitrusMessage;
use Citrus\CitrusNVL;
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

    /** @var string var type tel */
    const VAR_TYPE_TEL = 'tel';

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


    /** @var string form type element */
    const FORM_TYPE_ELEMENT = 'element';

    /** @var string form type text */
    const FORM_TYPE_TEXT = 'text';

    /** @var string form type hidden */
    const FORM_TYPE_HIDDEN = 'hidden';

    /** @var string form type select */
    const FORM_TYPE_SELECT = 'select';

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
//        var_dump(debug_backtrace());
//        debug_print_backtrace();
//        var_dump($element);

//        $this->bindObject($element, true);
        $this->bindArray($element, true);
    }



    /**
     * generate html tag
     *
     * @param string     $tag
     * @param array|null $elements
     * @param mixed|null $options
     * @return string
     */
    public static function generateTag(string $tag, array $elements = null, $options = null) : string
    {
        // 閉じタグがあるタイプか否か
        $is_multiple_tag = in_array($tag, [ 'select' ]);

        $form_element = [];
        foreach ($elements as $ky => $vl)
        {
            // 基本初期値はnullだが、例外的に空配列[]を利用している為
            // 0も通したい
            if (is_null($vl) === true
                || (is_string($vl) === true && $vl === '')
                || (is_array($vl) === true && $vl === []))
            {
                continue;
            }

            if (is_array($vl) === true)
            {
                $form_element[$ky] = sprintf('%s="%s"', $ky, implode(', ', $vl));
            }
            else
            {
                $form_element[$ky] = sprintf('%s="%s"', $ky, $vl);
            }
        }
//CitrusLogger::debug($form_element);
        // 閉じタグがあるタイプ
        if ($is_multiple_tag === true)
        {
            $inner_tags = [];
            if (is_array($options) === true)
            {
                // select
                if ($tag == 'select')
                {
                    foreach ($options as $ky => $vl)
                    {
                        $inner_tags[] = sprintf('<option value="%s" %s>%s</option>',
                            $ky,
                            ($ky === $elements['value'] ? 'selected' : ''),
                            $vl
                            );
                    }
                }
            }
            // その他のタグ
            else if (is_string($options) === true)
            {
                $inner_tags[] = $options;
            }

            // valueは渡されてきてもいらなくなる
            unset($form_element['value']);

            return sprintf('<%s %s>%s</%s>',
                $tag,
                implode(' ', $form_element),
                implode(PHP_EOL, $inner_tags),
                $tag
            );
        }
        // 閉じタグがないタイプ
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
        if ($this->escape === true)
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
        if ($this->escape === true)
        {
            return htmlspecialchars($this->default, ENT_QUOTES);
        }
        return $this->default;
    }



    /**
     * call prefixed id
     *
     * @return string
     */
    public function callPrefixedId()
    {
        if (empty($this->prefix) === true)
        {
            return $this->id;
        }
        return sprintf('%s%s', $this->prefix, $this->id);
    }



    /**
     * to string accesser
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }



    /**
     * to string accesser with option
     *
     * @param array $option
     * @return string
     */
    public function option(array $option = []) : string
    {
        return $this->toString($option);
    }
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


    /**
     * to span tag
     *
     * @return string
     */
    public function span()
    {
        $elements = [
//            'type'      => 'text',
            'id'        => $this->callPrefixedId(),
            'name'      => $this->id,
            'class'     => $this->class,
            'style'     => $this->style,
        ];
//        $elements = array_merge($elements, $appends);

        return self::generateTag('span', $elements, CitrusNVL::coalesceNull($this->value, $this->callValue(), $this->callDefault()));
    }



    /**
     * validate
     *
     * @return int
     * @throws CitrusException
     */
    public function validate() : int
    {
        try
        {
            $result = 0;
            // validate require
            if ($this->_validateRequired() === false)
            {
                $result++;
            }
            else
            {
                // validate type
                if ($this->_validateVarType() === false)
                {
                    $result++;
                }

                // validate max
                if ($this->_validateMax() === false)
                {
                    $result++;
                }

                // validate min
                if ($this->_validateMin() === false)
                {
                    $result++;
                }
            }
            return $result;
        }
        catch (CitrusException $e)
        {
            throw $e;
        }
    }



    /**
     * value filter
     *
     * @return mixed|null
     */
    public function filter()
    {
        // result value
        $result = $this->value;

//        // empty case
//        if (empty($result) === true)
//        {
//            return null;
//        }

        // non filter
        if (is_null($this->filters) === true)
        {
            return $result;
        }

        // filter list
        foreach ($this->filters as $one)
        {
            $filter_method_name = 'filter'.ucfirst(trim($one));
            $result = $this->$filter_method_name($result);
        }

        return $result;
    }



    /**
     * validate value required
     *
     * @return bool
     * @throws CitrusException
     */
    protected function _validateRequired() : bool
    {
        try
        {
            if ($this->required === true)
            {
                $message = sprintf('「%s」は入力必須です。', $this->name);
                if (is_numeric($this->value) === true)
                {
                    return true;
                }
                else if (empty($this->value) === true)
                {
                    if ($this->validate_null_safe === true && is_null($this->value) === true)
                    {}
                    else
                    {
                        $this->addError($message);
                        return false;
                    }
                }
            }
            return true;
        }
        catch (CitrusException $e)
        {
            throw $e;
        }
    }



    /**
     * validate value type
     *
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarType() : bool
    {
        try
        {
            // 入力がある場合のみチェックする。
            if (is_null($this->value) === true || $this->value === '')
            {
                return true;
            }

            // result
            $result = true;

            // message
            $message_form_validate_type_int = sprintf('「%s」には整数を入力してください。', $this->name);
            $message_form_validate_type_float = sprintf('「%s」には少数を入力してください。', $this->name);
            $message_form_validate_type_numeric = sprintf('「%s」には数字を入力してください。', $this->name);
            $message_form_validate_numeric_max = sprintf('「%s」には「%s」以下の値を入力してください。', $this->name, PHP_INT_MAX);
            $message_form_validate_numeric_min = sprintf('「%s」には「%s」以上の値を入力してください。', $this->name, PHP_INT_MIN);
            $message_form_validate_length_max = sprintf('「%s」には「%s」文字以下で入力してください。', $this->name, 0);
            $message_form_validate_length_min = sprintf('「%s」には「%s」文字以上で入力してください。', $this->name, 0);

            $message_form_validate_type_alphabet = sprintf('「%s」には半角英字を入力してください。', $this->name);
            $message_form_validate_type_alphanumeric = sprintf('「%s」には半角英数字を入力してください。', $this->name);
            $message_form_validate_type_an_marks = sprintf('「%s」には半角英数字および記号を入力してください。', $this->name);
            $message_form_validate_type_date = sprintf('「%s」には年月日を「yyyy-mm-dd」「yyyy/mm/dd」「yyyymmdd」のいずれかの形式で入力してください。', $this->name);
            $message_form_validate_type_time = sprintf('「%s」には時分秒または時分を入力してください。', $this->name);
            $message_form_validate_type_datetime = sprintf('「%s」には年月日時分秒を入力してください。', $this->name);
            $message_form_validate_type_tel = sprintf('「%s」には電話番号を入力してください。', $this->name);
            $message_form_validate_type_fax = sprintf('「%s」にはFAX番号を入力してください。', $this->name);
            $message_form_validate_type_email = sprintf('「%s」にはメールアドレスを入力してください。', $this->name);




            
            // filter
            $filtered_value = $this->filter();

            // validate
            switch($this->var_type)
            {
                // int
                case self::VAR_TYPE_INT :
                    $result = $this->_validateVarTypeInt($filtered_value);
                    break;

                // float
                case self::VAR_TYPE_FLOAT :
                    $result = $this->_validateVarTypeFloat($filtered_value);
                    break;

                // numeric
                case self::VAR_TYPE_NUMERIC :
                    $result = $this->_validateVarTypeNumeric($filtered_value);
                    break;

                // string
                case self::VAR_TYPE_STRING :
                    break;

                // alphabet
                case self::VAR_TYPE_ALPHABET :
                    $result = $this->_validateVarTypeAlphabet($filtered_value);
                    break;

                // alphabet & numeric
                case self::VAR_TYPE_ALPHANUMERIC :
                    $result = $this->_validateVarTypeAlphanumeric($filtered_value);
                    break;

                // alphabet & numeric & marks
                case self::VAR_TYPE_AN_MARKS :
                    $result = $this->_validateVarTypeANMarks($filtered_value);
                    break;

                // date
                case self::VAR_TYPE_DATE :
                    $result = $this->_validateVarTypeDate($filtered_value);
                    break;

                // time
                case self::VAR_TYPE_TIME :
                    $result = $this->_validateVarTypeTime($filtered_value);
                    break;

                // datetime
                case self::VAR_TYPE_DATETIME :
                    $result = $this->_validateVarTypeDatetime($filtered_value);
                    break;

                // tel
                case self::VAR_TYPE_TEL :
                    $result = $this->_validateVarTypeTel($filtered_value);
                    break;

                // fax
                case self::VAR_TYPE_FAX :
                    $result = $this->_validateVarTypeFax($filtered_value);
                    break;

                // email
                case self::VAR_TYPE_EMAIL :
                    $result = $this->_validateVarTypeEmail($filtered_value);
                    break;

                // other
                default :
                    $result = true;
                    break;
            }
            return $result;
        }
        catch (CitrusException $e)
        {
            throw $e;
        }
    }



    /**
     * validate value type int
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeInt($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_int = sprintf('「%s」には整数を入力してください。', $this->name);
        $message_form_validate_numeric_max = sprintf('「%s」には「%s」以下の値を入力してください。', $this->name, PHP_INT_MAX);
        $message_form_validate_numeric_min = sprintf('「%s」には「%s」以上の値を入力してください。', $this->name, PHP_INT_MIN);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeInt($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (is_int(intval($filtered_value)) === false && is_numeric($filtered_value) === false && !preg_match('/^-?[0-9]*$/', $filtered_value))
        {
            $this->addError($message_form_validate_type_int);
            $result = false;
        }
        else if ($filtered_value >= PHP_INT_MAX)
        {
            $this->addError($message_form_validate_numeric_max);
            $result = false;
        }
        else if ($filtered_value <= PHP_INT_MIN)
        {
            $this->addError($message_form_validate_numeric_min);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type float
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeFloat($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_float = sprintf('「%s」には少数を入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeFloat($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (is_float(floatval($filtered_value)) === false)
        {
            $this->addError($message_form_validate_type_float);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type numeric
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeNumeric($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_numeric = sprintf('「%s」には数字を入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeFloat($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (is_numeric($filtered_value) === false)
        {
            $this->addError($message_form_validate_type_numeric);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type alphabet
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeAlphabet($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_alphabet = sprintf('「%s」には半角英字を入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeAlphabet($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (!preg_match('/^[a-zA-Z]/', $filtered_value))
        {
            $this->addError($message_form_validate_type_alphabet);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type alphanumeric
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeAlphanumeric($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_alphanumeric = sprintf('「%s」には半角英数字を入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeAlphabet($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (!preg_match('/^[a-zA-Z0-9_.]/', $filtered_value))
        {
            $this->addError($message_form_validate_type_alphanumeric);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type alphanumeric & marks
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeANMarks($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_an_marks = sprintf('「%s」には半角英数字および記号を入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeANMarks($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (!preg_match('/^[a-zA-Z0-9_.%&#-]/', $filtered_value))
        {
            $this->addError($message_form_validate_type_an_marks);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type date
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeDate($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_date = sprintf('「%s」には年月日を「yyyy-mm-dd」「yyyy/mm/dd」「yyyymmdd」のいずれかの形式で入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeDate($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (strtotime($filtered_value) !== false)
        {
            $timestamp = strtotime($filtered_value);
            if ($timestamp === false)
            {
                $this->addError($message_form_validate_type_date);
                $result = false;
            }

            $year   = date('Y', $timestamp);
            $month  = date('n', $timestamp);
            $day    = date('j', $timestamp);
            if (checkdate($month, $day, $year) === false)
            {
                $this->addError($message_form_validate_type_date);
                $result = false;
            }
        }

        return $result;
    }



    /**
     * validate value type time
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeTime($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_time = sprintf('「%s」には時分秒または時分を入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeTime($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (!preg_match('/^[0-9]{2}[:.]?[0-5][0-9][:.]?([0-5][0-9])?/', $filtered_value))
        {
            $this->addError($message_form_validate_type_time);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type datetime
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeDatetime($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_datetime = sprintf('「%s」には年月日時分秒を入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeDatetime($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (strtotime($filtered_value) === false)
        {
            $this->addError($message_form_validate_type_datetime);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type tel
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeTel($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_tel = sprintf('「%s」には電話番号を入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeTel($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (!preg_match('/^([0-9]{2,3}-){0,1}[0-9]{1,4}-[0-9]{2,4}-[0-9]{2,4}$/', $filtered_value))
        {
            $this->addError($message_form_validate_type_tel);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type fax
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeFax($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_fax = sprintf('「%s」にはFAX番号を入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeFax($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (!preg_match('/^([0-9]{2,3}-){0,1}[0-9]{1,4}-[0-9]{2,4}-[0-9]{2,4}$/', $filtered_value))
        {
            $this->addError($message_form_validate_type_fax);
            $result = false;
        }

        return $result;
    }



    /**
     * validate value type email
     *
     * @param mixed $filtered_value
     * @return bool
     * @throws CitrusException
     */
    private function _validateVarTypeEmail($filtered_value) : bool
    {
        // result
        $result = true;

        // message
        $message_form_validate_type_email = sprintf('「%s」にはメールアドレスを入力してください。', $this->name);

        // validate
        if (is_array($filtered_value) === true)
        {
            foreach ($filtered_value as $one)
            {
                $result = $this->_validateVarTypeEmail($one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (!preg_match('/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?> :-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})$/i', $filtered_value))
        {
            $this->addError($message_form_validate_type_email);
            $result = false;
        }

        return $result;
    }



    /**
     * validate max
     *
     * @return bool
     * @throws CitrusException
     */
    protected function _validateMax()
    {
        try
        {
            // 入力がある場合のみチェックする。
            // null もしくは 空文字 はスルー
            if (is_null($this->value) === true || $this->value == '')
            {
                return true;
            }
            if (is_null($this->max) === false)
            {
                // numeric
                if (in_array($this->var_type, [ self::VAR_TYPE_INT, self::VAR_TYPE_FLOAT, self::VAR_TYPE_NUMERIC ], true) === true)
                {
                    return $this->_validateNumericMax();
                }
                else if ($this->var_type == self::VAR_TYPE_STRING)
                {
                    return $this->_validateLengthMax();
                }
            }
        }
        catch (CitrusException $ee)
        {
            throw $ee;
        }
    }



    /**
     * validate min
     *
     * @return bool
     * @throws CitrusException
     */
    protected function _validateMin()
    {
        try
        {
            // 入力がある場合のみチェックする。
            // null もしくは 空文字 はスルー
            if (is_null($this->value) === true || $this->value == '')
            {
                return true;
            }
            if (is_null($this->min) === false)
            {
                // numeric
                if (in_array($this->var_type, [ self::VAR_TYPE_INT, self::VAR_TYPE_FLOAT, self::VAR_TYPE_NUMERIC ], true) === true)
                {
                    return $this->_validateNumericMin();
                }
                else if ($this->var_type === self::VAR_TYPE_STRING)
                {
                    return $this->_validateLengthMin();
                }
            }
        }
        catch (CitrusException $e)
        {
            throw $e;
        }
    }



    /**
     * validate max numeric
     *
     * @return bool
     */
    protected function _validateNumericMax()
    {
        // result
        $result = true;

        // message
        $message_form_validate_numeric_max = sprintf('「%s」には「%s」以下の値を入力してください。', $this->name, $this->max);

        if ($this->value > $this->max)
        {
            $this->addError($message_form_validate_numeric_max);
            $result = false;
        }

        return $result;
    }



    /**
     * validate min numeric
     *
     * @return bool
     */
    protected function _validateNumericMin()
    {
        // result
        $result = true;

        // message
        $message_form_validate_numeric_min = sprintf('「%s」には「%s」以上の値を入力してください。', $this->name, $this->min);

        if ($this->value < $this->min)
        {
            $this->addError($message_form_validate_numeric_min);
            $result = false;
        }

        return $result;
    }



    /**
     * validate max length
     *
     * @return bool
     */
    protected function _validateLengthMax()
    {
        // result
        $result = true;

        // message
        $message_form_validate_length_max = sprintf('「%s」には「%s」文字以下で入力してください。', $this->name, $this->max);

        $length = mb_strwidth($this->value, 'UTF-8');
        if ($length > $this->max)
        {
            $this->addError($message_form_validate_length_max);
            $result = false;
        }

        return $result;
    }



    /**
     * validate min length
     *
     * @return bool
     */
    protected function _validateLengthMin()
    {
        // result
        $result = true;

        // message
        $message_form_validate_length_min = sprintf('「%s」には「%s」文字以上で入力してください。', $this->name, $this->min);

        $length = mb_strwidth($this->value, 'UTF-8');
        if ($length < $this->min)
        {
            $this->addError($message_form_validate_length_min);
            $result = false;
        }

        return $result;
    }

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
//        if (is_null($this->value) === true || $this->value == '')
//        {
//            return true;
//        }
//        if (is_null($element) === false)
//        {
//            // 日付の場合
//            if (($this->var_type == self::VAR_TYPE_DATE && $element->var_type == self::VAR_TYPE_DATE))
//            {
//                if (strtotime($this->value) > strtotime($element->value))
//                {
//                    CitrusMessage::addError('[ '.$this->name.' ] は ['.$element->name.' ] 以前の日付を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//
//                    return false;
//                }
//            }
//            // 数値系の場合
//            if ((
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
//                if (floatVal($this->value) > floatVal($element->value))
//                {
//                    CitrusMessage::addError('[ '.$this->name.' ] は ['.$element->name.' ] 以下の値を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
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
//        if (is_null($this->value) === true || $this->value == '')
//        {
//            return true;
//        }
//        if (is_null($element) === false)
//        {
//            // 日付の場合
//            if (($this->var_type == self::VAR_TYPE_DATE && $element->var_type == self::VAR_TYPE_DATE))
//            {
//                if (strtotime($this->value) < strtotime($element->value))
//                {
//                    CitrusMessage::addError('[ '.$this->name.' ] は ['.$element->name.' ] 以降の日付を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
//
//                    return false;
//                }
//            }
//            // 数値系の場合
//            if ((
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
//                if (floatVal($this->value) < floatVal($element->value))
//                {
//                    CitrusMessage::addError('[ '.$this->name.' ] は ['.$element->name.' ] 以上の値を入力してください。', '', CitrusFormmap::MESSAGE_TAG);
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
//        if (empty($value) === false)
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
//        if (empty($value) === false)
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


    /**
     * filter password hash
     *
     * @param $value
     * @return bool|string
     */
    public function filterPasswordHash($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }



    /**
     * add formmap error message
     *
     * @param string $message
     */
    private function addError(string $message)
    {
        CitrusMessage::addError($message, null, CitrusFormmap::MESSAGE_TAG);
    }
}