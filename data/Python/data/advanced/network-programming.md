# Python Network Programming

## Socket Programming Basics

### TCP Server and Client
```python
import socket
import threading
import time

def tcp_server(host='localhost', port=8080):
    """Simple TCP server."""
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    
    try:
        server_socket.bind((host, port))
        server_socket.listen(5)
        print(f"Server listening on {host}:{port}")
        
        while True:
            client_socket, address = server_socket.accept()
            print(f"Connection from {address}")
            
            # Handle client in separate thread
            client_thread = threading.Thread(
                target=handle_client,
                args=(client_socket, address)
            )
            client_thread.start()
    
    except Exception as e:
        print(f"Server error: {e}")
    finally:
        server_socket.close()

def handle_client(client_socket, address):
    """Handle individual client connection."""
    try:
        while True:
            data = client_socket.recv(1024)
            if not data:
                break
            
            message = data.decode('utf-8')
            print(f"Received from {address}: {message}")
            
            # Echo back to client
            response = f"Echo: {message}"
            client_socket.send(response.encode('utf-8'))
    
    except Exception as e:
        print(f"Error handling client {address}: {e}")
    finally:
        client_socket.close()
        print(f"Connection closed for {address}")

def tcp_client(host='localhost', port=8080):
    """Simple TCP client."""
    client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    
    try:
        client_socket.connect((host, port))
        print(f"Connected to {host}:{port}")
        
        messages = ["Hello, Server!", "How are you?", "Goodbye!"]
        
        for message in messages:
            client_socket.send(message.encode('utf-8'))
            
            response = client_socket.recv(1024)
            print(f"Server response: {response.decode('utf-8')}")
            time.sleep(0.5)
    
    except Exception as e:
        print(f"Client error: {e}")
    finally:
        client_socket.close()

def run_tcp_example():
    """Run TCP server and client example."""
    import threading
    
    # Start server in separate thread
    server_thread = threading.Thread(target=tcp_server)
    server_thread.daemon = True
    server_thread.start()
    
    # Give server time to start
    time.sleep(1)
    
    # Run client (in main thread)
    tcp_client()
    
    # Wait a bit for server to handle connections
    time.sleep(2)

if __name__ == "__main__":
    run_tcp_example()
```

### UDP Server and Client
```python
import socket
import threading
import time

def udp_server(host='localhost', port=8081):
    """Simple UDP server."""
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    
    try:
        server_socket.bind((host, port))
        print(f"UDP server listening on {host}:{port}")
        
        while True:
            data, address = server_socket.recvfrom(1024)
            message = data.decode('utf-8')
            print(f"Received from {address}: {message}")
            
            # Echo back to client
            response = f"UDP Echo: {message}"
            server_socket.sendto(response.encode('utf-8'), address)
    
    except Exception as e:
        print(f"UDP server error: {e}")
    finally:
        server_socket.close()

def udp_client(host='localhost', port=8081):
    """Simple UDP client."""
    client_socket = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    
    try:
        messages = ["Hello, UDP Server!", "How are you?", "Goodbye!"]
        
        for message in messages:
            client_socket.sendto(message.encode('utf-8'), (host, port))
            
            response, _ = client_socket.recvfrom(1024)
            print(f"Server response: {response.decode('utf-8')}")
            time.sleep(0.5)
    
    except Exception as e:
        print(f"UDP client error: {e}")
    finally:
        client_socket.close()

def run_udp_example():
    """Run UDP server and client example."""
    import threading
    
    # Start server in separate thread
    server_thread = threading.Thread(target=udp_server)
    server_thread.daemon = True
    server_thread.start()
    
    # Give server time to start
    time.sleep(1)
    
    # Run client (in main thread)
    udp_client()
    
    # Wait a bit for server to handle connections
    time.sleep(2)

if __name__ == "__main__":
    run_udp_example()
```

## HTTP Programming

