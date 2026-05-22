# SQL Performance Optimization

## Query Optimization

### Execution Plans
```sql
-- Analyze query execution plan
EXPLAIN SELECT * FROM employees WHERE department_id = 50;

-- Detailed execution plan (PostgreSQL)
EXPLAIN ANALYZE SELECT * FROM employees WHERE department_id = 50;

-- Execution plan with statistics (MySQL)
EXPLAIN FORMAT=JSON SELECT * FROM employees WHERE department_id = 50;

-- Show execution plan (SQL Server)
SET SHOWPLAN_TEXT ON;
GO
SELECT * FROM employees WHERE department_id = 50;
GO
SET SHOWPLAN_TEXT OFF;
```

### Query Rewriting
```sql
-- Avoid SELECT *
-- Bad: Selects all columns
SELECT * FROM employees WHERE department_id = 50;

-- Good: Select only needed columns
SELECT employee_id, first_name, last_name, email 
FROM employees WHERE department_id = 50;

-- Use EXISTS instead of IN for subqueries
-- Bad: Can be slow with large subquery results
SELECT * FROM employees e
WHERE e.department_id IN (
    SELECT department_id FROM departments WHERE location_id = 1700
);

-- Good: Often more efficient
SELECT * FROM employees e
WHERE EXISTS (
    SELECT 1 FROM departments d 
    WHERE d.department_id = e.department_id 
    AND d.location_id = 1700
);

-- Use JOIN instead of subquery when possible
-- Bad: Subquery in WHERE clause
SELECT * FROM employees e
WHERE e.department_id = (
    SELECT department_id FROM departments WHERE department_name = 'IT'
);

-- Good: JOIN
SELECT e.* FROM employees e
JOIN departments d ON e.department_id = d.department_id
WHERE d.department_name = 'IT';
```

## Index Optimization

### Index Usage Analysis
```sql
-- Check index usage (PostgreSQL)
SELECT 
    schemaname,
    tablename,
    indexname,
    idx_scan,
    idx_tup_read,
    idx_tup_fetch
FROM pg_stat_user_indexes
WHERE tablename = 'employees';

-- Check unused indexes (PostgreSQL)
SELECT 
    schemaname,
    tablename,
    indexname,
    idx_scan
FROM pg_stat_user_indexes
WHERE tablename = 'employees' AND idx_scan = 0;

-- Index statistics (MySQL)
SHOW INDEX FROM employees;
ANALYZE TABLE employees;
```

### Composite Index Strategy
```sql
-- Create effective composite indexes
-- Order columns by selectivity and query patterns
CREATE INDEX idx_employees_dept_job_salary 
ON employees(department_id, job_id, salary);

-- This index supports multiple query patterns
SELECT * FROM employees WHERE department_id = 50 AND job_id = 'IT_PROG';
SELECT * FROM employees WHERE department_id = 50 ORDER BY salary;
SELECT * FROM employees WHERE department_id = 50 AND job_id = 'IT_PROG' ORDER BY salary;
```

### Covering Indexes
```sql
-- Create covering index to avoid table lookups
CREATE INDEX idx_employees_covering 
ON employees(department_id, job_id, salary, first_name, last_name);

-- Query satisfied by index only
SELECT department_id, job_id, salary, first_name, last_name
FROM employees 
WHERE department_id = 50 AND job_id = 'IT_PROG';
```

## Database Configuration

### Memory Settings
```sql
-- PostgreSQL memory configuration
-- shared_buffers: 25% of RAM
-- work_mem: Per operation memory
-- maintenance_work_mem: Maintenance operations
-- effective_cache_size: System cache estimate

-- Check current settings
SHOW shared_buffers;
SHOW work_mem;
SHOW effective_cache_size;

-- MySQL memory configuration
-- innodb_buffer_pool_size: 70-80% of RAM
-- innodb_log_file_size: Transaction log size
-- innodb_flush_log_at_trx_commit: Flush behavior

-- Check current settings
SHOW VARIABLES LIKE 'innodb_buffer_pool_size';
SHOW VARIABLES LIKE 'innodb_log_file_size';
```

### Connection Pooling
```sql
-- Connection pool settings
-- max_connections: Maximum concurrent connections
-- connection_timeout: Connection timeout
-- idle_timeout: Idle connection timeout

-- PostgreSQL
SHOW max_connections;
SHOW shared_buffers;

-- MySQL
SHOW VARIABLES LIKE 'max_connections';
SHOW VARIABLES LIKE 'wait_timeout';
```

