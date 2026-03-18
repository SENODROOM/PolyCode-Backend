// ============================================================
//  Project 04: Mini HTTP Server
//
//  Features:
//  - TCP listener on localhost:7878
//  - Parse HTTP GET requests
//  - Route handler system
//  - Serve HTML responses
//  - Thread pool for concurrent connections
//  - Graceful 404 handling
//
//  Run: cargo run
//  Then: curl http://localhost:7878/
//        curl http://localhost:7878/hello
//        curl http://localhost:7878/time
//        curl http://localhost:7878/echo?msg=Hello
// ============================================================

use std::collections::HashMap;
use std::io::{Read, Write};
use std::net::{TcpListener, TcpStream};
use std::sync::{Arc, Mutex};
use std::thread;
use std::time::Duration;

// ─────────────────────────────────────────────
// HTTP TYPES
// ─────────────────────────────────────────────

#[derive(Debug)]
struct HttpRequest {
    method:  String,
    path:    String,
    query:   HashMap<String, String>,
    headers: HashMap<String, String>,
    body:    String,
}

impl HttpRequest {
    fn parse(raw: &str) -> Option<Self> {
        let mut lines = raw.lines();
        let request_line = lines.next()?;
        let mut parts = request_line.split_whitespace();

        let method = parts.next()?.to_string();
        let full_path = parts.next()?.to_string();

        // Split path and query string
        let (path, query_str) = if let Some(pos) = full_path.find('?') {
            (&full_path[..pos], &full_path[pos + 1..])
        } else {
            (full_path.as_str(), "")
        };

        // Parse query parameters
        let mut query = HashMap::new();
        for pair in query_str.split('&').filter(|s| s.contains('=')) {
            let mut kv = pair.splitn(2, '=');
            if let (Some(k), Some(v)) = (kv.next(), kv.next()) {
                query.insert(url_decode(k), url_decode(v));
            }
        }

        // Parse headers
        let mut headers = HashMap::new();
        let mut body_start = false;
        let mut body_lines: Vec<&str> = Vec::new();

        for line in lines {
            if line.is_empty() { body_start = true; continue; }
            if body_start {
                body_lines.push(line);
            } else if let Some(colon_pos) = line.find(':') {
                let key = line[..colon_pos].trim().to_lowercase();
                let val = line[colon_pos + 1..].trim().to_string();
                headers.insert(key, val);
            }
        }

        Some(HttpRequest {
            method,
            path: path.to_string(),
            query,
            headers,
            body: body_lines.join("\n"),
        })
    }
}

struct HttpResponse {
    status:  u16,
    headers: HashMap<String, String>,
    body:    String,
}

impl HttpResponse {
    fn html(status: u16, body: String) -> Self {
        let mut headers = HashMap::new();
        headers.insert("Content-Type".into(), "text/html; charset=utf-8".into());
        Self { status, headers, body }
    }

    fn text(status: u16, body: String) -> Self {
        let mut headers = HashMap::new();
        headers.insert("Content-Type".into(), "text/plain; charset=utf-8".into());
        Self { status, headers, body }
    }

    fn to_bytes(&self) -> Vec<u8> {
        let status_text = match self.status {
            200 => "OK", 201 => "Created", 400 => "Bad Request",
            404 => "Not Found", 405 => "Method Not Allowed", _ => "Internal Server Error",
        };
        let mut header_str = String::new();
        for (k, v) in &self.headers { header_str.push_str(&format!("{}: {}\r\n", k, v)); }
        let response = format!(
            "HTTP/1.1 {} {}\r\nContent-Length: {}\r\n{}\r\n{}",
            self.status, status_text, self.body.len(), header_str, self.body
        );
        response.into_bytes()
    }
}

// ─────────────────────────────────────────────
// THREAD POOL
// ─────────────────────────────────────────────

type Job = Box<dyn FnOnce() + Send + 'static>;

struct ThreadPool {
    workers: Vec<thread::JoinHandle<()>>,
    sender:  std::sync::mpsc::Sender<Job>,
}

