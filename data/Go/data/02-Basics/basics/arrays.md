# Go Arrays and Slices

## Arrays

### Array Declaration and Initialization
```go
package main

import "fmt"

func main() {
    // Array declaration with specified size
    var numbers [5]int
    fmt.Printf("Array of 5 integers: %v\n", numbers)
    fmt.Printf("Array length: %d\n", len(numbers))
    
    // Array initialization
    var primes = [5]int{2, 3, 5, 7, 11}
    fmt.Printf("Prime numbers: %v\n", primes)
    
    // Array with ellipsis (compiler determines size)
    fruits := [...]string{"apple", "banana", "cherry"}
    fmt.Printf("Fruits: %v\n", fruits)
    fmt.Printf("Fruits length: %d\n", len(fruits))
    
    // Array with specific index initialization
    var scores [5]int
    scores[0] = 95
    scores[1] = 87
    scores[2] = 92
    scores[3] = 78
    scores[4] = 88
    fmt.Printf("Scores: %v\n", scores)
    
    // Array initialization with index values
    temperatures := [7]float64{0: 72.5, 1: 73.2, 6: 71.8}
    fmt.Printf("Temperatures: %v\n", temperatures)
    
    // Array of structs
    type Person struct {
        Name string
        Age  int
    }
    
    people := [3]Person{
        {"Alice", 25},
        {"Bob", 30},
        {"Carol", 28},
    }
    fmt.Printf("People: %v\n", people)
    
    // Multi-dimensional arrays
    var matrix [3][3]int
    matrix[0] = [3]int{1, 2, 3}
    matrix[1] = [3]int{4, 5, 6}
    matrix[2] = [3]int{7, 8, 9}
    fmt.Printf("Matrix: %v\n", matrix)
    
    // Accessing array elements
    fmt.Printf("First prime: %d\n", primes[0])
    fmt.Printf("Last prime: %d\n", primes[len(primes)-1])
    
    // Modifying array elements
    primes[0] = 13
    fmt.Printf("Modified primes: %v\n", primes)
    
    // Arrays are value types
    original := [3]int{1, 2, 3}
    copy := original
    copy[0] = 99
    fmt.Printf("Original: %v\n", original)
    fmt.Printf("Copy: %v\n", copy)
    
    // Comparing arrays
    arr1 := [3]int{1, 2, 3}
    arr2 := [3]int{1, 2, 3}
    arr3 := [3]int{1, 2, 4}
    
    fmt.Printf("arr1 == arr2: %t\n", arr1 == arr2)
    fmt.Printf("arr1 == arr3: %t\n", arr1 == arr3)
    
    // Array iteration
    fmt.Println("Iterating over array:")
    for i, value := range primes {
        fmt.Printf("Index %d: %d\n", i, value)
    }
    
    // Array with pointers
    var ptrArray [3]*int
    a, b, c := 10, 20, 30
    ptrArray[0] = &a
    ptrArray[1] = &b
    ptrArray[2] = &c
    
    fmt.Printf("Pointer array: %v\n", ptrArray)
    for i, ptr := range ptrArray {
        fmt.Printf("Index %d: %d (address: %p)\n", i, *ptr, ptr)
    }
}
```

### Array Operations
```go
package main

import "fmt"

func main() {
    // Array operations
    numbers := [5]int{10, 20, 30, 40, 50}
    
    // Finding elements
    target := 30
    var foundIndex int = -1
    
    for i, num := range numbers {
        if num == target {
            foundIndex = i
            break
        }
    }
    
    if foundIndex != -1 {
        fmt.Printf("Found %d at index %d\n", target, foundIndex)
    } else {
        fmt.Printf("%d not found\n", target)
    }
    
    // Summing array elements
    sum := 0
    for _, num := range numbers {
        sum += num
    }
    fmt.Printf("Sum: %d\n", sum)
    
    // Finding maximum and minimum
    max := numbers[0]
    min := numbers[0]
    
    for _, num := range numbers {
        if num > max {
            max = num
        }
        if num < min {
            min = num
        }
    }
    
    fmt.Printf("Max: %d, Min: %d\n", max, min)
    
    // Reversing array
    var reversed [5]int
    for i, num := range numbers {
        reversed[len(numbers)-1-i] = num
    }
    fmt.Printf("Original: %v\n", numbers)
    fmt.Printf("Reversed: %v\n", reversed)
    
    // In-place reversal
    for i, j := 0, len(numbers)-1; i < j; i, j = i+1, j-1 {
        numbers[i], numbers[j] = numbers[j], numbers[i]
    }
    fmt.Printf("Reversed in-place: %v\n", numbers)
    
    // Array sorting (bubble sort)
    unsorted := [5]int{64, 34, 25, 12, 22}
    n := len(unsorted)
    
    for i := 0; i < n-1; i++ {
        for j := 0; j < n-i-1; j++ {
            if unsorted[j] > unsorted[j+1] {
                unsorted[j], unsorted[j+1] = unsorted[j+1], unsorted[j]
            }
        }
    }
    fmt.Printf("Sorted: %v\n", unsorted)
    
    // Array filtering
    numbers2 := [10]int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    var evenNumbers [5]int
    evenIndex := 0
    
    for _, num := range numbers2 {
        if num%2 == 0 {
            evenNumbers[evenIndex] = num
            evenIndex++
        }
    }
    
    fmt.Printf("Even numbers: %v\n", evenNumbers[:evenIndex])
    
    // Array copying
    source := [4]int{1, 2, 3, 4}
    var destination [4]int
    
    for i := 0; i < len(source); i++ {
        destination[i] = source[i]
    }
    
    fmt.Printf("Source: %v\n", source)
    fmt.Printf("Destination: %v\n", destination)
    
    // Array comparison
    arr1 := [3]int{1, 2, 3}
    arr2 := [3]int{1, 2, 3}
    
    if arr1 == arr2 {
        fmt.Println("Arrays are equal")
    } else {
        fmt.Println("Arrays are not equal")
    }
    
    // Array as function parameter
    result := sumArray(numbers)
    fmt.Printf("Sum via function: %d\n", result)
    
    // Multi-dimensional array operations
    matrix := [3][3]int{
        {1, 2, 3},
        {4, 5, 6},
        {7, 8, 9},
    }
    
    // Matrix diagonal sum
    diagSum := 0
    for i := 0; i < len(matrix); i++ {
        diagSum += matrix[i][i]
    }
    fmt.Printf("Diagonal sum: %d\n", diagSum)
    
    // Matrix transpose
    var transposed [3][3]int
    for i := 0; i < len(matrix); i++ {
        for j := 0; j < len(matrix[i]); j++ {
            transposed[j][i] = matrix[i][j]
        }
    }
    
    fmt.Printf("Original matrix: %v\n", matrix)
    fmt.Printf("Transposed matrix: %v\n", transposed)
}

func sumArray(arr [5]int) int {
    sum := 0
    for _, num := range arr {
        sum += num
    }
    return sum
}
```

