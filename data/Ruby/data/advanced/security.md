# Security in Ruby

## Overview

Security is crucial for any application. This guide covers security best practices, common vulnerabilities, and techniques for building secure Ruby applications.

## Input Validation

### Parameter Validation

```ruby
# Strong parameters in Rails
class UsersController < ApplicationController
  def user_params
    params.require(:user).permit(:name, :email, :age)
  end
end

# Custom validation
class User < ApplicationRecord
  validates :email, format: { with: URI::MailTo::EMAIL_REGEXP }
  validates :age, numericality: { greater_than: 0, less_than: 150 }
  validates :name, length: { minimum: 2, maximum: 50 }
  
  validate :validate_email_uniqueness
  
  private
  
  def validate_email_uniqueness
    return unless email.present?
    
    if User.where(email: email.downcase).where.not(id: id).exists?
      errors.add(:email, 'has already been taken')
    end
  end
end

# Input sanitization
class InputSanitizer
  def self.sanitize_html(input)
    # Use Rails' sanitize helper
    ActionController::Base.helpers.sanitize(input)
  end
  
  def self.sanitize_sql(input)
    # Never use string interpolation in SQL
    # Use parameterized queries instead
    ActiveRecord::Base.connection.quote(input)
  end
  
  def self.sanitize_filename(filename)
    # Remove dangerous characters
    filename.gsub(/[^a-zA-Z0-9._-]/, '_')
  end
end

# Usage
safe_html = InputSanitizer.sanitize_html('<script>alert("xss")</script>')
safe_sql = InputSanitizer.sanitize_sql("'; DROP TABLE users; --")
safe_filename = InputSanitizer.sanitize_filename("../../../etc/passwd")
```

### SQL Injection Prevention

```ruby
# Bad: Vulnerable to SQL injection
class BadUserController < ApplicationController
  def show
    @user = User.find_by_sql("SELECT * FROM users WHERE id = #{params[:id]}")
  end
end

# Good: Using parameterized queries
class GoodUserController < ApplicationController
  def show
    @user = User.find_by_sql([
      "SELECT * FROM users WHERE id = ?",
      params[:id]
    ])
  end
end

# Better: Using ActiveRecord methods
class BestUserController < ApplicationController
  def show
    @user = User.find(params[:id])
  end
end

# Custom queries with parameters
class User < ApplicationRecord
  def self.search_by_name(name)
    where('name ILIKE ?', "%#{sanitize_sql_like(name)}%")
  end
  
  def self.find_by_complex_criteria(criteria)
    where([
      'name = ? AND email = ? AND created_at > ?',
      criteria[:name],
      criteria[:email],
      criteria[:date]
    ])
  end
end
```

## Authentication and Authorization

### Password Security

```ruby
# Secure password handling
class User < ApplicationRecord
  has_secure_password
  
  validates :password, length: { minimum: 8 }, if: :password_required?
  validates :password, format: {
    with: /\A(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/,
    message: 'must contain at least one lowercase letter, one uppercase letter, one digit, and one special character'
  }, if: :password_required?
  
  def password_required?
    !persisted? || !password.nil? || !password.empty?
  end
  
  # Password reset
  def generate_password_reset_token
    token = SecureRandom.urlsafe_base64
    update(password_reset_token: token, password_reset_sent_at: Time.now)
    token
  end
  
  def password_reset_token_valid?
    password_reset_sent_at && password_reset_sent_at > 2.hours.ago
  end
  
  def reset_password!(new_password)
    self.password = new_password
    self.password_reset_token = nil
    self.password_reset_sent_at = nil
    save!
  end
end

# Session management
class SessionsController < ApplicationController
  def create
    user = User.find_by(email: params[:email].downcase)
    
    if user&.authenticate(params[:password])
      session[:user_id] = user.id
      session[:expires_at] = 2.hours.from_now
      
      # Regenerate session ID to prevent session fixation
      reset_session
      session[:user_id] = user.id
      session[:expires_at] = 2.hours.from_now
      
      redirect_to dashboard_path, notice: 'Logged in successfully'
    else
      flash.now[:alert] = 'Invalid email or password'
      render :new
    end
  end
  
  def destroy
    reset_session
    redirect_to root_path, notice: 'Logged out successfully'
  end
end

# Session security
class ApplicationController < ActionController::Base
  before_action :check_session_expiration
  
  private
  
  def check_session_expiration
    if session[:expires_at] && session[:expires_at] < Time.now
      reset_session
      redirect_to login_path, alert: 'Session expired. Please log in again.'
    end
  end
  
  def current_user
    @current_user ||= User.find(session[:user_id]) if session[:user_id]
  end
  
  helper_method :current_user
  
  def authenticate_user!
    redirect_to login_path, alert: 'Please log in to continue.' unless current_user
  end
end
```

