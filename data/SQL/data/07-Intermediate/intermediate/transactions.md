# SQL Transactions

## Transaction Basics

### ACID Properties
```sql
-- Atomicity: All or nothing
-- Consistency: Data remains valid
-- Isolation: Transactions don't interfere
-- Durability: Changes persist
```

### Basic Transaction
```sql
-- Start transaction
BEGIN TRANSACTION;

-- Perform operations
INSERT INTO employees (employee_id, first_name, last_name, email, hire_date, job_id, salary, department_id)
VALUES (101, 'John', 'Doe', 'john.doe@company.com', '2024-01-15', 'IT_PROG', 75000, 60);

UPDATE departments
SET employee_count = employee_count + 1
WHERE department_id = 60;

-- Commit or rollback
COMMIT;
-- or ROLLBACK;
```

## Transaction Control

### BEGIN/COMMIT/ROLLBACK
```sql
-- Explicit transaction control
BEGIN;

INSERT INTO orders (order_id, customer_id, order_date, total_amount)
VALUES (1001, 1, '2024-01-15', 1500.00);

INSERT INTO order_items (order_id, product_id, quantity, unit_price)
VALUES (1001, 101, 2, 750.00);

-- If everything is good
COMMIT;

-- If something goes wrong
-- ROLLBACK;
```

### Auto-commit Control
```sql
-- Disable auto-commit (varies by database)
-- MySQL
SET autocommit = 0;

-- PostgreSQL
BEGIN;

-- SQL Server
BEGIN TRANSACTION;

-- Oracle
SET AUTOCOMMIT OFF;
```

## Transaction Isolation Levels

### Read Uncommitted
```sql
-- Lowest isolation, allows dirty reads
SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

-- Can read uncommitted changes from other transactions
-- Risk: Reading data that might be rolled back
```

### Read Committed
```sql
-- Default isolation in many databases
SET TRANSACTION ISOLATION LEVEL READ COMMITTED;

-- Only reads committed changes
-- Prevents dirty reads
-- Allows non-repeatable reads and phantom reads
```

### Repeatable Read
```sql
-- Higher isolation level
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- Guarantees repeatable reads
-- Prevents dirty reads and non-repeatable reads
-- May still allow phantom reads
```

### Serializable
```sql
-- Highest isolation level
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;

-- Complete isolation
-- Prevents all concurrency issues
-- May cause performance impact
```

## Savepoints

### Basic Savepoints
```sql
-- Create savepoint
BEGIN;

INSERT INTO employees (employee_id, first_name, last_name, email)
VALUES (101, 'John', 'Doe', 'john.doe@company.com');

-- Create savepoint
SAVEPOINT sp1;

INSERT INTO employees (employee_id, first_name, last_name, email)
VALUES (102, 'Jane', 'Smith', 'jane.smith@company.com');

-- Rollback to savepoint
ROLLBACK TO sp1;

-- Continue with transaction
INSERT INTO employees (employee_id, first_name, last_name, email)
VALUES (103, 'Bob', 'Johnson', 'bob.johnson@company.com');

COMMIT;
```

### Multiple Savepoints
```sql
BEGIN;

-- First operation
INSERT INTO orders (order_id, customer_id, total_amount)
VALUES (1001, 1, 1500.00);

SAVEPOINT order_insert;

-- Second operation
INSERT INTO order_items (order_id, product_id, quantity, unit_price)
VALUES (1001, 101, 2, 750.00);

SAVEPOINT items_insert;

-- Third operation
UPDATE inventory
SET quantity = quantity - 2
WHERE product_id = 101;

-- If inventory update fails
-- ROLLBACK TO items_insert;

-- If everything succeeds
COMMIT;
```

## Nested Transactions

