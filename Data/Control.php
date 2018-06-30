<?php

namespace Data;

use Data\Catalog\Pages;

class Control {
    const PAGE_SIZE = 10;
    protected $db, $obj_class;

    public function __construct($obj_class) {
        $this->db = dbConnect();
        $this->obj_class = $obj_class;
    }

    public function getByField($field, $producer, $page = 1) {
        $binds = array();
        $inst = new $this->obj_class();
        $tbl = $inst->getTable();
        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM $tbl ";
        if($producer) {
            $query .= "WHERE $field=? ";
            $binds[] = $producer;
        }
        $query .= "LIMIT " . ($page - 1) * self::PAGE_SIZE . "," . self::PAGE_SIZE;
        $stm = $this->db->prepare($query, $binds);
        $stm->execute($binds);
        $products = $stm->fetchAll(\PDO::FETCH_CLASS, $this->obj_class);
        $rows = $this->db->query('SELECT FOUND_ROWS();')->fetchColumn();
        $pages = max(ceil((float)$rows / self::PAGE_SIZE), 1);
        if($page > $pages) {
            exit('INVALID PAGE');
        }
        return new Pages($products, $pages, $page);
    }

    public function getAll() {
        $inst = new $this->obj_class();
        $tbl = $inst->getTable();
        $query = "SELECT * FROM $tbl";
        $stm = $this->db->query($query);
        $products = $stm->fetchAll(\PDO::FETCH_CLASS, $this->obj_class);
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
        $inst = new $this->obj_class();
        $fields = $inst::getFields();
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

    public function deleteBatch($identifier, $idField) {
        if(empty($identifier)) {
            return 0;
        }
        $idBind = ":" . $idField;
        $identifier = implode(',', $identifier);
        $query = "DELETE FROM products WHERE FIND_IN_SET($idField,$idBind)";
        $stm = $this->db->prepare($query);
        $stm->bindParam(':' . $idField, $identifier);
        $stm->execute();
        return $stm->rowCount();
    }

    public function updateBatch($rows, $idField) {
        $updated = 0;
        $now = time();
        $bindField = ":" . $idField;
        foreach ($rows as $row) {
            $values = $row->getUpdateQueryValues("");
            $query = "UPDATE products SET $values,updated=$now
                       WHERE $idField=$bindField; ";
            $binds = $row->getQueryBinds("");
            $stm = $this->db->prepare($query);
            $stm->execute($binds);
            $updated += $stm->rowCount();
        }
        return $updated;
    }
}