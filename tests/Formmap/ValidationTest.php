<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Formmap\Element;
use Citrus\Formmap\Validation;
use Citrus\Session;
use PHPUnit\Framework\TestCase;

/**
 * フォームチェックテスト
 */
class ValidationTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // セッションデータ生成
        Session::factory();
    }


    public function tearDown(): void
    {
        parent::tearDown();

        // セッション終了
        Session::commit();
    }



    /**
     * @test
     */
    public function required_必須チェック()
    {
        // true
        $element = new Element([
           'id' => 'user_id',
           'value' => 'hogehoge',
           'required' => true,
        ]);
        $this->assertTrue(Validation::required($element));

        // false
        $element->value = null;
        $this->assertFalse(Validation::required($element));
    }



    /**
     * @test
     */
    public function varTypeInt_数値チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => 11,
        ]);
        $this->assertTrue(Validation::varTypeInt($element, $element->filter()));

        // false
        $element->value = null;
        $this->assertFalse(Validation::varTypeInt($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeFlaot_浮動小数点チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => 11.5,
        ]);
        $this->assertTrue(Validation::varTypeFloat($element, $element->filter()));

        // false
        $element->value = null;
        $this->assertFalse(Validation::varTypeFloat($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeNumeric_数値チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => 11.5,
        ]);
        $this->assertTrue(Validation::varTypeNumeric($element, $element->filter()));

        // false
        $element->value = 'aaa';
        $this->assertFalse(Validation::varTypeNumeric($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeAlphabet_アルファベットチェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => 'abc',
        ]);
        $this->assertTrue(Validation::varTypeAlphabet($element, $element->filter()));

        // false
        $element->value = 11;
        $this->assertFalse(Validation::varTypeAlphabet($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeAlphanumeric_英数字チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => 'abc11',
        ]);
        $this->assertTrue(Validation::varTypeAlphanumeric($element, $element->filter()));

        // false
        $element->value = '#$a';
        $this->assertFalse(Validation::varTypeAlphanumeric($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeANMarks_英数字記号チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => 'abc11$#',
        ]);
        $this->assertTrue(Validation::varTypeANMarks($element, $element->filter()));

        // false
        $element->value = 'あああ';
        $this->assertFalse(Validation::varTypeANMarks($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeDate_日付チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => '2019-11-11',
        ]);
        $this->assertTrue(Validation::varTypeDate($element, $element->filter()));

        // false
        $element->value = '2019-13-13';
        $this->assertFalse(Validation::varTypeDate($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeTime_時間チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => '01:02:03',
        ]);
        $this->assertTrue(Validation::varTypeTime($element, $element->filter()));

        // false
        $element->value = '01:02:79';
        $this->assertFalse(Validation::varTypeTime($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeDatetime_日時チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => '2019-11-11 01:02:03',
        ]);
        $this->assertTrue(Validation::varTypeDatetime($element, $element->filter()));

        // false
        $element->value = '2019-13-13 01:02:79';
        $this->assertFalse(Validation::varTypeDatetime($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeTel_電話番号チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => '01-2345-6789',
        ]);
        $this->assertTrue(Validation::varTypeTel($element, $element->filter()));

        // false
        $element->value = '0001-2345-6789';
        $this->assertFalse(Validation::varTypeTel($element, $element->filter()));
    }



    /**
     * @test
     */
    public function varTypeEmail_メールアドレスチェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => 'hoge@example.com',
        ]);
        $this->assertTrue(Validation::varTypeEmail($element, $element->filter()));

        // false
        $element->value = 'hoge[at]example.c';
        $this->assertFalse(Validation::varTypeEmail($element, $element->filter()));
    }



    /**
     * @test
     */
    public function numericMax_最大値チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => 10,
            'max' => 10
        ]);
        $this->assertTrue(Validation::numericMax($element));

        // false
        $element->value = 11;
        $this->assertFalse(Validation::numericMax($element));
    }



    /**
     * @test
     */
    public function numericMin_最小値チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => 1,
            'min' => 1
        ]);
        $this->assertTrue(Validation::numericMin($element));

        // false
        $element->value = 0;
        $this->assertFalse(Validation::numericMin($element));
    }



    /**
     * @test
     */
    public function lengthMax_最大値チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => '0123456789',
            'max' => 10
        ]);
        $this->assertTrue(Validation::lengthMax($element));

        // false
        $element->value = '01234567890';
        $this->assertFalse(Validation::lengthMax($element));
    }



    /**
     * @test
     */
    public function lengthMin_最小値チェック()
    {
        // true
        $element = new Element([
            'id' => 'user_id',
            'value' => '01',
            'min' => 2
        ]);
        $this->assertTrue(Validation::lengthMin($element));

        // false
        $element->value = '0';
        $this->assertFalse(Validation::lengthMin($element));
    }
}

