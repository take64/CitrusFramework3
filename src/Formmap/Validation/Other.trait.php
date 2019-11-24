<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Formmap\Validation;

use Citrus\Formmap\Element;
use Citrus\Formmap\FormmapException;

/**
 * フォームエレメントの検証(その他)
 */
trait Other
{
    /**
     * 電話番号チェック
     *
     * @param Element $element フォームエレメント
     * @param mixed   $var     検証値
     * @return void
     * @throws FormmapException
     */
    public static function varTypeTel(Element $element, $var): void
    {
        if (0 === preg_match('/^[0-9]{2,3}-[0-9]{1,4}-[0-9]{2,4}$/', $var))
        {
            throw new FormmapException(sprintf('「%s」には電話番号を入力してください。', $element->name));
        }
    }



    /**
     * メールアドレスチェック
     *
     * @param Element $element フォームエレメント
     * @param mixed   $var     検証値
     * @return void
     * @throws FormmapException
     */
    public static function varTypeEmail(Element $element, $var): void
    {
        if (0 === preg_match('/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?> :-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})$/i', $var))
        {
            throw new FormmapException(sprintf('「%s」にはメールアドレスを入力してください。', $element->name));
        }
    }
}
