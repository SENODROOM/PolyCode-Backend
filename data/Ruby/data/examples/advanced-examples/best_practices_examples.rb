# Ruby Best Practices Examples
# Demonstrating code style, architecture patterns, security practices, and maintainability

puts "=== NAMING CONVENTIONS ==="

# ✅ GOOD: Clear, descriptive names
class UserAuthenticationService
  def initialize(user_repository, token_generator)
    @user_repository = user_repository
    @token_generator = token_generator
  end
  
  def authenticate_user(email, password)
    user = @user_repository.find_by_email(email)
    return false unless user
    
    user.password_matches?(password)
  end
  
  def generate_authentication_token(user_id)
    @token_generator.create_token(user_id, expires_in: 24.hours)
  end
end

# ✅ GOOD: Method names that clearly express intent
class OrderProcessor
  def calculate_total_price(order)
    subtotal = calculate_subtotal(order)
    tax = calculate_tax(subtotal)
    shipping = calculate_shipping(order)
    
    subtotal + tax + shipping
  end
  
  def apply_discount_rules(order, customer)
    return order unless customer.loyalty_member?
    
    discounted_order = apply_loyalty_discount(order)
    discounted_order = apply_bulk_discount(discounted_order) if order.items.length > 10
    
    discounted_order
  end
  
  private
  
  def calculate_subtotal(order)
    order.items.sum { |item| item.price * item.quantity }
  end
  
  def calculate_tax(subtotal)
    subtotal * 0.08  # 8% tax rate
  end
  
  def calculate_shipping(order)
    order.weight > 10 ? 15.0 : 5.0
  end
  
  def apply_loyalty_discount(order)
    # Apply 10% loyalty discount
    discounted_items = order.items.map do |item|
      item.new(price: item.price * 0.9)
    end
    
    order.new(items: discounted_items)
  end
  
  def apply_bulk_discount(order)
    # Apply 5% bulk discount
    discounted_items = order.items.map do |item|
      item.new(price: item.price * 0.95)
    end
    
    order.new(items: discounted_items)
  end
end

# ❌ BAD: Unclear or abbreviated names (for comparison)
class UAS
  def initialize(ur, tg)
    @ur = ur
    @tg = tg
  end
  
  def auth_user(em, pw)
    u = @ur.find_by_email(em)
    return false unless u
    
    u.pw_match?(pw)
  end
  
  def gen_auth_token(uid)
    @tg.create_token(uid, exp: 24.hours)
  end
end

puts "Naming Conventions Example:"

# Demonstrate good naming
auth_service = UserAuthenticationService.new(nil, nil)
puts "Created UserAuthenticationService with clear purpose"

order_processor = OrderProcessor.new
puts "Created OrderProcessor with expressive method names"

puts "\n=== SINGLE RESPONSIBILITY PRINCIPLE ==="

# ✅ GOOD: Each class has a single responsibility
class UserRegistration
  def initialize(user_repository, email_service, password_validator)
    @user_repository = user_repository
    @email_service = email_service
    @password_validator = password_validator
  end
  
  def register(user_data)
    validate_user_data(user_data)
    
    user = create_user(user_data)
    saved_user = @user_repository.save(user)
    
    send_welcome_email(saved_user)
    
    saved_user
  end
  
  private
  
  def validate_user_data(user_data)
    raise ValidationError, "Email is required" if user_data[:email].nil?
    raise ValidationError, "Password is required" if user_data[:password].nil?
    raise ValidationError, "Invalid email format" unless valid_email?(user_data[:email])
    raise ValidationError, "Invalid password" unless @password_validator.valid?(user_data[:password])
  end
  
  def create_user(user_data)
    User.new(
      email: user_data[:email],
      password_hash: @password_validator.hash_password(user_data[:password]),
      created_at: Time.now
    )
  end
  
  def send_welcome_email(user)
    @email_service.send_welcome_email(user)
  end
  
  def valid_email?(email)
    email.match?(/\A[^@\s]+@[^@\s]+\z/)
  end
end

