<?php

function removeDuplicate($data)  {
    $unique = [];
    $removed = [];

    foreach ($data as $item){
        if (!in_array($item, $unique)) {
            $unique[] = $item;
        }else{
            $removed[] = $item;
        }
    }

    $totalRemoved = array_sum($removed);
    return[
        'unique' => $unique,
        'removed' => $removed,
        'total_removed' => $totalRemoved
    ];
}

// contoh penggunaan
$data = [1,2,3,2,4,3,5];
$hasil = removeDuplicate($data);

print_r($hasil);

?>