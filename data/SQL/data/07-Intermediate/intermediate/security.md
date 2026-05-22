# SQL Security

## Authentication and Authorization

### User Management
```sql
-- Create user (MySQL)
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'secure_password';

-- Create user with specific host (PostgreSQL)
CREATE USER app_user WITH PASSWORD 'secure_password';

-- Create user (SQL Server)
CREATE LOGIN app_user WITH PASSWORD = 'secure_password';
CREATE USER app_user FOR LOGIN app_user;

-- Create user (Oracle)
CREATE USER app_user IDENTIFIED BY secure_password;
```

### Grant Permissions
```sql
-- Grant basic permissions (MySQL)
GRANT SELECT, INSERT, UPDATE, DELETE ON company_db.employees TO 'app_user'@'localhost';

-- Grant with options (PostgreSQL)
GRANT SELECT, INSERT, UPDATE, DELETE ON employees TO app_user;
GRANT USAGE ON SCHEMA company TO app_user;

-- Grant role membership (SQL Server)
ALTER ROLE db_datareader ADD MEMBER app_user;
ALTER ROLE db_datawriter ADD MEMBER app_user;

-- Grant system privileges (Oracle)
GRANT CREATE SESSION TO app_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON employees TO app_user;
```

### Revoke Permissions
```sql
-- Revoke permissions (MySQL)
REVOKE DELETE ON company_db.employees FROM 'app_user'@'localhost';

-- Revoke permissions (PostgreSQL)
REVOKE DELETE ON employees FROM app_user;

-- Revoke role membership (SQL Server)
ALTER ROLE db_datawriter DROP MEMBER app_user;

-- Revoke system privileges (Oracle)
REVOKE DELETE ON employees FROM app_user;
```

## Role-Based Security

### Create Roles
```sql
-- Create role (PostgreSQL)
CREATE ROLE read_only;
CREATE ROLE read_write;
CREATE ROLE admin;

-- Create role (SQL Server)
CREATE ROLE read_only;
CREATE ROLE read_write;
CREATE ROLE admin;

-- Create role (Oracle)
CREATE ROLE read_only;
CREATE ROLE read_write;
CREATE ROLE admin;
```

### Assign Permissions to Roles
```sql
-- Grant permissions to roles (PostgreSQL)
GRANT SELECT ON ALL TABLES IN SCHEMA public TO read_only;
GRANT SELECT, INSERT, UPDATE ON ALL TABLES IN SCHEMA public TO read_write;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO admin;

-- Grant permissions to roles (SQL Server)
ALTER ROLE read_only ADD MEMBER db_datareader;
ALTER ROLE read_write ADD MEMBER db_datareader, db_datawriter;
ALTER ROLE admin ADD MEMBER db_datareader, db_datawriter, db_ddladmin;
```

### Assign Users to Roles
```sql
-- Assign user to role (PostgreSQL)
GRANT read_only TO app_user;
GRANT read_write TO manager_user;
GRANT admin TO admin_user;

-- Assign user to role (SQL Server)
ALTER ROLE read_only ADD MEMBER app_user;
ALTER ROLE read_write ADD MEMBER manager_user;
ALTER ROLE admin ADD MEMBER admin_user;
```

## Data Encryption

### Column-Level Encryption
```sql
-- Transparent Data Encryption (SQL Server)
CREATE COLUMN ENCRYPTION KEY CEK_EmployeeData 
WITH ALGORITHM = 'AES_256'
ENCRYPTION BY SERVER CERTIFICATE Cert_Employees;

ALTER TABLE employees
ALTER COLUMN ssn ADD ENCRYPTED WITH (ENCRYPTION_TYPE = DETERMINISTIC, ALGORITHM = 'AES_256', COLUMN_ENCRYPTION_KEY = CEK_EmployeeData);

-- Application-level encryption (MySQL)
INSERT INTO employees (ssn)
VALUES(AES_ENCRYPT('123-45-6789', 'encryption_key'));

-- Decrypt data
SELECT AES_DECRYPT(ssn, 'encryption_key') FROM employees;
```

