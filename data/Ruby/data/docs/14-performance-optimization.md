# Performance Optimization in Ruby

## 🚀 Overview

Performance optimization is crucial for building scalable Ruby applications. This guide covers techniques, tools, and best practices for making Ruby code faster and more efficient.

## 🔍 Performance Measurement

### 1. Benchmarking with Ruby's Benchmark Module

Accurate performance measurement:

```ruby
require 'benchmark'

# Simple benchmarking
Benchmark.bm do |x|
  x.report("map") do
    (1..1000000).map { |n| n * 2 }
  end
  
  x.report("each") do
    result = []
    (1..1000000).each { |n| result << n * 2 }
    result
  end
  
  x.report("inject") do
    (1..1000000).inject([]) { |arr, n| arr << n * 2 }
  end
end

# Benchmark with memory profiling
Benchmark.bmbm do |x|
  x.report("string concatenation") do
    result = ""
    10000.times { |i| result += "item#{i}" }
    result
  end
  
  x.report("array join") do
    items = 10000.times.map { |i| "item#{i}" }
    items.join
  end
end
```

### 2. Memory Profiling

Track memory usage and leaks:

```ruby
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
    
    puts "#{label}: #{(end_time - start_time).round(4)}s"
    puts "#{label}: #{end_memory - start_memory} objects allocated"
    
    result
  end
end

# Usage
MemoryProfiler.profile("data processing") do
  data = (1..10000).map { |i| "item_#{i}" }
  processed = data.map(&:upcase)
end
```

### 3. Object Allocation Tracking

Monitor object creation:

```ruby
require 'objspace'

class ObjectTracker
  def self.track_allocations
    objects_before = ObjectSpace.count_objects
    
    yield
    
    objects_after = ObjectSpace.count_objects
    allocated = objects_after - objects_before
    
    puts "Allocated #{allocated} objects"
    
    # Show most allocated classes
    allocations = ObjectSpace.count_objects
    sorted_allocations = allocations.sort_by { |klass, count| -count }.first(5)
    
    puts "Top allocated classes:"
    sorted_allocations.each do |klass, count|
      puts "  #{klass}: #{count}"
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
```

## ⚡ Optimization Techniques

### 1. String Optimization

Efficient string handling:

```ruby
# Bad: String concatenation in loops
def bad_concatenation(items)
  result = ""
  items.each { |item| result += item.to_s }  # Creates new strings
  result
end

# Good: Array join
def good_concatenation(items)
  items.map(&:to_s).join  # More efficient
end

# Good: StringIO for large concatenations
require 'stringio'

def stream_concatenation(items)
  io = StringIO.new
  items.each { |item| io.write(item.to_s) }
  io.string
end

# Benchmark results
items = (1..10000).map { |i| "item_#{i}" }

Benchmark.bm do |x|
  x.report("bad concatenation") { bad_concatenation(items) }
  x.report("good concatenation") { good_concatenation(items) }
  x.report("stream concatenation") { stream_concatenation(items) }
end

# Interpolation vs concatenation
def interpolation_test(name, age)
  "Hello, #{name}! You are #{age} years old."  # Fast
end

def concatenation_test(name, age)
  "Hello, " + name + "! You are " + age.to_s + " years old."  # Slow
end
```

### 2. Array Optimization

Efficient array operations:

```ruby
# Bad: Repeated array operations
def bad_array_processing(items)
  result = []
  items.each do |item|
    if item.even?
      result << item * 2
    end
  end
  result
end

# Good: Functional operations
def good_array_processing(items)
  items.select(&:even?).map { |item| item * 2 }
end

# In-place operations for large arrays
def in_place_operations!(array)
  # Sort in place
  array.sort!
  
  # Remove duplicates in place
  array.uniq!
  
  # Filter in place
  array.select!(&:odd?)
  
  array
end

# Pre-allocate arrays
def preallocated_processing(size)
  result = Array.new(size)  # Pre-allocate
  size.times { |i| result[i] = i * 2 }
  result
end
```

### 3. Hash Optimization

Efficient hash usage:

```ruby
# Use symbols as keys for better performance
def fast_hash_access
  # Bad: String keys
  config = {
    "database_host" => "localhost",
    "database_port" => 5432,
    "database_name" => "myapp"
  }
  
  # Good: Symbol keys
  config = {
    database_host: "localhost",
    database_port: 5432,
    database_name: "myapp"
  }
  
  config[:database_host]  # Faster lookup
end

# Hash with default values
def hash_with_defaults(overrides = {})
  defaults = {
    timeout: 30,
    retries: 3,
    verbose: false
  }
  
  defaults.merge(overrides)  # Efficient merge
end

# Hash for caching
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
    sleep(0.1)  # Simulate expensive operation
    key.to_s.upcase
  end
end
```

