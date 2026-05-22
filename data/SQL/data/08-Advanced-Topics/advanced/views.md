# SQL Views

## Basic Views

### Simple View
```sql
-- Create basic view for employee information
CREATE VIEW vw_employee_info AS
SELECT 
    employee_id,
    first_name,
    last_name,
    email,
    phone,
    hire_date,
    salary
FROM employees
WHERE status = 'ACTIVE';

-- Query the view
SELECT * FROM vw_employee_info WHERE salary > 50000;
```

### View with Calculated Columns
```sql
-- Create view with calculated columns
CREATE VIEW vw_employee_details AS
SELECT 
    employee_id,
    first_name,
    last_name,
    email,
    CONCAT(first_name, ' ', last_name) AS full_name,
    salary,
    salary * 12 AS annual_salary,
    DATEDIFF(YEAR, hire_date, GETDATE()) AS years_of_service,
    CASE 
        WHEN salary > 80000 THEN 'High'
        WHEN salary > 50000 THEN 'Medium'
        ELSE 'Low'
    END AS salary_level
FROM employees;

-- PostgreSQL version
CREATE VIEW vw_employee_details AS
SELECT 
    employee_id,
    first_name,
    last_name,
    email,
    first_name || ' ' || last_name AS full_name,
    salary,
    salary * 12 AS annual_salary,
    EXTRACT(YEAR FROM AGE(CURRENT_DATE, hire_date)) AS years_of_service,
    CASE 
        WHEN salary > 80000 THEN 'High'
        WHEN salary > 50000 THEN 'Medium'
        ELSE 'Low'
    END AS salary_level
FROM employees;
```

### View with JOINs
```sql
-- Create view with table joins
CREATE VIEW vw_employee_department AS
SELECT 
    e.employee_id,
    e.first_name,
    e.last_name,
    e.email,
    e.salary,
    d.department_name,
    l.city,
    l.state,
    j.job_title
FROM employees e
JOIN departments d ON e.department_id = d.department_id
JOIN locations l ON d.location_id = l.location_id
JOIN jobs j ON e.job_id = j.job_id;
```

## Advanced Views

### View with Aggregation
```sql
-- Create view with aggregated data
CREATE VIEW vw_department_summary AS
SELECT 
    d.department_id,
    d.department_name,
    COUNT(e.employee_id) AS employee_count,
    AVG(e.salary) AS avg_salary,
    MIN(e.salary) AS min_salary,
    MAX(e.salary) AS max_salary,
    SUM(e.salary) AS total_salary
FROM departments d
LEFT JOIN employees e ON d.department_id = e.department_id
GROUP BY d.department_id, d.department_name;
```

### View with Subquery
```sql
-- Create view with subquery
CREATE VIEW vw_high_earners AS
SELECT 
    employee_id,
    first_name,
    last_name,
    salary,
    department_id
FROM employees e
WHERE salary > (
    SELECT AVG(salary) * 1.5
    FROM employees
    WHERE department_id = e.department_id
);
```

### Partitioned View
```sql
-- Create partitioned view (SQL Server)
CREATE VIEW vw_all_orders AS
SELECT order_id, customer_id, order_date, total_amount, '2023' as year
FROM orders_2023
UNION ALL
SELECT order_id, customer_id, order_date, total_amount, '2024' as year
FROM orders_2024
UNION ALL
SELECT order_id, customer_id, order_date, total_amount, '2025' as year
FROM orders_2025;
```

## Database-Specific Views

### MySQL Views
```sql
-- MySQL view syntax
CREATE VIEW vw_active_employees AS
SELECT employee_id, first_name, last_name, email, salary
FROM employees
WHERE status = 'ACTIVE'
WITH CHECK OPTION;

-- MySQL view with ALGORITHM
CREATE ALGORITHM = TEMPTORARY VIEW vw_temp_employees AS
SELECT * FROM employees WHERE department_id = 50;

-- MySQL updatable view
CREATE VIEW vw_updatable_employees AS
SELECT employee_id, first_name, last_name, email
FROM employees
WHERE status = 'ACTIVE';
```

