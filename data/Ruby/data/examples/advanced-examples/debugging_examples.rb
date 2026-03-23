# Advanced Debugging Examples
# Demonstrating debugging tools, techniques, and strategies

puts "=== RUBY DEBUGGER ==="

# Enable debugging
require 'debug'

class Calculator
  def add(a, b)
    result = a + b
    debugger  # Breakpoint - execution will pause here
    result
  end
  
  def divide(a, b)
    debugger  # Breakpoint - inspect variables
    if b == 0
      raise ZeroDivisionError, "Cannot divide by zero"
    end
    a / b
  end
  
  def complex_calculation(x, y, z)
    step1 = x * y
    debugger  # Inspect intermediate calculation
    step2 = step1 + z
    step3 = step2 / 2
    debugger  # Final result before return
    step3
  end
end

# Usage example (would pause in debugger)
puts "Ruby Debugger Example:"
calc = Calculator.new

begin
  result = calc.add(5, 3)
  puts "Addition result: #{result}"
rescue => e
  puts "Error in addition: #{e.message}"
end

begin
  result = calc.divide(10, 2)
  puts "Division result: #{result}"
rescue => e
  puts "Error in division: #{e.message}"
end

begin
  result = calc.complex_calculation(10, 20, 30)
  puts "Complex calculation result: #{result}"
rescue => e
  puts "Error in complex calculation: #{e.message}"
end

puts "\n=== STRUCTURED LOGGING ==="

require 'logger'
require 'json'

class StructuredLogger
  def initialize(output = STDOUT, level = Logger::INFO)
    @logger = Logger.new(output)
    @logger.level = level
    @logger.formatter = proc do |severity, datetime, progname, msg|
      {
        timestamp: datetime.iso8601,
        level: severity,
        message: msg,
        service: 'ruby-debug-app',
        thread_id: Thread.current.object_id
      }.to_json + "\n"
    end
  end
  
  def info(message, context = {})
    @logger.info({ message: message, context: context }.to_json)
  end
  
  def error(message, context = {}, error = nil)
    log_entry = {
      message: message,
      context: context
    }
    
    if error
      log_entry[:error] = {
        class: error.class.name,
        message: error.message,
        backtrace: error.backtrace&.first(5)
      }
    end
    
    @logger.error(log_entry.to_json)
  end
  
  def debug(message, context = {})
    @logger.debug({ message: message, context: context }.to_json)
  end
  
  def warn(message, context = {})
    @logger.warn({ message: message, context: context }.to_json)
  end
end

# Request Tracing
class RequestTracer
  def initialize
    @current_trace = nil
    @spans = {}
    @mutex = Mutex.new
  end
  
  def start_trace(trace_id = nil)
    trace_id ||= generate_trace_id
    
    @mutex.synchronize do
      @current_trace = {
        trace_id: trace_id,
        started_at: Time.now,
        spans: []
      }
    end
    
    trace_id
  end
  
  def start_span(operation_name, parent_span_id = nil)
    return nil unless @current_trace
    
    span_id = generate_span_id
    span = {
      span_id: span_id,
      parent_span_id: parent_span_id,
      operation_name: operation_name,
      started_at: Time.now,
      tags: {}
    }
    
    @mutex.synchronize do
      @spans[span_id] = span
      @current_trace[:spans] << span
    end
    
    span_id
  end
  
  def finish_span(span_id, error = nil)
    return unless @spans[span_id]
    
    @mutex.synchronize do
      span = @spans[span_id]
      span[:finished_at] = Time.now
      span[:duration] = span[:finished_at] - span[:started_at]
      
      if error
        span[:error] = true
        span[:error_message] = error.message
      end
    end
  end
  
  def add_tag(span_id, key, value)
    return unless @spans[span_id]
    
    @mutex.synchronize do
      @spans[span_id][:tags][key] = value
    end
  end
  
  def finish_trace
    return nil unless @current_trace
    
    @mutex.synchronize do
      @current_trace[:finished_at] = Time.now
      @current_trace[:duration] = @current_trace[:finished_at] - @current_trace[:started_at]
      
      trace = @current_trace.dup
      @current_trace = nil
      @spans.clear
      
      trace
    end
  end
  
  private
  
  def generate_trace_id
    "trace_#{Time.now.to_f}_#{rand(1000)}"
  end
  
  def generate_span_id
    "span_#{Time.now.to_f}_#{rand(1000)}"
  end
