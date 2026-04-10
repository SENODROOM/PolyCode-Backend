# C++ Control Flow

## Conditional Statements

### if Statement
```cpp
#include <iostream>

int main() {
    int age = 25;
    
    if (age >= 18) {
        std::cout << "You are an adult" << std::endl;
    }
    
    return 0;
}
```

### if-else Statement
```cpp
int main() {
    int score = 85;
    
    if (score >= 60) {
        std::cout << "Pass" << std::endl;
    } else {
        std::cout << "Fail" << std::endl;
    }
    
    return 0;
}
```

### if-else if-else Ladder
```cpp
int main() {
    int grade = 85;
    
    if (grade >= 90) {
        std::cout << "A" << std::endl;
    } else if (grade >= 80) {
        std::cout << "B" << std::endl;
    } else if (grade >= 70) {
        std::cout << "C" << std::endl;
    } else if (grade >= 60) {
        std::cout << "D" << std::endl;
    } else {
        std::cout << "F" << std::endl;
    }
    
    return 0;
}
```

### Nested if Statements
```cpp
int main() {
    int age = 25;
    bool has_license = true;
    
    if (age >= 18) {
        if (has_license) {
            std::cout << "You can drive" << std::endl;
        } else {
            std::cout << "You need a license" << std::endl;
        }
    } else {
        std::cout << "You are too young to drive" << std::endl;
    }
    
    return 0;
}
```

## switch Statement

### Basic switch
```cpp
int main() {
    int day = 3;
    
    switch (day) {
        case 1:
            std::cout << "Monday" << std::endl;
            break;
        case 2:
            std::cout << "Tuesday" << std::endl;
            break;
        case 3:
            std::cout << "Wednesday" << std::endl;
            break;
        case 4:
            std::cout << "Thursday" << std::endl;
            break;
        case 5:
            std::cout << "Friday" << std::endl;
            break;
        default:
            std::cout << "Weekend" << std::endl;
            break;
    }
    
    return 0;
}
```

### switch with Multiple Cases
```cpp
int main() {
    char grade = 'B';
    
    switch (grade) {
        case 'A':
        case 'a':
            std::cout << "Excellent" << std::endl;
            break;
        case 'B':
        case 'b':
            std::cout << "Good" << std::endl;
            break;
        case 'C':
        case 'c':
            std::cout << "Average" << std::endl;
            break;
        case 'F':
        case 'f':
            std::cout << "Fail" << std::endl;
            break;
        default:
            std::cout << "Invalid grade" << std::endl;
            break;
    }
    
    return 0;
}
```

### switch with Enum
```cpp
enum class Color { RED, GREEN, BLUE, YELLOW };

std::string get_color_name(Color color) {
    switch (color) {
        case Color::RED:    return "Red";
        case Color::GREEN:  return "Green";
        case Color::BLUE:   return "Blue";
        case Color::YELLOW: return "Yellow";
        default:            return "Unknown";
    }
}
```

## Loops

### for Loop
```cpp
int main() {
    // Traditional for loop
    for (int i = 1; i <= 10; i++) {
        std::cout << i << " ";
    }
    std::cout << std::endl;
    
    // Range-based for loop (C++11)
    std::vector<int> numbers = {1, 2, 3, 4, 5};
    for (int num : numbers) {
        std::cout << num << " ";
    }
    std::cout << std::endl;
    
    // Range-based for loop with auto (C++11)
    for (auto num : numbers) {
        std::cout << num << " ";
    }
    std::cout << std::endl;
    
    // Range-based for loop with reference (C++11)
    for (auto& num : numbers) {
        num *= 2;  // Modify elements
    }
    
    return 0;
}
```

### while Loop
```cpp
int main() {
    int count = 1;
    
    while (count <= 5) {
        std::cout << "Count: " << count << std::endl;
        count++;
    }
    
    // Input validation loop
    int number;
    std::cout << "Enter a positive number: ";
    std::cin >> number;
    
    while (number <= 0) {
        std::cout << "Invalid! Enter a positive number: ";
        std::cin >> number;
    }
    
    return 0;
}
```

### do-while Loop
```cpp
int main() {
    int choice;
    
    do {
        std::cout << "1. Option 1" << std::endl;
        std::cout << "2. Option 2" << std::endl;
        std::cout << "3. Exit" << std::endl;
        std::cout << "Enter choice: ";
        std::cin >> choice;
        
        switch (choice) {
            case 1:
                std::cout << "You chose option 1" << std::endl;
                break;
            case 2:
                std::cout << "You chose option 2" << std::endl;
                break;
            case 3:
                std::cout << "Exiting..." << std::endl;
                break;
            default:
                std::cout << "Invalid choice" << std::endl;
        }
    } while (choice != 3);
    
    return 0;
}
```

## Jump Statements

