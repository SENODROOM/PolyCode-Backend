# Advanced Ruby Features

## 🎯 Overview

Ruby is packed with advanced features that make it incredibly powerful and expressive. This guide covers the sophisticated features that separate Ruby beginners from Ruby experts.

## 🔬 Advanced Data Structures

### 1. Sets and Set Operations

Ruby's Set class provides powerful collection operations:

```ruby
require 'set'

# Create sets
set1 = Set.new([1, 2, 3, 4, 5])
set2 = Set.new([4, 5, 6, 7, 8])

# Set operations
union = set1 | set2          # Union: {1, 2, 3, 4, 5, 6, 7, 8}
intersection = set1 & set2     # Intersection: {4, 5}
difference = set1 - set2       # Difference: {1, 2, 3}
symmetric_diff = set1 ^ set2   # Symmetric difference: {1, 2, 3, 6, 7, 8}

# Set methods
puts set1.include?(3)    # => true
puts set1.add?(3)       # => false
puts set1.empty?        # => false
puts set1.size          # => 5

# Set operations for data processing
users = Set.new(["alice", "bob", "charlie"])
admins = Set.new(["alice", "david"])

regular_users = users - admins
puts regular_users  # => #<Set: {"bob", "charlie"}>
```

### 2. Structs for Data Containers

Structs provide lightweight data structures:

```ruby
# Define a struct
Person = Struct.new(:name, :age, :email, :address)

# Create instances
person1 = Person.new("Alice", 30, "alice@test.com", "123 Main St")
person2 = Person.new("Bob", 25, "bob@test.com", "456 Oak Ave")

# Access attributes
puts person1.name      # => "Alice"
puts person1.age       # => 30
puts person1.email     # => "alice@test.com"

# Struct with default values
User = Struct.new(:name, :email, :role) do
  def admin?
    role == :admin
  end
end

user = User.new("Alice", "alice@test.com", :admin)
puts user.admin?  # => true

# OpenStruct for dynamic attributes
require 'ostruct'
config = OpenStruct.new(
  host: "localhost",
  port: 3000,
  database: "myapp"
)

puts config.host      # => "localhost"
config.new_field = "value"  # Dynamically add new field
puts config.new_field  # => "value"
```

### 3. Ranges and Range Operations

Ranges are more powerful than they appear:

```ruby
# Numeric ranges
numbers = 1..10          # Inclusive range
numbers_exclusive = 1...10   # Exclusive range

# Character ranges
letters = 'a'..'z'        # All lowercase letters
vowels = 'aeiou'         # Specific characters

# Range methods
puts numbers.begin          # => 1
puts numbers.end            # => 10
puts numbers.include?(5)      # => true
puts numbers.include?(10)     # => true
puts numbers_exclusive.include?(10) # => false

# Range operations
numbers.each { |n| print n }  # 12345678910
numbers.to_a                    # => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
numbers.step(2) { |n| print n }  # 13579
numbers.select(&:even?)              # => [2, 4, 6, 8, 10]

# Advanced range usage
def generate_password(length = 8)
  chars = ('a'..'z').to_a + ('A'..'Z').to_a + (0..9).to_a
  (1..length).map { chars.sample }.join
end

puts generate_password(12)  # Random 12-character password
```

## 🔀 Advanced Method Techniques

### 1. Splat and Double Splat Operators

Master splat operators for flexible method signatures:

```ruby
# Single splat - collects arguments into array
def sum_all(*numbers)
  numbers.sum
end

puts sum_all(1, 2, 3, 4, 5)  # => 15
puts sum_all([1, 2, 3, 4, 5])  # => 15 (array unpacked)

# Double splat - unpacks array into arguments
def process_data(name, age, city)
  "#{name} is #{age} years old and lives in #{city}"
end

data = ["Alice", 30, "New York"]
puts process_data(*data)  # => "Alice is 30 years old and lives in New York"

# Splat in method definitions
def flexible_method(required, *optional, keyword:)
  puts "Required: #{required}"
  puts "Optional: #{optional}" if optional.any?
  puts "Keyword: #{keyword}"
end

flexible_method("test", "opt1", "opt2", keyword: "value")
# Output:
# Required: test
# Optional: ["opt1", "opt2"]
# Keyword: value

# Splat for destructuring
def destructure_array(array)
  first, *middle, last = array
  puts "First: #{first}"
  puts "Middle: #{middle}" if middle.any?
  puts "Last: #{last}"
end

destructure_array([1, 2, 3, 4, 5])
# Output:
# First: 1
# Middle: [2, 3, 4]
# Last: 5
```

