# Ruby Best Practices Guide

## Overview

Best practices in Ruby development ensure code quality, maintainability, and performance. This guide covers essential Ruby best practices, coding standards, and architectural patterns that every Ruby developer should follow.

## Code Style and Formatting

### Ruby Style Guide Implementation
```ruby
class RubyStyleGuide
  def self.naming_conventions
    {
      classes_and_modules: {
        convention: "CamelCase",
        examples: ["User", "PaymentProcessor", "DatabaseConnection"],
        guidelines: [
          "Use CamelCase for class and module names",
          "Keep names descriptive but concise",
          "Avoid abbreviations unless commonly known",
          "Use nouns for class names"
        ]
      },
      methods: {
        convention: "snake_case",
        examples: ["calculate_total", "send_notification", "validate_input"],
        guidelines: [
          "Use snake_case for method names",
          "Start with verbs for action methods",
          "Use question marks for predicate methods",
          "Use exclamation marks for methods that modify state"
        ]
      },
      variables: {
        convention: "snake_case",
        examples: ["user_name", "total_amount", "is_valid"],
        guidelines: [
          "Use snake_case for local variables",
          "Use descriptive names",
          "Avoid single-letter variables except for counters",
          "Use meaningful names for instance variables"
        ]
      },
      constants: {
        convention: "SCREAMING_SNAKE_CASE",
        examples: ["MAX_RETRY_ATTEMPTS", "DEFAULT_TIMEOUT", "API_BASE_URL"],
        guidelines: [
          "Use SCREAMING_SNAKE_CASE for constants",
          "Define constants at the top of class/module",
          "Use descriptive names",
          "Group related constants together"
        ]
      }
    }
  end

  def self.method_formatting
    {
      method_length: {
        recommendation: "Keep methods under 10 lines",
        exception: "Complex algorithms may be longer",
        guidelines: [
          "Extract complex logic to separate methods",
          "Use descriptive method names",
          "Focus on single responsibility",
          "Use private methods for implementation details"
        ]
      },
      parameter_list: {
        recommendation: "Keep parameter lists short",
        exception: "Configuration objects for many parameters",
        guidelines: [
          "Use keyword arguments for optional parameters",
          "Group related parameters",
          "Use default values when appropriate",
          "Consider using options hash for many parameters"
        ]
      },
      return_values: {
        recommendation: "Be explicit about return values",
        guidelines: [
          "Always return consistent types",
          "Use early returns for guard clauses",
          "Avoid multiple return types",
          "Document return values in comments"
        ]
      }
    }
  end

  def self.class_structure
    {
      organization: [
        "Constants at the top",
        "Public methods next",
        "Protected methods",
        "Private methods at the bottom",
        "Related methods grouped together"
      ],
      size_guidelines: {
        recommendation: "Keep classes under 200 lines",
        exception: "Utility classes may be larger",
        guidelines: [
          "Extract large classes into smaller classes",
          "Use modules for shared functionality",
          "Focus on single responsibility",
          "Consider composition over inheritance"
        ]
      },
      inheritance: {
        guidelines: [
          "Prefer composition over inheritance",
          "Use inheritance for 'is-a' relationships",
          "Keep inheritance chains shallow",
          "Use modules for shared behavior"
        ]
      }
    }
  end

  def self.commenting_guidelines
    {
      when_to_comment: [
        "Complex business logic",
        "Non-obvious algorithms",
        "Public API methods",
        "Configuration options",
        "Workarounds and temporary solutions"
      ],
      comment_style: {
        inline: "# Single line comments for clarification",
        block: "Multi-line comments for complex explanations",
        documentation: "YARD documentation for public methods"
      },
      what_to_avoid: [
        "Comments that repeat the code",
        "Outdated comments",
        "Comments that explain obvious code",
        "Excessive commenting"
      ]
    }
  end
end

# Usage example
puts "Ruby Naming Conventions:"
RubyStyleGuide.naming_conventions.each do |category, details|
  puts "\n#{category.to_s.gsub('_', ' ').capitalize}:"
  puts "  Convention: #{details[:convention]}"
  puts "  Examples: #{details[:examples].join(', ')}"
  puts "  Guidelines:"
  details[:guidelines].each { |guideline| puts "    - #{guideline}" }
end
```

