# Web Development Examples
# Demonstrating web development concepts and patterns in Ruby

puts "=== RAILS MVC ARCHITECTURE ==="

# Simulate Rails Model
class User
  attr_accessor :id, :name, :email, :created_at
  
  def initialize(id: nil, name:, email:)
    @id = id
    @name = name
    @email = email
    @created_at = Time.now
    @errors = []
  end
  
  def save
    validate
    return false unless @errors.empty?
    
    @id = rand(1000..9999) unless @id
    puts "User #{@id} saved to database"
    true
  end
  
  def valid?
    validate
    @errors.empty?
  end
  
  def full_name
    @name
  end
  
  def posts
    Post.where(user_id: @id)
  end
  
  private
  
  def validate
    @errors.clear
    @errors << "Name can't be blank" if @name.nil? || @name.strip.empty?
    @errors << "Email is invalid" unless @email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
  end
end

# Simulate Rails Controller
class UsersController
  def initialize
    @users = []
    @next_id = 1
  end
  
  def index
    @users
  end
  
  def show(id)
    @users.find { |user| user.id == id }
  end
  
  def new
    User.new(name: "", email: "")
  end
  
  def create(user_params)
    user = User.new(user_params)
    if user.save
      @users << user
      { success: true, user: user }
    else
      { success: false, errors: user.instance_variable_get(:@errors) }
    end
  end
  
  def update(id, user_params)
    user = show(id)
    if user
      user.name = user_params[:name] if user_params[:name]
      user.email = user_params[:email] if user_params[:email]
      if user.valid?
        puts "User #{id} updated"
        { success: true, user: user }
      else
        { success: false, errors: user.instance_variable_get(:@errors) }
      end
    else
      { success: false, errors: ["User not found"] }
    end
  end
  
  def destroy(id)
    user = show(id)
    if user
      @users.delete(user)
      puts "User #{id} deleted"
      { success: true }
    else
      { success: false, errors: ["User not found"] }
    end
  end
end

# Simulate Rails View
class UserView
  def self.index(users)
    puts "\n=== USERS INDEX ==="
    users.each_with_index do |user, index|
      puts "#{index + 1}. #{user.full_name} (#{user.email})"
    end
    puts "Total: #{users.length} users"
  end
  
  def self.show(user)
    puts "\n=== USER PROFILE ==="
    puts "ID: #{user.id}"
    puts "Name: #{user.full_name}"
    puts "Email: #{user.email}"
    puts "Created: #{user.created_at.strftime('%Y-%m-%d %H:%M')}"
  end
  
  def self.form(action, user = nil)
    puts "\n=== USER FORM ==="
    puts "Action: #{action}"
    puts "Name: #{user&.name || ''}"
    puts "Email: #{user&.email || ''}"
  end
  
  def self.errors(errors)
    puts "\n=== ERRORS ==="
    errors.each { |error| puts "- #{error}" }
  end
end

puts "Rails MVC Example:"
controller = UsersController.new

# Create users
result1 = controller.create(name: "John Doe", email: "john@example.com")
result2 = controller.create(name: "Jane Smith", email: "jane@example.com")
result3 = controller.create(name: "Bob Wilson", email: "bob@example.com")

if result1[:success]
  puts "Created user: #{result1[:user].full_name}"
else
  UserView.errors(result1[:errors])
end

# List users
UserView.index(controller.index)

# Show user
user = controller.show(1)
UserView.show(user) if user

puts "\n=== ACTIVE RECORD PATTERNS ==="

# Simulate ActiveRecord associations
class Post
  attr_accessor :id, :title, :content, :user_id, :status, :created_at
  
  @@posts = []
  
  def initialize(title:, content:, user_id:, status: 'draft')
    @id = @@posts.length + 1
    @title = title
    @content = content
    @user_id = user_id
    @status = status
    @created_at = Time.now
    @@posts << self
  end
  
  def self.all
    @@posts
  end
  
  def self.find(id)
    @@posts.find { |post| post.id == id }
  end
  
  def self.where(conditions)
    @@posts.select do |post|
      conditions.all? { |key, value| post.send(key) == value }
    end
  end
  
  def user
    User.find(@user_id)
  end
  
  def comments
    Comment.where(post_id: @id)
  end
  
  def published?
    @status == 'published'
  end
  
  def word_count
    @content.split(/\s+/).length
  end
  
  def summary(length: 100)
    @content.length > length ? "#{@content[0...length]}..." : @content
  end
end

