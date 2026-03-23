/**
 * Traits and Generics in Rust
 * This tutorial covers defining common behavior with Traits 
 * and using Generics for type-independent code.
 */

// --- Trait Definition ---
trait Summary {
    // Default implementation
    fn summarize(&self) -> String {
        String::from("(Read more...)")
    }
}

struct NewsArticle {
    headline: String,
    location: String,
    author: String,
}

impl Summary for NewsArticle {
    fn summarize(&self) -> String {
        format!("{}, by {} ({})", self.headline, self.author, self.location)
    }
}

struct Tweet {
    username: String,
    content: String,
}

impl Summary for Tweet {
    fn summarize(&self) -> String {
        format!("{}: {}", self.username, self.content)
    }
}

// --- Generic Function ---
// This function takes any type that implements the Summary trait
fn notify<T: Summary>(item: &T) {
    println!("Breaking news! {}", item.summarize());
}

fn main() {
    let article = NewsArticle {
        headline: String::from("Rust 1.77 Released"),
        location: String::from("Online"),
        author: String::from("Rust Team"),
    };

    let tweet = Tweet {
        username: String::from("rustlang"),
        content: String::from("Rust is safe and fast!"),
    };

    println!("Article Summary: {}", article.summarize());
    println!("Tweet Summary: {}", tweet.summarize());

    notify(&article);
    notify(&tweet);
}