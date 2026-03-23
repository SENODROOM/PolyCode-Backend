# Advanced Ruby Features Examples
# Demonstrating sophisticated Ruby capabilities

require 'json'

class AdvancedFeaturesExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "🚀 Advanced Ruby Features Examples"
    puts "==================================="
    puts "Explore Ruby's sophisticated features and capabilities!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Advanced Features Menu:"
      puts "1. Advanced Data Structures"
      puts "2. Advanced Method Techniques"
      puts "3. Advanced String Manipulation"
      puts "4. Advanced Array Operations"
      puts "5. Advanced Hash Operations"
      puts "6. Advanced Enumerators"
      puts "7. Advanced Module Techniques"
      puts "8. Advanced Error Handling"
      puts "9. Advanced Enumerators with Lazy Evaluation"
      puts "10. Performance Optimization"
      puts "11. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-11): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        advanced_data_structures
      when 2
        advanced_method_techniques
      when 3
        advanced_string_manipulation
      when 4
        advanced_array_operations
      when 5
        advanced_hash_operations
      when 6
        advanced_enumerators
      when 7
        advanced_module_techniques
      when 8
        advanced_error_handling
      when 9
        lazy_enumerators
      when 10
        performance_optimization
      when 11
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def advanced_data_structures
    puts "\n📊 Example 1: Advanced Data Structures"
    puts "=" * 50
    puts "Working with Sets, Structs, and Ranges."
    puts ""
    
    # Set operations demonstration
    puts "🔹 Set Operations:"
    set1 = Set.new([1, 2, 3, 4, 5])
    set2 = Set.new([4, 5, 6, 7, 8])
    
    union = set1 | set2
    intersection = set1 & set2
    difference = set1 - set2
    symmetric_diff = set1 ^ set2
    
    puts "  Set1: #{set1.to_a}"
    puts "  Set2: #{set2.to_a}"
    puts "  Union: #{union.to_a}"
    puts "  Intersection: #{intersection.to_a}"
    puts "  Difference (Set1 - Set2): #{difference.to_a}"
    puts "  Symmetric Difference: #{symmetric_diff.to_a}"
    
    # Struct demonstration
    puts "\n🏗️ Struct Usage:"
    Person = Struct.new(:name, :age, :email, :address)
    
    person1 = Person.new("Alice", 30, "alice@test.com", "123 Main St")
    person2 = Person.new("Bob", 25, "bob@test.com", "456 Oak Ave")
    
    puts "  Person 1: #{person1.name}, #{person1.age}, #{person1.email}"
    puts "  Person 2: #{person2.name}, #{person2.age}, #{person2.email}"
    
    # OpenStruct for dynamic attributes
    puts "\n🔓 OpenStruct Usage:"
    config = OpenStruct.new(
      host: "localhost",
      port: 3000,
      database: "myapp",
      debug: true
    )
    
    puts "  Config: #{config.host}:#{config.port}"
    config.new_field = "value"  # Dynamically add new field
    puts "  With new field: #{config.new_field}"
    
    # Range operations
    puts "\n📏 Range Operations:"
    numbers = 1..10
    chars = 'a'..'z'
    
    puts "  Numbers range: #{numbers.to_a}"
    puts "  Characters range: #{chars.to_a.first(5).join}..."
    puts "  Range steps: #{numbers.step(2).to_a}"
    puts "  Range select (evens): #{numbers.select(&:even?)}"
    
    @examples << {
      title: "Advanced Data Structures",
      description: "Working with Sets, Structs, and Ranges",
      code: <<~RUBY
        # Set operations
        set1 = Set.new([1, 2, 3, 4, 5])
        set2 = Set.new([4, 5, 6, 7, 8])
        union = set1 | set2
        intersection = set1 & set2
        
        # Struct usage
        Person = Struct.new(:name, :age, :email)
        person = Person.new("Alice", 30, "alice@test.com")
        
        # OpenStruct for dynamic attributes
        config = OpenStruct.new(host: "localhost", port: 3000)
      RUBY
    }
    
    puts "\n✅ Advanced Data Structures example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def advanced_method_techniques
    puts "\n🔧 Example 2: Advanced Method Techniques"
    puts "=" * 50
    puts "Mastering splat operators, keyword arguments, and closures."
    puts ""
    
    # Splat operators
    puts "💫 Splat Operators:"
    
    def splat_demo(*numbers)
      puts "  Received: #{numbers}"
      first, *middle, last = numbers
      puts "  First: #{first}, Middle: #{middle}, Last: #{last}"
    end
    
    def double_splat(*args)
      puts "  Double splat: #{args}"
      args.each { |arg| puts "    #{arg}" }
    end
    
    splat_demo(1, 2, 3, 4, 5)
    double_splat("a", "b", "c", "d", "e")
    
    # Keyword arguments
    puts "\n🔑 Keyword Arguments:"
    
    def flexible_method(required, *optional, keyword:)
      puts "  Required: #{required}"
      puts "  Optional: #{optional}" if optional.any?
      puts "  Keyword: #{keyword}"
    end
    
    flexible_method("test", "opt1", "opt2", keyword: "value")
    
    # Blocks, Procs, and Lambdas
    puts "\n📦 Blocks, Procs, and Lambdas:"
    
    my_proc = Proc.new { |x| x * 2 }
    my_lambda = ->(x) { x * 2 }
    
    def test_closures(closure)
      result = closure.call(5)
      "Closure result: #{result}"
    end
    
    puts "  Proc result: #{test_closures(my_proc)}"
    puts "  Lambda result: #{test_closures(my_lambda)}"
    
    # Return behavior differences
    def test_return_behavior
      lambda_proc = -> { return "from lambda" }
      regular_proc = Proc.new { return "from proc" }
      
      result1 = lambda_proc.call
      result2 = regular_proc.call
      
      "Lambda: #{result1}, Proc: #{result2}"
    end
    
    puts "  Return behavior: #{test_return_behavior}"
    
    @examples << {
      title: "Advanced Method Techniques",
      description: "Splat operators, keyword arguments, and closures",
      code: <<~RUBY
        # Splat operators
        def splat_demo(*numbers)
          first, *middle, last = numbers
        end
        
        # Keyword arguments
        def flexible_method(required, *optional, keyword:)
          # Method implementation
        end
        
        # Closures
        my_proc = Proc.new { |x| x * 2 }
        my_lambda = ->(x) { x * 2 }
      RUBY
    }
    
    puts "\n✅ Advanced Method Techniques example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def advanced_string_manipulation
    puts "\n🔤 Example 3: Advanced String Manipulation"
    puts "=" * 50
    puts "Mastering regular expressions and advanced string methods."
    puts ""
    
    # Advanced regex
    puts "🔍 Advanced Regular Expressions:"
    
    text = "John Doe, age: 30, email: john@test.com"
    pattern = /(?<name>\\w+ \\w+), age: (?<age>\\d+), email: (?<email>[\\w\\.-]+@[\\w\\.-]+\\.[\\w\\.-]+)/
    
    match = text.match(pattern)
    puts "  Text: #{text}"
    puts "  Matched name: #{match[:name]}"
    puts "  Matched age: #{match[:age]}"
    puts "  Matched email: #{match[:email]}"
    
    # Lookaheads and lookbehinds
    password_text = "password123 and admin456"
    password_pattern = /(?<=password)\\d+(?=\\D|$)/
    admin_pattern = /(?<=admin)\\d+(?=\\D|$)/
    
    puts "  Password matches: #{password_text.scan(password_pattern)}"
    puts "  Admin matches: #{password_text.scan(admin_pattern)}"
    
    # Complex regex for data extraction
    log_pattern = /
      (?<timestamp>\\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}:\\d{2})
      \\s+
      (?<level>\\w+)
      \\s+
      (?<message>.*)
    /x
    
    log_entry = "2023-12-25 10:30:45 ERROR Database connection failed"
    match = log_entry.match(log_pattern)
    
    puts "  Timestamp: #{match[:timestamp]}"
    puts "  Level: #{match[:level]}"
    puts "  Message: #{match[:message]}"
    
    # Advanced string methods
    puts "\n🎨 Advanced String Methods:"
    
    def analyze_string(text)
      {
        original: text,
        cleaned: text.strip.squeeze,
        words: text.split(/\\s+/),
        word_count: text.split(/\\s+/).length,
        char_count: text.length,
        vowels: text.count("aeiouAEIOU"),
        consonants: text.count("bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ"),
        reversed: text.reverse,
        palindrome: text.downcase == text.downcase.reverse
      }
    end
    
    analysis = analyze_string("Ruby Programming")
    puts "  Analysis of 'Ruby Programming':"
    puts "    Word count: #{analysis[:word_count]}"
    puts "    Char count: #{analysis[:char_count]}"
    puts "    Vowels: #{analysis[:vowels]}"
    puts "    Palindrome: #{analysis[:palindrome]}"
    
    @examples << {
      title: "Advanced String Manipulation",
      description: "Regular expressions and advanced string methods",
      code: <<~RUBY
        # Complex regex with named captures
        pattern = /(?<name>\\w+ \\w+), age: (?<age>\\d+)/
        match = text.match(pattern)
        
        # String interpolation and formatting
        template = "Hello, %{name}! You are %{age} years old."
        result = template % { name: name, age: age }
      RUBY
    }
    
    puts "\n✅ Advanced String Manipulation example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def advanced_array_operations
    puts "\n📊 Example 4: Advanced Array Operations"
    puts "=" * 50
    puts "Functional programming with arrays and advanced operations."
    puts ""
    
    numbers = (1..10).to_a
    
    # Functional array methods
    puts "🔢 Functional Array Methods:"
    
    squared = numbers.map { |n| n ** 2 }
    evens = numbers.select { |n| n.even? }
    sum = numbers.reduce(0) { |acc, n| acc + n }
    product = numbers.reduce(1) { |acc, n| acc * n }
    
    puts "  Squared: #{squared}"
    puts "  Evens: #{evens}"
    puts "  Sum: #{sum}"
    puts "  Product: #{product}"
    
    # Advanced reduce operations
    grouped = numbers.group_by { |n| n.even? }
    indexed = numbers.index_by { |n| n.length }
    
    puts "  Grouped by even/odd: #{grouped}"
    puts "  Indexed by length: #{indexed}"
    
    # Array combinations and permutations
    puts "\n🔄 Combinations and Permutations:"
    
    combinations = numbers.combination(2).to_a
    permutations = numbers.permutation(2).to_a
    
    puts "  2-element combinations (first 5): #{combinations.first(5)}"
    puts "  2-element permutations (first 5): #{permutations.first(5)}"
    
    # In-place operations
    puts "\n⚡ In-place Operations:"
    
    def in_place_operations!(array)
      array.sort!
      array.uniq!
      array.select!(&:odd?)
    end
    
    test_array = [5, 2, 3, 4, 1, 5, 2, 3]
    puts "  Original: #{test_array}"
    in_place_operations!(test_array)
    puts "  After in-place ops: #{test_array}"
    
    @examples << {
      title: "Advanced Array Operations",
      description: "Functional programming, combinations, and in-place operations",
      code: <<~RUBY
        # Functional operations
        numbers.map { |n| n ** 2 }
        numbers.select(&:even?)
        numbers.reduce(0) { |acc, n| acc + n }
        
        # Combinations and permutations
        numbers.combination(2).to_a
        numbers.permutation(2).to_a
        
        # In-place operations
        array.sort!
        array.uniq!
      RUBY
    }
    
    puts "\n✅ Advanced Array Operations example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def advanced_hash_operations
    puts "\n🗄️ Example 5: Advanced Hash Operations"
    puts "=" * 50
    puts "Transformations, merging, and advanced hash techniques."
    puts ""
    
    # Hash transformations
    puts "🔄 Hash Transformations:"
    
    data = { a: 1, b: 2, c: 3 }
    
    doubled = data.transform_values { |v| v * 2 }
    upper_keys = data.transform_keys { |k| k.to_s.upcase.to_sym }
    inverted = data.invert
    
    puts "  Original: #{data}"
    puts "  Doubled values: #{doubled}"
    puts "  Upper case keys: #{upper_keys}"
    puts "  Inverted: #{inverted}"
    
    # Select and reject
    puts "\n🔍 Select and Reject:"
    
    positive = data.select { |k, v| v > 0 }
    non_zero = data.reject { |k, v| v == 0 }
    
    puts "  Positive values: #{positive}"
    puts "  Non-zero values: #{non_zero}"
    
    # Hash as default parameters
    puts "\n⚙️ Hash as Default Parameters:"
    
    def process_data(data, options = {})
      defaults = {
        timeout: 30,
        retries: 3,
        verbose: false
      }
      
      config = defaults.merge(options)
      puts "  Processing with config: #{config}"
    end
    
    process_data({ name: "test" })
    process_data({ name: "test" }, timeout: 60, verbose: true)
    
    # Hash for caching
    puts "\n💾 Hash for Caching:"
    
    class FastCache
      def initialize
        @cache = {}
      end
      
      def get(key)
        @cache[key]
      end
      
      def set(key, value)
        @cache[key] = value
      end
      
      def cached_calculation(key)
        get(key) || set(key, expensive_calculation(key))
      end
      
      private
      
      def expensive_calculation(key)
        sleep(0.01)  # Simulate expensive operation
        key.to_s.upcase
      end
    end
    
    cache = FastCache.new
    cache.set("user:123", { name: "Alice", age: 30 })
    cache.set("config:app", { version: "1.0", debug: false })
    
    puts "  Cached user: #{cache.get("user:123")}"
    puts "  Cached config: #{cache.get("config:app")}"
    
    @examples << {
      title: "Advanced Hash Operations",
      description: "Transformations, merging, and parameter defaults",
      code: <<~RUBY
        # Transformations
        data.transform_values { |v| v * 2 }
        data.transform_keys { |k| k.to_s.upcase.to_sym }
        data.invert
        
        # Default parameters
        defaults = { timeout: 30, retries: 3 }
        config = defaults.merge(options)
      RUBY
    }
    
    puts "\n✅ Advanced Hash Operations example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def advanced_enumerators
    puts "\n🔄 Example 6: Advanced Enumerators"
    puts "=" * 50
    puts "Creating custom enumerable objects and enumerator patterns."
    puts ""
    
    # Custom enumerable object
    puts "🔢 Custom Enumerable Object:"
    
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
    puts "  First 10 Fibonacci numbers: #{fib.to_a.first(10)}"
    puts "  Even numbers: #{fib.select(&:even?).first(10)}"
    puts "  Sum of numbers < 100: #{fib.to_a.sum}"
    
    # Enumerator with external data
    puts "\n📊 Enumerator with External Data:"
    
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
    
    puts "  Even numbers: #{evens.to_a.first(10)}"
    puts "  Squared numbers: #{squared.to_a.first(10)}"
    
    @examples << {
      title: "Advanced Enumerators",
      description: "Custom enumerable objects and enumerator patterns",
      code: <<~RUBY
        class FibonacciSequence
          include Enumerable
          
          def each
            a, b = 0, 1
            while a < @limit
              yield a
              a, b = b, a + b
            end
          end
        end
      RUBY
    }
    
    puts "\n✅ Advanced Enumerators example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def advanced_module_techniques
    puts "\n🔧 Example 7: Advanced Module Techniques"
    puts "=" * 50
    puts "Module composition, prepending, and advanced patterns."
    puts ""
    
    # Module composition
    puts "🎨 Module Composition:"
    
    module Validations
      def validate_email(email)
        email.match?(/\\A[\\w+\\-.]+@[a-z\\d\\-]+(\\.[a-z\\d\\-]+)*\\.[a-z]+\\z/i)
      end
      
      def validate_age(age)
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
    puts "  User email: #{user.email}"
    puts "  User email (upcase): #{user.email_upcase}"
    puts "  Created at: #{User.created_at}"
    
    # Module prepending
    puts "\n⚡ Module Prepending:"
    
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
    
    Calculator.prepend(Logger, Timer)
    
    calc = Calculator.new
    result = calc.add(5, 3)
    puts "  Result: #{result}"
    
    @examples << {
      title: "Advanced Module Techniques",
      description: "Module composition, prepending, and advanced patterns",
      code: <<~RUBY
        module Logger
          def method_missing(method_name, *args, &block)
            super
          end
        end
        
        class Calculator
          prepend(Logger, Timer)
        end
      RUBY
    }
    
    puts "\n✅ Advanced Module Techniques example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def advanced_error_handling
    puts "\n🚨 Example 8: Advanced Error Handling"
    puts "=" * 50
    puts "Custom exception hierarchies and retry mechanisms."
    puts ""
    
    # Custom exception hierarchy
    puts "🔧 Custom Exception Hierarchy:"
    
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
    
    def test_error_handling
      begin
        raise ValidationError.new(:email, "invalid-email", "invalid format")
      rescue ValidationError => e
        puts "  Validation Error: #{e.details[:message]}"
      end
      
      begin
        raise AuthenticationError.new(123, "invalid password")
      rescue AuthenticationError => e
        puts "  Authentication Error: #{e.details[:message]}"
      end
    end
    
    test_error_handling
    
    # Retry mechanisms
    puts "\n🔄 Retry Mechanisms:"
    
    def unreliable_operation(success_rate = 0.3)
      success = rand < success_rate
      raise "Random failure" unless success
      "Operation succeeded"
    end
    
    def with_retry(max_attempts: 3, delay: 1, backoff: 2)
      attempts = 0
      
      begin
        yield
      rescue => e
        attempts += 1
        if attempts <= max_attempts
          sleep(delay * (backoff ** (attempts - 1)))
          retry
        else
          raise e
        end
      end
    end
    
    # Usage examples
    puts "  Simple retry:"
    result = with_retry do
      unreliable_operation(0.8)  # 80% success rate
    end
    puts "  Result: #{result}"
    
    puts "  Retry with backoff:"
    result = with_retry(max_attempts: 5, delay: 0.5, backoff: 1.5) do
      unreliable_operation(0.2)  # 20% success rate
    end
    puts "  Result: #{result}"
    
    @examples << {
      title: "Advanced Error Handling",
      description: "Custom exceptions and retry mechanisms",
      code: <<~RUBY
        class ApplicationError < StandardError
          attr_reader :context, :error_code
          
          def initialize(message, context: {}, error_code: nil)
            super(message)
            @context = context
            @error_code = error_code
          end
        end
      RUBY
    }
    
    puts "\n✅ Advanced Error Handling example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def lazy_enumerators
    puts "\n⏳ Example 9: Lazy Enumerators"
    puts "=" * 50
    puts "Memory-efficient enumeration with lazy evaluation."
    puts ""
    
    # Lazy enumeration for large datasets
    puts "🔄 Lazy Enumeration for Large Datasets:"
    
    class LazyFileReader
      include Enumerable
      
      def initialize(filename)
        @filename = filename
      end
      
      def each(&block)
        File.foreach(@filename) do |line|
          yield line.chomp
        end
      end
    end
    
    # Process large file efficiently
    reader = LazyFileReader.new("large_file.txt")
    
    # Find first line containing "error"
    error_line = reader.find { |line| line.include?("error") }
    puts "  First error line: #{error_line}" if error_line
    
    # Count lines matching pattern (without loading all into memory)
    error_count = reader.count { |line| line.include?("error") }
    puts "  Total error lines: #{error_count}"
    
    # Lazy map and filter
    require 'enumerator'
    
    lazy_numbers = (1..1_000_000).lazy
    result = lazy_numbers
      .select { |n| n.even? }
      .map { |n| n ** 2 }
      .take(10)
      .to_a
    
    puts "  First 10 even squares: #{result}"
    
    # Lazy enumerator with custom logic
    class LazyPrimeChecker
      include Enumerable
      
      def initialize(numbers)
        @numbers = numbers
      end
      
      def each(&block)
        @numbers.each do |n|
          yield n if is_prime?(n)
        end
      end
      
      private
      
      def is_prime?(n)
        return false if n <= 1
        (2..Math.sqrt(n).to_i).none? { |i| n % i == 0 }
      end
    end
    
    primes = LazyPrimeChecker.new((1..1000))
    first_10_primes = primes.take(10).to_a
    
    puts "  First 10 primes: #{first_10_primes}"
    
    @examples << {
      title: "Lazy Enumerators",
      description: "Memory-efficient enumeration with lazy evaluation",
      code: <<~RUBY
        class LazyFileReader
          include Enumerable
          
          def each(&block)
            File.foreach(@filename) { |line| yield line.chomp }
          end
        end
        
        # Lazy usage
        lazy_numbers = (1..1_000_000).lazy
        result = lazy_numbers.select { |n| n.even? }.take(10).to_a
      RUBY
    }
    
    puts "\n✅ Lazy Enumerators example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def performance_optimization
    puts "\n⚡ Example 10: Performance Optimization"
    puts "=" * 50
    puts "Benchmarking, memory profiling, and optimization techniques."
    puts ""
    
    # Benchmarking
    puts "📊 Benchmarking:"
    
    require 'benchmark'
    
    # String concatenation benchmark
    Benchmark.bm do |x|
      x.report("string concat") do
        result = ""
        1000.times { |i| result += "item#{i}" }
        result
      end
      
      x.report("array join") do
        items = 1000.times.map { |i| "item#{i}" }
        items.join
      end
      
      x.report("stringio") do
        io = StringIO.new
        1000.times { |i| io.write("item#{i}") }
        io.string
      end
    end
    
    # Memory profiling
    puts "\n🧠 Memory Profiling:"
    
    require 'objspace'
    
    class MemoryProfiler
      def self.profile(label = nil)
        GC.start
        start_memory = GC.stat[:total_allocated_objects]
        start_time = Time.now
        
        result = yield
        
        GC.start
        end_memory = GC.stat[:total_allocated_objects]
        end_time = Time.now
        
        puts "  #{label}: #{(end_time - start_time).round(4)}s"
        puts "  #{label}: #{end_memory - start_memory} objects allocated"
        
        result
      end
    end
    
    # Usage
    MemoryProfiler.profile("data processing") do
      data = (1..10000).map { |i| "item_#{i}" }
      processed = data.map(&:upcase)
    end
    
    # Object allocation tracking
    puts "\n📦 Object Allocation Tracking:"
    
    class ObjectTracker
      def self.track_allocations
        objects_before = ObjectSpace.count_objects
        
        yield
        
        objects_after = ObjectSpace.count_objects
        allocated = objects_after - objects_before
        
        puts "  Allocated #{allocated} objects"
        
        # Find most allocated classes
        object_counts = ObjectSpace.count_objects
        sorted_counts = object_counts.sort_by { |klass, count| -count }.first(5)
        
        puts "  Top allocated classes:"
        sorted_counts.each do |klass, count|
          puts "    #{klass}: #{count}"
        end
      end
    end
    
    # Usage
    ObjectTracker.track_allocations do
      users = 1000.times.map { |i| "User#{i}" }
      emails = 1000.times.map { |i| "user#{i}@test.com" }
      
      users.each_with_index do |user, index|
        user.email = emails[index]
      end
    end
    
    @examples << {
      title: "Performance Optimization",
      description: "Benchmarking, memory profiling, and optimization techniques",
      code: <<~RUBY
        require 'benchmark'
        
        Benchmark.bm do |x|
          x.report("fast") { fast_method }
          x.report("slow") { slow_method }
        end
      RUBY
    }
    
    puts "\n✅ Performance Optimization example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Advanced Features Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
      puts "   Code demonstrates: #{example[:code].split("\n").first(3)}..."
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate different aspects of advanced Ruby features!"
  end
end

# Main execution
if __FILE__ == $0
  examples = AdvancedFeaturesExamples.new
  examples.start_examples
end
