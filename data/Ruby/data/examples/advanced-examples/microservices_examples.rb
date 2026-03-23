# Microservices Architecture Examples
# Demonstrating service design, inter-service communication, and deployment patterns

puts "=== SERVICE DESIGN ==="

# Domain Models with Single Responsibility
class User
  attr_reader :id, :name, :email, :created_at
  
  def initialize(data)
    @id = data[:id] || SecureRandom.uuid
    @name = data[:name]
    @email = data[:email]
    @created_at = data[:created_at] || Time.now
    @errors = {}
  end
  
  def valid?
    validate_name
    validate_email
    @errors.empty?
  end
  
  def errors
    @errors
  end
  
  private
  
  def validate_name
    @errors[:name] = ["Name can't be blank"] if @name.nil? || @name.strip.empty?
    @errors[:name] = ["Name is too short"] if @name && @name.length < 2
  end
  
  def validate_email
    @errors[:email] = ["Email can't be blank"] if @email.nil? || @email.strip.empty?
    @errors[:email] = ["Email is invalid"] unless @email&.match?(/\A[^@\s]+@[^@\s]+\z/)
  end
end

class Order
  attr_reader :id, :user_id, :items, :total, :status, :created_at
  
  def initialize(data)
    @id = data[:id] || SecureRandom.uuid
    @user_id = data[:user_id]
    @items = data[:items] || []
    @total = data[:total] || 0
    @status = data[:status] || :pending
    @created_at = data[:created_at] || Time.now
    @errors = {}
  end
  
  def valid?
    validate_user_id
    validate_items
    @errors.empty?
  end
  
  def confirm!
    @status = :confirmed
  end
  
  def cancel!
    @status = :cancelled
  end
  
  def errors
    @errors
  end
  
  private
  
  def validate_user_id
    @errors[:user_id] = ["User ID can't be blank"] if @user_id.nil? || @user_id.empty?
  end
  
  def validate_items
    @errors[:items] = ["Items can't be empty"] if @items.empty?
    
    @items.each_with_index do |item, index|
      if item[:price].nil? || item[:price] <= 0
        @errors["items_#{index}_price"] = ["Price must be greater than 0"]
      end
      
      if item[:quantity].nil? || item[:quantity] <= 0
        @errors["items_#{index}_quantity"] = ["Quantity must be greater than 0"]
      end
    end
  end
end

# Service with Single Responsibility
class UserService
  def initialize(user_repository, event_publisher)
    @user_repository = user_repository
    @event_publisher = event_publisher
  end
  
  def create_user(user_data)
    user = User.new(user_data)
    
    if user.valid?
      @user_repository.save(user)
      
      # Publish event
      @event_publisher.publish(UserCreatedEvent.new(user))
      
      user
    else
      raise ValidationError, user.errors.full_messages
    end
  end
  
  def get_user(user_id)
    @user_repository.find(user_id)
  end
  
  def update_user(user_id, updates)
    user = @user_repository.find(user_id)
    raise NotFoundError, "User not found" unless user
    
    user.update(updates)
    
    if user.valid?
      @user_repository.save(user)
      @event_publisher.publish(UserUpdatedEvent.new(user))
      user
    else
      raise ValidationError, user.errors.full_messages
    end
  end
  
  def delete_user(user_id)
    user = @user_repository.find(user_id)
    raise NotFoundError, "User not found" unless user
    
    @user_repository.delete(user)
    @event_publisher.publish(UserDeletedEvent.new(user))
    
    true
  end
end

