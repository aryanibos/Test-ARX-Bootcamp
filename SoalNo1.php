<?php

    // Soal No 1
    function findThreeNumbers($array_number) {
        $count = count($array_number);
        for ($i = 0; $i < $count - 2; $i++) {
            for ($i = 0; $i < $count - 1; $i++) {
                for ($j = 0; $j < $count - $i - 1; $j++) {
                    if ($array_number[$j] > $array_number[$j + 1]) {
                        $temp = $array_number[$j];
                        $array_number[$j] = $array_number[$j + 1];
                        $array_number[$j + 1] = $temp;
                    }
                }
            }
        }
        return "Not Found";
    }

    // Contoh penggunaan
    $array_number = [2, 1, 5, 7, 4, -8, -3, -1];
    $result = findThreeNumbers($array_number);
    if (is_array($result)) {
        echo implode(",", $result);
    } else {
        echo $result;
    }


?>