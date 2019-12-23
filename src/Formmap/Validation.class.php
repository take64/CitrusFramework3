<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Formmap;

use Citrus\Formmap\Validation\Datetime;
use Citrus\Formmap\Validation\Other;
use Citrus\Formmap\Validation\Size;
use Citrus\Formmap\Validation\VarType;
use Citrus\Variable\Strings;

/**
 * フォームエレメントの検証
 */
class Validation
{
    // 検証(サイズ)
    use Size;
    // 検証(変数型)
    use VarType;
    // 検証(日時)
    use Datetime;
    // 検証(その他)
    use Other;



    /**
     * 型チェック
     *
     * @param Element $element フォームエレメント
     * @return void
     * @throws FormmapException
     */
    public static function varType(Element $element): void
    {
        // 入力がある場合のみチェックする。
        if (false === is_numeric($element->value) and true === Strings::isEmpty($element->value))
        {
            return;
        }

        // フィルター
        $filtered_values = $element->filter();
        // 配列化
        $filtered_values = (true === is_array($filtered_values) ? $filtered_values : [$filtered_values]);
        // 配列分精査する
        foreach ($filtered_values as $filtered_value)
        {
            switch($element->var_type)
            {
                // int
                case ElementType::VAR_TYPE_INT:
                    self::varTypeInt($element, $filtered_value);
                    break;

                // float
                case ElementType::VAR_TYPE_FLOAT:
                    self::varTypeFloat($element, $filtered_value);
                    break;

                // numeric
                case ElementType::VAR_TYPE_NUMERIC:
                    self::varTypeNumeric($element, $filtered_value);
                    break;

                // string
                case ElementType::VAR_TYPE_STRING:
                    self::varTypeString($element, $filtered_value);
                    break;

                // alphabet
                case ElementType::VAR_TYPE_ALPHABET:
                    self::varTypeString($element, $filtered_value);
                    self::varTypeAlphabet($element, $filtered_value);
                    break;

                // alphabet & numeric
                case ElementType::VAR_TYPE_ALPHANUMERIC:
                    self::varTypeString($element, $filtered_value);
                    self::varTypeAlphanumeric($element, $filtered_value);
                    break;

                // alphabet & numeric & marks
                case ElementType::VAR_TYPE_AN_MARKS:
                    self::varTypeString($element, $filtered_value);
                    self::varTypeANMarks($element, $filtered_value);
                    break;

                // date
                case ElementType::VAR_TYPE_DATE:
                    self::varTypeDate($element, $filtered_value);
                    break;

                // time
                case ElementType::VAR_TYPE_TIME:
                    self::varTypeString($element, $filtered_value);
                    self::varTypeTime($element, $filtered_value);
                    break;

                // datetime
                case ElementType::VAR_TYPE_DATETIME:
                    self::varTypeString($element, $filtered_value);
                    self::varTypeDatetime($element, $filtered_value);
                    break;

                // tel
                case ElementType::VAR_TYPE_TEL:
                    self::varTypeString($element, $filtered_value);
                    self::varTypeTel($element, $filtered_value);
                    break;

                // email
                case ElementType::VAR_TYPE_EMAIL:
                    self::varTypeString($element, $filtered_value);
                    self::varTypeEmail($element, $filtered_value);
                    break;

                // other
                default:
                    break;
            }
        }
    }
}