# Order Service
class OrderService
  def initialize(order_repository, user_service_client, payment_service_client, event_publisher)
    @order_repository = order_repository
    @user_service_client = user_service_client
    @payment_service_client = payment_service_client
    @event_publisher = event_publisher
  end
  
  def create_order(user_id, order_data)
    # Verify user exists
    user = @user_service_client.get_user(user_id)
    raise NotFoundError, "User not found" unless user
    
    order = Order.new(user_id: user_id, items: order_data[:items])
    
    if order.valid?
      # Calculate total
      total = calculate_total(order)
      order.instance_variable_set(:@total, total)
      
      # Process payment
      payment_result = @payment_service_client.process_payment(
        user_id: user_id,
        amount: total,
        order_id: order.id
      )
      
      if payment_result[:success]
        order.confirm!
        @order_repository.save(order)
        
        @event_publisher.publish(OrderCreatedEvent.new(order))
        order
      else
        raise PaymentError, payment_result[:error]
      end
    else
      raise ValidationError, order.errors.full_messages
    end
  end
  
  def get_order(order_id)
    @order_repository.find(order_id)
  end
  
  def get_user_orders(user_id)
    @order_repository.find_by_user_id(user_id)
  end
  
  private
  
  def calculate_total(order)
    order.items.sum { |item| item[:price] * item[:quantity] }
  end
end

puts "Service Design Example:"

# Mock repositories and services
class MockUserRepository
  def initialize
    @users = {}
  end
  
  def save(user)
    @users[user.id] = user
    user
  end
  
  def find(user_id)
    @users[user_id]
  end
end

class MockEventPublisher
  def publish(event)
    puts "Published event: #{event.class.name} for #{event.data[:name] || event.data[:user_id]}"
  end
end

# Create services
user_repo = MockUserRepository.new
event_publisher = MockEventPublisher.new
user_service = UserService.new(user_repo, event_publisher)

# Create a user
user_data = { name: "John Doe", email: "john@example.com" }
user = user_service.create_user(user_data)
puts "Created user: #{user.name} (#{user.email})"

# Update user
updated_user = user_service.update_user(user.id, { name: "John Updated" })
puts "Updated user: #{updated_user.name}"

puts "\n=== INTER-SERVICE COMMUNICATION ==="

# Service Client with Circuit Breaker
class ServiceClient
  def initialize(base_url, circuit_breaker = nil)
    @base_url = base_url
    @circuit_breaker = circuit_breaker
  end
  
  def get_user(user_id)
    if @circuit_breaker
      @circuit_breaker.call { simulate_get_user(user_id) }
    else
      simulate_get_user(user_id)
    end
  end
  
  def create_user(user_data)
    if @circuit_breaker
      @circuit_breaker.call { simulate_create_user(user_data) }
    else
      simulate_create_user(user_data)
    end
  end
  
  private
  
  def simulate_get_user(user_id)
    # Simulate API call
    puts "Calling user service API for user #{user_id}"
    sleep(0.1)  # Simulate network latency
    
    if rand < 0.1  # 10% chance of failure
      raise ServiceError, "Service unavailable"
    end
    
    {
      id: user_id,
      name: "User #{user_id}",
      email: "user#{user_id}@example.com"
    }
  end
  
  def simulate_create_user(user_data)
    puts "Calling user service API to create user"
    sleep(0.1)  # Simulate network latency
    
    if rand < 0.1  # 10% chance of failure
      raise ServiceError, "Service unavailable"
    end
    
    {
      id: SecureRandom.uuid,
      name: user_data[:name],
      email: user_data[:email]
    }
  end
end

# Circuit Breaker Implementation
class CircuitBreaker
  def initialize(failure_threshold = 5, timeout = 60)
    @failure_threshold = failure_threshold
    @timeout = timeout
    @failure_count = 0
    @last_failure_time = nil
    @state = :closed
  end
  
  def call(&block)
    case @state
    when :open
      if Time.now - @last_failure_time > @timeout
        @state = :half_open
      else
        raise CircuitBreakerError, "Circuit breaker is open"
      end
    when :half_open
      execute_with_circuit_breaker(&block)
    else # :closed
      execute_with_circuit_breaker(&block)
    end
  end
  
  private
  
  def execute_with_circuit_breaker(&block)
    begin
      result = block.call
      reset
      result
    rescue => e
      record_failure
      raise e
    end
  end
  
  def record_failure
    @failure_count += 1
    @last_failure_time = Time.now
    
    if @failure_count >= @failure_threshold
      @state = :open
    end
  end
  
  def reset
    @failure_count = 0
    @state = :closed
  end
