<?php

class Brick {

  public static $bricks = array();

  public $tag    = null;
  public $attr   = array();
  public $html   = null;
  public $events = array();

  public function __construct($tag, $html = false, $attr = array()) {

    if(is_array($html)) {
      $attr = $html;
      $html = false;
    }

    $this->tag($tag);
    $this->html($html);
    $this->attr($attr);

  }

  public function __set($attr, $value) {
    $this->attr($attr, $value);
  }

  public function on($event, $callback) {
    if(!isset($this->events[$event])) $this->events[$event] = array();
    $this->events[$event][] = $callback;
    return $this;
  }

  public function trigger($event, $args = array()) {
    if(isset($this->events[$event])) {
      array_unshift($args, $this);
      foreach($this->events[$event] as $e) {
        call_user_func_array($e, $args);
      }
    }
  }

  public function tag($tag = null) {
    if(is_null($tag)) return $this->tag;
    $this->tag = $tag;
    return $this;
  }

  public function attr($key = null, $value = null) {
    if(is_null($key)) {
      return $this->attr;
    } else if(is_array($key)) {
      foreach($key as $k => $v) {
        $this->attr($k, $v);
      }
      return $this;
    } else if(is_null($value)) {
      return a::get($this->attr, $key);
    } else if($key == 'class') {
      $this->addClass($value);
      return $this;
    } else {
      $this->attr[$key] = $value;
      return $this;
    }
  }

  public function data($key = null, $value = null) {
    if(is_null($key)) {
      $data = array();
      foreach($this->attr as $key => $val) {
        if(str::startsWith($key, 'data-')) {
          $data[$key] = $val;
        }
      }
      return $data;
    } else if(is_array($key)) {
      foreach($key as $k => $v) {
        $this->data($k, $v);
      }
      return $this;
    } else if(is_null($value)) {
      return a::get($this->attr, 'data-' . $key);
    } else {
      $this->attr['data-' . $key] = $value;
      return $this;
    }
  }

  public function removeAttr($key) {
    unset($this->attr[$key]);
    return $this;
  }

  public function classNames() {

    if(!isset($this->attr['class'])) {
      $this->attr['class'] = array();
    } else if(is_string($this->attr['class'])) {
      $raw = $this->attr['class'];
      $this->attr['class'] = array();
      $this->addClass($raw);
    }

    return $this->attr['class'];

  }

  public function val($value = null) {
    return $this->attr('value', $value);
  }

  public function addClass($class) {

    $classNames = $this->classNames();
    $classIndex = array_map('strtolower', $classNames);

    foreach(str::split($class, ' ') as $c) {
      if(!in_array(strtolower($c), $classIndex)) {
        $classNames[] = $c;
      }
    }

    $this->attr['class'] = $classNames;

    return $this;

  }

  public function removeClass($class) {

    $classNames = $this->classNames();

    foreach(str::split($class, ' ') as $c) {
      $classNames = array_filter($classNames, function($e) use($c) {
        return (strtolower($e) !== strtolower($c));
      });
    }

    $this->attr['class'] = $classNames;

    return $this;

  }

  public function replaceClass($classA, $classB) {
    return $this->removeClass($classA)->addClass($classB);
  }

  public function text($text = null) {
    if(is_null($text)) return trim(strip_tags($this->html));
    $this->html = html($text, false);
    return $this;
  }

  public function html($html = null) {
    if(is_null($html)) {
      return $this->html = $this->isVoid() ? null : $this->html;
    }
    $this->html = $html;
    return $this;
  }

  public function prepend($html) {
    if(is_callable($html)) $html = $html();
    $this->html = $html . $this->html;
    return $this;
  }

  public function append($html) {
    if(is_callable($html)) $html = $html();
    $this->html = $this->html . $html;
    return $this;
  }

  public function isVoid() {
    return html::isVoid($this->tag());
  }

  public function toString() {
    $this->attr['class'] = implode(' ', $this->classNames());
    return html::tag($this->tag(), $this->html(), $this->attr());
  }

  public function __toString() {
    try {
      return $this->toString();
    } catch(Exception $e) {
      return 'Error: ' . $e->getMessage();
    }
  }

