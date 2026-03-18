/**
 * Simple Linear Regression in Rust
 * This example demonstrates basic mathematical computations for linear regression.
 */

struct Point {
    x: f64,
    y: f64,
}

struct LinearRegression {
    slope: f64,
    intercept: f64,
}

impl LinearRegression {
    fn train(points: &[Point]) -> Option<Self> {
        let n = points.len() as f64;
        if n == 0.0 {
            return None;
        }

        let mut sum_x = 0.0;
        let mut sum_y = 0.0;
        let mut sum_xy = 0.0;
        let mut sum_x2 = 0.0;

        for p in points {
            sum_x += p.x;
            sum_y += p.y;
            sum_xy += p.x * p.y;
            sum_x2 += p.x * p.x;
        }

        let denominator = n * sum_x2 - sum_x * sum_x;
        if denominator == 0.0 {
            return None;
        }

        let slope = (n * sum_xy - sum_x * sum_y) / denominator;
        let intercept = (sum_y - slope * sum_x) / n;

        Some(LinearRegression { slope, intercept })
    }

    fn predict(&self, x: f64) -> f64 {
        self.slope * x + self.intercept
    }
}

fn main() {
    let data = vec![
        Point { x: 1.0, y: 2.0 },
        Point { x: 2.0, y: 3.0 },
        Point { x: 3.0, y: 5.0 },
        Point { x: 4.0, y: 4.0 },
        Point { x: 5.0, y: 6.0 },
    ];

    if let Some(lr) = LinearRegression::train(&data) {
        println!("Linear Regression Model Trained!");
        println!("Slope (m): {:.4}", lr.slope);
        println!("Intercept (b): {:.4}", lr.intercept);

        let test_x = 6.0;
        println!("Prediction for x={}: y={:.4}", test_x, lr.predict(test_x));
    } else {
        println!("Failed to train model (possibly insufficient or invalid data)");
    }
}