### HTTP Server with sockets
```python
import socket
import threading

class HTTPServer:
    """Simple HTTP server using sockets."""
    
    def __init__(self, host='localhost', port=8082):
        self.host = host
        self.port = port
        self.server_socket = None
    
    def start(self):
        """Start the HTTP server."""
        self.server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        
        try:
            self.server_socket.bind((self.host, self.port))
            self.server_socket.listen(5)
            print(f"HTTP server listening on {self.host}:{self.port}")
            
            while True:
                client_socket, address = self.server_socket.accept()
                print(f"HTTP request from {address}")
                
                # Handle request in separate thread
                client_thread = threading.Thread(
                    target=self.handle_request,
                    args=(client_socket, address)
                )
                client_thread.start()
        
        except Exception as e:
            print(f"HTTP server error: {e}")
    
    def handle_request(self, client_socket, address):
        """Handle individual HTTP request."""
        try:
            request_data = client_socket.recv(4096).decode('utf-8')
            
            # Parse HTTP request
            lines = request_data.split('\r\n')
            if not lines:
                return
            
            request_line = lines[0]
            method, path, version = request_line.split()
            
            # Generate response
            if path == '/':
                response_body = self.generate_homepage()
                status = "200 OK"
            elif path == '/about':
                response_body = self.generate_about_page()
                status = "200 OK"
            else:
                response_body = self.generate_404_page()
                status = "404 Not Found"
            
            response = self.generate_http_response(status, response_body)
            client_socket.send(response.encode('utf-8'))
        
        except Exception as e:
            print(f"Error handling request from {address}: {e}")
        finally:
            client_socket.close()
    
    def generate_http_response(self, status, body):
        """Generate HTTP response."""
        response = f"HTTP/1.1 {status}\r\n"
        response += "Content-Type: text/html\r\n"
        response += f"Content-Length: {len(body)}\r\n"
        response += "Connection: close\r\n"
        response += "\r\n"
        response += body
        return response
    
    def generate_homepage(self):
        """Generate homepage HTML."""
        return """
<!DOCTYPE html>
<html>
<head>
    <title>Python HTTP Server</title>
</head>
<body>
    <h1>Welcome to Python HTTP Server</h1>
    <p>This is a simple HTTP server implemented with Python sockets.</p>
    <ul>
        <li><a href="/about">About</a></li>
    </ul>
</body>
</html>
        """.strip()
    
    def generate_about_page(self):
        """Generate about page HTML."""
        return """
<!DOCTYPE html>
<html>
<head>
    <title>About - Python HTTP Server</title>
</head>
<body>
    <h1>About This Server</h1>
    <p>This HTTP server demonstrates basic socket programming in Python.</p>
    <p>Features:</p>
    <ul>
        <li>Basic HTTP request handling</li>
        <li>Multi-threaded request processing</li>
        <li>Simple routing</li>
    </ul>
    <a href="/">Back to Home</a>
</body>
</html>
        """.strip()
    
    def generate_404_page(self):
        """Generate 404 page HTML."""
        return """
<!DOCTYPE html>
<html>
<head>
    <title>404 Not Found</title>
</head>
<body>
    <h1>404 Not Found</h1>
    <p>The requested resource was not found on this server.</p>
    <a href="/">Back to Home</a>
</body>
</html>
        """.strip()

def run_http_server():
    """Run the custom HTTP server."""
    server = HTTPServer()
    
    try:
        server.start()
    except KeyboardInterrupt:
        print("\nShutting down server...")
    finally:
        if server.server_socket:
            server.server_socket.close()

if __name__ == "__main__":
    run_http_server()
```