  public static function make($id, $callback) {
    static::$bricks[$id] = $callback;
  }

  public static function get($id) {
    if(!isset(static::$bricks[$id])) return false;
    $args = array_slice(func_get_args(), 1);
    return call_user_func_array(static::$bricks[$id], $args);
  }

  public static function brick($tag, $html = false, $attr = array()) {
    return new Brick($tag, $html, $attr);
  }

  public static function br(){
    $br = static::brick('break');
    echo $br;
  }

  public static function info($text){
    $h = static::brick('h4', $text);

    $info = static::brick('field');
    $info->addClass('full');
    $info->append($h);

    echo $info;
  }

  public static function btn(){
    $button = static::brick('button');
    $button->attr('type', 'button'); /* Per evitare submit */
    $button->addClass('btn');

    $args = func_get_args();
    $hasText = false;
    $hasIcon = false;

    // Ciclo gli argomenti
    foreach($args as $arg){

        if(!str::contains($arg, ':')){
            $k = $arg;
        }else{
            $k = str::before($arg, ':');
        }

        $v = str::after($arg, ':');

        switch ($k) {
            case "id":
                $button->attr('id', $v);
                break;
            case "icon":
                $button->append(icon::get($v));
                $hasIcon = true;
                break;
            case "text":
                $button->append($v);
                $hasText = true;
                break;
            case "class":
                $button->addClass($v);
                break;
            case "title":
                $button->attr('title', $v);
                break;
            case "name":
                $button->attr('name', $v);
                break;
            case "type":
                $button->attr('type', $v);
                break;
            case "value":
                $button->attr('value', $v);
                break;
            case "click":
                $button->attr('onclick', $v);
                break;
        }
        if($k == "formnovalidate"){
            $button->attr('formnovalidate', 'formnovalidate');
        }
    }

    if(!$hasText){
        $button->addClass('only--icon');
    }

    if(!$hasIcon){
        $button->addClass('only--text');
    }

    return $button;
  }

