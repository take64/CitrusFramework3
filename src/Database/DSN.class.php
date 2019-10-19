<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Struct;

class DSN extends Struct
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
            default:
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
            default:
        }
        return $dsn;
    }
}
