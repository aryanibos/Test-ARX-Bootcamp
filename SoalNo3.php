<?php

    function customSort($array_number) {
        $count = count($array_number);
        for ($i = 0; $i < $count - 1; $i++) {
            for ($j = 0; $j < $count - $i - 1; $j++) {
                if ($array_number[$j] > $array_number[$j + 1]) {
                    $temp = $array_number[$j];
                    $array_number[$j] = $array_number[$j + 1];
                    $array_number[$j + 1] = $temp;
                }
            }
        }

        $result = [];
        $left = 0;
        $right = $count - 1;
        $group = 1;

        while ($left <= $right) {
            if ($group % 2 == 1) {
                for ($i = 0; $i < 5 && $left <= $right; $i++) {
                    $result[] = $array_number[$left];
                    $left++;
                }
            } else {
                for ($i = 0; $i < 5 && $left <= $right; $i++) {
                    $result[] = $array_number[$right];
                    $right--;
                }
            }

            $group++;
        }

        return $result;
    }

    // Contoh penggunaan
    $array_number = [2,5,1,12,-5,4,-1,3,-3,20,8,7,-2,6,9];
    $result = customSort($array_number);
    echo implode(",", $result);

?>