end

puts "Structured Logging Example:"

# Create logger
logger = StructuredLogger.new

# Log various events
logger.info("Application started", { version: "1.0.0", environment: "development" })
logger.debug("Processing request", { request_id: "req-123", user_id: 456 })

# Simulate an error
begin
  raise StandardError, "Something went wrong"
rescue => e
  logger.error("Error occurred", { request_id: "req-123" }, e)
end

# Request tracing example
tracer = RequestTracer.new

# Start a trace
trace_id = tracer.start_trace

# Create spans
db_span = tracer.start_span("database_query", nil)
tracer.add_tag(db_span, "table", "users")
tracer.add_tag(db_span, "operation", "select")
sleep(0.1)  # Simulate database work
tracer.finish_span(db_span)

api_span = tracer.start_span("api_call", db_span)
tracer.add_tag(api_span, "endpoint", "/users")
tracer.add_tag(api_span, "method", "GET")
sleep(0.05)  # Simulate API work
tracer.finish_span(api_span)

# Finish trace
trace = tracer.finish_trace
puts "Trace completed: #{trace[:trace_id]}"
puts "Duration: #{trace[:duration].round(3)}s"
puts "Spans: #{trace[:spans].length}"

puts "\n=== MEMORY LEAK DETECTION ==="

class MemoryLeakDetector
  def initialize
    @snapshots = []
    @objects_history = []
  end
  
  def take_snapshot(label = nil)
    GC.start  # Force garbage collection
    
    snapshot = {
      timestamp: Time.now,
      label: label,
      memory_usage: get_memory_usage,
      object_counts: get_object_counts,
      total_objects: ObjectSpace.count_objects[:TOTAL]
    }
    
    @snapshots << snapshot
    snapshot
  end
  
  def detect_leaks(threshold = 0.1)
    return [] if @snapshots.length < 2
    
    leaks = []
    
    (1...@snapshots.length).each do |i|
      prev = @snapshots[i - 1]
      curr = @snapshots[i]
      
      # Check for memory growth
      memory_growth = (curr[:memory_usage] - prev[:memory_usage]) / prev[:memory_usage].to_f
      
      if memory_growth > threshold
        leaks << {
          type: :memory,
          from: prev[:timestamp],
          to: curr[:timestamp],
          growth: memory_growth,
          from_mb: prev[:memory_usage],
          to_mb: curr[:memory_usage]
        }
      end
      
      # Check for object count growth
      object_growth = (curr[:total_objects] - prev[:total_objects]) / prev[:total_objects].to_f
      
      if object_growth > threshold
        leaks << {
          type: :objects,
          from: prev[:timestamp],
          to: curr[:timestamp],
          growth: object_growth,
          from_count: prev[:total_objects],
          to_count: curr[:total_objects]
        }
      end
    end
    
    leaks
  end
  
  def find_object_growth
    return {} if @snapshots.length < 2
    
    growth = {}
    
    (1...@snapshots.length).each do |i|
      prev = @snapshots[i - 1][:object_counts]
      curr = @snapshots[i][:object_counts]
      
      prev.each_key do |type|
        prev_count = prev[type] || 0
        curr_count = curr[type] || 0
        
        if curr_count > prev_count
          growth[type] ||= 0
          growth[type] += curr_count - prev_count
        end
      end
    end
    
    growth.sort_by { |_, count| -count }.first(10).to_h
  end
  
  def generate_report
    return "No snapshots available" if @snapshots.empty?
    
    report = []
    report << "Memory Leak Detection Report"
    report << "=" * 40
    report << "Snapshots: #{@snapshots.length}"
    report << "Time range: #{@snapshots.first[:timestamp]} - #{@snapshots.last[:timestamp]}"
    report << ""
    
    # Memory usage trend
    if @snapshots.length > 1
      first_memory = @snapshots.first[:memory_usage]
      last_memory = @snapshots.last[:memory_usage]
      memory_change = last_memory - first_memory
      
      report << "Memory Usage:"
      report << "  Initial: #{first_memory} KB"
      report << "  Final: #{last_memory} KB"
      report << "  Change: #{memory_change > 0 ? '+' : ''}#{memory_change} KB"
      report << ""
    end
    
    # Object growth
    object_growth = find_object_growth
    unless object_growth.empty?
      report << "Object Growth (Top 10):"
      object_growth.each do |type, count|
        report << "  #{type}: +#{count}"
      end
      report << ""
    end
    
    # Potential leaks
    leaks = detect_leaks(0.05)
    unless leaks.empty?
      report << "Potential Leaks:"
      leaks.each do |leak|
        if leak[:type] == :memory
          report << "  Memory: #{(leak[:growth] * 100).round(2)}% growth"
        else
          report << "  Objects: #{(leak[:growth] * 100).round(2)}% growth"
        end
      end
    end
    
    report.join("\n")
  end
  
  private
  
  def get_memory_usage
    # Simulate memory usage (in real implementation, use system calls)
    rand(50000..100000)
  end
  
  def get_object_counts
    ObjectSpace.count_objects
  end
