<?php

function makeSymetric($text) {
    $reverse = '';

    for ($i=strlen($text) - 1; $i >= 0; $i--) { 
        $reverse .= $text[$i];
    }

    return $text . $reverse;
}

// COntoh Penggunaan
$kata = "Arya";

$hasil = makeSymetric($kata);
echo $hasil;

?>