## Performance Optimization

### Ruby Performance Best Practices
```ruby
class PerformanceOptimization
  def self.string_optimization
    {
      concatenation: {
        bad: "result = str1 + str2 + str3",
        good: "result = [str1, str2, str3].join",
        explanation: "Array#join is more efficient for multiple strings"
      },
      interpolation: {
        bad: 'result = "Hello " + name + ", you are " + age.to_s',
        good: 'result = "Hello #{name}, you are #{age}"',
        explanation: "String interpolation is more readable and efficient"
      },
      freezing_strings: {
        bad: "OPTIONS = ['option1', 'option2', 'option3']",
        good: "OPTIONS = ['option1', 'option2', 'option3'].freeze",
        explanation: "Freeze strings that shouldn't be modified"
      }
    }
  end

  def self.array_optimization
    {
      array_creation: {
        bad: "result = Array.new(size) { |i| i * 2 }",
        good: "result = (0...size).map { |i| i * 2 }",
        explanation: "Range#map is more efficient than Array.new with block"
      },
      array_access: {
        bad: "array.each { |item| process(item) }",
        good: "array.each(&method(:process))",
        explanation: "Use symbol-to-proc conversion for better performance"
      },
      array_manipulation: {
        bad: "result = array.map { |item| item.upcase }",
        good: "result = array.map(&:upcase)",
        explanation: "Symbol-to-proc conversion is more efficient"
      }
    }
  end

  def self.hash_optimization
    {
      hash_creation: {
        bad: "hash = Hash.new",
        good: "hash = {}",
        explanation: "Use literal hash syntax when possible"
      },
      hash_access: {
        bad: "hash.keys.each { |key| value = hash[key] }",
        good: "hash.each { |key, value| process(key, value) }",
        explanation: "Iterate over key-value pairs directly"
      },
      default_values: {
        bad: "hash = Hash.new { |h, k| h[k] = [] }",
        good: "hash = Hash.new { |h, k| h[k] = [] }",
        explanation: "Use default values carefully, they can cause memory issues"
      }
    }
  end

  def self.loop_optimization
    {
      loop_types: {
        bad: "(0...array.length).each { |i| process(array[i]) }",
        good: "array.each { |item| process(item) }",
        explanation: "Iterate directly over elements"
      },
      break_conditions: {
        bad: "found = false\narray.each { |item| break if item == target; found = true }",
        good: "found = array.include?(target)",
        explanation: "Use built-in methods when available"
      },
      map_vs_each: {
        bad: "results = []\narray.each { |item| results << process(item) }",
        good: "results = array.map { |item| process(item) }",
        explanation: "Use map for transformations"
      }
    }
  end

  def self.memory_optimization
    {
      object_creation: {
        bad: "1000.times { User.new }",
        good: "users = Array.new(1000) { User.new }",
        explanation: "Pre-allocate arrays when size is known"
      },
      garbage_collection: {
        bad: "Create many temporary objects",
        good: "Reuse objects when possible",
        explanation: "Minimize object creation to reduce GC pressure"
      },
      symbol_usage: {
        bad: "string_hash = { 'key' => 'value' }",
        good: "symbol_hash = { key: 'value' }",
        explanation: "Use symbols for hash keys when strings won't change"
      }
    }
  end
end

# Performance monitoring example
class PerformanceMonitor
  def self.measure_performance
    require 'benchmark'
    
    test_string = "Hello, World! "
    
    results = Benchmark.measure do
      10000.times do
        result = test_string + test_string + test_string
      end
    end
    
    puts "String concatenation (bad): #{results.realtime.round(4)}s"
    
    results = Benchmark.measure do
      10000.times do
        result = [test_string, test_string, test_string].join
      end
    end
    
    puts "Array#join (good): #{results.realtime.round(4)}s"
  end

  def self.memory_usage
    require 'objspace'
    
    before = ObjectSpace.count_objects
    
    # Create objects
    objects = Array.new(1000) { Object.new }
    
    after = ObjectSpace.count_objects
    
    puts "Objects created: #{after[:TOTAL] - before[:TOTAL]}"
    puts "Memory usage: #{(after[:TOTAL] - before[:TOTAL]) * 40} bytes (approx)"
  end

  def self.gc_impact
    require 'benchmark'
    
    # Measure GC impact
    results = Benchmark.measure do
      1000.times do
        GC.start
      end
    end
    
    puts "GC.start (1000 times): #{results.realtime.round(4)}s"
  end
end

# Usage example
puts "\nPerformance Optimization Examples:"
PerformanceOptimization.string_optimization.each do |category, examples|
  puts "\n#{category.to_s.gsub('_', ' ').capitalize}:"
  examples.each { |style, (bad, good, explanation)| puts "  #{style}: #{explanation}" }
end

# Performance monitoring
puts "\nPerformance Monitoring:"
PerformanceMonitor.measure_performance
PerformanceMonitor.memory_usage
PerformanceMonitor.gc_impact
```