end

puts "Memory Leak Detection Example:"

detector = MemoryLeakDetector.new

# Take initial snapshot
detector.take_snapshot("initial")

# Simulate memory usage
puts "Creating objects to simulate memory usage..."
data = []
1000.times { |i| data << "Item #{i}" * 100 }

detector.take_snapshot("after_data_creation")

# Create more objects
puts "Creating more objects..."
objects = []
500.times { |i| objects << Object.new }

detector.take_snapshot("after_object_creation")

# Clean up some objects
puts "Cleaning up objects..."
data.clear
GC.start

detector.take_snapshot("after_cleanup")

# Generate report
puts "\n" + detector.generate_report

# Detect leaks
leaks = detector.detect_leaks(0.05)
puts "\nDetected leaks: #{leaks.length}"

puts "\n=== PERFORMANCE PROFILER ==="

class PerformanceProfiler
  def initialize
    @profiles = {}
    @current_profile = nil
  end
  
  def start_profile(name)
    @current_profile = {
      name: name,
      started_at: Time.now,
      method_calls: {},
      memory_snapshots: []
    }
    
    # Take initial memory snapshot
    GC.start
    @current_profile[:memory_snapshots] << {
      timestamp: Time.now,
      memory_usage: get_memory_usage,
      object_counts: get_object_counts
    }
  end
  
  def profile_method(method_name, &block)
    return block.call unless @current_profile
    
    method_stats = @current_profile[:method_calls][method_name] ||= {
      call_count: 0,
      total_time: 0,
      avg_time: 0,
      max_time: 0,
      min_time: Float::INFINITY
    }
    
    start_time = Time.now
    result = block.call
    end_time = Time.now
    
    duration = end_time - start_time
    method_stats[:call_count] += 1
    method_stats[:total_time] += duration
    method_stats[:avg_time] = method_stats[:total_time] / method_stats[:call_count]
    method_stats[:max_time] = [method_stats[:max_time], duration].max
    method_stats[:min_time] = [method_stats[:min_time], duration].min
    
    result
  end
  
  def take_memory_snapshot(label = nil)
    return unless @current_profile
    
    GC.start
    @current_profile[:memory_snapshots] << {
      timestamp: Time.now,
      label: label,
      memory_usage: get_memory_usage,
      object_counts: get_object_counts
    }
  end
  
  def finish_profile
    return nil unless @current_profile
    
    @current_profile[:finished_at] = Time.now
    @current_profile[:duration] = @current_profile[:finished_at] - @current_profile[:started_at]
    
    profile = @current_profile.dup
    @profiles[@current_profile[:name]] = profile
    @current_profile = nil
    
    profile
  end
  
  def generate_report(name)
    profile = @profiles[name]
    return "Profile '#{name}' not found" unless profile
    
    report = []
    report << "Performance Profile: #{name}"
    report << "=" * 40
    report << "Duration: #{profile[:duration].round(3)}s"
    report << "Time range: #{profile[:started_at]} - #{profile[:finished_at]}"
    report << ""
    
    # Method calls
    if profile[:method_calls].any?
      report << "Method Calls:"
      sorted_methods = profile[:method_calls].sort_by { |_, stats| -stats[:total_time] }
      
      sorted_methods.each do |method, stats|
        report << "  #{method}:"
        report << "    Calls: #{stats[:call_count]}"
        report << "    Total: #{stats[:total_time].round(4)}s"
        report << "    Average: #{stats[:avg_time].round(6)}s"
        report << "    Max: #{stats[:max_time].round(6)}s"
        report << "    Min: #{stats[:min_time].round(6)}s"
      end
      report << ""
    end
    
    # Memory usage
    if profile[:memory_snapshots].length > 1
      first_snapshot = profile[:memory_snapshots].first
      last_snapshot = profile[:memory_snapshots].last
      
      memory_change = last_snapshot[:memory_usage] - first_snapshot[:memory_usage]
      object_change = last_snapshot[:object_counts][:TOTAL] - first_snapshot[:object_counts][:TOTAL]
      
      report << "Memory Usage:"
      report << "  Initial: #{first_snapshot[:memory_usage]} KB"
      report << "  Final: #{last_snapshot[:memory_usage]} KB"
      report << "  Change: #{memory_change > 0 ? '+' : ''}#{memory_change} KB"
      report << ""
      report << "Object Count:"
      report << "  Initial: #{first_snapshot[:object_counts][:TOTAL]}"
      report << "  Final: #{last_snapshot[:object_counts][:TOTAL]}"
      report << "  Change: #{object_change > 0 ? '+' : ''}#{object_change}"
    end
    
    report.join("\n")
  end
  
  private
  
  def get_memory_usage
    # Simulate memory usage
    rand(50000..100000)
  end
  
  def get_object_counts
    ObjectSpace.count_objects
  end