### Authorization

```ruby
# Role-based access control
class User < ApplicationRecord
  enum role: { user: 0, moderator: 1, admin: 2 }
  
  def can?(action, resource)
    case role.to_sym
    when :admin
      true
    when :moderator
      can_moderate?(action, resource)
    when :user
      can_user?(action, resource)
    end
  end
  
  private
  
  def can_moderate?(action, resource)
    case resource.class.name
    when 'Post'
      action.in?([:read, :update, :destroy])
    when 'Comment'
      action.in?([:read, :update, :destroy])
    else
      action == :read
    end
  end
  
  def can_user?(action, resource)
    case resource.class.name
    when 'Post'
      action == :read || (action.in?([:update, :destroy]) && resource.user == self)
    when 'Comment'
      action == :read || (action.in?([:update, :destroy]) && resource.user == self)
    else
      action == :read
    end
  end
end

# Pundit policy system
class PostPolicy < ApplicationPolicy
  class Scope < Scope
    def resolve
      if user.admin?
        scope.all
      elsif user.moderator?
        scope.all
      else
        scope.where(user: user)
      end
    end
  end
  
  def show?
    true
  end
  
  def create?
    user.present?
  end
  
  def update?
    user.admin? || user.moderator? || record.user == user
  end
  
  def destroy?
    user.admin? || user.moderator? || record.user == user
  end
end

# Controller usage
class PostsController < ApplicationController
  before_action :authenticate_user!
  before_action :set_post, only: [:show, :edit, :update, :destroy]
  
  def index
    @posts = policy_scope(Post)
    authorize @posts
  end
  
  def show
    authorize @post
  end
  
  def update
    authorize @post
    
    if @post.update(post_params)
      redirect_to @post, notice: 'Post was successfully updated.'
    else
      render :edit
    end
  end
  
  private
  
  def set_post
    @post = Post.find(params[:id])
  end
end
```

## XSS Prevention

### Output Escaping

```ruby
# Rails automatically escapes output in ERB
# <%= @post.title %> # Escaped
# <%= raw @post.title %> # Not escaped (dangerous)
# <%= @post.title.html_safe %> # Not escaped (dangerous)

# Manual escaping
class OutputEscaper
  def self.escape_html(input)
    ERB::Util.html_escape(input)
  end
  
  def self.escape_javascript(input)
    ERB::Util.javascript_escape(input)
  end
  
  def self.escape_url(input)
    ERB::Util.url_encode(input)
  end
end

# Content Security Policy
class ApplicationController < ActionController::Base
  def set_csp_header
    response.headers['Content-Security-Policy'] = [
      "default-src 'self'",
      "script-src 'self' https://trusted.cdn.com",
      "style-src 'self' 'unsafe-inline'",
      "img-src 'self' data: https:",
      "font-src 'self'",
      "connect-src 'self'",
      "frame-ancestors 'none'",
      "base-uri 'self'"
    ].join('; ')
  end
end

# Safe JSON responses
class ApiController < ApplicationController
  def render_json(data)
    render json: data.as_json
  end
end
```

### File Upload Security

