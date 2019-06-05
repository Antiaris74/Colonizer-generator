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
p - wheat- 4
s - wool - 4
d - desert - 1 - center
 */
$field = new Field('standard');
$field->fill();
$fill = $field->getFill();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<style>
    .container {
        margin-top: 100px;
        width: 1000px;
        line-height: 1.3;
    }

    ol.even {
        position: relative;
        left: 5.45455em;
    }

    ol.odd {
        position: relative;
        margin-top: -6.5%;
        margin-bottom: -6.5%;
    }

    .hex {
        position: relative;
        margin: 1em auto;
        width: 6em;
        height: 10.2em;
        border-radius: 1em/.5em;
        background: #ccc;
        transform: rotate(-90deg);
        display: inline-block;
        margin-right: 4.61538em;
        transition: all 150ms ease-in-out;
    }

    .hex:before, .hex:after {
        position: absolute;
        width: inherit;
        height: inherit;
        border-radius: inherit;
        background: inherit;
        content: '';
    }

    .hex:before {
        transform: rotate(60deg);
    }

    .hex:after {
        transform: rotate(-60deg);
    }

    .hex:hover {
        background: #F58787;
        cursor: pointer;
    }

    .invisible {
        visibility: hidden;
    }

    .hex-char-p {
        background: #c7cc00;
    }

    .hex-char-r {
        background: #ccc;
    }

    .hex-char-w {
        background: #00660d;
    }

    .hex-char-c {
        background: #cc6408;
    }

    .hex-char-s {
        background: #00cc3b;
    }

    .hex-char-d {
        background: #8c6b00;
    }
</style>
<body>
<div class="container">
    <?php
    $rowCount = 2;
    foreach ($fill as $row) :?>
    <ol class="<?=$rowCount%2 === 0?'odd':'even'?>">
        <?php if (\count($row) === 3) :?>
        <li class='hex invisible'></li>
        <?php endif;?>
        <?php foreach ($row as $resource) :?>
        <li class='hex hex-char-<?=$resource?>'></li>
        <?php endforeach;?>
        <?php if (\count($row) === 3) :?>
        <li class='hex invisible'></li>
        <?php endif;?>
    </ol>
    <?php
    $rowCount++;
    endforeach;?>
</div>
</body>
</html>