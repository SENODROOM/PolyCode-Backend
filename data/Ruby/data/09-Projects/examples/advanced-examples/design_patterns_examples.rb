# Design Patterns Examples
# Demonstrating various design patterns in Ruby

puts "=== CREATIONAL PATTERNS ==="

# Singleton Pattern
require 'singleton'

class DatabaseConnection
  include Singleton
  
  def initialize
    @connection = "Database connection established at #{Time.now}"
  end
  
  def execute_query(sql)
    puts "#{@connection}: Executing #{sql}"
    "Query result for #{sql}"
  end
end

puts "Singleton Pattern:"
conn1 = DatabaseConnection.instance
conn2 = DatabaseConnection.instance
puts "Same instance? #{conn1 == conn1}"
puts conn1.execute_query("SELECT * FROM users")

puts "\n=== FACTORY METHOD PATTERN ==="

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

class Bird < Animal
  def speak
    "Tweet!"
  end
end

class AnimalFactory
  def self.create_animal(type)
    case type.downcase
    when 'dog'
      Dog.new
    when 'cat'
      Cat.new
    when 'bird'
      Bird.new
    else
      raise ArgumentError, "Unknown animal type: #{type}"
    end
  end
end

puts "Factory Method Pattern:"
dog = AnimalFactory.create_animal('dog')
cat = AnimalFactory.create_animal('cat')
bird = AnimalFactory.create_animal('bird')

puts "Dog says: #{dog.speak}"
puts "Cat says: #{cat.speak}"
puts "Bird says: #{bird.speak}"

puts "\n=== BUILDER PATTERN ==="

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

puts "Builder Pattern:"
gaming_pc = ComputerDirector.build_gaming_pc
office_pc = ComputerDirector.build_office_pc

puts "Gaming PC: #{gaming_pc.specs}"
puts "Office PC: #{office_pc.specs}"

puts "\n=== STRUCTURAL PATTERNS ==="

# Adapter Pattern
class LegacyPrinter
  def print_old_format(text)
    puts "OLD FORMAT: #{text}"
  end
end

class ModernPrinter
  def print(text)
    puts "MODERN FORMAT: #{text}"
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

puts "Adapter Pattern:"
legacy = LegacyPrinter.new
modern = ModernPrinter.new
adapter = PrinterAdapter.new(legacy)

modern.print("Hello World")
adapter.print("Hello World")

puts "\n=== DECORATOR PATTERN ==="

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

puts "Decorator Pattern:"
coffee = Coffee.new
coffee = MilkDecorator.new(coffee)
coffee = SugarDecorator.new(coffee)
coffee = WhippedCreamDecorator.new(coffee)

puts "#{coffee.description}: $#{coffee.cost}"

puts "\n=== COMPOSITE PATTERN ==="

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

puts "Composite Pattern:"
root = Directory.new("root")
documents = Directory.new("documents")
images = Directory.new("images")

file1 = File.new("document1.txt")
file2 = File.new("image1.jpg")
file3 = File.new("presentation.pptx")

documents.add(file1)
images.add(file2)
documents.add(file3)

root.add(documents)
root.add(images)

root.operation

puts "\n=== BEHAVIORAL PATTERNS ==="

# Observer Pattern
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

puts "Observer Pattern:"
station = WeatherStation.new
display = WeatherDisplay.new
logger = WeatherLogger.new

station.add_observer(display)
station.add_observer(logger)

station.set_measurements(25, 60)
station.set_measurements(18, 45)

puts "\n=== STRATEGY PATTERN ==="

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

puts "Strategy Pattern:"
cart = ShoppingCart.new(CreditCardStrategy.new)
cart.add_item(100)
cart.add_item(50)

puts cart.checkout

cart.set_payment_strategy(PayPalStrategy.new)
puts cart.checkout

puts "\n=== COMMAND PATTERN ==="

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

puts "Command Pattern:"
light = Light.new
remote = RemoteControl.new

remote.set_command(LightOnCommand.new(light))
remote.set_command(LightOffCommand.new(light))

remote.press_button(0)  # Light is on
remote.press_button(1)  # Light is off

puts "\n=== STATE PATTERN ==="

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

puts "State Pattern:"
order = Order.new
puts order.process  # Order is being processed

order.ship
puts order.process  # Order has been shipped

order.deliver
puts order.process  # Order has been delivered

puts "\n=== RUBY-SPECIFIC PATTERNS ==="

# Module Mixin Pattern
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

puts "Module Mixin Pattern:"
user = User.new("", "invalid-email")
puts "User valid? #{user.valid?}"
puts "Errors: #{user.errors.join(', ')}"

puts "\n=== DSL (Domain-Specific Language) PATTERN ==="

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

puts "DSL Pattern:"
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

puts "Routes defined:"
builder.routes.each do |route|
  puts "#{route[:method]} #{route[:path]} => #{route[:handler].call}"
end

puts "\n=== DESIGN PATTERNS SUMMARY ==="
puts "- Creational Patterns: Singleton, Factory Method, Builder"
puts "- Structural Patterns: Adapter, Decorator, Composite"
puts "- Behavioral Patterns: Observer, Strategy, Command, State"
puts "- Ruby-Specific Patterns: Module Mixin, DSL"
puts "\nAll examples demonstrate how design patterns solve common problems in Ruby!"
