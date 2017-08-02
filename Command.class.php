<?php
/**
 * Command.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  .
 * @license     http://www.besidesplus.net/
 */

namespace Citrus;


class CitrusCommand extends CitrusObject
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
     * generate command instance
     *
     * @return CitrusCommand
     */
    public static function callCommand() : CitrusCommand
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
        $application = CitrusConfigure::$CONFIGURE_ITEMS[$domain]->application;

        $class_paths = explode('-', $script);
        $class_path = $application->path . '/Command';
        $class_name = ucfirst($application->id);
        $namespace  = '\\' . ucfirst($application->id) . '\\Command';
        foreach ($class_paths as $one)
        {
            $part = ucfirst(strtolower($one));
            $class_path .= '/' . $part;
            $class_name .= $part;

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

        /** @var CitrusCommand $command */
        $command = new $class_name();

        return $command;
    }
}