### HTTP Client with sockets
```python
import socket

class HTTPClient:
    """Simple HTTP client using sockets."""
    
    def __init__(self):
        self.socket = None
    
    def get(self, url, host='localhost', port=8082):
        """Perform HTTP GET request."""
        try:
            # Create socket
            self.socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            
            # Connect to server
            self.socket.connect((host, port))
            
            # Send HTTP GET request
            request = f"GET {url} HTTP/1.1\r\n"
            request += f"Host: {host}:{port}\r\n"
            request += "Connection: close\r\n"
            request += "\r\n"
            
            self.socket.send(request.encode('utf-8'))
            
            # Receive response
            response = b""
            while True:
                chunk = self.socket.recv(4096)
                if not chunk:
                    break
                response += chunk
            
            # Parse response
            response_str = response.decode('utf-8')
            headers, body = response_str.split('\r\n\r\n', 1)
            status_line = headers.split('\n')[0]
            
            print(f"Status: {status_line}")
            print(f"Headers: {headers}")
            print(f"Body: {body}")
            
            return body
        
        except Exception as e:
            print(f"HTTP client error: {e}")
        finally:
            if self.socket:
                self.socket.close()
    
    def post(self, url, data, host='localhost', port=8082):
        """Perform HTTP POST request."""
        try:
            # Create socket
            self.socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            
            # Connect to server
            self.socket.connect((host, port))
            
            # Convert data to JSON
            import json
            json_data = json.dumps(data)
            
            # Send HTTP POST request
            request = f"POST {url} HTTP/1.1\r\n"
            request += f"Host: {host}:{port}\r\n"
            request += "Content-Type: application/json\r\n"
            request += f"Content-Length: {len(json_data)}\r\n"
            request += "Connection: close\r\n"
            request += "\r\n"
            request += json_data
            
            self.socket.send(request.encode('utf-8'))
            
            # Receive response
            response = b""
            while True:
                chunk = self.socket.recv(4096)
                if not chunk:
                    break
                response += chunk
            
            # Parse response
            response_str = response.decode('utf-8')
            headers, body = response_str.split('\r\n\r\n', 1)
            status_line = headers.split('\n')[0]
            
            print(f"Status: {status_line}")
            print(f"Headers: {headers}")
            print(f"Body: {body}")
            
            return body
        
        except Exception as e:
            print(f"HTTP client error: {e}")
        finally:
            if self.socket:
                self.socket.close()

def run_http_client_example():
    """Run HTTP client example."""
    client = HTTPClient()
    
    print("GET request:")
    client.get("/")
    
    print("\nGET request to /about:")
    client.get("/about")
    
    print("\nPOST request:")
    post_data = {"name": "John", "age": 30}
    client.post("/submit", post_data)

if __name__ == "__main__":
    # Run server first in separate terminal
    print("Make sure to run the HTTP server first!")
    print("Then run this client example.")
    
    # Uncomment to test client (requires server to be running)
    # run_http_client_example()
```

## Advanced Network Programming

### Non-blocking Sockets
```python
import socket
import select
import time

class NonBlockingServer:
    """Non-blocking server using select."""
    
    def __init__(self, host='localhost', port=8083):
        self.host = host
        self.port = port
        self.server_socket = None
        self.clients = []
        self.running = True
    
    def start(self):
        """Start the non-blocking server."""
        self.server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        self.server_socket.setblocking(False)
        
        self.server_socket.bind((self.host, self.port))
        self.server_socket.listen(5)
        
        print(f"Non-blocking server listening on {self.host}:{self.port}")
        
        self.server_socket.setblocking(False)
        self.clients.append(self.server_socket)
        
        try:
            while self.running:
                # Use select to monitor sockets
                read_sockets, write_sockets, error_sockets = select.select(
                    self.clients, self.clients, self.clients, 1.0
                )
                
                # Handle new connections
                for sock in read_sockets:
                    if sock is self.server_socket:
                        client_socket, address = sock.accept()
                        client_socket.setblocking(False)
                        self.clients.append(client_socket)
                        print(f"New connection from {address}")
                    else:
                        data = sock.recv(1024)
                        if data:
                            message = data.decode('utf-8')
                            print(f"Received: {message}")
                            
                            # Echo back
                            response = f"Echo: {message}"
                            sock.send(response.encode('utf-8'))
                        else:
                            sock.close()
                            self.clients.remove(sock)
                
                # Handle errors
                for sock in error_sockets:
                    if sock in self.clients:
                        self.clients.remove(sock)
                        sock.close()
                        print(f"Client disconnected")
        
        except KeyboardInterrupt:
            print("\nShutting down server...")
            self.running = False
        finally:
            for sock in self.clients:
                sock.close()
            if self.server_socket:
                self.server_socket.close()

def run_non_blocking_server():
    """Run the non-blocking server."""
    server = NonBlockingServer()
    server.start()

if __name__ == "__main__":
    run_non_blocking_server()
```