## Slices

### Slice Creation and Operations
```go
package main

import "fmt"

func main() {
    // Creating slices from arrays
    array := [5]int{10, 20, 30, 40, 50}
    slice := array[1:4] // Elements from index 1 to 3
    fmt.Printf("Slice: %v\n", slice)
    fmt.Printf("Slice length: %d\n", len(slice))
    fmt.Printf("Slice capacity: %d\n", cap(slice))
    
    // Slice literals
    fruits := []string{"apple", "banana", "cherry", "date"}
    fmt.Printf("Fruits slice: %v\n", fruits)
    
    // Empty slice
    var emptySlice []int
    fmt.Printf("Empty slice: %v (nil: %t)\n", emptySlice, emptySlice == nil)
    
    // Slice with make
    numbers := make([]int, 5) // Length 5, capacity 5
    fmt.Printf("Numbers slice: %v\n", numbers)
    
    // Slice with make and capacity
    largeSlice := make([]int, 3, 10) // Length 3, capacity 10
    fmt.Printf("Large slice: %v (len: %d, cap: %d)\n", largeSlice, len(largeSlice), cap(largeSlice))
    
    // Slice operations
    data := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    
    // Slicing
    subSlice := data[2:6] // Elements 3, 4, 5, 6
    fmt.Printf("Sub-slice: %v\n", subSlice)
    
    // Slicing with omitted bounds
    firstThree := data[:3] // First 3 elements
    lastThree := data[7:] // Last 3 elements
    allElements := data[:] // All elements
    
    fmt.Printf("First three: %v\n", firstThree)
    fmt.Printf("Last three: %v\n", lastThree)
    fmt.Printf("All elements: %v\n", allElements)
    
    // Appending to slice
    slice1 := []int{1, 2, 3}
    slice1 = append(slice1, 4, 5)
    fmt.Printf("After append: %v\n", slice1)
    
    // Appending another slice
    slice2 := []int{6, 7, 8}
    slice1 = append(slice1, slice2...)
    fmt.Printf("After appending slice: %v\n", slice1)
    
    // Copying slices
    src := []int{1, 2, 3, 4, 5}
    dst := make([]int, len(src))
    copy(dst, src)
    fmt.Printf("Source: %v\n", src)
    fmt.Printf("Destination: %v\n", dst)
    
    // Partial copy
    partialDst := make([]int, 3)
    copy(partialDst, src[1:4])
    fmt.Printf("Partial copy: %v\n", partialDst)
    
    // Slice deletion
    items := []int{10, 20, 30, 40, 50}
    
    // Delete element at index 2
    items = append(items[:2], items[3:]...)
    fmt.Printf("After deleting index 2: %v\n", items)
    
    // Delete first element
    items = items[1:]
    fmt.Printf("After deleting first element: %v\n", items)
    
    // Delete last element
    items = items[:len(items)-1]
    fmt.Printf("After deleting last element: %v\n", items)
    
    // Slice insertion
    numbers := []int{1, 2, 4, 5}
    
    // Insert 3 at index 2
    numbers = append(numbers[:2], append([]int{3}, numbers[2:]...)...)
    fmt.Printf("After inserting 3 at index 2: %v\n", numbers)
    
    // Slice iteration
    fruits = []string{"apple", "banana", "cherry", "date", "elderberry"}
    
    fmt.Println("Iterating with index:")
    for i, fruit := range fruits {
        fmt.Printf("Index %d: %s\n", i, fruit)
    }
    
    fmt.Println("Iterating without index:")
    for _, fruit := range fruits {
        fmt.Printf("Fruit: %s\n", fruit)
    }
    
    // Slice filtering
    numbers = []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    
    // Filter even numbers
    var evenNumbers []int
    for _, num := range numbers {
        if num%2 == 0 {
            evenNumbers = append(evenNumbers, num)
        }
    }
    fmt.Printf("Even numbers: %v\n", evenNumbers)
    
    // Slice transformation
    doubled := make([]int, len(numbers))
    for i, num := range numbers {
        doubled[i] = num * 2
    }
    fmt.Printf("Doubled numbers: %v\n", doubled)
    
    // Slice aggregation
    sum := 0
    for _, num := range numbers {
        sum += num
    }
    fmt.Printf("Sum: %d\n", sum)
    
    // Slice search
    target := 5
    var foundIndex int = -1
    
    for i, num := range numbers {
        if num == target {
            foundIndex = i
            break
        }
    }
    
    if foundIndex != -1 {
        fmt.Printf("Found %d at index %d\n", target, foundIndex)
    } else {
        fmt.Printf("%d not found\n", target)
    }
    
    // Slice sorting
    unsorted := []int{64, 34, 25, 12, 22, 11, 90}
    
    // Simple bubble sort
    n := len(unsorted)
    for i := 0; i < n-1; i++ {
        for j := 0; j < n-i-1; j++ {
            if unsorted[j] > unsorted[j+1] {
                unsorted[j], unsorted[j+1] = unsorted[j+1], unsorted[j]
            }
        }
    }
    
    fmt.Printf("Sorted slice: %v\n", unsorted)
    
    // Slice capacity growth
    var dynamicSlice []int
    
    for i := 0; i < 10; i++ {
        dynamicSlice = append(dynamicSlice, i)
        fmt.Printf("Length: %d, Capacity: %d\n", len(dynamicSlice), cap(dynamicSlice))
    }
    
    // Slice comparison
    slice1 := []int{1, 2, 3}
    slice2 := []int{1, 2, 3}
    slice3 := []int{1, 2, 4}
    
    fmt.Printf("slice1 == slice2: %t\n", equalSlices(slice1, slice2))
    fmt.Printf("slice1 == slice3: %t\n", equalSlices(slice1, slice3))
    
    // Slice as function parameter
    processSlice(numbers)
    
    // Slice of structs
    type Person struct {
        Name string
        Age  int
    }
    
    people := []Person{
        {"Alice", 25},
        {"Bob", 30},
        {"Carol", 28},
    }
    
    fmt.Printf("People slice: %v\n", people)
    
    // Modifying slice elements
    people[0].Age = 26
    fmt.Printf("After modification: %v\n", people)
}

func equalSlices(a, b []int) bool {
    if len(a) != len(b) {
        return false
    }
    
    for i := range a {
        if a[i] != b[i] {
            return false
        }
    }
    
    return true
}

func processSlice(slice []int) {
    fmt.Printf("Processing slice: %v\n", slice)
    
    // Modify slice (will affect original if capacity allows)
    slice[0] = 999
    fmt.Printf("Modified slice: %v\n", slice)
}
```