### 2. Keyword Arguments and Defaults

Advanced keyword argument patterns:

```ruby
# Keyword arguments with defaults
def create_user(name:, email:, age: 18, role: :user, active: true)
  {
    name: name,
    email: email,
    age: age,
    role: role,
    active: active
  }
end

user = create_user(
  name: "Alice",
  email: "alice@test.com",
  age: 25
)

puts user
# => {
#   :name=>"Alice",
#   :email=>"alice@test.com",
#   :age=>25,
#   :role=>:user,
#   :active=>true
# }

# Double splat for keyword arguments
def process_options(**options)
  options.each do |key, value|
    puts "#{key}: #{value}"
  end
end

process_options(host: "localhost", port: 3000, debug: true)
# Output:
# host: localhost
# port: 3000
# debug: true

# Combining positional and keyword arguments
def complex_method(pos1, pos2, *splat, kw1:, kw2: nil, **kwargs)
  puts "Positional: #{pos1}, #{pos2}"
  puts "Splat: #{splat}" if splat.any?
  puts "Required keyword: #{kw1}"
  puts "Optional keyword: #{kw2}" if kw2
  puts "Extra keywords: #{kwargs}" if kwargs.any?
end

complex_method(
  "a", "b", "c", "d",
  kw1: "required",
  extra1: "value1",
  extra2: "value2"
)
```

### 3. Blocks, Procs, and Lambdas

Deep understanding of Ruby's closures:

```ruby
# Blocks are not objects, procs and lambdas are
def method_with_block
  yield if block_given?
end

# Create procs
my_proc = Proc.new { |x| x * 2 }
my_lambda = ->(x) { x * 2 }

# Differences between procs and lambdas
def test_closure(closure)
  closure.call(5)
rescue ArgumentError => e
  puts "Error: #{e.message}"
end

# Lambda checks argument count
test_closure(->(x) { x * 2 })      # Works
test_closure(->(x, y) { x + y })    # Works

# Proc doesn't check argument count
test_closure(Proc.new { |x, y| x + y })  # Works even with wrong args

# Return behavior differences
def test_return_behavior
  lambda_proc = -> { return "from lambda" }
  regular_proc = Proc.new { return "from proc" }
  
  result1 = lambda_proc.call
  result2 = regular_proc.call
  
  puts "Lambda result: #{result1}"  # => "from lambda"
  puts "Proc result: #{result2}"    # => "from proc"
  
  "from method"
end

puts test_return_behavior  # => "from method"
```

## 🔧 Advanced String Manipulation

### 1. Regular Expressions in Depth

Master Ruby's regex capabilities:

```ruby
# Named captures
text = "John Doe, age: 30, email: john@test.com"
pattern = /(?<name>\w+ \w+), age: (?<age>\d+), email: (?<email>[\w\.-]+@\w+\.\w+)/

match = text.match(pattern)
puts match[:name]   # => "John Doe"
puts match[:age]    # => "30"
puts match[:email]  # => "john@test.com"

# Regex modifiers and options
text = "Ruby is FUN and fun!"
pattern = /fun/i  # Case insensitive
puts text.scan(pattern)  # => ["FUN", "fun"]

# Lookaheads and lookbehinds
text = "password123 and admin456"
password_pattern = /(?<=password)\d+(?=\D|$)/
admin_pattern = /(?<=admin)\d+(?=\D|$)/

puts text.scan(password_pattern)  # => ["123"]
puts text.scan(admin_pattern)     # => ["456"]

# Complex regex for data extraction
log_pattern = /
  (?<timestamp>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})
  \s+
  (?<level>\w+)
  \s+
  (?<message>.*)
/x

log_entry = "2023-12-25 10:30:45 ERROR Database connection failed"
match = log_entry.match(log_pattern)

puts match[:timestamp]  # => "2023-12-25 10:30:45"
puts match[:level]      # => "ERROR"
puts match[:message]    # => "Database connection failed"
```

### 2. Advanced String Methods

Leverage Ruby's powerful string methods:

```ruby
# String interpolation and formatting
name = "Alice"
age = 30
template = "Hello, %{name}! You are %{age} years old."
result = template % { name: name, age: age }
puts result  # => "Hello, Alice! You are 30 years old."

# String manipulation
text = "  Hello, World!  "
puts text.strip        # => "Hello, World!"
puts text.lstrip       # => "Hello, World!  "
puts text.rstrip       # => "  Hello, World!"
puts text.squeeze      # => " Helo, World!" (removes consecutive duplicates)

# Advanced string methods
def analyze_string(text)
  {
    original: text,
    cleaned: text.strip.squeeze,
    words: text.split(/\s+/),
    word_count: text.split(/\s+/).length,
    char_count: text.length,
    vowels: text.count("aeiouAEIOU"),
    consonants: text.count("bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ"),
    reversed: text.reverse,
    palindrome: text.downcase == text.downcase.reverse
  }
end

analysis = analyze_string("Ruby Programming")
puts analysis
# => {
#   :original=>"Ruby Programming",
#   :cleaned=>"Ruby Programing",
#   :words=>["Ruby", "Programming"],
#   :word_count=>2,
#   :char_count=>17,
#   :vowels=>5,
#   :consonants=>12,
#   :reversed=>"gnimmargorP ybuR",
#   :palindrome=>false
# }
```

## 📊 Advanced Array Operations

### 1. Functional Array Methods

Master functional programming with arrays:

```ruby
# Map, filter, reduce
numbers = (1..10).to_a

squared = numbers.map { |n| n ** 2 }
evens = numbers.select { |n| n.even? }
sum = numbers.reduce(0) { |acc, n| acc + n }
product = numbers.reduce(1) { |acc, n| acc * n }

puts squared   # => [1, 4, 9, 16, 25, 36, 49, 64, 81, 100]
puts evens      # => [2, 4, 6, 8, 10]
puts sum        # => 55
puts product    # => 3628800

# Advanced reduce operations
numbers.reduce({}) do |hash, n|
  hash[n] = n ** 2
  hash
end
# => {1=>1, 2=>4, 3=>9, 4=>16, 5=>25, 6=>36, 7=>49, 8=>64, 9=>81, 10=>100}

# Group by and index by
words = ["apple", "banana", "cherry", "apricot", "blueberry"]
grouped = words.group_by { |word| word[0] }
puts grouped
# => {"a"=>["apple", "apricot"], "b"=>["banana", "blueberry"], "c"=>["cherry"]}

indexed = words.index_by { |word| word.length }
puts indexed
# => {5=>["apple", "grape"], 6=>["banana", "orange"], 7=>["cherry"]}
```

### 2. Advanced Array Combinations

Generate combinations and permutations:

```ruby
require 'set'

# Combinations
items = [1, 2, 3, 4]
combinations = items.combination(2).to_a
puts combinations
# => [[1, 2], [1, 3], [1, 4], [2, 3], [2, 4], [3, 4]]

# Permutations
permutations = items.permutation(2).to_a
puts permutations
# => [[1, 2], [1, 3], [1, 4], [2, 1], [2, 3], [2, 4], [3, 1], [3, 2], [3, 4], [4, 1], [4, 2], [4, 3]]

# Repeated combinations
repeated = items.repeated_combination(2).to_a
puts repeated
# => [[1, 1], [1, 2], [1, 3], [1, 4], [2, 1], [2, 2], [2, 3], [2, 4], [3, 1], [3, 2], [3, 3], [3, 4], [4, 1], [4, 2], [4, 3], [4, 4]]

# Practical example: Generate all possible team combinations
team_members = ["Alice", "Bob", "Charlie", "Diana"]
teams_of_2 = team_members.combination(2).to_a

teams_of_2.each_with_index do |team, index|
  puts "Team #{index + 1}: #{team.join(' & ')}"
end
```

## 🔀 Advanced Hash Operations

### 1. Hash Transformations

Advanced hash manipulation techniques:

```ruby
# Transform hash values
data = { a: 1, b: 2, c: 3 }

# Map values
doubled = data.transform_values { |v| v * 2 }
puts doubled  # => {:a=>2, :b=>4, :c=>6}

# Transform keys
upper_keys = data.transform_keys { |k| k.to_s.upcase.to_sym }
puts upper_keys  # => {:A=>1, :B=>2, :C=>3}

# Select and reject
positive = data.select { |k, v| v > 0 }
non_zero = data.reject { |k, v| v == 0 }

# Invert hash
inverted = data.invert
puts inverted  # => {1=>:a, 2=>:b, 3=>:c}

# Merge with conflict resolution
hash1 = { a: 1, b: 2 }
hash2 = { b: 3, c: 4, d: 5 }

merged = hash1.merge(hash2) { |key, oldval, newval|
  key == :b ? "#{oldval} or #{newval}" : newval
}
puts merged  # => {:a=>1, :b=>"2 or 3", :c=>4, :d=>5}
```

### 2. Hash as Default Parameters

Use hashes for flexible method parameters:

