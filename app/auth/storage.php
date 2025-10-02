<?php

// Хранилище данных
class Storage {
  private $dataDir;

  public function __construct() {
    $this->dataDir = Config::DATA_DIR;
    if (!is_dir($this->dataDir)) {
      mkdir($this->dataDir,0755, true);
    }
  }

  public function read($file) {
    $path = $this->dataDir . $file;

    if (!file_exists($path)) {
      return [];
    }

    $content = file_get_contents($path);

    return json_decode($content, true) ?? [];
  }

  public function write($file, $data) {
    $path = $this->dataDir . $file;
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
  }

  public function generateID() {
    return uniqid(mt_rand(), true);
  }
}