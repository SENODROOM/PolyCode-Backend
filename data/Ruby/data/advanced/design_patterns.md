# Design Patterns in Ruby

## Overview

Design patterns are reusable solutions to commonly occurring problems in software design. Ruby's dynamic nature makes it particularly well-suited for implementing many classic design patterns.

## Creational Patterns

### Singleton Pattern

Ensures a class has only one instance and provides global access to it.

```ruby
require 'singleton'

class DatabaseConnection
  include Singleton
  
  def initialize
    @connection = "Database connection established"
  end
  
  def execute_query(sql)
    "#{@connection}: Executing #{sql}"
  end
end

# Usage
conn1 = DatabaseConnection.instance
conn2 = DatabaseConnection.instance

puts conn1 == conn2  # => true
puts conn1.execute_query("SELECT * FROM users")
```

### Factory Method Pattern

Defines an interface for creating an object but lets subclasses decide which class to instantiate.

```ruby
class Animal
  def speak
    raise NotImplementedError, "Subclasses must implement speak"
  end
end

class Dog < Animal
  def speak
    "Woof!"
  end
end

class Cat < Animal
  def speak
    "Meow!"
  end
end

class AnimalFactory
  def self.create_animal(type)
    case type.downcase
    when 'dog'
      Dog.new
    when 'cat'
      Cat.new
    else
      raise ArgumentError, "Unknown animal type: #{type}"
    end
  end
end

# Usage
dog = AnimalFactory.create_animal('dog')
cat = AnimalFactory.create_animal('cat')

puts dog.speak  # => "Woof!"
puts cat.speak  # => "Meow!"
```

### Abstract Factory Pattern

Provides an interface for creating families of related objects without specifying their concrete classes.

```ruby
class GUIFactory
  def create_button
    raise NotImplementedError
  end
  
  def create_window
    raise NotImplementedError
  end
end

class WindowsFactory < GUIFactory
  def create_button
    WindowsButton.new
  end
  
  def create_window
    WindowsWindow.new
  end
end

class MacOSFactory < GUIFactory
  def create_button
    MacOSButton.new
  end
  
  def create_window
    MacOSWindow.new
  end
end

class WindowsButton
  def render
    "Windows button rendered"
  end
end

class MacOSButton
  def render
    "macOS button rendered"
  end
end

class WindowsWindow
  def render
    "Windows window rendered"
  end
end

class MacOSWindow
  def render
    "macOS window rendered"
  end
end

# Usage
def create_gui(factory)
  button = factory.create_button
  window = factory.create_window
  
  button.render
  window.render
end

puts "Windows GUI:"
create_gui(WindowsFactory.new)

puts "\nmacOS GUI:"
create_gui(MacOSFactory.new)
```

### Builder Pattern

Separates the construction of a complex object from its representation.

```ruby
class Computer
  attr_accessor :cpu, :memory, :storage, :graphics
  
  def initialize
    @cpu = nil
    @memory = nil
    @storage = nil
    @graphics = nil
  end
  
  def specs
    "CPU: #{@cpu}, Memory: #{@memory}GB, Storage: #{@storage}GB, Graphics: #{@graphics}"
  end
end

class ComputerBuilder
  def initialize
    @computer = Computer.new
  end
  
  def cpu(model)
    @computer.cpu = model
    self
  end
  
  def memory(gb)
    @computer.memory = gb
    self
  end
  
  def storage(gb)
    @computer.storage = gb
    self
  end
  
  def graphics(model)
    @computer.graphics = model
    self
  end
  
  def build
    @computer
  end
end

class ComputerDirector
  def self.build_gaming_pc
    ComputerBuilder.new
      .cpu("Intel i9-9900K")
      .memory(32)
      .storage(1000)
      .graphics("NVIDIA RTX 3080")
      .build
  end
  
  def self.build_office_pc
    ComputerBuilder.new
      .cpu("Intel i5-9400")
      .memory(16)
      .storage(512)
      .graphics("Intel UHD Graphics")
      .build
  end
end

# Usage
gaming_pc = ComputerDirector.build_gaming_pc
office_pc = ComputerDirector.build_office_pc

puts "Gaming PC: #{gaming_pc.specs}"
puts "Office PC: #{office_pc.specs}"
```

## Structural Patterns

### Adapter Pattern

Allows incompatible interfaces to work together.

```ruby
class LegacyPrinter
  def print_old_format(text)
    puts "OLD: #{text}"
  end
end

class ModernPrinter
  def print(text)
    puts "MODERN: #{text}"
  end
end

class PrinterAdapter
  def initialize(legacy_printer)
    @legacy_printer = legacy_printer
  end
  
  def print(text)
    converted_text = convert_to_old_format(text)
    @legacy_printer.print_old_format(converted_text)
  end
  
  private
  
  def convert_to_old_format(text)
    "[CONVERTED] #{text}"
  end
end

# Usage
legacy = LegacyPrinter.new
modern = ModernPrinter.new
adapter = PrinterAdapter.new(legacy)

modern.print("Hello World")
adapter.print("Hello World")
```

### Decorator Pattern

