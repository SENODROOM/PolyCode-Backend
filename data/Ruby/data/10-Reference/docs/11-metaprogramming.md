# Metaprogramming in Ruby

## 🎯 Overview

Metaprogramming is one of Ruby's most powerful features - writing code that writes or modifies other code. Ruby's dynamic nature makes metaprogramming incredibly expressive and useful for creating flexible, reusable code.

## 🔬 What is Metaprogramming?

Metaprogramming is programming that treats programs as data. In Ruby, this means:

- **Code that generates code** - Creating methods, classes, and modules dynamically
- **Code that modifies code** - Changing existing classes and methods at runtime
- **Code that analyzes code** - Inspecting and understanding program structure
- **Code that extends language** - Adding new language features

## 🛠️ Core Metaprogramming Concepts

### 1. Dynamic Method Definition

Ruby allows you to define methods programmatically:

```ruby
class DynamicMethods
  def self.create_method(method_name)
    define_method(method_name) do |arg|
      "Dynamically created method #{method_name} called with #{arg}"
    end
  end
end

# Create methods dynamically
DynamicMethods.create_method(:greet)
DynamicMethods.create_method(:calculate)
DynamicMethods.create_method(:process)

obj = DynamicMethods.new
puts obj.greet("Alice")        # => "Dynamically created method greet called with Alice"
puts obj.calculate(42)         # => "Dynamically created method calculate called with 42"
puts obj.process("data")        # => "Dynamically created method process called with data"
```

### 2. Method Missing

Handle calls to undefined methods:

```ruby
class FlexibleObject
  def method_missing(method_name, *args, &block)
    if method_name.to_s.start_with?("find_by_")
      attribute = method_name.to_s.sub("find_by_", "")
      "Finding by #{attribute}: #{args.first}"
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.start_with?("find_by_") || super
  end
end

user = FlexibleObject.new
puts user.find_by_name("Alice")    # => "Finding by name: Alice"
puts user.find_by_email("test@test") # => "Finding by email: test@test"
puts user.unknown_method            # => NoMethodError
```

### 3. Class Evaluation

Execute code in the context of a class:

```ruby
class DynamicClass
  def self.add_class_method(method_name, &block)
    class_eval do
      define_singleton_method(method_name, &block)
    end
  end
  
  def self.add_instance_method(method_name, &block)
    define_method(method_name, &block)
  end
end

# Add class methods
DynamicClass.add_class_method(:class_info) do
  "This is a class method"
end

# Add instance methods
DynamicClass.add_instance_method(:instance_info) do
  "This is an instance method"
end

puts DynamicClass.class_info        # => "This is a class method"
obj = DynamicClass.new
puts obj.instance_info           # => "This is an instance method"
```

### 4. Instance Evaluation

Execute code in the context of an object:

```ruby
class Configuration
  def initialize(config_hash)
    config_hash.each do |key, value|
      instance_eval do
        define_method(key) { value }
      end
    end
  end
end

config = Configuration.new(
  host: "localhost",
  port: 3000,
  database: "myapp_development"
)

puts config.host     # => "localhost"
puts config.port     # => 3000
puts config.database # => "myapp_development"
```

## 🎨 Advanced Metaprogramming Techniques

### 1. Domain-Specific Languages (DSLs)

Create mini-languages for specific domains:

```ruby
class WebAppBuilder
  def self.create_app(&block)
    app = Class.new
    app_class_eval(&block)
    app
  end
  
  def self.route(path, &handler)
    define_method("route_#{path.gsub('/', '_')}") do |request|
      handler.call(request)
    end
  end
  
  def self.middleware(name, &block)
    define_method("#{name}_middleware") do |app|
      block.call(app)
    end
  end
end

# Create a web application
MyApp = WebAppBuilder.create_app do
  route "/home" do |request|
    "Welcome to the home page!"
  end
  
  route "/about" do |request|
    "About our application"
  end
  
  middleware :auth do |app|
    # Authentication middleware
    lambda do |request|
      if request[:authenticated]
        app.call(request)
      else
        "Please log in"
      end
    end
  end
end

app = MyApp.new
puts app.route_home(request: { authenticated: true })  # => "Welcome to the home page!"
puts app.route_about(request: { authenticated: false }) # => "Please log in"
```