### Advanced Slice Operations
```go
package main

import "fmt"

func main() {
    // Advanced slice operations
    
    // 1. Slice partitioning
    numbers := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    
    even, odd := partition(numbers, func(x int) bool {
        return x%2 == 0
    })
    
    fmt.Printf("Even numbers: %v\n", even)
    fmt.Printf("Odd numbers: %v\n", odd)
    
    // 2. Slice grouping
    words := []string{"apple", "banana", "cherry", "date", "elderberry", "fig"}
    grouped := groupByLength(words)
    
    fmt.Printf("Grouped by length: %v\n", grouped)
    
    // 3. Slice flattening
    nested := [][]int{{1, 2}, {3, 4, 5}, {6, 7, 8, 9}}
    flattened := flatten(nested)
    
    fmt.Printf("Flattened: %v\n", flattened)
    
    // 4. Slice chunking
    large := []int{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    chunks := chunk(large, 3)
    
    fmt.Printf("Chunks of size 3: %v\n", chunks)
    
    // 5. Slice sliding window
    data := []int{1, 2, 3, 4, 5, 6, 7, 8}
    windows := slidingWindow(data, 3)
    
    fmt.Printf("Sliding windows of size 3: %v\n", windows)
    
    // 6. Slice unique elements
    withDuplicates := []int{1, 2, 2, 3, 4, 4, 5, 5, 5}
    unique := uniqueElements(withDuplicates)
    
    fmt.Printf("Unique elements: %v\n", unique)
    
    // 7. Slice intersection
    set1 := []int{1, 2, 3, 4, 5}
    set2 := []int{4, 5, 6, 7, 8}
    intersection := intersect(set1, set2)
    
    fmt.Printf("Intersection: %v\n", intersection)
    
    // 8. Slice union
    union := unionSets(set1, set2)
    fmt.Printf("Union: %v\n", union)
    
    // 9. Slice difference
    difference := difference(set1, set2)
    fmt.Printf("Difference (set1 - set2): %v\n", difference)
    
    // 10. Slice rotation
    original := []int{1, 2, 3, 4, 5}
    rotated := rotate(original, 2)
    
    fmt.Printf("Original: %v\n", original)
    fmt.Printf("Rotated right by 2: %v\n", rotated)
    
    rotatedLeft := rotate(original, -2)
    fmt.Printf("Rotated left by 2: %v\n", rotatedLeft)
    
    // 11. Slice reversal
    toReverse := []int{1, 2, 3, 4, 5}
    reversed := reverse(toReverse)
    
    fmt.Printf("Original: %v\n", toReverse)
    fmt.Printf("Reversed: %v\n", reversed)
    
    // 12. Slice palindrome check
    palindrome := []int{1, 2, 3, 2, 1}
    notPalindrome := []int{1, 2, 3, 4, 5}
    
    fmt.Printf("Is palindrome %v: %t\n", palindrome, isPalindrome(palindrome))
    fmt.Printf("Is palindrome %v: %t\n", notPalindrome, isPalindrome(notPalindrome))
    
    // 13. Slice binary search
    sorted := []int{1, 3, 5, 7, 9, 11, 13, 15}
    index := binarySearch(sorted, 7)
    
    fmt.Printf("Binary search for 7: index %d\n", index)
    
    // 14. Slice merging
    sorted1 := []int{1, 3, 5, 7}
    sorted2 := []int{2, 4, 6, 8}
    merged := mergeSorted(sorted1, sorted2)
    
    fmt.Printf("Merged sorted: %v\n", merged)
    
    // 15. Slice compression
    data := []int{1, 1, 2, 2, 2, 3, 4, 4, 5}
    compressed := compress(data)
    
    fmt.Printf("Original: %v\n", data)
    fmt.Printf("Compressed: %v\n", compressed)
}

// Helper functions for advanced operations

func partition(slice []int, predicate func(int) bool) ([]int, []int) {
    var trueSlice, falseSlice []int
    
    for _, item := range slice {
        if predicate(item) {
            trueSlice = append(trueSlice, item)
        } else {
            falseSlice = append(falseSlice, item)
        }
    }
    
    return trueSlice, falseSlice
}

func groupByLength(words []string) map[int][]string {
    groups := make(map[int][]string)
    
    for _, word := range words {
        length := len(word)
        groups[length] = append(groups[length], word)
    }
    
    return groups
}

func flatten(nested [][]int) []int {
    var result []int
    
    for _, inner := range nested {
        result = append(result, inner...)
    }
    
    return result
}

func chunk(slice []int, size int) [][]int {
    if size <= 0 {
        return [][]int{slice}
    }
    
    var chunks [][]int
    
    for i := 0; i < len(slice); i += size {
        end := i + size
        if end > len(slice) {
            end = len(slice)
        }
        chunks = append(chunks, slice[i:end])
    }
    
    return chunks
}

func slidingWindow(slice []int, windowSize int) [][]int {
    if windowSize > len(slice) || windowSize <= 0 {
        return [][]int{slice}
    }
    
    var windows [][]int
    
    for i := 0; i <= len(slice)-windowSize; i++ {
        window := make([]int, windowSize)
        copy(window, slice[i:i+windowSize])
        windows = append(windows, window)
    }
    
    return windows
}

func uniqueElements(slice []int) []int {
    seen := make(map[int]bool)
    var result []int
    
    for _, item := range slice {
        if !seen[item] {
            seen[item] = true
            result = append(result, item)
        }
    }
    
    return result
}

func intersect(slice1, slice2 []int) []int {
    set2 := make(map[int]bool)
    for _, item := range slice2 {
        set2[item] = true
    }
    
    var result []int
    for _, item := range slice1 {
        if set2[item] {
            result = append(result, item)
        }
    }
    
    return uniqueElements(result)
}

func unionSets(slice1, slice2 []int) []int {
    seen := make(map[int]bool)
    var result []int
    
    for _, item := range slice1 {
        if !seen[item] {
            seen[item] = true
            result = append(result, item)
        }
    }
    
    for _, item := range slice2 {
        if !seen[item] {
            seen[item] = true
            result = append(result, item)
        }
    }
    
    return result
}

func difference(slice1, slice2 []int) []int {
    set2 := make(map[int]bool)
    for _, item := range slice2 {
        set2[item] = true
    }
    
    var result []int
    for _, item := range slice1 {
        if !set2[item] {
            result = append(result, item)
        }
    }
    
    return result
}

func rotate(slice []int, positions int) []int {
    n := len(slice)
    if n == 0 {
        return slice
    }
    
    // Normalize positions
    positions = positions % n
    if positions < 0 {
        positions += n
    }
    
    rotated := make([]int, n)
    
    for i := 0; i < n; i++ {
        newPos := (i + positions) % n
        rotated[newPos] = slice[i]
    }
    
    return rotated
}

func reverse(slice []int) []int {
    reversed := make([]int, len(slice))
    
    for i, item := range slice {
        reversed[len(slice)-1-i] = item
    }
    
    return reversed
}

func isPalindrome(slice []int) bool {
    n := len(slice)
    
    for i := 0; i < n/2; i++ {
        if slice[i] != slice[n-1-i] {
            return false
        }
    }
    
    return true
}

func binarySearch(slice []int, target int) int {
    left, right := 0, len(slice)-1
    
    for left <= right {
        mid := left + (right-left)/2
        
        if slice[mid] == target {
            return mid
        } else if slice[mid] < target {
            left = mid + 1
        } else {
            right = mid - 1
        }
    }
    
    return -1
}

func mergeSorted(slice1, slice2 []int) []int {
    i, j := 0, 0
    var result []int
    
    for i < len(slice1) && j < len(slice2) {
        if slice1[i] <= slice2[j] {
            result = append(result, slice1[i])
            i++
        } else {
            result = append(result, slice2[j])
            j++
        }
    }
    
    // Add remaining elements
    result = append(result, slice1[i:]...)
    result = append(result, slice2[j:]...)
    
    return result
}

func compress(slice []int) []int {
    if len(slice) == 0 {
        return slice
    }
    
    compressed := []int{slice[0]}
    
    for i := 1; i < len(slice); i++ {
        if slice[i] != slice[i-1] {
            compressed = append(compressed, slice[i])
        }
    }
    
    return compressed
}
```