### PostgreSQL Views
```sql
-- PostgreSQL view syntax
CREATE VIEW vw_employee_stats AS
SELECT 
    employee_id,
    first_name,
    last_name,
    salary,
        (SELECT AVG(salary) FROM employees) as avg_salary,
        (SELECT COUNT(*) FROM employees) as total_employees
FROM employees;

-- PostgreSQL materialized view
CREATE MATERIALIZED VIEW vw_department_stats AS
SELECT 
    department_id,
    COUNT(*) as employee_count,
    AVG(salary) as avg_salary
FROM employees
GROUP BY department_id;

-- Refresh materialized view
REFRESH MATERIALIZED VIEW vw_department_stats;

-- PostgreSQL recursive view (using WITH RECURSIVE)
CREATE VIEW vw_employee_hierarchy AS
WITH RECURSIVE employee_tree AS (
    -- Base case: top-level managers
    SELECT employee_id, first_name, last_name, manager_id, 1 as level
    FROM employees
    WHERE manager_id IS NULL
    
    UNION ALL
    
    -- Recursive case: employees under managers
    SELECT 
        e.employee_id, 
        e.first_name, 
        e.last_name, 
        e.manager_id, 
        et.level + 1
    FROM employees e
    JOIN employee_tree et ON e.manager_id = et.employee_id
)
SELECT * FROM employee_tree;
```

### SQL Server Views
```sql
-- SQL Server view syntax
CREATE VIEW vw_employee_info AS
SELECT employee_id, first_name, last_name, email, salary
FROM employees
WHERE status = 'ACTIVE'
WITH CHECK OPTION;

-- SQL Server indexed view
CREATE VIEW vw_employee_indexed WITH SCHEMABINDING
AS
SELECT employee_id, first_name, last_name, email, salary
FROM employees
WHERE status = 'ACTIVE';

-- Create unique index on indexed view
CREATE UNIQUE CLUSTERED INDEX idx_employee_indexed 
ON vw_employee_indexed (employee_id);

-- SQL Server partitioned view
CREATE VIEW vw_orders_partitioned AS
SELECT * FROM orders_2023
UNION ALL
SELECT * FROM orders_2024
UNION ALL
SELECT * FROM orders_2025;
```

### Oracle Views
```sql
-- Oracle view syntax
CREATE OR REPLACE VIEW vw_employee_info AS
SELECT employee_id, first_name, last_name, email, salary
FROM employees
WHERE status = 'ACTIVE';

-- Oracle materialized view
CREATE MATERIALIZED VIEW vw_department_stats
BUILD IMMEDIATE
REFRESH COMPLETE ON DEMAND
AS
SELECT 
    department_id,
    COUNT(*) as employee_count,
    AVG(salary) as avg_salary
FROM employees
GROUP BY department_id;

-- Refresh materialized view
BEGIN
    DBMS_MVIEW.REFRESH('vw_department_stats');
END;
/

-- Oracle inline view
CREATE OR REPLACE FORCE VIEW vw_employee_details
    (employee_id, full_name, annual_salary)
AS
SELECT 
    employee_id, 
    first_name || ' ' || last_name as full_name,
    salary * 12 as annual_salary
FROM employees;
```

## View Management

### Create View
```sql
-- Basic view creation
CREATE VIEW view_name AS
SELECT column1, column2, column3
FROM table_name
WHERE condition;

-- View with options
CREATE OR REPLACE VIEW view_name AS
SELECT column1, column2
FROM table_name
WHERE condition
WITH CHECK OPTION;
```

### Modify View
```sql
-- MySQL/PostgreSQL
CREATE OR REPLACE VIEW view_name AS
SELECT column1, column2
FROM table_name
WHERE condition;

-- SQL Server
ALTER VIEW view_name AS
SELECT column1, column2
FROM table_name
WHERE condition;

-- Oracle
CREATE OR REPLACE VIEW view_name AS
SELECT column1, column2
FROM table_name
WHERE condition;
```

### Drop View
```sql
-- Drop view
DROP VIEW view_name;

-- Drop view if exists (PostgreSQL)
DROP VIEW IF EXISTS view_name;

-- Drop view with schema (SQL Server)
DROP VIEW schema_name.view_name;
```

### View Information
```sql
-- List views (MySQL)
SHOW FULL TABLES IN database_name WHERE TABLE_TYPE = 'VIEW';

-- List views (PostgreSQL)
SELECT table_name, table_type
FROM information_schema.tables
WHERE table_type = 'VIEW';

-- List views (SQL Server)
SELECT name, type_desc
FROM sys.objects
WHERE type = 'V';

-- View definition (MySQL)
SHOW CREATE VIEW view_name;

-- View definition (PostgreSQL)
SELECT definition
FROM information_schema.views
WHERE table_name = 'view_name';

-- View definition (SQL Server)
SELECT definition
FROM sys.sql_modules
WHERE object_id = OBJECT_ID('view_name');
```

