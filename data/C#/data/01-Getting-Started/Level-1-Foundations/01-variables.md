# Variables in C#

Variables are containers for storing data values. In C#, you must declare a variable before using it.

## Declaration and Initialization

```csharp
// Declaration
int age;
string name;
double salary;
bool isActive;

// Initialization
age = 25;
name = "John Doe";
salary = 50000.50;
isActive = true;

// Declaration and initialization in one line
int score = 100;
string city = "New York";
```

## Data Types

### Value Types
- `int` - Whole numbers (-2,147,483,648 to 2,147,483,647)
- `double` - Floating-point numbers (64-bit)
- `float` - Floating-point numbers (32-bit)
- `decimal` - Precise decimal numbers (128-bit)
- `bool` - Boolean values (true/false)
- `char` - Single character
- `DateTime` - Date and time values

### Reference Types
- `string` - Text values
- `object` - Base type for all types
- Arrays, Classes, and more

## Variable Naming Rules

1. Must start with a letter or underscore
2. Can contain letters, numbers, and underscores
3. Cannot use C# keywords
4. Use camelCase for local variables
5. Use PascalCase for public properties

## Constants

Use `const` for variables that never change:

```csharp
const double PI = 3.14159;
const int MAX_ATTEMPTS = 3;
```

## Best Practices

- Choose meaningful names
- Initialize variables when possible
- Use appropriate data types
- Keep variable scope as small as possible
