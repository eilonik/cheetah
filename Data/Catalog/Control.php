<?php

namespace Data;

use Data\Catalog\Pages;
use Data\Catalog\Product;

class Control {
    const PAGE_SIZE = 10;
    protected $db;

    public function __construct() {
        $this->db = dbConnect();
    }

    public function getByProducer($producer, $page = 1) {
        $binds = array();
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM products ";
        if($producer) {
            $query .= "WHERE producer=? ";
            $binds[] = $producer;
        }
        $query .= "LIMIT " . ($page - 1) * self::PAGE_SIZE . "," . self::PAGE_SIZE;
        $stm = $this->db->prepare($query, $binds);
        $stm->execute($binds);
        $products = $stm->fetchAll(\PDO::FETCH_CLASS, "\Data\Catalog\Product");
        $rows = $this->db->query('SELECT FOUND_ROWS();')->fetchColumn();
        $pages = max(ceil((float)$rows / self::PAGE_SIZE), 1);
        if($page > $pages) {
            exit('INVALID PAGE');
        }
        return new Pages($products, $pages, $page);
    }

    public function getAll() {
        $query = "SELECT * FROM products";
        $stm = $this->db->query($query);
        $products = $stm->fetchAll(\PDO::FETCH_CLASS, "\Data\Catalog\Product");
        $response = array();
        foreach ($products as $product) {
            $response[$product->sku] = $product;
        }
        return $response;
    }

    public function insertBatch($rows) {
        if(empty($rows)) {
            return 0;
        }
        $now = time();
        $inserted = 0;
        $binds = array();
        $fields = Product::getFields();
        $query = 'INSERT INTO products (' . $fields . ',added) VALUES ';
        $values = "";
        $i = 1;
        foreach ($rows as $row) {
            $query_string = $row->getQueryValues($i);
            $values .= '(' . $query_string . ",$now" . '),';
            $binds = array_merge($binds, $row->getQueryBinds($i));
            if($i % 1000 == 0) {
                $values = substr($values, 0, strlen($values) - 1);
                $stm = $this->db->prepare($query . $values);
                $stm->execute($binds);
                $inserted += $stm->rowCount();
                $values = "";
                $binds = array();
            }
            $i++;
        }
        if(!empty($values)) {
            $values = substr($values, 0, strlen($values) - 1);
            $stm = $this->db->prepare($query . $values);
            $stm->execute($binds);
            $inserted += $stm->rowCount();
        }
        return $inserted;
    }

    public function deleteBatch($skus) {
        if(empty($skus)) {
            return 0;
        }
        $skus = implode(',', $skus);
        $query = "DELETE FROM products WHERE FIND_IN_SET(sku,:skus)";
        $stm = $this->db->prepare($query);
        $stm->bindParam(':skus', $skus);
        $stm->execute();
        return $stm->rowCount();
    }

    public function updateBatch($rows) {
        $updated = 0;
        $now = time();
        foreach ($rows as $row) {
            $values = $row->getUpdateQueryValues("");
            $query = "UPDATE products SET $values,updated=$now
                       WHERE sku=:sku; ";
            $binds = $row->getQueryBinds("");
            $stm = $this->db->prepare($query);
            $stm->execute($binds);
            $updated += $stm->rowCount();
        }
        return $updated;
    }
}