Adds new functionality to an object dynamically without altering its structure.

```ruby
class Coffee
  def cost
    2.50
  end
  
  def description
    "Simple coffee"
  end
end

class CoffeeDecorator
  def initialize(coffee)
    @coffee = coffee
  end
  
  def cost
    @coffee.cost
  end
  
  def description
    @coffee.description
  end
end

class MilkDecorator < CoffeeDecorator
  def cost
    @coffee.cost + 0.50
  end
  
  def description
    "#{@coffee.description}, milk"
  end
end

class SugarDecorator < CoffeeDecorator
  def cost
    @coffee.cost + 0.25
  end
  
  def description
    "#{@coffee.description}, sugar"
  end
end

class WhippedCreamDecorator < CoffeeDecorator
  def cost
    @coffee.cost + 1.00
  end
  
  def description
    "#{@coffee.description}, whipped cream"
  end
end

# Usage
coffee = Coffee.new
coffee = MilkDecorator.new(coffee)
coffee = SugarDecorator.new(coffee)
coffee = WhippedCreamDecorator.new(coffee)

puts "#{coffee.description}: $#{coffee.cost}"
```

### Proxy Pattern

Provides a surrogate or placeholder for another object to control access to it.

```ruby
class ExpensiveImage
  def initialize(filename)
    @filename = filename
    load_image
  end
  
  def display
    puts "Displaying #{@filename}"
  end
  
  private
  
  def load_image
    puts "Loading #{@filename} from disk (expensive operation)"
    sleep(1)  # Simulate expensive loading
  end
end

class ImageProxy
  def initialize(filename)
    @filename = filename
    @image = nil
  end
  
  def display
    unless @image
      @image = ExpensiveImage.new(@filename)
    end
    @image.display
  end
end

# Usage
proxy = ImageProxy.new("large_image.jpg")
proxy.display  # Loads and displays
proxy.display  # Uses cached version
```

### Composite Pattern

Compose objects into tree structures to represent part-whole hierarchies.

```ruby
class FileSystemComponent
  def add(component)
    raise NotImplementedError
  end
  
  def remove(component)
    raise NotImplementedError
  end
  
  def operation
    raise NotImplementedError
  end
end

class File < FileSystemComponent
  def initialize(name)
    @name = name
  end
  
  def operation
    puts "File: #{@name}"
  end
end

class Directory < FileSystemComponent
  def initialize(name)
    @name = name
    @children = []
  end
  
  def add(component)
    @children << component
  end
  
  def remove(component)
    @children.delete(component)
  end
  
  def operation
    puts "Directory: #{@name}"
    @children.each(&:operation)
  end
end

# Usage
root = Directory.new("root")
documents = Directory.new("documents")
images = Directory.new("images")

file1 = File.new("document1.txt")
file2 = File.new("image1.jpg")

documents.add(file1)
images.add(file2)

root.add(documents)
root.add(images)

root.operation
```

## Behavioral Patterns

### Observer Pattern

Defines a one-to-many dependency between objects so that when one object changes state, all its dependents are notified.

```ruby
class Observable
  def initialize
    @observers = []
  end
  
  def add_observer(observer)
    @observers << observer
  end
  
  def remove_observer(observer)
    @observers.delete(observer)
  end
  
  def notify_observers
    @observers.each { |observer| observer.update(self) }
  end
end

class WeatherStation < Observable
  attr_reader :temperature, :humidity
  
  def initialize
    super
    @temperature = 20
    @humidity = 50
  end
  
  def set_measurements(temperature, humidity)
    @temperature = temperature
    @humidity = humidity
    notify_observers
  end
end

class WeatherDisplay
  def update(weather_station)
    puts "Display: Temp=#{weather_station.temperature}°C, Humidity=#{weather_station.humidity}%"
  end
end

class WeatherLogger
  def update(weather_station)
    puts "Logger: Logging weather data - Temp: #{weather_station.temperature}, Humidity: #{weather_station.humidity}"
  end
end

# Usage
station = WeatherStation.new
display = WeatherDisplay.new
logger = WeatherLogger.new

station.add_observer(display)
station.add_observer(logger)

station.set_measurements(25, 60)
station.set_measurements(18, 45)
```

### Strategy Pattern

Defines a family of algorithms, encapsulates each one, and makes them interchangeable.

```ruby
class PaymentStrategy
  def pay(amount)
    raise NotImplementedError
  end
end

class CreditCardStrategy < PaymentStrategy
  def pay(amount)
    "Paid $#{amount} with credit card"
  end
end

class PayPalStrategy < PaymentStrategy
  def pay(amount)
    "Paid $#{amount} with PayPal"
  end
end

class BitcoinStrategy < PaymentStrategy
  def pay(amount)
    "Paid $#{amount} with Bitcoin"
  end
end

class ShoppingCart
  def initialize(payment_strategy)
    @payment_strategy = payment_strategy
    @items = []
  end
  
  def add_item(item)
    @items << item
  end
  
  def total
    @items.sum
  end
  
  def checkout
    amount = total
    @payment_strategy.pay(amount)
  end
  
  def set_payment_strategy(strategy)
    @payment_strategy = strategy
  end
end

# Usage
cart = ShoppingCart.new(CreditCardStrategy.new)
cart.add_item(100)
cart.add_item(50)

puts cart.checkout

cart.set_payment_strategy(PayPalStrategy.new)
puts cart.checkout
```

