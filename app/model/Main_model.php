<?php

namespace App\Model;

use Nette;

class Main_model
{

  public function __construct(Nette\Database\Explorer $database)
  {
    $this->database = $database;
  }
}