### Socket Options and Configuration
```python
import socket
import struct

def demonstrate_socket_options():
    """Demonstrate various socket options."""
    
    # Create socket
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    
    print("Socket options:")
    
    # SO_REUSEADDR
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    reuse_addr = sock.getsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR)
    print(f"SO_REUSEADDR: {reuse_addr}")
    
    # TCP_NODELAY
    sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1)
    tcp_nodelay = sock.getsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY)
    print(f"TCP_NODELAY: {tcp_nodelay}")
    
    # SO_KEEPALIVE
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
    keepalive = sock.getsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE)
    print(f"SO_KEEPALIVE: {keepalive}")
    
    # SO_SNDBUF
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_SNDBUF, 8192)
    sndbuf = sock.getsockopt(socket.SOL_SOCKET, socket.SO_SNDBUF)
    print(f"SO_SNDBUF: {sndbuf}")
    
    # SO_RCVBUF
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_RCVBUF, 8192)
    rcvbuf = sock.getsockopt(socket.SOL_SOCKET, socket.SO_RCVBUF)
    print(f"SO_RCVBUF: {rcvbuf}")
    
    # Socket timeout
    sock.settimeout(5.0)
    timeout = sock.gettimeout()
    print(f"Timeout: {timeout}")
    
    sock.close()

def socket_timeout_example():
    """Demonstrate socket timeout handling."""
    
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    sock.settimeout(2.0)  # 2 second timeout
    
    try:
        # Try to connect to non-existent server
        sock.connect(('nonexistent-host', 80))
    except socket.timeout:
        print("Connection timed out as expected")
    except Exception as e:
        print(f"Other error: {e}")
    finally:
        sock.close()

def socket_buffer_size_example():
    """Demonstrate buffer size effects."""
    
    # Small buffer size
    small_buffer_size = 1024
    large_buffer_size = 65536
    
    def test_buffer_size(buffer_size):
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.setsockopt(socket.SOL_SOCKET, socket.SO_SNDBUF, buffer_size)
        sock.setsockopt(socket.SOL_SOCKET, socket.SO_RCVBUF, buffer_size)
        
        print(f"Buffer size: {buffer_size}")
        print(f"Send buffer: {sock.getsockopt(socket.SOL_SOCKET, socket.SO_SNDBUF)}")
        print(f"Receive buffer: {sock.getsockopt(socket.SOL_SOCKET, socket.SO_RCVBUF)}")
        
        sock.close()
    
    test_buffer_size(small_buffer_size)
    test_buffer_size(large_buffer_size)

if __name__ == "__main__":
    print("=== Socket Options ===")
    demonstrate_socket_options()
    
    print("\n=== Socket Timeout ===")
    socket_timeout_example()
    
    print("\n=== Buffer Size ===")
    socket_buffer_size_example()
```

## Real-world Applications

### Simple Chat Server
```python
import socket
import threading
import json
import time

class ChatServer:
    """Simple multi-client chat server."""
    
    def __init__(self, host='localhost', port=8084):
        self.host = host
        self.port = port
        self.server_socket = None
        self.clients = []
        self.nicknames = {}
    
    def start(self):
        """Start the chat server."""
        self.server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        
        try:
            self.server_socket.bind((self.host, self.port))
            self.server_socket.listen(5)
            print(f"Chat server listening on {self.host}:{port}")
            
            while True:
                client_socket, address = self.server_socket.accept()
                print(f"New connection from {address}")
                
                client_thread = threading.Thread(
                    target=self.handle_client,
                    args=(client_socket, address)
                )
                client_thread.start()
        
        except Exception as e:
            print(f"Chat server error: {e}")
    
    def handle_client(self, client_socket, address):
        """Handle individual chat client."""
        client_id = len(self.clients)
        self.clients.append(client_socket)
        
        try:
            # Send welcome message
            welcome_msg = {
                "type": "welcome",
                "client_id": client_id,
                "message": "Welcome to the chat server!"
            }
            
            self.send_message(client_socket, welcome_msg)
            
            while True:
                data = client_socket.recv(1024)
                if not data:
                    break
                
                try:
                    message = json.loads(data.decode('utf-8'))
                except json.JSONDecodeError:
                    continue
                
                # Handle different message types
                if message['type'] == 'chat':
                    self.broadcast_message(client_socket, message)
                elif message['type'] == 'nickname':
                    self.nicknames[client_id] = message['nickname']
                    self.broadcast_message(client_socket, {
                        "type": "nickname_update",
                        "client_id": client_id,
                        "nickname": message['nickname']
                    })
                elif message['type'] == 'list':
                    client_list = [
                        self.nicknames.get(cid, f"Client{cid}")
                        for cid in range(len(self.clients))
                    ]
                    self.send_message(client_socket, {
                        "type": "client_list",
                        "clients": client_list
                    })
        
        except Exception as e:
            print(f"Error handling client {address}: {e}")
        finally:
            # Remove client
            if client_socket in self.clients:
                self.clients.remove(client_socket)
            
            # Broadcast disconnect message
            disconnect_msg = {
                "type": "disconnect",
                "client_id": client_id
            }
            self.broadcast_message(None, disconnect_msg)
    
    def send_message(self, client_socket, message):
        """Send message to specific client."""
        try:
            data = json.dumps(message).encode('utf-8')
            client_socket.send(data)
        except:
            pass  # Client disconnected
    
    def broadcast_message(self, sender_socket, message):
        """Broadcast message to all clients."""
        for client in self.clients:
            if client != sender_socket:
                self.send_message(client, message)

def run_chat_server():
    """Run the chat server."""
    server = ChatServer()
    
    try:
        server.start()
    except KeyboardInterrupt:
        print("\nShutting down chat server...")
    finally:
        if server.server_socket:
            server.server_socket.close()

if __name__ == "__main__":
    run_chat_server()
```