end

class CircuitBreakerError < StandardError; end

# Event System
class EventBus
  def initialize
    @handlers = Hash.new { |h, k| h[k] = [] }
    @mutex = Mutex.new
  end
  
  def subscribe(event_type, handler)
    @mutex.synchronize do
      @handlers[event_type] << handler
    end
  end
  
  def publish(event)
    @mutex.synchronize do
      handlers = @handlers[event.class] || []
      
      handlers.each do |handler|
        Thread.new { handler.call(event) }
      end
    end
  end
end

# Event Classes
class DomainEvent
  attr_reader :id, :timestamp, :data
  
  def initialize(data)
    @id = SecureRandom.uuid
    @timestamp = Time.now
    @data = data
  end
end

class UserCreatedEvent < DomainEvent
  def initialize(user)
    super({
      user_id: user.id,
      name: user.name,
      email: user.email
    })
  end
end

class OrderCreatedEvent < DomainEvent
  def initialize(order)
    super({
      order_id: order.id,
      user_id: order.user_id,
      total: order.total,
      items: order.items
    })
  end
end

# Event Publisher
class EventPublisher
  def initialize(event_bus, message_queue = nil)
    @event_bus = event_bus
    @message_queue = message_queue
  end
  
  def publish(event)
    # Publish locally
    @event_bus.publish(event)
    
    # Publish to message queue if available
    if @message_queue
      @message_queue.publish({
        event_type: event.class.name,
        event_id: event.id,
        timestamp: event.timestamp,
        data: event.data
      })
    end
  end
end

puts "Inter-Service Communication Example:"

# Create circuit breaker
circuit_breaker = CircuitBreaker.new(3, 30)

# Create service client with circuit breaker
user_client = ServiceClient.new('http://user-service', circuit_breaker)

# Create event system
event_bus = EventBus.new
event_publisher = EventPublisher.new(event_bus)

# Subscribe to events
event_bus.subscribe(UserCreatedEvent) do |event|
  puts "🎉 User created event received: #{event.data[:name]}"
end

event_bus.subscribe(OrderCreatedEvent) do |event|
  puts "🛒 Order created event received: #{event.data[:order_id]}"
end

# Test service client with circuit breaker
5.times do |i|
  begin
    user = user_client.get_user(i)
    puts "✅ Got user: #{user[:name]}"
  rescue ServiceError => e
    puts "❌ Service error: #{e.message}"
  rescue CircuitBreakerError => e
    puts "🔌 Circuit breaker error: #{e.message}"
    break
  end
end

# Publish events
user = User.new(name: "Alice", email: "alice@example.com")
event_publisher.publish(UserCreatedEvent.new(user))

order = Order.new(user_id: user.id, items: [{ name: "Product", price: 29.99, quantity: 2 }])
event_publisher.publish(OrderCreatedEvent.new(order))

puts "\n=== API GATEWAY ==="

# Simple API Gateway
class APIGateway
  def initialize
    @routes = {}
    @services = {}
    @middleware = []
  end
  
  def register_service(name, url)
    @services[name] = ServiceRegistry.new(name, url)
  end
  
  def add_route(method, path, service_name, service_path)
    @routes["#{method.upcase} #{path}"] = {
      service: service_name,
      path: service_path
    }
  end
  
  def use(middleware)
    @middleware << middleware
  end
  
  def handle_request(method, path, data = {})
    route_key = "#{method.upcase} #{path}"
    route = @routes[route_key]
    
    return { status: 404, body: "Not Found" } unless route
    
    service = @services[route[:service]]
    return { status: 503, body: "Service Unavailable" } unless service
    
    # Apply middleware
    context = {
      method: method,
      path: path,
      data: data,
      service: service,
      service_path: route[:path]
    }
    
    @middleware.reduce(context) do |ctx, middleware|
      middleware.call(ctx)
    end
  end
