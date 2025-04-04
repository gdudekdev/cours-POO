<?php

class Product
{
  // ON créé une variable par champ de la BDD
  private ?float $price = 0;
  private ?int $id = 0;
  private ?string $serie = "";
  protected ?string $name = "";

  // Les 3 mots clés de définition des attributs:
  // private : l'attribut n'est pas lisible directement dans l'bjet créé par la classe, ne peut pas être hérité
  // public : l'attribut est accessible depuis l'objet créé
  // protected : l'attribut est héritable à une classe fille

  // Le point d'interrogation veut dire qu'on accepte les valeurs null
  // On peut donner une valeur par défaut avec =

  public function __construct($row = false)
  {
    // On vérifier que ce qui est fourni en paramètre de la création est bien ce qui est demandé
    if ($row) {
      $this->hydrate($row);
    }
  }

  public function hydrate($data)
  {
    $this->setPrice($data["product_price"]);
    $this->setName($data["product_name"]);

    foreach ($data as $key => $value) {
      $method = "set" . ucfirst(str_replace("product_", "", $key));
      if (method_exists($this, $method)) {
        $this->{$method}($value);
      }
    }
  }

  // Le principe de création d'un setter et d'un getter par attribut de l'objet s'appel l'encapsulation
  public function setPrice(float $price)
  {
    $this->price = ($price >= 0) ? $price : 0;
  }
  public function setName(string $name)
  {
    $this->name = $name;
  }
  public function setId(float $id)
  {
    $this->id = $id;
  }
  public function setSerie(string $serie)
  {
    $this->serie = $serie;
  }

  public function getPrice($raw = false)
  {
    // $raw est ici optionnel car il a une valeur par défaut
    if ($raw) {
      return $this->price;
    }
    // On effectue une vérification dans le cas où price est null pour éviter les erreurs php
    return is_null($this->price) ? "" : htmlspecialchars($this->price);
  }
  public function getName($raw = false)
  {
    return $raw ? $this->name : htmlspecialchars($this->name);
  }
  public function getId($raw = false)
  {
    return $raw ? $this->id : htmlspecialchars($this->id);
  }
  public function getSerie($raw = false)
  {
    return $raw ? $this->serie : htmlspecialchars($this->serie);
  }
  // Il est possible d'avoir des fonctions privé.
}
