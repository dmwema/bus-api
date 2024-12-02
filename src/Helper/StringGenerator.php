<?php
namespace App\Helper;
class StringGenerator{
    public function __construct(){}
    public function generate(int $length = 0):string{
        $bytes = random_bytes($length/2);
        return strtoupper(bin2hex($bytes));
    }
}