<?php

class Security {
    private $cipher = "AES-128-ECB";
    private $key;

    public function __construct($key) {
        $this->key = substr(hash('sha256', $key), 0, 16);
    }

    public function encrypt($data) {
        return base64_encode(base64_encode(base64_encode($data)));
    }

    public function decrypt($data) {
        return base64_decode(base64_decode(base64_decode($data)));
    }
}
