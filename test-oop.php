<?php

interface Juicable {
    public function getVolume(): float;
}

class Fruit implements Juicable {
    private string $color;
    private float $volume;

    public function __construct(string $color, float $volume) {
        if ($volume <= 0) {
            throw new InvalidArgumentException("Volume cannot be negative.");
        }

        $this->color = $color;
        $this->volume = $volume;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $value): void
    {
        $this->color = $value;
    }


    public function getVolume(): float
    {
        return $this->volume;
    }
}

class Apple extends Fruit {
    private bool $rotten;

    public function __construct(float $volume) {
        parent::__construct("red", $volume);

        $this->rotten = (mt_rand(1, 100) <= 20);
    }

    public function isRotten(): bool {
        return $this->rotten;
    }

}

class FruitContainer {
    private array $fruits = [];
    private float $capacity; // liters

     public function __construct(float $capacity) {
        $this->capacity = $capacity;
    }

    public function getCapacity(): float
    {
        return $this->capacity;
    }

    public function addFruit(Fruit  $fruit): void
    {
        if ($this->getUsedSpace() + $fruit->getVolume() > $this->capacity) {
            throw new Exception("Container is full!");
        }

        $this->fruits[] = $fruit;
    }

    
    public function fruitsCount()
    {
        return count($this->fruits);
    }

    public function getUsedSpace(): float {
        return array_sum(array_map(fn($f) => $f->getVolume(), $this->fruits));
    }

    public function getSpaceLeft()
    {
        return $this->getCapacity() - $this->getUsedSpace();
    }

    public function popFruit(): ?Fruit 
    {
        return array_shift($this->fruits);
    }
}

class Strainer {
    public function squeeze(Juicable $fruit)
    {
        return $fruit->getVolume() * 0.5;
    }
}

class Juicer {
    private FruitContainer $container;
    private Strainer $strainer;

    public function __construct(float $capacity) {
        $this->container = new FruitContainer($capacity);
        $this->strainer = new Strainer();
    }

    public function addFruit(Fruit $fruit): void {
        try {
            $this->container->addFruit($fruit);
            echo "Added fruit (volume {$fruit->getVolume()}). Free space left: {$this->container->getSpaceLeft()}\n";
        } catch (Exception $e) {
            echo "Failed to add fruit: " . $e->getMessage() . "\n";
        }
    }

    public function squeezeFruit(): void {
        $fruit = $this->container->popFruit();

        if (!$fruit) {
            echo "No fruit available to squeeze!\n";
            return;
        }

        // If apple is rotten – do not squeeze
        if ($fruit instanceof Apple && $fruit->isRotten()) {
            echo "Apple was rotten! Discarded.\n";
            return;
        }

        $juice = $this->strainer->squeeze($fruit);
        echo "Squeezed fruit → Juice gained: {$juice} liters\n";
    }

    public function getContainer(): FruitContainer {
        return $this->container;
    }
}

$juicer = new Juicer(20);

echo "=== SIMULATION START ===\n\n";

$actionCount = 100;
$squeezeCounter = 0;

for ($i = 1; $i <= $actionCount; $i++) {

    echo "Action #{$i}: ";

    if ($squeezeCounter > 0 && $squeezeCounter % 9 == 0) {
        $volume = mt_rand(1, 5);
        $apple = new Apple($volume);
        echo "Adding an apple (volume {$volume}, rotten? " . ($apple->isRotten() ? "YES" : "NO") . ")\n";
        $juicer->addFruit($apple);
        $squeezeCounter++; 
        continue;
    }

    $squeezeCounter++;
    echo "Squeezing fruit...\n";
    $juicer->squeezeFruit();

    $squeezeCounter++;
}

echo "\n=== SIMULATION COMPLETE ===\n";