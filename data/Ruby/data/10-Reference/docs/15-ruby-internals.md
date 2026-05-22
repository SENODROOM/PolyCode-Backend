# Ruby Internals

## 🔬 Overview

Understanding Ruby internals gives you deep insight into how Ruby works under the hood. This guide explores Ruby's internal architecture, memory management, and execution model.

## 🏗️ Ruby Architecture

### 1. Object Model

Ruby's object-oriented architecture:

```ruby
# Everything is an object
puts 5.is_a?(Object)           # => true
puts "hello".is_a?(Object)       # => true
puts nil.is_a?(Object)           # => true
puts Class.is_a?(Object)          # => true

# Object identity and equality
a = "hello"
b = "hello"

puts a.object_id == b.object_id    # => false (different objects)
puts a == b                      # => true (same value)

# Class hierarchy
puts String.superclass              # => Object
puts Class.superclass               # => Module
puts Module.superclass              # => Object
puts BasicObject.superclass        # => nil
```

### 2. Method Lookup Chain

How Ruby finds methods:

```ruby
# Method lookup path
class A
  def test_method
    "From A"
  end
end

class B < A
  def test_method
    "From B"
  end
end

class C < B
  # No test_method defined
end

obj = C.new

# Lookup chain: C -> B -> A -> Object -> Kernel
puts obj.test_method  # => "From B"

# Method visibility
class VisibilityDemo
  def public_method
    "Public"
  end
  
  protected
  
  def protected_method
    "Protected"
  end
  
  private
  
  def private_method
    "Private"
  end
end

obj = VisibilityDemo.new
puts obj.public_method      # => "Public"
puts obj.protected_method   # => Error (protected)
puts obj.private_method     # => Error (private)

# Inside class
class VisibilityDemo
  def test_visibility
    puts public_method      # => "Public"
    puts protected_method   # => "Protected"
    puts private_method     # => "Private"
  end
end
```

### 3. Singleton Classes

Understanding Ruby's singleton classes:

```ruby
# Every object has a singleton class
str = "hello"
puts str.singleton_class  # => #<Class:#<Class:0x...>>

# Adding methods to singleton class
def str.add_custom_method
  "Custom method for this string"
end

puts str.add_custom_method  # => "Custom method for this string"

# Different objects, different singleton classes
str1 = "hello"
str2 = "world"

def str1.unique_method
  "Unique to str1"
end

# str2 doesn't have unique_method
puts str1.unique_method  # => "Unique to str1"
puts str2.unique_method rescue nil  # => NoMethodError

# Singleton classes for all instances
class String
  def self.add_class_method
    "Class method for all strings"
  end
end

puts "hello".add_class_method  # => Error (instance method)
puts String.add_class_method    # => "Class method for all strings"
```

## 🧠 Memory Management

### 1. Object Allocation

How Ruby manages memory:

```ruby
require 'objspace'

class AllocationTracker
  def self.track_allocations
    # Get current allocation statistics
    stats_before = GC.stat
    
    # Allocate objects
    objects = 1000.times.map { |i| "object_#{i}" }
    
    # Get allocation statistics after
    stats_after = GC.stat
    
    allocated_objects = stats_after[:total_allocated_objects] - stats_before[:total_allocated_objects]
    allocated_bytes = stats_after[:total_allocated_bytes] - stats_before[:total_allocated_bytes]
    
    puts "Allocated #{allocated_objects} objects"
    puts "Allocated #{allocated_bytes} bytes"
    
    # Track object types
    object_counts = ObjectSpace.count_objects
    sorted_counts = object_counts.sort_by { |klass, count| -count }.first(5)
    
    puts "Top allocated object types:"
    sorted_counts.each do |klass, count|
      puts "  #{klass}: #{count}"
    end
    
    objects
  end
end

# Usage
AllocationTracker.track_allocations
```

### 2. Garbage Collection

Understanding Ruby's GC:

