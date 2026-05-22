# SQL Functions

## Aggregate Functions

### COUNT
```sql
-- Count all rows
SELECT COUNT(*) FROM employees;

-- Count non-null values
SELECT COUNT(first_name) FROM employees;

-- Count distinct values
SELECT COUNT(DISTINCT department_id) FROM employees;

-- Count with conditions
SELECT 
    COUNT(*) as total_employees,
    COUNT(CASE WHEN salary > 50000 THEN 1 END) as high_earners
FROM employees;
```

### SUM
```sql
-- Sum all values
SELECT SUM(salary) FROM employees;

-- Sum with conditions
SELECT SUM(CASE WHEN department_id = 50 THEN salary END) as dept_50_total
FROM employees;

-- Sum distinct values
SELECT SUM(DISTINCT salary) FROM employees;
```

### AVG
```sql
-- Average salary
SELECT AVG(salary) FROM employees;

-- Average with rounding
SELECT ROUND(AVG(salary), 2) as avg_salary FROM employees;

-- Average by department
SELECT department_id, AVG(salary) as avg_salary
FROM employees
GROUP BY department_id;
```

### MIN and MAX
```sql
-- Minimum and maximum values
SELECT MIN(salary), MAX(salary) FROM employees;

-- By department
SELECT department_id, MIN(salary), MAX(salary)
FROM employees
GROUP BY department_id;

-- Earliest and latest dates
SELECT MIN(hire_date), MAX(hire_date) FROM employees;
```

## String Functions

### Basic String Functions
```sql
-- Concatenation
SELECT first_name || ' ' || last_name AS full_name FROM employees;
SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM employees;

-- Length
SELECT LENGTH(first_name) FROM employees;
SELECT CHAR_LENGTH(first_name) FROM employees;

-- Upper and lower case
SELECT UPPER(first_name), LOWER(first_name) FROM employees;

-- Trim spaces
SELECT TRIM(first_name) FROM employees;
SELECT LTRIM(first_name), RTRIM(first_name) FROM employees;
```

### String Manipulation
```sql
-- Substring
SELECT SUBSTRING(first_name, 1, 3) FROM employees;
SELECT SUBSTR(first_name, 1, 3) FROM employees;

-- Replace
SELECT REPLACE(email, '@company.com', '@newcompany.com') FROM employees;

-- Position/Find
SELECT POSITION(' ' IN first_name) FROM employees;
SELECT LOCATE(' ', first_name) FROM employees;

-- Padding
SELECT LPAD(first_name, 10, '*') FROM employees;
SELECT RPAD(first_name, 10, '*') FROM employees;

-- Reverse
SELECT REVERSE(first_name) FROM employees;
```

### String Analysis
```sql
-- Extract parts
SELECT 
    email,
    SUBSTRING_INDEX(email, '@', 1) as username,
    SUBSTRING_INDEX(email, '@', -1) as domain
FROM employees;

-- Check patterns
SELECT first_name 
FROM employees 
WHERE first_name LIKE 'J%';

-- Regular expressions (PostgreSQL)
SELECT first_name 
FROM employees 
WHERE first_name ~ '^J.*';
```

## Numeric Functions

### Mathematical Functions
```sql
-- Absolute value
SELECT ABS(-10), ABS(10);

-- Rounding
SELECT ROUND(123.456, 2), ROUND(123.456);
SELECT CEIL(123.456), FLOOR(123.456);

-- Power and square root
SELECT POWER(2, 3), SQRT(16);

-- Modulo
SELECT MOD(10, 3), 10 % 3;

-- Random
SELECT RAND();
SELECT RANDOM();
```

### Numeric Analysis
```sql
-- Salary ranges
SELECT 
    salary,
    CASE 
        WHEN salary < 40000 THEN 'Low'
        WHEN salary BETWEEN 40000 AND 70000 THEN 'Medium'
        ELSE 'High'
    END as salary_range
FROM employees;

-- Percentiles
SELECT 
    PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY salary) as median,
    PERCENTILE_CONT(0.25) WITHIN GROUP (ORDER BY salary) as q25,
    PERCENTILE_CONT(0.75) WITHIN GROUP (ORDER BY salary) as q75
FROM employees;
```

## Date and Time Functions