### File Transfer Server
```python
import socket
import os
import threading

class FileTransferServer:
    """Simple file transfer server."""
    
    def __init__(self, host='localhost', port=8085, directory='uploads'):
        self.host = host
        self.port = port
        self.directory = directory
        self.server_socket = None
        
        # Create upload directory
        os.makedirs(directory, exist_ok=True)
    
    def start(self):
        """Start the file transfer server."""
        self.server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        
        try:
            self.server_socket.bind((self.host, self.port))
            self.server_socket.listen(5)
            print(f"File transfer server listening on {self.host}:{port}")
            print(f"Upload directory: {self.directory}")
            
            while True:
                client_socket, address = self.server_socket.accept()
                print(f"Connection from {address}")
                
                # Handle file transfer in separate thread
                client_thread = threading.Thread(
                    target=self.handle_client,
                    args=(client_socket, address)
                )
                client_thread.start()
        
        except Exception as e:
            print(f"File server error: {e}")
    
    def handle_client(self, client_socket, address):
        """Handle file transfer client."""
        try:
            # Receive file info
            file_info_data = client_socket.recv(1024)
            file_info = json.loads(file_info_data.decode('utf-8'))
            
            filename = file_info['filename']
            file_size = file_info['filesize']
            
            print(f"Receiving file: {filename} ({file_size} bytes)")
            
            # Receive file data
            file_path = os.path.join(self.directory, filename)
            received_size = 0
            
            with open(file_path, 'wb') as f:
                while received_size < file_size:
                    data = client_socket.recv(4096)
                    if not data:
                        break
                    f.write(data)
                    received_size += len(data)
            
            if received_size == file_size:
                print(f"File {filename} received successfully")
                
                # Send confirmation
                confirmation = {
                    "type": "success",
                    "filename": filename,
                    "size": received_size
                }
                
                client_socket.send(json.dumps(confirmation).encode('utf-8'))
            else:
                print(f"File {filename} incomplete (received {received_size}/{file_size} bytes)")
                
                # Send error
                error_msg = {
                    "type": "error",
                    "filename": filename,
                    "message": "File transfer incomplete"
                }
                
                client_socket.send(json.dumps(error_msg).encode('utf-8'))
        
        except Exception as e:
            print(f"Error handling file transfer from {address}: {e}")
        finally:
            client_socket.close()

def run_file_transfer_server():
    """Run the file transfer server."""
    server = FileTransferServer()
    
    try:
        server.start()
    except KeyboardInterrupt:
        print("\nShutting down file transfer server...")
    finally:
        if server.server_socket:
            server.server_socket.close()

if __name__ == "__main__":
    run_file_transfer_server()
```

