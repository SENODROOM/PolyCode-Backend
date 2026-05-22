# SQL Subqueries

## Basic Subqueries

### Subquery in SELECT Clause
```sql
-- Single value subquery
SELECT 
    employee_id,
    first_name,
    salary,
    (SELECT AVG(salary) FROM employees) as avg_salary
FROM employees;

-- Multiple value subquery
SELECT 
    employee_id,
    first_name,
    salary,
    (SELECT COUNT(*) FROM employees WHERE department_id = e.department_id) as dept_count
FROM employees e;
```

### Subquery in WHERE Clause
```sql
-- Single row subquery
SELECT employee_id, first_name, salary
FROM employees
WHERE salary > (SELECT AVG(salary) FROM employees);

-- Multiple row subquery with IN
SELECT employee_id, first_name, salary
FROM employees
WHERE department_id IN (SELECT department_id FROM departments WHERE location_id = 1700);

-- Multiple row subquery with ANY
SELECT employee_id, first_name, salary
FROM employees
WHERE salary > ANY (SELECT salary FROM employees WHERE department_id = 50);

-- Multiple row subquery with ALL
SELECT employee_id, first_name, salary
FROM employees
WHERE salary > ALL (SELECT salary FROM employees WHERE department_id = 50);
```

### Subquery in FROM Clause
```sql
-- Derived table
SELECT dept_id, avg_salary
FROM (
    SELECT department_id as dept_id, AVG(salary) as avg_salary
    FROM employees
    GROUP BY department_id
) dept_avg
WHERE avg_salary > 60000;

-- Complex derived table
SELECT 
    department_id,
    employee_count,
    avg_salary,
    CASE 
        WHEN avg_salary > 70000 THEN 'High'
        WHEN avg_salary > 50000 THEN 'Medium'
        ELSE 'Low'
    END as salary_level
FROM (
    SELECT 
        department_id,
        COUNT(*) as employee_count,
        AVG(salary) as avg_salary
    FROM employees
    GROUP BY department_id
) dept_stats;
```

## Correlated Subqueries

### Correlated Subquery Examples
```sql
-- Correlated subquery in WHERE
SELECT employee_id, first_name, salary
FROM employees e
WHERE salary > (
    SELECT AVG(salary)
    FROM employees
    WHERE department_id = e.department_id
);

-- EXISTS with correlated subquery
SELECT employee_id, first_name, department_id
FROM employees e
WHERE EXISTS (
    SELECT 1
    FROM departments d
    WHERE d.department_id = e.department_id
    AND d.location_id = 1700
);

-- NOT EXISTS
SELECT employee_id, first_name
FROM employees e
WHERE NOT EXISTS (
    SELECT 1
    FROM job_history jh
    WHERE jh.employee_id = e.employee_id
);
```

### Correlated Subquery in SELECT
```sql
-- Running total with correlated subquery
SELECT 
    employee_id,
    first_name,
    salary,
    (
        SELECT SUM(salary)
        FROM employees e2
        WHERE e2.employee_id <= e.employee_id
    ) as running_total
FROM employees e
ORDER BY employee_id;

-- Department rank
SELECT 
    employee_id,
    first_name,
    department_id,
    salary,
    (
        SELECT COUNT(*) + 1
        FROM employees e2
        WHERE e2.department_id = e.department_id
        AND e2.salary > e.salary
    ) as dept_rank
FROM employees e;
```

## Nested Subqueries

### Multiple Level Subqueries
```sql
-- Three-level subquery
SELECT employee_id, first_name, salary
FROM employees
WHERE department_id IN (
    SELECT department_id
    FROM departments
    WHERE location_id IN (
        SELECT location_id
        FROM locations
        WHERE country_id = 'US'
    )
);

-- Complex nested subquery
SELECT department_id, avg_salary
FROM (
    SELECT department_id, AVG(salary) as avg_salary
    FROM employees
    WHERE employee_id NOT IN (
        SELECT employee_id
        FROM job_history
        WHERE end_date < '2020-01-01'
    )
    GROUP BY department_id
) dept_avg
WHERE avg_salary > (
    SELECT AVG(salary)
    FROM employees
    WHERE hire_date > '2020-01-01'
);
```

## Subquery Types

