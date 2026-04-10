# Python Object-Oriented Programming

## Classes and Objects

### Basic Class Definition
```python
class Person:
    # Class attribute (shared by all instances)
    species = "Homo sapiens"
    
    # Constructor (initializer)
    def __init__(self, name, age):
        # Instance attributes (unique to each instance)
        self.name = name
        self.age = age
        print(f"Person {self.name} created")
    
    # Destructor
    def __del__(self):
        print(f"Person {self.name} destroyed")
    
    # Instance method
    def introduce(self):
        return f"Hi, I'm {self.name} and I'm {self.age} years old."
    
    # Class method
    @classmethod
    def get_species(cls):
        return cls.species
    
    # Static method
    @staticmethod
    def is_adult(age):
        return age >= 18

# Creating objects (instances)
person1 = Person("Alice", 25)
person2 = Person("Bob", 30)

# Using methods
print(person1.introduce())
print(f"Species: {Person.get_species()}")
print(f"Is Alice adult? {Person.is_adult(person1.age)}")
```

### Instance vs Class vs Static Methods
```python
class Calculator:
    multiplier = 2  # Class attribute
    
    def __init__(self, base_value):
        self.base_value = base_value  # Instance attribute
    
    def multiply(self):  # Instance method
        return self.base_value * self.multiplier
    
    @classmethod
    def set_multiplier(cls, multiplier):  # Class method
        cls.multiplier = multiplier
    
    @staticmethod
    def add(a, b):  # Static method
        return a + b

# Using the class
calc = Calculator(10)
print(f"10 * 2 = {calc.multiply()}")

Calculator.set_multiplier(5)
calc2 = Calculator(10)
print(f"10 * 5 = {calc2.multiply()}")

print(f"5 + 3 = {Calculator.add(5, 3)}")
```

## Inheritance

### Single Inheritance
```python
class Animal:
    def __init__(self, name):
        self.name = name
        print(f"Animal {self.name} created")
    
    def speak(self):
        return f"{self.name} makes a sound"
    
    def eat(self):
        return f"{self.name} is eating"

class Dog(Animal):
    def __init__(self, name, breed):
        # Call parent constructor
        super().__init__(name)
        self.breed = breed
        print(f"Dog {self.name} ({self.breed}) created")
    
    # Override parent method
    def speak(self):
        return f"{self.name} barks!"
    
    # Extend with new method
    def fetch(self):
        return f"{self.name} fetches the ball"

# Creating instances
animal = Animal("Generic Animal")
dog = Dog("Buddy", "Golden Retriever")

print(animal.speak())
print(dog.speak())
print(dog.fetch())
print(dog.eat())  # Inherited method
```

### Multiple Inheritance
```python
class Flyer:
    def fly(self):
        return "Flying through the sky"

class Swimmer:
    def swim(self):
        return "Swimming in the water"

class Duck(Flyer, Swimmer):
    def __init__(self, name):
        self.name = name
    
    def quack(self):
        return f"{self.name} quacks!"

duck = Duck("Donald")
print(duck.fly())
print(duck.swim())
print(duck.quack())
```

### Method Resolution Order (MRO)
```python
class A:
    def method(self):
        return "A"

class B(A):
    def method(self):
        return "B"

class C(A):
    def method(self):
        return "C"

class D(B, C):
    pass

class E(C, B):
    pass

# Check method resolution order
print("D MRO:", D.__mro__)
print("E MRO:", E.__mro__)

d = D()
e = E()
print(f"D method: {d.method()}")
print(f"E method: {e.method()}")
```

## Encapsulation

### Private and Protected Attributes
```python
class BankAccount:
    def __init__(self, account_number, balance):
        # Public attribute
        self.account_number = account_number
        
        # Protected attribute (convention)
        self._balance = balance
        
        # Private attribute (name mangling)
        self.__pin = "1234"
    
    def get_balance(self):
        return self._balance
    
    def set_balance(self, new_balance):
        if new_balance >= 0:
            self._balance = new_balance
        else:
            raise ValueError("Balance cannot be negative")
    
    def __verify_pin(self, pin):
        return pin == self.__pin
    
    def withdraw(self, amount, pin):
        if self.__verify_pin(pin):
            if self._balance >= amount:
                self._balance -= amount
                return f"Withdrew ${amount}"
            else:
                return "Insufficient funds"
        else:
            return "Invalid PIN"

account = BankAccount("12345", 1000)
print(f"Balance: ${account.get_balance()}")
print(f"Withdraw: {account.withdraw(100, '1234')}")
print(f"New balance: ${account.get_balance()}")
```

