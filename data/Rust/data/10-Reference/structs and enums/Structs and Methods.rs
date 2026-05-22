/**
 * Rust Structs and Methods
 * Rust uses structs to group data and impl blocks to define methods.
 */

struct Rectangle {
    width: u32,
    height: u32,
}

impl Rectangle {
    // Constructor (Associated Function)
    fn new(width: u32, height: u32) -> Rectangle {
        Rectangle { width, height }
    }

    // Method (takes &self)
    fn area(&self) -> u32 {
        self.width * self.height
    }

    // Method with multiple parameters
    fn can_hold(&self, other: &Rectangle) -> bool {
        self.width > other.width && self.height > other.height
    }
}

fn main() {
    let rect1 = Rectangle::new(30, 50);
    let rect2 = Rectangle::new(10, 40);
    let rect3 = Rectangle::new(60, 45);

    println!("The area of rect1 is {} square pixels.", rect1.area());
    println!("Can rect1 hold rect2? {}", rect1.can_hold(&rect2));
    println!("Can rect1 hold rect3? {}", rect1.can_hold(&rect3));
}