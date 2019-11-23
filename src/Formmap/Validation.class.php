<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Formmap;

/**
 * フォームエレメント
 */
class Validation
{
    /**
     * 必須チェック
     *
     * @param Element $element フォームエレメント
     * @return bool
     */
    public static function required(Element $element): bool
    {
        // 必須でなければtrue
        if (false === $element->required)
        {
            return true;
        }
        // 数値として認識できるということはtrue
        if (true === is_numeric($element->value))
        {
            return true;
        }

        $message = sprintf('「%s」は入力必須です。', $element->name);
        if (true === empty($element->value) and
            false === (true === $element->validate_null_safe and true === is_null($element->value)))
        {
            $element->addError($message);
            return false;
        }
        return true;
    }



    /**
     * 最大値チェック
     *
     * @param Element $element フォームエレメント
     * @return bool
     */
    public static function max(Element $element): bool
    {
        // 入力がある場合のみチェックする。
        // null もしくは 空文字 はスルー
        if (true === is_null($element->value) or '' === $element->value)
        {
            return true;
        }

        // 入力値チェック
        $result = true;
        if (false === is_null($element->max))
        {
            // numeric
            if (true === in_array($element->var_type, [ElementType::VAR_TYPE_INT, ElementType::VAR_TYPE_FLOAT, ElementType::VAR_TYPE_NUMERIC], true))
            {
                $result = self::numericMax($element);
            }
            else if (ElementType::VAR_TYPE_STRING === $element->var_type)
            {
                $result = self::lengthMax($element);
            }
        }
        return $result;
    }



    /**
     * 最小値チェック
     *
     * @param Element $element フォームエレメント
     * @return bool
     */
    public static function min(Element $element): bool
    {
        // 入力がある場合のみチェックする。
        // null もしくは 空文字 はスルー
        if (true === is_null($element->value) or '' === $element->value)
        {
            return true;
        }

        // 入力値チェック
        $result = true;
        if (false === is_null($element->min))
        {
            // numeric
            if (true === in_array($element->var_type, [ElementType::VAR_TYPE_INT, ElementType::VAR_TYPE_FLOAT, ElementType::VAR_TYPE_NUMERIC], true))
            {
                $result = self::numericMin($element);
            }
            else if (ElementType::VAR_TYPE_STRING === $element->var_type)
            {
                $result = self::lengthMin($element);
            }
        }
        return $result;
    }



    /**
     * 最大値チェック(数値)
     *
     * @param Element $element フォームエレメント
     * @return bool
     */
    public static function numericMax(Element $element): bool
    {
        // 結果
        $result = true;

        // 検証
        if ($element->value > $element->max)
        {
            $element->addError(sprintf('「%s」には「%s」以下の値を入力してください。', $element->name, $element->max));
            $result = false;
        }

        return $result;
    }



    /**
     * 最小値チェック(数値)
     *
     * @param Element $element フォームエレメント
     * @return bool
     */
    public static function numericMin(Element $element): bool
    {
        // 結果
        $result = true;

        // 検証
        if ($element->value < $element->min)
        {
            $element->addError(sprintf('「%s」には「%s」以上の値を入力してください。', $element->name, $element->min));
            $result = false;
        }

        return $result;
    }



    /**
     * 最大値チェック(文字列長)
     *
     * @param Element $element フォームエレメント
     * @return bool
     */
    public static function lengthMax(Element $element): bool
    {
        // 結果
        $result = true;

        // 検証
        $length = mb_strwidth($element->value, 'UTF-8');
        if ($length > $element->max)
        {
            $element->addError(sprintf('「%s」には「%s」文字以下で入力してください。', $element->name, $element->max));
            $result = false;
        }

        return $result;
    }



    /**
     * 最小値チェック(文字列長)
     *
     * @param Element $element フォームエレメント
     * @return bool
     */
    public static function lengthMin(Element $element): bool
    {
        // 結果
        $result = true;

        // 検証
        $length = mb_strwidth($element->value, 'UTF-8');
        if ($length < $element->min)
        {
            $element->addError(sprintf('「%s」には「%s」文字以上で入力してください。', $element->name, $element->min));
            $result = false;
        }

        return $result;
    }



