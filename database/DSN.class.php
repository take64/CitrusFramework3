<?php

/**
 * DSN.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     .
 * @subpackage  .
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Database;


use Citrus\CitrusObject;

class CitrusDatabaseDSN extends CitrusObject
{
    /** @var string */
    public $type;

    /** @var string */
    public $hostname;

    /** @var string */
    public $port;

    /** @var string */
    public $database;

    /** @var string */
    public $schema;

    /** @var string */
    public $username;

    /** @var string */
    public $password;



    /**
     * generate dsn string
     *
     * @return string
     */
    public function toString()
    {
        $dsn = '';
        switch ($this->type)
        {
            case 'pgsql' :
            case 'postgres' :
            case 'postgresql' :
                $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s',
                    $this->hostname,
                    $this->port,
                    $this->database
                    );
                break;
        }
        return $dsn;
    }



    /**
     * generate dsn string with authentication
     *
     * @return string
     */
    public function toStringWithAuth()
    {
        $dsn = '';
        switch ($this->type)
        {
            case 'pgsql' :
            case 'postgres' :
            case 'postgresql' :
                $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s',
                    $this->hostname,
                    $this->port,
                    $this->database,
                    $this->username,
                    $this->password
                );
                break;
        }
        return $dsn;
    }
}
