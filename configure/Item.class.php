<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Configure;


use Citrus\CitrusNVL;
use Citrus\Database\CitrusDatabaseDSN;

class CitrusConfigureItem
{
    /** @var string */
    public $domain;

    /** @var CitrusConfigureApplication */
    public $application;

    /** @var CitrusDatabaseDSN */
    public $database;

    /** @var CitrusConfigurePaths */
    public $paths;

    /** @var CitrusConfigureRouting */
    public $routing;




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

        // paths
        $key = 'paths';
        $this->paths = new CitrusConfigurePaths();
        $this->paths->domain = $this->application->domain;
        $this->paths->bind(CitrusNVL::ArrayVL($default_configure, $key, []));
        $this->paths->bind(CitrusNVL::ArrayVL($configure, $key, []));

        // routing
        $key = 'routing';
        $this->routing = new CitrusConfigureRouting();
        $this->routing->bind(CitrusNVL::ArrayVL($default_configure, $key, []));
        $this->routing->bind(CitrusNVL::ArrayVL($configure, $key, []));
    }
}
