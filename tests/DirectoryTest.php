<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2019, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\Directory;
use PHPUnit\Framework\TestCase;

/**
 * ディレクトリ処理のテスト
 */
class DirecotryTest extends TestCase
{
    /**
     * @test
     */
    public function 親参照が入るデレクトリパスを適切な形にする()
    {
        // 親参照が入るパス
        $source_path = '/var/www/html/public/app/src/../src/citrus-configure.php';
        // 期待するパス
        $expected_path = '/var/www/html/public/app/src/citrus-configure.php';
        // 適切な形に変換
        $suitable_path = Directory::suitablePath($source_path);
        // 検算
        $this->assertSame($expected_path, $suitable_path);


        // 親参照が入るパス、自参照
        $source_path = '/var/www/html/public/app/src/./../src/citrus-configure.php';
        // 期待するパス
        $expected_path = '/var/www/html/public/app/src/citrus-configure.php';
        // 適切な形に変換
        $suitable_path = Directory::suitablePath($source_path);
        // 検算
        $this->assertSame($expected_path, $suitable_path);


        // 親参照が複数入るパス
        $source_path = '/var/www/html/public/app/src/../../app/src/citrus-configure.php';
        // 期待するパス
        $expected_path = '/var/www/html/public/app/src/citrus-configure.php';
        // 適切な形に変換
        $suitable_path = Directory::suitablePath($source_path);
        // 検算
        $this->assertSame($expected_path, $suitable_path);
    }
}