### Current Date/Time
```sql
-- Current date and time
SELECT CURRENT_DATE, CURRENT_TIME, CURRENT_TIMESTAMP;
SELECT NOW(), SYSDATE();

-- Extract parts
SELECT 
    EXTRACT(YEAR FROM hire_date) as year,
    EXTRACT(MONTH FROM hire_date) as month,
    EXTRACT(DAY FROM hire_date) as day
FROM employees;

-- Date parts
SELECT 
    YEAR(hire_date) as year,
    MONTH(hire_date) as month,
    DAY(hire_date) as day
FROM employees;
```

### Date Calculations
```sql
-- Date arithmetic
SELECT 
    hire_date,
    hire_date + INTERVAL '1 YEAR' as plus_1_year,
    hire_date - INTERVAL '30 DAY' as minus_30_days
FROM employees;

-- Date differences
SELECT 
    DATEDIFF(CURRENT_DATE, hire_date) as days_employed,
    TIMESTAMPDIFF(YEAR, hire_date, CURRENT_DATE) as years_employed
FROM employees;

-- Date formatting
SELECT DATE_FORMAT(hire_date, '%Y-%m-%d') as formatted_date
FROM employees;

-- Date parsing
SELECT STR_TO_DATE('2024-01-15', '%Y-%m-%d') as parsed_date;
```

### Time Functions
```sql
-- Time extraction
SELECT 
    EXTRACT(HOUR FROM CURRENT_TIME) as hour,
    EXTRACT(MINUTE FROM CURRENT_TIME) as minute,
    EXTRACT(SECOND FROM CURRENT_TIME) as second;

-- Time formatting
SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i:%s') as formatted_time;
```

## Conditional Functions

### CASE Statement
```sql
-- Simple CASE
SELECT 
    salary,
    CASE salary
        WHEN < 40000 THEN 'Low'
        WHEN BETWEEN 40000 AND 70000 THEN 'Medium'
        ELSE 'High'
    END as salary_category
FROM employees;

-- Searched CASE
SELECT 
    first_name,
    salary,
    CASE 
        WHEN salary < 40000 THEN 'Low'
        WHEN salary BETWEEN 40000 AND 70000 THEN 'Medium'
        WHEN salary > 70000 THEN 'High'
        ELSE 'Unknown'
    END as salary_category
FROM employees;
```

### COALESCE and NULLIF
```sql
-- COALESCE - return first non-null value
SELECT COALESCE(commission_pct, 0) FROM employees;
SELECT COALESCE(phone, mobile, email) as contact FROM employees;

-- NULLIF - return null if values equal
SELECT NULLIF(salary, 0) FROM employees;
SELECT NULLIF(department_id, 0) FROM employees;
```

### IF Functions
```sql
-- MySQL IF
SELECT IF(salary > 50000, 'High', 'Low') as salary_level FROM employees;

-- MySQL IFNULL
SELECT IFNULL(commission_pct, 0) FROM employees;

-- PostgreSQL CASE as IF
SELECT CASE WHEN salary > 50000 THEN 'High' ELSE 'Low' END as salary_level FROM employees;
```

## Window Functions

### Ranking Functions
```sql
-- ROW_NUMBER
SELECT 
    employee_id,
    first_name,
    salary,
    ROW_NUMBER() OVER (ORDER BY salary DESC) as salary_rank
FROM employees;

-- RANK and DENSE_RANK
SELECT 
    employee_id,
    first_name,
    salary,
    RANK() OVER (ORDER BY salary DESC) as rank,
    DENSE_RANK() OVER (ORDER BY salary DESC) as dense_rank
FROM employees;

-- NTILE
SELECT 
    employee_id,
    first_name,
    salary,
    NTILE(4) OVER (ORDER BY salary DESC) as salary_quartile
FROM employees;
```

### Aggregate Window Functions
```sql
-- Running total
SELECT 
    employee_id,
    salary,
    SUM(salary) OVER (ORDER BY employee_id) as running_total
FROM employees;

-- Moving average
SELECT 
    employee_id,
    salary,
    AVG(salary) OVER (ORDER BY employee_id ROWS BETWEEN 2 PRECEDING AND 2 FOLLOWING) as moving_avg
FROM employees;

-- Partition by department
SELECT 
    employee_id,
    department_id,
    salary,
    AVG(salary) OVER (PARTITION BY department_id) as dept_avg,
    salary - AVG(salary) OVER (PARTITION BY department_id) as diff_from_avg
FROM employees;
```