### Data Masking
```sql
-- Dynamic data masking (SQL Server)
ALTER TABLE employees
ALTER COLUMN email ADD MASKED WITH (FUNCTION = 'email()');

ALTER TABLE employees
ALTER COLUMN phone ADD MASKED WITH (FUNCTION = 'partial(1,"XXXXXXX",0)');

-- Virtual Private Database (Oracle)
BEGIN
    DBMS_RLS.ADD_POLICY(
        policy_name => 'employee_masking_policy',
        object_schema => 'hr',
        object_name => 'employees',
        function_name => 'mask_employee_data',
        statement_types => 'SELECT, UPDATE',
        update_check => TRUE,
        enable => TRUE
    );
END;
```

## SQL Injection Prevention

### Parameterized Queries
```sql
-- Use parameterized queries instead of string concatenation
-- Bad (vulnerable to SQL injection)
-- "SELECT * FROM employees WHERE name = '" + userName + "'"

-- Good (parameterized)
-- "SELECT * FROM employees WHERE name = ?"

-- Prepared statement example
PREPARE stmt FROM 'SELECT * FROM employees WHERE email = ?';
SET @email = 'john.doe@company.com';
EXECUTE stmt USING @email;
DEALLOCATE PREPARE stmt;
```

### Input Validation
```sql
-- Validate input in application before passing to SQL
-- Check for SQL injection patterns
-- Use whitelist validation for known good values

-- Example validation patterns
-- Only allow alphanumeric for usernames
-- Only allow valid email formats
-- Only allow known department IDs
-- Escape special characters
```

### Stored Procedures
```sql
-- Use stored procedures to encapsulate logic
CREATE PROCEDURE get_employee_by_email(IN p_email VARCHAR(100))
BEGIN
    SELECT employee_id, first_name, last_name
    FROM employees
    WHERE email = p_email;
END;

-- Call stored procedure
CALL get_employee_by_email('john.doe@company.com');
```

## Access Control

### Row-Level Security
```sql
-- Row-level security (PostgreSQL)
CREATE POLICY employee_isolation_policy ON employees
FOR ALL
USING (department_id = current_setting('app.current_department_id'));

-- Enable row-level security
ALTER TABLE employees ENABLE ROW LEVEL SECURITY;

-- Row-level security (SQL Server)
CREATE SECURITY POLICY employee_isolation_policy
ON employees
ADD FILTER PREDICATE (department_id = SESSION_CONTEXT(N'department_id'));

-- Apply security policy
ALTER SECURITY POLICY employee_isolation_policy
ON employees WITH (STATE = ON);
```

### Column-Level Security
```sql
-- Column-level permissions (PostgreSQL)
REVOKE SELECT (salary) ON employees FROM public;
GRANT SELECT (employee_id, first_name, last_name, email) ON employees TO read_only;

-- Column-level encryption (MySQL)
-- Encrypt sensitive columns at application level
-- Use views to restrict access

CREATE VIEW employee_public AS
SELECT employee_id, first_name, last_name, email, department_id
FROM employees;

GRANT SELECT ON employee_public TO read_only;
```

## Audit and Logging

### Audit Trail
```sql
-- Create audit table
CREATE TABLE employee_audit (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT,
    action VARCHAR(10),
    old_values JSON,
    new_values JSON,
    changed_by VARCHAR(50),
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create trigger for audit trail (MySQL)
DELIMITER //
CREATE TRIGGER employees_audit_insert
AFTER INSERT ON employees
FOR EACH ROW
BEGIN
    INSERT INTO employee_audit (employee_id, action, new_values, changed_by)
    VALUES (NEW.employee_id, 'INSERT', 
            JSON_OBJECT('first_name', NEW.first_name, 'last_name', NEW.last_name, 'email', NEW.email),
            CURRENT_USER());
END//
DELIMITER ;

-- Create trigger for updates
DELIMITER //
CREATE TRIGGER employees_audit_update
AFTER UPDATE ON employees
FOR EACH ROW
BEGIN
    INSERT INTO employee_audit (employee_id, action, old_values, new_values, changed_by)
    VALUES (NEW.employee_id, 'UPDATE', 
            JSON_OBJECT('first_name', OLD.first_name, 'last_name', OLD.last_name, 'email', OLD.email),
            JSON_OBJECT('first_name', NEW.first_name, 'last_name', NEW.last_name, 'email', NEW.email),
            CURRENT_USER());
END//
DELIMITER ;
```