```ruby
class GCAnalyzer
  def self.analyze_gc
    # Get detailed GC statistics
    stats = GC.stat
    
    puts "GC Statistics:"
    puts "  Count: #{stats[:count]}"
    puts "  Heap used: #{stats[:heap_used]}"
    puts "  Heap length: #{stats[:heap_length]}"
    puts "  Total allocated: #{stats[:total_allocated_objects]}"
    puts "  Total freed: #{stats[:total_freed_objects]}"
    puts "  Heap increment: #{stats[:heap_increment]}"
    puts "  Heap increment: #{stats[:heap_increment]}"
    
    # GC generations
    puts "GC Generations:"
    stats[:heap_used].each_with_index do |size, index|
      puts "  Generation #{index}: #{size} slots"
    end
  end
  
  def self.force_gc_and_analyze
    puts "Before GC:"
    analyze_gc
    
    GC.start  # Force garbage collection
    
    puts "After GC:"
    analyze_gc
  end
  
  def self.gc_stress_test
    puts "GC Stress Test:"
    
    # Allocate many objects to trigger GC
    objects = []
    10000.times do |i|
      objects << "test_object_#{i}" * 100
    end
    
    analyze_gc
  end
end

# Usage
GCAnalyzer.analyze_gc
GCAnalyzer.force_gc_and_analyze
GCAnalyzer.gc_stress_test
```

### 3. Memory Leaks

Detecting and preventing memory leaks:

```ruby
class MemoryLeakDetector
  def self.detect_leaks
    # Baseline memory
    GC.start
    baseline_objects = ObjectSpace.count_objects
    
    yield  # Code that might leak
    
    # Check for leaks
    GC.start
    final_objects = ObjectSpace.count_objects
    leaked_objects = final_objects - baseline_objects
    
    if leaked_objects > 0
      puts "Memory leak detected: #{leaked_objects} objects"
      
      # Find leaked object types
      leaked = ObjectSpace.count_objects
      leaked.each do |klass, count|
        if count > baseline_objects[klass]
          leaked_count = count - baseline_objects[klass]
          puts "Leaked #{klass}: #{leaked_count} instances"
        end
      end
    end
  end
  
  def self.find_retained_references
    # Find objects that are being retained
    retained = ObjectSpace.each_object.select do |obj|
      # Check if object has references that might prevent GC
      obj.instance_variables.any? do |var|
        value = obj.instance_variable_get(var)
        value.is_a?(Array) || value.is_a?(Hash)
      end
    end
    
    puts "Objects with potential retention issues:"
    retained.each do |obj|
      puts "  #{obj.class} (#{obj.object_id})"
    end
  end
end

# Test for leaks
MemoryLeakDetector.detect_leaks do
  # Potential leak: global array
  $global_cache ||= []
  1000.times { |i| $global_cache << "item_#{i}" }
end
```

## ⚙️ Execution Model

### 1. Method Dispatch

How Ruby calls methods:

```ruby
class MethodDispatchDemo
  def initialize
    @methods = {}
  end
  
  def add_method(name, &block)
    @methods[name] = block
  end
  
  def call_method(name, *args)
    method = @methods[name]
    if method
      method.call(*args)
    else
      # Fallback to method_missing
      method_missing(name, *args)
    end
  end
  
  def method_missing(name, *args)
    puts "Method #{name} not found"
    "Fallback for #{name}"
  end
  
  def respond_to_missing?(name, include_private = false)
    @methods.key?(name) || super
  end
end

demo = MethodDispatchDemo.new
demo.add_method(:custom_method) { |x| x * 2 }

puts demo.call_method(:custom_method, 5)  # => 10
puts demo.call_method(:unknown_method, 5) # => "Fallback for unknown_method"
```

### 2. Block and Closure Semantics

Understanding block execution:

