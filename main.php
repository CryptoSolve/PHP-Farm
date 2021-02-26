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
        public $min_production_amount;
        public $max_production_amount;

        public function __construct($id, $min_production_amount, $max_production_amount)
        {
            $this->id = $id;
            $this->min_production_amount = $min_production_amount;
            $this->max_production_amount = $max_production_amount;
        }
    }

    class Chicken extends Animal implements IGetProduction
    {
        public function __construct($id)
        {
            parent::__construct($id, 0, 1);
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
            parent::__construct($id, 8, 12);
        }

        public function getProduct()
        {
            return new Milk(rand($this->min_production_amount, $this->max_production_amount));
        }
    }

    // Отвечает за сбор и суммарный подсчет продуктов
    class ProductCollector
    {
        public $products = array();
        public $animals = array();

        public function __construct($animals)
        {
            $this->animals = $animals;
        }

        public function collect()
        {
            $current_product_type;

            foreach ($this->animals as $animal)
            {
                $collected_products = $animal->getProduct();
                $last_index = count($this->products) - 1;
                if ($current_product_type != $collected_products->name)
                {
                    array_push($this->products, $collected_products);
                    $current_product_type = $collected_products->name;
                }
                else
                {
                    $current_product = $this->products[$last_index];
                    $current_product->count += $collected_products->count;
                }
            }

            return $this->products;
        }
    }

    // Хлев с животными
    class Barn
    {
        public $number_of_livestock = 0;
        public $animals = array();

        public function __construct()
        {
            for ($i = 0; $i < 10; $i++)
            {
                $cow = new Cow(++$number_of_livestock);
                array_push($this->animals, $cow);
            }

            for ($i = 0; $i < 20; $i++)
            {
                $chicken = new Chicken(++$number_of_livestock);
                array_push($this->animals, $chicken);
            }
        }

        // Получает информацию от сборщика продуктов и выводит на экран в виде таблицы
        public function getDayInfo()
        {
            $product_collector = new ProductCollector($this->animals);
            $products = $product_collector->collect();

            echo "<table><th colspan=\"2\">Сегодня собрано</th><tr><td>Тип продукта</td><td>Количество</td></tr>";
            foreach ($products as $product)
            {
                echo "<tr><td>{$product->name}</td><td class=\"count\">x{$product->count}</td><tr>";
            }
            echo "</table>";
        }
    }
    
    echo "
    <html>
    <head>
    <style type=\"text/css\">
        table, th, tr, td {
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

    $barn = new Barn();

    $barn->getDayInfo();
?>