### 4. Loop Optimization

Efficient iteration patterns:

```ruby
# Use appropriate iteration methods
numbers = (1..1000000).to_a

# Bad: Each with manual counting
def bad_count_evens(array)
  count = 0
  array.each { |n| count += 1 if n.even? }
  count
end

# Good: Built-in methods
def good_count_evens(array)
  array.count(&:even?)
end

# Use while for large arrays when breaking early
def find_first_match(array, target)
  i = 0
  while i < array.length
    return array[i] if array[i] == target
    i += 1
  end
  nil
end

# Use times for known iterations
def optimized_times(n)
  result = Array.new(n)
  n.times { |i| result[i] = i * 2 }
  result
end

# Benchmark
Benchmark.bm do |x|
  x.report("bad count") { bad_count_evens(numbers) }
  x.report("good count") { good_count_evens(numbers) }
  x.report("while loop") { find_first_match(numbers, 500000) }
  x.report("times loop") { optimized_times(500000) }
end
```

## 🗄️ Memory Management

### 1. Garbage Collection Optimization

Control GC behavior:

```ruby
# Manual GC control
class GCController
  def self.with_optimized_gc
    # Disable GC during critical section
    GC.disable
    
    yield
    
    # Force GC after
    GC.enable
    GC.start
  end
  
  def self.memory_usage
    {
      heap_used: GC.stat[:heap_used],
      heap_length: GC.stat[:heap_length],
      total_allocated: GC.stat[:total_allocated_objects],
      total_freed: GC.stat[:total_freed_objects]
    }
  end
end

# Usage
GCController.with_optimized_gc do
  # Memory-intensive operation
  large_array = (1..1000000).map { |i| i * 2 }
  processed = large_array.map(&:to_s)
end

puts GCController.memory_usage
```

### 2. Object Pooling

Reuse objects to reduce allocation:

```ruby
class ObjectPool
  def initialize(create_proc, max_size = 100)
    @create_proc = create_proc
    @pool = []
    @max_size = max_size
  end
  
  def checkout
    if @pool.empty?
      @create_proc.call
    else
      @pool.pop
    end
  end
  
  def checkin(object)
    if @pool.length < @max_size
      object.reset if object.respond_to?(:reset)
      @pool << object
    end
  end
end

# Usage for database connections
class ConnectionPool < ObjectPool
  def initialize
    super(-> { create_database_connection }, 10)
  end
end

pool = ConnectionPool.new

# Use pool
connection = pool.checkout
begin
  # Use connection
  result = connection.execute_query("SELECT * FROM users")
ensure
  pool.checkin(connection)
end
```

### 3. Memory Leak Detection

Find and fix memory leaks:

```ruby
class MemoryLeakDetector
  def self.detect_leaks
    # Take initial snapshot
    GC.start
    initial_objects = ObjectSpace.count_objects
    
    yield
    
    # Take final snapshot
    GC.start
    final_objects = ObjectSpace.count_objects
    leaked_objects = final_objects - initial_objects
    
    if leaked_objects > 0
      puts "Memory leak detected: #{leaked_objects} objects"
      
      # Find leaked objects
      leaked = ObjectSpace.count_objects
      leaked.each do |obj, count|
        if count > initial_objects[obj]
          puts "Leaked #{obj}: #{count - initial_objects[obj]} instances"
        end
      end
    end
  end
  end
end

# Test for leaks
MemoryLeakDetector.detect_leaks do
  # Potential leak
  @global_cache = {}
  1000.times { |i| @global_cache["key_#{i}"] = "value_#{i}" }
end
```

## 🔄 Caching Strategies

### 1. In-Memory Caching

Fast in-process caching:

```ruby
class MemoryCache
  def initialize(max_size = 1000)
    @cache = {}
    @access_times = {}
    @max_size = max_size
  end
  
  def get(key)
    if @cache.key?(key)
      @access_times[key] = Time.now
      @cache[key]
    end
  end
  
  def set(key, value)
    @cache[key] = value
    @access_times[key] = Time.now
    
    # Evict if over size limit
    evict_if_needed
  end
  
  def evict_if_needed
    return if @cache.size <= @max_size
    
    # LRU eviction
    oldest_key = @access_times.min_by { |k, v| v }.first
    @cache.delete(oldest_key)
    @access_times.delete(oldest_key)
  end
end

# Usage
cache = MemoryCache.new

cache.set("user_1", { name: "Alice", age: 30 })
cache.set("user_2", { name: "Bob", age: 25 })

puts cache.get("user_1")  # => { name: "Alice", age: 30 }
```

