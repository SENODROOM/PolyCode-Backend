# SQL Indexes

## Index Basics

### What is an Index
```sql
-- Indexes improve query performance
-- They work like book indexes
-- Trade-off: Faster reads, slower writes
-- Types: B-tree, Hash, Full-text, Spatial
```

### Create Basic Index
```sql
-- Create index on single column
CREATE INDEX idx_employees_last_name ON employees(last_name);

-- Create index on multiple columns
CREATE INDEX idx_employees_dept_salary ON employees(department_id, salary);

-- Create unique index
CREATE UNIQUE INDEX idx_employees_email ON employees(email);
```

## Index Types

### B-Tree Index
```sql
-- Default index type in most databases
-- Good for equality and range queries
-- Supports ORDER BY efficiently

CREATE INDEX idx_employees_salary ON employees(salary);

-- Range queries benefit from B-tree
SELECT * FROM employees WHERE salary BETWEEN 50000 AND 70000;
```

### Hash Index
```sql
-- Good for equality queries only
-- Memory-based (MySQL)
-- Not supported in all databases

-- MySQL hash index
CREATE INDEX idx_employees_email_hash ON employees(email) USING HASH;

-- Equality queries
SELECT * FROM employees WHERE email = 'john.doe@company.com';
```

### Full-Text Index
```sql
-- For text searching
-- Supports natural language queries

-- MySQL full-text index
CREATE FULLTEXT INDEX idx_employees_bio ON employees(bio);

-- Full-text search
SELECT * FROM employees 
WHERE MATCH(bio) AGAINST('experienced developer' IN NATURAL LANGUAGE MODE);
```

### Spatial Index
```sql
-- For geographic data
-- Supports spatial queries

-- PostGIS spatial index
CREATE INDEX idx_locations_geom ON locations USING GIST(geom);

-- Spatial query
SELECT * FROM locations 
WHERE ST_Contains(geom, ST_GeomFromText('POINT(40.7128 -74.0060)'));
```

## Index Strategies

### Single Column Index
```sql
-- Index on frequently queried column
CREATE INDEX idx_employees_email ON employees(email);

-- Good for WHERE clause
SELECT * FROM employees WHERE email = 'john.doe@company.com';
```

### Composite Index
```sql
-- Index on multiple columns
CREATE INDEX idx_employees_dept_job ON employees(department_id, job_id);

-- Column order matters
-- Most selective column first
-- Used in WHERE clause order

-- Uses index efficiently
SELECT * FROM employees WHERE department_id = 50 AND job_id = 'IT_PROG';

-- Only uses first part of index
SELECT * FROM employees WHERE department_id = 50;
```

### Covering Index
```sql
-- Index that covers all query columns
CREATE INDEX idx_employees_covering ON employees(department_id, salary, first_name, last_name);

-- Query satisfied by index alone
SELECT department_id, salary, first_name, last_name 
FROM employees 
WHERE department_id = 50;
```

### Partial Index
```sql
-- Index on subset of data
-- PostgreSQL syntax
CREATE INDEX idx_employees_active ON employees(employee_id) WHERE status = 'ACTIVE';

-- MySQL syntax (conditional index)
CREATE INDEX idx_employees_high_salary ON employees(employee_id) WHERE salary > 80000;

-- Query benefits
SELECT * FROM employees WHERE status = 'ACTIVE';
```

## Database-Specific Indexes

### MySQL Indexes
```sql
-- MySQL index types
CREATE INDEX idx_name ON table(column); -- B-tree
CREATE UNIQUE INDEX idx_email ON table(email);
CREATE FULLTEXT INDEX idx_text ON table(text_column);
CREATE SPATIAL INDEX idx_geom ON table(geom_column);

-- MySQL composite index
CREATE INDEX idx_composite ON table(col1, col2, col3);

-- MySQL functional index (8.0+)
CREATE INDEX idx_email_lower ON table((LOWER(email)));
```