# Separate repository for data access
class UserRepository
  def initialize(database)
    @database = database
  end
  
  def save(user)
    # Save user to database
    puts "Saving user: #{user.email}"
    user.id = SecureRandom.uuid
    user
  end
  
  def find_by_email(email)
    # Find user by email
    puts "Finding user by email: #{email}"
    nil  # Mock implementation
  end
end

# Separate email service
class EmailService
  def initialize(smtp_client)
    @smtp_client = smtp_client
  end
  
  def send_welcome_email(user)
    puts "Sending welcome email to: #{user.email}"
    # Implementation would send actual email
  end
end

# Password validator
class PasswordValidator
  def valid?(password)
    password.length >= 8 && password.match?(/\d/) && password.match?(/[A-Z]/)
  end
  
  def hash_password(password)
    # Hash password securely
    "hashed_#{password}"
  end
end

puts "Single Responsibility Principle Example:"

# Create components
user_repo = UserRepository.new(nil)
email_service = EmailService.new(nil)
password_validator = PasswordValidator.new

# Create registration service
registration = UserRegistration.new(user_repo, email_service, password_validator)

# Register a user
user_data = {
  email: "john@example.com",
  password: "SecurePass123"
}

begin
  user = registration.register(user_data)
  puts "✅ User registered successfully: #{user.email}"
rescue ValidationError => e
  puts "❌ Registration failed: #{e.message}"
end

puts "\n=== DEPENDENCY INJECTION ==="

# ✅ GOOD: Use dependency injection for testability and flexibility
class PaymentProcessor
  def initialize(gateway:, logger:, fraud_detector:)
    @gateway = gateway
    @logger = logger
    @fraud_detector = fraud_detector
  end
  
  def process_payment(payment_data)
    @logger.info("Processing payment for #{payment_data[:amount]}")
    
    # Fraud detection
    if @fraud_detector.suspicious?(payment_data)
      @logger.warn("Suspicious payment detected: #{payment_data}")
      raise FraudError, "Payment flagged as suspicious"
    end
    
    # Process payment
    begin
      result = @gateway.charge(
        amount: payment_data[:amount],
        token: payment_data[:payment_token],
        description: payment_data[:description]
      )
      
      @logger.info("Payment processed successfully: #{result[:transaction_id]}")
      result
      
    rescue GatewayError => e
      @logger.error("Payment failed: #{e.message}")
      raise PaymentError, "Payment processing failed"
    end
  end
end

# Mock dependencies for testing
class MockGateway
  def charge(options)
    puts "Processing charge: #{options[:amount]}"
    { transaction_id: SecureRandom.uuid, status: "success" }
  end
end

class MockLogger
  def info(message)
    puts "INFO: #{message}"
  end
  
  def warn(message)
    puts "WARN: #{message}"
  end
  
  def error(message)
    puts "ERROR: #{message}"
  end
end

class MockFraudDetector
  def suspicious?(payment_data)
    payment_data[:amount] > 10000  # Flag large amounts
  end
end

puts "Dependency Injection Example:"

# Create dependencies
gateway = MockGateway.new
logger = MockLogger.new
fraud_detector = MockFraudDetector.new

# Create payment processor with injected dependencies
payment_processor = PaymentProcessor.new(
  gateway: gateway,
  logger: logger,
  fraud_detector: fraud_detector
)

# Process a normal payment
normal_payment = {
  amount: 100,
  payment_token: "tok_123",
  description: "Test payment"
}

begin
  result = payment_processor.process_payment(normal_payment)
  puts "✅ Normal payment processed: #{result[:transaction_id]}"
rescue PaymentError => e
  puts "❌ Payment failed: #{e.message}"
end

# Process a suspicious payment
suspicious_payment = {
  amount: 15000,
  payment_token: "tok_456",
  description: "Large payment"
}

begin
  result = payment_processor.process_payment(suspicious_payment)
  puts "✅ Suspicious payment processed: #{result[:transaction_id]}"
rescue PaymentError => e
  puts "❌ Suspicious payment failed: #{e.message}"
end

puts "\n=== FACTORY PATTERN ==="

# ✅ GOOD: Factory pattern for object creation
class ReportFactory
  def self.create_report(type, data, options = {})
    case type
    when :pdf
      PDFReport.new(data, options)
    when :excel
      ExcelReport.new(data, options)
    when :csv
      CSVReport.new(data, options)
    when :html
      HTMLReport.new(data, options)
    else
      raise ArgumentError, "Unknown report type: #{type}"
    end
  end