### Properties
```python
class Temperature:
    def __init__(self, celsius=0):
        self._celsius = celsius
    
    @property
    def celsius(self):
        return self._celsius
    
    @celsius.setter
    def celsius(self, value):
        if value < -273.15:
            raise ValueError("Temperature below absolute zero is not possible")
        self._celsius = value
    
    @property
    def fahrenheit(self):
        return (self._celsius * 9/5) + 32
    
    @fahrenheit.setter
    def fahrenheit(self, value):
        self._celsius = (value - 32) * 5/9

temp = Temperature()
temp.celsius = 25
print(f"25°C = {temp.fahrenheit}°F")

temp.fahrenheit = 77
print(f"77°F = {temp.celsius}°C")

try:
    temp.celsius = -300
except ValueError as e:
    print(f"Error: {e}")
```

## Polymorphism

### Method Overriding
```python
class Shape:
    def area(self):
        raise NotImplementedError("Subclass must implement area method")
    
    def perimeter(self):
        raise NotImplementedError("Subclass must implement perimeter method")

class Rectangle(Shape):
    def __init__(self, width, height):
        self.width = width
        self.height = height
    
    def area(self):
        return self.width * self.height
    
    def perimeter(self):
        return 2 * (self.width + self.height)

class Circle(Shape):
    def __init__(self, radius):
        self.radius = radius
    
    def area(self):
        return 3.14159 * self.radius ** 2
    
    def perimeter(self):
        return 2 * 3.14159 * self.radius

class Triangle(Shape):
    def __init__(self, base, height, side1, side2):
        self.base = base
        self.height = height
        self.side1 = side1
        self.side2 = side2
    
    def area(self):
        return 0.5 * self.base * self.height
    
    def perimeter(self):
        return self.base + self.side1 + self.side2

# Polymorphic usage
shapes = [
    Rectangle(10, 5),
    Circle(7),
    Triangle(6, 4, 5, 5)
]

for shape in shapes:
    print(f"{shape.__class__.__name__} - Area: {shape.area():.2f}, Perimeter: {shape.perimeter():.2f}")
```

### Duck Typing
```python
class Duck:
    def quack(self):
        return "Quack!"
    
    def fly(self):
        return "Flying!"

class Person:
    def quack(self):
        return "I'm quacking like a duck!"
    
    def fly(self):
        return "I'm flying like a duck!"

def make_duck_quack_and_fly(duck):
    return f"{duck.quack()} {duck.fly()}"

# Duck typing in action
duck = Duck()
person = Person()

print(make_duck_quack_and_fly(duck))
print(make_duck_quack_and_fly(person))
```

## Special Methods (Magic Methods)

### Common Magic Methods
```python
class Book:
    def __init__(self, title, author, pages):
        self.title = title
        self.author = author
        self.pages = pages
    
    def __str__(self):
        return f"Book: {self.title} by {self.author}"
    
    def __repr__(self):
        return f"Book('{self.title}', '{self.author}', {self.pages})"
    
    def __len__(self):
        return self.pages
    
    def __getitem__(self, key):
        if isinstance(key, int):
            if 0 <= key < self.pages:
                return f"Page {key + 1} of {self.title}"
            else:
                raise IndexError("Page number out of range")
        elif isinstance(key, slice):
            start = key.start if key.start is not None else 0
            stop = key.stop if key.stop is not None else self.pages
            return f"Pages {start + 1}-{stop} of {self.title}"
        else:
            raise TypeError("Invalid key type")
    
    def __eq__(self, other):
        if not isinstance(other, Book):
            return False
        return (self.title == other.title and 
                self.author == other.author and 
                self.pages == other.pages)
    
    def __lt__(self, other):
        if not isinstance(other, Book):
            return NotImplemented
        return self.pages < other.pages

# Using magic methods
book1 = Book("1984", "George Orwell", 328)
book2 = Book("Animal Farm", "George Orwell", 112)
book3 = Book("1984", "George Orwell", 328)

print(str(book1))
print(repr(book1))
print(f"Length: {len(book1)}")
print(f"Page 5: {book1[4]}")
print(f"Pages 1-10: {book1[0:10]}")
print(f"Book 1 == Book 3: {book1 == book3}")
print(f"Book 1 < Book 2: {book1 < book2}")
```

### Arithmetic Magic Methods
```python
class Vector2D:
    def __init__(self, x, y):
        self.x = x
        self.y = y
    
    def __add__(self, other):
        if not isinstance(other, Vector2D):
            return NotImplemented
        return Vector2D(self.x + other.x, self.y + other.y)
    
    def __sub__(self, other):
        if not isinstance(other, Vector2D):
            return NotImplemented
        return Vector2D(self.x - other.x, self.y - other.y)
    
    def __mul__(self, scalar):
        if not isinstance(scalar, (int, float)):
            return NotImplemented
        return Vector2D(self.x * scalar, self.y * scalar)
    
    def __str__(self):
        return f"Vector2D({self.x}, {self.y})"
    
    def __repr__(self):
        return f"Vector2D({self.x}, {self.y})"

v1 = Vector2D(3, 4)
v2 = Vector2D(1, 2)

print(f"{v1} + {v2} = {v1 + v2}")
print(f"{v1} - {v2} = {v1 - v2}")
print(f"{v1} * 3 = {v1 * 3}")
```