### PostgreSQL Indexes
```sql
-- PostgreSQL index types
CREATE INDEX idx_name ON table(column); -- B-tree
CREATE UNIQUE INDEX idx_email ON table(email);
CREATE INDEX idx_text_gin ON table USING GIN(text_column); -- Full-text
CREATE INDEX idx_geom_gist ON table USING GIST(geom_column); -- Spatial

-- PostgreSQL functional index
CREATE INDEX idx_email_lower ON table(LOWER(email));

-- PostgreSQL partial index
CREATE INDEX idx_active ON table(id) WHERE status = 'ACTIVE';

-- PostgreSQL expression index
CREATE INDEX idx_salary_bonus ON table((salary + bonus));
```

### SQL Server Indexes
```sql
-- SQL Server index types
CREATE INDEX idx_name ON table(column); -- B-tree
CREATE UNIQUE INDEX idx_email ON table(email);
CREATE FULLTEXT CATALOG ft_catalog AS DEFAULT;
CREATE FULLTEXT INDEX ON table(text_column);

-- SQL Server filtered index
CREATE INDEX idx_high_salary ON table(salary) WHERE salary > 80000;

-- SQL Server included columns
CREATE INDEX idx_covering ON table(department_id) INCLUDE (first_name, last_name);
```

## Index Management

### Create Index
```sql
-- Basic index creation
CREATE INDEX idx_employees_last_name ON employees(last_name);

-- With specific options
CREATE INDEX idx_employees_salary ON employees(salary) 
TABLESPACE users;

-- Concurrent creation (PostgreSQL)
CREATE INDEX CONCURRENTLY idx_employees_email ON employees(email);
```

### Drop Index
```sql
-- Drop index
DROP INDEX idx_employees_last_name;

-- Drop with IF EXISTS (PostgreSQL)
DROP INDEX IF EXISTS idx_employees_last_name;

-- Drop index with cascade (PostgreSQL)
DROP INDEX idx_employees_last_name CASCADE;
```

### Rebuild Index
```sql
-- Rebuild index (SQL Server)
ALTER INDEX idx_employees_last_name REBUILD;

-- Rebuild all indexes (MySQL)
ANALYZE TABLE employees;

-- Reindex table (PostgreSQL)
REINDEX TABLE employees;

-- Reindex specific index (PostgreSQL)
REINDEX INDEX idx_employees_last_name;
```

## Index Analysis

### Check Index Usage
```sql
-- PostgreSQL index usage statistics
SELECT 
    schemaname,
    tablename,
    indexname,
    idx_scan,
    idx_tup_read,
    idx_tup_fetch
FROM pg_stat_user_indexes
WHERE tablename = 'employees';

-- MySQL index usage
SHOW INDEX FROM employees;

-- SQL Server index usage
SELECT 
    object_name(i.object_id) AS table_name,
    i.name AS index_name,
    s.user_seeks,
    s.user_scans,
    s.user_lookups,
    s.user_updates
FROM sys.indexes i
JOIN sys.dm_db_index_usage_stats s ON i.object_id = s.object_id AND i.index_id = s.index_id
WHERE OBJECT_NAME(i.object_id) = 'employees';
```

### Explain Plan Analysis
```sql
-- PostgreSQL explain
EXPLAIN ANALYZE 
SELECT * FROM employees WHERE last_name = 'Smith';

-- MySQL explain
EXPLAIN 
SELECT * FROM employees WHERE last_name = 'Smith';

-- SQL Server explain
SET SHOWPLAN_TEXT ON;
GO
SELECT * FROM employees WHERE last_name = 'Smith';
GO
SET SHOWPLAN_TEXT OFF;
```

## Performance Optimization

### Index Selection
```sql
-- Choose columns for indexing
-- High cardinality columns (unique values)
-- Frequently used in WHERE clauses
-- Used in JOIN conditions
-- Used in ORDER BY clauses

-- Good candidates
CREATE INDEX idx_employee_id ON employees(employee_id); -- Primary key
CREATE INDEX idx_email ON employees(email); -- Unique, frequent lookups
CREATE INDEX idx_department_id ON employees(department_id); -- Foreign key, joins

-- Poor candidates
CREATE INDEX idx_gender ON employees(gender); -- Low cardinality
CREATE INDEX idx_status ON employees(status); -- Low selectivity
```

