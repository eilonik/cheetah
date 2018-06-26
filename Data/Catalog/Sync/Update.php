<?php

define('IS_SERVER', true);
require_once '../../../Server.php';
$file = "catalog - cheetah.csv";
$row = 1;
$rows = array();
$duplicate_sku = array();
$no_sku = array();

//Reads the csv into appropriate arrays
if (($handle = fopen($file, "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ',','"')) !== false) {
        if($row != 1) {
            $prod = new \Data\Catalog\Product();
            $prod->setProperties($data);
            if($prod->sku){
                if(key_exists($prod->sku, $rows)) {
                    $duplicate_sku[$row] = $prod->sku;
                } else {
                    $rows[$prod->sku] = $prod;
                }
            } else {
                $no_sku[] = $row;
            }
        }
        $row++;
    }
    fclose($handle);

    $control = new \Data\Control();
    $current_products = $control->getAll();

    //Prepares new products to add to DB
    $new_products = array_diff_key($rows, $current_products);

    //Prepares a list of products to remove
    $products_to_remove = array_diff_key($current_products, $rows);
    $products_to_remove = array_keys($products_to_remove);

    //Prepares the products that need to be updated
    $products_to_update = array();
    $rows = array_diff_key($rows, $new_products);
    foreach ($rows as $key => $row) {
        $current_product = $current_products[$key];
        if(!$row->equals($current_product)) {
            $products_to_update_total++;
            $products_to_update[] = $row;
        }
    }

    $rows_inserted = $control->insertBatch($new_products);
    $rows_deleted = $control->deleteBatch($products_to_remove);
    $rows_updated = $control->updateBatch($products_to_update);

    $now = date('d/m/Y H:i:s', time());
    echo "Data Update $now:<br><br>";
    echo "-------------------------------------";
    echo "<br>";
    echo "New rows inserted: " . $rows_inserted . "<br>";
    echo "Rows deleted: " . $rows_deleted. "<br>";
    echo "Rows updated: " . $rows_updated. "<br>";
    echo "-------------------------------------";
    echo "<br>";
    echo "Invalid Products:<br><br>";
    echo "Products with no SKU:<br>";
    foreach ($no_sku as $line) {
        echo "  Line: " . $line . "<br>";
    }

    echo "<br>";
    echo "Products with Duplicate SKU:<br>";
    foreach ($duplicate_sku as $line => $sku) {
        echo "  Line: " . $line . "  - SKU: " . $sku . "<br>";
    }

}