```ruby
def process_data(data, options = {})
  defaults = {
    timeout: 30,
    retries: 3,
    verbose: false
  }
  
  config = defaults.merge(options)
  
  puts "Processing data with config:"
  config.each { |k, v| puts "  #{k}: #{v}" }
  
  # Process data...
  "Processed"
end

# Usage examples
process_data({ name: "test" })
# Output:
# Processing data with config:
#   timeout: 30
#   retries: 3
#   verbose: false

process_data({ name: "test" }, timeout: 60, verbose: true)
# Output:
# Processing data with config:
#   timeout: 60
#   retries: 3
#   verbose: true
```

## 🎯 Advanced Enumerators

### 1. Custom Enumerators

Create your own enumerable objects:

```ruby
class FibonacciSequence
  include Enumerable
  
  def initialize(limit)
    @limit = limit
  end
  
  def each
    a, b = 0, 1
    while a < @limit
      yield a
      a, b = b, a + b
    end
  end
end

fib = FibonacciSequence.new(100)
puts fib.to_a.first(10)  # => [0, 1, 1, 2, 3, 5, 8, 13, 21, 34]
puts fib.select(&:even?)     # => [0, 2, 8, 34]
puts fib.sum                 # => 143 (sum of all numbers < 100)

# Custom enumerator with external data
class DataProcessor
  include Enumerable
  
  def initialize(data)
    @data = data
  end
  
  def each(&block)
    @data.each(&block)
  end
  
  def filter(&predicate)
    DataProcessor.new(select(&predicate))
  end
  
  def map(&transform)
    DataProcessor.new(map(&transform))
  end
end

data = (1..20).to_a
processor = DataProcessor.new(data)

evens = processor.filter(&:even?)
squared = processor.map { |n| n ** 2 }

puts evens.to_a     # => [2, 4, 6, 8, 10, 12, 14, 16, 18, 20]
puts squared.to_a    # => [1, 4, 9, 16, 25, 36, 49, 64, 81, 100, 121, 144, 169, 196, 225, 256, 289, 324, 361, 400]
```

### 2. Enumerator Laziness

Create lazy enumerators for memory efficiency:

```ruby
# Lazy enumeration for large datasets
class LazyFileReader
  include Enumerable
  
  def initialize(filename)
    @filename = filename
  end
  
  def each
    File.foreach(@filename) do |line|
      yield line.chomp
    end
  end
end

# Process large file efficiently
reader = LazyFileReader.new("large_file.txt")

# Find first line containing "error"
error_line = reader.find { |line| line.include?("error") }
puts error_line if error_line

# Count lines matching pattern (without loading all into memory)
error_count = reader.count { |line| line.include?("error") }
puts "Found #{error_count} error lines"

# Lazy map and filter
require 'enumerator'

lazy_numbers = (1..1_000_000).lazy
result = lazy_numbers
  .select { |n| n.even? }
  .map { |n| n ** 2 }
  .take(10)
  .to_a

puts result  # => [4, 16, 36, 64, 100, 144, 196, 256, 324, 400]
```

## 🔧 Advanced Module Techniques

### 1. Module Composition

Combine multiple modules effectively:

```ruby
module Validations
  def validate_email
    email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
  end
  
  def validate_age
    age.is_a?(Integer) && age >= 0 && age <= 150
  end
end

module Transformations
  def upcase_fields(*fields)
    fields.each do |field|
      define_method("#{field}=") do
        value = instance_variable_get("@#{field}")
        value ? value.upcase : value
      end
      
      define_method(field) do
        instance_variable_get("@#{field}")
      end
    end
  end
end

module Timestamps
  def self.included(base)
    base.extend(ClassMethods)
  end
  
  module ClassMethods
    def created_at
      @created_at ||= Time.now
    end
    
    def updated_at
      @updated_at ||= Time.now
    end
  end
end

class User
  include Validations
  include Transformations
  include Timestamps
  
  def initialize(email, age)
    @email = email
    @age = age
    upcase_fields(:email)
  end
end

user = User.new("alice@test.com", 30)
puts user.email        # => "ALICE@TEST.COM"
puts user.validate_email  # => true
puts user.validate_age   # => true
puts User.created_at    # => Current time
```

### 2. Module Prepending

Modify existing class behavior:

```ruby
module Logger
  def method_missing(method_name, *args, &block)
    puts "[LOG] Calling #{method_name} with #{args}"
    super
  end
end

module Timer
  def method_missing(method_name, *args, &block)
    start_time = Time.now
    result = super
    end_time = Time.now
    
    puts "[TIMER] #{method_name} took #{(end_time - start_time).round(4)} seconds"
    result
  end
end

class Calculator
  def add(a, b) a + b end
  def multiply(a, b) a * b end
  def divide(a, b) a / b.to_f end
end

# Prepend modules to add behavior
Calculator.prepend(Logger, Timer)

calc = Calculator.new
result = calc.add(5, 3)
# Output:
# [LOG] Calling add with [5, 3]
# [TIMER] add took 0.0001 seconds
puts result  # => 8
```

