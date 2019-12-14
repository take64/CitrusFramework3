<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Formmap\Element;
use Citrus\Formmap\FormmapException;
use Citrus\Formmap\Validation;
use Citrus\Session;
use PHPUnit\Framework\TestCase;

/**
 * フォームチェックテスト
 */
class ValidationTest extends TestCase
{
    /**
     * @test
     * @throws FormmapException
     */
    public function required_必須チェック_正常()
    {
        $element = new Element([
           'id' => 'user_id',
           'value' => 'hogehoge',
           'required' => true,
        ]);
        Validation::required($element);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function required_必須チェック_例外()
    {
        $this->expectException(FormmapException::class);
        $element = new Element([
            'id' => 'user_id',
            'value' => null,
            'required' => true,
        ]);
        Validation::required($element);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeInt_数値チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => 11,
        ]);
        Validation::varTypeInt($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeInt_数値チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => null,
        ]);
        Validation::varTypeInt($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeFlaot_浮動小数点チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => 11.5,
        ]);
        Validation::varTypeFloat($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeFlaot_浮動小数点チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => null,
        ]);
        Validation::varTypeFloat($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeNumeric_数値チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => 11.5,
        ]);
        Validation::varTypeNumeric($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeNumeric_数値チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => 'aaa',
        ]);
        Validation::varTypeNumeric($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeAlphabet_アルファベットチェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => 'abc',
        ]);
        Validation::varTypeAlphabet($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeAlphabet_アルファベットチェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => '11',
        ]);
        Validation::varTypeAlphabet($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeAlphanumeric_英数字チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => 'abc11',
        ]);
        Validation::varTypeAlphanumeric($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeAlphanumeric_英数字チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => '#$a',
        ]);
        Validation::varTypeAlphanumeric($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeANMarks_英数字記号チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => 'abc11$#',
        ]);
        Validation::varTypeANMarks($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeANMarks_英数字記号チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => 'あああ',
        ]);
        Validation::varTypeANMarks($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeDate_日付チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => '2019-11-11',
        ]);
        Validation::varTypeDate($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeDate_日付チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => '2019-13-13',
        ]);
        Validation::varTypeDate($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeTime_時間チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => '01:02:03',
        ]);
        Validation::varTypeTime($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeTime_時間チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => '01:02:79',
        ]);
        Validation::varTypeTime($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeDatetime_日時チェック_正常()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => '2019-11-11 01:02:03',
        ]);
        Validation::varTypeDatetime($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeDatetime_日時チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => '2019-13-13 01:02:79',
        ]);
        Validation::varTypeDatetime($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeTel_電話番号チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => '01-2345-6789',
        ]);
        Validation::varTypeTel($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeTel_電話番号チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => '0001-2345-6789',
        ]);
        Validation::varTypeTel($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeEmail_メールアドレスチェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => 'hoge@example.com',
        ]);
        Validation::varTypeEmail($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function varTypeEmail_メールアドレスチェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => 'hoge[at]example.c',
        ]);
        Validation::varTypeEmail($element, $element->filter());
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function numericMax_最大値チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => 10,
            'max' => 10
        ]);
        Validation::numericMax($element);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function numericMax_最大値チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => 11,
            'max' => 10
        ]);
        Validation::numericMax($element);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function numericMin_最小値チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => 1,
            'min' => 1
        ]);
        Validation::numericMin($element);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function numericMin_最小値チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => 0,
            'min' => 1
        ]);
        Validation::numericMin($element);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function lengthMax_最大値チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => '0123456789',
            'max' => 10
        ]);
        Validation::lengthMax($element);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function lengthMax_最大値チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => '01234567890',
            'max' => 10
        ]);
        Validation::lengthMax($element);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function lengthMin_最小値チェック_正常()
    {
        $element = new Element([
            'id' => 'user_id',
            'value' => '01',
            'min' => 2
        ]);
        Validation::lengthMin($element);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function lengthMin_最小値チェック_例外()
    {
        $this->expectException(FormmapException::class);

        $element = new Element([
            'id' => 'user_id',
            'value' => '0',
            'min' => 2
        ]);
        Validation::lengthMin($element);
    }
}

