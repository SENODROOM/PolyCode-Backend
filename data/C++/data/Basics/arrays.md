# C++ Arrays

## C-style Arrays

### Basic Declaration and Initialization
```cpp
#include <iostream>

int main() {
    // Declaration
    int numbers[5];
    
    // Initialization
    int scores[5] = {85, 92, 78, 96, 88};
    
    // Partial initialization (rest are 0)
    int partial[5] = {1, 2, 3};  // {1, 2, 3, 0, 0}
    
    // Size inference
    int auto_sized[] = {1, 2, 3, 4, 5};
    
    // Accessing elements
    std::cout << scores[0];        // 85
    scores[1] = 95;          // Modify element
    
    return 0;
}
```

### Multi-dimensional Arrays
```cpp
int main() {
    // 2D array
    int matrix[3][4] = {
        {1, 2, 3, 4},
        {5, 6, 7, 8},
        {9, 10, 11, 12}
    };
    
    // Accessing elements
    std::cout << matrix[1][2];  // 7
    
    // 3D array
    int cube[2][3][4] = {
        {{1, 2, 3, 4}, {5, 6, 7, 8}, {9, 10, 11, 12}},
        {{13, 14, 15, 16}, {17, 18, 19, 20}, {21, 22, 23, 24}}
    };
    
    return 0;
}
```

### Array Iteration
```cpp
int main() {
    int numbers[] = {1, 2, 3, 4, 5};
    
    // Traditional for loop
    for (int i = 0; i < 5; i++) {
        std::cout << numbers[i] << " ";
    }
    
    // Range-based for loop (C++11)
    for (int num : numbers) {
        std::cout << num << " ";
    }
    
    // Range-based for loop with reference (C++11)
    for (int& num : numbers) {
        num *= 2;  // Modify elements
    }
    
    return 0;
}
```

### Array Size
```cpp
#include <cstddef>  // for std::size_t

int main() {
    int numbers[] = {1, 2, 3, 4, 5};
    
    // C-style size calculation
    size_t size = sizeof(numbers) / sizeof(numbers[0]);
    
    // C++17 way with std::size
    size_t size_cpp17 = std::size(numbers);
    
    // Using range-based for loop (no size needed)
    for (int num : numbers) {
        std::cout << num << " ";
    }
    
    return 0;
}
```

## std::array (C++11)

### Basic Usage
```cpp
#include <array>
#include <iostream>

int main() {
    // Fixed-size array with type safety
    std::array<int, 5> numbers = {1, 2, 3, 4, 5};
    
    // Access elements
    std::cout << numbers[0];        // 1
    std::cout << numbers.at(0);      // 1 (with bounds checking)
    
    // Size
    std::cout << numbers.size();    // 5
    
    // Iteration
    for (int num : numbers) {
        std::cout << num << " ";
    }
    
    return 0;
}
```

### std::array Operations
```cpp
#include <algorithm>
#include <array>

int main() {
    std::array<int, 5> numbers = {3, 1, 4, 1, 5};
    
    // Fill
    numbers.fill(0);  // All elements become 0
    
    // Sort
    std::sort(numbers.begin(), numbers.end());
    
    // Find
    auto it = std::find(numbers.begin(), numbers.end(), 3);
    
    // Reverse
    std::reverse(numbers.begin(), numbers.end());
    
    // Min/Max
    auto min_val = std::min_element(numbers.begin(), numbers.end());
    auto max_val = std::max_element(numbers.begin(), numbers.end());
    
    return 0;
}
```

## std::vector (Dynamic Array)

### Basic Usage
```cpp
#include <vector>
#include <iostream>

int main() {
    // Create vector
    std::vector<int> numbers;
    
    // Add elements
    numbers.push_back(1);
    numbers.push_back(2);
    numbers.push_back(3);
    
    // Access elements
    std::cout << numbers[0];        // 1
    std::cout << numbers.at(0);      // 1 (with bounds checking)
    
    // Size and capacity
    std::cout << numbers.size();     // 3
    std::cout << numbers.capacity(); // >= 3
    
    // Initialize with size
    std::vector<int> sized(10, 0);   // 10 elements, all 0
    
    // Initialize with list
    std::vector<int> init = {1, 2, 3, 4, 5};
    
    return 0;
}
```