end

# Report classes
class PDFReport
  def initialize(data, options = {})
    @data = data
    @options = options
  end
  
  def generate
    puts "Generating PDF report with #{@data.length} records"
    "PDF report content"
  end
end

class ExcelReport
  def initialize(data, options = {})
    @data = data
    @options = options
  end
  
  def generate
    puts "Generating Excel report with #{@data.length} records"
    "Excel report content"
  end
end

class CSVReport
  def initialize(data, options = {})
    @data = data
    @options = options
  end
  
  def generate
    puts "Generating CSV report with #{@data.length} records"
    "CSV report content"
  end
end

class HTMLReport
  def initialize(data, options = {})
    @data = data
    @options = options
  end
  
  def generate
    puts "Generating HTML report with #{@data.length} records"
    "HTML report content"
  end
end

puts "Factory Pattern Example:"

# Sample data
data = [
  { name: "John", age: 30, city: "New York" },
  { name: "Jane", age: 25, city: "Boston" },
  { name: "Bob", age: 35, city: "Chicago" }
]

# Create different types of reports
report_types = [:pdf, :excel, :csv, :html]

report_types.each do |type|
  report = ReportFactory.create_report(type, data, { title: "User Report" })
  output = report.generate
  puts "✅ Generated #{type} report"
end

puts "\n=== INPUT VALIDATION ==="

