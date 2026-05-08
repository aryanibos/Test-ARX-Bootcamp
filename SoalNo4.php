<?php

    function isSymmetric($str) {
        $length = strlen($str);
        for ($i = 0; $i < $length / 2; $i++) {
            if ($str[$i] !== $str[$length - 1 - $i]) {
                return false;
            }
        }
        return true;
    }

    // Contoh penggunaan
    $str1 = "madam";
    $str2 = "gozaru";
    $result1 = isSymmetric($str1) ? "TRUE" : "FALSE";
    $result2 = isSymmetric($str2) ? "TRUE" : "FALSE";
    echo "Input: $str1\nOutput: $result1\n";
    echo "Input: $str2\nOutput: $result2\n";

?>