impl ThreadPool {
    fn new(size: usize) -> Self {
        let (sender, receiver) = std::sync::mpsc::channel::<Job>();
        let receiver = Arc::new(Mutex::new(receiver));
        let mut workers = Vec::with_capacity(size);

        for id in 0..size {
            let rx = Arc::clone(&receiver);
            let handle = thread::spawn(move || {
                loop {
                    match rx.lock().unwrap().recv() {
                        Ok(job)  => { job(); }
                        Err(_)   => { println!("Worker {} shutting down", id); break; }
                    }
                }
            });
            workers.push(handle);
        }
        ThreadPool { workers, sender }
    }

    fn execute<F: FnOnce() + Send + 'static>(&self, f: F) {
        self.sender.send(Box::new(f)).expect("Failed to send job");
    }
}

// ─────────────────────────────────────────────
// ROUTER
// ─────────────────────────────────────────────

type HandlerFn = fn(&HttpRequest) -> HttpResponse;

struct Router { routes: HashMap<String, HandlerFn> }

impl Router {
    fn new() -> Self { Self { routes: HashMap::new() } }

    fn register(&mut self, path: &str, handler: HandlerFn) {
        self.routes.insert(path.to_string(), handler);
    }

    fn dispatch(&self, req: &HttpRequest) -> HttpResponse {
        if let Some(handler) = self.routes.get(&req.path) {
            handler(req)
        } else {
            handler_404(req)
        }
    }
}

// ─────────────────────────────────────────────
// HANDLERS
// ─────────────────────────────────────────────

