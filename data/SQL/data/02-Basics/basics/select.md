# SQL SELECT Statement

## Basic SELECT

### Select All Columns
```sql
-- Select all columns from a table
SELECT * FROM employees;

-- Select all columns with table alias
SELECT e.* FROM employees e;
```

### Select Specific Columns
```sql
-- Select specific columns
SELECT employee_id, first_name, last_name FROM employees;

-- Select with column aliases
SELECT 
    employee_id AS "Employee ID",
    first_name AS "First Name",
    last_name AS "Last Name"
FROM employees;
```

## Filtering with WHERE

### Basic WHERE Conditions
```sql
-- Select with single condition
SELECT * FROM employees WHERE department_id = 10;

-- Multiple conditions with AND
SELECT * FROM employees 
WHERE department_id = 10 AND salary > 50000;

-- Multiple conditions with OR
SELECT * FROM employees 
WHERE department_id = 10 OR department_id = 20;

-- Using IN operator
SELECT * FROM employees 
WHERE department_id IN (10, 20, 30);

-- Using BETWEEN operator
SELECT * FROM employees 
WHERE salary BETWEEN 30000 AND 70000;

-- Using LIKE operator
SELECT * FROM employees 
WHERE first_name LIKE 'J%';

-- Using IS NULL
SELECT * FROM employees 
WHERE manager_id IS NULL;
```

## Sorting Results

### ORDER BY
```sql
-- Sort by single column
SELECT * FROM employees ORDER BY last_name;

-- Sort by multiple columns
SELECT * FROM employees 
ORDER BY department_id, last_name;

-- Sort with ASC/DESC
SELECT * FROM employees 
ORDER BY salary DESC, last_name ASC;

-- Sort by column position
SELECT employee_id, first_name, last_name 
FROM employees 
ORDER BY 2, 3;
```

## Limiting Results

### LIMIT and OFFSET
```sql
-- MySQL/PostgreSQL syntax
SELECT * FROM employees LIMIT 10;

-- Skip first 5 rows, get next 10
SELECT * FROM employees LIMIT 10 OFFSET 5;

-- SQL Server syntax
SELECT TOP 10 * FROM employees;

-- Oracle syntax (ROWNUM)
SELECT * FROM employees WHERE ROWNUM <= 10;
```

## Distinct Values

### DISTINCT
```sql
-- Get unique department IDs
SELECT DISTINCT department_id FROM employees;

-- Get unique combinations
SELECT DISTINCT department_id, job_id FROM employees;
```

## Aggregation

### COUNT, SUM, AVG, MIN, MAX
```sql
-- Count rows
SELECT COUNT(*) FROM employees;
SELECT COUNT(employee_id) FROM employees;
SELECT COUNT(DISTINCT department_id) FROM employees;

-- Sum values
SELECT SUM(salary) FROM employees;

-- Average values
SELECT AVG(salary) FROM employees;

-- Minimum and maximum
SELECT MIN(salary), MAX(salary) FROM employees;
```

## Grouping

### GROUP BY
```sql
-- Group by department
SELECT department_id, COUNT(*) 
FROM employees 
GROUP BY department_id;

-- Group by multiple columns
SELECT department_id, job_id, COUNT(*) 
FROM employees 
GROUP BY department_id, job_id;

-- Group by with HAVING
SELECT department_id, AVG(salary) 
FROM employees 
GROUP BY department_id 
HAVING AVG(salary) > 50000;
```

## Examples

### Employee Queries
```sql
-- Find employees in IT department
SELECT employee_id, first_name, last_name, salary
FROM employees 
WHERE department_id = 60
ORDER BY salary DESC;

-- Find highest paid employees by department
SELECT department_id, employee_id, first_name, salary
FROM (
    SELECT 
        department_id, 
        employee_id, 
        first_name, 
        salary,
        RANK() OVER (PARTITION BY department_id ORDER BY salary DESC) as rank
    FROM employees
) ranked
WHERE rank = 1;
```

## Best Practices
- Use specific columns instead of SELECT * when possible
- Use table aliases for complex queries
- Index columns used in WHERE clauses
- Avoid using functions on indexed columns in WHERE clauses
- Use appropriate data types for columns