### File Transfer Client
```python
import socket
import json
import os

class FileTransferClient:
    """Simple file transfer client."""
    
    def upload_file(self, filename, host='localhost', port=8085):
        """Upload a file to the server."""
        if not os.path.exists(filename):
            print(f"File {filename} not found")
            return
        
        file_size = os.path.getsize(filename)
        
        try:
            # Connect to server
            client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            client_socket.connect((host, port))
            
            # Send file info
            file_info = {
                "filename": os.path.basename(filename),
                "filesize": file_size
            }
            
            client_socket.send(json.dumps(file_info).encode('utf-8'))
            
            # Send file data
            with open(filename, 'rb') as f:
                while True:
                    data = f.read(4096)
                    if not data:
                        break
                    client_socket.send(data)
            
            # Receive confirmation
            response_data = client_socket.recv(1024)
            response = json.loads(response_data.decode('utf-8'))
            
            if response['type'] == 'success':
                print(f"File {filename} uploaded successfully")
                print(f"Size: {response['size']} bytes")
            else:
                print(f"Upload failed: {response['message']}")
        
        except Exception as e:
            print(f"Upload error: {e}")
        finally:
            client_socket.close()

def run_file_transfer_client():
    """Run the file transfer client."""
    client = FileTransferClient()
    
    # Create a test file
    test_file = "test_upload.txt"
    with open(test_file, 'w') as f:
        f.write("This is a test file for upload.\n" * 100)
    
    # Upload the file
    client.upload_file(test_file)
    
    # Clean up
    os.remove(test_file)

if __name__ == "__main__":
    print("Make sure to run the file transfer server first!")
    print("Then run this client example.")
    
    # Uncomment to test client (requires server to be running)
    # run_file_transfer_client()
```

## Network Security

### SSL/TLS Support
```python
import socket
import ssl

def create_ssl_context():
    """Create SSL context for secure connections."""
    context = ssl.create_default_context()
    context.check_hostname = False
    context.verify_mode = ssl.CERT_NONE
    return context

def ssl_server_example():
    """Demonstrate SSL server."""
    context = create_ssl_context()
    
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    
    # Wrap socket with SSL
    ssl_socket = context.wrap_socket(server_socket, server_side=True)
    
    try:
        ssl_socket.bind(('localhost', 8443))
        ssl_socket.listen(5)
        print("SSL server listening on localhost:8443")
        
        while True:
            client_socket, address = ssl_socket.accept()
            print(f"SSL connection from {address}")
            
            # Handle client
            try:
                data = client_socket.recv(1024)
                if data:
                    print(f"Received: {data.decode('utf-8')}")
                    client_socket.send(b"SSL Echo: " + data)
            except Exception as e:
                print(f"SSL client error: {e}")
            finally:
                client_socket.close()
    
    except Exception as e:
        print(f"SSL server error: {e}")
    finally:
        ssl_socket.close()

def ssl_client_example():
    """Demonstrate SSL client."""
    context = create_ssl_context()
    
    client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    
    # Wrap socket with SSL
    ssl_socket = context.wrap_socket(client_socket, server_hostname='localhost')
    
    try:
        ssl_socket.connect(('localhost', 8443))
        print("Connected to SSL server")
        
        ssl_socket.send(b"Hello SSL Server!")
        
        response = ssl_socket.recv(1024)
        print(f"Server response: {response.decode('utf-8')}")
    
    except Exception as e:
        print(f"SSL client error: {e}")
    finally:
        ssl_socket.close()

def run_ssl_example():
    """Run SSL server and client example."""
    import threading
    
    print("=== SSL Server/Client Example ===")
    
    # Start server in separate thread
    server_thread = threading.Thread(target=ssl_server_example)
    server_thread.daemon = True
    server_thread.start()
    
    # Give server time to start
    import time
    time.sleep(1)
    
    # Run client
    ssl_client_example()
    
    # Wait for server to handle connection
    time.sleep(1)

if __name__ == "__main__":
    run_ssl_example()
```

