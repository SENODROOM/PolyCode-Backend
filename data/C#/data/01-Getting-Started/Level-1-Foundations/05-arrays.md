# Arrays in C#

Arrays are data structures that store multiple values of the same type in a contiguous memory location.

## Single-Dimensional Arrays

### Declaration and Initialization

```csharp
// Declaration only
int[] numbers;

// Declaration and initialization with size
int[] scores = new int[5];

// Declaration and initialization with values
int[] ages = { 25, 30, 35, 40, 45 };

// Using new keyword with values
string[] names = new string[] { "Alice", "Bob", "Charlie" };
```

### Accessing Array Elements

```csharp
int[] numbers = { 10, 20, 30, 40, 50 };

// Access elements using index (0-based)
int first = numbers[0];  // 10
int last = numbers[4];   // 50

// Modify elements
numbers[2] = 35;

// Get array length
int length = numbers.Length; // 5
```

### Iterating Through Arrays

```csharp
int[] numbers = { 1, 2, 3, 4, 5 };

// Using for loop
for (int i = 0; i < numbers.Length; i++)
{
    Console.WriteLine(numbers[i]);
}

// Using foreach loop
foreach (int num in numbers)
{
    Console.WriteLine(num);
}
```

## Multi-Dimensional Arrays

### Two-Dimensional Arrays

```csharp
// Declaration
int[,] matrix = new int[3, 3];

// Initialization
int[,] grid = { 
    { 1, 2, 3 }, 
    { 4, 5, 6 }, 
    { 7, 8, 9 } 
};

// Accessing elements
int value = grid[1, 2]; // 6 (row 1, column 2)

// Iterating
for (int i = 0; i < grid.GetLength(0); i++)
{
    for (int j = 0; j < grid.GetLength(1); j++)
    {
        Console.Write(grid[i, j] + " ");
    }
    Console.WriteLine();
}
```

### Three-Dimensional Arrays

```csharp
int[,,] cube = new int[2, 2, 2];
int[,,] threeD = { 
    { { 1, 2 }, { 3, 4 } }, 
    { { 5, 6 }, { 7, 8 } } 
};
```

## Jagged Arrays

Jagged arrays are arrays of arrays where each sub-array can have different lengths.

```csharp
// Declaration
int[][] jagged = new int[3][];

// Initialization
jagged[0] = new int[] { 1, 2 };
jagged[1] = new int[] { 3, 4, 5 };
jagged[2] = new int[] { 6 };

// Alternative initialization
int[][] jagged2 = new int[][]
{
    new int[] { 1, 2 },
    new int[] { 3, 4, 5 },
    new int[] { 6 }
};

// Accessing elements
int value = jagged[1][2]; // 5
```

## Common Array Operations

### Sorting

```csharp
int[] numbers = { 5, 2, 8, 1, 9 };
Array.Sort(numbers); // { 1, 2, 5, 8, 9 }
```

### Reversing

```csharp
int[] numbers = { 1, 2, 3, 4, 5 };
Array.Reverse(numbers); // { 5, 4, 3, 2, 1 }
```

### Searching

```csharp
int[] numbers = { 10, 20, 30, 40, 50 };
int index = Array.IndexOf(numbers, 30); // 2
bool exists = Array.Exists(numbers, x => x > 25); // true
```

### Copying

```csharp
int[] source = { 1, 2, 3, 4, 5 };
int[] destination = new int[5];
Array.Copy(source, destination, 5);
```

## Array Properties and Methods

- `Length` - Total number of elements
- `Rank` - Number of dimensions
- `GetLength(dimension)` - Length of specific dimension
- `Sort()` - Sorts the array
- `Reverse()` - Reverses the array
- `IndexOf()` - Finds index of element
- `Clear()` - Sets elements to default values

## Best Practices

- Use arrays when you know the exact number of elements
- Consider `List<T>` for dynamic collections
- Be careful with array bounds (IndexOutOfRangeException)
- Use meaningful variable names for arrays
- Initialize arrays when possible to avoid null references
