# Metaprogramming Examples in Ruby
# Demonstrating Ruby's powerful metaprogramming capabilities

require 'json'

class MetaprogrammingExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "🔬 Ruby Metaprogramming Examples"
    puts "================================"
    puts "Explore Ruby's dynamic code generation capabilities!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Metaprogramming Examples Menu:"
      puts "1. Dynamic Method Definition"
      puts "2. Method Missing Magic"
      puts "3. Class Evaluation"
      puts "4. Instance Evaluation"
      puts "5. DSL Creation"
      puts "6. Attribute Generators"
      puts "7. Method Wrapping"
      puts "8. Dynamic Class Creation"
      puts "9. Reflection and Introspection"
      puts "10. Code Generation"
      puts "11. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-11): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        dynamic_method_definition
      when 2
        method_missing_magic
      when 3
        class_evaluation
      when 4
        instance_evaluation
      when 5
        dsl_creation
      when 6
        attribute_generators
      when 7
        method_wrapping
      when 8
        dynamic_class_creation
      when 9
        reflection_introspection
      when 10
        code_generation
      when 11
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def dynamic_method_definition
    puts "\n🔧 Example 1: Dynamic Method Definition"
    puts "=" * 50
    puts "Creating methods programmatically at runtime."
    puts ""
    
    # Basic dynamic method definition
    class DynamicMethods
      def self.add_math_operation(operation, &block)
        define_method(operation, &block)
      end
      
      def self.add_string_operation(operation, &block)
        define_method(operation, &block)
      end
    end
    
    # Add methods dynamically
    DynamicMethods.add_math_operation(:add) { |a, b| a + b }
    DynamicMethods.add_math_operation(:multiply) { |a, b| a * b }
    DynamicMethods.add_math_operation(:power) { |a, b| a ** b }
    
    DynamicMethods.add_string_operation(:reverse) { |str| str.reverse }
    DynamicMethods.add_string_operation(:upcase) { |str| str.upcase }
    
    # Use the dynamically created methods
    math_obj = DynamicMethods.new
    puts "Math operations:"
    puts "  5 + 3 = #{math_obj.add(5, 3)}"
    puts "  5 * 3 = #{math_obj.multiply(5, 3)}"
    puts "  5 ** 3 = #{math_obj.power(5, 3)}"
    
    str_obj = DynamicMethods.new
    puts "String operations:"
    puts "  'hello'.reverse = #{str_obj.reverse('hello')}"
    puts "  'hello'.upcase = #{str_obj.upcase('hello')}"
    
    # Advanced: Method with context
    DynamicMethods.add_context_method(:contextual_operation) do |context|
      define_method(:in_#{context}) { |value| "#{context}: #{value}" }
    end
    
    DynamicMethods.add_context_method(:user)
    DynamicMethods.add_context_method(:admin)
    
    puts "\nContextual methods:"
    puts "  in_user('test') = #{math_obj.in_user('test')}"
    puts "  in_admin('test') = #{math_obj.in_admin('test')}"
    
    @examples << {
      title: "Dynamic Method Definition",
      description: "Creating methods at runtime with define_method",
      code: <<~RUBY
        class DynamicMethods
          def self.add_math_operation(operation, &block)
            define_method(operation, &block)
          end
        end
      RUBY
    }
    
    puts "\n✅ Dynamic Method Definition example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def method_missing_magic
    puts "\n✨ Example 2: Method Missing Magic"
    puts "=" * 45
    puts "Handling calls to undefined methods."
    puts ""
    
    class FlexibleHandler
      def initialize
        @handlers = {}
      end
      
      def add_handler(pattern, &block)
        @handlers[pattern] = block
      end
      
      def method_missing(method_name, *args, &block)
        # Try to find a matching handler
        @handlers.each do |pattern, handler|
          if method_name.to_s.match(pattern)
            return handler.call(method_name, *args, &block)
          end
        end
        
        # Fallback to super
        super
      end
      
      def respond_to_missing?(method_name, include_private = false)
        @handlers.any? { |pattern, _| method_name.to_s.match(pattern) } || super
      end
    end
    
    # Create a flexible handler
    handler = FlexibleHandler.new
    
    # Add handlers for different patterns
    handler.add_handler(/^get_(.+)/) do |method_name, *args|
      property = method_name.to_s.sub(/^get_/, '')
      "Getting #{property}: #{args.first || 'default'}"
    end
    
    handler.add_handler(/^set_(.+)/) do |method_name, *args|
      property = method_name.to_s.sub(/^set_/, '')
      "Setting #{property} to #{args.first}"
    end
    
    handler.add_handler(/^process_(.+)/) do |method_name, *args|
      process_type = method_name.to_s.sub(/^process_/, '')
      "Processing #{process_type} with #{args.join(', ')}"
    end
    
    # Use the flexible handler
    obj = FlexibleHandler.new
    
    puts "Method missing examples:"
    puts "  #{obj.get_name('Alice')} = #{obj.get_name('Alice')}"
    puts "  #{obj.set_name('Bob')} = #{obj.set_name('Bob')}"
    puts "  #{obj.process_data('test', 'data')} = #{obj.process_data('test', 'data')}"
    puts "  #{obj.unknown_method} = #{obj.unknown_method rescue 'Method not found'}"
    
    @examples << {
      title: "Method Missing Magic",
      description: "Handling undefined method calls with method_missing",
      code: <<~RUBY
        class FlexibleHandler
          def method_missing(method_name, *args, &block)
            @handlers.each do |pattern, handler|
              if method_name.to_s.match(pattern)
                return handler.call(method_name, *args, &block)
              end
            end
            super
          end
        end
      RUBY
    }
    
    puts "\n✅ Method Missing Magic example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def class_evaluation
    puts "\n🏗️ Example 3: Class Evaluation"
    puts "=" * 45
    puts "Executing code in the context of a class."
    puts ""
    
    class ClassEvaluator
      def self.add_class_methods
        class_eval do
          define_singleton_method(:class_info) do
            "This is #{name} class"
          end
          
          define_singleton_method(:count_methods) do
            instance_methods(false).length
          end
          
          define_singleton_method(:list_methods) do
            instance_methods(false).join(', ')
          end
        end
      end
      
      def self.add_instance_methods
        class_eval do
          define_method(:info) do
            "#{self.class} with ID #{object_id}"
          end
          
          define_method(:double_value) do
            @value * 2 if @value
          end
          
          define_method(:set_value) do |value|
            @value = value
          end
        end
      end
      
      def initialize(value = 0)
        @value = value
      end
    end
    
    # Add methods to class
    ClassEvaluator.add_class_methods
    ClassEvaluator.add_instance_methods
    
    # Use the class methods
    puts "Class methods:"
    puts "  #{ClassEvaluator.class_info} = #{ClassEvaluator.class_info}"
    puts "  #{ClassEvaluator.count_methods} = #{ClassEvaluator.count_methods}"
    puts "  #{ClassEvaluator.list_methods} = #{ClassEvaluator.list_methods}"
    
    # Use instance methods
    obj = ClassEvaluator.new(5)
    puts "\nInstance methods:"
    puts "  #{obj.info} = #{obj.info}"
    puts "  #{obj.double_value} = #{obj.double_value}"
    
    obj.set_value(10)
    puts "  After set_value: #{obj.double_value} = #{obj.double_value}"
    
    @examples << {
      title: "Class Evaluation",
      description: "Using class_eval to add methods to classes",
      code: <<~RUBY
        class ClassEvaluator
          def self.add_class_methods
            class_eval do
              define_singleton_method(:class_info) { "This is \#{name} class" }
              define_singleton_method(:count_methods) { instance_methods(false).length }
            end
          end
        end
      RUBY
    }
    
    puts "\n✅ Class Evaluation example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def instance_evaluation
    puts "\n🔍 Example 4: Instance Evaluation"
    puts "=" * 45
    puts "Executing code in the context of an object."
    puts ""
    
    class ConfigurationBuilder
      def initialize
        @config = {}
      end
      
      def method_missing(method_name, *args)
        if method_name.to_s.end_with?('=')
          key = method_name.to_s.chomp('=').to_sym
          @config[key] = args.first
        else
          super
        end
      end
      
      def respond_to_missing?(method_name, include_private = false)
        method_name.to_s.end_with?('=') || super
      end
      
      def build
        @config
      end
      
      def to_json
        @config.to_json
      end
    end
    
    # Use instance_eval to add methods
    config = ConfigurationBuilder.new
    config.instance_eval do
      def host=(value)
        @config[:host] = value
      end
      
      def port=(value)
        @config[:port] = value
      end
      
      def database=(value)
        @config[:database] = value
      end
    end
    
    # Configure using the dynamically added methods
    config.host = "localhost"
    config.port = 3000
    config.database = "myapp_development"
    
    puts "Instance evaluation configuration:"
    puts "  Host: #{config.build[:host]}"
    puts "  Port: #{config.build[:port]}"
    puts "  Database: #{config.build[:database]}"
    puts "  JSON: #{config.to_json}"
    
    @examples << {
      title: "Instance Evaluation",
      description: "Using instance_eval to add methods to objects",
      code: <<~RUBY
        config = ConfigurationBuilder.new
        config.instance_eval do
          def host=(value)
            @config[:host] = value
          end
        end
      RUBY
    }
    
    puts "\n✅ Instance Evaluation example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def dsl_creation
    puts "\n🎨 Example 5: DSL Creation"
    puts "=" * 45
    puts "Creating Domain-Specific Languages (DSLs)."
    puts ""
    
    # Web Application DSL
    class WebApp
      def self.create(&block)
        app = Class.new
        
        # Add DSL methods to the class
        app_class_eval do
          define_singleton_method(:route) do |path, &handler|
            @routes ||= {}
            @routes[path] = handler
          end
          
          define_singleton_method(:middleware) do |name, &middleware|
            @middlewares ||= {}
            @middlewares[name] = middleware
          end
          
          define_singleton_method(:run) do |env|
            path = env['PATH_INFO']
            
            # Find matching route
            handler = @routes[path]
            if handler
              handler.call(env)
            else
              [404, {}, "Not Found"]
            end
          end
        end
      end
    end
    
    # Create a web app using the DSL
    app = WebApp.create do
      route "/hello" do
        [200, {"Content-Type" => "text/plain"}, "Hello, World!"]
      end
      
      route "/time" do
        [200, {"Content-Type" => "text/plain"}, "Current time: #{Time.now}"]
      end
      
      middleware :logging do |app|
        lambda do |env|
          puts "[#{Time.now}] #{env['REQUEST_METHOD']} #{env['PATH_INFO']}"
          app.call(env)
        end
      end
    end
    
    # Simulate requests
    puts "DSL-created web app responses:"
    puts "  GET /hello: #{app.run({'REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/hello'})[0]}"
    puts "  GET /time: #{app.run({'REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/time'})[0]}"
    
    @examples << {
      title: "DSL Creation",
      description: "Creating fluent APIs for specific domains",
      code: <<~RUBY
        class WebApp
          def self.create(&block)
            app = Class.new
            app_class_eval do
              define_singleton_method(:route) do |path, &handler|
                @routes ||= {}
                @routes[path] = handler
              end
            end
          end
        end
      RUBY
    }
    
    puts "\n✅ DSL Creation example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def attribute_generators
    puts "\n⚙️ Example 6: Attribute Generators"
    puts "=" * 45
    puts "Automatically creating getter/setter methods."
    puts ""
    
    module SmartAttributes
      def self.attr_accessor_with_validation(attr_name, validation_proc = nil)
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
      extend SmartAttributes
      
      attr_accessor_with_validation :email, ->(email) { email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i) }
      attr_accessor_with_validation :age, ->(age) { age.is_a?(Integer) && age >= 0 && age <= 150 }
      attr_accessor_with_validation :name, ->(name) { name.is_a?(String) && !name.empty? }
    end
    
    # Test the attribute generator
    user = User.new
    
    puts "Testing attribute generators:"
    
    # Valid assignments
    user.email = "test@example.com"
    user.age = 25
    user.name = "Alice"
    
    puts "  Valid email: #{user.email} (valid: #{user.email_valid?})"
    puts "  Valid age: #{user.age} (valid: #{user.age_valid?})"
    puts "  Valid name: #{user.name} (valid: #{user.name_valid?})"
    
    # Invalid assignments
    begin
      user.email = "invalid-email"
      puts "  Invalid email: #{user.email} (valid: #{user.email_valid?})"
    rescue ArgumentError => e
      puts "  Email error: #{e.message}"
    end
    
    begin
      user.age = -5
      puts "  Invalid age: #{user.age} (valid: #{user.age_valid?})"
    rescue ArgumentError => e
      puts "  Age error: #{e.message}"
    end
    
    @examples << {
      title: "Attribute Generators",
      description: "Automatically creating getter/setter methods with validation",
      code: <<~RUBY
        module SmartAttributes
          def self.attr_accessor_with_validation(attr_name, validation_proc = nil)
            define_method(attr_name) { instance_variable_get("@#{attr_name}") }
            define_method("\#{attr_name}=") { |value| instance_variable_set("@#{attr_name}", value) }
          end
        end
      RUBY
    }
    
    puts "\n✅ Attribute Generators example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def method_wrapping
    puts "\n🎁 Example 7: Method Wrapping"
    puts "=" * 45
    puts "Adding functionality to existing methods."
    puts ""
    
    class MethodWrapper
      def self.wrap_method(object, method_name, &wrapper)
        original_method = object.method(method_name)
        
        object.define_singleton_method("#{method_name}_with_wrapper") do |*args, &block|
          # Call the wrapper first
          wrapper.call(method_name, args) do
            # Then call the original method
            original_method.call(*args, &block)
          end
        end
      end
      
      def self.wrap_all_methods(object, &wrapper)
        object.methods.each do |method_name|
          next if method_name.to_s.start_with?('_')
          wrap_method(object, method_name, &wrapper)
        end
      end
    end
    
    class Calculator
      def add(a, b)
        result = a + b
        puts "  Adding #{a} + #{b} = #{result}"
        result
      end
      
      def multiply(a, b)
        result = a * b
        puts "  Multiplying #{a} * #{b} = #{result}"
        result
      end
      
      def divide(a, b)
        result = a / b.to_f
        puts "  Dividing #{a} / #{b} = #{result}"
        result
      end
    end
    
    # Wrap methods with logging
    calc = Calculator.new
    
    MethodWrapper.wrap_method(calc, :add) do |method_name, args, &block|
      puts "  [WRAPPER] Entering #{method_name} with args: #{args}"
      result = yield
      puts "  [WRAPPER] Exiting #{method_name} with result: #{result}"
      result
    end
    
    MethodWrapper.wrap_method(calc, :multiply) do |method_name, args, &block|
      puts "  [WRAPPER] #{method_name} called"
      yield
    end
    
    MethodWrapper.wrap_method(calc, :divide) do |method_name, args, &block|
      puts "  [WRAPPER] #{method_name} called"
      
      begin
        yield
      rescue ZeroDivisionError => e
        puts "  [WRAPPER] #{method_name} caught error: #{e.message}"
        0
      end
    end
    
    # Use wrapped methods
    puts "\nUsing wrapped methods:"
    calc.add_with_wrapper(5, 3)
    calc.multiply_with_wrapper(4, 6)
    calc.divide_with_wrapper(10, 2)
    
    @examples << {
      title: "Method Wrapping",
      description: "Adding functionality to existing methods using alias_method or define_method",
      code: <<~RUBY
        class MethodWrapper
          def self.wrap_method(object, method_name, &wrapper)
            original_method = object.method(method_name)
            object.define_singleton_method("\#{method_name}_with_wrapper") do |*args, &block|
              wrapper.call(method_name, args) { original_method.call(*args, &block) }
            end
          end
        end
      RUBY
    }
    
    puts "\n✅ Method Wrapping example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def dynamic_class_creation
    puts "\n🏗️ Example 8: Dynamic Class Creation"
    puts "=" * 45
    puts "Creating classes programmatically at runtime."
    puts ""
    
    class ClassFactory
      def self.create_model_class(table_name)
        class_name = table_name.to_s.capitalize.gsub(/s$/, '')
        
        Class.new do
          define_method(:table_name) { table_name.to_s }
          define_method(:class_name) { class_name }
          
          define_singleton_method(:find) do |id|
            # Simulate database find
            { id: id, name: "#{class_name} #{id}", table_name: table_name }
          end
          
          define_singleton_method(:all) do
            # Simulate database all
            (1..5).map { |i| find(i) }
          end
        end
      end
    end
      
      def self.create_service_class(service_name)
        class_name = "#{service_name}Service"
        
        Class.new do
          define_method(:service_name) { service_name }
          
          def initialize(config = {})
            @config = config
          end
          
          def call(method_name, *args)
            puts "[#{service_name}] Calling #{method_name} with #{args}"
            "#{service_name} response"
          end
        end
      end
    end
    
    # Create dynamic classes
    user_class = ClassFactory.create_model_class("users")
    order_service = ClassFactory.create_service_class("order")
    
    # Use the dynamic classes
    puts "Dynamic User class:"
    user = user_class.find(1)
    puts "  #{user.inspect}"
    
    users = user_class.all
    puts "  All users: #{users.inspect}"
    
    puts "\nDynamic Service class:"
    order = OrderService.new(timeout: 30)
    puts "  #{order.call('process', 'order_123')}"
    
    @examples << {
      title: "Dynamic Class Creation",
      description: "Creating classes at runtime using Class.new",
      code: <<~RUBY
        class ClassFactory
          def self.create_model_class(table_name)
            Class.new do
              define_method(:table_name) { table_name.to_s }
              define_singleton_method(:find) { |id| { id: id, name: "#{class_name} #{id}" } }
            end
          end
        end
      RUBY
    }
    
    puts "\n✅ Dynamic Class Creation example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def reflection_introspection
    puts "\n🔍 Example 9: Reflection and Introspection"
    puts "=" * 45
    puts "Examining Ruby objects and code at runtime."
    puts ""
    
    class ObjectAnalyzer
      def self.analyze_object(obj)
        analysis = {
          class: obj.class,
          class_name: obj.class.name,
          object_id: obj.object_id,
          singleton_class: obj.singleton_class,
          instance_variables: obj.instance_variables,
          methods: obj.methods(false),
          public_methods: obj.public_methods(false),
          private_methods: obj.private_methods(false),
          protected_methods: obj.protected_methods(false),
          singleton_methods: obj.singleton_methods(false)
        }
        
        # Add method analysis
        analysis[:methods_info] = {
          total: analysis[:methods].length,
          public: analysis[:public_methods].length,
          private: analysis[:private_methods].length,
          protected: analysis[:protected_methods].length,
          singleton: analysis[:singleton_methods].length
        }
        
        # Add instance variable analysis
        analysis[:instance_vars_info] = analysis[:instance_variables].map do |var|
          {
            name: var,
            value: obj.instance_variable_get(var),
            type: obj.instance_variable_get(var).class.name
          }
        end
        
        analysis
      end
      
      def self.analyze_class(klass)
        {
          name: klass.name,
          superclass: klass.superclass,
          ancestors: klass.ancestors,
          included_modules: klass.included_modules,
          singleton_class: klass.singleton_class,
          instance_methods: klass.instance_methods(false),
          class_methods: klass.singleton_methods(false)
        }
      end
    end
    
    # Test with different objects
    test_string = "Hello, Ruby!"
    test_array = [1, 2, 3]
    test_hash = { key: "value", number: 42 }
    test_proc = proc { |x| x * 2 }
    
    puts "String analysis:"
    string_analysis = ObjectAnalyzer.analyze_object(test_string)
    puts "  Class: #{string_analysis[:class_name]}"
    puts "  Object ID: #{string_analysis[:object_id]}"
    puts "  Methods: #{string_analysis[:methods_info][:total]}"
    
    puts "\nArray analysis:"
    array_analysis = ObjectAnalyzer.analyze_object(test_array)
    puts "  Class: #{array_analysis[:class_name]}"
    puts "  Methods: #{array_analysis[:methods_info][:total]}"
    
    puts "\nHash analysis:"
    hash_analysis = ObjectAnalyzer.analyze_object(test_hash)
    puts "  Class: #{hash_analysis[:class_name]}"
    puts "  Methods: #{hash_analysis[:methods_info][:total]}"
    
    puts "\nProc analysis:"
    proc_analysis = ObjectAnalyzer.analyze_object(test_proc)
    puts "  Class: #{proc_analysis[:class_name]}"
    puts "  Methods: #{proc_analysis[:methods_info][:total]}"
    
    puts "\nClass analysis:"
    class_analysis = ObjectAnalyzer.analyze_class(String)
    puts "  Name: #{class_analysis[:name]}"
    puts "  Superclass: #{class_analysis[:superclass]}"
    puts "  Ancestors: #{class_analysis[:ancestors].map(&:name).join(' < ')}"
    
    @examples << {
      title: "Reflection and Introspection",
      description: "Examining Ruby objects and classes at runtime",
      code: <<~RUBY
        class ObjectAnalyzer
          def self.analyze_object(obj)
            {
              class: obj.class,
              object_id: obj.object_id,
              methods: obj.methods(false)
            }
          end
        end
      RUBY
    }
    
    puts "\n✅ Reflection and Introspection example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def code_generation
    puts "\n⚡ Example 10: Code Generation"
    puts "=" * 45
    puts "Generating Ruby code dynamically."
    puts ""
    
    class CodeGenerator
      def self.generate_class_code(class_name, methods)
        code = <<~RUBY
          class #{class_name}
            #{methods.map { |method| generate_method_code(method) }.join("\n  ")}
          end
        RUBY
        
        # Save to file
        filename = "#{class_name.downcase}.rb"
        File.write(filename, code)
        filename
      end
      
      def self.generate_method_code(method)
        "def #{method[:name]}(#{method[:args].join(', ')})
          #{method[:body]}
        end
      end
      
      def self.generate_module_code(module_name, methods)
        code = <<~RUBY
          module #{module_name}
            #{methods.map { |method| generate_method_code(method) }.join("\n  ")}
          end
        RUBY
        
        filename = "#{module_name.downcase}.rb"
        File.write(filename, code)
        filename
      end
      
      def self.generate_dsl_code(dsl_name, methods)
        code = <<~RUBY
          class #{dsl_name}
            def self.create(&block)
              dsl = Class.new
              
              dsl.class_eval do
                #{methods.map { |method| generate_method_code(method) }.join("\n                ")}
              end
              
              dsl
            end
          end
        RUBY
        
        filename = "#{dsl_name.downcase}.rb"
        File.write(filename, code)
        filename
      end
    end
    
    # Generate different types of code
    puts "Generating class with validation methods:"
    class_methods = [
      {
        name: "validate_email",
        args: ["email"],
        body: "email.match?(/\\A[\\w+\\-.]+@[a-z\\d\\-]+(\\.[a-z\\d\\-]+)*\\.[a-z]+\\z/i)"
      },
      {
        name: "validate_age",
        args: ["age"],
        body: "age.is_a?(Integer) && age >= 0 && age <= 150"
      },
      {
        name: "to_s",
        args: [],
        body: "\"\#{name}: \#{email} (Age: \#{age})\""
      }
    ]
    
    generated_file = CodeGenerator.generate_class_code("ValidUser", class_methods)
    puts "Generated file: #{generated_file}"
    
    puts "\nGenerating utility module:"
    module_methods = [
      {
        name: "format_currency",
        args: ["amount"],
        body: "\"$\#{amount.to_s.gsub(/\\d(?=\\d{3})/, ',')}\""
      },
      {
        name: "parse_date",
        args: ["date_string"],
        body: "Date.parse(date_string)"
      },
      {
        name: "slugify",
        args: ["text"],
        body: "text.downcase.gsub(/[^a-z0-9\\s]/, '-').gsub(/\\s+/, '-').squeeze('-')"
      }
    ]
    
    generated_module = CodeGenerator.generate_module_code("StringUtils", module_methods)
    puts "Generated module file: #{generated_module}"
    
    puts "\nGenerating DSL:"
    dsl_methods = [
      {
        name: "get",
        args: ["path"],
        body: "requests[:path] = path"
      },
      {
        name: "post",
        args: ["path"],
        body: "requests[:path] = path"
      },
      {
        name: "use",
        args: ["middleware"],
        body: "@middlewares << middleware"
      }
    ]
    
    generated_dsl = CodeGenerator.generate_dsl_code("APIDSL", dsl_methods)
    puts "Generated DSL file: #{generated_dsl}"
    
    # Show generated code content
    puts "\nGenerated ValidUser class content:"
    puts File.read(generated_file)
    
    @examples << {
      title: "Code Generation",
      description: "Generating Ruby code dynamically",
      code: <<~RUBY
        class CodeGenerator
          def self.generate_class_code(class_name, methods)
            code = "class \#{class_name}\\n  \#{methods.map { |m| generate_method_code(m) }.join("\\n  ")}\\nend"
            File.write(filename, code)
          end
        end
      RUBY
    }
    
    puts "\n✅ Code Generation example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Metaprogramming Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
      puts "   Code snippet saved to memory"
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate different aspects of Ruby metaprogramming!"
  end
end

# Main execution
if __FILE__ == $0
  examples = MetaprogrammingExamples.new
  examples.start_examples
end