## Maps

### Map Creation and Operations
```go
package main

import "fmt"

func main() {
    // Map declaration and initialization
    var emptyMap map[string]int
    fmt.Printf("Empty map: %v (nil: %t)\n", emptyMap, emptyMap == nil)
    
    // Map with make
    ages := make(map[string]int)
    fmt.Printf("Ages map: %v\n", ages)
    
    // Map literal
    studentGrades := map[string]int{
        "Alice": 85,
        "Bob":   92,
        "Carol": 78,
        "David": 95,
    }
    fmt.Printf("Student grades: %v\n", studentGrades)
    
    // Adding elements
    ages["John"] = 25
    ages["Jane"] = 30
    fmt.Printf("Ages after adding: %v\n", ages)
    
    // Accessing elements
    aliceGrade := studentGrades["Alice"]
    fmt.Printf("Alice's grade: %d\n", aliceGrade)
    
    // Accessing non-existent key
    unknownGrade := studentGrades["Unknown"]
    fmt.Printf("Unknown grade: %d\n", unknownGrade) // Returns zero value
    
    // Checking if key exists
    grade, exists := studentGrades["Bob"]
    if exists {
        fmt.Printf("Bob's grade: %d\n", grade)
    } else {
        fmt.Println("Bob not found")
    }
    
    // Updating elements
    studentGrades["Alice"] = 88
    fmt.Printf("Alice's updated grade: %d\n", studentGrades["Alice"])
    
    // Deleting elements
    delete(studentGrades, "Carol")
    fmt.Printf("Grades after deleting Carol: %v\n", studentGrades)
    
    // Map iteration
    fmt.Println("Iterating over student grades:")
    for name, grade := range studentGrades {
        fmt.Printf("%s: %d\n", name, grade)
    }
    
    // Iterating over keys only
    fmt.Println("Student names:")
    for name := range studentGrades {
        fmt.Printf("%s\n", name)
    }
    
    // Iterating over values only
    fmt.Println("Grades:")
    for _, grade := range studentGrades {
        fmt.Printf("%d\n", grade)
    }
    
    // Map length
    fmt.Printf("Number of students: %d\n", len(studentGrades))
    
    // Map with different value types
    userInfo := map[string]interface{}{
        "name":  "John Doe",
        "age":   30,
        "email": "john@example.com",
        "active": true,
        "scores": []int{85, 92, 78},
    }
    
    fmt.Printf("User info: %v\n", userInfo)
    
    // Accessing different types
    if name, ok := userInfo["name"].(string); ok {
        fmt.Printf("Name: %s\n", name)
    }
    
    if scores, ok := userInfo["scores"].([]int); ok {
        fmt.Printf("Scores: %v\n", scores)
    }
    
    // Map of maps
    departments := map[string]map[string]int{
        "Engineering": {
            "developers": 10,
            "designers":  2,
            "managers":   1,
        },
        "Marketing": {
            "analysts": 3,
            "managers": 1,
        },
    }
    
    fmt.Printf("Departments: %v\n", departments)
    fmt.Printf("Engineering developers: %d\n", departments["Engineering"]["developers"])
    
    // Map operations
    numbers := map[int]string{
        1: "one",
        2: "two",
        3: "three",
        4: "four",
        5: "five",
    }
    
    // Find key by value
    target := "three"
    var foundKey int
    var keyFound bool
    
    for key, value := range numbers {
        if value == target {
            foundKey = key
            keyFound = true
            break
        }
    }
    
    if keyFound {
        fmt.Printf("Found '%s' at key %d\n", target, foundKey)
    } else {
        fmt.Printf("'%s' not found\n", target)
    }
    
    // Filter map
    highGrades := make(map[string]int)
    for name, grade := range studentGrades {
        if grade >= 90 {
            highGrades[name] = grade
        }
    }
    
    fmt.Printf("High grades: %v\n", highGrades)
    
    // Transform map values
    doubledGrades := make(map[string]int)
    for name, grade := range studentGrades {
        doubledGrades[name] = grade * 2
    }
    
    fmt.Printf("Doubled grades: %v\n", doubledGrades)
    
    // Map comparison
    map1 := map[string]int{"a": 1, "b": 2}
    map2 := map[string]int{"a": 1, "b": 2}
    map3 := map[string]int{"a": 1, "b": 3}
    
    fmt.Printf("map1 == map2: %t\n", equalMaps(map1, map2))
    fmt.Printf("map1 == map3: %t\n", equalMaps(map1, map3))
    
    // Map keys and values as slices
    keys := make([]string, 0, len(studentGrades))
    values := make([]int, 0, len(studentGrades))
    
    for name, grade := range studentGrades {
        keys = append(keys, name)
        values = append(values, grade)
    }
    
    fmt.Printf("Keys: %v\n", keys)
    fmt.Printf("Values: %v\n", values)
    
    // Map with custom key type
    type Person struct {
        Name string
        Age  int
    }
    
    people := map[Person]string{
        {"Alice", 25}: "Engineer",
        {"Bob", 30}:   "Manager",
        {"Carol", 28}: "Designer",
    }
    
    fmt.Printf("People jobs: %v\n", people)
    
    // Map as function parameter
    processMap(studentGrades)
    
    // Map with struct values
    type Student struct {
        Name  string
        Grade int
        Age   int
    }
    
    studentInfo := map[string]Student{
        "alice": {"Alice", 85, 20},
        "bob":   {"Bob", 92, 21},
        "carol": {"Carol", 78, 19},
    }
    
    fmt.Printf("Student info: %v\n", studentInfo)
    fmt.Printf("Alice info: %v\n", studentInfo["alice"])
    
    // Map with slice values
    classStudents := map[string][]string{
        "Math":    {"Alice", "Bob", "Carol"},
        "Science": {"David", "Eve", "Frank"},
        "Art":     {"Grace", "Henry"},
    }
    
    fmt.Printf("Class students: %v\n", classStudents)
    
    // Adding to slice values
    classStudents["Math"] = append(classStudents["Math"], "Ivan")
    fmt.Printf("Math students after adding Ivan: %v\n", classStudents["Math"])
}

func equalMaps(map1, map2 map[string]int) bool {
    if len(map1) != len(map2) {
        return false
    }
    
    for key, value := range map1 {
        if map2[key] != value {
            return false
        }
    }
    
    return true
}

func processMap(grades map[string]int) {
    fmt.Printf("Processing grades map: %v\n", grades)
    
    // Modify map (will affect original)
    grades["Eve"] = 88
    fmt.Printf("Modified grades: %v\n", grades)
}
```