fn handler_home(_req: &HttpRequest) -> HttpResponse {
    HttpResponse::html(200, r#"<!DOCTYPE html>
<html>
<head><title>PolyCode Rust Server</title>
<style>body{font-family:monospace;background:#020408;color:#dde0ec;padding:40px}
h1{color:#c8ff00}a{color:#00e8ff}code{background:#0f1628;padding:2px 8px;border-radius:4px}</style>
</head><body>
<h1>🦀 PolyCode Rust HTTP Server</h1>
<p>A minimal HTTP server built in pure Rust — no framework, no magic.</p>
<h2>Routes</h2>
<ul>
  <li><a href="/">/</a> — This page</li>
  <li><a href="/hello">/hello</a> — Greeting</li>
  <li><a href="/time">/time</a> — Current uptime</li>
  <li><a href="/echo?msg=HelloRust">/echo?msg=...</a> — Echo message</li>
  <li><a href="/stats">/stats</a> — Server statistics</li>
  <li><a href="/about">/about</a> — About this server</li>
</ul>
<p>Built with: <code>TcpListener</code>, <code>ThreadPool</code>, custom HTTP parser</p>
</body></html>"#.to_string())
}

fn handler_hello(req: &HttpRequest) -> HttpResponse {
    let name = req.query.get("name").cloned().unwrap_or_else(|| "World".to_string());
    HttpResponse::html(200, format!(r#"<!DOCTYPE html>
<html><head><title>Hello</title>
<style>body{{font-family:monospace;background:#020408;color:#dde0ec;padding:40px}}h1{{color:#c8ff00}}</style>
</head><body>
<h1>Hello, {}! 🦀</h1>
<p>You've reached the Rust HTTP Server.</p>
<p>Try: <code>/hello?name=YourName</code></p>
<p><a href="/">← Back</a></p>
</body></html>"#, name))
}

static START_TIME: std::sync::OnceLock<std::time::Instant> = std::sync::OnceLock::new();

fn handler_time(_req: &HttpRequest) -> HttpResponse {
    let uptime = START_TIME.get_or_init(std::time::Instant::now).elapsed();
    let secs  = uptime.as_secs();
    let hours = secs / 3600;
    let mins  = (secs % 3600) / 60;
    let s     = secs % 60;
    HttpResponse::html(200, format!(r#"<!DOCTYPE html>
<html><head><title>Server Time</title>
<style>body{{font-family:monospace;background:#020408;color:#dde0ec;padding:40px}}
.time{{font-size:3rem;color:#c8ff00;margin:20px 0}}</style>
</head><body>
<h1>Server Uptime</h1>
<div class="time">{}h {}m {}s</div>
<p>Total seconds: {}</p>
<p><a href="/">← Back</a></p>
</body></html>"#, hours, mins, s, secs))
}

fn handler_echo(req: &HttpRequest) -> HttpResponse {
    let msg = req.query.get("msg").cloned().unwrap_or_else(|| "(no message — use ?msg=YourText)".to_string());
    HttpResponse::html(200, format!(r#"<!DOCTYPE html>
<html><head><title>Echo</title>
<style>body{{font-family:monospace;background:#020408;color:#dde0ec;padding:40px}}
.echo{{background:#0f1628;padding:20px;border-left:4px solid #c8ff00;font-size:1.5rem;color:#c8ff00}}</style>
</head><body>
<h1>Echo</h1>
<div class="echo">{}</div>
<p>Query params: {:?}</p>
<p><a href="/">← Back</a></p>
</body></html>"#, msg, req.query))
}

fn handler_about(_req: &HttpRequest) -> HttpResponse {
    HttpResponse::html(200, r#"<!DOCTYPE html>
<html><head><title>About</title>
<style>body{font-family:monospace;background:#020408;color:#dde0ec;padding:40px}
h1{color:#c8ff00}code{background:#0f1628;padding:2px 8px}</style>
</head><body>
<h1>About This Server</h1>
<p>This HTTP server was built as part of the PolyCode Rust course.</p>
<h2>Architecture</h2>
<ul>
  <li><code>TcpListener</code> — accepts incoming connections</li>
  <li><code>ThreadPool</code> — reuses threads for concurrent connections</li>
  <li><code>HttpRequest</code> — manually parsed HTTP/1.1 requests</li>
  <li><code>Router</code> — dispatches paths to handler functions</li>
  <li><code>HttpResponse</code> — builds valid HTTP response bytes</li>
</ul>
<h2>No External Dependencies</h2>
<p>Pure Rust standard library only — no Actix, no Axum, no Rocket.</p>
<p><a href="/">← Back</a></p>
</body></html>"#.to_string())
}

fn handler_404(_req: &HttpRequest) -> HttpResponse {
    HttpResponse::html(404, r#"<!DOCTYPE html>
<html><head><title>404</title>
<style>body{font-family:monospace;background:#020408;color:#dde0ec;padding:40px}
h1{color:#ff4466;font-size:4rem}</style>
</head><body>
<h1>404</h1>
<p>Page not found</p>
<p><a href="/" style="color:#00e8ff">← Go Home</a></p>
</body></html>"#.to_string())
}

// ─────────────────────────────────────────────
// CONNECTION HANDLER
// ─────────────────────────────────────────────

fn handle_connection(mut stream: TcpStream, router: Arc<Router>) {
    stream.set_read_timeout(Some(Duration::from_secs(5))).ok();

    let mut buffer = [0u8; 4096];
    let bytes_read = match stream.read(&mut buffer) {
        Ok(n) => n,
        Err(_) => return,
    };

    let raw = String::from_utf8_lossy(&buffer[..bytes_read]).to_string();

    let response = if let Some(req) = HttpRequest::parse(&raw) {
        println!("  {} {} — query: {:?}", req.method, req.path, req.query);
        if req.method != "GET" {
            HttpResponse::text(405, "Method Not Allowed".to_string())
        } else {
            router.dispatch(&req)
        }
    } else {
        HttpResponse::text(400, "Bad Request".to_string())
    };

    stream.write_all(&response.to_bytes()).ok();
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

fn main() {
    let addr = "127.0.0.1:7878";
    START_TIME.get_or_init(std::time::Instant::now);

    let listener = TcpListener::bind(addr).expect("Failed to bind");
    println!("🦀 PolyCode Rust HTTP Server");
    println!("   Listening on http://{}", addr);
    println!("   Thread pool: 4 workers");
    println!("   Press Ctrl+C to stop\n");

    let mut router = Router::new();
    router.register("/",       handler_home);
    router.register("/hello",  handler_hello);
    router.register("/time",   handler_time);
    router.register("/echo",   handler_echo);
    router.register("/about",  handler_about);

    let router = Arc::new(router);
    let pool   = ThreadPool::new(4);

    for stream in listener.incoming() {
        match stream {
            Ok(stream) => {
                let router = Arc::clone(&router);
                pool.execute(move || handle_connection(stream, router));
            }
            Err(e) => eprintln!("Connection error: {}", e),
        }
    }
}

fn url_decode(s: &str) -> String {
    s.replace('+', " ")
     .replace("%20", " ").replace("%21", "!").replace("%22", "\"")
     .replace("%27", "'").replace("%28", "(").replace("%29", ")")
}