### 2. Delegation and Forwarding

Automatically forward method calls to other objects:

```ruby
class Delegator
  def initialize(target)
    @target = target
  end
  
  def method_missing(method_name, *args, &block)
    if @target.respond_to?(method_name)
      @target.send(method_name, *args, &block)
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    @target.respond_to?(method_name) || super
  end
end

class Logger
  def log(message)
    puts "[LOG] #{message}"
  end
end

class DataProcessor
  def process(data)
    "Processed: #{data}"
  end
end

# Create a delegating object
processor = DataProcessor.new
logger = Logger.new
delegating_processor = Delegator.new(processor)

# Add logging capability
class << delegating_processor
  def method_missing(method_name, *args, &block)
    logger.log("Calling #{method_name} with #{args}")
    @target.send(method_name, *args, &block)
  end
end

puts delegating_processor.process("test data")
# Output:
# [LOG] Calling process with ["test data"]
# Processed: test data
```

### 3. Attribute Accessors and Validators

Generate attribute methods dynamically:

```ruby
module ValidatedAttributes
  def self.attr_validated(attr_name, validation_proc = nil)
    define_method(attr_name) do
      instance_variable_get("@#{attr_name}")
    end
    
    define_method("#{attr_name}=") do |value|
      if validation_proc && !validation_proc.call(value)
        raise ArgumentError, "Invalid value for #{attr_name}"
      end
      instance_variable_set("@#{attr_name}", value)
    end
    
    define_method("#{attr_name}_valid?") do
      value = instance_variable_get("@#{attr_name}")
      validation_proc ? validation_proc.call(value) : true
    end
  end
end

class User
  extend ValidatedAttributes
  
  attr_validated :email, ->(email) { email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i) }
  attr_validated :age, ->(age) { age.is_a?(Integer) && age >= 0 && age <= 150 }
  attr_validated :name, ->(name) { name.is_a?(String) && !name.empty? }
end

user = User.new
user.email = "test@example.com"  # Valid
user.age = 25                   # Valid
user.name = "Alice"                # Valid

puts user.email_valid?  # => true
puts user.age_valid?    # => true
puts user.name_valid?    # => true

begin
  user.email = "invalid-email"
rescue ArgumentError => e
  puts e.message  # => "Invalid value for email"
end
```

## 🔧 Practical Metaprogramming Examples

### 1. Active Record Pattern

Implement something like ActiveRecord's dynamic finders:

```ruby
class Model
  def self.inherited(subclass)
    subclass.define_singleton_method(:find) do |id|
      objects = subclass.all_objects
      objects.find { |obj| obj[:id] == id }
    end
    
    subclass.define_singleton_method(:find_by) do |attribute, value|
      objects = subclass.all_objects
      objects.select { |obj| obj[attribute] == value }
    end
    
    subclass.define_singleton_method(:where) do |conditions|
      objects = subclass.all_objects
      objects.select do |obj|
        conditions.all? { |attr, val| obj[attr] == val }
      end
    end
  end
  
  def self.all_objects
    @objects ||= []
  end
  
  def self.create(attributes)
    new_obj = new(attributes)
    all_objects << new_obj
    new_obj
  end
end

class User < Model
end

class Product < Model
end

# Create some data
User.create(id: 1, name: "Alice", email: "alice@test.com")
User.create(id: 2, name: "Bob", email: "bob@test.com")
Product.create(id: 1, name: "Laptop", price: 999.99)
Product.create(id: 2, name: "Mouse", price: 29.99)

# Use dynamic finders
user = User.find(1)
puts user[:name]  # => "Alice"

users = User.find_by(:name, "Alice")
puts users.map { |u| u[:email] }  # => ["alice@test.com"]

products = Product.where(price: 29.99)
puts products.map { |p| p[:name] }  # => ["Mouse"]
```

