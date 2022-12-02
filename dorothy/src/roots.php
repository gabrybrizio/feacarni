<?php

class Roots extends Obj {

  public $index;

  public function __construct($index) {
    $this->index = $index;
  }

  public function public() {
    return isset($this->public) ? $this->public : $this->index . DS . 'app-public';
  }

  public function dorothy() {
    return isset($this->dorothy) ? $this->dorothy : $this->index . DS . 'dorothy';
  }

  public function admin() {
    return isset($this->admin) ? $this->admin : $this->index . DS . 'app-admin';
  }

  public function config() {
    return isset($this->config) ? $this->config : $this->index . DS . 'config';
  }

  public function languages() {
    return isset($this->languages) ? $this->languages : $this->config() . DS . 'languages';
  }
}