### 2. File-based Caching

Persistent caching with files:

```ruby
require 'fileutils'
require 'digest'

class FileCache
  def initialize(cache_dir)
    @cache_dir = cache_dir
    FileUtils.mkdir_p(@cache_dir)
  end
  
  def get(key)
    cache_file = cache_file_path(key)
    return nil unless File.exist?(cache_file)
    
    # Check if cache is expired (1 hour)
    if File.mtime(cache_file) < Time.now - 3600
      File.delete(cache_file)
      return nil
    end
    
    Marshal.load(File.read(cache_file))
  end
  
  def set(key, value, ttl = 3600)
    cache_file = cache_file_path(key)
    
    File.write(cache_file, Marshal.dump(value))
    
    # Set expiration
    File.utime(Time.now + ttl, cache_file)
  end
  
  private
  
  def cache_file_path(key)
    File.join(@cache_dir, "#{Digest::MD5.hexdigest(key)}.cache")
  end
end

# Usage
cache = FileCache.new("/tmp/cache")

cache.set("expensive_data", expensive_operation())
puts cache.get("expensive_data")  # Returns cached result
```

### 3. Distributed Caching

Redis and Memcached integration:

```ruby
require 'redis'

class DistributedCache
  def initialize(redis_url)
    @redis = Redis.new(url: redis_url)
  end
  
  def get(key)
    @redis.get(key)
  end
  
  def set(key, value, ttl = 3600)
    @redis.setex(key, ttl, value)
  end
  
  def get_multi(keys)
    @redis.mget(*keys)
  end
  
  def set_multi(hash, ttl = 3600)
    @redis.multi do
      hash.each do |key, value|
        @redis.setex(key, ttl, value)
      end
    end
  end
  
  def delete(key)
    @redis.del(key)
  end
end

# Usage
cache = DistributedCache.new("redis://localhost:6379")

# Cache user data
cache.set("user:123", { name: "Alice", email: "alice@test.com" })
user_data = cache.get("user:123")

# Cache multiple items
cache.set_multi({
  "config:app" => { version: "1.0", debug: false },
  "config:db" => { host: "localhost", port: 5432 }
})

configs = cache.get_multi(["config:app", "config:db"])
```

## 🗄️ Database Optimization

### 1. Query Optimization

Efficient database queries:

```ruby
# N+1 query problem and solution
class Post < ApplicationRecord
  # Bad: N+1 queries
  def self.bad_loading
    posts = limit(10)
    posts.each do |post|
      post.comments.each { |comment| puts comment.content }  # N+1 queries
    end
  end
  
  # Good: Eager loading
  def self.good_loading
    posts.includes(:comments).limit(10).each do |post|
      post.comments.each { |comment| puts comment.content }  # 2 queries total
    end
  end
  
  # Good: Batch loading
  def self.batch_loading
    posts = limit(10)
    post_ids = posts.map(&:id)
    
    # Load all comments in one query
    comments = Comment.where(post_id: post_ids).group_by(&:post_id)
    
    posts.each do |post|
      post_comments = comments[post.id] || []
      post_comments.each { |comment| puts comment.content }
    end
  end
  
  # Optimized complex queries
  def self.search_complex(query)
    # Use Arel for complex conditions
    posts = arel_table
    
    posts
      .where(
        posts[:title].matches("%#{query}%")
        .or(posts[:content].matches("%#{query}%"))
      )
      .where(posts[:published_at].lteq(Time.current))
      .order(posts[:created_at].desc)
      .limit(20)
  end
end
```

### 2. Connection Pooling

Database connection management:

```ruby
require 'connection_pool'

class DatabasePool
  def initialize(connection_string, pool_size = 5)
    @pool = ConnectionPool.new(size: pool_size) do
      PG.connect(connection_string)
    end
  end
  
  def with_connection
    @pool.with do |connection|
      yield connection
    end
  end
  
  def execute_query(sql)
    with_connection do |connection|
      connection.execute(sql)
    end
  end
end

# Usage
db_pool = DatabasePool.new("postgresql://user:pass@localhost/db")

# Execute queries with connection pooling
db_pool.execute_query("SELECT * FROM users WHERE active = true")

# Transaction with connection pooling
db_pool.with_connection do |connection|
  connection.transaction do
    connection.execute("INSERT INTO users (name) VALUES ('Alice')")
    connection.execute("INSERT INTO posts (title) VALUES ('Hello World')")
  end
end
```

