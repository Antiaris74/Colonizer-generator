<?php
require_once 'vendor/autoload.php';

use Colonizer\Field;

/*
0   q w e
1  q w e r
2 q w e r t
3  q w e r
4   q w e

q w e
q w e r
q w e r t
q w e r
q w e

r - rock - 3
w - wood - 4
с - clay - 3
wh - wheat- 4
wo - wool - 4
d - desert - 1 - center
 */
global $argv;

if (array_key_exists(1, $argv) && in_array($argv[1], \Colonizer\Strategy::STRATEGIES, true)) {
    $field = new Field($argv[1]);
    if ($field->fill()) {
        $field->printFill();
    } else {
        echo 'No solution';
    }
} else {
    echo 'No strategy';
}