  /**
   * Genera un campo per l'area riservata
   * Può accettare un unico array: field(['type' => 'label', 'class' => 'my-class', 'required']);
   * oppure più parametri dove il primo è il type: field('label', 'class:my-class', 'required');
   * Il campo "choices" può esser un array chiave-valore nel primo caso e una stringa con valori separati da virgola nel secondo.
   *
   * @return void
   */
  public static function field(){

    // Dati a db prima del salvataggio
    global $el;

    // Dati che arrivano in post
    global $data;

    $args = func_get_args();

    $opt = array(
        'label' => '',
        'id' => '',
        'name' => '',
        'value' => '',
        'required' => '',
        'default' => '',
        'class' => '',
        'autofocus' => '',
        'placeholder' => '',
        'choices' => '',
        'relation-modulo' => '',
        'relation-display' => '',
        'files-source' => '',
        'oncheck' => '',
        'onchoice' => '',
        'validation-exact-length' => '',
        'validation-max-length' => '',
        'validation-file-type' => ''
    );

    // se il parametro è uno ed è un array vuol dire che pesco gli argomenti dall'array chiave-valore
    if(count($args) === 1 && is_array($args[0])) {
      $opt = a::merge($opt, $args[0]);
      $type = $opt['type'];

      // se il valore è vuoto metto sempre true. Es. "required"
      foreach($opt as $key => $val) {
         if ($val == '') {
           $val = true;
         }
      }
    } else {
      $type = array_shift($args);

      // Ciclo gli argomenti e sovrascrivo le $opt di defaults
      foreach($args as $arg){

        if(!str::contains($arg, ':')){
            $k = $arg;
            $v = 'true';
        }else{
            $k = str::before($arg, ':');
            $v = str::after($arg, ':');
        }
        $opt[$k] = $v;
      }
    }

    // Se il name non è definito uso lo slug della label
    if($opt['name'] == '') $opt['name'] = str::slug($opt['label']);

    // Se l'id non è definito uso il name
    if($opt['id'] == '') $opt['id'] =  $opt['name'];

    // Se il value non è definito lo definisco recuperando il campo name nel $data
    // In pratica se non va bene lo slug della label (o la label non c'è), occorre definire il name
    if($opt['value'] == '') $opt['value'] = a::get($data, $opt['name']);

    // Creo il <field> contenitore
    $field = static::brick('field');
    $field->attr('type', $type);
    $field->addClass($opt['class']);
    $error = false;

    $label = static::brick('label');
    if($opt['required'] == ''){
        $label->html($opt['label'] . ':');
    }else{
        $label->html($opt['label'] . ':' . ' <small>(obbligatorio)</small>');
    }

    $input = static::brick('input');
    $input->attr('id', $opt['id']);
    $input->attr('name', $opt['name']);
    $input->attr('value', $opt['value']);

    // Default value
    if($opt['default'] != ''){
        $input->attr('data-default', $opt['default']); // Serve per i repeater

        if($opt['value'] == ''){
            $opt['value'] = $opt['default'];
            $input->attr('value', $opt['default']);
        }
    }

    switch ($type) {
        case "text":
        case "number":
        case "euro":
        case "telephone":
        case "email":
        case "url":
        case "hour":

            $input->attr('type', 'text');
            if($opt['autofocus'] != '') $input->attr('autofocus', 'autofocus');
            if($opt['placeholder'] != '') $input->attr('placeholder', $opt['placeholder']);

            // Validation
            if($opt['value'] == ''){
                if($opt['required'] != '') $error = true;
            }else{
                switch ($type) {
                    case "text":
                        if(v::match($opt['value'], '/[<>]/i')) $error = true;
                        break;
                    case "number":
                        if(!v::num($opt['value'])) $error = true;
                        break;
                    case "euro":
                        if(!v::match($opt['value'], '/^([0-9,.])+$/i')) $error = true;
                        break;
                    case "telephone":
                        if(!v::match($opt['value'], '/^([0-9 +.])+$/i')) $error = true;
                        break;
                    case "email":
                        if(!v::email($opt['value'])) $error = true;
                        break;
                    case "url":
                        if(!v::url($opt['value'])) $error = true;
                        break;
                    case "hour":
                        if(!v::match($opt['value'], '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/')) $error = true;
                        break;
                }
            }

            // Validation exact length
            if($opt['validation-exact-length'] != '' && str::length($opt['value']) != $opt['validation-exact-length']) $error = true;

            // Validation max length
            if($opt['validation-max-length'] != '' && str::length($opt['value']) > $opt['validation-max-length']) $error = true;

            // Html
            $field->append($label);
            $field->append($input);

            break;
        case "hidden":

            $input->attr('type', 'hidden');

            // Validation
            if($opt['value'] == ''){
                if($opt['required'] != '') $error = true;
            }

            // Html
            $field->append($input);

            break;
        case "attachments":
        case "readonly-attachments":

            $input->attr('type', 'hidden');

            // Validation
            if($opt['value'] == ''){
                if($opt['required'] != '') $error = true;
            }

            // Html
            $field->append($input);
            $inputId = $input->attr('id') . '_upload_files';

            if($type == "attachments"){
                $validationFileType = $opt['validation-file-type'];

                $field->append('<input type="file" name="file[]" id="' . $inputId . '" class="inputfile" multiple="multiple" data-validation-file-type="' . $validationFileType . '" onchange="FILE.upload(this)" />');
                $field->append('<label for="' . $inputId . '" class="btn neutral" onclick="FIELD.focus(this)">'. icon::get("paperclip") . ' CARICA FILE</label>');
            }
            $field->append('<div class="attachments-files full"></div>');

            break;
        case "readonly":
        case "readonly-richtext":

            $readonly = static::brick('div');
            $readonly->addClass('read-only');

            $value = $opt['value'];

            // Se il valore non è settato recupero quello a db
            if(!isset($data[$opt['name']] )){
                $data[$opt['name']] = a::get($el, $opt['name']);
            }

            if($value == ''){
                // Il value non è passato lo recupero da db
                $value = a::get($el, $opt['name']);
            }

            // Se si tratta di un GUID ed è presente il tag "modulo" si tratta di una relazione devo ricavarmi il dato
            if(util::isGUID($value) && $opt['relation-modulo'] != ''){

                $el_related = module::dataOf($opt['relation-modulo'], $value);

                $display = array();
                $displays = str::split($opt['relation-display']);

                // Creo array di ogni valore o inserisco la stringa se presente #stringa# o formatto il prezzo £numero£
                foreach($displays as $d){
                    if(str::contains($d,"#")){
                        $display[] = str::replace($d, '#', '');
                    }else if(str::contains($d,"£")){
                        $d =  str::replace($d, '£', '');
                        $display[] = util::euro(a::get($el_related, $d));
                    }else{
                        $display[] = a::get($el_related, $d);
                    }
                }

                $value = join(" ", $display);
            }

            $readonly->html($value);

            // Html
            $field->append($label);
            $field->append($readonly);

            break;
        case "password":

            $input->attr('type', 'password');
            $input->attr('autocomplete', 'off');
            $input->attr('style','padding-right:32px');

            // Validation
            if($opt['value'] == '' && $opt['required'] != '' && a::get($el, $opt['name']) == '') $error = true;

            // Non faccio mai visualizzare la password
            $input->attr('value', '');

            // Html
            $field->append($label);
            $field->append($input);

            $arrow =  brick::btn('icon:eye', 'class:side side--ghost', 'click:PASSWORD.switch(this)');
            $field->append($arrow);

            break;
        case "check":
        case "readonly-check":

            $label->html($opt['label']);

            $input->attr('type', 'hidden');

            // Validation
            if($opt['value'] != '' && !in_array($opt['value'], array("1","0"))) $error = true;

            // Html
            $check = static::brick('check');
            if($opt['value']  == '1'){
                $check->append(icon::get('check-square', ''));
            }else{
                $input->attr('value', '0');
                $check->append(icon::get('square', ''));
            }

            $check->append($label);

            if($type == "check"){
                $check->attr('onclick','CHECK.toggle(this);' . $opt['oncheck']);
                $check->append($input);
            }

            $field->addClass('nolabel');
            $field->append($check);

            break;
        case "date":

            $input->attr('type', 'text');
            $input->addClass('btn--side');
            if($opt['autofocus'] != '') $input->attr('autofocus', 'autofocus');
            if($opt['placeholder'] != ''){
                $input->attr('placeholder', $opt['placeholder']);
            }else{
                $input->attr('placeholder', "Es. gg/mm/aaaa");
            }

            // Validation
            if($opt['value'] == '' && $opt['required'] != '') $error = true;
            if($opt['value'] != '' && !v::match($opt['value'], '/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/')) $error = true;

            // Html
            $field->append($label);
            $field->append($input);

            $reset =  brick::btn("icon:x", "click:INPUT.reset(this)", "class:reset");
            $field->append($reset);

            $arrow =  brick::btn('icon:calendar', 'class:side', 'click:CALENDAR.display(this)');
            $field->append($arrow);

            break;
        case "choice":
        case "countries":
            // dato che le choice possono avere chiave-valore differenti, salvo la chiave nel campo hidden
            $input->attr('type', 'hidden');

            // l'input type text (usato come label) dove mostro il valore corrispondente alla chiave del campo hidden
            $trueInput = static::brick('input');
            $trueInput->attr('type', 'text');
            $trueInput->attr('readonly', true);
            $trueInput->attr('onclick', 'CHOICE.toggle(event)');
            $trueInput->addClass('btn--side');

            if ($type === 'countries') {
              // se è un array di tipo countries pesco i valori da db
              $opt['choices'] = util::countries();
            }

            // Choices
            $arr = $opt['choices'];
            if(is_array($arr)) {
              // se è già un array lo prendo così com'è
              $choices = $arr;
            } else {
              // se non è un array splitto la stringa con key e value identici
              $arr = str::split($arr);
              $choices = array_combine($arr, $arr) ;
            };

            if (array_key_exists($opt['value'], $choices)) {
              $trueInput->attr('value', $choices[$opt['value']]);
            }

            // Validation
            if($opt['value'] == '' && $opt['required'] != '') $error = true;
            if($opt['value'] != '' && !array_key_exists($opt['value'], $choices)) $error = true;

            // Html
            $choices__box = static::brick('choices');

            foreach($choices as $key => $value) {
                $a = static::brick('choice', "<choice-value val=\"$key\">$value</choice-value>");
                $a->attr('onclick', 'CHOICE.set(this);' . $opt['onchoice']);
                $choices__box->append($a);
            }

            $field->append($label);

            $field->append($input);
            $field->append($trueInput);

            $reset =  brick::btn("icon:x", "click:INPUT.reset(this)", "class:reset");
            $field->append($reset);

            $arrow =  brick::btn("icon:chevron-down", "class:side", "click:CHOICE.toggle(event)");
            $field->append($arrow);
            $field->append($choices__box);

            break;
        case "image":

            $input->attr('type', 'text');
            $input->attr('readonly', true);
            $input->attr('onclick', 'CHOICE.toggle(event)');
            $input->addClass('btn--side');

            // Validation
            // Controllo che il valore corrisponda a uno dei nomi delle immagini
            $el__images = module::imagesOf(s::get('modulo'), s::get('el__ID'));
            $el__images_arr = array();
            foreach($el__images as $img){
                $el__images_arr[] = $img->filename();
            }
            if($opt['value'] != '' && !in_array($opt['value'], $el__images_arr)) $error = true;
            if($opt['value'] == '' && $opt['required'] != '') $error = true;

            // Html
            $choices__box = static::brick('choices');

            $field->attr('data-source', $opt['files-source']);
            $field->append($label);
            $field->append($input);

            $reset =  brick::btn("icon:x", "click:INPUT.reset(this)", "class:reset");
            $field->append($reset);

            $arrow =  brick::btn("icon:chevron-down", "class:side", "click:CHOICE.toggle(event)");
            $field->append($arrow);
            $field->append($choices__box);

            break;
        case "document":

            $input->attr('type', 'text');
            $input->attr('readonly', true);
            $input->attr('data-source', $opt['files-source']);
            $input->attr('onclick', 'CHOICE.toggle(event)');
            $input->addClass('btn--side');

            // Validation
            // Controllo che il valore corrisponda a uno dei nomi dei documenti
            $el__documents = module::documentsOf(s::get('modulo'), s::get('el__ID'));
            $el__documents_arr = array();
            foreach($el__documents as $doc){
                $el__documents_arr[] = $doc->filename();
            }
            if($opt['value'] != '' && !in_array($opt['value'], $el__documents_arr)) $error = true;
            if($opt['value'] == '' && $opt['required'] != '') $error = true;

            // Html
            $choices__box = static::brick('choices');

            $field->append($label);
            $field->append($input);

            $reset =  brick::btn("icon:x", "click:INPUT.reset(this)", "class:reset");
            $field->append($reset);

            $arrow =  brick::btn("icon:chevron-down", "class:side", "click:CHOICE.toggle(event)");
            $field->append($arrow);
            $field->append($choices__box);

            break;
        case "relation":

            $input->attr('type', 'hidden');

            $fakeinput = static::brick('input');
            $fakeinput->attr('readonly', true);
            $fakeinput->addClass('btn--side');

            // Recupero i "data" dell'elemento relazionato e visualizzo i campi definiti da "display" (separati da virgola)
            if($opt['value'] != ''){
                $el_related = module::dataOf($opt['relation-modulo'], $opt['value']);

                $display = array();
                $displays = str::split($opt['relation-display']);

                // Creo array di ogni valore o inserisco la stringa se presente #stringa# o formatto il prezzo £numero£
                foreach($displays as $d){
                    if(str::contains($d,"#")){
                        $display[] = str::replace($d, '#', '');
                    }else if(str::contains($d,"£")){
                        $d =  str::replace($d, '£', '');
                        $display[] = util::euro(a::get($el_related, $d));
                    }else{
                        $display[] = a::get($el_related, $d);
                    }
                }

                $fakeinput->attr('value', join(" ",$display));
            }

            // Validation
            // Controllo solamente che sia un GUID
            if($opt['value'] == '' && $opt['required'] != '') $error = true;
            if($opt['value'] != '' && !util::isGUID($opt['value'])) $error = true;

            // Html
            $field->append($label);
            $field->append($input);

            $reset =  brick::btn("icon:x", "click:INPUT.reset(this)", "class:reset");
            $field->append($reset);

            $field->append($fakeinput);
            $arrow =  brick::btn('icon:link', 'class:side', 'click:RELATION.display(this,\'' . $opt['relation-modulo'] . '\')');
            $field->append($arrow);

            break;
        case "richtext":

            $richtext_toolbar = static::brick('div');
            $richtext_toolbar->addClass('richtext-toolbar');

            $richtext_toolbar->append( brick::btn('icon:corner-up-left', 'title:Annulla', 'click:RICHTEXT.format(\'undo\')'));
            $richtext_toolbar->append( brick::btn('icon:bold', 'title:Grassetto', 'click:RICHTEXT.format(\'bold\')'));
            $richtext_toolbar->append( brick::btn('icon:italic', 'title:Corsivo', 'click:RICHTEXT.format(\'italic\')'));
            $richtext_toolbar->append( brick::btn('icon:underline', 'title:Sottolineato', 'click:RICHTEXT.format(\'underline\')'));
            $richtext_toolbar->append( brick::btn('icon:listnumber', 'title:Lista numerata', 'click:RICHTEXT.format(\'insertorderedlist\')'));
            $richtext_toolbar->append( brick::btn('icon:list', 'title:Lista', 'click:RICHTEXT.format(\'insertunorderedlist\')'));
            $richtext_toolbar->append( brick::btn('icon:deformatting', 'title:Elimina formattazione', 'click:RICHTEXT.deformatting()'));
            $richtext_toolbar->append( brick::btn('icon:link', 'title:Crea link', 'click:RICHTEXT.writelink()'));
            $richtext_toolbar->append( brick::btn('icon:loader', 'title:Cancella tutto', 'click:RICHTEXT.empty(this)'));

            $richtext_area = static::brick('div');
            $richtext_area->attr('contenteditable','true');
            $richtext_area->addClass('richtext-area');

            $input->attr('type', 'hidden');
            $input->addClass('richtext-input');

            // Validation
            if($opt['value'] != '' && str::contains($opt['value'], '<script>')) $error = true;

            // Validation max length
            if($opt['validation-max-length'] != '' && str::length(str::unhtml($opt['value'])) > $opt['validation-max-length']) $error = true;

            // Html
            $field->append($label);
            $field->append($richtext_toolbar);
            $field->append($richtext_area);
            $field->append($input);

            break;
    }

    if(s::get('saving') && $error){
        $field->addClass('error');
        s::set('errors', true);
    }

    echo $field;
  }

