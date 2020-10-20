<?php

include_once '../vendor/autoload.php';

use HugeJsonCollectionStreamingParser\Parser;

$parser = new Parser('./debug.json');

while ($parser->next()) {
    try {
        $item = $parser->current();
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }
}