## Updatable Views

### Simple Updatable View
```sql
-- Simple updatable view (single table, no aggregation)
CREATE VIEW vw_updatable_employees AS
SELECT employee_id, first_name, last_name, email, phone
FROM employees;

-- This view is updatable
INSERT INTO vw_updatable_employees (employee_id, first_name, last_name, email, phone)
VALUES (101, 'John', 'Doe', 'john.doe@company.com', '555-1234');

UPDATE vw_updatable_employees 
SET email = 'john.newemail@company.com' 
WHERE employee_id = 101;

DELETE FROM vw_updatable_employees WHERE employee_id = 101;
```

### Complex Updatable View
```sql
-- SQL Server updatable view with INSTEAD OF triggers
CREATE VIEW vw_employee_department AS
SELECT 
    e.employee_id,
    e.first_name,
    e.last_name,
    e.email,
    d.department_name
FROM employees e
JOIN departments d ON e.department_id = d.department_id;

-- Create INSTEAD OF triggers for updates
CREATE TRIGGER tr_employee_department_insert
ON vw_employee_department
INSTEAD OF INSERT
AS
BEGIN
    INSERT INTO employees (employee_id, first_name, last_name, email, department_id)
    SELECT 
        employee_id, 
        first_name, 
        last_name, 
        email,
        d.department_id
    FROM inserted i
    JOIN departments d ON i.department_name = d.department_name;
END;

CREATE TRIGGER tr_employee_department_update
ON vw_employee_department
INSTEAD OF UPDATE
AS
BEGIN
    UPDATE e
    SET e.first_name = i.first_name,
        e.last_name = i.last_name,
        e.email = i.email,
        e.department_id = d.department_id
    FROM employees e
    JOIN inserted i ON e.employee_id = i.employee_id
    JOIN departments d ON i.department_name = d.department_name;
END;
```

## Performance Considerations

### View Optimization
```sql
-- Create index on view columns
CREATE INDEX idx_view_employee_name ON vw_employee_info(first_name, last_name);

-- Use indexed views for complex queries (SQL Server)
CREATE VIEW vw_employee_performance WITH SCHEMABINDING
AS
SELECT employee_id, first_name, last_name, email, salary
FROM employees
WHERE status = 'ACTIVE';

CREATE UNIQUE CLUSTERED INDEX idx_view_performance 
ON vw_employee_performance (employee_id);

-- Use materialized views for expensive queries (PostgreSQL)
CREATE MATERIALIZED VIEW vw_expensive_report AS
SELECT 
    d.department_name,
    COUNT(e.employee_id) as employee_count,
    AVG(e.salary) as avg_salary,
    MAX(e.salary) as max_salary,
    MIN(e.salary) as min_salary
FROM departments d
LEFT JOIN employees e ON d.department_id = e.department_id
GROUP BY d.department_name;

-- Schedule refresh for materialized view
CREATE OR REPLACE FUNCTION refresh_department_stats()
RETURNS void AS $$
BEGIN
    REFRESH MATERIALIZED VIEW vw_expensive_report;
END;
$$ LANGUAGE plpgsql;
```

### View vs Stored Procedure
```sql
-- View: Simple data access
CREATE VIEW vw_employee_summary AS
SELECT 
    employee_id,
    first_name,
    last_name,
    salary
FROM employees
WHERE status = 'ACTIVE';

-- Stored procedure: Complex business logic
CREATE PROCEDURE sp_get_employee_summary(
    @department_id INT,
    @min_salary DECIMAL(10,2)
)
AS
BEGIN
    SELECT 
        employee_id,
        first_name,
        last_name,
        salary
    FROM employees
    WHERE department_id = @department_id
    AND salary >= @min_salary
    AND status = 'ACTIVE'
    ORDER BY salary DESC;
END;
```

## Examples