### 2. Configuration Builder Pattern

Create fluent configuration builders:

```ruby
class ConfigurationBuilder
  def initialize
    @config = {}
  end
  
  def method_missing(method_name, *args, &block)
    if method_name.to_s.end_with?("=")
      key = method_name.to_s.chomp("=").to_sym
      @config[key] = args.first
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.end_with?("=") || super
  end
  
  def build
    @config
  end
end

# Usage
config = ConfigurationBuilder.new
config.database = "postgresql"
config.host = "localhost"
config.port = 5432
config.username = "myuser"
config.password = "mypassword"
config.pool_size = 10

final_config = config.build
puts final_config
# => {
#   :database=>"postgresql",
#   :host=>"localhost",
#   :port=>5432,
#   :username=>"myuser",
#   :password=>"mypassword",
#   :pool_size=>10
# }
```

### 3. API Wrapper Generator

Automatically generate API wrapper methods:

```ruby
class APIWrapper
  def self.define_api_methods(endpoints)
    endpoints.each do |endpoint|
      method_name = endpoint[:name]
      url = endpoint[:url]
      method = endpoint[:method] || :get
      
      define_method(method_name) do |params = {}|
        # Simulate API call
        puts "Making #{method.upcase} request to #{url}"
        puts "Parameters: #{params}"
        
        # Simulate response
        case method_name
        when :get_user
          { id: params[:id], name: "User #{params[:id]}", email: "user#{params[:id]}@test.com" }
        when :create_user
          { id: rand(1000..9999), success: true, message: "User created" }
        when :list_users
          { users: (1..5).map { |i| { id: i, name: "User #{i}" } } }
        else
          { error: "Unknown endpoint" }
        end
      end
    end
  end
end

# Define API endpoints
APIWrapper.define_api_methods([
  { name: :get_user, url: "/api/users/:id", method: :get },
  { name: :create_user, url: "/api/users", method: :post },
  { name: :list_users, url: "/api/users", method: :get },
  { name: :delete_user, url: "/api/users/:id", method: :delete }
])

# Use the generated API
api = APIWrapper.new
user = api.get_user(id: 123)
puts user  # => { id: 123, name: "User 123", email: "user123@test.com" }

result = api.create_user(name: "Alice", email: "alice@test.com")
puts result  # => { id: 4567, success: true, message: "User created" }

users = api.list_users
puts users  # => { users: [{ id: 1, name: "User 1" }, ...] }
```

## ⚡ Performance Considerations

### Metaprogramming Overhead

Metaprogramming can impact performance:

```ruby
# Regular method definition
class Regular
  def hello
    "Hello, World!"
  end
end

# Metaprogrammed method
class Meta
  define_method(:hello) do
    "Hello, World!"
  end
end

# Benchmark
require 'benchmark'

Benchmark.bm do |x|
  x.report("Regular") do
    1000000.times { Regular.new.hello }
  end
  
  x.report("Metaprogrammed") do
    1000000.times { Meta.new.hello }
  end
end

# Typical results:
# Regular        0.050000
# Metaprogrammed  0.080000
```

### Optimization Tips

1. **Cache generated methods** - Don't regenerate unnecessarily
2. **Use metaprogramming sparingly** - Only when it provides real value
3. **Profile your code** - Measure actual performance impact
4. **Consider alternatives** - Sometimes regular code is clearer and faster

## 🛡️ Security Considerations

### Safe Metaprogramming

Always validate user input in metaprogramming:

```ruby
class SafeEvaluator
  ALLOWED_METHODS = %w[upcase downcase reverse length]
  
  def self.safe_eval(object, method_name, *args)
    unless ALLOWED_METHODS.include?(method_name.to_s)
      raise SecurityError, "Method #{method_name} is not allowed"
    end
    
    object.send(method_name, *args)
  end
end

# Usage
SafeEvaluator.safe_eval("hello", :upcase)  # => "HELLO"
SafeEvaluator.safe_eval("hello", :eval)   # => SecurityError
```

