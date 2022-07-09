<?php

namespace Csr\Framework\Adapters\Doctrine;

class Drivers
{
    public const MSSQL = 'pdo_sqlsrv';
    public const MYSQL = 'pdo_mysql';
    public const POSTGRESQL = 'pdo_pgsql';
    public const SQLITE = 'pdo_sqlite';
}