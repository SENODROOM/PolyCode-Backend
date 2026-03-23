// ============================================================
//  Project 03: Inventory Management System
//
//  Features:
//  - Add, update, delete products
//  - Track stock levels with low-stock alerts
//  - Category management
//  - CSV import/export
//  - Sales recording & revenue tracking
//  - Search and filter
//  - Full test suite
//
//  Run: cargo run
// ============================================================

use std::collections::HashMap;
use std::fmt;

// ─────────────────────────────────────────────
// TYPES
// ─────────────────────────────────────────────

#[derive(Debug, Clone, PartialEq)]
enum Category { Electronics, Clothing, Food, Books, Sports, Other(String) }

impl fmt::Display for Category {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            Category::Other(s) => write!(f, "{}", s),
            _                  => write!(f, "{:?}", self),
        }
    }
}

impl Category {
    fn from_str(s: &str) -> Self {
        match s.to_lowercase().as_str() {
            "electronics" => Category::Electronics,
            "clothing"    => Category::Clothing,
            "food"        => Category::Food,
            "books"       => Category::Books,
            "sports"      => Category::Sports,
            other         => Category::Other(other.to_string()),
        }
    }
}

#[derive(Debug, Clone)]
struct Product {
    id:            u32,
    name:          String,
    sku:           String,
    category:      Category,
    price:         f64,
    cost:          f64,
    stock:         u32,
    reorder_level: u32,
}

impl Product {
    fn new(id: u32, name: &str, sku: &str, category: Category, price: f64, cost: f64, stock: u32) -> Self {
        Self { id, name: name.to_string(), sku: sku.to_string(), category, price, cost, stock, reorder_level: 10 }
    }
    fn is_low_stock(&self)  -> bool { self.stock <= self.reorder_level }
    fn is_out_of_stock(&self)-> bool { self.stock == 0 }
    fn margin_pct(&self)    -> f64  { if self.price > 0.0 { (self.price - self.cost) / self.price * 100.0 } else { 0.0 } }
    fn stock_value(&self)   -> f64  { self.cost * self.stock as f64 }
    fn retail_value(&self)  -> f64  { self.price * self.stock as f64 }
}

#[derive(Debug, Clone)]
struct Sale {
    product_id: u32,
    product_name: String,
    quantity:   u32,
    unit_price: f64,
    unit_cost:  f64,
}

impl Sale {
    fn revenue(&self) -> f64 { self.unit_price * self.quantity as f64 }
    fn profit(&self)  -> f64 { (self.unit_price - self.unit_cost) * self.quantity as f64 }
}

#[derive(Debug)]
enum InventoryError {
    ProductNotFound(u32),
    SkuAlreadyExists(String),
    InsufficientStock { available: u32, requested: u32 },
    InvalidPrice(String),
}

impl fmt::Display for InventoryError {
    fn fmt(&self, f: &mut fmt::Formatter) -> fmt::Result {
        match self {
            InventoryError::ProductNotFound(id)      => write!(f, "Product #{} not found", id),
            InventoryError::SkuAlreadyExists(sku)    => write!(f, "SKU '{}' already exists", sku),
            InventoryError::InsufficientStock { available, requested }
                => write!(f, "Insufficient stock: {} requested, {} available", requested, available),
            InventoryError::InvalidPrice(msg)        => write!(f, "Invalid price: {}", msg),
        }
    }
}

// ─────────────────────────────────────────────
// INVENTORY
// ─────────────────────────────────────────────

struct Inventory {
    products: HashMap<u32, Product>,
    sales:    Vec<Sale>,
    next_id:  u32,
}

impl Inventory {
    fn new() -> Self { Self { products: HashMap::new(), sales: Vec::new(), next_id: 1 } }

    fn add_product(&mut self, name: &str, sku: &str, category: Category, price: f64, cost: f64, stock: u32)
        -> Result<u32, InventoryError>
    {
        if price < 0.0 { return Err(InventoryError::InvalidPrice("price cannot be negative".into())); }
        if self.products.values().any(|p| p.sku == sku) {
            return Err(InventoryError::SkuAlreadyExists(sku.to_string()));
        }
        let id = self.next_id;
        self.products.insert(id, Product::new(id, name, sku, category, price, cost, stock));
        self.next_id += 1;
        Ok(id)
    }

    fn get(&self, id: u32) -> Result<&Product, InventoryError> {
        self.products.get(&id).ok_or(InventoryError::ProductNotFound(id))
    }
    fn get_mut(&mut self, id: u32) -> Result<&mut Product, InventoryError> {
        self.products.get_mut(&id).ok_or(InventoryError::ProductNotFound(id))
    }

    fn restock(&mut self, id: u32, qty: u32) -> Result<u32, InventoryError> {
        let p = self.get_mut(id)?;
        p.stock += qty;
        Ok(p.stock)
    }