### Network Scanning
```python
import socket
import threading
import time
from concurrent.futures import ThreadPoolExecutor

class PortScanner:
    """Network port scanner."""
    
    def __init__(self, host='localhost', timeout=1):
        self.host = host
        self.timeout = timeout
    
    def scan_port(self, port):
        """Scan a single port."""
        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(self.timeout)
            
            result = sock.connect_ex((self.host, port))
            sock.close()
            
            return port, result is None
        
        except Exception:
            return port, False
    
    def scan_range(self, start_port, end_port, max_threads=50):
        """Scan a range of ports."""
        open_ports = []
        
        with ThreadPoolExecutor(max_workers=max_threads) as executor:
            futures = []
            
            for port in range(start_port, end_port + 1):
                future = executor.submit(self.scan_port, port)
                futures.append(future)
            
            for future in futures:
                port, is_open = future.result()
                if is_open:
                    open_ports.append(port)
                    print(f"Port {port} is open")
        
        return open_ports

def scan_common_ports():
    """Scan common ports."""
    scanner = PortScanner()
    
    common_ports = [21, 22, 23, 25, 53, 80, 110, 143, 443, 993, 995, 3306, 3389, 5432, 8080, 8443]
    
    print(f"Scanning common ports on {scanner.host}...")
    open_ports = scanner.scan_range(min(common_ports), max(common_ports))
    
    if open_ports:
        print(f"Open ports: {open_ports}")
    else:
        print("No open ports found")

def run_port_scanner():
    """Run the port scanner."""
    scan_common_ports()

if __name__ == "__main__":
    run_port_scanner()
```

## Best Practices