## Partitioning

### Table Partitioning
```sql
-- Range partitioning (PostgreSQL)
CREATE TABLE orders (
    order_id BIGINT,
    customer_id INT,
    order_date DATE,
    total_amount DECIMAL(10,2)
) PARTITION BY RANGE (order_date);

-- Create partitions
CREATE TABLE orders_2023 PARTITION OF orders
    FOR VALUES FROM ('2023-01-01') TO ('2024-01-01');

CREATE TABLE orders_2024 PARTITION OF orders
    FOR VALUES FROM ('2024-01-01') TO ('2025-01-01');

-- List partitioning (MySQL)
CREATE TABLE employees (
    employee_id INT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    department_id INT
)
PARTITION BY LIST (department_id) (
    PARTITION p_it VALUES IN (10, 20, 30),
    PARTITION p_sales VALUES IN (40, 50),
    PARTITION p_hr VALUES IN (60, 70),
    PARTITION p_other VALUES IN (DEFAULT)
);
```

### Partition Pruning
```sql
-- Query benefits from partition pruning
-- Only scans relevant partitions
SELECT * FROM orders WHERE order_date BETWEEN '2023-06-01' AND '2023-06-30';
-- Only scans orders_2023 partition

-- Add new partitions
ALTER TABLE orders ADD PARTITION orders_2025
    FOR VALUES FROM ('2025-01-01') TO ('2026-01-01');

-- Drop old partitions
ALTER TABLE orders DROP PARTITION orders_2022;
```

## Caching Strategies

### Query Caching
```sql
-- MySQL query cache
SHOW VARIABLES LIKE 'query_cache%';
SHOW STATUS LIKE 'Qcache%';

-- Enable query cache
SET GLOBAL query_cache_type = ON;
SET GLOBAL query_cache_size = 268435456; -- 256MB

-- PostgreSQL prepared statements
PREPARE get_employees(int) AS
SELECT * FROM employees WHERE department_id = $1;

EXECUTE get_employees(50);
DEALLOCATE PREPARE get_employees;
```

### Materialized Views
```sql
-- Create materialized view (PostgreSQL)
CREATE MATERIALIZED VIEW employee_summary AS
SELECT 
    department_id,
    COUNT(*) as employee_count,
    AVG(salary) as avg_salary,
    MIN(salary) as min_salary,
    MAX(salary) as max_salary
FROM employees
GROUP BY department_id;

-- Refresh materialized view
REFRESH MATERIALIZED VIEW employee_summary;

-- Query materialized view (fast)
SELECT * FROM employee_summary WHERE department_id = 50;

-- Oracle materialized view
CREATE MATERIALIZED VIEW employee_summary
BUILD IMMEDIATE
REFRESH COMPLETE ON DEMAND
AS
SELECT 
    department_id,
    COUNT(*) as employee_count,
    AVG(salary) as avg_salary
FROM employees
GROUP BY department_id;
```

## Statistical Analysis

### Table Statistics
```sql
-- Update table statistics (PostgreSQL)
ANALYZE employees;

-- Update table statistics (MySQL)
ANALYZE TABLE employees;

-- Update table statistics (SQL Server)
UPDATE STATISTICS employees;

-- Check statistics (PostgreSQL)
SELECT 
    schemaname,
    tablename,
    attname,
    n_distinct,
    correlation
FROM pg_stats
WHERE tablename = 'employees';
```

### Histogram Statistics
```sql
-- Create extended statistics (PostgreSQL)
CREATE STATISTICS employees_stats (department_id, salary)
ON employees;

-- Update extended statistics
ANALYZE employees;

-- Check statistics (MySQL)
SHOW INDEX STATISTICS;
```

## Performance Monitoring

### Slow Query Log
```sql
-- Enable slow query log (MySQL)
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;
SET GLOBAL log_queries_not_using_indexes = 'ON';

-- Check slow query log settings
SHOW VARIABLES LIKE 'slow_query_log%';
SHOW VARIABLES LIKE 'long_query_time';

-- PostgreSQL log_min_duration_statement
ALTER SYSTEM SET log_min_duration_statement = 1000; -- 1 second
SELECT pg_reload_conf();
```

