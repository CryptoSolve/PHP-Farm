<?php

    interface IGetProduction
    {
        public function getProduct();
    }

    class Product
    {
        public $name;
        public $count;

        public function __construct($name, $count)
        {
            $this->name = $name;
            $this->count = $count;
        }
    }

    class Egg extends Product
    {
        public function __construct($count)
        {
            parent::__construct("Яйцо", $count);
        }
    }

    class Milk extends Product
    {
        public function __construct($count)
        {
            parent::__construct("Молоко", $count);
        }
    }

    class Animal
    {
        public $id;
        public $name;
        public $min_production_amount;
        public $max_production_amount;

        public function __construct($id, $name, $min_production_amount, $max_production_amount)
        {
            $this->id = $id;
            $this->name = $name;
            $this->min_production_amount = $min_production_amount;
            $this->max_production_amount = $max_production_amount;
        }
    }

    class Chicken extends Animal implements IGetProduction
    {
        public function __construct($id)
        {
            parent::__construct($id, "Курица", 0, 1);
        }

        public function getProduct()
        {
            return new Egg(rand($this->min_production_amount, $this->max_production_amount));
        }
    }

    class Cow extends Animal implements IGetProduction
    {
        public function __construct($id)
        {
            parent::__construct($id, "Корова", 8, 12);
        }

        public function getProduct()
        {
            return new Milk(rand($this->min_production_amount, $this->max_production_amount));
        }
    }

    // Хлев с животными
    class Barn
    {
        private $number_of_livestock;
        private $animals;
        private $product_storage;

        public function __construct()
        {
            $this->number_of_livestock = 0;
            $this->animals = array();
            $this->product_storage = array();
        }

        // Добавляет массив животных в хлев
        public function addAnimal(array $animals)
        {
            foreach ($animals as $animal)
            {
                $animal->id = ++$number_of_livestock;
            }

            $this->animals = array_merge($this->animals, $animals);
        }

        // Выводит на экран информацию о собранных сегодня продуктах
        public function getDayInfo()
        {
            $products = $this->collect();

            echo "<table><caption colspan=\"2\">За день собрано</caption><tr><th>Тип продукта</th><th>Количество</th></tr>";
            foreach ($products as $product)
            {
                echo "<tr><td>{$product->name}</td><td class=\"count\">x{$product->count}</td><tr>";
                $this->product_storage = $this->mergeProducts($this->product_storage, $product);
            }
            echo "</table>";
        }

        // Выводит на экран информацию о количестве животных в хлеву
        public function getAnimalsInfo()
        {
            echo "<table><caption colspan=\"2\">Животных в хлеву</caption><tr><th>Тип животного</th><th>Количество</th></tr>";
            $animals = array();
            $animals = $this->mergeAnimals($animals);

            foreach ($animals as $animal_type => $animal_amount)
            {
                echo "<tr><td>{$animal_type}</td><td class=\"count\">x{$animal_amount}</td><tr>";
            }
            echo "</table>";
        }

        // Выводит на экран количество всех собранных продуктов
        public function getStorageInfo()
        {
            echo "<table><caption colspan=\"2\">Всего собрано</caption><tr><th>Тип продукта</th><th>Количество</th></tr>";

            foreach ($this->product_storage as $product)
            {
                echo "<tr><td>{$product->name}</td><td class=\"count\">x{$product->count}</td><tr>";
            }
            echo "</table>";
        }

        // Собирает продукты с животных
        private function collect()
        {
            $products = array();

            foreach ($this->animals as $animal)
            {
                $collected_product = $animal->getProduct();

                $products = $this->mergeProducts($products, $collected_product);
            }

            return $products;
        }

        // Объединяет продукты
        private function mergeProducts(array $products, Product $collected_product)
        {
            // Для нахождения индекса повторяющегося продукта
            $found_index = array_search($collected_product->name, array_map(function($p) {
                return $p->name;
            }, $products));

            if ($found_index === false)
            {
                array_push($products, $collected_product);
            }
            else
            {
                $products[$found_index]->count += $collected_product->count;
            }

            return $products;
        }

        // Объединяет животных
        private function mergeAnimals(array $animals)
        {
            foreach ($this->animals as $animal)
            {
                $animal_types = array_keys($animals);
                $found_index = array_search($animal->name, $animal_types);

                if ($found_index === false)
                {
                    $animals[$animal->name] = 1;
                }
                else
                {
                    ++$animals[$animal->name];
                }
            }

            return $animals;
        }
    }
    
    echo "
    <html>
    <head>
    <style type=\"text/css\">
        table, th, tr, td {
            padding: 5px 10px;
            border: 1px solid black;
            border-collapse: collapse;
        }

        .count {
            display: flex;
            justify-content: center;
            border: none;
            color: red;
        }
    </style>
    </head>
    <body>
    </body>
    </html>";

    function initialAnimals($cows_amount, $chickens_amount)
    {
        $initial_animals = array();
        for ($i = 0; $i < $cows_amount; $i++)
        {
            $cow = new Cow(0);
            array_push($initial_animals, $cow);
        }
    
        for ($i = 0; $i < $chickens_amount; $i++)
        {
            $chicken = new Chicken(0);
            array_push($initial_animals, $chicken);
        }

        return $initial_animals;
    }

    $barn = new Barn();

    $barn->addAnimal(initialAnimals(9, 18));
    $barn->addAnimal([new Cow(0)]);
    $barn->addAnimal([new Chicken(0), new Chicken(0)]);

    $barn->getAnimalsInfo();
    $barn->getDayInfo();
    $barn->getDayInfo();
    $barn->getStorageInfo();
?>