### Advanced Map Operations
```go
package main

import "fmt"

func main() {
    // Advanced map operations
    
    // 1. Map merging
    map1 := map[string]int{"a": 1, "b": 2, "c": 3}
    map2 := map[string]int{"b": 20, "d": 4, "e": 5}
    
    merged := mergeMaps(map1, map2)
    fmt.Printf("Merged maps: %v\n", merged)
    
    // 2. Map filtering
    original := map[string]int{"a": 1, "b": 2, "c": 3, "d": 4, "e": 5}
    filtered := filterMap(original, func(key string, value int) bool {
        return value > 2
    })
    
    fmt.Printf("Filtered map (values > 2): %v\n", filtered)
    
    // 3. Map transformation
    transformed := transformMap(original, func(key string, value int) (string, int) {
        return strings.ToUpper(key), value * 2
    })
    
    fmt.Printf("Transformed map: %v\n", transformed)
    
    // 4. Map inversion
    inverted := invertMap(original)
    fmt.Printf("Inverted map: %v\n", inverted)
    
    // 5. Map grouping
    words := []string{"apple", "banana", "cherry", "date", "elderberry"}
    grouped := groupWordsByFirstLetter(words)
    
    fmt.Printf("Grouped by first letter: %v\n", grouped)
    
    // 6. Map counting
    items := []string{"apple", "banana", "apple", "cherry", "banana", "apple"}
    counts := countOccurrences(items)
    
    fmt.Printf("Occurrences: %v\n", counts)
    
    // 7. Map with default values
    defaultMap := newDefaultMap(0)
    defaultMap["a"] = 1
    defaultMap["b"] = 2
    
    fmt.Printf("Default map: %v\n", defaultMap.data)
    fmt.Printf("Value for 'c' (default): %d\n", defaultMap.Get("c"))
    
    // 8. Map with TTL (time-to-live)
    ttlMap := NewTTLMap()
    ttlMap.Set("key1", "value1", 2*time.Second)
    ttlMap.Set("key2", "value2", 5*time.Second)
    
    fmt.Printf("TTL map: %v\n", ttlMap.data)
    
    time.Sleep(3 * time.Second)
    ttlMap.Cleanup()
    fmt.Printf("TTL map after cleanup: %v\n", ttlMap.data)
    
    // 9. Map with nested structure
    nested := map[string]interface{}{
        "user": map[string]interface{}{
            "name": "John",
            "age": 30,
            "address": map[string]interface{}{
                "street": "123 Main St",
                "city": "New York",
            },
        },
        "settings": map[string]interface{}{
            "theme": "dark",
            "notifications": true,
        },
    }
    
    fmt.Printf("Nested map: %v\n", nested)
    
    // Access nested values
    if user, ok := nested["user"].(map[string]interface{}); ok {
        if address, ok := user["address"].(map[string]interface{}); ok {
            fmt.Printf("User city: %v\n", address["city"])
        }
    }
    
    // 10. Map with custom sorting
    unsorted := map[string]int{"c": 3, "a": 1, "b": 2, "e": 5, "d": 4}
    sortedKeys := sortMapKeys(unsorted)
    
    fmt.Printf("Sorted keys: %v\n", sortedKeys)
    for _, key := range sortedKeys {
        fmt.Printf("%s: %d\n", key, unsorted[key])
    }
    
    // 11. Map with multiple keys
    multiKey := NewMultiKeyMap()
    multiKey.Set([]string{"key1", "alias1"}, "value1")
    multiKey.Set([]string{"key2", "alias2"}, "value2")
    
    fmt.Printf("Multi-key map: %v\n", multiKey.data)
    fmt.Printf("Get by 'key1': %v\n", multiKey.Get("key1"))
    fmt.Printf("Get by 'alias1': %v\n", multiKey.Get("alias1"))
    
    // 12. Map with validation
    validatedMap := NewValidatedMap(func(key string, value int) error {
        if key == "" {
            return fmt.Errorf("key cannot be empty")
        }
        if value < 0 {
            return fmt.Errorf("value cannot be negative")
        }
        return nil
    })
    
    err := validatedMap.Set("valid", 10)
    fmt.Printf("Set valid: %v, error: %v\n", validatedMap.data, err)
    
    err = validatedMap.Set("", -5)
    fmt.Printf("Set invalid: %v, error: %v\n", validatedMap.data, err)
    
    // 13. Map with caching
    cache := NewCache(3) // Max 3 items
    cache.Set("a", 1)
    cache.Set("b", 2)
    cache.Set("c", 3)
    
    fmt.Printf("Cache: %v\n", cache.data)
    
    cache.Set("d", 4) // Should evict "a"
    fmt.Printf("Cache after adding 'd': %v\n", cache.data)
    
    // 14. Map with statistics
    statsMap := NewStatsMap()
    statsMap.Set("a", 1)
    statsMap.Set("b", 2)
    statsMap.Get("a")
    statsMap.Get("c") // Non-existent
    statsMap.Delete("b")
    
    fmt.Printf("Stats: %+v\n", statsMap.stats)
    
    // 15. Map with persistence simulation
    persistentMap := NewPersistentMap()
    persistentMap.Set("name", "John")
    persistentMap.Set("age", 30)
    
    fmt.Printf("Persistent map: %v\n", persistentMap.data)
    
    // Simulate save
    saved := persistentMap.Save()
    fmt.Printf("Saved data: %v\n", saved)
    
    // Simulate load
    newMap := NewPersistentMap()
    newMap.Load(saved)
    fmt.Printf("Loaded map: %v\n", newMap.data)
}

// Helper functions and types for advanced operations

func mergeMaps(map1, map2 map[string]int) map[string]int {
    merged := make(map[string]int)
    
    // Copy first map
    for key, value := range map1 {
        merged[key] = value
    }
    
    // Add/overwrite with second map
    for key, value := range map2 {
        merged[key] = value
    }
    
    return merged
}

func filterMap(original map[string]int, predicate func(string, int) bool) map[string]int {
    filtered := make(map[string]int)
    
    for key, value := range original {
        if predicate(key, value) {
            filtered[key] = value
        }
    }
    
    return filtered
}

func transformMap(original map[string]int, transform func(string, int) (string, int)) map[string]int {
    transformed := make(map[string]int)
    
    for key, value := range original {
        newKey, newValue := transform(key, value)
        transformed[newKey] = newValue
    }
    
    return transformed
}

func invertMap(original map[string]int) map[int]string {
    inverted := make(map[int]string)
    
    for key, value := range original {
        inverted[value] = key
    }
    
    return inverted
}

func groupWordsByFirstLetter(words []string) map[string][]string {
    grouped := make(map[string][]string)
    
    for _, word := range words {
        if len(word) > 0 {
            firstLetter := strings.ToLower(string(word[0]))
            grouped[firstLetter] = append(grouped[firstLetter], word)
        }
    }
    
    return grouped
}

func countOccurrences(items []string) map[string]int {
    counts := make(map[string]int)
    
    for _, item := range items {
        counts[item]++
    }
    
    return counts
}

// DefaultMap type
type DefaultMap struct {
    data     map[string]int
    defaults int
}

func newDefaultMap(defaultValue int) *DefaultMap {
    return &DefaultMap{
        data:     make(map[string]int),
        defaults: defaultValue,
    }
}

func (dm *DefaultMap) Get(key string) int {
    if value, exists := dm.data[key]; exists {
        return value
    }
    return dm.defaults
}

func (dm *DefaultMap) Set(key string, value int) {
    dm.data[key] = value
}

// TTLMap type
type TTLMap struct {
    data map[string]ttlValue
    mu   sync.RWMutex
}

type ttlValue struct {
    value     interface{}
    expiresAt time.Time
}

func NewTTLMap() *TTLMap {
    return &TTLMap{
        data: make(map[string]ttlValue),
    }
}

func (tm *TTLMap) Set(key string, value interface{}, ttl time.Duration) {
    tm.mu.Lock()
    defer tm.mu.Unlock()
    
    tm.data[key] = ttlValue{
        value:     value,
        expiresAt: time.Now().Add(ttl),
    }
}

func (tm *TTLMap) Get(key string) (interface{}, bool) {
    tm.mu.RLock()
    defer tm.mu.RUnlock()
    
    if value, exists := tm.data[key]; exists {
        if time.Now().Before(value.expiresAt) {
            return value.value, true
        }
        delete(tm.data, key)
    }
    
    return nil, false
}

func (tm *TTLMap) Cleanup() {
    tm.mu.Lock()
    defer tm.mu.Unlock()
    
    now := time.Now()
    for key, value := range tm.data {
        if now.After(value.expiresAt) {
            delete(tm.data, key)
        }
    }
}

// MultiKeyMap type
type MultiKeyMap struct {
    data map[string]interface{}
}

func NewMultiKeyMap() *MultiKeyMap {
    return &MultiKeyMap{
        data: make(map[string]interface{}),
    }
}

func (mkm *MultiKeyMap) Set(keys []string, value interface{}) {
    for _, key := range keys {
        mkm.data[key] = value
    }
}

func (mkm *MultiKeyMap) Get(key string) (interface{}, bool) {
    value, exists := mkm.data[key]
    return value, exists
}

// ValidatedMap type
type ValidatedMap struct {
    data map[string]int
    validator func(string, int) error
}

func NewValidatedMap(validator func(string, int) error) *ValidatedMap {
    return &ValidatedMap{
        data:      make(map[string]int),
        validator: validator,
    }
}

func (vm *ValidatedMap) Set(key string, value int) error {
    if err := vm.validator(key, value); err != nil {
        return err
    }
    vm.data[key] = value
    return nil
}

// Cache type
type Cache struct {
    data map[string]int
    max  int
}

func NewCache(maxSize int) *Cache {
    return &Cache{
        data: make(map[string]int),
        max:  maxSize,
    }
}

func (c *Cache) Set(key string, value int) {
    if len(c.data) >= c.max {
        // Simple LRU: remove first key
        for k := range c.data {
            delete(c.data, k)
            break
        }
    }
    c.data[key] = value
}

// StatsMap type
type StatsMap struct {
    data  map[string]int
    stats struct {
        sets     int
        gets     int
        deletes  int
        hits     int
        misses   int
    }
}

func NewStatsMap() *StatsMap {
    return &StatsMap{
        data: make(map[string]int),
    }
}

func (sm *StatsMap) Set(key string, value int) {
    sm.data[key] = value
    sm.stats.sets++
}

func (sm *StatsMap) Get(key string) int {
    sm.stats.gets++
    if value, exists := sm.data[key]; exists {
        sm.stats.hits++
        return value
    }
    sm.stats.misses++
    return 0
}

func (sm *StatsMap) Delete(key string) {
    if _, exists := sm.data[key]; exists {
        delete(sm.data, key)
        sm.stats.deletes++
    }
}

// PersistentMap type
type PersistentMap struct {
    data map[string]interface{}
}

func NewPersistentMap() *PersistentMap {
    return &PersistentMap{
        data: make(map[string]interface{}),
    }
}

func (pm *PersistentMap) Set(key string, value interface{}) {
    pm.data[key] = value
}

func (pm *PersistentMap) Save() map[string]interface{} {
    saved := make(map[string]interface{})
    for k, v := range pm.data {
        saved[k] = v
    }
    return saved
}

func (pm *PersistentMap) Load(data map[string]interface{}) {
    pm.data = data
}

func sortMapKeys(m map[string]int) []string {
    keys := make([]string, 0, len(m))
    for k := range m {
        keys = append(keys, k)
    }
    
    sort.Strings(keys)
    return keys
}
```