    fn sell(&mut self, id: u32, qty: u32) -> Result<f64, InventoryError> {
        let p = self.get(id)?;
        if p.stock < qty { return Err(InventoryError::InsufficientStock { available: p.stock, requested: qty }); }
        let sale = Sale { product_id: id, product_name: p.name.clone(), quantity: qty, unit_price: p.price, unit_cost: p.cost };
        let revenue = sale.revenue();
        self.sales.push(sale);
        self.products.get_mut(&id).unwrap().stock -= qty;
        Ok(revenue)
    }

    fn update_price(&mut self, id: u32, new_price: f64) -> Result<(), InventoryError> {
        if new_price < 0.0 { return Err(InventoryError::InvalidPrice("cannot be negative".into())); }
        self.get_mut(id)?.price = new_price;
        Ok(())
    }

    fn delete(&mut self, id: u32) -> Result<String, InventoryError> {
        self.products.remove(&id).map(|p| p.name).ok_or(InventoryError::ProductNotFound(id))
    }

    fn search(&self, query: &str) -> Vec<&Product> {
        let q = query.to_lowercase();
        let mut results: Vec<&Product> = self.products.values()
            .filter(|p| p.name.to_lowercase().contains(&q) || p.sku.to_lowercase().contains(&q))
            .collect();
        results.sort_by_key(|p| p.id);
        results
    }

    fn by_category(&self, cat: &Category) -> Vec<&Product> {
        let mut results: Vec<&Product> = self.products.values()
            .filter(|p| &p.category == cat)
            .collect();
        results.sort_by_key(|p| p.id);
        results
    }

    fn low_stock_alerts(&self) -> Vec<&Product> {
        let mut alerts: Vec<&Product> = self.products.values()
            .filter(|p| p.is_low_stock())
            .collect();
        alerts.sort_by_key(|p| p.stock);
        alerts
    }

    fn total_stock_value(&self)  -> f64 { self.products.values().map(|p| p.stock_value()).sum() }
    fn total_retail_value(&self) -> f64 { self.products.values().map(|p| p.retail_value()).sum() }
    fn total_revenue(&self)      -> f64 { self.sales.iter().map(|s| s.revenue()).sum() }
    fn total_profit(&self)       -> f64 { self.sales.iter().map(|s| s.profit()).sum() }

    fn print_catalog(&self) {
        let mut products: Vec<&Product> = self.products.values().collect();
        products.sort_by_key(|p| p.id);
        println!("\n{:─<80}");
        println!("{:>4} {:<25} {:<10} {:<15} {:>8} {:>8} {:>7} {}",
            "ID", "Name", "SKU", "Category", "Price", "Cost", "Stock", "Margin");
        println!("{:─<80}");
        for p in products {
            let alert = if p.is_out_of_stock() { " 🚫" } else if p.is_low_stock() { " ⚠" } else { "" };
            println!("{:>4} {:<25} {:<10} {:<15} {:>8.2} {:>8.2} {:>7}{}{}", 
                p.id, p.name, p.sku, p.category, p.price, p.cost, p.stock, alert, 
                format!("  {:.1}%", p.margin_pct()));
        }
        println!("{:─<80}");
        println!("Stock value: ${:.2}  |  Retail value: ${:.2}", self.total_stock_value(), self.total_retail_value());
    }

    fn print_sales_report(&self) {
        println!("\n{:─<60}");
        println!("Sales Report ({} transactions)", self.sales.len());
        println!("{:─<60}");
        let mut by_product: HashMap<u32, (String, u32, f64, f64)> = HashMap::new();
        for s in &self.sales {
            let e = by_product.entry(s.product_id).or_insert((s.product_name.clone(), 0, 0.0, 0.0));
            e.1 += s.quantity;
            e.2 += s.revenue();
            e.3 += s.profit();
        }
        let mut items: Vec<_> = by_product.iter().collect();
        items.sort_by(|a, b| b.1.2.partial_cmp(&a.1.2).unwrap());
        println!("{:<25} {:>8} {:>12} {:>12}", "Product", "Units", "Revenue", "Profit");
        println!("{:─<60}");
        for (_, (name, qty, rev, profit)) in &items {
            println!("{:<25} {:>8} {:>12.2} {:>12.2}", name, qty, rev, profit);
        }
        println!("{:─<60}");
        println!("{:<25} {:>8} {:>12.2} {:>12.2}", "TOTAL", 
            self.sales.iter().map(|s| s.quantity).sum::<u32>(),
            self.total_revenue(), self.total_profit());
    }