  public static function select(string $id, string $name, array $options, string $selectedValue = '', bool $addBlankOption = false, array $attributes = null): string {
    $optionsHtml = '';
    foreach ($options as $value => $title) {
      $attr = [];

      $attr['value'] = $value;
      if ($value  === $selectedValue) {
        $attr['selected'] = 'selected';
      }
      $optionsHtml .= brick('option', $title, $attr);
    }

    if ($addBlankOption) {
      $optionsHtml = brick('option', '', '') . $optionsHtml;
    }

    $attr = ['id' => $id, 'name' => $name];
    if ($attributes) {
      $attr = array_merge($attr, $attributes);
    }

    $select = brick('select', $optionsHtml, $attr);

    return $select;
  }

  /**
   * Genera un blocco html per effettuare upload.
   *
   * @param  mixed $id
   * @param  mixed $validationFileType Può accettare i vari tipi specificati in f::$types (attualmente abilitati: image, document)
   * @return string
   */
  public static function upload(string $id, string $validationFileType): string {
    $html = '<div class="box box--files">
              <input id="' . $id . '_upload_files" type="file" name="file[]"  class="inputfile" data-validation-file-type="' . $validationFileType . '" multiple="multiple" onchange="FILE.upload(this)" />
              <label for="' . $id . '_upload_files" class="btn contextual important only--icon">' . icon::get('paperclip') . '</label>
              <div id="'. $id . '"></div>
            </div>';

    return $html;
  }
}