## Security Best Practices

### Security Guidelines
```ruby
class SecurityBestPractices
  def self.input_validation
    {
      sql_injection: {
        vulnerable: "User.where('name = ?', params[:name])",
        safe: "User.where('name = ?', ActiveRecord::Base.sanitize(params[:name]))",
        explanation: "Always sanitize user input for database queries"
      },
      xss_prevention: {
        vulnerable: "html_content = params[:content]",
        safe: "html_content = ActionController::Base.helpers.sanitize(params[:content])",
        explanation: "Sanitize HTML content to prevent XSS attacks"
      },
      parameter_validation: {
        vulnerable: "id = params[:id]",
        safe: "id = params[:id].to_i if params[:id].match?(/^\d+$/)",
        explanation: "Validate parameter types and formats"
      }
    }
  end

  def self.authentication_authorization
    {
      password_handling: {
        vulnerable: "password = params[:password]",
        safe: "password = BCrypt::Password.new(params[:password])",
        explanation: "Always hash passwords using strong algorithms"
      },
      session_management: {
        vulnerable: "session[:user_id] = user.id",
        safe: "session[:user_id] = SecureRandom.hex(32)",
        explanation: "Use secure session tokens instead of user IDs"
      },
      authorization_checks: {
        vulnerable: "# No authorization check",
        safe: "return redirect_to root_path unless current_user.admin?",
        explanation: "Always check user permissions before actions"
      }
    }
  end

  def self.data_protection
    {
      sensitive_data: {
        vulnerable: "api_key = 'sk-123456789'",
        safe: "api_key = ENV['API_KEY']",
        explanation: "Never hardcode sensitive data"
      },
      encryption: {
        vulnerable: "credit_card = user.credit_card",
        safe: "credit_card = encrypt_data(user.credit_card)",
        explanation: "Encrypt sensitive data at rest"
      },
      logging: {
        vulnerable: "logger.info 'User password: #{user.password}'",
        safe: "logger.info 'User login attempt'",
        explanation: "Never log sensitive information"
      }
    }
  end

  def self.error_handling
    {
      exception_handling: {
        vulnerable: "begin; rescue; end",
        safe: "begin; rescue StandardError => e; log_error(e); end",
        explanation: "Always handle exceptions appropriately"
      },
      error_messages: {
        vulnerable: "render json: { error: e.message }, status: 500",
        safe: "render json: { error: 'Internal server error' }, status: 500",
        explanation: "Don't expose internal error details"
      },
      debugging: {
        vulnerable: "raise e if Rails.env.development?",
        safe: "Rails.logger.debug e.backtrace.join('\n') if Rails.env.development?",
        explanation: "Controlled debugging information exposure"
      }
    }
  end
end

# Security audit example
class SecurityAuditor
  def self.audit_codebase(directory)
    issues = []
    
    # Check for hardcoded secrets
    Dir.glob("#{directory}/**/*.rb").each do |file|
      content = File.read(file)
      
      # Check for API keys
      if content.match(/['"]sk_[a-zA-Z0-9]+['"]/)
        issues << { file: file, issue: "Hardcoded API key found", line: find_line(content, /sk_/) }
      end
      
      # Check for SQL injection vulnerabilities
      if content.match(/where\(['"]\s*\+\s*/)
        issues << { file: file, issue: "Potential SQL injection", line: find_line(content, /where/) }
      end
      
      # Check for password exposure
      if content.match(/password.*\s*=\s*['"]?[^'"]+['"]?/)
        issues << { file: file, issue: "Password exposure", line: find_line(content, /password/) }
      end
    end
    
    issues
  end

  def self.check_gem_security(gemfile_path)
    vulnerable_gems = [
      'rails-html-sanitizer',  # Known vulnerabilities
      'loofah',              # Known vulnerabilities
      'nokogiri'             # Known vulnerabilities
    ]
    
    issues = []
    gemfile_content = File.read(gemfile_path)
    
    vulnerable_gems.each do |gem|
      if gemfile_content.match(/gem ['"]#{gem}['"]/)
        issues << { gem: gem, issue: "Known vulnerabilities", recommendation: "Update to latest version" }
      end
    end
    
    issues
  end

  private

  def self.find_line(content, pattern)
    lines = content.split("\n")
    lines.each_with_index.find { |line, i| line.match(pattern) }
    lines ? lines[1] + 1 : nil
  end
end

# Usage example
puts "\nSecurity Best Practices:"
SecurityBestPractices.input_validation.each do |category, examples|
  puts "\n#{category.to_s.gsub('_', ' ').capitalize}:"
  examples.each { |type, (vulnerable, safe, explanation)| puts "  #{type}: #{explanation}" }
end

# Security audit
puts "\nSecurity Audit:"
current_dir = Dir.pwd
issues = SecurityAuditor.audit_codebase(current_dir)
issues.each { |issue| puts "  #{issue[:file]}: #{issue[:issue]} (Line #{issue[:line]})" }
```