### Avoiding eval When Possible

Prefer safer alternatives to `eval`:

```ruby
# Dangerous - evaluates any code
def dangerous_eval(code)
  eval(code)
end

# Safer - only allows specific operations
def safe_operation(operation, value)
  case operation
  when :upcase
    value.upcase
  when :downcase
    value.downcase
  when :reverse
    value.reverse
  else
    raise ArgumentError, "Unknown operation: #{operation}"
  end
end
```

## 🎯 Best Practices

### 1. Use Metaprogramming Intentionally
- Have clear reasons for using metaprogramming
- Document the metaprogramming behavior
- Provide fallbacks for when metaprogramming fails

### 2. Keep It Simple
- Don't over-engineer metaprogramming solutions
- Prefer readability over cleverness
- Test metaprogrammed code thoroughly

### 3. Consider Alternatives
- Regular methods are often clearer
- Inheritance and modules can solve many problems
- Use metaprogramming when it provides unique value

### 4. Document Everything
- Explain what dynamic methods do
- Provide examples of generated methods
- Document the metaprogramming API

## 🚀 Advanced Topics

### 1. Method Combinations

Create methods that combine existing ones:

```ruby
class MethodCombiner
  def self.combine_methods(base_methods, prefix = "combined_")
    base_methods.combination(2).each do |method1, method2|
      combined_name = "#{prefix}#{method1}_and_#{method2}"
      define_method(combined_name) do |*args|
        send(method1, *args) + send(method2, *args)
      end
    end
  end
end

class Calculator
  def add(a, b) a + b end
  def multiply(a, b) a * b end
  def subtract(a, b) a - b end
end

Calculator.extend(MethodCombiner)
Calculator.combine_methods([:add, :multiply, :subtract])

calc = Calculator.new
puts calc.comined_add_and_multiply(2, 3)  # => 25 (2+3)*2
puts calc.comined_multiply_and_subtract(6, 2) # => 4 6-2
```

### 2. Runtime Class Modification

Modify classes at runtime:

```ruby
class RuntimeModifier
  def self.add_method_to_class(target_class, method_name, &block)
    target_class.define_method(method_name, &block)
  end
  
  def self.add_class_method_to_class(target_class, method_name, &block)
    target_class.define_singleton_method(method_name, &block)
  end
end

class String
  def self.add_custom_methods
    RuntimeModifier.add_method_to_class(self, :word_count) do
      split(/\s+/).length
    end
    
    RuntimeModifier.add_class_method_to_class(self, :analyze) do |text|
      {
        word_count: text.word_count,
        char_count: text.length,
        vowel_count: text.count("aeiouAEIOU")
      }
    end
  end
end

String.add_custom_methods

puts "Hello Ruby".word_count  # => 2
puts "Hello Ruby".char_count  # => 10
puts String.analyze("Hello Ruby")  # => {:word_count=>2, :char_count=>10, :vowel_count=>4}
```

## 🎓 Exercises

### Beginner Exercises

1. **Dynamic Method Creator**: Create a class that can add methods dynamically
2. **Simple DSL**: Build a configuration DSL using method_missing
3. **Attribute Generator**: Create a module that generates attribute accessors

### Intermediate Exercises

1. **API Wrapper**: Build a simple API wrapper generator
2. **Validation Framework**: Create a validation attribute system
3. **Query Builder**: Implement a dynamic query builder

### Advanced Exercises

1. **ORM Features**: Build basic ActiveRecord-like features
2. **Template Engine**: Create a simple template engine
3. **Code Generator**: Build a code generation tool

---

## 🎯 Summary

Metaprogramming is one of Ruby's most powerful features, enabling:

- **Dynamic code generation** - Create methods and classes at runtime
- **Domain-specific languages** - Build expressive APIs for specific domains
- **Flexible architectures** - Create adaptable and extensible systems
- **Code analysis** - Inspect and modify program structure

Use metaprogramming wisely - it's powerful but should be used intentionally and documented clearly. When used properly, it can make your Ruby code incredibly expressive and flexible!
