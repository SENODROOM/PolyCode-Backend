# Web Development Examples in Ruby
# Demonstrating web development patterns and frameworks

require 'json'
require 'socket'
require 'webrick'
require 'uri'
require 'net/http'

class WebDevelopmentExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "🌐 Web Development Examples"
    puts "=========================="
    puts "Explore web development patterns and frameworks in Ruby!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Web Development Examples Menu:"
      puts "1. HTTP Server from Scratch"
      puts "2. REST API Development"
      puts "3. WebSockets"
      puts "4. Template Engine"
      puts "5. Middleware"
      puts "6. Session Management"
      puts "7. File Upload Handling"
      puts "8. Authentication System"
      puts "9. Rate Limiting"
      puts "10. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-10): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        http_server_scratch
      when 2
        rest_api_development
      when 3
        websockets
      when 4
        template_engine
      when 5
        middleware
      when 6
        session_management
      when 7
        file_upload_handling
      when 8
        authentication_system
      when 9
        rate_limiting
      when 10
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def http_server_scratch
    puts "\n🌐 Example 1: HTTP Server from Scratch"
    puts "=" * 50
    puts "Building a complete HTTP server using Ruby sockets."
    puts ""
    
    # Basic HTTP server
    puts "🔧 Basic HTTP Server:"
    
    class SimpleHTTPServer
      def initialize(port = 8080)
        @port = port
        @routes = {}
        @server = TCPServer.new('localhost', @port)
      end
      
      def start
        puts "🚀 Server starting on port #{@port}..."
        
        Thread.new do
          while (socket = @server.accept)
            Thread.new do
              handle_request(socket)
            end
          end
        end
      end
      
      def add_route(path, &handler)
        @routes[path] = handler
      end
      
      def stop
        @server.close
      puts "🛑 Server stopped"
      end
      
      private
      
      def handle_request(socket)
        request = socket.gets
        method, path, version = request.split(' ')
        path = path.empty? ? '/' : path
        method = method.empty? ? 'GET' : method
        
        puts "📥 #{method} #{path} #{version}"
        
        if @routes.key?(path)
          response = @routes[path].call(method, parse_query_string(request))
        else
          response = generate_error_response(404, "Not Found")
        end
        
        socket.print(response)
        socket.close
      end
      
      def parse_query_string(request)
        query_start = request.index('?')
        return {} unless query_start
        
        query_string = request[query_start + 1..-1]
        query_string.split('&').each_with_object({}) do |pair|
          key, value = pair.split('=', 2)
          query[key] = URI.decode_www_form_component(value)
        end
      end
      
      def generate_response(status, content, headers = {})
        response_lines = [
          "HTTP/1.1 #{status}",
          "Content-Type: text/html",
          "Content-Length: #{content.length}",
          "Connection: close"
        ]
        
        headers.each do |key, value|
          response_lines.insert(-2, "#{key}: #{value}")
        end
        
        response_lines.join("\r\n") + "\r\n" + content
      end
      
      def generate_error_response(status, message)
        html = <<~HTML
          <!DOCTYPE html>
          <html>
          <head><title>Error #{status}</title></head>
          <body>
            <h1>Error #{status}</h1>
            <p>#{message}</p>
          </body>
          </html>
        HTML
        
        generate_response(status.to_s, html)
      end
    end
    
    # Create and start server
    server = SimpleHTTPServer.new
    
    # Add routes
    server.add_route('/') do |method, params|
      generate_response(200, <<~HTML)
        <!DOCTYPE html>
        <html>
          <head><title>Ruby HTTP Server</title></head>
          <body>
            <h1>Welcome to Ruby HTTP Server!</h1>
            <p>Method: #{method}</p>
            <p>Request time: #{Time.now}</p>
          </body>
        </html>
        HTML
      )
    end
    
    server.add_route('/api/data') do |method, params|
      data = {
        message: "Hello from API",
        timestamp: Time.now.iso8601,
        method: method
      }
      
      generate_response(200, data.to_json, {
        'Content-Type' => 'application/json'
      })
    end
    
    server.add_route('/time') do |method, params|
      generate_response(200, "Current time: #{Time.now}")
    end
    
    # Start server in a separate thread
    server_thread = Thread.new { server.start }
    
    puts "Server is running..."
    puts "Try these URLs:"
    puts "  http://localhost:8080/"
    puts "  http://localhost:8080/api/data"
    puts "  http://localhost:8080/time"
    puts "  http://localhost:8080/nonexistent (404)"
    puts "\nPress Ctrl+C to stop the server"
    
    begin
      sleep 30  # Run for 30 seconds
    rescue Interrupt
      puts "\n🛑 Stopping server..."
      server.stop
      server_thread.join
    end
    
    @examples << {
      title: "HTTP Server from Scratch",
      description: "Complete HTTP server implementation using Ruby sockets",
      code: <<~RUBY
        class SimpleHTTPServer
          def initialize(port = 8080)
            @server = TCPServer.new('localhost', port)
          end
          
          def handle_request(socket)
            request = socket.gets
            method, path = request.split(' ')
            response = route_request(method, path)
            socket.print(response)
          end
        end
      RUBY
    }
    
    puts "\n✅ HTTP Server from Scratch example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def rest_api_development
    puts "\n🔌 Example 2: REST API Development"
    puts "=" * 50
    puts "Building a RESTful API with proper HTTP methods."
    puts ""
    
    # REST API framework
    puts "🔌 REST API Framework:"
    
    class RestAPI
      def initialize
        @routes = {}
        @middleware = []
      end
      
      def add_route(method, path, &handler)
        @routes["#{method.upcase} #{path}"] = handler
      end
      
      def use_middleware(&middleware)
        @middleware << middleware
      end
      
      def handle_request(method, path, body = nil, headers = {})
        route_key = "#{method.upcase} #{path}"
        
        # Apply middleware
        @middleware.each do |middleware|
          result = middleware.call(method, path, body, headers)
          case result
          when Array
            method, path, result[0], result[1]
          when Hash
            method, path, result[:body], result[:headers]
          else
            method, path, result, headers
          end
        end
        
        # Find route
        if @routes.key?(route_key)
          response = @routes[route_key].call(method, path, body, headers)
        else
          response = error_response(404, "Route not found")
        end
        
        response
      end
      
      def get(path, &block)
        add_route('GET', path, &block)
      end
      
      def post(path, &block)
        add_route('POST', path, &block)
      end
      
      def put(path, &block)
        add_route('PUT', path, &block)
      end
      
      def delete(path, &block)
        add_route('DELETE', path, &block)
      end
      
      private
      
      def success_response(data, status = 200)
        {
          status: status,
          headers: { 'Content-Type' => 'application/json' },
          body: data.to_json
        }
      end
      
      def error_response(status, message)
        {
          status: status,
          headers: { 'Content-Type' => 'application/json' },
          body: { error: message }
        }
      end
    end
    
    # JSON middleware
    puts "🔧 JSON Middleware:"
    
    class JSONMiddleware
      def self.new(app)
        lambda do |method, path, body, headers|
          request = {
            method: method,
            path: path,
            headers: headers,
            body: body
          }
          
          # Parse JSON body
          if headers['Content-Type']&.include?('application/json')
            begin
              request[:json] = JSON.parse(body)
            rescue JSON::ParserError
              request[:json] = nil
            end
          end
          
          result = app.call(method, path, request[:json] || body, request[:headers])
          result
        end
      end
    end
    
    # CORS middleware
    puts "🌐 CORS Middleware:"
    
    class CORSMiddleware
      def self.new(app)
        lambda do |method, path, body, headers|
          # Add CORS headers
          cors_headers = {
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            'Access-Control-Max-Age' => '86400'
          }
          
          result = app.call(method, path, body, headers)
          
          # Merge CORS headers
          if result.is_a?(Hash)
            result[:headers] = cors_headers.merge(result[:headers])
          else
            status, result_headers, response_body = result
            [status, cors_headers.merge(result_headers), response_body]
          end
          
          result
        end
      end
    end
    
    # Rate limiting middleware
    puts "⏱️ Rate Limiting Middleware:"
    
    class RateLimitMiddleware
      def initialize(app, limit = 100, window = 60)
        @app = app
        @limit = limit
        @window = window
        @requests = {}
      end
      
      def call(method, path, body, headers)
        client_ip = headers['X-Real-IP'] || 'unknown'
        now = Time.now
        
        # Clean old requests
        @requests.delete_if { |ip, time| time < now - @window }
        
        # Check rate limit
        request_count = (@requests[client_ip] ||= []).length
        
        if request_count >= @limit
          error_response = {
            status: 429,
            headers: { 'Content-Type' => 'application/json' },
            body: { error: 'Rate limit exceeded' }
          }
          error_response
        else
          @requests[client_ip] ||= []
          @requests[client_ip] << now
          @app.call(method, path, body, headers)
        end
      end
    end
    
    # Create API with middleware
    api = RestAPI.new
    api.use_middleware(JSONMiddleware.new)
    api.use_middleware(CORSMiddleware.new)
    api.use_middleware(RateLimitMiddleware.new)
    
    # Add routes
    api.get('/users') do |method, path, body, headers|
      users = [
        { id: 1, name: 'Alice', email: 'alice@test.com' },
        { id: 2, name: 'Bob', email: 'bob@test.com' }
      ]
      api.success_response(users)
    end
    
    api.post('/users') do |method, path, body, headers|
      # Create new user
      user_data = headers['Content-Type']&.include?('application/json') ? JSON.parse(body) : {}
      new_user = {
        id: rand(1000..9999),
        name: user_data['name'] || 'Unknown',
        email: user_data['email'] || 'unknown@example.com'
      }
      api.success_response(new_user, 201)
    end
    
    api.put('/users/:id') do |method, path, body, headers|
      id = path.split('/').last.to_i
      user_data = headers['Content-Type']&.include?('application/json') ? JSON.parse(body) : {}
      
      users = [
        { id: 1, name: 'Alice', email: 'alice@test.com' },
        { id: 2, name: 'Bob', email: 'bob@test.com' }
      ]
      
      user = users.find { |u| u[:id] == id }
      if user
        updated_user = user.merge(user_data)
        api.success_response(updated_user)
      else
        api.error_response(404, 'User not found')
      end
    end
    
    api.delete('/users/:id') do |method, path, body, headers|
      id = path.split('/').last.to_i
      users = [
        { id: 1, name: 'Alice', email: 'alice@test.com' },
        { id: 2, name: 'Bob', email: 'bob@test.com' }
      ]
      
      users.reject! { |u| u[:id] == id }
      api.success_response({ message: "User #{id} deleted" })
    end
    
    puts "REST API with middleware configured!"
    puts "Available endpoints: GET /users, POST /users, PUT /users/:id, DELETE /users/:id"
    
    @examples << {
      title: "REST API Development",
      description: "Complete RESTful API with middleware",
      code: <<~RUBY
        class RestAPI
          def get(path, &block)
            add_route('GET', path, &block)
          end
          
          def post(path, &block)
            add_route('POST', path, &block)
          end
        end
      RUBY
    }
    
    puts "\n✅ REST API Development example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def websockets
    puts "\n🔌 Example 3: WebSockets"
    puts "=" * 50
    puts "Real-time communication with WebSocket protocol."
    puts ""
    
    # WebSocket server
    puts "🔌 WebSocket Server:"
    
    class WebSocketServer
      def initialize(port = 8081)
        @port = port
        @server = TCPServer.new('localhost', @port)
        @clients = []
      end
      
      def start
        puts "🚀 WebSocket server starting on port #{@port}..."
        
        Thread.new do
          while (socket = @server.accept)
            Thread.new do
              handle_websocket_connection(socket)
            end
          end
        end
      end
      
      def stop
        @server.close
        puts "🛑 WebSocket server stopped"
      end
      
      private
      
      def handle_websocket_connection(socket)
        # WebSocket handshake
        request = socket.gets
        if request.include?('Upgrade: websocket')
          key = Digest::SHA1.base64digest('258EAFA5-E914-47DA-95CA-C5AB0DC85B11')
          accept = request.match(/Sec-WebSocket-Key: (.+)/)[1]
          
          response = [
            "HTTP/1.1 101 Switching Protocols",
            "Upgrade: websocket",
            "Connection: Upgrade",
            "Sec-WebSocket-Accept: #{accept}",
            "Sec-WebSocket-Key: #{key}",
            "WebSocket-Origin: http://localhost:#{8080}"
          ].join("\r\n") + "\r\n"
          
          socket.print(response)
          
          # WebSocket communication
          handle_websocket_messages(socket)
        else
          socket.close
        end
      end
      
      def handle_websocket_messages(socket)
        loop do
          message = socket.gets
          break if message.nil?
          
          # Parse WebSocket frame (simplified)
          if message.start_with?("\x81")
            payload_length = message[1].ord
            mask = message[2..5]
            payload = message[6..payload_length]
            
            decoded_payload = payload.bytes.each_with_index.map { |byte, i| byte ^ mask[i % 4] }.pack('C*')
            
            # Echo back the message
            response = "\x81" + [payload_length].pack('C*') + mask + decoded_payload
            socket.print(response)
            
            puts "WebSocket message received and echoed: #{decoded_payload}"
          end
        end
      end
      
      def broadcast(message)
        @clients.each do |client|
          begin
            payload_length = message.bytes.length
            mask = SecureRandom.random_bytes(4)
            encoded_message = "\x81" + [payload_length].pack('C*') + mask + message
            client.print(encoded_message)
          rescue => e
            puts "Broadcast error: #{e.message}"
          end
        end
      end
    end
    
    # WebSocket client
    puts "🔌 WebSocket Client:"
    
    class WebSocketClient
      def initialize(url)
        @url = url
        @socket = nil
      end
      
      def connect
        uri = URI.parse(@url)
        @socket = TCPSocket.new(uri.host, uri.port || 80)
        
        # WebSocket handshake
        key = SecureRandom.base64(16)
        handshake = [
          "GET #{uri.path} HTTP/1.1",
          "Host: #{uri.host}",
          "Upgrade: websocket",
          "Connection: Upgrade",
          "Sec-WebSocket-Key: #{key}",
          "Sec-WebSocket-Version: 13",
          "Origin: http://#{uri.host}"
        ].join("\r\n") + "\r\n"
        
        @socket.print(handshake)
        
        # Read handshake response
        response = @socket.gets
        puts "WebSocket handshake response: #{response}"
        
        if response.include?('101 Switching Protocols')
          puts "WebSocket connection established!"
          
          # Start message loop
          message_loop
        else
          puts "WebSocket handshake failed"
          @socket.close
        end
      end
      
      def message_loop
        loop do
          message = receive_message
          break if message.nil?
          
          puts "Received: #{message}"
          send_message("Echo: #{message}")
        end
      end
      
      def send_message(message)
        payload_length = message.bytes.length
        mask = SecureRandom.random_bytes(4)
        encoded_message = "\x81" + [payload_length].pack('C*') + mask + message
        @socket.print(encoded_message)
      end
      
      def receive_message
        message = @socket.gets
        if message.start_with?("\x81")
          payload_length = message[1].ord
          mask = message[2..5]
          payload = message[6..payload_length]
          
          decoded_payload = payload.bytes.each_with_index.map { |byte, i| byte ^ mask[i % 4] }.pack('C*')
          decoded_payload
        else
          message
        end
      end
    end
    
    # Start WebSocket server
    ws_server = WebSocketServer.new
    server_thread = Thread.new { ws_server.start }
    
    puts "WebSocket server running on ws://localhost:8081"
    puts "Connect with a WebSocket client to test"
    
    begin
      sleep 30  # Run for 30 seconds
    rescue Interrupt
      puts "\n🛑 Stopping WebSocket server..."
      ws_server.stop
      server_thread.join
    end
    
    @examples << {
      title: "WebSockets",
      description: "Real-time communication with WebSocket protocol",
      code: <<~RUBY
        class WebSocketServer
          def handle_websocket_connection(socket)
            # WebSocket handshake and message handling
          end
        end
      RUBY
    }
    
    puts "\n✅ WebSockets example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def template_engine
    puts "\n🔌 Example 4: Template Engine"
    puts "=" * 50
    puts "Building a template engine for dynamic HTML generation."
    puts ""
    
    # Template engine
    puts "🔌 Template Engine:"
    
    class TemplateEngine
      def initialize(template_path = nil)
        @template_path = template_path
        @templates = {}
        load_templates if @template_path
      end
      
      def load_templates
        Dir.glob("#{@template_path}/*.html").each do |file|
          template_name = File.basename(file, '.html')
          @templates[template_name] = File.read(file)
        end
      end
      
      def render(template_name, variables = {})
        template = @templates["#{template_name}.html"]
        variables.each do |key, value|
          template = template.gsub(/\{\{\s*#{key}\s*\}\}/, value.to_s)
        end
        template
      end
      
      def render_file(template_name, variables, output_path)
        content = render(template_name, variables)
        File.write(output_path, content)
        output_path
      end
      
      def render_string(template_string, variables = {})
        variables.each do |key, value|
          template_string = template_string.gsub(/\{\{\s*#{key}\s*\}\}/, value.to_s)
        end
        template_string
      end
    end
    
    # Example templates
    puts "📄 Example Templates:"
    
    # Create templates directory
    Dir.mkdir('templates') unless Dir.exist?('templates')
    
    # Base template
    base_template = <<~HTML
      <!DOCTYPE html>
      <html>
      <head>
        <title>{{title}}</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 40px; }
          .container { max-width: 800px; margin: 0 auto; }
          .header { background: #f4f4f4; color: white; padding: 20px; text-align: center; }
          .content { padding: 20px; }
          .footer { background: #f4f4f4; color: white; padding: 20px; text-align: center; margin-top: 20px; }
        </style>
      </head>
      <body>
        <div class="container">
          <div class="header">
            <h1>{{title}}</h1>
          </div>
          <div class="content">
            {{content}}
          </div>
          <div class="footer">
            <p>Generated at {{timestamp}}</p>
          </div>
        </div>
      </body>
      </html>
    HTML
    
    File.write('templates/base.html', base_template)
    
    # User profile template
    profile_template = <<~HTML
      <!DOCTYPE html>
      <html>
      <head>
        <title>{{name}}'s Profile</title>
        <style>
          .profile { text-align: center; max-width: 600px; margin: 50px auto; }
          .avatar { width: 100px; height: 100px; border-radius: 50%; margin-bottom: 20px; }
          .info { text-align: left; }
        </style>
      </head>
      <body>
        <div class="profile">
          <img src="{{avatar_url}}" alt="{{name}}" class="avatar">
          <h2>{{name}}</h2>
          <div class="info">
            <p><strong>Email:</strong> {{email}}</p>
            <p><strong>Age:</strong> {{age}}</p>
            <p><strong>Location:</strong> {{location}}</p>
            <p><strong>Bio:</strong> {{bio}}</p>
          </div>
        </div>
      </body>
      </html>
    HTML
    
    File.write('templates/profile.html', profile_template)
    
    # Use template engine
    engine = TemplateEngine.new('templates')
    
    # Render base template
    base_vars = {
      title: "Welcome",
      content: "This is a dynamic website built with Ruby!",
      timestamp: Time.now.strftime("%Y-%m-%d %H:%M:%S")
    }
    
    puts "Base template:"
    puts engine.render('base', base_vars)
    
    # Render profile template
    profile_vars = {
      name: "Alice Johnson",
      email: "alice@example.com",
      age: 30,
      location: "New York",
      bio: "Ruby developer with 5+ years of experience",
      avatar_url: "https://via.placeholder.com/100"
    }
    
    puts "\nProfile template:"
    puts engine.render('profile', profile_vars)
    
    # Save rendered templates
    engine.render_file('base', base_vars, 'output/index.html')
    engine.render_file('profile', profile_vars, 'output/profile.html')
    
    puts "Templates saved to output/ directory"
    
    @examples << {
      title: "Template Engine",
      description: "Template engine for dynamic HTML generation",
      code: <<~RUBY
        class TemplateEngine
          def render(template_name, variables = {})
            template = @templates["#{template_name}.html"]
            variables.each do |key, value|
              template = template.gsub(/\{\{\s*#{key}\s*\}\}/, value.to_s)
            end
            template
          end
        end
      RUBY
    }
    
    puts "\n✅ Template Engine example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def middleware
    puts "\n🔧 Example 5: Middleware"
    puts "=" * 50
    puts "Creating composable middleware for web applications."
    puts ""
    
    # Logging middleware
    puts "📝 Logging Middleware:"
    
    class LoggingMiddleware
      def initialize(app, logger = nil)
        @app = app
        @logger = logger || Logger.new(STDOUT)
      end
      
      def call(env)
        start_time = Time.now
        
        status, headers, response = @app.call(env)
        
        end_time = Time.now
        duration = end_time - start_time
        
        @logger.info("#{env['REQUEST_METHOD']} #{env['PATH_INFO']} - #{status} - #{duration.round(4)}s")
        
        [status, headers, response]
      end
    end
    
    # Authentication middleware
    puts "🔐 Authentication Middleware:"
    
    class AuthMiddleware
      def initialize(app, users = {})
        @app = app
        @users = users
      end
      
      def call(env)
        # Check for Authorization header
        auth_header = env['HTTP_AUTHORIZATION']
        
        if auth_header
          # Basic auth: username:password
          auth_type, auth_string = auth_header.split(' ', 2)
          
          if auth_type == 'Basic'
            username, password = Base64.decode64(auth_string).split(':', 2)
            if @users[username] == password
              env['AUTH_USER'] = username
              # Continue to app
              @app.call(env)
            else
              unauthorized_response
            end
          else
            unauthorized_response
          end
        else
          unauthorized_response
        end
      end
      
      private
      
      def unauthorized_response
        [401, {'WWW-Authenticate' => 'Basic realm="Secure Area"'}, 'Unauthorized']
      end
    end
    
    # Request ID middleware
    puts "🆔 Request ID Middleware:"
    
    class RequestIDMiddleware
      def initialize(app)
        @app = app
        @counter = 0
      end
      
      def call(env)
        @counter += 1
        env['HTTP_X_REQUEST_ID'] = "req-#{@counter}"
        
        @app.call(env)
      end
    end
    
    # CORS middleware
    puts "🌐 CORS Middleware:"
    
    class CORSMiddleware
      def initialize(app, origins = ['*'])
        @app = app
        @origins = origins
      end
      
      def call(env)
        status, headers, response = @app.call(env)
        
        origin = env['HTTP_ORIGIN']
        
        if @origins.include?(origin) || @origins.include?('*')
          cors_headers = {
            'Access-Control-Allow-Origin' => origin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            'Access-Control-Max-Age' => '86400'
          }
          
          if response.is_a?(Hash)
            [status, headers.merge(cors_headers), response]
          else
            status, response_headers, response_body = response
            [status, cors_headers.merge(response_headers), response_body]
          end
        else
          [403, {'Content-Type' => 'text/plain'}, 'CORS policy violation']
        end
      end
    end
    
    # Create sample app with middleware
    puts "🔧 Sample App with Middleware:"
    
    class SampleApp
      def call(env)
        case env['PATH_INFO']
        when '/'
          [200, {'Content-Type' => 'text/html'}, 'Hello from middleware-protected app!']
        when '/api/data'
          [200, {'Content-Type' => 'application/json'}, {'message' => 'API data', 'timestamp' => Time.now.iso8601}]
        else
          [404, {'Content-Type' => 'text/plain'}, 'Not Found']
        end
      end
    end
    
    # Chain middleware
    logging_middleware = LoggingMiddleware.new
    auth_middleware = AuthMiddleware.new(SampleApp.new)
    cors_middleware = CORSMiddleware.new(auth_middleware)
    
    puts "Middleware chain configured!"
    puts "Try these URLs:"
    puts "  http://localhost:8080/ (protected)"
    puts "  http://localhost:8080/api/data (protected)"
    
    @examples << {
      title: "Middleware",
      description: "Composable middleware for web applications",
      code: <<~RUBY
        class LoggingMiddleware
          def call(env)
            start_time = Time.now
            result = @app.call(env)
            end_time = Time.now
            @logger.info("Request took #{end_time - start_time}s")
            result
          end
        end
      RUBY
    }
    
    puts "\n✅ Middleware example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Web Development Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
      puts "   Key features: #{example[:code].split("\n").first(3)}..."
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate different aspects of web development in Ruby!"
  end
end

# Main execution
if __FILE__ == $0
  examples = WebDevelopmentExamples.new
  examples.start_examples
end
