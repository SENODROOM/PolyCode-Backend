# SQL JOINs

## INNER JOIN

### Basic INNER JOIN
```sql
-- Join two tables
SELECT e.employee_id, e.first_name, e.last_name, d.department_name
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id;

-- Join with table aliases
SELECT e.first_name, e.last_name, d.department_name, l.city, l.state
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
INNER JOIN locations l ON d.location_id = l.location_id;
```

### INNER JOIN with WHERE
```sql
-- Join with additional conditions
SELECT e.first_name, e.last_name, d.department_name
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
WHERE d.location_id = 1700;

-- Join with multiple conditions
SELECT e.first_name, e.last_name, d.department_name
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
AND d.location_id = 1700;
```

## LEFT JOIN

### Basic LEFT JOIN
```sql
-- Left join (all employees, departments only if matched)
SELECT e.employee_id, e.first_name, d.department_name
FROM employees e
LEFT JOIN departments d ON e.department_id = d.department_id;

-- Left join to find employees without departments
SELECT e.employee_id, e.first_name, e.last_name
FROM employees e
LEFT JOIN departments d ON e.department_id = d.department_id
WHERE d.department_id IS NULL;
```

### LEFT JOIN with Aggregation
```sql
-- Count employees per department (including departments with no employees)
SELECT d.department_name, COUNT(e.employee_id) as employee_count
FROM departments d
LEFT JOIN employees e ON d.department_id = e.department_id
GROUP BY d.department_id, d.department_name
ORDER BY employee_count DESC;
```

## RIGHT JOIN

### Basic RIGHT JOIN
```sql
-- Right join (all departments, employees only if matched)
SELECT d.department_name, e.first_name, e.last_name
FROM employees e
RIGHT JOIN departments d ON e.department_id = d.department_id;

-- Right join to find departments without employees
SELECT d.department_id, d.department_name
FROM employees e
RIGHT JOIN departments d ON e.department_id = d.department_id
WHERE e.employee_id IS NULL;
```

## FULL OUTER JOIN

### Basic FULL OUTER JOIN
```sql
-- Full outer join (all rows from both tables)
SELECT e.employee_id, e.first_name, d.department_name
FROM employees e
FULL OUTER JOIN departments d ON e.department_id = d.department_id;

-- Find unmatched records from both tables
SELECT 
    COALESCE(e.employee_id, d.department_id) as id,
    e.first_name,
    d.department_name,
    CASE 
        WHEN e.employee_id IS NULL THEN 'Department without employee'
        WHEN d.department_id IS NULL THEN 'Employee without department'
        ELSE 'Matched'
    END as status
FROM employees e
FULL OUTER JOIN departments d ON e.department_id = d.department_id
WHERE e.employee_id IS NULL OR d.department_id IS NULL;
```

## CROSS JOIN

### Basic CROSS JOIN
```sql
-- Cross join (Cartesian product)
SELECT e.first_name, d.department_name
FROM employees e
CROSS JOIN departments d;

-- Cross join for combinations
SELECT e.first_name, j.job_title
FROM employees e
CROSS JOIN jobs j
WHERE e.department_id = 60;
```

## SELF JOIN

### Self Join Examples
```sql
-- Find employees and their managers
SELECT 
    e.first_name || ' ' || e.last_name as employee,
    m.first_name || ' ' || m.last_name as manager
FROM employees e
LEFT JOIN employees m ON e.manager_id = m.employee_id;

-- Find pairs of employees in same department
SELECT 
    e1.first_name || ' ' || e1.last_name as employee1,
    e2.first_name || ' ' || e2.last_name as employee2,
    d.department_name
FROM employees e1
JOIN employees e2 ON e1.department_id = e2.department_id
JOIN departments d ON e1.department_id = d.department_id
WHERE e1.employee_id < e2.employee_id;
```

## Multiple JOINs

### Complex Join Examples
```sql
-- Join multiple tables
SELECT 
    e.first_name,
    e.last_name,
    d.department_name,
    j.job_title,
    l.city,
    l.state,
    c.country_name
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
INNER JOIN jobs j ON e.job_id = j.job_id
INNER JOIN locations l ON d.location_id = l.location_id
INNER JOIN countries c ON l.country_id = c.country_id
WHERE c.country_name = 'United States';

-- Complex join with subqueries
SELECT 
    e.first_name,
    e.last_name,
    d.department_name,
    e.salary,
    avg_dept.avg_salary
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
INNER JOIN (
    SELECT 
        department_id, 
        AVG(salary) as avg_salary
    FROM employees 
    GROUP BY department_id
) avg_dept ON e.department_id = avg_dept.department_id
WHERE e.salary > avg_dept.avg_salary;
```

