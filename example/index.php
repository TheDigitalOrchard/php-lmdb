<?php
use iggyvolz\lmdb\Environment;
require_once __DIR__ . "/../vendor/autoload.php";
$transaction = (new Environment(__DIR__ . "/db", numDatabases: 0))->newTransaction(readOnly: true);
$handle = $transaction->getHandle();
?>
<html lang="en">
<head>
    <style>
        table,th,td{border: 1px solid black;}
    </style>
    <title>List Databases</title>
</head>
<body>
<ul>
<?php foreach($handle->all() as $key => $value): ?>
    <li><a href="db.php?db=<?= urlencode($key) ?>"><?= htmlspecialchars($key) ?></a></li>
<?php endforeach; ?>
</ul>
</body>
</html>