### LAG and LEAD
```sql
-- Previous and next values
SELECT 
    employee_id,
    salary,
    LAG(salary, 1) OVER (ORDER BY employee_id) as prev_salary,
    LEAD(salary, 1) OVER (ORDER BY employee_id) as next_salary
FROM employees;

-- First and last values
SELECT 
    employee_id,
    salary,
    FIRST_VALUE(salary) OVER (ORDER BY salary DESC ROWS UNBOUNDED PRECEDING) as highest_salary,
    LAST_VALUE(salary) OVER (ORDER BY salary DESC ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) as lowest_salary
FROM employees;
```

## Database-Specific Functions

### MySQL Functions
```sql
-- MySQL string functions
SELECT CONCAT_WS(' ', first_name, last_name) as full_name FROM employees;
SELECT GROUP_CONCAT(first_name SEPARATOR ', ') FROM employees;

-- MySQL date functions
SELECT DATE_ADD(hire_date, INTERVAL 1 MONTH) FROM employees;
SELECT DATE_FORMAT(hire_date, '%M %d, %Y') FROM employees;

-- MySQL conditional functions
SELECT IFNULL(commission_pct, 0) FROM employees;
SELECT COALESCE(commission_pct, 0, 0.05) FROM employees;
```

### PostgreSQL Functions
```sql
-- PostgreSQL string functions
SELECT STRING_AGG(first_name, ', ') FROM employees;
SELECT REGEXP_REPLACE(email, '.*@', 'user@') FROM employees;

-- PostgreSQL date functions
SELECT hire_date + INTERVAL '1 month' FROM employees;
SELECT TO_CHAR(hire_date, 'Month DD, YYYY') FROM employees;

-- PostgreSQL array functions
SELECT ARRAY_AGG(first_name) FROM employees;
SELECT UNNEST(ARRAY[1, 2, 3]);
```

### SQL Server Functions
```sql
-- SQL Server string functions
SELECT CONCAT(first_name, ' ', last_name) as full_name FROM employees;
SELECT STUFF(first_name, 1, 3, '***') FROM employees;

-- SQL Server date functions
SELECT DATEADD(month, 1, hire_date) FROM employees;
SELECT FORMAT(hire_date, 'MMMM dd, yyyy') FROM employees;

-- SQL Server conditional functions
SELECT ISNULL(commission_pct, 0) FROM employees;
SELECT COALESCE(commission_pct, 0, 0.05) FROM employees;
```

## Examples

### Employee Analytics
```sql
-- Complete employee profile with functions
SELECT 
    employee_id,
    CONCAT(first_name, ' ', last_name) as full_name,
    UPPER(email) as uppercase_email,
    LENGTH(first_name) as name_length,
    CASE 
        WHEN salary < 40000 THEN 'Entry Level'
        WHEN salary BETWEEN 40000 AND 70000 THEN 'Mid Level'
        ELSE 'Senior Level'
    END as experience_level,
    ROUND(salary, 2) as rounded_salary,
    DATEDIFF(YEAR, hire_date, GETDATE()) as years_employed,
    DATE_FORMAT(hire_date, '%M %Y') as hire_month_year
FROM employees
WHERE YEAR(hire_date) >= 2020;

-- Department statistics with window functions
SELECT 
    department_id,
    employee_id,
    salary,
    AVG(salary) OVER (PARTITION BY department_id) as dept_avg,
    salary - AVG(salary) OVER (PARTITION BY department_id) as diff_from_avg,
    PERCENT_RANK() OVER (PARTITION BY department_id ORDER BY salary) as salary_percentile
FROM employees
ORDER BY department_id, salary DESC;
```

## Best Practices
- Use appropriate functions for your database system
- Consider performance impact of complex functions
- Use window functions for analytics instead of self-joins
- Handle NULL values appropriately
- Test functions with sample data
- Document complex function usage
- Use consistent formatting for readability
- Consider indexing for function-based queries
- Use parameterized queries for application code
