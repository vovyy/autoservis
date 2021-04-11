<?php

namespace App\Model;

use Nette;

class Main_model
{

  public function __construct(Nette\Database\Explorer $database)
  {
    $this->database = $database;
  }
  function delete_by_id($id)
  {
    return $this->database->table('automobily')
      ->where("id", $id)
      ->delete('automobily');
  }
}
