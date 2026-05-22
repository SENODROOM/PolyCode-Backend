<?php
    // Exercise 3: Data Analysis
    
    echo "<h2>Sales Data Analysis</h2>";
    
    // Sample sales data
    $salesData = [
        ["date" => "2024-01-01", "product" => "Laptop", "category" => "Electronics", "quantity" => 2, "price" => 999.99, "region" => "North"],
        ["date" => "2024-01-02", "product" => "Mouse", "category" => "Electronics", "quantity" => 5, "price" => 25.99, "region" => "South"],
        ["date" => "2024-01-03", "product" => "Desk Chair", "category" => "Furniture", "quantity" => 3, "price" => 199.99, "region" => "North"],
        ["date" => "2024-01-04", "product" => "Keyboard", "category" => "Electronics", "quantity" => 4, "price" => 79.99, "region" => "East"],
        ["date" => "2024-01-05", "product" => "Desk", "category" => "Furniture", "quantity" => 1, "price" => 299.99, "region" => "West"],
        ["date" => "2024-01-06", "product" => "Monitor", "category" => "Electronics", "quantity" => 3, "price" => 299.99, "region" => "South"],
        ["date" => "2024-01-07", "product" => "Bookshelf", "category" => "Furniture", "quantity" => 2, "price" => 149.99, "region" => "East"],
        ["date" => "2024-01-08", "product" => "Webcam", "category" => "Electronics", "quantity" => 6, "price" => 49.99, "region" => "West"],
        ["date" => "2024-01-09", "product" => "Office Chair", "category" => "Furniture", "quantity" => 4, "price" => 249.99, "region" => "North"],
        ["date" => "2024-01-10", "product" => "Headphones", "category" => "Electronics", "quantity" => 3, "price" => 89.99, "region" => "South"]
    ];
    
    // Function to calculate total for each sale
    function calculateTotal($sale) {
        return $sale['quantity'] * $sale['price'];
    }
    
    // Add total to each sale record
    $salesWithTotal = array_map(function($sale) {
        $sale['total'] = calculateTotal($sale);
        return $sale;
    }, $salesData);
    
    echo "<h3>Sales Data with Totals:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Date</th><th>Product</th><th>Category</th><th>Quantity</th><th>Price</th><th>Total</th><th>Region</th></tr>";
    foreach ($salesWithTotal as $sale) {
        echo "<tr>";
        echo "<td>{$sale['date']}</td>";
        echo "<td>{$sale['product']}</td>";
        echo "<td>{$sale['category']}</td>";
        echo "<td>{$sale['quantity']}</td>";
        echo "<td>\${$sale['price']}</td>";
        echo "<td>\$" . number_format($sale['total'], 2) . "</td>";
        echo "<td>{$sale['region']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Group data by categories
    $categoryData = [];
    foreach ($salesWithTotal as $sale) {
        $category = $sale['category'];
        if (!isset($categoryData[$category])) {
            $categoryData[$category] = [
                'sales' => [],
                'totalRevenue' => 0,
                'totalQuantity' => 0,
                'count' => 0
            ];
        }
        $categoryData[$category]['sales'][] = $sale;
        $categoryData[$category]['totalRevenue'] += $sale['total'];
        $categoryData[$category]['totalQuantity'] += $sale['quantity'];
        $categoryData[$category]['count']++;
    }
    
    echo "<h3>Analysis by Category:</h3>";
    foreach ($categoryData as $category => $data) {
        $avgSale = $data['totalRevenue'] / $data['count'];
        $avgQuantity = $data['totalQuantity'] / $data['count'];
        
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
        echo "<strong>$category</strong><br>";
        echo "Number of sales: {$data['count']}<br>";
        echo "Total revenue: $" . number_format($data['totalRevenue'], 2) . "<br>";
        echo "Total quantity: {$data['totalQuantity']}<br>";
        echo "Average sale value: $" . number_format($avgSale, 2) . "<br>";
        echo "Average quantity: " . round($avgQuantity, 1) . "<br>";
        echo "</div>";
    }
    
    // Group data by regions
    $regionData = [];
    foreach ($salesWithTotal as $sale) {
        $region = $sale['region'];
        if (!isset($regionData[$region])) {
            $regionData[$region] = [
                'totalRevenue' => 0,
                'totalQuantity' => 0,
                'count' => 0,
                'categories' => []
            ];
        }
        $regionData[$region]['totalRevenue'] += $sale['total'];
        $regionData[$region]['totalQuantity'] += $sale['quantity'];
        $regionData[$region]['count']++;
        
        if (!isset($regionData[$region]['categories'][$sale['category']])) {
            $regionData[$region]['categories'][$sale['category']] = 0;
        }
        $regionData[$region]['categories'][$sale['category']]++;
    }
    
    echo "<h3>Analysis by Region:</h3>";
    foreach ($regionData as $region => $data) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
        echo "<strong>$region Region</strong><br>";
        echo "Number of sales: {$data['count']}<br>";
        echo "Total revenue: $" . number_format($data['totalRevenue'], 2) . "<br>";
        echo "Total quantity: {$data['totalQuantity']}<br>";
        echo "Average sale value: $" . number_format($data['totalRevenue'] / $data['count'], 2) . "<br>";
        echo "Sales by category: ";
        foreach ($data['categories'] as $category => $count) {
            echo "$category ($count), ";
        }
        echo "</div>";
    }
    
    // Top performing products
    $productPerformance = [];
    foreach ($salesWithTotal as $sale) {
        $product = $sale['product'];
        if (!isset($productPerformance[$product])) {
            $productPerformance[$product] = [
                'totalRevenue' => 0,
                'totalQuantity' => 0,
                'count' => 0,
                'category' => $sale['category']
            ];
        }
        $productPerformance[$product]['totalRevenue'] += $sale['total'];
        $productPerformance[$product]['totalQuantity'] += $sale['quantity'];
        $productPerformance[$product]['count']++;
    }
    
    // Sort products by revenue
    uasort($productPerformance, function($a, $b) {
        return $b['totalRevenue'] <=> $a['totalRevenue'];
    });
    
    echo "<h3>Top Products by Revenue:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Product</th><th>Category</th><th>Sales Count</th><th>Total Quantity</th><th>Total Revenue</th><th>Avg Sale</th></tr>";
    foreach ($productPerformance as $product => $data) {
        $avgSale = $data['totalRevenue'] / $data['count'];
        echo "<tr>";
        echo "<td>$product</td>";
        echo "<td>{$data['category']}</td>";
        echo "<td>{$data['count']}</td>";
        echo "<td>{$data['totalQuantity']}</td>";
        echo "<td>\$" . number_format($data['totalRevenue'], 2) . "</td>";
        echo "<td>\$" . number_format($avgSale, 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Overall statistics
    $overallStats = [
        'totalRevenue' => array_sum(array_column($salesWithTotal, 'total')),
        'totalSales' => count($salesWithTotal),
        'totalQuantity' => array_sum(array_column($salesWithTotal, 'quantity')),
        'avgSaleValue' => 0,
        'avgQuantity' => 0
    ];
    $overallStats['avgSaleValue'] = $overallStats['totalRevenue'] / $overallStats['totalSales'];
    $overallStats['avgQuantity'] = $overallStats['totalQuantity'] / $overallStats['totalSales'];
    
    echo "<h3>Overall Statistics:</h3>";
    echo "<div style='background-color: #f0f0f0; padding: 15px; border-radius: 5px;'>";
    echo "<strong>Total Sales:</strong> {$overallStats['totalSales']}<br>";
    echo "<strong>Total Revenue:</strong> $" . number_format($overallStats['totalRevenue'], 2) . "<br>";
    echo "<strong>Total Quantity Sold:</strong> {$overallStats['totalQuantity']}<br>";
    echo "<strong>Average Sale Value:</strong> $" . number_format($overallStats['avgSaleValue'], 2) . "<br>";
    echo "<strong>Average Quantity per Sale:</strong> " . round($overallStats['avgQuantity'], 1) . "<br>";
    echo "<strong>Number of Categories:</strong> " . count($categoryData) . "<br>";
    echo "<strong>Number of Regions:</strong> " . count($regionData) . "<br>";
    echo "<strong>Number of Products:</strong> " . count($productPerformance) . "<br>";
    echo "</div>";
    
    // Find best and worst performing categories
    $categoryPerformance = [];
    foreach ($categoryData as $category => $data) {
        $categoryPerformance[$category] = $data['totalRevenue'];
    }
    arsort($categoryPerformance);
    
    echo "<h3>Category Performance Ranking:</h3>";
    $rank = 1;
    foreach ($categoryPerformance as $category => $revenue) {
        echo "$rank. $category: $" . number_format($revenue, 2) . "<br>";
        $rank++;
    }
?>
