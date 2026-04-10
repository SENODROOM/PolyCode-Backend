# C++ Functions

## Function Declaration and Definition

### Basic Function
```cpp
#include <iostream>

// Function declaration (prototype)
int add(int a, int b);

// Function definition
int add(int a, int b) {
    return a + b;
}

int main() {
    int result = add(5, 3);
    std::cout << "Result: " << result << std::endl;
    return 0;
}
```

### Function Parameters

#### Pass by Value
```cpp
void modify_value(int x) {
    x = 100;  // Only modifies local copy
}

int main() {
    int value = 5;
    modify_value(value);
    std::cout << value;  // Still 5
    return 0;
}
```

#### Pass by Reference
```cpp
void modify_reference(int& x) {
    x = 100;  // Modifies original
}

int main() {
    int value = 5;
    modify_reference(value);
    std::cout << value;  // Now 100
    return 0;
}
```

#### Pass by Pointer
```cpp
void modify_pointer(int* x) {
    *x = 100;  // Modifies through pointer
}

int main() {
    int value = 5;
    modify_pointer(&value);
    std::cout << value;  // Now 100
    return 0;
}
```

## Function Overloading

```cpp
int add(int a, int b) {
    return a + b;
}

double add(double a, double b) {
    return a + b;
}

std::string add(const std::string& a, const std::string& b) {
    return a + b;
}

int main() {
    int i = add(5, 3);
    double d = add(3.14, 2.71);
    std::string s = add("Hello, ", "World!");
    return 0;
}
```

## Default Parameters

```cpp
void print_message(const std::string& message, int times = 1) {
    for (int i = 0; i < times; ++i) {
        std::cout << message << std::endl;
    }
}

int main() {
    print_message("Hello");        // Prints once
    print_message("Hi", 3);        // Prints three times
    return 0;
}
```

## Inline Functions

```cpp
inline int square(int x) {
    return x * x;
}

// Compiler may substitute function body directly
int main() {
    int result = square(5);  // May become: result = 5 * 5;
    return 0;
}
```

## Function Templates

### Basic Template
```cpp
template<typename T>
T maximum(T a, T b) {
    return (a > b) ? a : b;
}

int main() {
    int max_int = maximum(5, 3);
    double max_double = maximum(3.14, 2.71);
    std::string max_str = maximum("apple", "banana");
    return 0;
}
```

### Multiple Template Parameters
```cpp
template<typename T, typename U>
auto add(T t, U u) -> decltype(t + u) {
    return t + u;
}

int main() {
    auto result1 = add(5, 3.14);    // double
    auto result2 = add(5.0, 3);     // double
    auto result3 = add(5, 3);      // int
    return 0;
}
```

## Lambda Functions (C++11)

### Basic Lambda
```cpp
#include <algorithm>
#include <vector>

int main() {
    std::vector<int> numbers = {1, 2, 3, 4, 5};
    
    // Lambda function
    auto square = [](int x) { return x * x; };
    
    std::transform(numbers.begin(), numbers.end(), numbers.begin(), square);
    
    // Inline lambda
    std::for_each(numbers.begin(), numbers.end(), 
                 [](int x) { std::cout << x << " "; });
    
    return 0;
}
```

### Lambda with Capture
```cpp
void demonstrate_capture() {
    int multiplier = 2;
    
    // Capture by value
    auto multiply_by_value = [multiplier](int x) { 
        return x * multiplier; 
    };
    
    // Capture by reference
    auto multiply_by_ref = [&multiplier](int x) { 
        multiplier = 3;  // Can modify original
        return x * multiplier; 
    };
    
    // Capture all by value
    auto capture_all = [=](int x) { return x * multiplier; };
    
    // Capture all by reference
    auto capture_all_ref = [&](int x) { 
        multiplier = 4;
        return x * multiplier; 
    };
    
    std::cout << multiply_by_value(10);  // 20
    std::cout << multiply_by_ref(10);   // 30 (multiplier is now 3)
}
```

## Function Objects (Functors)