### 3. Indexing Strategy

Database indexing for performance:

```ruby
# Migration with indexes
class AddPerformanceIndexes < ActiveRecord::Migration[7.0]
  def change
    # Single column indexes
    add_index :users, :email
    add_index :posts, [:user_id, :created_at]
    
    # Composite indexes
    add_index :posts, [:published, :created_at, :category_id]
    
    # Partial indexes (for specific queries)
    add_index :posts, :title, name: 'index_posts_on_title'
    
    # Unique indexes
    add_index :users, :email, unique: true
  end
end

# Query optimization based on indexes
class Post < ApplicationRecord
  # Uses index on user_id and created_at
  def self.by_user_recent(user_id, limit = 10)
    where(user_id: user_id)
      .order(created_at: :desc)
      .limit(limit)
  end
  
  # Uses composite index
  def self.published_by_category(category)
    where(published: true, category_id: category)
      .order(created_at: :desc)
  end
  
  # Uses partial index
  def self.search_by_title(title)
    where("title ILIKE ?", "#{title}%")
  end
end
```

## 🌐 Web Performance

### 1. Response Optimization

Fast web responses:

```ruby
# Use proper HTTP headers
class ApplicationController < ActionController::Base
  def set_cache_headers
    response.headers['Cache-Control'] = 'public, max-age=3600'
    response.headers['ETag'] = Digest::MD5.hexdigest(response.body)
  end
  
  def set_compression_headers
    response.headers['Content-Encoding'] = 'gzip'
  end
  
  def conditional_get
    if stale?(last_modified)
      head :no_content
      return
    end
    
    set_cache_headers
    yield
  end
end

# Efficient JSON responses
class Api::V1::BaseController < ApplicationController
  def json_response(data, status: :ok)
    render json: data, status: status
  end
  
  def paginated_response(collection, page: 1, per_page: 20)
    total = collection.count
    paginated = collection.offset((page - 1) * per_page).limit(per_page)
    
    json_response({
      data: paginated,
      pagination: {
        current_page: page,
        per_page: per_page,
        total_pages: (total.to_f / per_page).ceil,
        total_count: total
      }
    })
  end
end
```

### 2. Asset Optimization

Optimize static assets:

```ruby
# Asset compression and minification
class AssetOptimizer
  def self.compress_css(content)
    # Remove comments and whitespace
    content.gsub(/\/\*[\s\S]*?\*\//, '')
           .gsub(/\s+/, ' ')
           .strip
  end
  
  def self.compress_js(content)
    # Simple JavaScript minification
    content.gsub(/\/\*[\s\S]*?\*\//, '')
           .gsub(/\/\/.*$/, '')
           .gsub(/\s+/, ' ')
           .strip
  end
  
  def self.optimize_image(image_path)
    # Use image optimization tools
    system("optipng -o #{image_path}.opt #{image_path}")
    system("jpegoptim --strip-all --progressive #{image_path}")
  end
end

# Asset pipeline with caching
class AssetPipeline
  def self.compile_assets
    assets = {
      'application.css' => compile_sass,
      'application.js' => compile_coffeescript,
      'application.min.css' => minify_css,
      'application.min.js' => minify_js
    }
    
    assets.each do |output, processor|
      input = output.gsub('.min.', '.')
      content = processor.call("app/assets/#{input}")
      File.write("public/assets/#{output}", content)
    end
  end
end
```

## 📊 Performance Monitoring

### 1. Application Metrics

Track application performance:

```ruby
require 'prometheus/client'

class MetricsCollector
  def initialize
    @metrics = {
      request_count: 0,
      request_duration: [],
      error_count: 0,
      active_users: 0
    }
  end
  
  def increment_request_count
    @metrics[:request_count] += 1
  end
  
  def record_request_duration(duration)
    @metrics[:request_duration] << duration
  end
  
  def increment_error_count
    @metrics[:error_count] += 1
  end
  
  def increment_active_users
    @metrics[:active_users] += 1
  end
  
  def get_metrics
    {
      request_count: @metrics[:request_count],
      avg_request_duration: @metrics[:request_duration].sum / @metrics[:request_duration].size,
      error_rate: @metrics[:error_count].to_f / @metrics[:request_count],
      active_users: @metrics[:active_users]
    }
  end
end

# Middleware for metrics collection
class MetricsMiddleware
  def initialize(app)
    @app = app
    @metrics = MetricsCollector.new
  end
  
  def call(env)
    start_time = Time.now
    
    status, headers, response = @app.call(env)
    
    end_time = Time.now
    duration = end_time - start_time
    
    @metrics.increment_request_count
    @metrics.record_request_duration(duration)
    @metrics.increment_error_count if status >= 400
    
    [status, headers, response]
  end
end
```

