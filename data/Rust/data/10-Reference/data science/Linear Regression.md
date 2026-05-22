# Linear Regression in Rust

## Overview
This implementation demonstrates **Simple Linear Regression** in Rust, focusing on the language's safety and performance characteristics. Rust's strict typing and ownership model make it an excellent choice for mathematical computations where precision and resource management are critical.

## Mathematical Model
The goal is to find the line of best fit:
$$y = mx + b$$

Where:
- **$m$ (Slope)**: The change in $y$ relative to $x$.
- **$b$ (Intercept)**: The value of $y$ when $x$ is 0.

## Implementation Details
The Rust implementation uses the **Least Squares Method**:

1.  **Slope ($m$)**:
    $$m = \frac{n(\sum xy) - (\sum x)(\sum y)}{n(\sum x^2) - (\sum x)^2}$$
2.  **Intercept ($b$)**:
    $$b = \frac{\sum y - m(\sum x)}{n}$$

## Rust Features Used
- **`struct`**: Used for `Point` and `LinearRegression` data structures.
- **`Option<T>`**: Handles potential division-by-zero errors gracefully.
- **`impl`**: Encapsulates the training and prediction logic.
- **`Vec<T>`**: Efficiently manages collections of data points.

## Error Handling
The code specifically checks for a denominator of zero to avoid runtime crashes, returning `None` if the model cannot be trained.

## How to Run
Compile and run the example using the Rust compiler:
```bash
rustc "Linear Regression.rs"
./"Linear Regression"
```

[Linear Regression.rs](file:///c:/Users/HP/OneDrive/Documents/Projects/PolyCode/Rust/data/data_science/Linear%20Regression.rs)