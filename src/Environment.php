<?php
declare(strict_types=1);
namespace iggyvolz\lmdb;
use FFI;
class Environment
{
    public FFI\CData $cdata;
    public function __construct(string $path, int $flags = 0, int $mode = 0755, int $numDatabases = 0)
    {
        $this->cdata = LMDB::new("MDB_env*");
        LMDB::assert(LMDB::mdb_env_create(FFI::addr($this->cdata)));
        if($numDatabases !== 0) {
            LMDB::assert(LMDB::mdb_env_set_maxdbs($this->cdata, $numDatabases));
        }
        LMDB::assert(LMDB::mdb_env_open($this->cdata, $path, $flags, $mode));
    }
    public function newTransaction(bool $readOnly = false): Transaction
    {
        $parent = FFI::cast("void*", null);
        return new Transaction($this, $parent, $readOnly ? 0x20000 : 0);
    }
    public function __destruct()
    {
        LMDB::mdb_env_close($this->cdata);
    }
}