### Scalar Subqueries
```sql
-- Single value subquery
SELECT 
    employee_id,
    first_name,
    salary,
    (SELECT MAX(salary) FROM employees) as max_salary,
    salary - (SELECT MAX(salary) FROM employees) as diff_from_max
FROM employees;

-- Scalar subquery in HAVING
SELECT department_id, AVG(salary)
FROM employees
GROUP BY department_id
HAVING AVG(salary) > (
    SELECT AVG(salary)
    FROM employees
);
```

### Multi-Row Subqueries
```sql
-- IN operator
SELECT employee_id, first_name, department_id
FROM employees
WHERE department_id IN (
    SELECT department_id
    FROM departments
    WHERE location_id = 1700
);

-- NOT IN operator
SELECT employee_id, first_name, department_id
FROM employees
WHERE department_id NOT IN (
    SELECT department_id
    FROM departments
    WHERE location_id = 1700
);
```

### Table Subqueries
```sql
-- Table subquery with JOIN
SELECT e.employee_id, e.first_name, d.avg_dept_salary
FROM employees e
JOIN (
    SELECT department_id, AVG(salary) as avg_dept_salary
    FROM employees
    GROUP BY department_id
) d ON e.department_id = d.department_id
WHERE e.salary > d.avg_dept_salary;

-- Table subquery with UNION
SELECT department_id, employee_count
FROM (
    SELECT department_id, COUNT(*) as employee_count
    FROM employees
    WHERE hire_date >= '2023-01-01'
    GROUP BY department_id
    
    UNION ALL
    
    SELECT department_id, COUNT(*) as employee_count
    FROM employees
    WHERE hire_date < '2023-01-01'
    GROUP BY department_id
) combined
GROUP BY department_id;
```

## Database-Specific Subqueries

### MySQL Subqueries
```sql
-- MySQL row constructor
SELECT employee_id, first_name, salary
FROM employees
WHERE (department_id, salary) IN (
    SELECT department_id, MAX(salary)
    FROM employees
    GROUP BY department_id
);

-- MySQL subquery with LIMIT
SELECT employee_id, first_name, salary
FROM employees
WHERE salary > (
    SELECT salary
    FROM employees
    ORDER BY salary DESC
    LIMIT 1 OFFSET 4
);
```

### PostgreSQL Subqueries
```sql
-- PostgreSQL ARRAY subquery
SELECT employee_id, first_name
FROM employees
WHERE department_id = ANY(
    SELECT department_id
    FROM departments
    WHERE location_id = 1700
);

-- PostgreSQL LATERAL subquery
SELECT e.employee_id, e.first_name, recent_jobs.job_title
FROM employees e
CROSS JOIN LATERAL (
    SELECT job_title
    FROM job_history jh
    WHERE jh.employee_id = e.employee_id
    ORDER BY end_date DESC
    LIMIT 1
) recent_jobs;
```

### SQL Server Subqueries
```sql
-- SQL Server CROSS APPLY
SELECT e.employee_id, e.first_name, recent_jobs.job_title
FROM employees e
CROSS APPLY (
    SELECT TOP 1 job_title
    FROM job_history jh
    WHERE jh.employee_id = e.employee_id
    ORDER BY end_date DESC
) recent_jobs;

-- SQL Server CTE with subquery
WITH DeptStats AS (
    SELECT 
        department_id,
        AVG(salary) as avg_salary,
        COUNT(*) as employee_count
    FROM employees
    GROUP BY department_id
)
SELECT e.employee_id, e.first_name, e.salary, ds.avg_salary
FROM employees e
JOIN DeptStats ds ON e.department_id = ds.department_id
WHERE e.salary > ds.avg_salary;
```

## Performance Considerations

### Subquery vs JOIN
```sql
-- Subquery approach
SELECT employee_id, first_name, department_name
FROM employees e
WHERE e.department_id IN (
    SELECT department_id
    FROM departments
    WHERE location_id = 1700
);

-- JOIN approach (usually more efficient)
SELECT e.employee_id, e.first_name, d.department_name
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
WHERE d.location_id = 1700;
```