## Testing Best Practices

### Testing Guidelines
```ruby
class TestingBestPractices
  def self.unit_testing
    {
      test_structure: {
        guidelines: [
          "One assertion per test method",
          "Use descriptive test names",
          "Arrange-Act-Assert pattern",
          "Independent tests"
        ]
      },
      test_coverage: {
        recommendations: [
          "Aim for 80%+ code coverage",
          "Focus on critical business logic",
          "Don't chase 100% coverage",
          "Use coverage tools to identify gaps"
        ]
      },
      test_data: {
        guidelines: [
          "Use factories for test data",
          "Avoid hardcoded test data",
          "Use realistic data",
          "Clean up test data after tests"
        ]
      }
    }
  end

  def self.integration_testing
    {
      scope: {
        guidelines: [
          "Test component interactions",
          "Test database operations",
          "Test API endpoints",
          "Test external service integrations"
        ]
      },
      test_environment: {
        recommendations: [
          "Use test database",
          "Mock external services",
          "Use consistent test data",
          "Isolate tests from each other"
        ]
      },
      assertions: {
        guidelines: [
          "Test happy path",
          "Test error conditions",
          "Test edge cases",
          "Verify side effects"
        ]
      }
    }
  end

  def self.acceptance_testing
    {
      user_scenarios: {
        guidelines: [
          "Test from user perspective",
          "Test complete workflows",
          "Test error handling",
          "Test performance from user view"
        ]
      },
      test_automation: {
        recommendations: [
          "Use browser automation tools",
          "Test on multiple browsers",
          "Test on mobile devices",
          "Include visual regression testing"
        ]
      }
    }
  end

  def self.test_maintenance
    {
      test_organization: [
        "Group related tests",
        "Use descriptive test names",
        "Keep tests focused",
        "Remove obsolete tests"
      ],
      test_performance: [
        "Keep tests fast",
        "Use test doubles appropriately",
        "Parallelize test execution",
        "Monitor test execution time"
      ]
    }
  end
end

# Testing framework example
class TestingFramework
  def self.setup_test_environment
    # Test configuration
    require 'rspec'
    require 'factory_bot'
    require 'database_cleaner'
    
    # RSpec configuration
    RSpec.configure do |config|
      config.use_transactional_fixtures = true
      config.infer_spec_type_from_file_location!
      config.filter_rails_from_backtrace!
      
      # Include factory bot methods
      config.include FactoryGirl::Syntax::Methods
    end
    
    # Database cleaner
    DatabaseCleaner.strategy = :transaction
    
    # Factory bot
    FactoryBot.definition_file_paths = ['spec/factories']
    FactoryBot.find_definitions
  end

  def self.create_test_examples
    # Example test structure
    test_example = <<~RUBY
      require 'rails_helper'

      RSpec.describe User, type: :model do
        describe '#valid?' do
          context 'with valid attributes' do
            let(:user) { build(:user) }
            
            it 'returns true' do
              expect(user).to be_valid
            end
          end
          
          context 'with invalid email' do
            let(:user) { build(:user, email: 'invalid') }
            
            it 'returns false' do
              expect(user).not_to be_valid
            end
          end
        end
        
        describe '#authenticate' do
          let(:user) { create(:user, password: 'password123') }
          
          it 'returns user with valid credentials' do
            result = user.authenticate('password123')
            expect(result).to eq(user)
          end
          
          it 'returns false with invalid credentials' do
            result = user.authenticate('wrongpassword')
            expect(result).to be_false
          end
        end
      end
    RUBY
    
    test_example
  end

  def self.performance_test_example
    performance_test = <<~RUBY
      require 'rails_helper'
      require 'benchmark'

      RSpec.describe 'User Performance' do
        describe 'user creation' do
          it 'creates 1000 users in under 1 second' do
            time = Benchmark.realtime do
              1000.times { create(:user) }
            end
            
            expect(time).to be < 1.0
          end
        end
        
        describe 'user query' do
          let!(:users) { create_list(:user, 1000) }
          
          it 'queries 1000 users in under 0.1 seconds' do
            time = Benchmark.realtime do
              User.all.to_a
            end
            
            expect(time).to be < 0.1
          end
        end
      end
    RUBY
    
    performance_test
  end
end

# Usage example
puts "\nTesting Best Practices:"
TestingBestPractices.unit_testing[:test_structure][:guidelines].each { |guideline| puts "  ✓ #{guideline}" }
```