### Savepoint as Nested Transaction
```sql
BEGIN;

-- Main transaction
INSERT INTO customers (customer_id, name, email)
VALUES (1, 'Acme Corp', 'info@acme.com');

SAVEPOINT customer_insert;

-- Nested transaction
BEGIN;
INSERT INTO orders (order_id, customer_id, total_amount)
VALUES (1001, 1, 1500.00);

-- If order fails, rollback to savepoint
-- ROLLBACK TO customer_insert;

-- Continue main transaction
INSERT INTO customer_contacts (customer_id, contact_name, phone)
VALUES (1, 'John Doe', '555-1234');

COMMIT;
```

## Database-Specific Transactions

### MySQL Transactions
```sql
-- MySQL transaction syntax
START TRANSACTION;

INSERT INTO employees (employee_id, first_name, last_name, email)
VALUES (101, 'John', 'Doe', 'john.doe@company.com');

-- Check affected rows
SELECT ROW_COUNT();

-- Commit or rollback
COMMIT;
-- or ROLLBACK;

-- MySQL with savepoints
SAVEPOINT sp1;
-- operations
ROLLBACK TO sp1;
-- RELEASE SAVEPOINT sp1;
```

### PostgreSQL Transactions
```sql
-- PostgreSQL transaction syntax
BEGIN;

INSERT INTO employees (employee_id, first_name, last_name, email)
VALUES (101, 'John', 'Doe', 'john.doe@company.com');

-- Check transaction status
SELECT current_setting('transaction_isolation');

COMMIT;

-- PostgreSQL savepoints
SAVEPOINT sp1;
-- operations
ROLLBACK TO sp1;
-- RELEASE SAVEPOINT sp1;
```

### SQL Server Transactions
```sql
-- SQL Server transaction syntax
BEGIN TRANSACTION;

INSERT INTO employees (employee_id, first_name, last_name, email)
VALUES (101, 'John', 'Doe', 'john.doe@company.com');

-- Check transaction count
SELECT @@TRANCOUNT;

COMMIT TRANSACTION;
-- or ROLLBACK TRANSACTION;

-- SQL Server savepoints
SAVE TRANSACTION sp1;
-- operations
ROLLBACK TRANSACTION sp1;
```

### Oracle Transactions
```sql
-- Oracle transaction syntax
INSERT INTO employees (employee_id, first_name, last_name, email)
VALUES (101, 'John', 'Doe', 'john.doe@company.com');

-- Implicit transaction ends with COMMIT or ROLLBACK
COMMIT;
-- or ROLLBACK;

-- Oracle savepoints
SAVEPOINT sp1;
-- operations
ROLLBACK TO sp1;
```

## Transaction Examples

### Bank Transfer
```sql
-- Bank transfer transaction
BEGIN;

-- Check sender balance
SELECT balance INTO @sender_balance
FROM accounts
WHERE account_id = 1;

-- Verify sufficient funds
IF @sender_balance >= 1000.00 THEN
    -- Debit sender
    UPDATE accounts
    SET balance = balance - 1000.00
    WHERE account_id = 1;
    
    -- Credit receiver
    UPDATE accounts
    SET balance = balance + 1000.00
    WHERE account_id = 2;
    
    -- Record transaction
    INSERT INTO transactions (transaction_id, from_account, to_account, amount, transaction_date)
    VALUES (1001, 1, 2, 1000.00, CURRENT_TIMESTAMP);
    
    COMMIT;
    SELECT 'Transfer completed successfully';
ELSE
    ROLLBACK;
    SELECT 'Insufficient funds';
END IF;
```

### Inventory Management
```sql
-- Inventory update with validation
BEGIN;

-- Check product availability
SELECT quantity INTO @available_quantity
FROM inventory
WHERE product_id = 101;

-- Reserve inventory
IF @available_quantity >= 10 THEN
    -- Update inventory
    UPDATE inventory
    SET quantity = quantity - 10,
        reserved_quantity = reserved_quantity + 10
    WHERE product_id = 101;
    
    -- Create reservation
    INSERT INTO reservations (reservation_id, product_id, quantity, status, created_date)
    VALUES (1001, 101, 10, 'PENDING', CURRENT_TIMESTAMP);
    
    COMMIT;
    SELECT 'Inventory reserved successfully';
ELSE
    ROLLBACK;
    SELECT 'Insufficient inventory';
END IF;
```

