<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


use Citrus\Autoloader\CitrusAutoloaderException;

class CitrusAutoloader
{
    /** @var string[] 読み込み可能な拡張子  */
    const EXTENTIONS = [
        'class',
        'interface',
        'abstract',
        'trait',
        'enum',
    ];



    /**
     * autoload framework
     *
     * @throws CitrusAutoloaderException
     */
    public static function autoloadFramework()
    {
        spl_autoload_register(function($use_class_name) {

            $namespace_head = 'Citrus\\';
            if (strpos($use_class_name, $namespace_head) === false)
            {
                return ;
            }

            // クラスパス要素の生成
            $use_class_paths = self::convertSimplePathsFromClassName($use_class_name);

            $class_file_path = sprintf('%s/%s.class.php',
                dirname(__FILE__),
                str_replace('Citrus/', '', $use_class_name)
            );

            $package_name = array_shift($use_class_paths);
            $class_name = array_pop($use_class_paths);

            $class_file_name = str_replace(implode(array_merge([ $package_name ], $use_class_paths)), '', $class_name);
            // 対象ファイルが見つからない時はベースのCitrusクラス
            if ($class_file_name === '')
            {
                $class_file_name = 'Citrus';
            }

            // パス確定
            $is_load_class = false;
            foreach (self::EXTENTIONS as $extention)
            {
                $class_file_path = sprintf('%s/%s%s.%s.php',
                    dirname(__FILE__),
                    strtolower(implode('/',  $use_class_paths) . '/'),
                    $class_file_name,
                    $extention
                );
                if (self::loadClass($class_file_path) === true)
                {
                    $is_load_class = true;
                    break;
                }
            }

            if ($is_load_class === false)
            {
                $error_message = 'load faild = ' . $class_file_path;
                throw new CitrusAutoloaderException($error_message);
            }
        });
    }



    /**
     * autoload application
     *
     * @throws CitrusAutoloaderException
     */
    public static function autoloadApplication()
    {
        spl_autoload_register(function($use_class_name) {
            $namespace_application = ucfirst(CitrusConfigure::$CONFIGURE_ITEM->application->id);
            $namespace_head = $namespace_application . '\\';
            if (strpos($use_class_name, $namespace_head) === false)
            {
                return ;
            }

            // クラスパス要素の生成
            $use_class_paths = self::convertSimplePathsFromClassName($use_class_name);

            // application base path
            $application_base_path = CitrusConfigure::$CONFIGURE_ITEM->application->path;

            // search path
            foreach ($use_class_paths as $ky => $vl)
            {
                // application namespace は削除
                if ($vl === $namespace_application)
                {
                    unset($use_class_paths[$ky]);
                    continue;
                }
                // class 名の中の application namespace は置換
                if (strpos($vl, $namespace_application) !== false)
                {
                    $use_class_paths[$ky] = str_replace($namespace_application, '', $vl);
                }
            }

            // パスに含まれる重複文字列の除去
            $_use_class_paths = $use_class_paths;
            $_use_class = array_pop($_use_class_paths);
            foreach ($_use_class_paths as $one)
            {
                if (empty($one) === true)
                {
                    continue;
                }
                if (strpos($_use_class, $one) === 0)
                {
                    $_use_class = str_replace($one, '', $_use_class);
                }
            }
            $_use_class_paths[] = $_use_class;
            $use_class_paths = $_use_class_paths;

            // パス確定
            $is_load_class = false;
            foreach (self::EXTENTIONS as $extention)
            {
                $class_file_path = sprintf('%s/%s.%s.php',
                    $application_base_path,
                    implode('/', $use_class_paths),
                    $extention
                );
                if (self::loadClass($class_file_path) === true)
                {
                    $is_load_class = true;
                    break;
                }
            }

            if ($is_load_class === false)
            {
                $error_message = 'load faild = ' . $class_file_path;
                throw new CitrusAutoloaderException($error_message);
            }
        });
    }



    /**
     * use で指定されたクラス名から、単純なパスを割り出す
     *
     * @param string $use_class_name useで指定されたクラス
     * @return string[]
     */
    private static function convertSimplePathsFromClassName(string $use_class_name) : array
    {
        $use_class_name = str_replace('\\', '/', $use_class_name);
        return explode('/', $use_class_name);
    }



    /**
     * クラスを読み込む
     *
     * @param string $class_file_path クラスパス
     * @return bool true:読込成功,false:読込失敗
     */
    private static function loadClass(string $class_file_path) : bool
    {
        $result = false;
        if (file_exists($class_file_path) === true)
        {
            include_once $class_file_path;
            $result = true;
        }
        return $result;
    }
}