## JOIN with Aggregation

### Aggregated JOINs
```sql
-- Department salary statistics
SELECT 
    d.department_name,
    COUNT(e.employee_id) as employee_count,
    AVG(e.salary) as avg_salary,
    MIN(e.salary) as min_salary,
    MAX(e.salary) as max_salary
FROM departments d
LEFT JOIN employees e ON d.department_id = e.department_id
GROUP BY d.department_id, d.department_name
ORDER BY avg_salary DESC;

-- Employee count by location
SELECT 
    l.city,
    l.state,
    COUNT(DISTINCT d.department_id) as department_count,
    COUNT(e.employee_id) as employee_count
FROM locations l
LEFT JOIN departments d ON l.location_id = d.location_id
LEFT JOIN employees e ON d.department_id = e.department_id
GROUP BY l.location_id, l.city, l.state
ORDER BY employee_count DESC;
```

## Database-Specific JOINs

### MySQL JOINs
```sql
-- MySQL - NATURAL JOIN
SELECT * FROM employees NATURAL JOIN departments;

-- MySQL - STRAIGHT_JOIN (force join order)
SELECT e.first_name, d.department_name
FROM employees e
STRAIGHT_JOIN departments d ON e.department_id = d.department_id;
```

### PostgreSQL JOINs
```sql
-- PostgreSQL - LATERAL JOIN
SELECT e.first_name, j.job_title
FROM employees e
CROSS JOIN LATERAL (
    SELECT job_title 
    FROM jobs 
    WHERE min_salary <= e.salary 
    AND max_salary >= e.salary
    LIMIT 1
) j;

-- PostgreSQL - USING clause
SELECT employee_id, first_name, department_id, department_name
FROM employees
INNER JOIN departments USING (department_id);
```

### SQL Server JOINs
```sql
-- SQL Server - CROSS APPLY
SELECT e.first_name, j.job_title
FROM employees e
CROSS APPLY (
    SELECT TOP 1 job_title
    FROM jobs
    WHERE min_salary <= e.salary
    ORDER BY min_salary DESC
) j;

-- SQL Server - OUTER APPLY
SELECT e.first_name, j.job_title
FROM employees e
OUTER APPLY (
    SELECT TOP 1 job_title
    FROM jobs
    WHERE min_salary <= e.salary
    ORDER BY min_salary DESC
) j;
```

## Performance Considerations

### JOIN Optimization
```sql
-- Use appropriate indexes
CREATE INDEX idx_employees_department_id ON employees(department_id);
CREATE INDEX idx_departments_location_id ON departments(location_id);

-- Filter early to reduce join size
SELECT e.first_name, d.department_name
FROM departments d
INNER JOIN employees e ON d.department_id = e.department_id
WHERE d.location_id = 1700
AND e.salary > 50000;

-- Use EXISTS instead of IN for better performance
SELECT e.first_name, e.last_name
FROM employees e
WHERE EXISTS (
    SELECT 1 
    FROM departments d 
    WHERE d.department_id = e.department_id 
    AND d.location_id = 1700
);
```

## Examples

### Employee Reports
```sql
-- Employee directory
SELECT 
    e.employee_id,
    e.first_name || ' ' || e.last_name as full_name,
    e.email,
    d.department_name,
    j.job_title,
    l.city || ', ' || l.state as location
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
INNER JOIN jobs j ON e.job_id = j.job_id
INNER JOIN locations l ON d.location_id = l.location_id
ORDER BY d.department_name, e.last_name;

-- Department summary
SELECT 
    d.department_name,
    COUNT(e.employee_id) as total_employees,
    COUNT(CASE WHEN e.hire_date >= '2023-01-01' THEN 1 END) as new_hires,
    AVG(e.salary) as avg_salary,
    MAX(e.salary) as max_salary
FROM departments d
LEFT JOIN employees e ON d.department_id = e.department_id
GROUP BY d.department_id, d.department_name
ORDER BY total_employees DESC;

-- Employees without managers
SELECT 
    e.first_name || ' ' || e.last_name as employee,
    d.department_name,
    j.job_title
FROM employees e
INNER JOIN departments d ON e.department_id = d.department_id
INNER JOIN jobs j ON e.job_id = j.job_id
WHERE e.manager_id IS NULL;
```

## Best Practices
- Use appropriate join type for your needs
- Use table aliases for readability
- Join on indexed columns when possible
- Filter data before joining when possible
- Use EXISTS instead of IN for subquery joins
- Be aware of NULL values in outer joins
- Consider performance for large datasets
- Test join conditions with SELECT first
- Use COALESCE for handling NULL values
- Document complex join logic