### Employee Management Views
```sql
-- 1. Employee directory view
CREATE VIEW vw_employee_directory AS
SELECT 
    e.employee_id,
    e.first_name,
    e.last_name,
    e.email,
    e.phone,
    d.department_name,
    j.job_title,
    l.city,
    l.state,
    CASE 
        WHEN e.status = 'ACTIVE' THEN 'Active'
        WHEN e.status = 'INACTIVE' THEN 'Inactive'
        ELSE 'Unknown'
    END as status
FROM employees e
JOIN departments d ON e.department_id = d.department_id
JOIN jobs j ON e.job_id = j.job_id
JOIN locations l ON d.location_id = l.location_id;

-- 2. Employee performance view
CREATE VIEW vw_employee_performance AS
SELECT 
    e.employee_id,
    e.first_name,
    e.last_name,
    e.salary,
    e.hire_date,
    DATEDIFF(YEAR, e.hire_date, GETDATE()) as years_of_service,
    e.salary / (
        SELECT AVG(salary) 
        FROM employees 
        WHERE department_id = e.department_id
    ) as salary_ratio,
    (
        SELECT COUNT(*) 
        FROM performance_reviews pr 
        WHERE pr.employee_id = e.employee_id 
        AND pr.rating >= 4
    ) as excellent_reviews
FROM employees e
WHERE e.status = 'ACTIVE';

-- 3. Department statistics view
CREATE VIEW vw_department_statistics AS
SELECT 
    d.department_id,
    d.department_name,
    COUNT(e.employee_id) as total_employees,
    COUNT(CASE WHEN e.hire_date >= DATEADD(YEAR, -1, GETDATE()) THEN 1 END) as new_hires,
    AVG(e.salary) as avg_salary,
    MIN(e.salary) as min_salary,
    MAX(e.salary) as max_salary,
    SUM(e.salary) as total_payroll,
    AVG(DATEDIFF(YEAR, e.hire_date, GETDATE())) as avg_tenure
FROM departments d
LEFT JOIN employees e ON d.department_id = e.department_id
GROUP BY d.department_id, d.department_name;

-- 4. Employee compensation view
CREATE VIEW vw_employee_compensation AS
SELECT 
    e.employee_id,
    e.first_name,
    e.last_name,
    e.salary as base_salary,
    e.salary * 0.10 as bonus,
    e.salary * 0.05 as benefits,
    e.salary + (e.salary * 0.10) + (e.salary * 0.05) as total_compensation,
    CASE 
        WHEN e.salary > 100000 THEN 'Executive'
        WHEN e.salary > 75000 THEN 'Senior'
        WHEN e.salary > 50000 THEN 'Mid'
        ELSE 'Junior'
    END as compensation_level
FROM employees e
WHERE e.status = 'ACTIVE';
```

### Financial Reporting Views
```sql
-- 1. Monthly sales view
CREATE VIEW vw_monthly_sales AS
SELECT 
    YEAR(order_date) as year,
    MONTH(order_date) as month,
    COUNT(DISTINCT customer_id) as unique_customers,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue,
    AVG(total_amount) as avg_order_value
FROM orders
GROUP BY YEAR(order_date), MONTH(order_date);

-- 2. Product performance view
CREATE VIEW vw_product_performance AS
SELECT 
    p.product_id,
    p.product_name,
    p.category,
    COUNT(oi.order_id) as order_count,
    SUM(oi.quantity) as total_quantity_sold,
    SUM(oi.quantity * oi.unit_price) as total_revenue,
    AVG(oi.unit_price) as avg_price,
    SUM(oi.quantity * oi.unit_price) / 
        SUM(oi.quantity) * AVG(oi.unit_price) * COUNT(DISTINCT oi.order_id) as market_share
FROM products p
JOIN order_items oi ON p.product_id = oi.product_id
GROUP BY p.product_id, p.product_name, p.category;

-- 3. Customer lifetime value view
CREATE VIEW vw_customer_lifetime_value AS
SELECT 
    c.customer_id,
    c.customer_name,
    c.registration_date,
    COUNT(o.order_id) as total_orders,
    SUM(o.total_amount) as total_spent,
    AVG(o.total_amount) as avg_order_value,
    DATEDIFF(DAY, c.registration_date, GETDATE()) as customer_age_days,
    SUM(o.total_amount) / NULLIF(DATEDIFF(DAY, c.registration_date, GETDATE()), 0) * 365 as annual_value
FROM customers c
JOIN orders o ON c.customer_id = o.customer_id
GROUP BY c.customer_id, c.customer_name, c.registration_date;
```

## Best Practices
- Use views to simplify complex queries
- Create views for frequently used data combinations
- Use materialized views for expensive queries
- Keep views simple and performant
- Document view purpose and usage
- Avoid complex joins in views when possible
- Use appropriate indexes for underlying tables
- Consider security implications (row-level security)
- Test view performance with real data
- Use views to implement data abstraction layer
- Avoid overusing views - don't create views for every query
- Regularly review and optimize views
- Use views to implement data access policies