## Advanced OOP Concepts

### Abstract Base Classes
```python
from abc import ABC, abstractmethod

class Animal(ABC):
    @abstractmethod
    def speak(self):
        pass
    
    @abstractmethod
    def eat(self):
        pass

class Dog(Animal):
    def speak(self):
        return "Woof!"
    
    def eat(self):
        return "Eating dog food"

class Cat(Animal):
    def speak(self):
        return "Meow!"
    
    def eat(self):
        return "Eating cat food"

# Cannot instantiate abstract class
# animal = Animal()  # This would raise TypeError

dog = Dog()
cat = Cat()
print(dog.speak())
print(cat.eat())
```

### Mixins
```python
class FlyableMixin:
    def fly(self):
        return f"{self.__class__.__name__} is flying"

class SwimmableMixin:
    def swim(self):
        return f"{self.__class__.__name__} is swimming"

class Bird(FlyableMixin):
    def __init__(self, name):
        self.name = name
    
    def speak(self):
        return f"{self.name} chirps"

class Fish(SwimmableMixin):
    def __init__(self, name):
        self.name = name
    
    def speak(self):
        return f"{self.name} makes bubble sounds"

class Duck(FlyableMixin, SwimmableMixin):
    def __init__(self, name):
        self.name = name
    
    def speak(self):
        return f"{self.name} quacks!"

bird = Bird("Tweety")
fish = Fish("Nemo")
duck = Duck("Donald")

print(bird.speak())
print(bird.fly())
print(fish.speak())
print(fish.swim())
print(duck.speak())
print(duck.fly())
print(duck.swim())
```

### Composition over Inheritance
```python
class Engine:
    def __init__(self, horsepower):
        self.horsepower = horsepower
    
    def start(self):
        return f"Engine starting ({self.horsepower} HP)"
    
    def stop(self):
        return "Engine stopping"

class Wheel:
    def __init__(self, size):
        self.size = size
    
    def rotate(self):
        return f"Wheel rotating ({self.size} inches)"

class Car:
    def __init__(self, make, model, year):
        self.make = make
        self.model = model
        self.year = year
        
        # Composition - Car has-an Engine and Wheels
        self.engine = Engine(200)
        self.wheels = [Wheel(18) for _ in range(4)]
    
    def start(self):
        engine_status = self.engine.start()
        wheel_status = " and ".join([wheel.rotate() for wheel in self.wheels])
        return f"{engine_status}, {wheel_status}"
    
    def stop(self):
        engine_status = self.engine.stop()
        return engine_status

car = Car("Toyota", "Camry", 2020)
print(car.start())
print(car.stop())
```

## Data Classes

### Using dataclass (Python 3.7+)
```python
from dataclasses import dataclass, field
from typing import List
from datetime import datetime

@dataclass
class Person:
    name: str
    age: int
    email: str = "unknown@example.com"
    friends: List[str] = field(default_factory=list)
    created_at: datetime = field(default_factory=datetime.now)
    
    def add_friend(self, friend_name: str):
        self.friends.append(friend_name)
    
    def get_friend_count(self) -> int:
        return len(self.friends)

# Creating instances
person1 = Person("Alice", 25, "alice@example.com")
person2 = Person("Bob", 30)

person1.add_friend("Charlie")
person1.add_friend("David")

print(person1)
print(f"Friend count: {person1.get_friend_count()}")
print(f"Created at: {person1.created_at}")
```

### Using __slots__ for Memory Efficiency
```python
class Point:
    __slots__ = ['x', 'y']  # Reduces memory usage
    
    def __init__(self, x, y):
        self.x = x
        self.y = y
    
    def __repr__(self):
        return f"Point({self.x}, {self.y})"

class Point3D:
    def __init__(self, x, y, z):
        self.x = x
        self.y = y
        self.z = z

# Compare memory usage
import sys

point2d = Point(1, 2)
point3d = Point3D(1, 2, 3)

print(f"Point2D size: {sys.getsizeof(point2d)} bytes")
print(f"Point3D size: {sys.getsizeof(point3d)} bytes")
```

## Design Patterns in OOP