    /**
     * 型チェック
     *
     * @param Element $element フォームエレメント
     * @return bool
     */
    public static function varType(Element $element): bool
    {
        // 入力がある場合のみチェックする。
        if (true === is_null($element->value) or '' === $element->value)
        {
            return true;
        }

        // filter
        $filtered_value = $element->filter();

        // validate
        $result = true;
        switch($element->var_type)
        {
            // int
            case ElementType::VAR_TYPE_INT:
                $result = self::varTypeInt($element, $filtered_value);
                break;

            // float
            case ElementType::VAR_TYPE_FLOAT:
                $result = self::varTypeFloat($element, $filtered_value);
                break;

            // numeric
            case ElementType::VAR_TYPE_NUMERIC:
                $result = self::varTypeNumeric($element, $filtered_value);
                break;

            // string
            case ElementType::VAR_TYPE_STRING :
                break;

            // alphabet
            case ElementType::VAR_TYPE_ALPHABET :
                $result = self::varTypeAlphabet($element, $filtered_value);
                break;

            // alphabet & numeric
            case ElementType::VAR_TYPE_ALPHANUMERIC :
                $result = self::varTypeAlphanumeric($element, $filtered_value);
                break;

            // alphabet & numeric & marks
            case ElementType::VAR_TYPE_AN_MARKS :
                $result = self::varTypeANMarks($element, $filtered_value);
                break;

            // date
            case ElementType::VAR_TYPE_DATE :
                $result = self::varTypeDate($element, $filtered_value);
                break;

            // time
            case ElementType::VAR_TYPE_TIME :
                $result = self::varTypeTime($element, $filtered_value);
                break;

            // datetime
            case ElementType::VAR_TYPE_DATETIME :
                $result = self::varTypeDatetime($element, $filtered_value);
                break;

            // tel
            case ElementType::VAR_TYPE_TEL :
                $result = self::varTypeTel($element, $filtered_value);
                break;

            // email
            case ElementType::VAR_TYPE_EMAIL :
                $result = self::varTypeEmail($element, $filtered_value);
                break;

            // other
            default :
                $result = true;
                break;
        }
        return $result;
    }


