<?php
/**
 * Product class
 */
class Product
{
      protected ?float $price;
      protected ?string $name;
      protected ?int $id;
      protected ?string $slug;
      protected ?string $date;
      protected ?string $serie;
      protected ?int $volume;
      protected ?string $author;
      protected ?string $description;
      protected ?string $resume;
      protected ?int $stock;
      protected ?string $publisher;
      protected ?string $cartoonist;



      

      public function __construct($data = false)
      {
            if ($data) {
                  $this->hydrate($data);
            }
      }
      public function hydrate($data)
      {
            foreach ($data as $key => $value) {
                  $method = "set" . ucfirst(str_replace("product_","",$key));
                  if(method_exists($this,$method))$this->{$method}($value);
            }
      }
      public function setPrice($value)
      {
            $value < 0 ? $this->price = 0 : $this->price = $value;
      }
      public function getPrice($raw = false)
      {
            return $raw ? $this->price : (is_null($this->price) ? "" : htmlspecialchars($this->price));
      }
      public function setName($value)
      {
            $this->name = $value;
      }
      public function getName($raw = false)
      {
            return $raw ? $this->name : (is_null($this->name) ? "" : htmlspecialchars($this->name));
      }
      public function setId($value)
      {
            $this->id = $value;
      }
      public function getId($raw = false)
      {
            return $raw ? $this->id : (is_null($this->id) ? "" : htmlspecialchars($this->id));
      }
      public function setSlug($value)
      {
            $this->slug = $value;
      }
      public function getSlug($raw = false)
      {
            return $raw ? $this->slug : (is_null($this->slug) ? "" : htmlspecialchars($this->slug));
      }
      public function setDate($value)
      {
            $this->date = $value;
      }
      public function getDate($raw = false)
      {
            return $raw ? $this->date : (is_null($this->date) ? "" : htmlspecialchars($this->date));
      }
      public function setSerie($value)
      {
            $this->serie = $value;
      }
      public function getSerie($raw = false)
      {
            return $raw ? $this->serie : (is_null($this->serie) ? "" : htmlspecialchars($this->serie));
      }
      public function setVolume($value)
      {
            $this->volume = $value;
      }
      public function getVolume($raw = false)
      {
            return $raw ? $this->volume : (is_null($this->volume) ? "" : htmlspecialchars($this->volume));
      }
      public function setAuthor($value)
      {
            $this->author = $value;
      }
      public function getAuthor($raw = false)
      {
            return $raw ? $this->author : (is_null($this->author) ? "" : htmlspecialchars($this->author));
      }
      public function setDescription($value)
      {
            $this->description = $value;
      }
      public function getDescription($raw = false)
      {
            return $raw ? $this->description : (is_null($this->description) ? "" : htmlspecialchars($this->description));
      }
      public function setResume($value)
      {
            $this->resume = $value;
      }
      public function getResume($raw = false)
      {
            return $raw ? $this->resume : (is_null($this->resume) ? "" : htmlspecialchars($this->resume));
      }
      public function setStock($value)
      {
            $this->stock = $value;
      }
      public function getStock($raw = false)
      {
            return $raw ? $this->stock : (is_null($this->stock) ? "" : htmlspecialchars($this->stock));
      }
      public function setPublisher($value)
      {
            $this->publisher = $value;
      }
      public function getPublisher($raw = false)
      {
            return $raw ? $this->publisher : (is_null($this->publisher) ? "" : htmlspecialchars($this->publisher));
      }
      public function setCartoonist($value)
      {
            $this->cartoonist = $value;
      }
      public function getCartoonist($raw = false)
      {
            return $raw ? $this->cartoonist : (is_null($this->cartoonist) ? "" : htmlspecialchars($this->cartoonist));
      }

}