```ruby
class BlockSemantics
  def self.block_execution_demo
    # Block binding and scope
    outer_var = "outer"
    
    result = [1, 2, 3].map do |num|
      # Block has access to outer variables
      "#{outer_var}-#{num}"
    end
    
    puts result
    # => ["outer-1", "outer-2", "outer-3"]
    
    # Block parameter binding
    def create_block
      local_var = "local"
      proc { |arg| "#{local_var}-#{arg}-#{outer_var}" }
    end
    
    block = create_block
    puts block.call("test")
    # => "local-test-outer"
  end
  
  def self.proc_vs_lambda_semantics
    # Return behavior differences
    def test_return(closure)
      result = closure.call
      "Method result: #{result}"
    end
    
    lambda_proc = -> { "Lambda result" }
    regular_proc = Proc.new { "Proc result" }
    
    puts test_return(lambda_proc)   # => "Method result: Lambda result"
    puts test_return(regular_proc) # => "Proc result"
    
    # Argument checking
    def test_args(closure)
      closure.call(1, 2)
    rescue ArgumentError => e
      "Error: #{e.message}"
    end
    
    strict_lambda = ->(x, y) { x + y }
    lenient_proc = Proc.new { |x, y| x + y }
    
    puts test_args(strict_lambda)  # => "Method result: 3"
    puts test_args(lenient_proc)  # => "Method result: 3"
    
    # Extra arguments
    puts test_args(->(x, y) { x + y }, 1, 2, 3)
    # => "Error: wrong number of arguments (given 3, expected 2)"
    
    puts test_args(Proc.new { |x, y| x + y }, 1, 2, 3)
    # => "Method result: 3" (ignores extra args)
  end
end

BlockSemantics.block_execution_demo
BlockSemantics.proc_vs_lambda_semantics
```

### 3. Constant Lookup

How Ruby finds constants:

```ruby
class ConstantLookup
  PARENT_CONSTANT = "Parent"
  
  class ChildClass
    CHILD_CONSTANT = "Child"
    
    def self.find_constant(name)
      const_get(name)
    rescue NameError
        "Constant not found: #{name}"
    end
  end
end

# Constant lookup path
puts ConstantLookup::PARENT_CONSTANT  # => "Parent"
puts ConstantLookup::ChildClass::CHILD_CONSTANT  # => "Child"

# Dynamic constant lookup
puts ConstantLookup::ChildClass.find_constant(:CHILD_CONSTANT)  # => "Child"
puts ConstantLookup::ChildClass.find_constant(:MISSING)    # => "Constant not found: MISSING"

# Constant inheritance
class GrandParent
  GRAND_PARENT_CONSTANT = "GrandParent"
end

class Parent < GrandParent
  PARENT_CONSTANT = "Parent"
end

class Child < Parent
  CHILD_CONSTANT = "Child"
  
  def self.show_constants
    puts GRAND_PARENT_CONSTANT  # => "GrandParent"
    puts PARENT_CONSTANT       # => "Parent"
    puts CHILD_CONSTANT        # => "Child"
  end
end

Child.show_constants
```

## 🔧 C Extension Integration

### 1. Ruby C API Overview

Understanding Ruby's C interface:

```c
// Example C extension (example.c)
#include <ruby.h>

// Function to be called from Ruby
VALUE example_function(VALUE self, VALUE arg) {
    // Convert Ruby objects to C types
    int number = NUM2INT(arg);
    
    // Perform C operation
    int result = number * 2;
    
    // Convert back to Ruby object
    return INT2NUM(result);
}

// Initialize the extension
void Init_example(void) {
    // Define the Example module
    VALUE mExample = rb_define_module("Example");
    
    // Define the function
    rb_define_module_function(mExample, "multiply_by_two", example_function, 1);
    
    // Register the module
    rb_register_module(mExample);
}

// extconf.rb
require 'mkmf'
$CFLAGS += " -O3"
create_makefile('example')

// Makefile generation
ruby extconf.rb
make
```

### 2. Ruby C API Patterns

Common patterns in C extensions:

```c
// Memory management in C extensions
VALUE safe_string_operation(VALUE self, VALUE str) {
    // Check argument type
    Check_Type(str, T_STRING);
    
    // Get C string from Ruby string
    char *c_str = StringValuePtr(str);
    long len = RSTRING_LEN(str);
    
    // Allocate new Ruby string
    VALUE result = rb_str_new(cstr, len * 2);
    
    return result;
}

// Exception handling in C extensions
VALUE safe_division(VALUE self, VALUE a, VALUE b) {
    int a_int = NUM2INT(a);
    int b_int = NUM2INT(b);
    
    if (b_int == 0) {
        // Raise Ruby exception
        rb_raise(rb_eZeroDivError, "division by zero");
        return Qnil;
    }
    
    return INT2NUM(a_int / b_int);
}

// Working with Ruby arrays in C
VALUE process_array(VALUE self, VALUE array) {
    // Check if argument is array
    Check_Type(array, T_ARRAY);
    
    long len = RARRAY_LEN(array);
    VALUE *c_array = RARRAY_PTR(array);
    
    // Process each element
    for (long i = 0; i < len; i++) {
        VALUE element = c_array[i];
        // Convert to C and back to Ruby
        int num = NUM2INT(element);
        VALUE doubled = INT2NUM(num * 2);
        rb_ary_store(array, i, doubled);
    }
    
    return array;
}
```

