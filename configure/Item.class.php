<?php
/**
 * Item.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Configure
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Configure;


use Citrus\CitrusLogger;
use Citrus\CitrusNVL;
use Citrus\Database\CitrusDatabaseDSN;
use Citrus\Logger\CitrusLoggerType;

class CitrusConfigureItem
{
    /** @var string */
    public $domain;

    /** @var CitrusConfigureApplication */
    public $application;

    /** @var CitrusDatabaseDSN */
    public $database;

    /** @var CitrusLoggerType */
    public $logger;

    /** @var CitrusConfigurePaths */
    public $paths;

//    /** @var string */
//    public $logger_type;

    /**
     * constructor.
     *
     * @param array $default_configure
     * @param array $configure
     */
    public function __construct(array $default_configure, array $configure)
    {
        // application
        $key = 'application';
        $this->application = new CitrusConfigureApplication();
        $this->application->bind(CitrusNVL::ArrayVL($default_configure, $key, []));
        $this->application->bind(CitrusNVL::ArrayVL($configure, $key, []));

        // database.sh
        $key = 'database';
        $this->database = new CitrusDatabaseDSN();
        $this->database->bind(CitrusNVL::ArrayVL($default_configure, $key, []));
        $this->database->bind(CitrusNVL::ArrayVL($configure, $key, []));

        // logger
        $this->logger = CitrusLogger::initialize($default_configure, $configure);

        // paths
        $key = 'paths';
        $this->paths = new CitrusConfigurePaths();
        $this->paths->domain = $this->application->domain;
        $this->paths->bind(CitrusNVL::ArrayVL($default_configure, $key, []));
        $this->paths->bind(CitrusNVL::ArrayVL($configure, $key, []));
    }
}