```ruby
# Secure file upload
class FileUploader
  ALLOWED_EXTENSIONS = %w[.jpg .jpeg .png .gif .pdf .doc .docx].freeze
  MAX_FILE_SIZE = 10.megabytes
  
  def self.upload(file)
    validate_file(file)
    
    filename = secure_filename(file.original_filename)
    path = Rails.root.join('uploads', filename)
    
    File.open(path, 'wb') do |f|
      f.write(file.read)
    end
    
    filename
  end
  
  private
  
  def self.validate_file(file)
    raise 'No file provided' if file.nil?
    raise 'File too large' if file.size > MAX_FILE_SIZE
    
    extension = File.extname(file.original_filename).downcase
    raise 'Invalid file type' unless ALLOWED_EXTENSIONS.include?(extension)
    
    # Check file content type
    content_type = file.content_type
    unless content_type.start_with?('image/') || content_type == 'application/pdf'
      raise 'Invalid content type'
    end
  end
  
  def self.secure_filename(filename)
    # Remove dangerous characters
    sanitized = filename.gsub(/[^a-zA-Z0-9._-]/, '_')
    
    # Add timestamp to prevent filename collisions
    timestamp = Time.now.strftime('%Y%m%d_%H%M%S')
    name_part = File.basename(sanitized, File.extname(sanitized))
    ext_part = File.extname(sanitized)
    
    "#{timestamp}_#{name_part}#{ext_part}"
  end
end

# Virus scanning (conceptual)
class VirusScanner
  def self.scan_file(file_path)
    # Integrate with antivirus software
    # This is a conceptual implementation
    system("clamscan --no-summary #{file_path}")
    $?.success?
  end
end
```

## CSRF Protection

### Cross-Site Request Forgery Prevention

```ruby
# Rails CSRF protection is enabled by default
class ApplicationController < ActionController::Base
  protect_from_forgery with: :exception
  
  # Custom token handling for APIs
  def verified_request?
    if request.content_type == "application/json"
      verified_request_with_json_token?
    else
      super
    end
  end
  
  private
  
  def verified_request_with_json_token?
    request.headers['X-CSRF-Token'] == form_authenticity_token
  end
end

# AJAX CSRF token handling
# app/views/layouts/application.html.erb
<head>
  <%= csrf_meta_tags %>
</head>

# app/assets/javascripts/application.js
document.addEventListener('DOMContentLoaded', function() {
  // Add CSRF token to all AJAX requests
  const token = document.querySelector('meta[name="csrf-token"]');
  
  if (token) {
    const csrfToken = token.getAttribute('content');
    
    // For fetch API
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
      const headers = new Headers(options.headers || {});
      headers.set('X-CSRF-Token', csrfToken);
      options.headers = headers;
      return originalFetch(url, options);
    };
    
    // For jQuery
    if (window.jQuery) {
      $.ajaxSetup({
        headers: {
          'X-CSRF-Token': csrfToken
        }
      });
    }
  }
});
```

## Rate Limiting

### API Rate Limiting

```ruby
# Rate limiting implementation
class RateLimiter
  def initialize(redis, max_requests, time_window)
    @redis = redis
    @max_requests = max_requests
    @time_window = time_window
  end
  
  def allowed?(key)
    current_time = Time.now.to_i
    window_start = current_time - @time_window
    
    # Remove old entries
    @redis.zremrangebyscore(key, 0, window_start)
    
    # Check current count
    current_count = @redis.zcard(key)
    
    if current_count >= @max_requests
      false
    else
      @redis.zadd(key, current_time, current_time)
      @redis.expire(key, @time_window)
      true
    end
  end
  
  def reset(key)
    @redis.del(key)
  end
end

# Rate limiting middleware
class RateLimitMiddleware
  def initialize(app, redis: nil, max_requests: 100, time_window: 3600)
    @app = app
    @rate_limiter = RateLimiter.new(redis || Redis.new, max_requests, time_window)
  end
  
  def call(env)
    request = Rack::Request.new(env)
    key = "rate_limit:#{request.ip}:#{request.path_info}"
    
    unless @rate_limiter.allowed?(key)
      return [429, {'Content-Type' => 'application/json'}, ['{"error": "Rate limit exceeded"}']]
    end
    
    @app.call(env)
  end
end

# Usage in Rails
class ApplicationController < ActionController::Base
  before_action :check_rate_limit
  
  private
  
  def check_rate_limit
    key = "rate_limit:#{request.remote_ip}:#{request.path}"
    rate_limiter = RateLimiter.new(Redis.new, 100, 3600)
    
    unless rate_limiter.allowed?(key)
      render json: { error: 'Rate limit exceeded' }, status: :too_many_requests
    end
  end
end
```

