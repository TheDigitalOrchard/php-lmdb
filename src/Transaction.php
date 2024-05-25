<?php
declare(strict_types=1);

namespace iggyvolz\lmdb;

use FFI;

class Transaction
{
    private bool $live = true;
    public FFI\CData $cdata;

    public function __construct(private Environment $environment, FFI\CData $parent, int $flags)
    {
        $this->cdata = LMDB::new('MDB_txn*');
        LMDB::assert(LMDB::mdb_txn_begin($environment->cdata, $parent, $flags, FFI::addr($this->cdata)));
    }

    private function assertLive(): void
    {
        if (!$this->live) {
            throw new \RuntimeException("Illegal access on dead transaction");
        }
    }

    public function getHandle(?string $name = null, int $flags = 0): DatabaseHandle
    {
        $this->assertLive();
        return new DatabaseHandle($this, $name, $flags);
    }

    public function abort(): void
    {
        $this->assertLive();
        LMDB::mdb_txn_abort($this->cdata);
        $this->live = false;
    }

    public function commit(): void
    {
        $this->assertLive();
        LMDB::mdb_txn_commit($this->cdata);
        $this->live = false;
    }

    public function __destruct()
    {
        if ($this->live) {
            $this->abort();
        }
    }
}