end

# Service Registry
class ServiceRegistry
  def initialize(name, base_url)
    @name = name
    @base_url = base_url
    @instances = []
    @current_index = 0
  end
  
  def add_instance(url)
    @instances << url
  end
  
  def get_instance
    return nil if @instances.empty?
    
    # Round-robin load balancing
    instance = @instances[@current_index]
    @current_index = (@current_index + 1) % @instances.length
    instance
  end
end

# Middleware
class LoggingMiddleware
  def call(context)
    start_time = Time.now
    
    puts "[#{start_time}] #{context[:method]} #{context[:path]}"
    
    result = context
    
    end_time = Time.now
    duration = end_time - start_time
    
    puts "[#{end_time}] Completed in #{duration.round(3)}s"
    
    result
  end
end

class AuthenticationMiddleware
  def call(context)
    # Skip authentication for health endpoints
    return context if context[:path] == '/health'
    
    token = context[:data][:token]
    
    unless token && token == 'valid-token'
      return { status: 401, body: 'Unauthorized' }
    end
    
    context[:user_id] = 'user-123'
    context
  end
end

puts "API Gateway Example:"

# Create gateway
gateway = APIGateway.new

# Register services
gateway.register_service('user-service', 'http://localhost:3001')
gateway.register_service('order-service', 'http://localhost:3002')

# Add routes
gateway.add_route('GET', '/users/:id', 'user-service', '/users/:id')
gateway.add_route('POST', '/users', 'user-service', '/users')
gateway.add_route('GET', '/orders/:id', 'order-service', '/orders/:id')
gateway.add_route('POST', '/orders', 'order-service', '/orders')

# Add middleware
gateway.use(LoggingMiddleware.new)
gateway.use(AuthenticationMiddleware.new)

# Handle requests
request_data = { token: 'valid-token' }

result = gateway.handle_request('GET', '/users/123', request_data)
puts "GET /users/123: #{result[:status]}"

result = gateway.handle_request('POST', '/users', { name: 'John', email: 'john@example.com' })
puts "POST /users: #{result[:status]}"

result = gateway.handle_request('GET', '/health')
puts "GET /health: #{result[:status]}"

puts "\n=== SERVICE DISCOVERY ==="

# Service Discovery
class ServiceDiscovery
  def initialize
    @services = {}
    @mutex = Mutex.new
  end
  
  def register_service(name, instance)
    @mutex.synchronize do
      @services[name] ||= []
      @services[name] << instance
      @services[name].uniq!
    end
  end
  
  def unregister_service(name, instance_id)
    @mutex.synchronize do
      @services[name]&.reject! { |instance| instance[:id] == instance_id }
    end
  end
  
  def discover_service(name)
    @mutex.synchronize do
      instances = @services[name] || []
      
      # Return healthy instances only
      healthy_instances = instances.select { |instance| instance[:healthy] }
      
      # Simple round-robin selection
      return nil if healthy_instances.empty?
      
      healthy_instances[rand(healthy_instances.length)]
    end
  end
  
  def health_check_all
    @mutex.synchronize do
      @services.each do |name, instances|
        instances.each do |instance|
          healthy = health_check_instance(instance)
          instance[:healthy] = healthy
          instance[:last_check] = Time.now
        end
      end
    end
  end
end

# Service Instance
class ServiceInstance
  def initialize(name, url)
    @name = name
    @url = url
    @id = SecureRandom.uuid
    @healthy = true
    @last_check = Time.now
  end
  
  attr_reader :name, :url, :id, :healthy, :last_check
  
  def healthy?
    @healthy
  end
  
  def to_hash
    {
      id: @id,
      name: @name,
      url: @url,
      healthy: @healthy,
      last_check: @last_check
    }
  end
