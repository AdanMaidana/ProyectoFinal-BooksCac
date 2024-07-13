<?php

class Libro
{
  public $id;
  public $titulo;
  public $autor;
  public $generos;
  public $sinopsis;

  public function __construct($titulo, $autor, $generos = null, $sinopsis = null, $id = null)
  {
    $this->id = $id;
    $this->titulo = $titulo;
    $this->autor = $autor;
    $this->generos = $generos;
    $this->sinopsis = $sinopsis;
  }

  public static function fromArray($data)
  {
    return new self(
      $data['titulo'] ?? null,
      $data['autor'] ?? null,
      $data['generos'] ?? null,
      $data['sinopsis'] ?? null,
      $data['id'] ?? null,
    );
  }

  public function toArray()
  {
    return get_object_vars($this);
  }
}