### Performance Metrics
```sql
-- MySQL performance schema
SELECT 
    thread_id,
    event_name,
    timer_wait/1000000000 as duration_seconds
FROM performance_schema.events_waits_current
WHERE thread_id != CONNECTION_ID();

-- PostgreSQL pg_stat_activity
SELECT 
    pid,
    usename,
    application_name,
    state,
    query,
    query_start
FROM pg_stat_activity
WHERE state = 'active';

-- SQL Server performance counters
SELECT 
    counter_name,
    cntr_value
FROM sys.dm_os_performance_counters
WHERE counter_name LIKE '%SQL%';
```

## Database-Specific Optimization

### MySQL Optimization
```sql
-- MySQL engine optimization
-- InnoDB buffer pool size
SET GLOBAL innodb_buffer_pool_size = 1073741824; -- 1GB

-- InnoDB log file size
SET GLOBAL innodb_log_file_size = 268435456; -- 256MB

-- Query cache optimization
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL query_cache_type = ON;

-- MySQL EXPLAIN optimization
EXPLAIN SELECT * FROM employees WHERE department_id = 50;

-- Force index usage
SELECT * FROM employees USE INDEX (idx_employees_department_id)
WHERE department_id = 50;
```

### PostgreSQL Optimization
```sql
-- PostgreSQL configuration
-- shared_buffers
ALTER SYSTEM SET shared_buffers = '256MB';

-- work_mem
ALTER SYSTEM SET work_mem = '4MB';

-- maintenance_work_mem
ALTER SYSTEM SET work_mem = '64MB';

-- PostgreSQL EXPLAIN ANALYZE
EXPLAIN ANALYZE SELECT * FROM employees WHERE department_id = 50;

-- PostgreSQL index usage
SELECT * FROM pg_stat_user_indexes WHERE tablename = 'employees';
```

### SQL Server Optimization
```sql
-- SQL Server configuration
-- Max memory
EXEC sp_configure 'max server memory', 2147483648; -- 2GB

-- Max degree of parallelism
EXEC sp_configure 'max degree of parallelism', 4;

-- SQL Server execution plan
SET SHOWPLAN_XML ON;
GO
SELECT * FROM employees WHERE department_id = 50;
GO
SET SHOWPLAN_XML OFF;

-- SQL Server index usage
SELECT 
    object_name(i.object_id) AS table_name,
    i.name AS index_name,
    s.user_seeks,
    s.user_scans
FROM sys.indexes i
JOIN sys.dm_db_index_usage_stats s ON i.object_id = s.object_id AND i.index_id = s.index_id
WHERE object_name(i.object_id) = 'employees';
```

## Query Examples

### Optimized Queries
```sql
-- Complex query with multiple optimizations
SELECT 
    e.employee_id,
    e.first_name,
    e.last_name,
    e.salary,
    d.department_name,
    j.job_title
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
INNER JOIN jobs j ON e.job_id = j.job_id
WHERE e.salary > (
    SELECT AVG(salary) * 1.2 
    FROM employees 
    WHERE department_id = e.department_id
)
AND e.hire_date >= '2023-01-01'
ORDER BY e.salary DESC
LIMIT 10;

-- Optimized with indexes
CREATE INDEX idx_employees_dept_salary ON employees(department_id, salary);
CREATE INDEX idx_employees_hire_date ON employees(hire_date);
CREATE INDEX idx_departments_id ON departments(department_id);
CREATE INDEX idx_jobs_id ON jobs(job_id);
```

### Performance Comparison
```sql
-- Before optimization (full table scan)
SELECT * FROM orders 
WHERE order_date BETWEEN '2023-01-01' AND '2023-12-31'
AND total_amount > 1000;

-- After optimization (partition + index)
-- Table partitioned by order_date
-- Index on total_amount
SELECT * FROM orders 
WHERE order_date BETWEEN '2023-01-01' AND '2023-12-31'
AND total_amount > 1000;
```

## Best Practices
- Use appropriate indexes for query patterns
- Avoid SELECT * in production queries
- Use EXISTS instead of IN for large subqueries
- Implement proper connection pooling
- Regularly update table statistics
- Monitor and analyze slow queries
- Use partitioning for large tables
- Implement caching strategies
- Optimize database configuration
- Regular performance testing and benchmarking
- Use EXPLAIN ANALYZE to understand query execution
- Consider materialized views for complex aggregations
- Implement proper database maintenance routines
