# C++ Variables

## Variable Declaration and Initialization

### Basic Types
```cpp
int age = 25;
double salary = 50000.50;
char grade = 'A';
bool is_valid = true;
float pi = 3.14159f;
```

### Modern C++ Initialization
```cpp
// Copy initialization
int x = 10;

// Direct initialization
int y(20);

// Uniform initialization (C++11)
int z{30};

// Auto type deduction (C++11)
auto number = 42;
auto value = 3.14;
auto name = std::string("Hello");

// Constexpr (compile-time constant)
constexpr int MAX_SIZE = 100;
constexpr double PI = 3.14159265359;
```

## Type Modifiers

### Signed/Unsigned
```cpp
unsigned int count = 100;    // Non-negative integers
signed int temperature = -10; // Can be positive or negative
```

### Short/Long
```cpp
short small_number = 32767;
long large_number = 2147483647L;
long long very_large = 9223372036854775807LL;
```

## References and Pointers

### References
```cpp
int original = 42;
int& ref = original;  // Reference to original
ref = 100;           // Modifies original
// original is now 100

// Const reference
const int& const_ref = original;
// const_ref = 200; // Error: cannot modify const reference

// Reference parameters
void increment(int& value) {
    value++;
}

int main() {
    int num = 10;
    increment(num);  // num becomes 11
    return 0;
}
```

### Pointers
```cpp
int value = 42;
int* ptr = &value;  // Pointer to value
std::cout << *ptr;  // Dereference: prints 42

*ptr = 100;         // Modify value through pointer
// value is now 100

// Smart pointers (C++11)
#include <memory>

std::unique_ptr<int> unique_ptr = std::make_unique<int>(42);
std::shared_ptr<int> shared_ptr1 = std::make_shared<int>(100);
std::shared_ptr<int> shared_ptr2 = shared_ptr1; // Shared ownership
```

## Constants

### const Keyword
```cpp
const int MAX_ATTEMPTS = 3;
const double PI = 3.14159;

// Const member function
class Circle {
public:
    double getRadius() const { return radius_; } // Cannot modify members
    void setRadius(double r) { radius_ = r; }
private:
    double radius_;
};
```

### constexpr
```cpp
constexpr int square(int x) {
    return x * x;
}

constexpr int result = square(5); // Evaluated at compile time
```

## Type Deduction

### auto Keyword
```cpp
auto x = 42;        // int
auto y = 3.14;      // double
auto z = "hello";   // const char*
auto vec = std::vector<int>{1, 2, 3}; // std::vector<int>

// With references
int value = 42;
auto& ref = value;  // int&
const auto& cref = value; // const int&
```

### decltype
```cpp
int x = 42;
decltype(x) y = 100;  // y is int
decltype((x)) z = x; // z is int& (reference)

// Use with templates
template<typename T, typename U>
auto add(T t, U u) -> decltype(t + u) {
    return t + u;
}
```

## Scope and Lifetime

### Local Variables
```cpp
void function() {
    int local_var = 10;  // Automatic storage duration
    // Destroyed when function exits
}
```

### Static Variables
```cpp
void counter() {
    static int count = 0;  // Static storage duration
    count++;
    std::cout << "Count: " << count << std::endl;
    // Retains value between function calls
}
```

### Global Variables
```cpp
int global_var = 100;  // Global scope

class MyClass {
public:
    static int class_var;  // Class member
};

int MyClass::class_var = 200; // Definition
```

## Type Casting

### Static Cast
```cpp
double d = 3.14;
int i = static_cast<int>(d);  // Explicit conversion

// Base to derived class
class Base {};
class Derived : public Base {};

Base* base_ptr = new Derived();
Derived* derived_ptr = static_cast<Derived*>(base_ptr);
```

### Dynamic Cast
```cpp
// Safe downcasting with polymorphic types
class Animal { virtual void speak() {} };
class Dog : public Animal { void speak() override {} };

Animal* animal = new Dog();
Dog* dog = dynamic_cast<Dog*>(animal); // Returns nullptr if not a Dog
```

### Const Cast
```cpp
const int const_val = 42;
int* non_const = const_cast<int*>(&const_val);
// Use with caution!
```

### Reinterpret Cast
```cpp
int value = 0x12345678;
char* bytes = reinterpret_cast<char*>(&value);
// Low-level type conversion
```

## Variable Templates (C++14)

```cpp
template<typename T>
constexpr T pi = T(3.1415926535897932385);

template<typename T>
T area_of_circle(T radius) {
    return pi<T> * radius * radius;
}

// Usage
double area1 = area_of_circle(2.5);
float area2 = area_of_circle(2.5f);
```

## Best Practices
- Use `auto` for complex type names
- Prefer `const` and `constexpr` for constants
- Use smart pointers instead of raw pointers
- Initialize variables at declaration
- Use references instead of pointers when possible
- Prefer `static_cast` over C-style casts
- Use meaningful variable names
- Minimize global variable usage
- Use appropriate scope for variables
- Consider using `std::optional` for optional values (C++17)