end

puts "Service Discovery Example:"

discovery = ServiceDiscovery.new

# Register services
user_service = ServiceInstance.new('user-service', 'http://localhost:3001')
order_service = ServiceInstance.new('order-service', 'http://localhost:3002')

discovery.register_service('user-service', user_service.to_hash)
discovery.register_service('order-service', order_service.to_hash)

# Discover services
user_instance = discovery.discover_service('user-service')
puts "Discovered user service: #{user_instance[:url]}" if user_instance

order_instance = discovery.discover_service('order-service')
puts "Discovered order service: #{order_instance[:url]}" if order_instance

# Health checking
discovery.health_check_all
puts "Health check completed"

puts "\n=== CONFIGURATION MANAGEMENT ==="

# Configuration Manager
class ConfigurationManager
  def initialize
    @config = {}
    @watchers = {}
    @mutex = Mutex.new
  end
  
  def load_from_file(file_path)
    @mutex.synchronize do
      # Simulate loading from YAML file
      @config = {
        database: {
          url: 'postgresql://localhost:5432/myapp',
          pool_size: 10,
          timeout: 30
        },
        redis: {
          url: 'redis://localhost:6379',
          pool_size: 5
        },
        services: {
          user_service: {
            url: 'http://localhost:3001',
            timeout: 10
          },
          order_service: {
            url: 'http://localhost:3002',
            timeout: 10
          }
        }
      }
      
      notify_watchers
    end
  end
  
  def get(key, default = nil)
    @mutex.synchronize do
      value = @config
      keys = key.split('.')
      
      keys.each do |k|
        value = value[k]
        return default if value.nil?
      end
      
      value
    end
  end
  
  def set(key, value)
    @mutex.synchronize do
      keys = key.split('.')
      last_key = keys.pop
      
      target = keys.reduce(@config) do |hash, k|
        hash[k] ||= {}
        hash[k]
      end
      
      target[last_key] = value
      notify_watchers(key, value)
    end
  end
  
  def watch(key, &block)
    @mutex.synchronize do
      @watchers[key] ||= []
      @watchers[key] << block
    end
  end
  
  private
  
  def notify_watchers(key = nil, value = nil)
    @watchers.each do |watch_key, watchers|
      if key.nil? || watch_key == key
        watchers.each { |watcher| watcher.call(key || watch_key, value || get(key)) }
      end
    end
  end
end

puts "Configuration Management Example:"

config = ConfigurationManager.new
config.load_from_file('config/services.yml')

# Get configuration
db_url = config.get('database.url')
puts "Database URL: #{db_url}"

user_service_url = config.get('services.user_service.url')
puts "User service URL: #{user_service_url}"

# Watch for changes
config.watch('database.pool_size') do |key, value|
  puts "Database pool size changed: #{value}"
end

# Update configuration
config.set('database.pool_size', 15)
puts "Updated database pool size"

puts "\n=== CONTAINERIZATION ==="

# Dockerfile Generator
class DockerfileGenerator
  def initialize(service_name, options = {})
    @service_name = service_name
    @ruby_version = options[:ruby_version] || '3.1'
    @port = options[:port] || 3000
    @dependencies = options[:dependencies] || []
    @environment_vars = options[:environment_vars] || {}
  end
  
  def generate
    <<~DOCKERFILE
      FROM ruby:#{@ruby_version}
      
      WORKDIR /app
      
      # Install system dependencies
      #{system_dependencies}
      
      # Copy gem files
      COPY Gemfile Gemfile.lock ./
      
      # Install ruby dependencies
      RUN bundle config set --local without 'development test'
      RUN bundle install
      
      # Copy application code
      COPY . .
      
      # Create non-root user
      RUN groupadd -r app && useradd -r -g app app
      RUN chown -R app:app /app
      USER app
      
      # Expose port
      EXPOSE #{@port}
      
      # Set environment variables
      #{environment_variables}
      
      # Start command
      CMD ["bundle", "exec", "ruby", "server.rb"]
    DOCKERFILE
  end
  
  private
  
  def system_dependencies
    return '' if @dependencies.empty?
    
    deps = @dependencies.map { |dep| "    #{dep}" }.join("\n")
    "RUN apt-get update && apt-get install -y \\\n#{deps} && rm -rf /var/lib/apt/lists/*"
  end
  
  def environment_variables
    return '' if @environment_vars.empty?
    
    vars = @environment_vars.map { |key, value| "ENV #{key}=#{value}" }.join("\n")
    vars
  end