### Order Processing
```sql
-- Complex order processing
BEGIN;

-- Create order
INSERT INTO orders (order_id, customer_id, order_date, status, total_amount)
VALUES (1001, 1, CURRENT_TIMESTAMP, 'PENDING', 0.00);

-- Savepoint after order creation
SAVEPOINT order_created;

-- Add order items
INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price)
VALUES (1001, 101, 2, 500.00, 1000.00);

-- Check inventory
SELECT quantity INTO @inventory_quantity
FROM inventory
WHERE product_id = 101;

IF @inventory_quantity >= 2 THEN
    -- Update inventory
    UPDATE inventory
    SET quantity = quantity - 2
    WHERE product_id = 101;
    
    -- Update order total
    UPDATE orders
    SET total_amount = total_amount + 1000.00
    WHERE order_id = 1001;
    
    COMMIT;
    SELECT 'Order processed successfully';
ELSE
    -- Rollback to order creation
    ROLLBACK TO order_created;
    
    -- Update order status to failed
    UPDATE orders
    SET status = 'FAILED'
    WHERE order_id = 1001;
    
    COMMIT;
    SELECT 'Order failed - insufficient inventory';
END IF;
```

## Error Handling

### Transaction with Error Handling
```sql
-- Transaction with exception handling (SQL Server)
BEGIN TRY
    BEGIN TRANSACTION;
    
    INSERT INTO employees (employee_id, first_name, last_name, email)
    VALUES (101, 'John', 'Doe', 'john.doe@company.com');
    
    -- More operations...
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    IF @@TRANCOUNT > 0
        ROLLBACK TRANSACTION;
    
    -- Log error
    INSERT INTO error_log (error_message, error_date)
    VALUES (ERROR_MESSAGE(), CURRENT_TIMESTAMP);
    
    SELECT 'Transaction failed: ' + ERROR_MESSAGE();
END CATCH;
```

### PostgreSQL Error Handling
```sql
-- PostgreSQL with exception handling
DO $$
BEGIN
    BEGIN;
    
    INSERT INTO employees (employee_id, first_name, last_name, email)
    VALUES (101, 'John', 'Doe', 'john.doe@company.com');
    
    -- More operations...
    
    COMMIT;
EXCEPTION WHEN OTHERS THEN
    ROLLBACK;
    
    -- Log error
    INSERT INTO error_log (error_message, error_date)
    VALUES (SQLERRM(), CURRENT_TIMESTAMP);
    
    RAISE NOTICE 'Transaction failed: %', SQLERRM();
END $$;
```

## Performance Considerations

### Transaction Optimization
```sql
-- Keep transactions short
BEGIN;
-- Only necessary operations
UPDATE employees SET salary = salary * 1.05 WHERE department_id = 50;
COMMIT;

-- Avoid long-running transactions
BEGIN;
-- Bad: Multiple operations that take time
-- Good: Minimal operations, quick commit
```

### Lock Management
```sql
-- Check for locks
SELECT * FROM information_schema.innodb_locks;

-- Set lock timeout (MySQL)
SET innodb_lock_wait_timeout = 50;

-- Deadlock detection
SHOW ENGINE INNODB STATUS;
```

## Best Practices
- Keep transactions as short as possible
- Use appropriate isolation levels
- Handle errors properly with rollback
- Use savepoints for complex operations
- Test transactions thoroughly
- Monitor transaction performance
- Avoid user interaction within transactions
- Use connection pooling for better performance
- Log transaction errors for debugging
- Consider transaction retry logic for concurrency issues