class Comment
  attr_accessor :id, :content, :user_id, :post_id, :created_at
  
  @@comments = []
  
  def initialize(content:, user_id:, post_id:)
    @id = @@comments.length + 1
    @content = content
    @user_id = user_id
    @post_id = post_id
    @created_at = Time.now
    @@comments << self
  end
  
  def self.where(conditions)
    @@comments.select do |comment|
      conditions.all? { |key, value| comment.send(key) == value }
    end
  end
  
  def user
    User.find(@user_id)
  end
  
  def post
    Post.find(@post_id)
  end
  
  def author_name
    user&.full_name || "Anonymous"
  end
end

puts "ActiveRecord Patterns Example:"

# Create posts
post1 = Post.new(title: "First Post", content: "This is my first post content", user_id: 1, status: 'published')
post2 = Post.new(title: "Second Post", content: "This is my second post content", user_id: 2, status: 'draft')
post3 = Post.new(title: "Third Post", content: "This is my third post content", user_id: 1, status: 'published')

# Create comments
comment1 = Comment.new(content: "Great post!", user_id: 2, post_id: 1)
comment2 = Comment.new(content: "Thanks for sharing", user_id: 3, post_id: 1)
comment3 = Comment.new(content: "Interesting read", user_id: 1, post_id: 3)

puts "\nPosts and their comments:"
Post.all.each do |post|
  puts "\nPost: #{post.title}"
  puts "Author: #{post.user.full_name}"
  puts "Status: #{post.status}"
  puts "Word count: #{post.word_count}"
  puts "Comments: #{post.comments.length}"
  
  post.comments.each do |comment|
    puts "  - #{comment.author_name}: #{comment.content}"
  end
end

puts "\n=== ROUTING SYSTEM ==="

class Router
  def initialize
    @routes = []
  end
  
  def get(path, controller:, action:)
    @routes << { method: 'GET', path: path, controller: controller, action: action }
  end
  
  def post(path, controller:, action:)
    @routes << { method: 'POST', path: path, controller: controller, action: action }
  end
  
  def match(method, path)
    @routes.find do |route|
      route[:method] == method && route[:path] == path
    end
  end
  
  def draw(&block)
    instance_eval(&block)
  end
end

class Request
  attr_reader :method, :path, :params
  
  def initialize(method, path, params = {})
    @method = method
    @path = path
    @params = params
  end
end

class Response
  attr_reader :status, :body
  
  def initialize(status: 200, body: "")
    @status = status
    @body = body
  end
end

# Simulate Rails routing
router = Router.new
router.draw do
  get '/', controller: 'home', action: 'index'
  get '/users', controller: 'users', action: 'index'
  get '/users/:id', controller: 'users', action: 'show'
  post '/users', controller: 'users', action: 'create'
  get '/posts', controller: 'posts', action: 'index'
  get '/posts/:id', controller: 'posts', action: 'show'
end

puts "Routing System Example:"
requests = [
  Request.new('GET', '/'),
  Request.new('GET', '/users'),
  Request.new('GET', '/users/1'),
  Request.new('POST', '/users'),
  Request.new('GET', '/posts'),
  Request.new('GET', '/posts/1'),
  Request.new('GET', '/unknown')
]

requests.each do |request|
  route = router.match(request.method, request.path)
  if route
    puts "#{request.method} #{request.path} -> #{route[:controller]}##{route[:action]}"
  else
    puts "#{request.method} #{request.path} -> No route found"
  end
end

puts "\n=== SINATRA-STYLE WEB APP ==="

class SimpleWebApp
  def initialize
    @routes = {}
  end
  
  def get(path, &block)
    @routes["GET #{path}"] = block
  end
  
  def post(path, &block)
    @routes["POST #{path}"] = block
  end
  
  def handle_request(method, path, params = {})
    route_key = "#{method} #{path}"
    handler = @routes[route_key]
    
    if handler
      instance_exec(params, &handler)
    else
      "404 - Page not found"
    end
  end
end

app = SimpleWebApp.new

# Define routes
app.get('/') do
  "Welcome to my Ruby web app!"
end

app.get('/hello') do
  "Hello, World!"
end

app.get('/hello/:name') do |params|
  "Hello, #{params[:name]}!"
end

app.get('/users') do
  users = [
    { id: 1, name: "John" },
    { id: 2, name: "Jane" }
  ]
  users.map { |u| "#{u[:id]}: #{u[:name]}" }.join(', ')
end

app.post('/users') do |params|
  name = params[:name] || "Anonymous"
  "Created user: #{name}"
end

puts "Sinatra-style Web App Example:"
requests = [
  ['GET', '/'],
  ['GET', '/hello'],
  ['GET', '/hello/Alice'],
  ['GET', '/users'],
  ['POST', '/users', { name: 'Bob' }],
  ['GET', '/nonexistent']
]

requests.each do |method, path, params = {}|
  response = app.handle_request(method, path, params)
  puts "#{method} #{path}: #{response}"
