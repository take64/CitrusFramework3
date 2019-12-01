<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Configure\Configurable;
use Citrus\Singleton;
use Citrus\Variable\Structs;

/**
 * DSN定義
 */
class DSN extends Configurable
{
    use Singleton;
    use Structs;

    /** @var string[] PostgreSQL */
    public const TYPES_POSTGRESQL = [
        'pgsql',
        'postgres',
        'postgresql',
    ];

    /** @var string[] SQLite */
    public const TYPES_SQLITE = [
        'sqlite',
    ];

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
     * {@inheritDoc}
     *
     * @return self
     */
    public function loadConfigures(array $configures = []): Configurable
    {
        parent::loadConfigures($configures);

        // bind
        $this->bindArray($this->configures);

        return $this;
    }



    /**
     * generate dsn string
     *
     * @return string
     */
    public function toString()
    {
        $dsn = '';

        // PostgreSQL
        if (true === $this->isPostgreSQL())
        {
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s',
                $this->hostname,
                $this->port,
                $this->database
            );
        }
        // SQLite
        else if (true === $this->isSQLite())
        {
            $dsn = sprintf('sqlite:%s',
                $this->hostname
            );
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

        // PostgreSQL
        if (true === $this->isPostgreSQL())
        {
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s',
                $this->hostname,
                $this->port,
                $this->database,
                $this->username,
                $this->password
            );
        }
        // SQLite
        else if (true === $this->isSQLite())
        {
            $dsn = sprintf('sqlite:%s',
                $this->hostname
            );
        }

        return $dsn;
    }



    /**
     * データベースタイプがPostgreSQLかどうか
     *
     * @return bool
     */
    public function isPostgreSQL()
    {
        return in_array($this->type, self::TYPES_POSTGRESQL, true);
    }



    /**
     * データベースタイプがSQLiteかどうか
     *
     * @return bool
     */
    public function isSQLite()
    {
        return in_array($this->type, self::TYPES_SQLITE, true);
    }



    /**
     * {@inheritDoc}
     */
    protected function configureKey(): string
    {
        return 'database';
    }



    /**
     * {@inheritDoc}
     */
    protected function configureDefaults(): array
    {
        return [];
    }



    /**
     * {@inheritDoc}
     */
    protected function configureRequires(): array
    {
        return [
            'type',
            'hostname',
            'database',
        ];
    }
}