## Summary

Go arrays, slices, and maps provide:

**Arrays:**
- Fixed-size collections with same type elements
- Value type (copied when assigned)
- Efficient for known-size data
- Multi-dimensional arrays support
- Direct index access O(1)

**Slices:**
- Dynamic view of underlying array
- Flexible length and capacity
- Reference type (shared underlying array)
- Built-in append and copy operations
- Slicing operations for sub-views
- Efficient for most collection needs

**Maps:**
- Key-value collections with hash table implementation
- Unordered collection (pre-Go 1.0)
- Fast lookups, insertions, and deletions
- Dynamic size
- Generic key and value types

**Key Operations:**
- Creation and initialization
- Element access and modification
- Iteration and searching
- Filtering and transformation
- Sorting and aggregation
- Advanced algorithms

**Memory Management:**
- Arrays: contiguous memory allocation
- Slices: references to backing arrays
- Maps: hash table with dynamic resizing
- Efficient memory usage patterns
- Garbage collection friendly

**Best Practices:**
- Use slices for most collection needs
- Prefer maps for key-value lookups
- Understand slice capacity growth
- Use appropriate data structures
- Consider performance implications

Go's collection types provide efficient, type-safe, and flexible ways to work with groups of related data, with slices being the most commonly used for their dynamic nature and performance characteristics.
