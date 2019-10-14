<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;


class Command extends Struct
{
    /** @var string script code */
    public $script = '';

    /** @var string domain */
    public $domain = '';


    /**
     * constructor.
     */
    public function __construct()
    {
        global $argv;

        $parameters = [];
        foreach ($argv as $arg)
        {
            list($ky, $vl) = explode('=', $arg);
            $ky = str_replace('--', '', $ky);
            $parameters[$ky] = $vl;
        }

        $this->bind($parameters);
    }



    /**
     * execute
     */
    public function execute()
    {

    }



    /**
     * before
     */
    public function before()
    {

    }



    /**
     * after
     */
    public function after()
    {

    }


    /**
     * generate command instance
     *
     * @return Command
     */
    public static function callCommand() : Command
    {
        global $argv;
        unset($argv[0]);

        // パラメータ
        $parameters = [];
        foreach ($argv as $arg)
        {
            list($ky, $vl) = explode('=', $arg);
            $parameters[$ky] = $vl;
        }
        $script = $parameters['--script'];
        $domain = $parameters['--domain'];

        // アプリケーション
        $application = Configure::$CONFIGURE_ITEMS[$domain]->application;

        $class_paths = explode('-', $script);
        $class_path = $application->path . '/Command';
        $class_name = '';
        $namespace  = '\\' . ucfirst($application->id) . '\\Command';

        foreach ($class_paths as $one)
        {
            $part = ucfirst(strtolower($one));
            $class_path .= '/' . $part;
            $class_name = $part;

            // 最後の要素以外
            $last = $class_paths[count($class_paths) - 1];
            if ($last != $one)
            {
                $namespace  .= '\\' . $part;
            }
        }
        $class_path .= 'Command.class.php';
        $class_name  = $namespace . '\\' . $class_name . 'Command';

        include_once($class_path);

        /** @var Command $command */
        return new $class_name();
    }
}