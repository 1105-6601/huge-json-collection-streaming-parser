<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use HugeJsonCollectionStreamingParser\Parser;

$filePath = dirname(__FILE__) . '/../tests/data/test.json';

$parser = new Parser($filePath);

$cnt   = 0;
$start = time();

while ($parser->next()) {
    $item = $parser->current();
    $cnt++;
    echo sprintf("Count: %s, Memory: %s bytes\n", $cnt, number_format(memory_get_usage()));
}

echo sprintf("Finish. Peak memory usage: %s bytes.\n", number_format(memory_get_peak_usage()));
echo sprintf("Time: %s seconds.\n", number_format(time() - $start));
exit;