### Vector Operations
```cpp
#include <vector>
#include <algorithm>

int main() {
    std::vector<int> numbers = {1, 2, 3};
    
    // Insert
    numbers.insert(numbers.begin() + 1, 10);  // {1, 10, 2, 3}
    
    // Erase
    numbers.erase(numbers.begin() + 1);       // {1, 2, 3}
    
    // Remove from end
    numbers.pop_back();                        // {1, 2}
    
    // Clear all
    numbers.clear();                           // {}
    
    // Resize
    numbers.resize(5, 0);                      // {0, 0, 0, 0, 0}
    
    // Reserve capacity
    numbers.reserve(100);                      // Capacity >= 100
    
    // Check if empty
    if (numbers.empty()) {
        std::cout << "Vector is empty" << std::endl;
    }
    
    return 0;
}
```

## Advanced Array Operations

### Array Algorithms
```cpp
#include <vector>
#include <algorithm>
#include <numeric>

int main() {
    std::vector<int> numbers = {1, 2, 3, 4, 5};
    
    // Sum
    int sum = std::accumulate(numbers.begin(), numbers.end(), 0);
    
    // Product
    int product = std::accumulate(numbers.begin(), numbers.end(), 1, 
                                  std::multiplies<int>());
    
    // Count
    int count = std::count(numbers.begin(), numbers.end(), 3);
    
    // Find if any element satisfies condition
    bool has_even = std::any_of(numbers.begin(), numbers.end(),
                                [](int x) { return x % 2 == 0; });
    
    // Transform
    std::vector<int> doubled;
    std::transform(numbers.begin(), numbers.end(),
                   std::back_inserter(doubled),
                   [](int x) { return x * 2; });
    
    // Filter
    std::vector<int> evens;
    std::copy_if(numbers.begin(), numbers.end(),
                std::back_inserter(evens),
                [](int x) { return x % 2 == 0; });
    
    return 0;
}
```

### 2D Vectors
```cpp
#include <vector>

int main() {
    // 2D vector
    std::vector<std::vector<int>> matrix = {
        {1, 2, 3},
        {4, 5, 6},
        {7, 8, 9}
    };
    
    // Access elements
    int element = matrix[1][2];  // 6
    
    // Add row
    matrix.push_back({10, 11, 12});
    
    // Iterate
    for (const auto& row : matrix) {
        for (int val : row) {
            std::cout << val << " ";
        }
        std::cout << std::endl;
    }
    
    // Resize matrix
    matrix.resize(4, std::vector<int>(3, 0));  // 4x3 matrix
    
    return 0;
}
```

## Array Views (C++20)

### std::span
```cpp
#include <span>
#include <vector>

void process_array(std::span<int> arr) {
    for (int& val : arr) {
        val *= 2;
    }
}

int main() {
    std::vector<int> vec = {1, 2, 3, 4, 5};
    int c_array[] = {10, 20, 30};
    
    // Pass vector as span
    process_array(vec);
    
    // Pass C-array as span
    process_array(c_array);
    
    // Create sub-span
    std::span<int> sub(vec.begin() + 1, vec.begin() + 4);
    
    return 0;
}
```

## Performance Considerations

### Reserve vs Resize
```cpp
#include <vector>

int main() {
    // Reserve capacity without changing size
    std::vector<int> vec;
    vec.reserve(1000);  // Allocate memory for 1000 elements
    
    // Resize changes size and initializes elements
    std::vector<int> vec2(1000, 0);  // 1000 elements, all 0
    
    // Efficient pattern for known size
    std::vector<int> efficient;
    efficient.reserve(1000);
    for (int i = 0; i < 1000; ++i) {
        efficient.push_back(i);  // No reallocations
    }
    
    return 0;
}
```

### Move Semantics with Arrays
```cpp
#include <vector>
#include <utility>

std::vector<int> create_large_vector() {
    std::vector<int> vec(1000000, 42);
    return vec;  // Move semantics (C++11+)
}

int main() {
    // Efficient move instead of copy
    std::vector<int> my_vec = create_large_vector();
    
    // Explicit move
    std::vector<int> another_vec = std::move(my_vec);
    
    return 0;
}
```

## Best Practices
- Prefer `std::vector` over C-style arrays for dynamic arrays
- Use `std::array` for fixed-size arrays (C++11+)
- Use range-based for loops for iteration (C++11+)
- Use `reserve()` when you know the final size
- Use `at()` for bounds checking in debug builds
- Prefer algorithms over manual loops when possible
- Use `std::span` for array views (C++20)
- Consider move semantics for large arrays (C++11+)
- Use `std::vector<bool>` only when memory is critical
- Be aware of iterator invalidation when modifying containers
