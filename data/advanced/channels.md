# Channels

Channels allow communication between goroutines.

## Example
```go
ch := make(chan int)
go func() {
    ch <- 5
}()
fmt.Println(<-ch)
Practice

Send and receive data using channels.