### EXISTS vs IN
```sql
-- EXISTS (often more efficient for large datasets)
SELECT e.employee_id, e.first_name
FROM employees e
WHERE EXISTS (
    SELECT 1
    FROM departments d
    WHERE d.department_id = e.department_id
    AND d.location_id = 1700
);

-- IN (can be less efficient with large subquery results)
SELECT e.employee_id, e.first_name
FROM employees e
WHERE e.department_id IN (
    SELECT department_id
    FROM departments
    WHERE location_id = 1700
);
```

## Common Table Expressions (CTEs)

### Basic CTE
```sql
-- Simple CTE
WITH HighEarners AS (
    SELECT employee_id, first_name, salary, department_id
    FROM employees
    WHERE salary > 70000
)
SELECT e.first_name, d.department_name, e.salary
FROM HighEarners e
JOIN departments d ON e.department_id = d.department_id;
```

### Recursive CTE
```sql
-- Recursive CTE for hierarchy
WITH RECURSIVE EmployeeHierarchy AS (
    -- Base case: top-level managers
    SELECT employee_id, first_name, manager_id, 1 as level
    FROM employees
    WHERE manager_id IS NULL
    
    UNION ALL
    
    -- Recursive case: employees under managers
    SELECT e.employee_id, e.first_name, e.manager_id, eh.level + 1
    FROM employees e
    JOIN EmployeeHierarchy eh ON e.manager_id = eh.employee_id
    WHERE eh.level < 5
)
SELECT 
    employee_id,
    first_name,
    manager_id,
    level,
    REPEAT('  ', level - 1) || first_name as hierarchy
FROM EmployeeHierarchy
ORDER BY level, employee_id;
```

### Multiple CTEs
```sql
-- Multiple CTEs
WITH DeptStats AS (
    SELECT 
        department_id,
        COUNT(*) as employee_count,
        AVG(salary) as avg_salary
    FROM employees
    GROUP BY department_id
),
HighDepts AS (
    SELECT department_id
    FROM DeptStats
    WHERE avg_salary > 60000
)
SELECT e.employee_id, e.first_name, e.salary, ds.avg_salary
FROM employees e
JOIN DeptStats ds ON e.department_id = ds.department_id
WHERE e.department_id IN (SELECT department_id FROM HighDepts)
ORDER BY e.salary DESC;
```

## Examples

### Employee Analytics
```sql
-- Find employees earning more than their department average
SELECT 
    e.employee_id,
    e.first_name,
    e.salary,
    d.avg_dept_salary,
    (e.salary - d.avg_dept_salary) as diff_from_avg
FROM employees e
JOIN (
    SELECT 
        department_id,
        AVG(salary) as avg_dept_salary
    FROM employees
    GROUP BY department_id
) d ON e.department_id = d.department_id
WHERE e.salary > d.avg_dept_salary;

-- Find departments with above-average employee count
SELECT department_id, department_name, employee_count
FROM departments d
JOIN (
    SELECT 
        department_id,
        COUNT(*) as employee_count
    FROM employees
    GROUP BY department_id
) e ON d.department_id = e.department_id
WHERE e.employee_count > (
    SELECT AVG(employee_count)
    FROM (
        SELECT COUNT(*) as employee_count
        FROM employees
        GROUP BY department_id
    ) dept_counts
);
```

### Data Validation
```sql
-- Find data inconsistencies
SELECT 
    'Employee with invalid department' as issue,
    employee_id,
    first_name,
    department_id
FROM employees e
WHERE NOT EXISTS (
    SELECT 1
    FROM departments d
    WHERE d.department_id = e.department_id
)

UNION ALL

SELECT 
    'Department with no manager' as issue,
    department_id as id,
    department_name as name,
    NULL as department_id
FROM departments d
WHERE NOT EXISTS (
    SELECT 1
    FROM employees e
    WHERE e.employee_id = d.manager_id
);
```

## Best Practices
- Use JOIN instead of subquery when possible for better performance
- Use EXISTS instead of IN for large subquery results
- Consider CTEs for complex subqueries to improve readability
- Test subqueries with EXPLAIN to analyze performance
- Use appropriate indexing for subquery conditions
- Avoid correlated subqueries in large datasets when possible
- Use LIMIT in subqueries to reduce result sets
- Document complex subquery logic
- Consider materialized views for frequently used subqueries
- Test subquery results with sample data before deployment