    /**
     * 型チェック(int)
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeInt(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeInt($element, $one);
                if (false === $result)
                {
                    break;
                }
            }
        }
        else if (false === is_int(intval($filtered_value)) and
            false === is_numeric($filtered_value) and
            0 === preg_match('/^-?[0-9]*$/', $filtered_value))
        {
            $element->addError(sprintf('「%s」には整数を入力してください。', $element->name));
            $result = false;
        }
        else if (PHP_INT_MAX <= $filtered_value)
        {
            $element->addError(sprintf('「%s」には「%s」以下の値を入力してください。', $element->name, PHP_INT_MAX));
            $result = false;
        }
        else if (PHP_INT_MIN >= $filtered_value)
        {
            $element->addError(sprintf('「%s」には「%s」以上の値を入力してください。', $element->name, PHP_INT_MIN));
            $result = false;
        }

        return $result;
    }



    /**
     * 型チェック(float)
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeFloat(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeFloat($element, $one);
                if (false === $result)
                {
                    break;
                }
            }
        }
        else if (false === is_float($filtered_value))
        {
            $element->addError(sprintf('「%s」には少数を入力してください。', $element->name));
            $result = false;
        }

        return $result;
    }



    /**
     * 型チェック(数値として認識できる)
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeNumeric(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeNumeric($element, $one);
                if (false === $result)
                {
                    break;
                }
            }
        }
        else if (false === is_numeric($filtered_value))
        {
            $element->addError(sprintf('「%s」には数字を入力してください。', $element->name));
            $result = false;
        }

        return $result;
    }



    /**
     * アルファベットチェック
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeAlphabet(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeAlphabet($element, $one);
                if (false === $result)
                {
                    break;
                }
            }
        }
        else if (false === is_string($filtered_value))
        {
            $element->addError(sprintf('「%s」には文字列を入力してください。', $element->name));
            $result = false;
        }
        else if (0 === preg_match('/^[a-zA-Z]/', $filtered_value))
        {
            $element->addError(sprintf('「%s」には半角英字を入力してください。', $element->name));
            $result = false;
        }

        return $result;
    }



    /**
     * 英数字チェック
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeAlphanumeric(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeAlphanumeric($element, $one);
                if (false === $result)
                {
                    break;
                }
            }
        }
        else if (false === is_string($filtered_value))
        {
            $element->addError(sprintf('「%s」には文字列を入力してください。', $element->name));
            $result = false;
        }
        else if (0 === preg_match('/^[a-zA-Z0-9_.]/', $filtered_value))
        {
            $element->addError(sprintf('「%s」には半角英数字を入力してください。', $element->name));
            $result = false;
        }

        return $result;
    }



    /**
     * 英数字と記号チェック
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeANMarks(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeANMarks($element, $one);
                if (false === $result)
                {
                    break;
                }
            }
        }
        else if (false === is_string($filtered_value))
        {
            $element->addError(sprintf('「%s」には文字列を入力してください。', $element->name));
            $result = false;
        }
        else if (0 === preg_match('/^[a-zA-Z0-9_.%&#-]/', $filtered_value))
        {
            $element->addError(sprintf('「%s」には半角英数字および記号を入力してください。', $element->name));
            $result = false;
        }

        return $result;
    }



    /**
     * 日付チェック
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeDate(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証　
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeDate($element, $one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (false === strtotime($filtered_value))
        {
            $result = false;
        }
        else
        {
            $timestamp = strtotime($filtered_value);
            $year   = (int)date('Y', $timestamp);
            $month  = (int)date('n', $timestamp);
            $day    = (int)date('j', $timestamp);
            if (false === checkdate($month, $day, $year))
            {
                $result = false;
            }
        }

        if (false === $result)
        {
            $element->addError(sprintf('「%s」には年月日を「yyyy-mm-dd」「yyyy/mm/dd」「yyyymmdd」のいずれかの形式で入力してください。', $element->name));
        }

        return $result;
    }



    /**
     * 時間チェック
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeTime(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeTime($element, $one);
                if (false === $result)
                {
                    break;
                }
            }
        }
        else if (false === is_string($filtered_value))
        {
            $element->addError(sprintf('「%s」には文字列を入力してください。', $element->name));
            $result = false;
        }
        else if (0 === preg_match('/^[0-9]{2}:[0-5][0-9]:?([0-5][0-9])+?/', $filtered_value))
        {
            $element->addError(sprintf('「%s」には時分秒または時分を入力してください。', $element->name));
            $result = false;
        }

        return $result;
    }



    /**
     * 日時チェック
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeDatetime(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeDatetime($element, $one);
                if (false === $result)
                {
                    break;
                }
            }
        }
        else if (false === is_string($filtered_value))
        {
            $element->addError(sprintf('「%s」には文字列を入力してください。', $element->name));
            $result = false;
        }
        else if (false === strtotime($filtered_value))
        {
            $element->addError(sprintf('「%s」には年月日時分秒を入力してください。', $element->name));
            $result = false;
        }

        return $result;
    }



    /**
     * 電話番号チェック
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeTel(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeTel($element, $one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (false === is_string($filtered_value))
        {
            $element->addError(sprintf('「%s」には文字列を入力してください。', $element->name));
            $result = false;
        }
        else if (0 === preg_match('/^[0-9]{2,3}-[0-9]{1,4}-[0-9]{2,4}$/', $filtered_value))
        {
            $element->addError(sprintf('「%s」には電話番号を入力してください。', $element->name));
            $result = false;
        }

        return $result;
    }



    /**
     * メールアドレスチェック
     *
     * @param Element $element        フォームエレメント
     * @param mixed   $filtered_value フィルター済みの値
     * @return bool
     */
    public static function varTypeEmail(Element $element, $filtered_value): bool
    {
        // 結果
        $result = true;

        // 検証
        if (true === is_array($filtered_value))
        {
            foreach ($filtered_value as $one)
            {
                $result = self::varTypeEmail($element, $one);
                if ($result === false)
                {
                    break;
                }
            }
        }
        else if (false === is_string($filtered_value))
        {
            $element->addError(sprintf('「%s」には文字列を入力してください。', $element->name));
            $result = false;
        }
        else if (0 === preg_match('/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?> :-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})$/i', $filtered_value))
        {
            $element->addError(sprintf('「%s」にはメールアドレスを入力してください。', $element->name));
            $result = false;
        }

        return $result;
    }
}