## Logging and Monitoring

### Security Logging

```ruby
# Security logger
class SecurityLogger
  def self.log_security_event(event, details = {})
    Rails.logger.info({
      event: event,
      timestamp: Time.now.utc,
      ip: details[:ip],
      user_id: details[:user_id],
      user_agent: details[:user_agent],
      details: details
    }.to_json)
  end
  
  def self.log_login_attempt(ip, email, success)
    log_security_event('login_attempt', {
      ip: ip,
      email: email,
      success: success
    })
  end
  
  def self.log_failed_login(ip, email, reason)
    log_security_event('failed_login', {
      ip: ip,
      email: email,
      reason: reason
    })
  end
  
  def self.log_permission_denial(user, action, resource)
    log_security_event('permission_denied', {
      user_id: user.id,
      action: action,
      resource: resource.class.name,
      resource_id: resource.id
    })
  end
end

# Usage in controllers
class SessionsController < ApplicationController
  def create
    user = User.find_by(email: params[:email].downcase)
    
    if user&.authenticate(params[:password])
      SecurityLogger.log_login_attempt(request.remote_ip, user.email, true)
      session[:user_id] = user.id
      redirect_to dashboard_path, notice: 'Logged in successfully'
    else
      SecurityLogger.log_failed_login(request.remote_ip, params[:email], 'invalid_credentials')
      flash.now[:alert] = 'Invalid email or password'
      render :new
    end
  end
end

class PostsController < ApplicationController
  def update
    authorize @post
    
    if @post.update(post_params)
      redirect_to @post, notice: 'Post was successfully updated.'
    else
      render :edit
    end
  rescue Pundit::NotAuthorizedError
    SecurityLogger.log_permission_denial(current_user, 'update', @post)
    redirect_to root_path, alert: 'You are not authorized to perform this action.'
  end
end
```

## Encryption and Hashing

### Data Encryption

```ruby
# Encryption utilities
class DataEncryption
  def self.encrypt(data, key: Rails.application.secret_key_base)
    cipher = OpenSSL::Cipher.new('aes-256-gcm')
    cipher.encrypt
    cipher.key = Digest::SHA256.digest(key)[0, 32]
    cipher.iv = iv = cipher.random_iv
    
    encrypted = cipher.update(data) + cipher.final
    tag = cipher.auth_tag
    
    Base64.strict_encode(iv + tag + encrypted)
  end
  
  def self.decrypt(encrypted_data, key: Rails.application.secret_key_base)
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
  
  def self.encrypt_sensitive_attributes(model, *attributes)
    attributes.each do |attr|
      model.define_method("#{attr}=") do |value|
        encrypted_value = encrypt(value.to_s)
        super(encrypted_value)
      end
      
      model.define_method(attr) do
        encrypted_value = super()
        encrypted_value ? decrypt(encrypted_value) : nil
      end
    end
  end
end

# Usage
class UserProfile < ApplicationRecord
  extend DataEncryption
  
  encrypt_sensitive_attributes :ssn, :bank_account_number
  
  validates :ssn, presence: true
  validates :bank_account_number, presence: true
end

# Hashing utilities
class SecureHasher
  def self.hash_password(password)
    BCrypt::Password.create(password, cost: 12)
  end
  
  def self.verify_password(password, hashed_password)
    BCrypt::Password.new(hashed_password) == password
  end
  
  def self.hash_data(data)
    Digest::SHA256.hexdigest(data)
  end
  
  def self.generate_secure_token
    SecureRandom.urlsafe_base64(32)
  end
end
```

