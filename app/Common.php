<?php

use PhpParser\Node\Stmt\TryCatch;

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter4.github.io/CodeIgniter4/
 */

function handleDivision($dividend, $divisor) {
    try {
        return $dividend / $divisor;
    } catch (\Throwable $th) {
        return 0;
    }
}


/**
 * Fungsi untuk sum array mulai dari $from ke $to
 * Susunan array adalah array[$from/$to][$prop]
 * Perhatian, $from <= $to
 */
function sumOfArray($arr, $from, $to, $prop) {
    try {
        if ($from >= $to) {
            return $arr[$to][$prop];
        }
        return $arr[$from][$prop] + sumOfArray($arr, $from + 1, $to, $prop);
    } catch (\Throwable $th) {
        return 0;
    }
    
}