### 3. FFI (Foreign Function Interface)

Using FFI for C libraries:

```ruby
require 'ffi'

module Math
  extend FFI::Library
  
  # Load C math library
  ffi_lib 'm'
  
  # Define C functions
  attach_function :pow, [:double, :double], :double
  attach_function :sin, [:double], :double
  attach_function :cos, [:double], :double
end

# Usage
puts Math.pow(2.0, 3.0)  # => 8.0
puts Math.sin(Math::PI / 2)  # => 1.0
puts Math.cos(0)              # => 1.0

# FFI with structs
module System
  extend FFI::Library
  ffi_lib FFI::Library::LIBC
  
  class TimeSpec < FFI::Struct
    layout :tv_sec, :long,
            :tv_usec, :long
  end
end

# Usage
time_spec = System::TimeSpec.new
System.gettimeofday(time_spec.pointer, nil)

puts "Seconds: #{time_spec[:tv_sec]}"
puts "Microseconds: #{time_spec[:tv_usec]}"
```

## 🔬 Debugging Ruby Internals

### 1. Ruby Debugger Integration

Using Ruby's debugging hooks:

```ruby
# TracePoint for method tracing
require 'tracepoint'

class MethodTracer
  def self.trace_method_calls(target_class)
    # Create tracepoint for all methods in target class
    target_class.instance_methods.each do |method_name|
      TracePoint.new(:method_call) do |tp|
        tp.enable(target: target_class, method: method_name) do
          # Called when method is invoked
          puts "Called: #{target_class}##{method_name}"
          puts "  Arguments: #{tp.binding.local_variables.map { |v| "#{v}=#{tp.binding.local_variable_get(v)}" }.join(', ')}"
          puts "  Return value: #{tp.return_value}"
        end
      end
    end
  end
  
  def self.stop_tracing
    TracePoint.trace.each(&:disable)
  end
end

# Usage
class TestClass
  def test_method(arg1, arg2)
    arg1 + arg2
  end
  
  def another_method
    "Hello from another_method"
  end
end

# Start tracing
MethodTracer.trace_method_calls(TestClass)

# Call methods
obj = TestClass.new
obj.test_method("hello", "world")
obj.another_method

# Stop tracing
MethodTracer.stop_tracing
```

### 2. ObjectSpace Inspection

Deep object inspection:

```ruby
require 'objspace'

class ObjectInspector
  def self.inspect_object(obj)
    puts "Object: #{obj.inspect}"
    puts "Class: #{obj.class}"
    puts "Object ID: #{obj.object_id}"
    
    # Instance variables
    ivars = obj.instance_variables
    puts "Instance variables: #{ivars}"
    ivars.each do |ivar|
      value = obj.instance_variable_get(ivar)
      puts "  #{ivar}: #{value.inspect}"
    end
    
    # Methods
    methods = obj.methods(false)  # false = only methods defined in this class
    puts "Methods: #{methods.length}"
    
    # Memory usage
    obj_size = ObjectSpace.memsize_of(obj)
    puts "Memory size: #{obj_size} bytes"
  end
  
  def self.find_all_instances(klass)
    ObjectSpace.each_object.select { |obj| obj.is_a?(klass) }
  end
  
  def self.object_count_by_class
    counts = Hash.new(0)
    ObjectSpace.each_object do |obj|
      counts[obj.class] += 1
    end
    
    counts.sort_by { |klass, count| -count }.first(10)
  end
end

# Usage
test_string = "Hello, Ruby World!"
ObjectInspector.inspect_object(test_string)

# Find all string objects
strings = ObjectInspector.find_all_instances(String)
puts "Total strings: #{strings.length}"

# Object count by class
counts = ObjectInspector.object_count_by_class
puts "Top 10 object types:"
counts.each { |klass, count| puts "  #{klass}: #{count}" }
```

