<?php
    // Exercise 2: Shape Calculator with Abstract Classes and Interfaces
    
    echo "<h2>Shape Calculator - Abstract Classes and Interfaces</h2>";
    
    // Interface for drawable shapes
    interface Drawable {
        public function draw(): void;
        public function getDimensions(): string;
    }
    
    // Interface for resizable shapes
    interface Resizable {
        public function resize(float $factor): void;
        public function getResizeLimits(): array;
    }
    
    // Interface for shapes with perimeter
    interface HasPerimeter {
        public function getPerimeter(): float;
    }
    
    // Abstract base class for all shapes
    abstract class Shape implements Drawable {
        protected string $name;
        protected string $color;
        protected float $area;
        
        public function __construct(string $name, string $color = "black") {
            $this->name = $name;
            $this->color = $color;
            $this->area = 0;
        }
        
        // Getters
        public function getName(): string {
            return $this->name;
        }
        
        public function getColor(): string {
            return $this->color;
        }
        
        public function getArea(): float {
            return $this->area;
        }
        
        // Setters
        public function setColor(string $color): void {
            $this->color = $color;
        }
        
        // Abstract methods that must be implemented
        abstract public function calculateArea(): float;
        abstract public function getDimensions(): string;
        
        // Concrete method that can be used as-is
        public function draw(): void {
            echo "Drawing a {$this->color} {$this->name} with area " . 
                 number_format($this->area, 2) . "<br>";
        }
        
        // Utility method
        public function compareArea(Shape $other): string {
            if ($this->area > $other->getArea()) {
                return "{$this->name} is larger than {$other->getName()}";
            } elseif ($this->area < $other->getArea()) {
                return "{$this->name} is smaller than {$other->getName()}";
            } else {
                return "{$this->name} has the same area as {$other->getName()}";
            }
        }
        
        // Static method for creating shapes from data
        public static function createFromData(array $data): ?Shape {
            switch ($data['type'] ?? '') {
                case 'circle':
                    return new Circle($data['radius'] ?? 1, $data['color'] ?? 'black');
                case 'rectangle':
                    return new Rectangle($data['width'] ?? 1, $data['height'] ?? 1, $data['color'] ?? 'black');
                case 'triangle':
                    return new Triangle($data['base'] ?? 1, $data['height'] ?? 1, $data['color'] ?? 'black');
                default:
                    return null;
            }
        }
    }
    
    // Circle class
    class Circle extends Shape implements Resizable, HasPerimeter {
        private float $radius;
        private const MIN_RADIUS = 0.1;
        private const MAX_RADIUS = 1000;
        
        public function __construct(float $radius, string $color = "black") {
            parent::__construct("Circle", $color);
            $this->radius = max($radius, self::MIN_RADIUS);
            $this->calculateArea();
        }
        
        public function getRadius(): float {
            return $this->radius;
        }
        
        public function setRadius(float $radius): void {
            $this->radius = max($radius, self::MIN_RADIUS);
            $this->calculateArea();
        }
        
        public function calculateArea(): float {
            $this->area = pi() * $this->radius * $this->radius;
            return $this->area;
        }
        
        public function getPerimeter(): float {
            return 2 * pi() * $this->radius;
        }
        
        public function getDimensions(): string {
            return "Circle with radius {$this->radius}";
        }
        
        public function resize(float $factor): void {
            $newRadius = $this->radius * $factor;
            if ($newRadius >= self::MIN_RADIUS && $newRadius <= self::MAX_RADIUS) {
                $this->radius = $newRadius;
                $this->calculateArea();
                echo "Circle resized by factor $factor<br>";
            } else {
                echo "Resize factor $factor would exceed limits<br>";
            }
        }
        
        public function getResizeLimits(): array {
            return [
                'min_factor' => self::MIN_RADIUS / $this->radius,
                'max_factor' => self::MAX_RADIUS / $this->radius
            ];
        }
        
        public function getDiameter(): float {
            return 2 * $this->radius;
        }
        
        public function draw(): void {
            echo "Drawing a {$this->color} circle (radius: {$this->radius}, diameter: " . 
                 $this->getDiameter() . ")<br>";
        }
    }
    
    // Rectangle class
    class Rectangle extends Shape implements Resizable, HasPerimeter {
        private float $width;
        private float $height;
        private const MIN_SIZE = 0.1;
        private const MAX_SIZE = 1000;
        
        public function __construct(float $width, float $height, string $color = "black") {
            parent::__construct("Rectangle", $color);
            $this->width = max($width, self::MIN_SIZE);
            $this->height = max($height, self::MIN_SIZE);
            $this->calculateArea();
        }
        
        public function getWidth(): float {
            return $this->width;
        }
        
        public function getHeight(): float {
            return $this->height;
        }
        
        public function setWidth(float $width): void {
            $this->width = max($width, self::MIN_SIZE);
            $this->calculateArea();
        }
        
        public function setHeight(float $height): void {
            $this->height = max($height, self::MIN_SIZE);
            $this->calculateArea();
        }
        
        public function calculateArea(): float {
            $this->area = $this->width * $this->height;
            return $this->area;
        }
        
        public function getPerimeter(): float {
            return 2 * ($this->width + $this->height);
        }
        
        public function getDimensions(): string {
            return "Rectangle ({$this->width} x {$this->height})";
        }
        
        public function resize(float $factor): void {
            $newWidth = $this->width * $factor;
            $newHeight = $this->height * $factor;
            
            if ($newWidth >= self::MIN_SIZE && $newWidth <= self::MAX_SIZE &&
                $newHeight >= self::MIN_SIZE && $newHeight <= self::MAX_SIZE) {
                $this->width = $newWidth;
                $this->height = $newHeight;
                $this->calculateArea();
                echo "Rectangle resized by factor $factor<br>";
            } else {
                echo "Resize factor $factor would exceed limits<br>";
            }
        }
        
        public function getResizeLimits(): array {
            return [
                'min_factor' => max(self::MIN_SIZE / $this->width, self::MIN_SIZE / $this->height),
                'max_factor' => min(self::MAX_SIZE / $this->width, self::MAX_SIZE / $this->height)
            ];
        }
        
        public function isSquare(): bool {
            return abs($this->width - $this->height) < 0.001;
        }
        
        public function draw(): void {
            $shapeType = $this->isSquare() ? "Square" : "Rectangle";
            echo "Drawing a {$this->color} $shapeType ({$this->width} x {$this->height})<br>";
        }
    }
    
    // Triangle class
    class Triangle extends Shape implements HasPerimeter {
        private float $base;
        private float $height;
        private float $sideA;
        private float $sideB;
        private float $sideC;
        
        public function __construct(float $base, float $height, string $color = "black") {
            parent::__construct("Triangle", $color);
            $this->base = $base;
            $this->height = $height;
            // Calculate sides for an isosceles triangle
            $this->sideA = $base;
            $this->sideB = sqrt(pow($base/2, 2) + pow($height, 2));
            $this->sideC = $this->sideB;
            $this->calculateArea();
        }
        
        public function getBase(): float {
            return $this->base;
        }
        
        public function getHeight(): float {
            return $this->height;
        }
        
        public function calculateArea(): float {
            $this->area = 0.5 * $this->base * $this->height;
            return $this->area;
        }
        
        public function getPerimeter(): float {
            return $this->sideA + $this->sideB + $this->sideC;
        }
        
        public function getDimensions(): string {
            return "Triangle (base: {$this->base}, height: {$this->height})";
        }
        
        public function draw(): void {
            echo "Drawing a {$this->color} triangle (base: {$this->base}, height: {$this->height})<br>";
        }
        
        public function getTriangleType(): string {
            if ($this->sideB == $this->sideC) {
                if ($this->base == $this->sideB) {
                    return "Equilateral";
                } else {
                    return "Isosceles";
                }
            }
            return "Scalene";
        }
    }
    
    // Shape Calculator class
    class ShapeCalculator {
        private array $shapes = [];
        
        public function addShape(Shape $shape): void {
            $this->shapes[] = $shape;
        }
        
        public function getTotalArea(): float {
            $total = 0;
            foreach ($this->shapes as $shape) {
                $total += $shape->getArea();
            }
            return $total;
        }
        
        public function getAverageArea(): float {
            if (empty($this->shapes)) {
                return 0;
            }
            return $this->getTotalArea() / count($this->shapes);
        }
        
        public function getLargestShape(): ?Shape {
            if (empty($this->shapes)) {
                return null;
            }
            
            $largest = $this->shapes[0];
            foreach ($this->shapes as $shape) {
                if ($shape->getArea() > $largest->getArea()) {
                    $largest = $shape;
                }
            }
            return $largest;
        }
        
        public function getShapesByType(string $type): array {
            return array_filter($this->shapes, function($shape) use ($type) {
                return get_class($shape) === $type;
            });
        }
        
        public function getTotalPerimeter(): float {
            $total = 0;
            foreach ($this->shapes as $shape) {
                if ($shape instanceof HasPerimeter) {
                    $total += $shape->getPerimeter();
                }
            }
            return $total;
        }
        
        public function resizeAllResizable(float $factor): void {
            foreach ($this->shapes as $shape) {
                if ($shape instanceof Resizable) {
                    $shape->resize($factor);
                }
            }
        }
        
        public function drawAllShapes(): void {
            echo "<h3>Drawing All Shapes:</h3>";
            foreach ($this->shapes as $shape) {
                $shape->draw();
            }
        }
        
        public function getStatistics(): array {
            $stats = [
                'total_shapes' => count($this->shapes),
                'total_area' => $this->getTotalArea(),
                'average_area' => $this->getAverageArea(),
                'total_perimeter' => $this->getTotalPerimeter(),
                'shapes_by_type' => []
            ];
            
            foreach ($this->shapes as $shape) {
                $type = get_class($shape);
                if (!isset($stats['shapes_by_type'][$type])) {
                    $stats['shapes_by_type'][$type] = 0;
                }
                $stats['shapes_by_type'][$type]++;
            }
            
            return $stats;
        }
    }
    
    // Demonstration
    echo "<h3>Creating Shapes:</h3>";
    
    $calculator = new ShapeCalculator();
    
    // Create different shapes
    $circle1 = new Circle(5, "red");
    $circle2 = new Circle(3, "blue");
    $rectangle1 = new Rectangle(4, 6, "green");
    $rectangle2 = new Rectangle(5, 5, "yellow");  // Square
    $triangle1 = new Triangle(4, 3, "purple");
    
    // Add shapes to calculator
    $calculator->addShape($circle1);
    $calculator->addShape($circle2);
    $calculator->addShape($rectangle1);
    $calculator->addShape($rectangle2);
    $calculator->addShape($triangle1);
    
    // Draw all shapes
    $calculator->drawAllShapes();
    
    echo "<h3>Shape Details:</h3>";
    foreach ($calculator->getShapesByType(Circle::class) as $circle) {
        echo $circle->getDimensions() . " - Area: " . number_format($circle->getArea(), 2) . 
             ", Perimeter: " . number_format($circle->getPerimeter(), 2) . "<br>";
    }
    
    foreach ($calculator->getShapesByType(Rectangle::class) as $rectangle) {
        $type = $rectangle->isSquare() ? "Square" : "Rectangle";
        echo $rectangle->getDimensions() . " - Area: " . number_format($rectangle->getArea(), 2) . 
             ", Perimeter: " . number_format($rectangle->getPerimeter(), 2) . " ($type)<br>";
    }
    
    foreach ($calculator->getShapesByType(Triangle::class) as $triangle) {
        echo $triangle->getDimensions() . " - Area: " . number_format($triangle->getArea(), 2) . 
             ", Perimeter: " . number_format($triangle->getPerimeter(), 2) . 
             " (" . $triangle->getTriangleType() . ")<br>";
    }
    
    echo "<h3>Shape Comparisons:</h3>";
    echo $circle1->compareArea($rectangle1) . "<br>";
    echo $rectangle1->compareArea($triangle1) . "<br>";
    echo $circle2->compareArea($rectangle2) . "<br>";
    
    echo "<h3>Resizing Shapes:</h3>";
    echo "Original circle radius: " . $circle1->getRadius() . "<br>";
    $circle1->resize(1.5);
    echo "New circle radius: " . $circle1->getRadius() . "<br>";
    echo "New circle area: " . number_format($circle1->getArea(), 2) . "<br><br>";
    
    echo "Original rectangle: " . $rectangle1->getDimensions() . "<br>";
    $rectangle1->resize(0.8);
    echo "New rectangle: " . $rectangle1->getDimensions() . "<br>";
    echo "New rectangle area: " . number_format($rectangle1->getArea(), 2) . "<br><br>";
    
    // Test resize limits
    echo "<h3>Testing Resize Limits:</h3>";
    $limits = $circle1->getResizeLimits();
    echo "Circle resize limits - Min: " . number_format($limits['min_factor'], 3) . 
         ", Max: " . number_format($limits['max_factor'], 2) . "<br>";
    
    $circle1->resize(100);  // Should fail - exceeds max limit
    $circle1->resize(0.001);  // Should fail - below min limit
    
    echo "<h3>Calculator Statistics:</h3>";
    $stats = $calculator->getStatistics();
    echo "Total shapes: " . $stats['total_shapes'] . "<br>";
    echo "Total area: " . number_format($stats['total_area'], 2) . "<br>";
    echo "Average area: " . number_format($stats['average_area'], 2) . "<br>";
    echo "Total perimeter: " . number_format($stats['total_perimeter'], 2) . "<br>";
    echo "Shapes by type:<br>";
    foreach ($stats['shapes_by_type'] as $type => $count) {
        echo "- $type: $count<br>";
    }
    
    echo "<h3>Largest Shape:</h3>";
    $largest = $calculator->getLargestShape();
    if ($largest) {
        echo $largest->getName() . " with area " . number_format($largest->getArea(), 2) . "<br>";
    }
    
    echo "<h3>Creating Shapes from Data:</h3>";
    $shapeData = [
        ['type' => 'circle', 'radius' => 7, 'color' => 'orange'],
        ['type' => 'rectangle', 'width' => 8, 'height' => 3, 'color' => 'cyan'],
        ['type' => 'triangle', 'base' => 6, 'height' => 4, 'color' => 'magenta']
    ];
    
    foreach ($shapeData as $data) {
        $shape = Shape::createFromData($data);
        if ($shape) {
            $calculator->addShape($shape);
            echo "Created: " . $shape->getDimensions() . "<br>";
        }
    }
    
    echo "<br><strong>Updated Statistics:</strong><br>";
    $newStats = $calculator->getStatistics();
    echo "Total shapes: " . $newStats['total_shapes'] . "<br>";
    echo "Total area: " . number_format($newStats['total_area'], 2) . "<br>";
    echo "Average area: " . number_format($newStats['average_area'], 2) . "<br>";
?>