### Command Pattern

Encapsulates a request as an object, thereby letting you parameterize clients with different requests.

```ruby
class Command
  def execute
    raise NotImplementedError
  end
end

class Light
  def turn_on
    puts "Light is on"
  end
  
  def turn_off
    puts "Light is off"
  end
end

class LightOnCommand < Command
  def initialize(light)
    @light = light
  end
  
  def execute
    @light.turn_on
  end
end

class LightOffCommand < Command
  def initialize(light)
    @light = light
  end
  
  def execute
    @light.turn_off
  end
end

class RemoteControl
  def initialize
    @commands = []
  end
  
  def set_command(command)
    @commands << command
  end
  
  def press_button(index)
    command = @commands[index]
    return "No command at position #{index}" unless command
    command.execute
  end
end

# Usage
light = Light.new
remote = RemoteControl.new

remote.set_command(LightOnCommand.new(light))
remote.set_command(LightOffCommand.new(light))

remote.press_button(0)  # Light is on
remote.press_button(1)  # Light is off
```

### State Pattern

Allows an object to change its behavior when its internal state changes.

```ruby
class OrderState
  def process_order
    raise NotImplementedError
  end
end

class PendingState < OrderState
  def process_order
    "Order is being processed"
  end
end

class ShippedState < OrderState
  def process_order
    "Order has been shipped"
  end
end

class DeliveredState < OrderState
  def process_order
    "Order has been delivered"
  end
end

class CancelledState < OrderState
  def process_order
    "Order has been cancelled"
  end
end

class Order
  def initialize
    @state = PendingState.new
  end
  
  def process
    @state.process_order
  end
  
  def ship
    @state = ShippedState.new
  end
  
  def deliver
    @state = DeliveredState.new
  end
  
  def cancel
    @state = CancelledState.new
  end
end

# Usage
order = Order.new
puts order.process  # Order is being processed

order.ship
puts order.process  # Order has been shipped

order.deliver
puts order.process  # Order has been delivered
```

## Ruby-Specific Patterns

### Module Mixin Pattern

Ruby's modules provide a way to share functionality between classes.

```ruby
module Validatable
  def valid?
    validate
    errors.empty?
  end
  
  def errors
    @errors ||= []
  end
  
  private
  
  def validate
    errors.clear
  end
end

module Timestampable
  def created_at
    @created_at ||= Time.now
  end
  
  def updated_at
    @updated_at ||= Time.now
  end
  
  def touch
    @updated_at = Time.now
  end
end

class User
  include Validatable
  include Timestampable
  
  attr_accessor :name, :email
  
  def initialize(name, email)
    @name = name
    @email = email
  end
  
  private
  
  def validate
    errors << "Name can't be blank" if name.nil? || name.strip.empty?
    errors << "Email is invalid" unless email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
  end
end

# Usage
user = User.new("", "invalid-email")
puts user.valid?  # => false
puts user.errors  # => ["Name can't be blank", "Email is invalid"]
```

### DSL (Domain-Specific Language) Pattern

Create a mini-language for a specific domain.

```ruby
class RouteBuilder
  def initialize
    @routes = []
  end
  
  def get(path, &block)
    @routes << { method: :GET, path: path, handler: block }
  end
  
  def post(path, &block)
    @routes << { method: :POST, path: path, handler: block }
  end
  
  def match(method, path, &block)
    @routes << { method: method, path: path, handler: block }
  end
  
  def draw(&block)
    instance_eval(&block)
  end
  
  def routes
    @routes
  end
end

# Usage
builder = RouteBuilder.new
builder.draw do
  get '/' do
    "Welcome to the home page"
  end
  
  get '/users' do
    "User listing"
  end
  
  post '/users' do
    "Create user"
  end
  
  match '/about', :GET do
    "About page"
  end
end

builder.routes.each do |route|
  puts "#{route[:method]} #{route[:path]} => #{route[:handler].call}"
end
```

## Practice Exercises

### Exercise 1: Pattern Library
Create a pattern library that:
- Implements multiple design patterns
- Provides examples for each pattern
- Includes usage guidelines
- Demonstrates pattern combinations

### Exercise 2: E-commerce System
Build an e-commerce system using:
- Factory pattern for product creation
- Strategy pattern for payment processing
- Observer pattern for inventory updates
- Builder pattern for order processing

### Exercise 3: Game Framework
Create a game framework with:
- Command pattern for actions
- State pattern for game states
- Observer pattern for event handling
- Singleton pattern for game manager

### Exercise 4: Document Processor
Implement a document processor with:
- Composite pattern for document structure
- Strategy pattern for different formats
- Visitor pattern for document operations
- Decorator pattern for document features

---

**Ready to explore more advanced Ruby topics? Let's continue! 🎯**