### Database Logging
```sql
-- Enable database logging (PostgreSQL)
ALTER SYSTEM SET log_statement = 'all';
ALTER SYSTEM SET log_connections = on;
ALTER SYSTEM SET log_disconnections = on;

-- MySQL general log
SET GLOBAL general_log = 'ON';
SET GLOBAL general_log_file = '/var/log/mysql/general.log';

-- SQL Server audit
CREATE SERVER AUDIT SPECIFICATION audit_spec
FOR SERVER
ADD (SUCCESSFUL_LOGIN_GROUP)
WITH (ON_FAILURE = CONTINUE);

CREATE SERVER AUDIT audit_spec
TO FILE (FILEPATH = 'C:\Audit\');
```

## Connection Security

### SSL/TLS Configuration
```sql
-- Require SSL connections (MySQL)
GRANT ALL PRIVILEGES ON *.* TO 'app_user'@'%' REQUIRE SSL;

-- Configure SSL (PostgreSQL)
-- Set ssl = on in postgresql.conf
-- Require SSL for specific users
ALTER USER app_user SET sslmode = 'require';

-- Configure encryption (SQL Server)
-- Force encryption on client connections
-- Use certificates for server authentication
```

### Network Security
```sql
-- Restrict access by host (MySQL)
CREATE USER 'app_user'@'192.168.1.100' IDENTIFIED BY 'password';
CREATE USER 'app_user'@'10.0.0.0/255.255.255.0' IDENTIFIED BY 'password';

-- Firewall rules for database ports
-- Allow only application servers
-- Use VPN for remote access

-- Connection limits
-- MySQL: max_connections parameter
-- PostgreSQL: max_connections parameter
-- SQL Server: User connection limits
```

## Database-Specific Security

### MySQL Security
```sql
-- MySQL user management
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT SELECT, INSERT, UPDATE ON company_db.* TO 'app_user'@'localhost';

-- MySQL privileges
GRANT ALL PRIVILEGES ON company_db.* TO 'admin_user'@'localhost';
GRANT SELECT ON company_db.* TO 'read_user'@'%';

-- MySQL SSL
SHOW VARIABLES LIKE 'have_ssl';
GRANT ALL PRIVILEGES ON *.* TO 'secure_user'@'%' REQUIRE SSL;
```

### PostgreSQL Security
```sql
-- PostgreSQL user management
CREATE USER app_user WITH PASSWORD 'secure_password';
GRANT CONNECT ON DATABASE company_db TO app_user;
GRANT USAGE ON SCHEMA public TO app_user;
GRANT SELECT, INSERT, UPDATE ON ALL TABLES IN SCHEMA public TO app_user;

-- PostgreSQL roles
CREATE ROLE read_only;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO read_only;
GRANT read_only TO app_user;

-- PostgreSQL row-level security
CREATE POLICY employee_policy ON employees
FOR ALL TO app_user
USING (department_id = current_setting('app.current_department'));
```

### SQL Server Security
```sql
-- SQL Server logins and users
CREATE LOGIN app_login WITH PASSWORD = 'secure_password';
CREATE USER app_user FOR LOGIN app_login;

-- SQL Server roles
ALTER ROLE db_datareader ADD MEMBER app_user;
ALTER ROLE db_datawriter ADD MEMBER app_user;

-- SQL Server schemas
CREATE SCHEMA app_data;
GRANT SELECT, INSERT, UPDATE ON SCHEMA::app_data TO app_user;

-- SQL Server data masking
ALTER TABLE employees
ALTER COLUMN ssn ADD MASKED WITH (FUNCTION = 'partial(0,"XXX-XX-",4)');
```