### 3. GC Hooks

Garbage collection hooks:

```ruby
class GCHooks
  def self.enable_gc_hooks
    # Hook into GC start
    GC::Profiler.enable
    
    # Custom GC tracking
    @gc_start_time = nil
    @gc_count = 0
    
    # Define GC start hook
    define_singleton_method(:gc_start_hook) do
      @gc_start_time = Time.now
      @gc_count += 1
      puts "GC ##{@gc_count} started at #{@gc_start_time}"
    end
    
    # Define GC end hook
    define_singleton_method(:gc_end_hook) do
      gc_duration = Time.now - @gc_start_time
      puts "GC ##{@gc_count} ended after #{gc_duration.round(4)}s"
    end
    
    # Register hooks
    ObjectSpace.define_finalizer { |id| puts "Object #{id} finalized" }
  end
  
  def self.memory_pressure_test
    puts "Memory pressure test..."
    
    # Allocate memory until GC triggers
    objects = []
    100000.times do |i|
      objects << "test_object_#{i}" * 100
    end
    
    puts "Allocated #{objects.length} objects"
    puts "GC count: #{@gc_count}"
  end
end

# Enable GC hooks
GCHooks.enable_gc_hooks

# Test memory pressure
GCHooks.memory_pressure_test
```

## 🎯 Performance Implications

### 1. Method Call Overhead

Understanding method call costs:

```ruby
class MethodOverhead
  def self.benchmark_method_calls
    iterations = 1000000
    
    # Direct method call
    def direct_method(x)
      x * 2
    end
    
    # Method through variable
    method_var = method(:direct_method)
    
    # Method through send
    Benchmark.bm do |x|
      x.report("direct call") do
        iterations.times { |i| direct_method(i) }
      end
      
      x.report("variable call") do
        iterations.times { |i| method_var.call(i) }
      end
      
      x.report("send call") do
        iterations.times { |i| send(:direct_method, i) }
      end
      
      x.report("eval call") do
        iterations.times { |i| eval("direct_method(#{i})") }
      end
    end
  end
end

MethodOverhead.benchmark_method_calls
# Typical results:
# direct call:    0.120000
# variable call:  0.130000
# send call:     0.180000
# eval call:      2.500000 (much slower)
```

### 2. Object Creation Cost

Object allocation performance:

```ruby
class ObjectCreationCost
  def self.benchmark_object_creation
    iterations = 100000
    
    Benchmark.bm do |x|
      x.report("string creation") do
        iterations.times { |i| "string_#{i}" }
      end
      
      x.report("symbol creation") do
        iterations.times { |i| :"symbol_#{i}" }
      end
      
      x.report("array creation") do
        iterations.times { |i| [i, i * 2] }
      end
      
      x.report("hash creation") do
        iterations.times { |i| { key: i, value: i * 2 } }
      end
    end
  end
end

ObjectCreationCost.benchmark_object_creation
# Typical results:
# string creation:   0.080000
# symbol creation:   0.060000
# array creation:    0.100000
# hash creation:     0.150000
```

## 🎓 Exercises

### Beginner Exercises

1. **Object Inspection**: Build an object inspector tool
2. **Memory Tracking**: Create a memory allocation tracker
3. **Method Tracing**: Implement a simple method tracer

### Intermediate Exercises

1. **GC Analysis**: Build a GC statistics analyzer
2. **Performance Profiling**: Create a performance profiler
3. **C Extension**: Write a simple C extension

### Advanced Exercises

1. **Memory Leak Detection**: Build a comprehensive leak detector
2. **Ruby Interpreter**: Implement a simple Ruby interpreter
3. **Performance Optimization**: Optimize a real Ruby application

---

## 🎯 Summary

Understanding Ruby internals provides:

- **Deep language knowledge** - How Ruby really works
- **Performance insights** - Why certain operations are fast/slow
- **Memory mastery** - Control over memory allocation and GC
- **Extension capabilities** - Interface with C libraries
- **Debugging power** - Advanced debugging and profiling techniques

Master Ruby internals to write highly optimized and efficient Ruby code!
