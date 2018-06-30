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

    public static function getFields(){
        return "product_name,photo_url,barcode,sku,price_cents,producer";
    }

    public function getQueryValues($bind) {
        return ":product_name$bind,:photo_url$bind,:barcode$bind,:sku$bind,:price_cents$bind,:producer$bind";
    }

    public function getUpdateQueryValues($bind) {
        return "product_name=:product_name$bind,photo_url=:photo_url$bind,barcode=:barcode$bind,sku=:sku$bind,price_cents=:price_cents$bind,producer=:producer$bind";
    }

    public function getQueryBinds($bind) {
        return array(
            "product_name$bind" => $this->product_name,
            "photo_url$bind" => $this->photo_url,
            "barcode$bind" => $this->barcode,
            "sku$bind" => $this->sku,
            "price_cents$bind" => $this->price_cents,
            "producer$bind" => $this->producer
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