## Code Organization

### Module and Class Organization
```ruby
class CodeOrganization
  def self.module_structure
    {
      module_purpose: [
        "Namespace for related functionality",
        "Share methods across classes",
        "Mixin functionality",
        "Organize related constants"
      ],
      module_guidelines: [
        "Keep modules focused",
        "Use descriptive names",
        "Group related functionality",
        "Document public methods"
      ],
      module_examples: [
        "Authentication module",
        "Validation module",
        "Formatting module",
        "Utility module"
      ]
    }
  end

  def self.class_organization
    {
      single_responsibility: [
        "Classes should have one reason to change",
        "Focus on core functionality",
        "Extract complex logic",
        "Keep classes small"
      ],
      class_guidelines: [
        "Use descriptive class names",
        "Keep methods focused",
        "Use composition over inheritance",
        "Extract complex classes"
      ],
      class_examples: [
        "User model",
        "Payment processor",
        "Email service",
        "Data validator"
      ]
    }
  end

  def self.file_organization
    {
      directory_structure: [
        "app/models/ for ActiveRecord models",
        "app/services/ for business logic",
        "app/controllers/ for controllers",
        "app/helpers/ for view helpers",
        "lib/ for utility classes"
      ],
      file_naming: [
        "Use snake_case for file names",
        "Match file names to class names",
        "Keep files focused",
        "Use descriptive names"
      ],
      file_guidelines: [
        "Keep files under 300 lines",
        "Extract large files",
        "Group related functionality",
        "Use consistent structure"
      ]
    }
  end
end

# Organization example
module Authentication
  module SessionManagement
    def self.create_session(user)
      # Session creation logic
      session_id = SecureRandom.hex(32)
      # Implementation details
    end
    
    def self.validate_session(session_id)
      # Session validation logic
      # Implementation details
    end
  end
  
  module TokenManagement
    def self.generate_token(user)
      # Token generation logic
      # Implementation details
    end
    
    def self.validate_token(token)
      # Token validation logic
      # Implementation details
    end
  end
end

# Usage example
puts "\nCode Organization:"
CodeOrganization.module_structure[:module_purpose].each { |purpose| puts "  ✓ #{purpose}" }
```

## Best Practices Summary

