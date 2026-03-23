# Security Examples
# Demonstrating security concepts and best practices in Ruby

puts "=== INPUT VALIDATION ==="

class InputValidator
  def self.validate_email(email)
    return false if email.nil? || email.strip.empty?
    
    email_pattern = /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
    email.match?(email_pattern)
  end
  
  def self.validate_password(password)
    return false if password.nil? || password.length < 8
    
    # Password must contain: uppercase, lowercase, digit, special character
    patterns = [
      /[A-Z]/,      # Uppercase
      /[a-z]/,      # Lowercase
      /\d/,         # Digit
      /[!@#$%^&*(),.?":{}|<>]/  # Special character
    ]
    
    patterns.all? { |pattern| password.match?(pattern) }
  end
  
  def self.validate_username(username)
    return false if username.nil? || username.strip.empty?
    return false if username.length < 3 || username.length > 20
    
    # Username can contain letters, numbers, underscores, and hyphens
    username_pattern = /\A[a-zA-Z0-9_-]+\z/
    username.match?(username_pattern)
  end
  
  def self.sanitize_filename(filename)
    return "default" if filename.nil? || filename.strip.empty?
    
    # Remove dangerous characters and limit length
    sanitized = filename.gsub(/[^a-zA-Z0-9._-]/, '_')
    sanitized[0..50]  # Limit to 50 characters
  end
  
  def self.sanitize_sql_input(input)
    # Never use string interpolation in SQL
    # This is a demonstration of proper escaping
    return "NULL" if input.nil?
    
    # In real applications, use parameterized queries
    ActiveRecord::Base.connection.quote(input.to_s)
  rescue
    "INVALID"
  end
end

puts "Input Validation Examples:"

emails = ["valid@example.com", "invalid-email", "", nil, "user@domain.com"]
emails.each do |email|
  puts "Email '#{email}': #{InputValidator.validate_email(email) ? 'Valid' : 'Invalid'}"
end

passwords = ["Password123!", "weak", "12345678", "NoSpecialChar123", "ValidPass123!"]
passwords.each do |password|
  puts "Password '#{password}': #{InputValidator.validate_password(password) ? 'Valid' : 'Invalid'}"
end

filenames = ["normal_file.txt", "../../../etc/passwd", "file with spaces.txt", "file|pipe.txt"]
filenames.each do |filename|
  sanitized = InputValidator.sanitize_filename(filename)
  puts "Filename '#{filename}' -> '#{sanitized}'"
end

puts "\n=== PASSWORD SECURITY ==="

require 'bcrypt'

class SecurePassword
  def self.hash_password(password)
    BCrypt::Password.create(password, cost: 12)
  end
  
  def self.verify_password(password, hashed_password)
    BCrypt::Password.new(hashed_password) == password
  end
  
  def self.generate_secure_token
    SecureRandom.urlsafe_base64(32)
  end
  
  def self.generate_password_reset_token
    token = SecureRandom.urlsafe_base64(32)
    expires_at = Time.now + 2.hours
    { token: token, expires_at: expires_at }
  end
end

puts "Password Security Examples:"

password = "MySecurePassword123!"
hashed = SecurePassword.hash_password(password)
puts "Original password: #{password}"
puts "Hashed password: #{hashed}"
puts "Password valid: #{SecurePassword.verify_password(password, hashed)}"

puts "Password invalid: #{SecurePassword.verify_password("wrongpassword", hashed)}"

token = SecurePassword.generate_secure_token
puts "Secure token: #{token}"

reset_data = SecurePassword.generate_password_reset_token
puts "Reset token: #{reset_data[:token]}"
puts "Expires at: #{reset_data[:expires_at]}"

puts "\n=== AUTHENTICATION SYSTEM ==="

class User
  attr_accessor :id, :email, :password_hash, :failed_attempts, :locked_until
  
  def initialize(email:, password:)
    @id = rand(1000..9999)
    @email = email
    @password_hash = SecurePassword.hash_password(password)
    @failed_attempts = 0
    @locked_until = nil
  end
  
  def authenticate(password)
    return false if locked?
    
    if SecurePassword.verify_password(password, @password_hash)
      reset_failed_attempts
      true
    else
      increment_failed_attempts
      false
    end
  end
  
  def locked?
    @locked_until && @locked_until > Time.now
  end
  
  def lock_account(duration = 30.minutes)
    @locked_until = Time.now + duration
  end
  
  private
  
  def increment_failed_attempts
    @failed_attempts += 1
    lock_account if @failed_attempts >= 5
  end
  
  def reset_failed_attempts
    @failed_attempts = 0
  end
end

class AuthenticationService
  def initialize
    @users = {}
    @sessions = {}
  end
  
  def register(email, password)
    return { success: false, error: "Invalid email" } unless InputValidator.validate_email(email)
    return { success: false, error: "Invalid password" } unless InputValidator.validate_password(password)
    return { success: false, error: "Email already exists" } if @users[email]
    
    user = User.new(email: email, password: password)
    @users[email] = user
    
    { success: true, user: user }
  end
  
  def login(email, password, ip_address = "unknown")
    user = @users[email]
    
    if user.nil?
      log_security_event("login_attempt", email: email, ip: ip_address, success: false, reason: "user_not_found")
      return { success: false, error: "Invalid credentials" }
    end
    
    if user.locked?
      log_security_event("login_attempt", email: email, ip: ip_address, success: false, reason: "account_locked")
      return { success: false, error: "Account locked" }
    end
    
    if user.authenticate(password)
      session_token = SecurePassword.generate_secure_token
      @sessions[session_token] = { user_id: user.id, created_at: Time.now, ip: ip_address }
      
      log_security_event("login_success", email: email, ip: ip_address)
      { success: true, session_token: session_token }
    else
      log_security_event("login_attempt", email: email, ip: ip_address, success: false, reason: "invalid_password")
      { success: false, error: "Invalid credentials" }
    end
  end
  
  def logout(session_token)
    if @sessions[session_token]
      @sessions.delete(session_token)
      log_security_event("logout", session_token: session_token)
      { success: true }
    else
      { success: false, error: "Invalid session" }
    end
  end
  
  def validate_session(session_token, ip_address = "unknown")
    session = @sessions[session_token]
    
    if session.nil?
      return { valid: false, error: "Invalid session" }
    end
    
    # Check session age (2 hours)
    if session[:created_at] < 2.hours.ago
      @sessions.delete(session_token)
      return { valid: false, error: "Session expired" }
    end
    
    # Check IP address (optional security measure)
    if session[:ip] != ip_address
      log_security_event("session_hijack_attempt", session_token: session_token, original_ip: session[:ip], current_ip: ip_address)
      return { valid: false, error: "Invalid session" }
    end
    
    { valid: true, user_id: session[:user_id] }
  end
  
  private
  
  def log_security_event(event, details = {})
    puts "[SECURITY LOG] #{Time.now.utc}: #{event} - #{details}"
  end
end

puts "Authentication System Example:"

auth_service = AuthenticationService.new

# Register users
result1 = auth_service.register("user@example.com", "SecurePass123!")
result2 = auth_service.register("admin@example.com", "AdminPass123!")

puts "Registration 1: #{result1[:success] ? 'Success' : result1[:error]}"
puts "Registration 2: #{result2[:success] ? 'Success' : result2[:error]}"

# Login attempts
login1 = auth_service.login("user@example.com", "SecurePass123!", "192.168.1.100")
login2 = auth_service.login("user@example.com", "wrongpassword", "192.168.1.100")
login3 = auth_service.login("nonexistent@example.com", "password", "192.168.1.100")

puts "Login 1: #{login1[:success] ? 'Success' : login1[:error]}"
puts "Login 2: #{login2[:success] ? 'Success' : login2[:error]}"
puts "Login 3: #{login3[:success] ? 'Success' : login3[:error]}"

# Session validation
if login1[:success]
  validation = auth_service.validate_session(login1[:session_token], "192.168.1.100")
  puts "Session validation: #{validation[:valid] ? 'Valid' : validation[:error]}"
end

puts "\n=== AUTHORIZATION AND RBAC ==="

module RoleBasedAccessControl
  ROLES = {
    guest: 0,
    user: 1,
    moderator: 2,
    admin: 3
  }.freeze
  
  PERMISSIONS = {
    read_posts: { min_level: 0 },
    create_posts: { min_level: 1 },
    edit_own_posts: { min_level: 1 },
    edit_any_posts: { min_level: 2 },
    delete_own_posts: { min_level: 1 },
    delete_any_posts: { min_level: 2 },
    manage_users: { min_level: 3 },
    view_admin_panel: { min_level: 3 }
  }.freeze
  
  def self.can?(user_role, permission)
    return false unless ROLES.key?(user_role.to_sym)
    return false unless PERMISSIONS.key?(permission.to_sym)
    
    user_level = ROLES[user_role.to_sym]
    required_level = PERMISSIONS[permission.to_sym][:min_level]
    
    user_level >= required_level
  end
  
  def self.permissions_for_role(role)
    return [] unless ROLES.key?(role.to_sym)
    
    user_level = ROLES[role.to_sym]
    
    PERMISSIONS.select { |_, config| config[:min_level] <= user_level }.keys
  end
end

class SecureController
  def initialize(current_user = nil)
    @current_user = current_user
  end
  
  def require_permission(permission)
    unless @current_user && RoleBasedAccessControl.can?(@current_user[:role], permission)
      raise "Access denied: #{permission} required"
    end
  end
  
  def create_post(title, content)
    require_permission(:create_posts)
    "Post '#{title}' created by #{@current_user[:name]}"
  end
  
  def edit_post(post_id, author_id = nil)
    if author_id && author_id == @current_user[:id]
      require_permission(:edit_own_posts)
    else
      require_permission(:edit_any_posts)
    end
    "Post #{post_id} edited by #{@current_user[:name]}"
  end
  
  def delete_user(user_id)
    require_permission(:manage_users)
    "User #{user_id} deleted by #{@current_user[:name]}"
  end
end

puts "Authorization and RBAC Example:"

roles = [:guest, :user, :moderator, :admin]
permissions = [:read_posts, :create_posts, :edit_own_posts, :edit_any_posts, :delete_own_posts, :delete_any_posts, :manage_users]

roles.each do |role|
  puts "\n#{role.to_s.upcase} permissions:"
  permissions.each do |permission|
    can = RoleBasedAccessControl.can?(role, permission)
    puts "  #{permission}: #{can ? '✓' : '✗'}"
  end
end

# Test controller actions
admin_user = { id: 1, name: "Admin", role: :admin }
regular_user = { id: 2, name: "User", role: :user }

admin_controller = SecureController.new(admin_user)
user_controller = SecureController.new(regular_user)

begin
  puts "\nAdmin creating post: #{admin_controller.create_post('Admin Post', 'Content')}"
  puts "User creating post: #{user_controller.create_post('User Post', 'Content')}"
  puts "User deleting user: #{user_controller.delete_user(3)}"
rescue => e
  puts "Error: #{e.message}"
end

puts "\n=== XSS PREVENTION ==="

class XSSProtection
  def self.escape_html(input)
    return "" if input.nil?
    
    # Basic HTML escaping
    input.gsub('&', '&amp;')
         .gsub('<', '&lt;')
         .gsub('>', '&gt;')
         .gsub('"', '&quot;')
         .gsub("'", '&#x27;')
  end
  
  def self.escape_javascript(input)
    return "" if input.nil?
    
    input.gsub('\\', '\\\\')
         .gsub('</', '<\/')
         .gsub("\r\n", '\\n')
         .gsub("\n", '\\n')
         .gsub("\r", '\\n')
         .gsub('"', '\\"')
         .gsub("'", "\\'")
  end
  
  def self.sanitize_user_content(content)
    return "" if content.nil?
    
    # Remove dangerous HTML tags and attributes
    dangerous_tags = %w[script iframe object embed form input button]
    dangerous_attributes = %w[onclick onload onerror onmouseover]
    
    # Simple sanitization (in production, use proper libraries)
    sanitized = content.dup
    
    dangerous_tags.each do |tag|
      sanitized.gsub!(/<#{tag}[^>]*>.*?<\/#{tag}>/im, '')
      sanitized.gsub!(/<#{tag}[^>]*\/>/im, '')
    end
    
    dangerous_attributes.each do |attr|
      sanitized.gsub!(/\s#{attr}=["'][^"']*["']/im, '')
    end
    
    sanitized
  end
  
  def self.generate_csp_header
    directives = [
      "default-src 'self'",
      "script-src 'self' https://trusted.cdn.com",
      "style-src 'self' 'unsafe-inline'",
      "img-src 'self' data: https:",
      "font-src 'self'",
      "connect-src 'self'",
      "frame-ancestors 'none'",
      "base-uri 'self'"
    ].join('; ')
    
    directives
  end
end

puts "XSS Prevention Examples:"

malicious_inputs = [
  '<script>alert("XSS")</script>',
  '<img src="x" onerror="alert(\'XSS\')">',
  'Hello <b>World</b>',
  'Link: <a href="javascript:alert(\'XSS\')">Click me</a>',
  'Normal text'
]

puts "HTML Escaping:"
malicious_inputs.each do |input|
  escaped = XSSProtection.escape_html(input)
  puts "Original: #{input}"
  puts "Escaped:  #{escaped}"
  puts "---"
end

puts "Content Sanitization:"
user_content = '<p>Hello <b>World</b></p><script>alert("XSS")</script><img src="x" onerror="alert(\'XSS\')">'
sanitized = XSSProtection.sanitize_user_content(user_content)
puts "Original: #{user_content}"
puts "Sanitized: #{sanitized}"

puts "CSP Header: #{XSSProtection.generate_csp_header}"

puts "\n=== CSRF PROTECTION ==="

class CSRFProtection
  def self.generate_token
    SecureRandom.urlsafe_base64(32)
  end
  
  def self.validate_token(session_token, request_token)
    return false if session_token.nil? || request_token.nil?
    
    # In a real application, you'd use constant-time comparison
    session_token == request_token
  end
  
  def self.generate_form_field(token)
    "<input type='hidden' name='csrf_token' value='#{token}'>"
  end
end

class SecureFormHandler
  def initialize
    @sessions = {}
  end
  
  def start_session
    session_id = SecureRandom.urlsafe_base64(32)
    csrf_token = CSRFProtection.generate_token
    
    @sessions[session_id] = {
      csrf_token: csrf_token,
      created_at: Time.now
    }
    
    { session_id: session_id, csrf_token: csrf_token }
  end
  
  def handle_form_submission(session_id, params)
    session = @sessions[session_id]
    
    if session.nil?
      return { success: false, error: "Invalid session" }
    end
    
    csrf_token = params[:csrf_token]
    
    unless CSRFProtection.validate_token(session[:csrf_token], csrf_token)
      return { success: false, error: "Invalid CSRF token" }
    end
    
    # Process the form data
    { success: true, message: "Form processed successfully" }
  end
end

puts "CSRF Protection Example:"

form_handler = SecureFormHandler.new
session_data = form_handler.start_session

puts "Session ID: #{session_data[:session_id]}"
puts "CSRF Token: #{session_data[:csrf_token]}"
puts "Form field: #{CSRFProtection.generate_form_field(session_data[:csrf_token])}"

# Simulate form submission
valid_params = { csrf_token: session_data[:csrf_token], name: "John", email: "john@example.com" }
invalid_params = { csrf_token: "invalid_token", name: "John", email: "john@example.com" }

result1 = form_handler.handle_form_submission(session_data[:session_id], valid_params)
result2 = form_handler.handle_form_submission(session_data[:session_id], invalid_params)

puts "Valid submission: #{result1[:success] ? 'Success' : result1[:error]}"
puts "Invalid submission: #{result2[:success] ? 'Success' : result2[:error]}"

puts "\n=== RATE LIMITING ==="

class RateLimiter
  def initialize(redis = nil)
    @redis = redis || MemoryStore.new
    @limits = {
      login_attempts: { max: 5, window: 15.minutes },
      api_requests: { max: 100, window: 1.hour },
      password_resets: { max: 3, window: 1.hour }
    }
  end
  
  def allowed?(key, type)
    limit = @limits[type]
    return true unless limit
    
    current_time = Time.now.to_i
    window_start = current_time - limit[:window]
    
    # Remove old entries
    @redis.zremrangebyscore("#{key}:#{type}", 0, window_start)
    
    # Check current count
    current_count = @redis.zcard("#{key}:#{type}")
    
    if current_count >= limit[:max]
      false
    else
      @redis.zadd("#{key}:#{type}", current_time, current_time)
      @redis.expire("#{key}:#{type}", limit[:window])
      true
    end
  end
  
  def reset(key, type)
    @redis.del("#{key}:#{type}")
  end
  
  def remaining_requests(key, type)
    limit = @limits[type]
    return limit[:max] unless limit
    
    current_count = @redis.zcard("#{key}:#{type}")
    [limit[:max] - current_count, 0].max
  end
end

# Simple in-memory store for demonstration
class MemoryStore
  def initialize
    @data = {}
  end
  
  def zadd(key, score, member)
    @data[key] ||= []
    @data[key] << { score: score, member: member }
    @data[key].sort_by! { |item| item[:score] }
  end
  
  def zcard(key)
    @data[key]&.length || 0
  end
  
  def zremrangebyscore(key, min, max)
    return unless @data[key]
    
    @data[key].reject! { |item| item[:score] <= min }
  end
  
  def expire(key, ttl)
    # In a real implementation, this would set expiration
  end
  
  def del(key)
    @data.delete(key)
  end
end

puts "Rate Limiting Example:"

rate_limiter = RateLimiter.new

# Test rate limiting
ip_address = "192.168.1.100"

5.times do |i|
  allowed = rate_limiter.allowed?(ip_address, :login_attempts)
  remaining = rate_limiter.remaining_requests(ip_address, :login_attempts)
  
  puts "Attempt #{i + 1}: #{allowed ? 'Allowed' : 'Blocked'} (Remaining: #{remaining})"
end

puts "\n=== ENCRYPTION EXAMPLE ==="

require 'openssl'
require 'base64'

class DataEncryption
  def self.encrypt(data, key: 'default_key_32_characters_long!!')
    cipher = OpenSSL::Cipher.new('aes-256-gcm')
    cipher.encrypt
    cipher.key = Digest::SHA256.digest(key)[0, 32]
    cipher.iv = iv = cipher.random_iv
    
    encrypted = cipher.update(data) + cipher.final
    tag = cipher.auth_tag
    
    Base64.strict_encode(iv + tag + encrypted)
  end
  
  def self.decrypt(encrypted_data, key: 'default_key_32_characters_long!!')
    data = Base64.strict_decode64(encrypted_data)
    
    cipher = OpenSSL::Cipher.new('aes-256-gcm')
    cipher.decrypt
    cipher.key = Digest::SHA256.digest(key)[0, 32]
    cipher.iv = data[0, 12]
    cipher.auth_tag = data[12, 16]
    
    decrypted = cipher.update(data[28..-1]) + cipher.final
    decrypted
  rescue OpenSSL::Cipher::CipherError
    raise 'Invalid encrypted data'
  end
  
  def self.hash_data(data)
    Digest::SHA256.hexdigest(data)
  end
end

puts "Encryption Example:"

sensitive_data = "This is sensitive information"
encryption_key = "my_secret_encryption_key"

encrypted = DataEncryption.encrypt(sensitive_data, key: encryption_key)
decrypted = DataEncryption.decrypt(encrypted, key: encryption_key)

puts "Original:  #{sensitive_data}"
puts "Encrypted: #{encrypted}"
puts "Decrypted: #{decrypted}"

# Test with wrong key
begin
  DataEncryption.decrypt(encrypted, key: "wrong_key")
rescue => e
  puts "Decryption with wrong key: #{e.message}"
end

data_hash = DataEncryption.hash_data("important data")
puts "Data hash: #{data_hash}"

puts "\n=== SECURITY SUMMARY ==="
puts "- Input Validation: Email, password, filename, SQL input validation"
puts "- Password Security: Hashing, token generation, account lockout"
puts "- Authentication: User registration, login, session management"
puts "- Authorization: Role-based access control, permission checking"
puts "- XSS Prevention: HTML escaping, content sanitization, CSP headers"
puts "- CSRF Protection: Token generation and validation"
puts "- Rate Limiting: Request limiting per IP/user"
puts "- Encryption: Data encryption and hashing"
puts "\nAll examples demonstrate essential security concepts in Ruby!"
