<?php
use iggyvolz\lmdb\Environment;
require_once __DIR__ . "/../vendor/autoload.php";
$db = $_GET["db"] ?? null;
if(is_null($db)) {
    header("Location: index.php");
    die();
}
$transaction = (new Environment(__DIR__ . "/db", numDatabases: 1))->newTransaction(readOnly: true);
$handle = $transaction->getHandle($db);
?>
<html lang="en">
<head>
<style>
table,th,td{border: 1px solid black;}
</style>
<title>Database Access</title>
</head>
<body>
<table>
    <tr>
    <th>Key</th>
    <th>Value</th>
    </tr>
<?php foreach($handle->all() as $key => $value): ?>
    <tr><td><?= htmlspecialchars($key) ?></td><td><?= htmlspecialchars($value) ?></td></tr>
<?php endforeach; ?>
</table>
</body>
</html>