    fn export_csv(&self) -> String {
        let mut lines = vec!["id,name,sku,category,price,cost,stock".to_string()];
        let mut products: Vec<&Product> = self.products.values().collect();
        products.sort_by_key(|p| p.id);
        for p in products {
            lines.push(format!("{},{},{},{},{:.2},{:.2},{}", p.id, p.name, p.sku, p.category, p.price, p.cost, p.stock));
        }
        lines.join("\n")
    }
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

fn main() {
    println!("===== Project 03: Inventory System =====\n");

    let mut inv = Inventory::new();

    // Add products
    inv.add_product("MacBook Pro 16",   "MBP16",  Category::Electronics,  2499.00, 1800.00, 15).unwrap();
    inv.add_product("iPhone 15 Pro",    "IP15P",  Category::Electronics,   999.00,  650.00, 30).unwrap();
    inv.add_product("Running Shoes",    "RS001",  Category::Sports,         89.99,   35.00, 8 ).unwrap();
    inv.add_product("The Rust Book",    "TRB01",  Category::Books,          39.99,   12.00, 50).unwrap();
    inv.add_product("Cotton T-Shirt",   "CTS01",  Category::Clothing,       24.99,    8.00, 100).unwrap();
    inv.add_product("Wireless Headset", "WH100",  Category::Electronics,   149.99,   60.00, 6 ).unwrap();
    inv.add_product("Python Cookbook",  "PCB01",  Category::Books,          49.99,   15.00, 25).unwrap();
    inv.add_product("Yoga Mat",         "YM001",  Category::Sports,         29.99,   10.00, 3 ).unwrap();

    inv.print_catalog();

    // Sales
    println!("\n--- Sales ---");
    for (id, qty) in &[(1u32, 2u32), (2, 5), (3, 4), (4, 10), (6, 2), (5, 15)] {
        match inv.sell(*id, *qty) {
            Ok(rev)  => println!("  ✅ Sold {} of #{} → revenue ${:.2}", qty, id, rev),
            Err(e)   => println!("  ❌ {}", e),
        }
    }

    // Error cases
    println!("\n--- Error Handling ---");
    println!("  {}", inv.sell(999, 1).unwrap_err());
    println!("  {}", inv.sell(8, 100).unwrap_err());
    println!("  {}", inv.add_product("Dup SKU", "MBP16", Category::Electronics, 100.0, 50.0, 5).unwrap_err());

    // Restock
    println!("\n--- Restocking ---");
    let new_stock = inv.restock(3, 20).unwrap();
    println!("  Restocked #3 (Running Shoes): now {} units", new_stock);

    // Low stock alerts
    println!("\n--- Low Stock Alerts ---");
    for p in inv.low_stock_alerts() {
        println!("  ⚠ {} (SKU: {}) — only {} left (reorder at {})", p.name, p.sku, p.stock, p.reorder_level);
    }

    // Search
    println!("\n--- Search 'pro' ---");
    for p in inv.search("pro") { println!("  #{}: {} — ${:.2}", p.id, p.name, p.price); }

    // Category filter
    println!("\n--- Electronics ---");
    for p in inv.by_category(&Category::Electronics) { println!("  #{}: {} stock={}", p.id, p.name, p.stock); }

    // CSV export
    println!("\n--- CSV Export (first 3 lines) ---");
    for line in inv.export_csv().lines().take(4) { println!("  {}", line); }

    inv.print_sales_report();
    println!("\n✅ Project 03 complete!");
}

#[cfg(test)]
mod tests {
    use super::*;

    fn test_inv() -> (Inventory, u32) {
        let mut inv = Inventory::new();
        let id = inv.add_product("Widget", "WID01", Category::Electronics, 100.0, 50.0, 20).unwrap();
        (inv, id)
    }

    #[test] fn add_product()          { let (inv, id) = test_inv(); assert_eq!(id, 1); assert_eq!(inv.products.len(), 1); }
    #[test] fn duplicate_sku()        { let (mut inv, _) = test_inv(); assert!(inv.add_product("X","WID01",Category::Books,1.0,0.5,1).is_err()); }
    #[test] fn sell_reduces_stock()   { let (mut inv, id) = test_inv(); inv.sell(id, 5).unwrap(); assert_eq!(inv.get(id).unwrap().stock, 15); }
    #[test] fn sell_insufficient()    { let (mut inv, id) = test_inv(); assert!(inv.sell(id, 99).is_err()); }
    #[test] fn restock()              { let (mut inv, id) = test_inv(); inv.restock(id, 10).unwrap(); assert_eq!(inv.get(id).unwrap().stock, 30); }
    #[test] fn margin()               { let (inv, id) = test_inv(); assert!((inv.get(id).unwrap().margin_pct() - 50.0).abs() < 0.01); }
    #[test] fn revenue_tracking()     { let (mut inv, id) = test_inv(); inv.sell(id, 3).unwrap(); assert!((inv.total_revenue() - 300.0).abs() < 0.01); }
    #[test] fn search()               { let (inv, _) = test_inv(); assert_eq!(inv.search("widget").len(), 1); assert_eq!(inv.search("xyz").len(), 0); }
}