### Comprehensive Best Practices Checklist
```ruby
class BestPracticesChecklist
  def self.checklist
    {
      code_quality: [
        "Follow Ruby style guide",
        "Use meaningful variable names",
        "Write self-documenting code",
        "Keep methods small and focused",
        "Use appropriate data structures"
      ],
      performance: [
        "Optimize string operations",
        "Use efficient iteration methods",
        "Minimize object creation",
        "Profile critical code paths",
        "Use caching appropriately"
      ],
      security: [
        "Validate all user input",
        "Use parameterized queries",
        "Hash passwords properly",
        "Never expose sensitive data",
        "Keep dependencies updated"
      ],
      testing: [
        "Write comprehensive tests",
        "Test edge cases",
        "Use test doubles appropriately",
        "Maintain test coverage",
        "Keep tests fast"
      ],
      documentation: [
        "Document public APIs",
        "Write clear commit messages",
        "Update documentation regularly",
        "Use inline comments sparingly",
        "Maintain README files"
      ]
    }
  end

  def self.get_score(project_path)
    # Simple scoring system
    score = 0
    total_checks = 0
    
    checklist[:code_quality].each do |check|
      total_checks += 1
      score += 1 if check_implemented?(project_path, check)
    end
    
    checklist[:performance].each do |check|
      total_checks += 1
      score += 1 if check_implemented?(project_path, check)
    end
    
    checklist[:security].each do |check|
      total_checks += 1
      score += 1 if check_implemented?(project_path, check)
    end
    
    checklist[:testing].each do |check|
      total_checks += 1
      score += 1 if check_implemented?(project_path, check)
    end
    
    checklist[:documentation].each do |check|
      total_checks += 1
      score += 1 if check_implemented?(project_path, check)
    end
    
    {
      score: score,
      total: total_checks,
      percentage: (score.to_f / total_checks * 100).round(2)
    }
  end

  private

  def self.check_implemented?(project_path, check)
    # Simplified implementation - in practice, this would check actual code
    case check
    when /Follow Ruby style guide/
      check_style_guide_compliance(project_path)
    when /Validate all user input/
      check_input_validation(project_path)
    when /Write comprehensive tests/
      check_test_coverage(project_path)
    else
      true  # Default to true for demo
    end
  end

  def self.check_style_guide_compliance(project_path)
    # Check for basic Ruby style compliance
    Dir.glob("#{project_path}/**/*.rb").each do |file|
      content = File.read(file)
      return false if content.match(/\s+$/)  # Trailing whitespace
      return false if content.match(/def [a-z]/)     # Method name not snake_case
    end
    true
  end

  def self.check_input_validation(project_path)
    # Check for input validation patterns
    Dir.glob("#{project_path}/**/*.rb").each do |file|
      content = File.read(file)
      return true if content.match(/params\[:/i) && content.match(/validate|sanitize|permit/i)
    end
    false
  end

  def self.check_test_coverage(project_path)
    # Check for test files
    test_files = Dir.glob("#{project_path}/spec/**/*.rb").length
    source_files = Dir.glob("#{project_path}/app/**/*.rb").length
    
    test_files > 0 && source_files > 0 && (test_files.to_f / source_files) > 0.5
  end
end

# Usage example
puts "\nBest Practices Checklist:"
checklist = BestPracticesChecklist.checklist
checklist.each { |category, items| puts "#{category}: #{items.join(', ')}" }

# Score calculation example
puts "\nProject Score:"
score = BestPracticesChecklist.get_score(Dir.pwd)
puts "Score: #{score[:score]}/#{score[:total]} (#{score[:percentage]}%)"
```

## Conclusion

Following Ruby best practices is essential for writing maintainable, efficient, and secure code. By implementing these guidelines consistently, you'll improve code quality, reduce bugs, and make your applications more robust and performant.

## Further Reading

- [Ruby Style Guide](https://rubystyle.guide/)
- [The Ruby Programming Language](https://www.ruby-lang.org/en/documentation/)
- [Ruby Performance Optimization](https://github.com/JuanitoFatas/fast_csv)
- [Ruby Security Guide](https://github.com/rubysec/rubysec-guide)
- [Testing Best Practices](https://rspec.rubystyleguide.com/)
