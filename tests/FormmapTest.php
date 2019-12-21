<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Authentication\Item;
use Citrus\Configure\ConfigureException;
use Citrus\Formmap;
use Citrus\Formmap\FormmapException;
use Citrus\Formmap\Text;
use Citrus\Session;
use PHPUnit\Framework\TestCase;

/**
 * フォームマップのテスト
 */
class FormmapTest extends TestCase
{
    /** @var Formmap */
    private $formmap;



    /**
     * {@inheritDoc}
     *
     * @throws ConfigureException
     */
    public function setUp(): void
    {
        parent::setUp();

        // 設定配列
        $configures = require(dirname(__DIR__). '/tests/citrus-configure.php');
        // formmapインスタンス
        $this->formmap = Formmap::sharedInstance()->loadConfigures($configures);
    }



    /**
     * @test
     * @throws FormmapException
     */
    public function ファイルが存在する場合は正常()
    {
        // formmap読み込み
        $this->formmap->load('Login.php');

        /** @var Text $user_id_element */
        $user_id_element = $this->formmap->user_id;
        $this->assertTrue($user_id_element instanceof Text);
        $this->assertSame('', $user_id_element->prefix);
        $this->assertSame('user_id', $user_id_element->id);
        $this->assertSame('text', $user_id_element->form_type);
        $this->assertSame('string', $user_id_element->var_type);
        $this->assertSame('ユーザID', $user_id_element->name);
        $this->assertNull($user_id_element->class);
        $this->assertFalse($user_id_element->required);
    }



    /**
     * @test
     */
    public function ファイルが存在しない場合は例外()
    {
        $this->expectException(FormmapException::class);

        // formmap読み込み
        $this->formmap->load('Login1.php');
    }



    /**
     * @test
     */
    public function REQUEST値が設定される()
    {
        // formmap読み込み
        $this->formmap->load('Login.php');

        // 設定値
        $value = 'aaa';

        // REQUESTに設定
        Session::$request->regist('user_id', $value);

        // 反映
        $this->formmap->bind();

        /** @var Text $user_id_element */
        $user_id_element = $this->formmap->user_id;
        // 値が設定されている
        $this->assertSame($value, $user_id_element->callValue());

        // ジェネレータからも値が設定されているのを確認できる
        /** @var Item $item */
        $item = $this->formmap->generate('Login', 'login');
        $this->assertSame($value, $item->user_id);
    }
}
