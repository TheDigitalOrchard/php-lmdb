<?php
declare(strict_types=1);
namespace iggyvolz\lmdb;
use FFI;
class DatabaseHandle
{
    private static function newString(string $val): FFI\Cdata
    {
        $cdata = FFI::new("const char[".(strlen($val)+1)."]", false);
        FFI::memcpy($cdata, $val, strlen($val));
        return $cdata;
    }
    private int $handle;
    public function __construct(private Transaction $transaction, ?string $name = null, int $flags = 0)
    {
        $handle = LMDB::new("MDB_dbi");
        if(!is_null($name)) {
            $namePtr = FFI::new("const char[".(strlen($name)+1)."]");
            FFI::memcpy($namePtr, $name, strlen($name));
        } else {
            $namePtr = LMDB::cast("const char*", 0);
        }
        LMDB::assert(LMDB::mdb_dbi_open($transaction->cdata, $namePtr, $flags, FFI::addr($handle)));
        $this->handle = $handle->cdata;
    }
    public function put(string $key, string $value, int $flags = 0): void
    {
        $keyVal = LMDB::new("MDB_val");
        $keyVal->mv_size=strlen($key);
        $keyVal->mv_data=self::newString($key);
        $dataVal = LMDB::new("MDB_val");
        $dataVal->mv_size=strlen($value);
        $dataVal->mv_data=self::newString($value);
        LMDB::assert(LMDB::mdb_put($this->transaction->cdata, $this->handle, FFI::addr($keyVal), FFI::addr($dataVal), $flags));
    }
    public function get(string $key): ?string
    {
        $keyVal = LMDB::new("MDB_val");
        $keyVal->mv_size=strlen($key);
        $keyVal->mv_data=self::newString($key);
        $dataVal = LMDB::new("MDB_val");
        $res = LMDB::mdb_get($this->transaction->cdata, $this->handle, FFI::addr($keyVal), FFI::addr($dataVal));
        if($res === LMDB::NOTFOUND) {
            return null;
        }
        LMDB::assert($res);
        return FFI::string($dataVal->mv_data, $dataVal->mv_size);
    }
    public function all(): \Generator
    {
        $cursor = LMDB::new("MDB_cursor*");
        LMDB::assert(LMDB::mdb_cursor_open($this->transaction->cdata, $this->handle, FFI::addr($cursor)));
        // Handle MDB_NOTFOUND as exit condition
        $key = LMDB::new("MDB_val");
        $data = LMDB::new("MDB_val");
        $ret = LMDB::mdb_cursor_get($cursor, FFI::addr($key), FFI::addr($data), LMDB::FIRST);
        while($ret === 0) {
            yield FFI::string($key->mv_data, $key->mv_size) => FFI::string($data->mv_data, $data->mv_size);
            $ret = LMDB::mdb_cursor_get($cursor, FFI::addr($key), FFI::addr($data), LMDB::NEXT);
        }
        if($ret !== LMDB::NOTFOUND) {
            LMDB::assert($ret);
        }
    }
    public function __destruct()
    {
        // Do NOT clean up database handle - will be handled when closing transaction
        // LMDB::mdb_dbi_close(LMDB::mdb_txn_env($this->transaction->cdata), $this->handle);
    }
}