end

puts "\n=== API DEVELOPMENT ==="

class ApiController
  def self.get(path, &block)
    define_method("handle_#{path.gsub('/', '_')}", &block)
  end
  
  def self.post(path, &block)
    define_method("handle_#{path.gsub('/', '_')}", &block)
  end
end

class UsersApi < ApiController
  get '/users' do
    users = [
      { id: 1, name: "John", email: "john@example.com" },
      { id: 2, name: "Jane", email: "jane@example.com" }
    ]
    { users: users }.to_json
  end
  
  get '/users/:id' do |params|
    users = {
      1 => { id: 1, name: "John", email: "john@example.com" },
      2 => { id: 2, name: "Jane", email: "jane@example.com" }
    }
    
    user = users[params[:id].to_i]
    if user
      user.to_json
    else
      { error: "User not found" }.to_json
    end
  end
  
  post '/users' do |params|
    name = params[:name]
    email = params[:email]
    
    if name && email
      new_user = {
        id: 3,
        name: name,
        email: email
      }
      { message: "User created", user: new_user }.to_json
    else
      { error: "Name and email required" }.to_json
    end
  end
end

puts "API Development Example:"
api = UsersApi.new

# Simulate API calls
api_requests = [
  ['GET', '/users', {}],
  ['GET', '/users/1', {}],
  ['GET', '/users/99', {}],
  ['POST', '/users', { name: 'Alice', email: 'alice@example.com' }],
  ['POST', '/users', { name: '', email: '' }]
]

api_requests.each do |method, path, params|
  action = "handle_#{path.gsub('/', '_')}"
  if api.respond_to?(action)
    response = api.send(action, params)
    puts "#{method} #{path}: #{response}"
  else
    puts "#{method} #{path}: Endpoint not found"
  end
end

puts "\n=== MIDDLEWARE EXAMPLE ==="

class Middleware
  def initialize(app)
    @app = app
  end
  
  def call(env)
    @app.call(env)
  end
end

class LoggingMiddleware < Middleware
  def call(env)
    puts "[LOG] #{env[:method]} #{env[:path]}"
    response = @app.call(env)
    puts "[LOG] Response: #{response[:status]}"
    response
  end
end

class AuthenticationMiddleware < Middleware
  def call(env)
    token = env[:headers]['Authorization']
    
    if token == 'valid_token'
      env[:user] = { id: 1, name: 'John' }
      @app.call(env)
    else
      { status: 401, body: 'Unauthorized' }
    end
  end
end

class RateLimitMiddleware < Middleware
  def initialize(app, max_requests: 5)
    super(app)
    @max_requests = max_requests
    @requests = {}
  end
  
  def call(env)
    ip = env[:ip]
    current_time = Time.now.to_i
    
    @requests[ip] ||= []
    @requests[ip] = @requests[ip].select { |time| time > current_time - 60 }
    
    if @requests[ip].length >= @max_requests
      { status: 429, body: 'Rate limit exceeded' }
    else
      @requests[ip] << current_time
      @app.call(env)
    end
  end
end

class SimpleApp
  def call(env)
    { status: 200, body: "Hello, #{env[:user]&.dig(:name) || 'Guest'}!" }
  end
end

puts "Middleware Example:"

# Build middleware stack
app = SimpleApp.new
app = AuthenticationMiddleware.new(app)
app = LoggingMiddleware.new(app)
app = RateLimitMiddleware.new(app, max_requests: 3)

# Simulate requests
middleware_requests = [
  { method: 'GET', path: '/', ip: '127.0.0.1', headers: {} },
  { method: 'GET', path: '/', ip: '127.0.0.1', headers: { 'Authorization' => 'valid_token' } },
  { method: 'GET', path: '/', ip: '127.0.0.1', headers: { 'Authorization' => 'valid_token' } },
  { method: 'GET', path: '/', ip: '127.0.0.1', headers: { 'Authorization' => 'valid_token' } },
  { method: 'GET', path: '/', ip: '127.0.0.1', headers: { 'Authorization' => 'valid_token' } },
]

middleware_requests.each do |env|
  response = app.call(env)
  puts "Response: #{response[:status]} - #{response[:body]}"
  puts "---"
end

puts "\n=== WEB DEVELOPMENT SUMMARY ==="
puts "- MVC Architecture: Models, Views, Controllers"
puts "- ActiveRecord Patterns: Associations, validations, queries"
puts "- Routing System: URL mapping and parameter handling"
puts "- Sinatra-style Apps: Lightweight web applications"
puts "- API Development: RESTful endpoints and JSON responses"
puts "- Middleware: Request/response processing pipeline"
puts "\nAll examples demonstrate modern web development concepts in Ruby!"