## 🎯 Advanced Error Handling

### 1. Custom Exception Hierarchies

Create sophisticated error handling:

```ruby
class ApplicationError < StandardError
  attr_reader :context, :error_code
  
  def initialize(message, context: {}, error_code: nil)
    super(message)
    @context = context
    @error_code = error_code
  end
  
  def details
    {
      message: message,
      context: context,
      error_code: error_code,
      backtrace: backtrace
    }
  end
end

class ValidationError < ApplicationError
  def initialize(field, value, rule)
    super("Invalid #{field}: '#{value}' violates #{rule}", 
          field: field, value: value, rule: rule)
  end
end

class AuthenticationError < ApplicationError
  def initialize(user_id, reason)
    super("Authentication failed for user #{user_id}: #{reason}",
          user_id: user_id, reason: reason)
  end
end

# Usage with rescue clauses
def process_user_registration(user_data)
  raise ValidationError.new(:email, user_data[:email], "invalid format") unless user_data[:email].match?(/\A[^@\s]+@[^@\s]+\z/)
  raise ValidationError.new(:age, user_data[:age], "must be between 0 and 150") unless (0..150).include?(user_data[:age])
  
  # Process registration...
  { success: true, user_id: rand(1000..9999) }
rescue ValidationError => e
  { success: false, error: e.details }
rescue ApplicationError => e
  { success: false, error: e.details }
end

result = process_user_registration(email: "invalid-email", age: 25)
puts result
# => {:success=>false, :error=>{:message=>"Invalid email: 'invalid-email' violates invalid format", :context=>{:field=>:email, :value=>"invalid-email", :rule=>"invalid format"}}}
```

### 2. Retry Mechanisms

Implement robust retry logic:

```ruby
require 'timeout'

class Retryable
  def self.with_options(max_attempts: 3, delay: 1, backoff: 2)
    retries = 0
    
    begin
      yield
    rescue => e
      retries += 1
      if retries <= max_attempts
        sleep(delay * (backoff ** (retries - 1)))
        retry
      else
        raise e
      end
    end
  end
  
  def self.with_timeout(seconds, &block)
    Timeout.timeout(seconds) do
      block.call
    end
  rescue Timeout::Error
    raise "Operation timed out after #{seconds} seconds"
  end
end

# Usage examples
def unreliable_operation
  success = rand < 0.3  # 30% success rate
  raise "Random failure" unless success
  "Operation succeeded"
end

# Retry with options
result = Retryable.with_options(max_attempts: 5, delay: 0.5, backoff: 1.5) do
  unreliable_operation
end

puts result  # => "Operation succeeded" (after retries)

# Timeout protection
result = Retryable.with_timeout(3) do
  sleep(2)  # Simulate work
  "Completed in time"
end

puts result  # => "Completed in time"

# Combine retry and timeout
result = Retryable.with_options(max_attempts: 3) do
  Retryable.with_timeout(5) do
    unreliable_operation
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Set Operations**: Implement a system that manages user permissions using sets
2. **Struct Usage**: Create a Person struct with validation methods
3. **Range Applications**: Build a date range validator

### Intermediate Exercises

1. **Advanced Regex**: Create a parser for log files using complex regex
2. **Functional Arrays**: Implement data processing pipelines using map/filter/reduce
3. **Custom Enumerators**: Build a lazy file processor

### Advanced Exercises

1. **Module Composition**: Create a plugin system using module composition
2. **Error Hierarchies**: Build a comprehensive error handling system
3. **Retry Mechanisms**: Implement a robust network client with retry logic

---

## 🎯 Summary

Advanced Ruby features provide incredible power and flexibility:

- **Rich data structures** - Sets, Structs, Ranges with powerful operations
- **Flexible methods** - Splat operators, keyword arguments, closures
- **String manipulation** - Advanced regex and string methods
- **Array mastery** - Functional operations, combinations, lazy evaluation
- **Hash techniques** - Transformations, merging, default parameters
- **Custom enumerators** - Create your own enumerable objects
- **Module composition** - Mixins, prepending, and behavior modification
- **Advanced error handling** - Custom exceptions, retry mechanisms, timeouts

Master these features to write truly expressive and powerful Ruby code!