### break Statement
```cpp
int main() {
    // Break from loop
    for (int i = 1; i <= 10; i++) {
        if (i == 6) {
            break;  // Exit loop when i equals 6
        }
        std::cout << i << " ";  // Prints: 1 2 3 4 5
    }
    std::cout << std::endl;
    
    // Break from switch
    int day = 3;
    switch (day) {
        case 1:
            std::cout << "Monday" << std::endl;
            break;
        case 2:
            std::cout << "Tuesday" << std::endl;
            break;
        case 3:
            std::cout << "Wednesday" << std::endl;
            break;  // Exit switch
    }
    
    return 0;
}
```

### continue Statement
```cpp
int main() {
    // Skip even numbers
    for (int i = 1; i <= 10; i++) {
        if (i % 2 == 0) {
            continue;  // Skip to next iteration
        }
        std::cout << i << " ";  // Prints: 1 3 5 7 9
    }
    std::cout << std::endl;
    
    // Process only positive numbers
    std::vector<int> numbers = {-2, -1, 0, 1, 2, 3};
    int positive_count = 0;
    
    for (int num : numbers) {
        if (num <= 0) {
            continue;
        }
        positive_count++;
        std::cout << "Positive: " << num << std::endl;
    }
    
    std::cout << "Total positive: " << positive_count << std::endl;
    
    return 0;
}
```

### goto Statement (Use sparingly)
```cpp
int main() {
    int i = 1;
    
start:
    std::cout << i << " ";
    i++;
    
    if (i <= 5) {
        goto start;
    }
    std::cout << std::endl;
    
    // Practical use: error handling
    FILE* file = fopen("data.txt", "r");
    if (file == nullptr) {
        goto error;
    }
    
    // File operations...
    
    fclose(file);
    return 0;
    
error:
    std::cerr << "Error opening file" << std::endl;
    return 1;
}
```

## Modern C++ Control Flow

### if with Initializer (C++17)
```cpp
int main() {
    // Traditional way
    std::map<int, std::string> data = {{1, "one"}, {2, "two"}};
    auto it = data.find(1);
    if (it != data.end()) {
        std::cout << "Found: " << it->second << std::endl;
    }
    
    // C++17 way
    if (auto it = data.find(1); it != data.end()) {
        std::cout << "Found: " << it->second << std::endl;
    }
    
    // With switch
    switch (int value = 42; value) {
        case 42:
            std::cout << "The answer" << std::endl;
            break;
        default:
            std::cout << "Something else" << std::endl;
    }
    
    return 0;
}
```

### std::optional and Control Flow (C++17)
```cpp
#include <optional>

std::optional<int> divide(int a, int b) {
    if (b == 0) {
        return std::nullopt;
    }
    return a / b;
}

int main() {
    auto result = divide(10, 2);
    
    if (result) {
        std::cout << "Result: " << *result << std::endl;
    } else {
        std::cout << "Division by zero" << std::endl;
    }
    
    // Using value_or
    int safe_result = divide(10, 0).value_or(-1);
    std::cout << "Safe result: " << safe_result << std::endl;
    
    return 0;
}
```

### std::variant and Control Flow (C++17)
```cpp
#include <variant>
#include <string>

std::variant<int, std::string, double> get_data(int type) {
    switch (type) {
        case 0: return 42;
        case 1: return std::string("hello");
        case 2: return 3.14;
        default: return 0;
    }
}

int main() {
    auto data = get_data(1);
    
    // Using std::visit
    std::visit([](auto&& arg) {
        std::cout << "Value: " << arg << std::endl;
    }, data);
    
    // Using holds_alternative
    if (std::holds_alternative<std::string>(data)) {
        std::cout << "String value: " << std::get<std::string>(data) << std::endl;
    }
    
    return 0;
}
```

## Structured Bindings (C++17)

```cpp
#include <tuple>
#include <map>

std::pair<int, std::string> get_person() {
    return {25, "John"};
}

int main() {
    // With pair
    auto [age, name] = get_person();
    std::cout << name << " is " << age << " years old" << std::endl;
    
    // With map iteration
    std::map<int, std::string> students = {{1, "Alice"}, {2, "Bob"}};
    
    for (const auto& [id, name] : students) {
        std::cout << "ID: " << id << ", Name: " << name << std::endl;
    }
    
    // With array
    int coordinates[3] = {10, 20, 30};
    auto [x, y, z] = coordinates;
    
    return 0;
}
```

## Best Practices
- Use range-based for loops when possible (C++11+)
- Prefer `if` with initializer for cleaner code (C++17)
- Use `std::optional` for functions that may not return a value (C++17)
- Avoid `goto` except for error handling in C-style code
- Use `break` and `continue` to make loops more readable
- Consider `std::variant` for type-safe alternatives (C++17)
- Use structured bindings to unpack values (C++17)
- Keep control flow simple and readable
- Use meaningful variable names in conditions
- Consider early returns to reduce nesting