### Network Programming Best Practices
```python
import socket
import logging

# Best Practice 1: Always handle exceptions
def safe_network_operation():
    """Demonstrate safe network operations."""
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.settimeout(5.0)
        
        # Perform network operation
        sock.connect(('localhost', 8080))
        sock.send(b"Hello")
        response = sock.recv(1024)
        
        print(f"Response: {response.decode('utf-8')}")
        return True
    
    except socket.timeout:
        print("Connection timed out")
        return False
    except socket.error as e:
        print(f"Socket error: {e}")
        return False
    except Exception as e:
        print(f"Unexpected error: {e}")
        return False
    finally:
        sock.close()

# Best Practice 2: Use appropriate timeouts
def timeout_example():
    """Demonstrate timeout usage."""
    
    # Short timeout for quick operations
    short_timeout = 1.0
    
    # Longer timeout for slow operations
    long_timeout = 30.0
    
    print(f"Short timeout: {short_timeout} seconds")
    print(f"Long timeout: {long_timeout} seconds")

# Best Practice 3: Use connection pooling
class ConnectionPool:
    """Simple connection pool."""
    
    def __init__(self, host, port, max_connections=5):
        self.host = host
        self.port = port
        self.max_connections = max_connections
        self.pool = []
        self.lock = threading.Lock()
    
    def get_connection(self):
        """Get a connection from the pool."""
        with self.lock:
            if self.pool:
                return self.pool.pop()
            else:
                return self._create_connection()
    
    def release_connection(self, connection):
        """Release a connection back to the pool."""
        with self.lock:
            if len(self.pool) < self.max_connections:
                self.pool.append(connection)
            else:
                connection.close()
    
    def _create_connection(self):
        """Create a new connection."""
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.settimeout(5.0)
        sock.connect((self.host, self.port))
        return sock

# Best Practice 4: Use proper error handling
def robust_network_client():
    """Robust network client with comprehensive error handling."""
    
    def __init__(self, host, port, max_retries=3):
        self.host = host
        self.port = port
        self.max_retries = max_retries
    
    def connect_with_retry(self):
        """Connect with retry logic."""
        last_exception = None
        
        for attempt in range(self.max_retries):
            try:
                sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                sock.settimeout(5.0)
                
                sock.connect((self.host, self.port))
                return sock
            
            except socket.timeout:
                last_exception = "Connection timed out"
                print(f"Attempt {attempt + 1}: Connection timed out")
            except socket.error as e:
                last_exception = f"Socket error: {e}"
                print(f"Attempt {attempt + 1}: {e}")
            except Exception as e:
                last_exception = f"Unexpected error: {e}"
                print(f"Attempt {attempt + 1}: {e}")
            
            if attempt < self.max_retries - 1:
                time.sleep(1)  # Wait before retry
        
        raise Exception(f"Failed to connect after {self.max_retries} attempts: {last_exception}")

# Best Practice 5: Use logging for debugging
def setup_logging():
    """Setup logging for network operations."""
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s - %(levelname)s - %(message)s'
    )

# Best Practice 6: Handle network latency
def handle_latency():
    """Demonstrate handling network latency."""
    
    # Implement retry with exponential backoff
    def connect_with_backoff():
        backoff = 1
        max_backoff = 32
        
        for attempt in range(5):
            try:
                sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                sock.settimeout(backoff)
                sock.connect(('localhost', 8080))
                return sock
            
            except socket.timeout:
                if backoff < max_backoff:
                    print(f"Connection failed, retrying in {backoff} seconds...")
                    time.sleep(backoff)
                    backoff *= 2
                else:
                    raise
        
        raise Exception("Max retries exceeded")
    
    try:
        sock = connect_with_backoff()
        print("Connected successfully with backoff")
        sock.close()
    except Exception as e:
        print(f"Connection failed: {e}")

# Best Practice 7: Use appropriate buffer sizes
def buffer_size_optimization():
    """Demonstrate buffer size optimization."""
    
    # Small buffer (more system calls, lower latency)
    small_buffer = 1024
    
    # Large buffer (fewer system calls, higher throughput)
    large_buffer = 65536
    
    def test_buffer_size(buffer_size):
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.setsockopt(socket.SOL_SOCKET, socket.SO_SNDBUF, buffer_size)
        sock.setsockopt(socket.SOL_SOCKET, socket.SO_RCVBUF, buffer_size)
        
        print(f"Buffer size: {buffer_size}")
        print(f"Send buffer: {sock.getsockopt(socket.SOL_SOCKET, socket.SO_SNDBUF)}")
        print(f"Receive buffer: {sock.getsockopt(socket.SOL_SOCKET, socket.SO_RCVBUF)}")
        sock.close()
    
    print("Small buffer:")
    test_buffer_size(small_buffer)
    
    print("\nLarge buffer:")
    test_buffer_size(large_buffer)

# Best Practice 8: Use keep-alive for long connections
def keepalive_example():
    """Demonstrate TCP keep-alive."""
    
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_KEEPALIVE, 1)
    
    # Set keepalive options
    sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_KEEPIDLE, 1)
    sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_KEEPINTVL, 60)
    sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_KEEPCNT, 5)
    
    try:
        sock.connect(('localhost', 8080))
        print("Connected with keep-alive")
        
        # Keep connection alive
        for i in range(3):
            sock.send(f"Message {i}".encode('utf-8'))
            response = sock.recv(1024)
            print(f"Response {i}: {response.decode('utf-8')}")
            time.sleep(2)
    
    except Exception as e:
        print(f"Keep-alive error: {e}")
    finally:
        sock.close()

def demonstrate_best_practices():
    """Demonstrate network programming best practices."""
    
    print("=== Network Programming Best Practices ===")
    
    print("\n1. Safe Network Operation:")
    safe_network_operation()
    
    print("\n2. Timeout Management:")
    timeout_example()
    
    print("\n3. Connection Pooling:")
    pool = ConnectionPool('localhost', 8080)
    conn1 = pool.get_connection()
    print(f"Got connection: {conn1.getsockname()}")
    pool.release_connection(conn1)
    
    print("\n4. Robust Client:")
    client = robust_network_client('localhost', 8080)
    try:
        conn = client.connect_with_retry()
        print(f"Connected: {conn.getsockname()}")
        conn.close()
    except Exception as e:
        print(f"Connection failed: {e}")
    
    print("\n5. Logging Setup:")
    setup_logging()
    logging.info("Network operations started")
    
    print("\n6. Latency Handling:")
    handle_latency()
    
    print("\n7. Buffer Size Optimization:")
    buffer_size_optimization()
    
    print("\n8. Keep-Alive:")
    keepalive_example()

if __name__ == "__main__":
    demonstrate_best_practices()
```

## Summary

Python network programming provides comprehensive capabilities:

**Core Concepts:**
- Socket programming fundamentals
- TCP and UDP protocols
- Client-server architecture
- Inter-process communication

**Advanced Features:**
- Non-blocking I/O with select
- SSL/TLS for secure connections
- Network scanning and discovery
- Connection pooling and management

**Real-world Applications:**
- Chat servers and messaging systems
- File transfer protocols
- Web servers and clients
- Network monitoring tools

**Security Considerations:**
- SSL/TLS implementation
- Certificate handling
- Secure communication patterns
- Network authentication

**Performance Optimization:**
- Buffer size tuning
- Connection pooling
- Keep-alive mechanisms
- Latency handling strategies

**Best Practices:**
- Comprehensive error handling
- Appropriate timeout usage
- Resource cleanup
- Logging and debugging
- Retry logic with backoff
- Thread safety considerations

Network programming in Python enables building robust, scalable network applications from simple sockets to complex distributed systems.