end

puts "Performance Profiler Example:"

profiler = PerformanceProfiler.new

# Start profiling
profiler.start_profile("data_processing")

# Profile some methods
profiler.profile_method(:load_data) do
  puts "Loading data..."
  sleep(0.1)  # Simulate data loading
  (1..1000).to_a
end

profiler.take_memory_snapshot("after_load")

profiler.profile_method(:process_data) do
  puts "Processing data..."
  sleep(0.05)  # Simulate processing
  data = (1..500).map { |i| i * 2 }
  data.select(&:even?)
end

profiler.take_memory_snapshot("after_process")

profiler.profile_method(:save_data) do
  puts "Saving data..."
  sleep(0.02)  # Simulate saving
  results = (1..200).map { |i| "Result #{i}" }
  results.join(", ")
end

# Finish profiling
profile = profiler.finish_profile

# Generate report
puts "\n" + profiler.generate_report("data_processing")

puts "\n=== EXCEPTION ANALYZER ==="

class ExceptionAnalyzer
  def initialize
    @exceptions = []
    @patterns = {}
  end
  
  def record_exception(exception, context = {})
    exception_data = {
      timestamp: Time.now,
      class_name: exception.class.name,
      message: exception.message,
      backtrace: exception.backtrace&.first(10),
      context: context
    }
    
    @exceptions << exception_data
    analyze_pattern(exception_data)
  end
  
  def get_error_rate(time_window = 3600)  # Default: 1 hour
    cutoff_time = Time.now - time_window
    recent_exceptions = @exceptions.select { |e| e[:timestamp] > cutoff_time }
    
    recent_exceptions.length.to_f / time_window
  end
  
  def get_top_errors(limit = 10)
    error_counts = Hash.new(0)
    
    @exceptions.each do |exception|
      key = "#{exception[:class_name]}: #{exception[:message]}"
      error_counts[key] += 1
    end
    
    error_counts.sort_by { |_, count| -count }.first(limit)
  end
  
  def find_error_patterns
    patterns = []
    
    @patterns.each do |pattern_key, pattern_data|
      if pattern_data[:frequency] > 2  # Pattern appears more than 2 times
        patterns << {
          pattern: pattern_key,
          frequency: pattern_data[:frequency],
          contexts: pattern_data[:contexts].uniq,
          first_seen: pattern_data[:first_seen],
          last_seen: pattern_data[:last_seen]
        }
      end
    end
    
    patterns.sort_by { |p| -p[:frequency] }
  end
  
  def generate_report
    report = []
    report << "Exception Analysis Report"
    report << "=" * 40
    report << "Total exceptions: #{@exceptions.length}"
    report << "Error rate: #{(get_error_rate * 3600).round(2)}/hour"
    report << ""
    
    # Top errors
    top_errors = get_top_errors(5)
    if top_errors.any?
      report << "Top Errors:"
      top_errors.each_with_index do |(error, count), i|
        report << "  #{i + 1}. #{error} (#{count} occurrences)"
      end
      report << ""
    end
    
    # Error patterns
    patterns = find_error_patterns
    if patterns.any?
      report << "Error Patterns:"
      patterns.each_with_index do |pattern, i|
        report << "  #{i + 1}. #{pattern[:pattern]}"
        report << "     Frequency: #{pattern[:frequency]}"
        report << "     Contexts: #{pattern[:contexts].join(', ')}"
        report << ""
      end
    end
    
    report.join("\n")
  end
  
  private
  
  def analyze_pattern(exception_data)
    # Create pattern key based on exception class and common message patterns
    pattern_key = "#{exception_data[:class_name]}:#{extract_pattern(exception_data[:message])}"
    
    @patterns[pattern_key] ||= {
      frequency: 0,
      contexts: [],
      first_seen: exception_data[:timestamp],
      last_seen: exception_data[:timestamp]
    }
    
    pattern = @patterns[pattern_key]
    pattern[:frequency] += 1
    pattern[:contexts] << exception_data[:context]
    pattern[:last_seen] = exception_data[:timestamp]
  end
  
  def extract_pattern(message)
    # Extract common patterns from error messages
    pattern = message.dup
    
    # Replace numbers with placeholder
    pattern.gsub!(/\d+/, 'N')
    
    # Replace UUIDs with placeholder
    pattern.gsub!(/\b[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\b/i, 'UUID')
    
    # Replace file paths with placeholder
    pattern.gsub!(/\/[^\s]+/, 'PATH')
    
    pattern
  end
end

puts "Exception Analyzer Example:"

analyzer = ExceptionAnalyzer.new

# Simulate various exceptions
5.times do |i|
  begin
    raise StandardError, "User not found with ID: #{1000 + i}"
  rescue => e
    analyzer.record_exception(e, { action: "get_user", user_id: 1000 + i })
  end
end

3.times do |i|
  begin
    raise StandardError, "Database connection failed to localhost:5432"
  rescue => e
    analyzer.record_exception(e, { action: "connect_db", host: "localhost" })
  end
end

2.times do |i|
  begin
    raise StandardError, "Invalid input: parameter cannot be nil"
  rescue => e
    analyzer.record_exception(e, { action: "validate_input", parameter: "email" })
  end
end

# Generate analysis report
puts analyzer.generate_report

puts "\n=== SELF-HEALING CODE ==="

class SelfHealingCode
  def initialize
    @error_patterns = {}
    @healing_strategies = {}
    @retry_attempts = {}
  end
  
  def register_error_pattern(pattern, &healing_strategy)
    @error_patterns[pattern] = pattern
    @healing_strategies[pattern] = healing_strategy
  end
  
  def execute_with_healing(operation_name, max_retries = 3, &block)
    @retry_attempts[operation_name] ||= 0
    
    begin
      result = block.call
      @retry_attempts[operation_name] = 0  # Reset on success
      result
    rescue => e
      @retry_attempts[operation_name] += 1
      
      if @retry_attempts[operation_name] <= max_retries
        healing_strategy = find_healing_strategy(e)
        
        if healing_strategy
          puts "🔧 Applying healing strategy for: #{e.class.name}"
          healing_strategy.call(e, @retry_attempts[operation_name])
          
          # Retry the operation
          sleep(2 ** @retry_attempts[operation_name])  # Exponential backoff
          retry
        else
          puts "❌ No healing strategy found for: #{e.class.name}"
          raise e
        end
      else
        puts "❌ Max retries exceeded for: #{operation_name}"
        raise e
      end
    end
  end
  
  def get_retry_stats
    @retry_attempts.dup
  end
  
  private
  
  def find_healing_strategy(error)
    @error_patterns.each do |pattern, strategy|
      if error.class.name.match?(pattern) || error.message.match?(pattern)
        return @healing_strategies[pattern]
      end
    end
    
    nil
  end
end

# Custom exception classes
class ConnectionError < StandardError; end
class TimeoutError < StandardError; end
class ValidationError < StandardError; end

puts "Self-Healing Code Example:"

healer = SelfHealingCode.new

# Register healing strategies
healer.register_error_pattern(/ConnectionError/) do |error, attempt|
  puts "🔄 Reconnecting to database (attempt #{attempt})"
  # Simulate reconnection
  sleep(0.1)
end

healer.register_error_pattern(/TimeoutError/) do |error, attempt|
  puts "⏱️ Increasing timeout (attempt #{attempt})"
  # Simulate timeout increase
  sleep(0.05)
end

healer.register_error_pattern(/ValidationError/) do |error, attempt|
  puts "✅ Using default values (attempt #{attempt})"
  # Simulate using defaults
end

# Simulate operations with healing
def database_operation
  # Simulate connection error
  raise ConnectionError, "Database connection lost" if rand < 0.3
  "Database operation successful"
end

def api_call
  # Simulate timeout
  raise TimeoutError, "Request timed out" if rand < 0.2
  "API call successful"
end

def process_data(input)
  # Simulate validation error
  raise ValidationError, "Invalid input data" if rand < 0.1
  "Data processed successfully"
end

# Execute with self-healing
puts "Testing self-healing mechanisms:"

result1 = healer.execute_with_healing("database_op") do
  database_operation
end
puts "Database result: #{result1}"

result2 = healer.execute_with_healing("api_call") do
  api_call
end
puts "API result: #{result2}"

result3 = healer.execute_with_healing("data_processing") do
  process_data(nil)
end
puts "Data processing result: #{result3}"

puts "Retry stats: #{healer.get_retry_stats}"

puts "\n=== AUTOMATED TESTING ==="

class AutomatedDebugger
  def initialize
    @test_cases = []
    @failed_tests = []
    @patterns = {}
  end
  
  def add_test_case(description, &test_block)
    @test_cases << {
      description: description,
      test: test_block,
      timestamp: Time.now
    }
  end
  
  def run_tests
    @failed_tests.clear
    
    @test_cases.each do |test_case|
      begin
        test_case[:test].call
        puts "✅ #{test_case[:description]}"
      rescue => e
        puts "❌ #{test_case[:description]}: #{e.message}"
        @failed_tests << {
          test: test_case,
          error: e,
          timestamp: Time.now
        }
        
        analyze_failure(test_case, e)
      end
    end
    
    generate_failure_report
  end
  
  def auto_fix_simple_failures
    fixes_applied = 0
    
    @failed_tests.each do |failed_test|
      fix = suggest_fix(failed_test)
      
      if fix && fix[:auto_applicable]
        puts "🔧 Auto-applying fix for: #{failed_test[:test][:description]}"
        apply_fix(fix)
        fixes_applied += 1
      end
    end
    
    fixes_applied
  end
  
  def generate_test_suite
    suite = []
    
    @failed_tests.each do |failed_test|
      fix = suggest_fix(failed_test)
      
      if fix
        suite << {
          description: "Regression test for: #{failed_test[:test][:description]}",
          test: generate_regression_test(failed_test, fix)
        }
      end
    end
    
    suite
  end
  
  private
  
  def analyze_failure(test_case, error)
    pattern_key = "#{error.class.name}:#{extract_error_pattern(error.message)}"
    
    @patterns[pattern_key] ||= {
      count: 0,
      test_cases: [],
      first_seen: Time.now
    }
    
    @patterns[pattern_key][:count] += 1
    @patterns[pattern_key][:test_cases] << test_case[:description]
  end
  
  def extract_error_pattern(message)
    # Extract pattern from error message
    pattern = message.dup
    pattern.gsub!(/\d+/, 'N')
    pattern.gsub!(/'[^']*'/, "'STRING'")
    pattern.gsub!(/"[^"]*"/, '"STRING"')
    pattern
  end
  
  def suggest_fix(failed_test)
    error = failed_test[:error]
    
    case error.class.name
    when 'NoMethodError'
      if error.message.include?('undefined method')
        method_name = error.message.match(/undefined method `([^']+)'/)[1]
        {
          type: :method_missing,
          suggestion: "Define method '#{method_name}' or check for typos",
          auto_applicable: false
        }
      end
    when 'ArgumentError'
      if error.message.include?('wrong number of arguments')
        {
          type: :argument_count,
          suggestion: "Check method signature and argument count",
          auto_applicable: false
        }
      end
    when 'TypeError'
      if error.message.include?('can\'t convert')
        {
          type: :type_conversion,
          suggestion: "Add type conversion or validation",
          auto_applicable: false
        }
      end
    end
  end
  
  def apply_fix(fix)
    # In a real implementation, this would modify the code
    puts "Applied fix: #{fix[:suggestion]}"
  end
  
  def generate_regression_test(failed_test, fix)
    # Generate a test to prevent regression
    lambda do
      # Test would verify the fix works
      puts "Running regression test for: #{failed_test[:test][:description]}"
    end
  end
  
  def generate_failure_report
    report = []
    report << "Automated Debugging Report"
    report << "=" * 40
    report << "Total tests: #{@test_cases.length}"
    report << "Failed tests: #{@failed_tests.length}"
    report << "Success rate: #{((@test_cases.length - @failed_tests.length).to_f / @test_cases.length * 100).round(2)}%"
    report << ""
    
    if @failed_tests.any?
      report << "Failed Tests:"
      @failed_tests.each_with_index do |failed, i|
        report << "  #{i + 1}. #{failed[:test][:description]}"
        report << "     Error: #{failed[:error].class.name}"
        report << "     Message: #{failed[:error].message}"
      end
      report << ""
    end
    
    if @patterns.any?
      report << "Error Patterns:"
      @patterns.each do |pattern, data|
        report << "  #{pattern}: #{data[:count]} occurrences"
        report << "    Affected tests: #{data[:test_cases].join(', ')}"
      end
    end
    
    report.join("\n")
  end
end

puts "Automated Testing Example:"

debugger = AutomatedDebugger.new

# Add test cases
debugger.add_test_case("Array length calculation") do
  array = [1, 2, 3]
  array.length
end

debugger.add_test_case("String concatenation") do
  "hello" + " " + "world"
end

debugger.add_test_case("Method call with valid arguments") do
  Math.sqrt(16)
end

debugger.add_test_case("Method call with invalid arguments") do
  Math.sqrt("invalid")  # This will fail
end

debugger.add_test_case("Accessing array element") do
  array = [1, 2, 3]
  array[1]
end

debugger.add_test_case("Accessing invalid array element") do
  array = [1, 2, 3]
  array[10]  # This will fail
end

# Run tests
puts "Running automated tests..."
debugger.run_tests

# Try to auto-fix simple failures
fixes_applied = debugger.auto_fix_simple_failures
puts "\nApplied #{fixes_applied} automatic fixes"

# Generate regression tests
regression_tests = debugger.generate_test_suite
puts "\nGenerated #{regression_tests.length} regression tests"

puts "\n=== DEBUGGING SUMMARY ==="
puts "- Ruby Debugger: Breakpoints, variable inspection, step-through debugging"
puts "- Structured Logging: JSON logging, request tracing, distributed tracing"
puts "- Memory Leak Detection: Memory monitoring, object tracking, pattern analysis"
puts "- Performance Profiling: Method timing, memory profiling, bottleneck identification"
puts "- Exception Analysis: Error tracking, pattern detection, trend analysis"
puts "- Self-Healing Code: Automatic recovery, circuit breakers, retry strategies"
puts "- Automated Testing: Test discovery, failure analysis, auto-fix suggestions"
puts "\nAll examples demonstrate comprehensive debugging techniques in Ruby!"