## Security Headers

### HTTP Security Headers

```ruby
# Security headers middleware
class SecurityHeaders
  def initialize(app)
    @app = app
  end
  
  def call(env)
    status, headers, response = @app.call(env)
    
    # Security headers
    headers['X-Frame-Options'] = 'DENY'
    headers['X-Content-Type-Options'] = 'nosniff'
    headers['X-XSS-Protection'] = '1; mode=block'
    headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains'
    headers['Referrer-Policy'] = 'strict-origin-when-cross-origin'
    headers['Permissions-Policy'] = [
      'geolocation=()',
      'microphone=()',
      'camera=()',
      'payment=()',
      'usb=()'
    ].join(', ')
    
    [status, headers, response]
  end
end

# Usage in Rails config
config.middleware.use SecurityHeaders
```

## Best Practices

### 1. Keep Dependencies Updated

```ruby
# Gemfile
source 'https://rubygems.org'

# Use specific versions
gem 'rails', '~> 7.0.0'
gem 'devise', '~> 4.8.0'

# Regular security updates
bundle audit
bundle update
```

### 2. Environment Variables

```ruby
# config/secrets.yml
development:
  secret_key_base: <%= ENV['SECRET_KEY_BASE'] %>
  database_password: <%= ENV['DATABASE_PASSWORD'] %>

production:
  secret_key_base: <%= ENV['SECRET_KEY_BASE'] %>
  database_password: <%= ENV['DATABASE_PASSWORD'] %>

# config/database.yml
production:
  adapter: postgresql
  database: myapp_production
  username: <%= ENV['DATABASE_USERNAME'] %>
  password: <%= ENV['DATABASE_PASSWORD'] %>
  host: <%= ENV['DATABASE_HOST'] %>
```

### 3. Regular Security Audits

```ruby
# Security audit script
class SecurityAudit
  def self.run
    puts "Running security audit..."
    
    check_default_passwords
    check_insecure_configurations
    check_vulnerable_gems
    check_file_permissions
    
    puts "Security audit completed."
  end
  
  private
  
  def self.check_default_passwords
    puts "Checking for default passwords..."
    
    User.where(email: 'admin@example.com').each do |user|
      if user.valid_password?('password')
        puts "WARNING: User #{user.email} has default password"
      end
    end
  end
  
  def self.check_insecure_configurations
    puts "Checking insecure configurations..."
    
    if Rails.env.production? && Rails.application.config.action_controller.perform_caching == false
      puts "WARNING: Caching is disabled in production"
    end
  end
  
  def self.check_vulnerable_gems
    puts "Checking for vulnerable gems..."
    system('bundle audit')
  end
  
  def self.check_file_permissions
    puts "Checking file permissions..."
    
    sensitive_files = %w[config/secrets.yml config/database.yml]
    
    sensitive_files.each do |file|
      if File.exist?(file)
        mode = File.stat(file).mode
        puts "WARNING: #{file} has permissive permissions: #{mode.to_s(8)}" if mode & 0o077 != 0
      end
    end
  end
end
```

## Practice Exercises

### Exercise 1: Secure Authentication System
Build a secure authentication system with:
- Multi-factor authentication
- Password strength requirements
- Account lockout after failed attempts
- Session management
- Password reset functionality

### Exercise 2: API Security
Implement API security with:
- JWT authentication
- Rate limiting
- API key management
- Request signing
- CORS configuration

### Exercise 3: Data Protection
Create a data protection system with:
- Field-level encryption
- Secure file uploads
- Data masking for logs
- Backup encryption
- Data retention policies

### Exercise 4: Security Monitoring
Build a security monitoring system with:
- Intrusion detection
- Anomaly detection
- Security event logging
- Alert system
- Audit trail

---

**Ready to explore more advanced Ruby topics? Let's continue! 🔒**
