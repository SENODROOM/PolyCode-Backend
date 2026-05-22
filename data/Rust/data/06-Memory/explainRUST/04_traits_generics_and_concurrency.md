## Traits

Traits define shared behavior — similar to interfaces in other languages.

```rust
trait Drawable {
    fn draw(&self);
    fn bounding_box(&self) -> (f64, f64, f64, f64);

    // Default implementation
    fn describe(&self) {
        println!("I am a drawable shape");
    }
}

struct Circle { cx: f64, cy: f64, r: f64 }
struct Rect   { x: f64, y: f64, w: f64, h: f64 }

impl Drawable for Circle {
    fn draw(&self) {
        println!("Drawing circle at ({}, {}), r={}", self.cx, self.cy, self.r);
    }
    fn bounding_box(&self) -> (f64, f64, f64, f64) {
        (self.cx - self.r, self.cy - self.r,
         self.cx + self.r, self.cy + self.r)
    }
}

impl Drawable for Rect {
    fn draw(&self) {
        println!("Drawing rect at ({}, {}), {}×{}", self.x, self.y, self.w, self.h);
    }
    fn bounding_box(&self) -> (f64, f64, f64, f64) {
        (self.x, self.y, self.x + self.w, self.y + self.h)
    }
}

// Trait objects — dynamic dispatch
fn render_all(shapes: &[Box<dyn Drawable>]) {
    for shape in shapes {
        shape.draw();
    }
}

// Important standard traits
// Display  — for println! formatting
// Debug    — for {:?} formatting  
// Clone    — deep copy
// Copy     — bitwise copy (stack types)
// Iterator — enables for loops, map, filter, etc.
// From/Into — type conversions
// PartialEq/Eq — equality comparison
// PartialOrd/Ord — ordering
```

---

## Generics

```rust
// Generic function
fn largest<T: PartialOrd>(list: &[T]) -> &T {
    let mut largest = &list[0];
    for item in list {
        if item > largest {
            largest = item;
        }
    }
    largest
}

// Generic struct
struct Pair<T> {
    first: T,
    second: T,
}

impl<T: std::fmt::Display + PartialOrd> Pair<T> {
    fn cmp_display(&self) {
        if self.first >= self.second {
            println!("First is larger: {}", self.first);
        } else {
            println!("Second is larger: {}", self.second);
        }
    }
}

// Where clause for complex bounds
fn complex<T, U>(t: &T, u: &U) -> String
where
    T: std::fmt::Display + Clone,
    U: std::fmt::Debug + Clone,
{
    format!("{t} and {u:?}")
}
```

---

## Concurrency

Rust's type system prevents data races at compile time — "Fearless Concurrency".

```rust
use std::thread;
use std::sync::{Arc, Mutex};

fn main() {
    // Spawn threads with move closure (ownership transfer)
    let handle = thread::spawn(|| {
        println!("Hello from a thread!");
    });
    handle.join().unwrap();

    // Sharing data across threads
    let data = Arc::new(Mutex::new(vec![1, 2, 3]));

    let mut handles = vec![];
    for i in 0..5 {
        let data_clone = Arc::clone(&data);
        let handle = thread::spawn(move || {
            let mut v = data_clone.lock().unwrap();
            v.push(i * 10);
        });
        handles.push(handle);
    }

    for h in handles { h.join().unwrap(); }
    println!("{:?}", data.lock().unwrap());

    // Channels — message passing
    use std::sync::mpsc;  // multiple producer, single consumer
    let (tx, rx) = mpsc::channel();

    thread::spawn(move || {
        tx.send("hello from thread").unwrap();
    });

    let msg = rx.recv().unwrap();
    println!("{msg}");

    // Async with Tokio (most popular async runtime)
    // async fn fetch(url: &str) -> Result<String, reqwest::Error> {
    //     reqwest::get(url).await?.text().await
    // }
}
```

---

