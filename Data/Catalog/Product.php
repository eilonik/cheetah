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

    public function getQueryValues($bind) {

        $this->product_name = str_replace("\\", "\\\\", $this->product_name);
        $this->photo_url = str_replace("\\", "\\\\", $this->photo_url);
        $this->barcode = str_replace("\\", "\\\\", $this->barcode);
        $this->producer = str_replace("\\", "\\\\", $this->producer);

        $this->product_name = str_replace("'", "\'", $this->product_name);
        $this->photo_url = str_replace("'", "\'", $this->photo_url);
        $this->barcode = str_replace("'", "\'", $this->barcode);
        $this->producer = str_replace("'", "\'", $this->producer);

        return ":product_name_$bind,:photo_url_$bind,:barcode_$bind,:sku_$bind,:price_cents_$bind,:producer_$bind";
    }

    public function getQueryBinds($bind) {
        return array(
            "product_name_$bind" => $this->product_name,
            "photo_url_$bind" => $this->photo_url,
            "barcode_$bind" => $this->barcode,
            "sku_$bind" => $this->sku,
            "price_cents_$bind" => $this->price_cents,
            "producer_$bind" => $this->producer
        );
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