### Composite Index Order
```sql
-- Order columns by selectivity
-- Most selective first
-- Consider query patterns

-- Good order for this query pattern
CREATE INDEX idx_dept_job_salary ON employees(department_id, job_id, salary);

-- Queries that benefit
SELECT * FROM employees WHERE department_id = 50 AND job_id = 'IT_PROG';
SELECT * FROM employees WHERE department_id = 50;
SELECT * FROM employees WHERE department_id = 50 ORDER BY salary;
```

### Covering Indexes
```sql
-- Index covers all query columns
CREATE INDEX idx_covering ON employees(department_id, job_id, salary, first_name, last_name);

-- Query satisfied by index only
SELECT department_id, job_id, salary, first_name, last_name
FROM employees 
WHERE department_id = 50 AND job_id = 'IT_PROG';
```

## Index Maintenance

### Fragmentation
```sql
-- Check index fragmentation (SQL Server)
SELECT 
    object_name(i.object_id) AS table_name,
    i.name AS index_name,
    s.avg_fragmentation_in_percent
FROM sys.dm_db_index_physical_stats(DB_ID(), NULL, NULL, NULL, 'LIMITED') s
JOIN sys.indexes i ON s.object_id = i.object_id AND s.index_id = i.index_id
WHERE object_name(i.object_id) = 'employees';

-- Rebuild fragmented indexes (SQL Server)
ALTER INDEX idx_employees_last_name REBUILD;
```

### Statistics
```sql
-- Update statistics (SQL Server)
UPDATE STATISTICS employees;

-- Update statistics (MySQL)
ANALYZE TABLE employees;

-- Update statistics (PostgreSQL)
ANALYZE employees;
```

## Examples

### Employee Table Indexing
```sql
-- Primary key (usually created automatically)
ALTER TABLE employees ADD PRIMARY KEY (employee_id);

-- Unique index for email
CREATE UNIQUE INDEX idx_employees_email ON employees(email);

-- Index for department lookups
CREATE INDEX idx_employees_department_id ON employees(department_id);

-- Composite index for common queries
CREATE INDEX idx_employees_dept_job ON employees(department_id, job_id);

-- Index for salary range queries
CREATE INDEX idx_employees_salary ON employees(salary);

-- Index for name searches
CREATE INDEX idx_employees_name ON employees(last_name, first_name);

-- Partial index for active employees
CREATE INDEX idx_employees_active ON employees(employee_id) WHERE status = 'ACTIVE';
```

### Query Optimization Examples
```sql
-- Before indexing
SELECT * FROM employees WHERE last_name = 'Smith'; -- Full table scan

-- After indexing
CREATE INDEX idx_employees_last_name ON employees(last_name);
SELECT * FROM employees WHERE last_name = 'Smith'; -- Index seek

-- Complex query optimization
SELECT e.first_name, e.last_name, d.department_name
FROM employees e
JOIN departments d ON e.department_id = d.department_id
WHERE e.salary > 50000
ORDER BY e.last_name;

-- Optimized with indexes
CREATE INDEX idx_employees_dept_salary ON employees(department_id, salary);
CREATE INDEX idx_employees_last_name ON employees(last_name);
CREATE INDEX idx_departments_id ON departments(department_id);
```

## Best Practices
- Index frequently queried columns
- Keep indexes minimal - don't over-index
- Consider query patterns when designing indexes
- Use covering indexes to avoid table lookups
- Monitor index usage and remove unused indexes
- Regularly maintain indexes (rebuild, update stats)
- Test index performance with real data
- Consider index size and storage costs
- Use partial indexes for conditional data
- Avoid indexing frequently updated columns when possible
