<?php

use iggyvolz\lmdb\LMDB;
use iggyvolz\lmdb\Environment;
require_once __DIR__ . "/../vendor/autoload.php";
$transaction = (new Environment(__DIR__ . "/db", numDatabases: 1))->newTransaction();
$handle = $transaction->getHandle("foox", flags: LMDB::CREATE);
$handle->put("foo", "bar");
$handle->put("foop", "bar2");
$transaction->commit();