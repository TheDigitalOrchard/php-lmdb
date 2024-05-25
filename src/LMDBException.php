<?php
declare(strict_types=1);

namespace iggyvolz\lmdb;

use Exception;
use FFI;

class LMDBException extends Exception
{
    public function __construct(int $error)
    {
        parent::__construct(FFI::string(LMDB::mdb_strerror($error)), $error);
    }
}