### Oracle Security
```sql
-- Oracle users and privileges
CREATE USER app_user IDENTIFIED BY secure_password;
GRANT CREATE SESSION TO app_user;
GRANT SELECT, INSERT, UPDATE, DELETE ON employees TO app_user;

-- Oracle roles
CREATE ROLE read_only;
GRANT SELECT ON employees TO read_only;
GRANT read_only TO app_user;

-- Oracle VPD (Virtual Private Database)
BEGIN
    DBMS_RLS.ADD_POLICY(
        object_schema => 'HR',
        object_name => 'EMPLOYEES',
        policy_name => 'EMP_POLICY',
        function_schema => 'SECURITY',
        policy_function => 'EMP_SEC_FUNC',
        statement_types => 'SELECT, INSERT, UPDATE, DELETE',
        update_check => TRUE,
        enable => TRUE
    );
END;
```

## Security Best Practices

### Principle of Least Privilege
```sql
-- Grant minimum necessary permissions
GRANT SELECT ON employees TO read_only_user;
-- Not: GRANT ALL PRIVILEGES ON *.* TO read_only_user;

-- Use roles for permission management
CREATE ROLE hr_clerk;
GRANT SELECT, INSERT, UPDATE ON employees TO hr_clerk;
GRANT hr_clerk TO hr_user;

-- Revoke unnecessary permissions
REVOKE DELETE ON employees FROM hr_user;
```

### Regular Security Audits
```sql
-- Review user permissions
SELECT 
    user_name,
    host,
    authentication_string,
    Select_priv,
    Insert_priv,
    Update_priv,
    Delete_priv
FROM mysql.user;

-- Review role memberships
SELECT 
    role_name,
    member_name
FROM information_schema.applicable_roles;

-- Monitor failed login attempts
-- Check database logs regularly
-- Set up alerts for suspicious activity
```

### Password Security
```sql
-- Enforce strong passwords
-- Use password validation plugins
-- Set password policies
-- Regular password rotation

-- MySQL password policy
INSTALL PLUGIN validate_password SONAME 'validate_password.so';
SET GLOBAL validate_password.policy = 'MEDIUM';

-- PostgreSQL password check
-- Use check constraints or application validation
ALTER TABLE users ADD CONSTRAINT password_check 
CHECK (LENGTH(password) >= 8 
       AND password ~ '[A-Z]' 
       AND password ~ '[a-z]' 
       AND password ~ '[0-9]');
```

## Examples

### Secure Application Setup
```sql
-- Create application user with limited permissions
CREATE USER 'web_app'@'localhost' IDENTIFIED BY 'SecurePass123!';
GRANT SELECT, INSERT, UPDATE ON company_db.customers TO 'web_app'@'localhost';
GRANT SELECT ON company_db.products TO 'web_app'@'localhost';
GRANT SELECT ON company_db.orders TO 'web_app'@'localhost';

-- Create read-only user for reporting
CREATE USER 'report_user'@'%' IDENTIFIED BY 'ReportPass456!';
GRANT SELECT ON company_db.* TO 'report_user'@'%';

-- Create admin user for management
CREATE USER 'admin_user'@'localhost' IDENTIFIED BY 'AdminPass789!';
GRANT ALL PRIVILEGES ON company_db.* TO 'admin_user'@'localhost';
```

### Data Protection Setup
```sql
-- Encrypt sensitive columns
ALTER TABLE employees
ADD COLUMN email_encrypted VARBINARY(255);

-- Create view with masked data
CREATE VIEW employees_public AS
SELECT 
    employee_id,
    first_name,
    last_name,
    CASE 
        WHEN department_id = 10 THEN 'IT'
        WHEN department_id = 20 THEN 'HR'
        ELSE 'Other'
    END as department_group
FROM employees;

-- Grant access to view instead of table
GRANT SELECT ON employees_public TO read_only_user;
```

## Best Practices
- Use strong, unique passwords for all database accounts
- Implement principle of least privilege
- Use roles for permission management
- Enable database auditing and logging
- Use parameterized queries to prevent SQL injection
- Encrypt sensitive data at rest and in transit
- Regularly review and update security policies
- Monitor database access and activity
- Keep database software updated
- Use SSL/TLS for database connections
- Implement network security (firewalls, VPNs)
- Regular security audits and penetration testing
- Document security policies and procedures
- Train developers on secure coding practices
