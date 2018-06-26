<?php

namespace Data\Catalog;

class Product {
    public $product_name, $photo_url, $barcode, $sku, $price_cents, $producer;
    protected $added, $updated, $id;

    public function getPretty() {
        $obj = json_decode(json_encode($this));
        $obj->price_cents = (float)$obj->price_cents / 100;
        return $obj;
    }

    public function getQueryValues() {
        $this->product_name = str_replace("\\", "\\\\", $this->product_name);
        $this->photo_url = str_replace("\\", "\\\\", $this->photo_url);
        $this->barcode = str_replace("\\", "\\\\", $this->barcode);
        $this->producer = str_replace("\\", "\\\\", $this->producer);

        $this->product_name = str_replace("'", "\'", $this->product_name);
        $this->photo_url = str_replace("'", "\'", $this->photo_url);
        $this->barcode = str_replace("'", "\'", $this->barcode);
        $this->producer = str_replace("'", "\'", $this->producer);

        $this->product_name = mysql_real_escape_string($this->product_name);
        $this->photo_url = mysql_real_escape_string($this->photo_url);
        $this->barcode = mysql_real_escape_string($this->barcode);
        $this->producer = mysql_real_escape_string($this->producer);
        $this->sku = mysql_real_escape_string($this->sku);
        $this->price_cents = mysql_real_escape_string($this->price_cents);

        return "'$this->product_name','$this->photo_url','$this->barcode','$this->sku','$this->price_cents','$this->producer'";
    }

    public function equals($other) {
        return $this->product_name == $other->product_name &&
                $this->photo_url == $other->photo_url &&
                $this->barcode == $other->barcode &&
                $this->sku == $other->sku &&
                $this->price_cents == $other->price_cents &&
                $this->producer == $other->producer;
    }

    public function setProperties($data) {
        $this->product_name = empty($data[0]) ? "" : $data[0];
        $this->photo_url = empty($data[1]) ? "" : $data[1];
        $this->barcode = empty($data[2]) ? "" : $data[2];
        $this->sku = empty($data[3]) ? null : $data[3];
        $this->price_cents = empty($data[4]) ? 0 : $data[4];
        $this->producer = empty($data[5]) ? "" : $data[5];
    }

}