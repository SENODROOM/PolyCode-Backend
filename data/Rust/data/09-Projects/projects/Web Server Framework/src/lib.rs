//! Web Server Framework
//! 
//! A high-performance, async web server framework built with Rust.
//! 
//! # Example
//! 
//! ```rust
//! use web_server_framework::{Server, Response};
//! 
//! #[tokio::main]
//! async fn main() {
//!     let mut app = Server::new();
//!     
//!     app.get("/", |req| async move {
//!         Response::html("<h1>Hello, World!</h1>")
//!     });
//!     
//!     app.listen("127.0.0.1:3000").await.unwrap();
//! }
//! ```

pub mod server;
pub mod http;
pub mod router;
pub mod middleware;
pub mod handlers;
pub mod security;
pub mod templates;
pub mod utils;
pub mod error;

// Re-export main types for convenience
pub use server::{Server, ServerBuilder};
pub use http::{Request, Response, Method, StatusCode};
pub use router::{Router, Route};
pub use middleware::{Middleware, Next};
pub use error::{Result, Error};

/// Library version
pub const VERSION: &str = env!("CARGO_PKG_VERSION");

/// Default configuration
pub mod config {
    /// Default server address
    pub const DEFAULT_ADDR: &str = "127.0.0.1:3000";
    
    /// Default number of worker threads
    pub const DEFAULT_WORKERS: usize = 4;
    
    /// Default request timeout in seconds
    pub const DEFAULT_TIMEOUT: u64 = 30;
    
    /// Default maximum request size in bytes
    pub const DEFAULT_MAX_REQUEST_SIZE: usize = 10 * 1024 * 1024; // 10MB
}

#[cfg(test)]
mod tests {
    use super::*;
    
    #[tokio::test]
    async fn test_basic_server() {
        let mut app = Server::new();
        
        app.get("/", |req| async move {
            Response::html("<h1>Test</h1>")
        });
        
        // This would need a test server to actually test
        // For now, just ensure it compiles
        assert_eq!(VERSION, env!("CARGO_PKG_VERSION"));
    }
}