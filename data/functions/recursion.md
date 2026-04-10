# Recursion

Function calling itself.

## Example
```go
func factorial(n int) int {
    if n == 0 {
        return 1
    }
    return n * factorial(n-1)
}
Practice

Write recursive sum function.
