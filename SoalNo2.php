<?php

    function removeDuplicates($array_number) {
        $unique_numbers = [];
        foreach ($array_number as $number) {
            if (!in_array($number, $unique_numbers)) {
                $unique_numbers[] = $number;
            }
        }
        return $unique_numbers;
    }

    // Contoh penggunaan
    $array_number = [1,1,4,4,4,5,5,6,8,9,10,10,12,13,13,17];
    $result = removeDuplicates($array_number);
    echo implode(",", $result);

?>