### Singleton Pattern
```python
class Singleton:
    _instance = None
    
    def __new__(cls):
        if cls._instance is None:
            cls._instance = super().__new__(cls)
        return cls._instance
    
    def __init__(self):
        if not hasattr(self, 'initialized'):
            self.initialized = True
            self.data = "Singleton data"

# Testing singleton
s1 = Singleton()
s2 = Singleton()

print(f"Same instance: {s1 is s2}")
print(f"Data: {s1.data}")
```

### Factory Pattern
```python
from abc import ABC, abstractmethod

class Animal(ABC):
    @abstractmethod
    def speak(self):
        pass

class Dog(Animal):
    def speak(self):
        return "Woof!"

class Cat(Animal):
    def speak(self):
        return "Meow!"

class AnimalFactory:
    @staticmethod
    def create_animal(animal_type):
        if animal_type.lower() == "dog":
            return Dog()
        elif animal_type.lower() == "cat":
            return Cat()
        else:
            raise ValueError(f"Unknown animal type: {animal_type}")

# Using factory
factory = AnimalFactory()
dog = factory.create_animal("dog")
cat = factory.create_animal("cat")

print(dog.speak())
print(cat.speak())
```

## Best Practices

### OOP Best Practices
```python
# 1. Use meaningful class and method names
class CustomerDatabase:
    """Manages customer data with proper validation."""
    
    def __init__(self):
        self._customers = {}  # Protected attribute
    
    def add_customer(self, customer_id: str, name: str, email: str) -> None:
        """Add a new customer to the database."""
        if not self._validate_email(email):
            raise ValueError("Invalid email format")
        
        self._customers[customer_id] = {
            'name': name,
            'email': email,
            'created_at': datetime.now()
        }
    
    def _validate_email(self, email: str) -> bool:
        """Validate email format (private method)."""
        return '@' in email and '.' in email.split('@')[-1]

# 2. Follow SOLID principles
class Repository(ABC):
    """Interface for data repositories."""
    
    @abstractmethod
    def save(self, data):
        pass
    
    @abstractmethod
    def get(self, id):
        pass

class FileRepository(Repository):
    """File-based repository implementation."""
    
    def __init__(self, filename):
        self.filename = filename
    
    def save(self, data):
        with open(self.filename, 'a') as f:
            f.write(f"{data}\n")
    
    def get(self, id):
        try:
            with open(self.filename, 'r') as f:
                for line in f:
                    if line.startswith(f"{id}:"):
                        return line.strip()
        except FileNotFoundError:
            return None

# 3. Use composition over inheritance
class Logger:
    """Simple logging utility."""
    
    def log(self, message):
        print(f"LOG: {message}")

class DataService:
    """Handles data operations with logging."""
    
    def __init__(self, repository: Repository):
        self.repository = repository
        self.logger = Logger()
    
    def save_data(self, data):
        self.logger.log(f"Saving data: {data}")
        self.repository.save(data)

# 4. Use proper error handling
class DatabaseError(Exception):
    """Custom exception for database operations."""
    pass

class Database:
    """Database with proper error handling."""
    
    def __init__(self):
        self.connected = False
    
    def connect(self):
        try:
            # Simulate connection
            self.connected = True
            if not self.connected:
                raise DatabaseError("Connection failed")
        except DatabaseError as e:
            print(f"Connection error: {e}")
            raise

# 5. Use type hints
from typing import Optional, Dict, List

class UserManager:
    """Manages user operations with type hints."""
    
    def __init__(self):
        self._users: Dict[str, Dict[str, str]] = {}
    
    def add_user(self, user_id: str, name: str, email: str) -> None:
        """Add a new user."""
        if user_id in self._users:
            raise ValueError(f"User {user_id} already exists")
        
        self._users[user_id] = {
            'name': name,
            'email': email
        }
    
    def get_user(self, user_id: str) -> Optional[Dict[str, str]]:
        """Get user by ID."""
        return self._users.get(user_id)
    
    def list_users(self) -> List[str]:
        """List all user IDs."""
        return list(self._users.keys())
```

## Summary

Python OOP provides powerful features for organizing code:

**Core Concepts:**
- Classes and objects for encapsulation
- Inheritance for code reuse
- Polymorphism for flexible interfaces
- Encapsulation for data protection

**Advanced Features:**
- Abstract base classes for interfaces
- Mixins for multiple inheritance
- Magic methods for operator overloading
- Data classes for simple data structures

**Design Patterns:**
- Singleton for single instance
- Factory for object creation
- Repository for data access
- Observer for event handling

**Best Practices:**
- Follow SOLID principles
- Use composition over inheritance
- Implement proper error handling
- Use type hints for better documentation
- Apply meaningful naming conventions

Python's OOP features make it easy to create clean, maintainable, and extensible code while maintaining readability and simplicity.