# ✅ GOOD: Comprehensive input validation
class InputValidator
  def self.validate_user_input(input, rules)
    sanitized_input = sanitize_input(input)
    
    rules.each do |field, validations|
      value = sanitized_input[field]
      
      validations.each do |validation|
        case validation[:type]
        when :required
          raise ValidationError, "#{field} is required" if value.nil? || value.to_s.strip.empty?
          
        when :max_length
          raise ValidationError, "#{field} is too long (max #{validation[:max]} characters)" if value.to_s.length > validation[:max]
          
        when :min_length
          raise ValidationError, "#{field} is too short (min #{validation[:min]} characters)" if value.to_s.length < validation[:min]
          
        when :format
          raise ValidationError, "#{field} has invalid format" unless value.match?(Regexp.new(validation[:pattern]))
          
        when :allowed_values
          raise ValidationError, "#{field} must be one of: #{validation[:values].join(', ')}" unless validation[:values].include?(value)
          
        when :numeric
          raise ValidationError, "#{field} must be numeric" unless value.to_s.match?(/\A-?\d+(\.\d+)?\z/)
          
        when :email
          raise ValidationError, "#{field} must be a valid email" unless valid_email?(value)
          
        when :url
          raise ValidationError, "#{field} must be a valid URL" unless valid_url?(value)
        end
      end
    end
    
    sanitized_input
  end
  
  def self.sanitize_input(input)
    return input unless input.is_a?(Hash)
    
    sanitized = {}
    
    input.each do |key, value|
      case value
      when String
        # Remove potentially dangerous characters
        sanitized[key] = value.gsub(/[<>'"&]/, '')
      when Array
        sanitized[key] = value.map { |v| sanitize_value(v) }
      when Hash
        sanitized[key] = sanitize_input(value)
      else
        sanitized[key] = value
      end
    end
    
    sanitized
  end
  
  private
  
  def self.valid_email?(email)
    return false if email.nil?
    email.match?(/\A[^@\s]+@[^@\s]+\z/)
  end
  
  def self.valid_url?(url)
    return false if url.nil?
    
    begin
      uri = URI.parse(url)
      uri.is_a?(URI::HTTP) || uri.is_a?(URI::HTTPS)
    rescue URI::InvalidURIError
      false
    end
  end
  
  def self.sanitize_value(value)
    case value
    when String
      value.gsub(/[<>'"&]/, '')
    else
      value
    end
  end
end

puts "Input Validation Example:"

# Test input validation
user_input = {
  name: "John Doe",
  email: "john@example.com",
  age: "25",
  bio: "<script>alert('xss')</script>Hello world!"
}

validation_rules = {
  name: [
    { type: :required },
    { type: :max_length, max: 100 },
    { type: :format, pattern: /\A[a-zA-Z\s]+\z/ }
  ],
  email: [
    { type: :required },
    { type: :email }
  ],
  age: [
    { type: :required },
    { type: :numeric }
  ],
  bio: [
    { type: :max_length, max: 500 }
  ]
}

begin
  sanitized_input = InputValidator.validate_user_input(user_input, validation_rules)
  puts "✅ Input validated successfully"
  puts "Sanitized bio: #{sanitized_input[:bio]}"
rescue ValidationError => e
  puts "❌ Validation error: #{e.message}"
end

# Test invalid input
invalid_input = {
  name: "",
  email: "invalid-email",
  age: "not-a-number"
}

begin
  InputValidator.validate_user_input(invalid_input, validation_rules)
rescue ValidationError => e
  puts "❌ Validation caught error: #{e.message}"
end

puts "\n=== SQL INJECTION PREVENTION ==="

# ✅ GOOD: Use parameterized queries
class SafeUserRepository
  def initialize(database)
    @database = database
  end
  
  def find_by_email(email)
    # Safe: Uses parameterized query
    puts "Executing: SELECT * FROM users WHERE email = '#{email}'"  # Simulated
    puts "✅ Safe parameterized query executed"
    
    # Mock result
    {
      id: 1,
      email: email,
      name: "John Doe"
    }
  end
  
  def search_users(query)
    # Safe: Uses parameterized query with LIKE
    search_pattern = "%#{query}%"
    puts "Executing: SELECT * FROM users WHERE name ILIKE '#{search_pattern}'"  # Simulated
    puts "✅ Safe parameterized LIKE query executed"
    
    # Mock results
    [
      { id: 1, name: "John Smith", email: "john@example.com" },
      { id: 2, name: "Johnny Appleseed", email: "johnny@example.com" }
    ]
  end
  
  def create_user(user_data)
    # Safe: Uses parameterized query
    puts "Executing: INSERT INTO users (name, email, created_at) VALUES ('#{user_data[:name]}', '#{user_data[:email]}', '#{Time.now}')"  # Simulated
    puts "✅ Safe parameterized INSERT query executed"
    
    # Mock result
    user_data.merge(id: SecureRandom.uuid)
  end
end

# ❌ BAD: Vulnerable to SQL injection (for comparison)
class UnsafeUserRepository
  def initialize(database)
    @database = database
  end
  
  def find_by_email(email)
    # Dangerous: Interpolates user input directly into SQL
    query = "SELECT * FROM users WHERE email = '#{email}'"
    puts "Executing: #{query}"
    puts "❌ Dangerous SQL injection vulnerability!"
    
    nil  # Mock implementation
  end
  
  def search_users(query)
    # Dangerous: Interpolates user input directly into SQL
    search_pattern = "%#{query}%"
    sql_query = "SELECT * FROM users WHERE name ILIKE '#{search_pattern}'"
    puts "Executing: #{sql_query}"
    puts "❌ Dangerous SQL injection vulnerability!"
    
    []  # Mock implementation
  end
end

puts "SQL Injection Prevention Example:"

# Safe repository
safe_repo = SafeUserRepository.new(nil)

# Test safe queries
user = safe_repo.find_by_email("john@example.com")
puts "Found user: #{user[:name]}"

results = safe_repo.search_users("john")
puts "Search results: #{results.length} users found"

new_user = safe_repo.create_user(name: "Jane Doe", email: "jane@example.com")
puts "Created user: #{new_user[:id]}"

# Demonstrate vulnerability
puts "\nDemonstrating SQL injection vulnerability:"
unsafe_repo = UnsafeUserRepository.new(nil)

# This would be dangerous in real application
malicious_email = "'; DROP TABLE users; --"
unsafe_repo.find_by_email(malicious_email)

malicious_query = "'; DELETE FROM users; --"
unsafe_repo.search_users(malicious_query)

puts "\n=== AUTHENTICATION AND AUTHORIZATION ==="

# ✅ GOOD: Secure authentication with proper password hashing
class AuthenticationService
  def initialize(user_repository, session_manager)
    @user_repository = user_repository
    @session_manager = session_manager
  end
  
  def authenticate(email, password)
    user = @user_repository.find_by_email(email)
    return nil unless user
    
    if BCrypt::Password.new(user.password_hash) == password
      session_token = @session_manager.create_session(user.id)
      
      {
        user_id: user.id,
        session_token: session_token,
        expires_at: Time.now + 24.hours
      }
    else
      nil
    end
  end
  
  def register(user_data)
    validate_registration_data(user_data)
    
    # Hash password securely
    password_hash = BCrypt::Password.create(user_data[:password], cost: 12)
    
    user = User.new(
      email: user_data[:email],
      password_hash: password_hash,
      name: user_data[:name],
      created_at: Time.now
    )
    
    @user_repository.save(user)
  end
  
  def logout(session_token)
    @session_manager.destroy_session(session_token)
  end
  
  def validate_session(session_token)
    session = @session_manager.find_session(session_token)
    return nil unless session
    
    return nil if session[:expires_at] < Time.now
    
    user = @user_repository.find(session[:user_id])
    return nil unless user
    
    {
      user_id: user.id,
      email: user.email,
      name: user.name
    }
  end
  
  private
  
  def validate_registration_data(user_data)
    raise ValidationError, "Email is required" if user_data[:email].nil?
    raise ValidationError, "Password is required" if user_data[:password].nil?
    raise ValidationError, "Name is required" if user_data[:name].nil?
    
    raise ValidationError, "Invalid email format" unless valid_email?(user_data[:email])
    raise ValidationError, "Password must be at least 8 characters" if user_data[:password].length < 8
    
    # Check if user already exists
    existing_user = @user_repository.find_by_email(user_data[:email])
    raise ValidationError, "User already exists" if existing_user
  end
  
  def valid_email?(email)
    email.match?(/\A[^@\s]+@[^@\s]+\z/)
  end
end

# User model
class User
  attr_accessor :id, :email, :password_hash, :name, :created_at
  
  def initialize(data)
    @id = data[:id]
    @email = data[:email]
    @password_hash = data[:password_hash]
    @name = data[:name]
    @created_at = data[:created_at]
  end
end

# Session manager
class SessionManager
  def initialize
    @sessions = {}
  end
  
  def create_session(user_id)
    session_token = SecureRandom.uuid
    
    @sessions[session_token] = {
      user_id: user_id,
      created_at: Time.now,
      expires_at: Time.now + 24.hours
    }
    
    session_token
  end
  
  def find_session(session_token)
    @sessions[session_token]
  end
  
  def destroy_session(session_token)
    @sessions.delete(session_token)
  end
end

# ✅ GOOD: Role-based authorization
class AuthorizationService
  def initialize(user_repository)
    @user_repository = user_repository
  end
  
  def can_perform_action?(user_id, action, resource = nil)
    user = @user_repository.find(user_id)
    return false unless user
    
    case action
    when :read_users
      user.role.in?([:admin, :manager])
    when :create_user
      user.role == :admin
    when :delete_user
      user.role == :admin
    when :read_orders
      user.role.in?([:admin, :manager, :user])
    when :create_order
      user.role.in?([:admin, :manager, :user])
    when :delete_order
      user.role.in?([:admin, :manager])
    when :read_own_orders
      # Users can read their own orders
      return true if resource && resource[:user_id] == user_id
      user.role.in?([:admin, :manager])
    else
      false
    end
  end
  
  def authorize!(user_id, action, resource = nil)
    unless can_perform_action?(user_id, action, resource)
      raise AuthorizationError, "Not authorized to perform #{action}"
    end
    
    true
  end
end

class AuthorizationError < StandardError; end

puts "Authentication and Authorization Example:"

# Mock user repository
class MockUserRepository
  def initialize
    @users = {}
  end
  
  def save(user)
    @users[user.email] = user
    user
  end
  
  def find_by_email(email)
    @users[email]
  end
  
  def find(user_id)
    @users.values.find { |u| u.id == user_id }
  end
end

# Create services
user_repo = MockUserRepository.new
session_manager = SessionManager.new
auth_service = AuthenticationService.new(user_repo, session_manager)
authz_service = AuthorizationService.new(user_repo)

# Register a user
begin
  user_data = {
    email: "admin@example.com",
    password: "SecurePassword123",
    name: "Admin User"
  }
  
  user = auth_service.register(user_data)
  user.role = :admin
  user_repo.save(user)
  
  puts "✅ User registered: #{user.email}"
rescue ValidationError => e
  puts "❌ Registration failed: #{e.message}"
end

# Authenticate user
session = auth_service.authenticate("admin@example.com", "SecurePassword123")
if session
  puts "✅ User authenticated successfully"
  
  # Validate session
  user_data = auth_service.validate_session(session[:session_token])
  puts "✅ Session validated for: #{user_data[:name]}"
  
  # Test authorization
  begin
    authz_service.authorize!(user_data[:user_id], :read_users)
    puts "✅ Authorized to read users"
  rescue AuthorizationError => e
    puts "❌ Not authorized: #{e.message}"
  end
  
  # Logout
  auth_service.logout(session[:session_token])
  puts "✅ User logged out"
else
  puts "❌ Authentication failed"
end

puts "\n=== MEMORY MANAGEMENT ==="

# ✅ GOOD: Efficient memory usage
class DataProcessor
  def initialize(batch_size = 1000)
    @batch_size = batch_size
  end
  
  def process_large_file(file_path)
    puts "Processing large file in batches of #{@batch_size}"
    
    # Process file in chunks to avoid loading everything into memory
    file = File.open(file_path, 'w')
    
    # Simulate large file processing
    batch = []
    (1..10000).each do |i|
      batch << "Line #{i}: This is sample data for line #{i}"
      
      if batch.size >= @batch_size
        process_batch(batch)
        batch.clear  # Clear batch to free memory
        
        # Force garbage collection periodically
        GC.start if i % 5000 == 0
      end
    end
    
    # Process remaining lines
    process_batch(batch) unless batch.empty?
    
    file.close
    puts "✅ Large file processed efficiently"
  end
  
  def process_batch(batch)
    # Process batch of data
    puts "Processing batch of #{batch.length} lines"
    
    # Simulate processing time
    sleep(0.01)
    
    # Write to file
    File.open('large_file.txt', 'a') do |file|
      batch.each { |line| file.puts(line) }
    end
  end
end

# ✅ GOOD: Use objects efficiently with object pool
class ObjectPool
  def initialize(pool_size = 10, &factory)
    @pool = Queue.new
    @factory = factory
    @created = 0
    @max_size = pool_size
    
    # Pre-populate pool
    pool_size.times { create_object }
  end
  
  def with_object
    obj = acquire
    begin
      yield obj
    ensure
      release(obj)
    end
  end
  
  private
  
  def acquire
    if @pool.empty? && @created < @max_size
      create_object
    end
    
    @pool.pop
  end
  
  def release(obj)
    reset_object(obj)
    @pool.push(obj)
  end
  
  def create_object
    obj = @factory.call
    @created += 1
    @pool.push(obj)
  end
  
  def reset_object(obj)
    # Reset object state if needed
    obj.clear if obj.respond_to?(:clear)
  end
end

# Mock database connection
class DatabaseConnection
  def initialize(id)
    @id = id
    @connected = true
  end
  
  def execute(query)
    puts "Connection #{@id}: Executing #{query}"
    "Result for #{query}"
  end
  
  def clear
    # Reset connection state
    @connected = true
  end
end

puts "Memory Management Example:"

# Efficient data processing
processor = DataProcessor.new(500)
processor.process_large_file('sample_data.txt')

# Object pool usage
connection_pool = ObjectPool.new(3) { |i| DatabaseConnection.new(i) }

puts "Using connection pool:"

# Use connections from pool
3.times do |i|
  connection_pool.with_object do |connection|
    result = connection.execute("SELECT * FROM users WHERE id = #{i + 1}")
    puts "Got result: #{result}"
  end
end

# Clean up
File.delete('large_file.txt') rescue nil
File.delete('sample_data.txt') rescue nil

puts "\n=== CACHING STRATEGIES ==="

# ✅ GOOD: Implement caching with proper invalidation
class CacheService
  def initialize
    @cache = {}
    @default_ttl = 3600  # 1 hour
  end
  
  def get(key)
    cached_data = @cache[key]
    return nil unless cached_data
    
    if cached_data[:expires_at] < Time.now
      @cache.delete(key)
      return nil
    end
    
    cached_data[:data]
  end
  
  def set(key, data, ttl = nil)
    ttl ||= @default_ttl
    
    @cache[key] = {
      data: data,
      expires_at: Time.now + ttl
    }
  end
  
  def delete(key)
    @cache.delete(key)
  end
  
  def fetch(key, ttl = nil, &block)
    cached_data = get(key)
    return cached_data if cached_data
    
    data = block.call
    set(key, data, ttl)
    data
  end
  
  def invalidate_pattern(pattern)
    keys_to_delete = @cache.keys.select { |key| key.match?(Regexp.new(pattern)) }
    keys_to_delete.each { |key| @cache.delete(key) }
    
    keys_to_delete.length
  end
end

# User service with caching
class CachedUserService
  def initialize(user_repository, cache_service)
    @user_repository = user_repository
    @cache = cache_service
  end
  
  def get_user(user_id)
    cache_key = "user:#{user_id}"
    
    @cache.fetch(cache_key, 1800) do  # 30 minutes TTL
      puts "Fetching user from repository: #{user_id}"
      @user_repository.find(user_id)
    end
  end
  
  def update_user(user_id, updates)
    user = @user_repository.update(user_id, updates)
    
    # Invalidate cache
    cache_key = "user:#{user_id}"
    @cache.delete(cache_key)
    
    # Invalidate related caches
    @cache.invalidate_pattern("user:#{user_id}:*")
    
    puts "Invalidated caches for user #{user_id}"
    user
  end
  
  def get_user_orders(user_id)
    cache_key = "user:#{user_id}:orders"
    
    @cache.fetch(cache_key, 600) do  # 10 minutes TTL
      puts "Fetching user orders from repository: #{user_id}"
      @user_repository.find_user_orders(user_id)
    end
  end
end

puts "Caching Strategies Example:"

# Create cache service
cache = CacheService.new

# Create cached user service
class MockUserRepo
  def find(user_id)
    { id: user_id, name: "User #{user_id}", email: "user#{user_id}@example.com" }
  end
  
  def update(user_id, updates)
    { id: user_id, name: updates[:name] || "User #{user_id}", email: "user#{user_id}@example.com" }
  end
  
  def find_user_orders(user_id)
    [
      { id: 1, user_id: user_id, total: 100 },
      { id: 2, user_id: user_id, total: 200 }
    ]
  end
end

user_repo = MockUserRepo.new
cached_user_service = CachedUserService.new(user_repo, cache)

# Test caching
puts "Testing caching:"

# First call - should fetch from repository
user = cached_user_service.get_user(1)
puts "Got user: #{user[:name]}"

# Second call - should fetch from cache
user = cached_user_service.get_user(1)
puts "Got user from cache: #{user[:name]}"

# Update user - should invalidate cache
updated_user = cached_user_service.update_user(1, { name: "Updated User" })
puts "Updated user: #{updated_user[:name]}"

# Next call - should fetch from repository again
user = cached_user_service.get_user(1)
puts "Got user after update: #{user[:name]}"

# Test related cache invalidation
orders = cached_user_service.get_user_orders(1)
puts "Got #{orders.length} orders for user 1"

# Update user again
cached_user_service.update_user(1, { name: "Another Update" })

# Orders should be refetched from repository
orders = cached_user_service.get_user_orders(1)
puts "Got #{orders.length} orders for user 1 after update"

puts "\n=== ERROR HANDLING BEST PRACTICES ==="

# ✅ GOOD: Create specific exception classes
module ApplicationExceptions
  class BaseError < StandardError
    attr_reader :error_code, :context
    
    def initialize(message, error_code: nil, context: {})
      super(message)
      @error_code = error_code
      @context = context
    end
    
    def to_hash
      {
        error: self.class.name,
        message: message,
        error_code: error_code,
        context: context
      }
    end
  end
  
  class ValidationError < BaseError; end
  class AuthenticationError < BaseError; end
  class AuthorizationError < BaseError; end
  class NotFoundError < BaseError; end
  class BusinessLogicError < BaseError; end
  class ExternalServiceError < BaseError; end
end

# ✅ GOOD: Graceful error handling with fallbacks
class RobustAPIClient
  def initialize(base_url, timeout: 30, retries: 3)
    @base_url = base_url
    @timeout = timeout
    @retries = retries
  end
  
  def get_user(user_id)
    response = with_retries do
      make_request(:get, "/users/#{user_id}")
    end
    
    case response[:status]
    when 200
      response[:body]
    when 404
      raise ApplicationExceptions::NotFoundError.new(
        "User not found",
        error_code: 'USER_NOT_FOUND',
        context: { user_id: user_id }
      )
    when 500..599
      raise ApplicationExceptions::ExternalServiceError.new(
        "External service error: #{response[:status]}",
        error_code: 'EXTERNAL_SERVICE_ERROR',
        context: { status: response[:status] }
      )
    else
      raise ApplicationExceptions::ExternalServiceError.new(
        "Unexpected response: #{response[:status]}",
        error_code: 'UNEXPECTED_RESPONSE',
        context: { status: response[:status] }
      )
    end
  end
  
  def get_user_with_fallback(user_id, fallback_user = nil)
    get_user(user_id)
  rescue ApplicationExceptions::NotFoundError
    fallback_user
  rescue ApplicationExceptions::ExternalServiceError => e
    # Log error and return fallback
    puts "External service error: #{e.message}"
    fallback_user
  end
  
  private
  
  def with_retries(&block)
    attempts = 0
    
    begin
      block.call
    rescue Net::TimeoutError, Net::ReadTimeout, Net::OpenTimeout => e
      attempts += 1
      if attempts <= @retries
        sleep(2 ** attempts)  # Exponential backoff
        retry
      else
        raise ApplicationExceptions::ExternalServiceError.new(
          "Service unavailable after #{attempts} attempts: #{e.message}",
          error_code: 'SERVICE_UNAVAILABLE'
        )
      end
    end
  end
  
  def make_request(method, path)
    # Simulate API call
    puts "Making #{method} request to #{@base_url}#{path}"
    
    # Simulate different responses
    case path
    when /\/users\/\d+/
      if rand < 0.8
        { status: 200, body: { id: path.split('/').last, name: "User" } }
      else
        { status: 404, body: { error: "User not found" } }
      end
    when /\/users\/999/
      { status: 500, body: { error: "Internal server error" } }
    else
      { status: 404, body: { error: "Not found" } }
    end
  end
end

puts "Error Handling Best Practices Example:"

# Test error handling
api_client = RobustAPIClient.new('https://api.example.com')

# Test successful request
begin
  user = api_client.get_user(123)
  puts "✅ Got user: #{user[:name]}"
rescue ApplicationExceptions::BaseError => e
  puts "❌ Error: #{e.message} (#{e.error_code})"
end

# Test not found error
begin
  user = api_client.get_user(999)
  puts "✅ Got user: #{user[:name]}"
rescue ApplicationExceptions::BaseError => e
  puts "❌ Error: #{e.message} (#{e.error_code})"
end

# Test fallback mechanism
fallback_user = { id: 0, name: "Default User" }
user = api_client.get_user_with_fallback(999, fallback_user)
puts "Fallback user: #{user[:name]}"

puts "\n=== BEST PRACTICES SUMMARY ==="
puts "- Naming Conventions: Clear, descriptive names for classes, methods, and variables"
puts "- Single Responsibility: Each class has one well-defined purpose"
puts "- Dependency Injection: Loose coupling through dependency injection"
puts "- Factory Pattern: Clean object creation with factory methods"
puts "- Input Validation: Comprehensive validation and sanitization"
puts "- SQL Injection Prevention: Parameterized queries and input sanitization"
puts "- Authentication & Authorization: Secure password hashing and role-based access"
puts "- Memory Management: Efficient memory usage and object pooling"
puts "- Caching Strategies: Proper caching with invalidation"
puts "- Error Handling: Specific exceptions and graceful fallbacks"
puts "\nAll examples demonstrate comprehensive Ruby best practices!"