end

# Docker Compose Generator
class DockerComposeGenerator
  def initialize(services, options = {})
    @services = services
    @network_name = options[:network_name] || 'microservices'
    @volumes = options[:volumes] || {}
  end
  
  def generate
    <<~COMPOSE
      version: '3.8'
      
      networks:
        #{@network_name}:
          driver: bridge
      
      #{volumes_section}
      
      services:
        #{services_section}
    COMPOSE
  end
  
  private
  
  def volumes_section
    return '' if @volumes.empty?
    
    volumes = @volumes.map { |name, config| "    #{name}:\n      #{config}" }.join("\n")
    "  volumes:\n#{volumes}"
  end
  
  def services_section
    @services.map do |name, config|
      <<~SERVICE
        #{name}:
          image: #{config[:image]}
          ports:
            - "#{config[:port]}:#{config[:internal_port] || 3000}"
          environment:
            #{environment_variables(config[:environment] || {})}
          networks:
            - #{@network_name}
          depends_on:
            #{dependencies(config[:depends_on] || [])}
      SERVICE
    end.join("\n")
  end
  
  def environment_variables(vars)
    vars.map { |key, value| "            - #{key}=#{value}" }.join("\n")
  end
  
  def dependencies(deps)
    deps.map { |dep| "            - #{dep}" }.join("\n")
  end
end

puts "Containerization Example:"

# Generate Dockerfile
dockerfile_generator = DockerfileGenerator.new('user-service', {
  ruby_version: '3.1',
  port: 3001,
  dependencies: ['postgresql-client'],
  environment_vars: {
    'RACK_ENV' => 'production',
    'DATABASE_URL' => 'postgresql://user:pass@db:5432/users'
  }
})

puts "Generated Dockerfile for user-service:"
puts dockerfile_generator.generate[0..500] + "..."

# Generate Docker Compose
services = {
  'user-service' => {
    image: 'user-service:latest',
    port: 3001,
    environment: {
      'DATABASE_URL' => 'postgresql://user:pass@postgres:5432/users',
      'REDIS_URL' => 'redis://redis:6379'
    },
    depends_on: ['postgres', 'redis']
  },
  'order-service' => {
    image: 'order-service:latest',
    port: 3002,
    environment: {
      'DATABASE_URL' => 'postgresql://order:pass@postgres:5432/orders',
      'USER_SERVICE_URL' => 'http://user-service:3001'
    },
    depends_on: ['postgres', 'user-service']
  }
}

compose_generator = DockerComposeGenerator.new(services, {
  volumes: {
    'postgres_data' => 'driver: local'
  }
})

puts "\nGenerated Docker Compose:"
puts compose_generator.generate[0..500] + "..."

puts "\n=== MICROSERVICES SUMMARY ==="
puts "- Service Design: Single responsibility, domain models, service boundaries"
puts "- Inter-Service Communication: REST APIs, circuit breakers, event-driven architecture"
puts "- API Gateway: Request routing, middleware, load balancing"
puts "- Service Discovery: Service registration, health checking, load balancing"
puts "- Configuration Management: Centralized config, environment variables, watchers"
puts "- Containerization: Docker containers, Docker Compose, multi-service deployment"
puts "\nAll examples demonstrate comprehensive microservices architecture patterns in Ruby!"