### 2. Health Checks

Application health monitoring:

```ruby
class HealthChecker
  def self.check_database
    begin
      ActiveRecord::Base.connection.execute("SELECT 1")
      { status: "healthy", latency: measure_db_latency }
    rescue => e
      { status: "unhealthy", error: e.message }
    end
  end
  
  def self.check_cache
    begin
      Rails.cache.read("health_check")
      { status: "healthy" }
    rescue => e
      { status: "unhealthy", error: e.message }
    end
  end
  
  def self.check_external_service(url)
    start_time = Time.now
    
    response = Net::HTTP.get_response(URI(url))
    
    end_time = Time.now
    latency = (end_time - start_time) * 1000
    
    {
      status: response.code == "200" ? "healthy" : "unhealthy",
      latency: latency,
      response_code: response.code
    }
  end
  
  def self.measure_db_latency
    start_time = Time.now
    ActiveRecord::Base.connection.execute("SELECT 1")
    (Time.now - start_time) * 1000
  end
end

# Health check endpoint
class HealthController < ApplicationController
  def show
    checks = {
      database: HealthChecker.check_database,
      cache: HealthChecker.check_cache,
      external_api: HealthChecker.check_external_service(ENV['EXTERNAL_API_URL'])
    }
    
    overall_status = checks.values.all? { |check| check[:status] == "healthy" }
    
    render json: {
      status: overall_status ? "healthy" : "unhealthy",
      timestamp: Time.now.iso8601,
      checks: checks
    }, status: overall_status ? :ok : :service_unavailable
  end
end
```

## 🎯 Optimization Best Practices

### 1. Measurement First

Always measure before optimizing:

```ruby
# Profile before optimizing
require 'ruby-prof'

# Profile the code
result = RubyProf.profile do
  slow_method
end

# Analyze results
printer = RubyProf::GraphHtmlPrinter.new(result)
printer.print(File.open('profile.html', 'w'))
```

### 2. Focus on Hot Paths

Optimize critical code paths:

```ruby
class ApplicationOptimizer
  def self.optimize_critical_paths
    # Profile to identify bottlenecks
    bottlenecks = profile_application
    
    bottlenecks.each do |bottleneck|
      optimize_method(bottleneck[:method])
    end
  end
  
  def self.profile_application
    # Simulate profiling
    {
      method: :user_authentication,
      time: 1000,  # ms
      calls: 1000
    }
  end
  
  def self.optimize_method(method_name)
    case method_name
    when :user_authentication
      # Implement caching for authentication
      optimize_authentication_cache
    when :data_processing
      # Optimize data processing algorithms
      optimize_data_algorithms
    end
  end
end
```

### 3. Avoid Premature Optimization

Optimize based on data, not assumptions:

```ruby
# Don't optimize without measurements
class PrematureOptimization
  def self.bad_optimization
    # Assumed optimization without measurement
    def process_data(data)
      # Assumed array operations are faster
      data.to_a.sort.uniq  # Might be slower for small datasets
    end
  end
  
  def self.good_optimization
    def process_data(data)
      # Choose based on data characteristics
      if data.size > 1000
        data.to_a.sort.uniq  # Use Set for large data
      else
        data.sort.uniq  # Use array for small data
      end
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Benchmark Basics**: Compare different Ruby methods
2. **Memory Profiling**: Find memory leaks in simple code
3. **String Optimization**: Optimize string concatenation

### Intermediate Exercises

1. **Caching Implementation**: Build a simple cache system
2. **Query Optimization**: Optimize database queries
3. **Asset Pipeline**: Create an asset optimization tool

### Advanced Exercises

1. **Performance Monitoring**: Build a metrics collection system
2. **Database Pooling**: Implement connection pooling
3. **Full Application Optimization**: Optimize a complete Rails application

---

## 🎯 Summary

Performance optimization in Ruby involves:

- **Accurate measurement** - Benchmarking and profiling
- **Memory management** - GC control and leak detection
- **Efficient algorithms** - Choose right data structures and methods
- **Smart caching** - In-memory, file-based, and distributed
- **Database optimization** - Query optimization and connection pooling
- **Web performance** - Response optimization and asset management
- **Continuous monitoring** - Metrics and health checks

Remember: **Measure first, optimize second**. Focus on actual bottlenecks rather than premature optimization!