```cpp
class Multiply {
private:
    int factor_;
public:
    Multiply(int factor) : factor_(factor) {}
    
    int operator()(int x) const {
        return x * factor_;
    }
};

int main() {
    Multiply multiply_by_3(3);
    int result = multiply_by_3(5);  // 15
    
    // Use with algorithms
    std::vector<int> numbers = {1, 2, 3, 4, 5};
    std::transform(numbers.begin(), numbers.end(), numbers.begin(), multiply_by_3);
    
    return 0;
}
```

## std::function (C++11)

```cpp
#include <functional>

int add(int a, int b) {
    return a + b;
}

class Calculator {
public:
    int multiply(int a, int b) {
        return a * b;
    }
};

int main() {
    // Store different callable types
    std::function<int(int, int)> operation;
    
    // Free function
    operation = add;
    std::cout << operation(5, 3);  // 8
    
    // Lambda
    operation = [](int a, int b) { return a - b; };
    std::cout << operation(5, 3);  // 2
    
    // Member function
    Calculator calc;
    operation = std::bind(&Calculator::multiply, &calc, 
                         std::placeholders::_1, std::placeholders::_2);
    std::cout << operation(5, 3);  // 15
    
    return 0;
}
```

## Return Type Deduction (C++14)

```cpp
auto add(int a, int b) {  // Return type deduced as int
    return a + b;
}

auto get_value() {  // Return type deduced as int
    return 42;
}

// Trailing return type (C++11)
auto divide(double a, double b) -> double {
    return a / b;
}
```

## constexpr Functions (C++11)

```cpp
constexpr int factorial(int n) {
    return (n <= 1) ? 1 : n * factorial(n - 1);
}

constexpr int result = factorial(5);  // Evaluated at compile time

// constexpr with if constexpr (C++17)
constexpr bool is_even(int n) {
    if constexpr (n % 2 == 0) {
        return true;
    } else {
        return false;
    }
}
```

## noexcept Specification

```cpp
void safe_function() noexcept {
    // Function promises not to throw exceptions
}

int divide(int a, int b) noexcept(noexcept(b != 0)) {
    return (b != 0) ? a / b : throw std::runtime_error("Division by zero");
}
```

## Function Pointers

```cpp
int add(int a, int b) {
    return a + b;
}

int multiply(int a, int b) {
    return a * b;
}

int main() {
    // Function pointer
    int (*operation)(int, int);
    
    operation = add;
    std::cout << operation(5, 3);  // 8
    
    operation = multiply;
    std::cout << operation(5, 3);  // 15
    
    // Using std::function (modern approach)
    std::function<int(int, int)> modern_op = add;
    std::cout << modern_op(5, 3);  // 8
    
    return 0;
}
```

## Variadic Functions

### C-style Variadic Functions
```cpp
#include <cstdarg>

int sum(int count, ...) {
    va_list args;
    va_start(args, count);
    
    int total = 0;
    for (int i = 0; i < count; ++i) {
        total += va_arg(args, int);
    }
    
    va_end(args);
    return total;
}

int main() {
    int result = sum(4, 1, 2, 3, 4);  // 10
    return 0;
}
```

### Variadic Templates (C++11)
```cpp
template<typename... Args>
void print(Args... args) {
    ((std::cout << args << " "), ...);  // Fold expression (C++17)
    std::cout << std::endl;
}

template<typename... Args>
auto sum(Args... args) {
    return (args + ...);  // Fold expression (C++17)
}

int main() {
    print(1, 2.5, "hello");  // 1 2.5 hello
    auto total = sum(1, 2, 3, 4);  // 10
    return 0;
}
```

## Best Practices
- Use `const` and `&` for parameters when possible
- Prefer `constexpr` for compile-time computations
- Use inline functions for small, frequently called functions
- Use templates for generic algorithms
- Prefer lambda expressions over function objects when simple
- Use `std::function` for type erasure when needed
- Specify `noexcept` when functions don't throw
- Use auto for return type deduction when clear
- Keep functions short and focused
- Use meaningful function names and parameter names
