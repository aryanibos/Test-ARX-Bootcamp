<?php

function findMedian($bil) {
    $n = count($bil);

    // melakukan sorting
    for ($i=0; $i < $n - 1; $i++) { 
        for ($a=0; $a < $n - $i - 1; $a++) { 
           if ($bil[$a] > $bil[$a + 1]) {
            $temp = $bil[$a];
            $bil[$a] = $bil[$a + 1];
            $bil[$a + 1] = $temp;
           }
        }
    }

    $middle = floor($n / 2);

    // jika datanya ganjil
    if ($n % 2 == 1) {
        return $bil [$middle];
    }
    // kalau genap
    return ($bil[$middle -1] + $bil[$middle]) / 2;


}

// contoh penggunaan
$bil = [7,1,3,4,2];
echo findMedian($bil);

?>