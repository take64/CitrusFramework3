<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Formmap;

/**
 * エレメントタイプ
 */
class ElementType
{
    /** var type int */
    const VAR_TYPE_INT = 'int';

    /** var type float */
    const VAR_TYPE_FLOAT = 'float';

    /** var type numeric */
    const VAR_TYPE_NUMERIC = 'numeric';

    /** var type string */
    const VAR_TYPE_STRING = 'string';

    /** var type alphabet */
    const VAR_TYPE_ALPHABET = 'alphabet';

    /** var type alphabet & numeric */
    const VAR_TYPE_ALPHANUMERIC = 'alphanumeric';

    /** var type alphabet & numeric & marks */
    const VAR_TYPE_AN_MARKS = 'an_marks';

    /** var type date */
    const VAR_TYPE_DATE = 'date';

    /** var type time */
    const VAR_TYPE_TIME = 'time';

    /** var type datetime */
    const VAR_TYPE_DATETIME = 'dateime';

    /** var type bool */
    const VAR_TYPE_BOOL = 'bool';

    /** var type file */
    const VAR_TYPE_FILE = 'file';

    /** var type telephone */
    const VAR_TYPE_TELEPHONE = 'telephone';

    /** var type tel */
    const VAR_TYPE_TEL = 'tel';

    /** var type year */
    const VAR_TYPE_YEAR = 'year';

    /** var type month */
    const VAR_TYPE_MONTH = 'month';

    /** var type day */
    const VAR_TYPE_DAY = 'day';

    /** var type email */
    const VAR_TYPE_EMAIL = 'email';


    /** form type element */
    const FORM_TYPE_ELEMENT = 'element';

    /** form type text */
    const FORM_TYPE_TEXT = 'text';

    /** form type text */
    const FORM_TYPE_TEXTAREA = 'textarea';

    /** form type search */
    const FORM_TYPE_SEARCH = 'search';

    /** form type hidden */
    const FORM_TYPE_HIDDEN = 'hidden';

    /** form type select */
    const FORM_TYPE_SELECT = 'select';

    /** form type password */
    const FORM_TYPE_PASSWD = 'password';

    /** form type submit */
    const FORM_TYPE_SUBMIT = 'submit';

    /** form type button */
    const FORM_TYPE_BUTTON = 'button';

    /** form type label */
    const FORM_TYPE_LABEL = 'label';

    /** html tag span */
    const HTML_TAG_SPAN = 'span';

    /** @var string[] 数値系要素 */
    public static $NUMERICALS = [
        self::VAR_TYPE_INT,
        self::VAR_TYPE_FLOAT,
        self::VAR_TYPE_NUMERIC,
    ];



    /**
     * formmapのelement配列要素からフォームインスタンスを生成
     *
     * @param array $element formmap要素
     * @return Element|null フォームインスタンス
     */
    public static function generate(array $element): ?Element
    {
        $form_type = $element['form_type'];

        switch ($form_type) {
            // デフォルトエレメント
            case ElementType::FORM_TYPE_ELEMENT:
                return new Element($element);
            // 隠し要素
            case ElementType::FORM_TYPE_HIDDEN:
                return new Hidden($element);
            // パスワード
            case ElementType::FORM_TYPE_PASSWD:
                return new Password($element);
            // SELECT
            case ElementType::FORM_TYPE_SELECT:
                return new Select($element);
            // SUBMIT
            case ElementType::FORM_TYPE_SUBMIT:
                return new Submit($element);
            // ボタン
            case ElementType::FORM_TYPE_BUTTON:
                return new Button($element);
            // インプットテキスト
            case ElementType::FORM_TYPE_TEXT:
                return new Text($element);
            // テキストエリア
            case ElementType::FORM_TYPE_TEXTAREA:
                return new Textarea($element);
            // 検索エリア
            case ElementType::FORM_TYPE_SEARCH:
                return new Search($element);
            // 該当なし
            default:
                return null;
        }
    }
}
