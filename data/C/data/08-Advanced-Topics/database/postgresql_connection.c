/*
 * File: postgresql_connection.c
 * Description: PostgreSQL database connection and operations
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>

// Database configuration
#define DB_HOST "localhost"
#define DB_PORT "5432"
#define DB_NAME "testdb"
#define DB_USER "postgres"
#define DB_PASSWORD "password"

// Connection string
#define CONNECTION_STRING "host=" DB_HOST " port=" DB_PORT " dbname=" DB_NAME " user=" DB_USER " password=" DB_PASSWORD

// Error handling macro
#define CHECK_PGERROR(conn, message) \
    if (PQstatus(conn) != CONNECTION_OK) { \
        fprintf(stderr, "Error: %s\n", message); \
        fprintf(stderr, "PostgreSQL Error: %s\n", PQerrorMessage(conn)); \
        PQfinish(conn); \
        exit(EXIT_FAILURE); \
    }

// Connection structure
typedef struct {
    PGconn* connection;
    int connected;
} PostgreSQLConnection;

// Initialize database connection
PostgreSQLConnection* pg_init() {
    PostgreSQLConnection* pg = (PostgreSQLConnection*)malloc(sizeof(PostgreSQLConnection));
    pg->connection = NULL;
    pg->connected = 0;
    return pg;
}

// Connect to database
int pg_connect(PostgreSQLConnection* pg) {
    pg->connection = PQconnectdb(CONNECTION_STRING);
    
    if (PQstatus(pg->connection) != CONNECTION_OK) {
        fprintf(stderr, "Connection to database failed: %s\n", PQerrorMessage(pg->connection));
        PQfinish(pg->connection);
        pg->connection = NULL;
        return 0;
    }
    
    pg->connected = 1;
    printf("Connected to PostgreSQL database: %s\n", DB_NAME);
    return 1;
}

// Disconnect from database
void pg_disconnect(PostgreSQLConnection* pg) {
    if (pg->connected && pg->connection) {
        PQfinish(pg->connection);
        pg->connection = NULL;
        pg->connected = 0;
        printf("Disconnected from database\n");
    }
}

// Execute SQL query
PGresult* pg_execute_query(PostgreSQLConnection* pg, const char* query) {
    if (!pg->connected || !pg->connection) {
        fprintf(stderr, "Not connected to database\n");
        return NULL;
    }
    
    PGresult* result = PQexec(pg->connection, query);
    
    if (PQresultStatus(result) != PGRES_COMMAND_OK && PQresultStatus(result) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Query failed: %s\n", PQerrorMessage(pg->connection));
        PQclear(result);
        return NULL;
    }
    
    return result;
}

// Execute parameterized query
PGresult* pg_execute_param_query(PostgreSQLConnection* pg, const char* query, 
                                int nParams, const char* const* paramValues) {
    if (!pg->connected || !pg->connection) {
        fprintf(stderr, "Not connected to database\n");
        return NULL;
    }
    
    PGresult* result = PQexecParams(pg->connection, query, nParams, NULL, paramValues, NULL, NULL, 0);
    
    if (PQresultStatus(result) != PGRES_COMMAND_OK && PQresultStatus(result) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Parameterized query failed: %s\n", PQerrorMessage(pg->connection));
        PQclear(result);
        return NULL;
    }
    
    return result;
}

// Create users table
int create_users_table(PostgreSQLConnection* pg) {
    const char* query = "CREATE TABLE IF NOT EXISTS users ("
                       "id SERIAL PRIMARY KEY,"
                       "username VARCHAR(50) UNIQUE NOT NULL,"
                       "email VARCHAR(100) UNIQUE NOT NULL,"
                       "password VARCHAR(255) NOT NULL,"
                       "age INTEGER,"
                       "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
                       "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
                       ")";
    
    PGresult* result = pg_execute_query(pg, query);
    if (result) {
        printf("Users table created successfully\n");
        PQclear(result);
        return 0;
    }
    
    return -1;
}

// Insert user with parameters
int insert_user(PostgreSQLConnection* pg, const char* username, const char* email, 
                const char* password, int age) {
    const char* query = "INSERT INTO users (username, email, password, age) VALUES ($1, $2, $3, $4)";
    
    char age_str[10];
    snprintf(age_str, sizeof(age_str), "%d", age);
    
    const char* paramValues[4] = {username, email, password, age_str};
    
    PGresult* result = pg_execute_param_query(pg, query, 4, paramValues);
    if (result) {
        printf("User inserted successfully: %s\n", username);
        PQclear(result);
        return 0;
    }
    
    return -1;
}

// Update user
int update_user(PostgreSQLConnection* pg, int user_id, const char* username, 
                const char* email, int age) {
    const char* query = "UPDATE users SET username = $1, email = $2, age = $3 WHERE id = $4";
    
    char user_id_str[10];
    char age_str[10];
    snprintf(user_id_str, sizeof(user_id_str), "%d", user_id);
    snprintf(age_str, sizeof(age_str), "%d", age);
    
    const char* paramValues[4] = {username, email, age_str, user_id_str};
    
    PGresult* result = pg_execute_param_query(pg, query, 4, paramValues);
    if (result) {
        printf("User updated successfully: ID %d\n", user_id);
        PQclear(result);
        return 0;
    }
    
    return -1;
}

// Delete user
int delete_user(PostgreSQLConnection* pg, int user_id) {
    const char* query = "DELETE FROM users WHERE id = $1";
    
    char user_id_str[10];
    snprintf(user_id_str, sizeof(user_id_str), "%d", user_id);
    
    const char* paramValues[1] = {user_id_str};
    
    PGresult* result = pg_execute_param_query(pg, query, 1, paramValues);
    if (result) {
        printf("User deleted successfully: ID %d\n", user_id);
        PQclear(result);
        return 0;
    }
    
    return -1;
}

// Select all users
void select_all_users(PostgreSQLConnection* pg) {
    const char* query = "SELECT id, username, email, age, created_at FROM users ORDER BY id";
    
    PGresult* result = pg_execute_query(pg, query);
    if (!result) return;
    
    int rows = PQntuples(result);
    int cols = PQnfields(result);
    
    printf("\nAll Users (%d rows):\n", rows);
    printf("ID\tUsername\tEmail\t\t\tAge\tCreated At\n");
    printf("------------------------------------------------------------\n");
    
    for (int i = 0; i < rows; i++) {
        printf("%s\t%-10s\t%-20s\t%s\t%s\n",
               PQgetvalue(result, i, 0),  // id
               PQgetvalue(result, i, 1),  // username
               PQgetvalue(result, i, 2),  // email
               PQgetvalue(result, i, 3),  // age
               PQgetvalue(result, i, 4)); // created_at
    }
    
    printf("\n");
    PQclear(result);
}

// Select user by ID
void select_user_by_id(PostgreSQLConnection* pg, int user_id) {
    const char* query = "SELECT id, username, email, age, created_at FROM users WHERE id = $1";
    
    char user_id_str[10];
    snprintf(user_id_str, sizeof(user_id_str), "%d", user_id);
    
    const char* paramValues[1] = {user_id_str};
    
    PGresult* result = pg_execute_param_query(pg, query, 1, paramValues);
    if (!result) return;
    
    int rows = PQntuples(result);
    
    if (rows > 0) {
        printf("User Details:\n");
        printf("ID: %s\n", PQgetvalue(result, 0, 0));
        printf("Username: %s\n", PQgetvalue(result, 0, 1));
        printf("Email: %s\n", PQgetvalue(result, 0, 2));
        printf("Age: %s\n", PQgetvalue(result, 0, 3));
        printf("Created At: %s\n", PQgetvalue(result, 0, 4));
    } else {
        printf("User with ID %d not found\n", user_id);
    }
    
    PQclear(result);
}

// Transaction example
void transaction_example(PostgreSQLConnection* pg) {
    printf("\n=== Transaction Example ===\n");
    
    // Begin transaction
    PGresult* result = pg_execute_query(pg, "BEGIN");
    if (!result) return;
    PQclear(result);
    
    printf("Transaction started\n");
    
    // Insert multiple records
    if (insert_user(pg, "alice", "alice@example.com", "pass123", 25) == 0 &&
        insert_user(pg, "bob", "bob@example.com", "pass456", 30) == 0) {
        
        // Commit transaction
        result = pg_execute_query(pg, "COMMIT");
        if (result) {
            printf("Transaction committed successfully\n");
            PQclear(result);
        }
    } else {
        // Rollback transaction
        result = pg_execute_query(pg, "ROLLBACK");
        if (result) {
            printf("Transaction rolled back\n");
            PQclear(result);
        }
    }
    
    select_all_users(pg);
}

// Prepared statement example
void prepared_statement_example(PostgreSQLConnection* pg) {
    printf("\n=== Prepared Statement Example ===\n");
    
    // Create prepared statement
    const char* stmtName = "insert_user_stmt";
    const char* query = "INSERT INTO users (username, email, age) VALUES ($1, $2, $3)";
    
    PGresult* result = PQprepare(pg->connection, stmtName, query, 3, NULL);
    if (PQresultStatus(result) != PGRES_COMMAND_OK) {
        fprintf(stderr, "Prepare failed: %s\n", PQerrorMessage(pg->connection));
        PQclear(result);
        return;
    }
    PQclear(result);
    
    // Execute prepared statement multiple times
    const char* usernames[] = {"charlie", "diana", "eve"};
    const char* emails[] = {"charlie@example.com", "diana@example.com", "eve@example.com"};
    int ages[] = {28, 32, 24};
    
    for (int i = 0; i < 3; i++) {
        char age_str[10];
        snprintf(age_str, sizeof(age_str), "%d", ages[i]);
        
        const char* paramValues[3] = {usernames[i], emails[i], age_str};
        
        result = PQexecPrepared(pg->connection, stmtName, 3, paramValues, NULL, NULL, 0);
        
        if (PQresultStatus(result) == PGRES_COMMAND_OK) {
            printf("Inserted user: %s\n", usernames[i]);
        } else {
            fprintf(stderr, "Execute prepared failed: %s\n", PQerrorMessage(pg->connection));
        }
        
        PQclear(result);
    }
    
    // Deallocate prepared statement
    result = PQexec(pg->connection, "DEALLOCATE insert_user_stmt");
    PQclear(result);
    
    select_all_users(pg);
}

// Batch insert example
void batch_insert_example(PostgreSQLConnection* pg) {
    printf("\n=== Batch Insert Example ===\n");
    
    // Create temporary table for batch insert
    const char* createTempTable = "CREATE TEMPORARY TABLE temp_users ("
                                  "username VARCHAR(50),"
                                  "email VARCHAR(100),"
                                  "age INTEGER"
                                  ")";
    
    PGresult* result = pg_execute_query(pg, createTempTable);
    if (!result) return;
    PQclear(result);
    
    // Copy data to temporary table
    const char* copyData = "COPY temp_users (username, email, age) FROM stdin WITH DELIMITER ','";
    
    result = PQexec(pg->connection, copyData);
    if (PQresultStatus(result) != PGRES_COPY_IN) {
        fprintf(stderr, "COPY failed: %s\n", PQerrorMessage(pg->connection));
        PQclear(result);
        return;
    }
    PQclear(result);
    
    // Send data
    const char* userData[] = {
        "frank,frank@example.com,35\n",
        "grace,grace@example.com,29\n",
        "henry,henry@example.com,42\n",
        "\\.\n"  // End of data marker
    };
    
    for (int i = 0; i < 4; i++) {
        if (PQputCopyData(pg->connection, userData[i], strlen(userData[i])) < 0) {
            fprintf(stderr, "COPY data failed: %s\n", PQerrorMessage(pg->connection));
            return;
        }
    }
    
    // End copy
    if (PQputCopyEnd(pg->connection, NULL) < 0) {
        fprintf(stderr, "COPY end failed: %s\n", PQerrorMessage(pg->connection));
        return;
    }
    
    result = PQgetResult(pg->connection);
    if (PQresultStatus(result) != PGRES_COMMAND_OK) {
        fprintf(stderr, "COPY result failed: %s\n", PQerrorMessage(pg->connection));
        PQclear(result);
        return;
    }
    PQclear(result);
    
    // Insert into main table
    const char* insertFromTemp = "INSERT INTO users (username, email, age) SELECT username, email, age FROM temp_users";
    result = pg_execute_query(pg, insertFromTemp);
    if (result) {
        printf("Batch insert completed successfully\n");
        PQclear(result);
    }
    
    select_all_users(pg);
}

// Get database information
void get_database_info(PostgreSQLConnection* pg) {
    printf("\n=== Database Information ===\n");
    
    // Get server version
    PGresult* result = pg_execute_query(pg, "SELECT version()");
    if (result) {
        printf("PostgreSQL Version: %s\n", PQgetvalue(result, 0, 0));
        PQclear(result);
    }
    
    // Get current database
    result = pg_execute_query(pg, "SELECT current_database()");
    if (result) {
        printf("Current Database: %s\n", PQgetvalue(result, 0, 0));
        PQclear(result);
    }
    
    // Get current user
    result = pg_execute_query(pg, "SELECT current_user");
    if (result) {
        printf("Current User: %s\n", PQgetvalue(result, 0, 0));
        PQclear(result);
    }
    
    // Get table count
    result = pg_execute_query(pg, "SELECT count(*) FROM information_schema.tables WHERE table_schema = 'public'");
    if (result) {
        printf("Number of tables: %s\n", PQgetvalue(result, 0, 0));
        PQclear(result);
    }
}

// Test function
void test_postgresql_operations() {
    PostgreSQLConnection* pg = pg_init();
    
    if (!pg) {
        printf("Failed to initialize PostgreSQL connection\n");
        return;
    }
    
    // Connect to database
    if (!pg_connect(pg)) {
        free(pg);
        return;
    }
    
    // Create table
    printf("\n=== Creating Table ===\n");
    if (create_users_table(pg) == 0) {
        printf("Users table created successfully\n");
    } else {
        printf("Failed to create users table\n");
    }
    
    // Insert sample data
    printf("\n=== Inserting Sample Data ===\n");
    insert_user(pg, "john_doe", "john@example.com", "password123", 25);
    insert_user(pg, "jane_smith", "jane@example.com", "password456", 30);
    insert_user(pg, "bob_jones", "bob@example.com", "password789", 35);
    
    // Select all users
    select_all_users(pg);
    
    // Select specific user
    printf("\n=== Selecting User by ID ===\n");
    select_user_by_id(pg, 1);
    
    // Update user
    printf("\n=== Updating User ===\n");
    update_user(pg, 1, "john_updated", "john_updated@example.com", 26);
    select_user_by_id(pg, 1);
    
    // Transaction example
    transaction_example(pg);
    
    // Prepared statement example
    prepared_statement_example(pg);
    
    // Batch insert example
    batch_insert_example(pg);
    
    // Get database information
    get_database_info(pg);
    
    // Delete user
    printf("\n=== Deleting User ===\n");
    delete_user(pg, 1);
    select_all_users(pg);
    
    // Disconnect
    pg_disconnect(pg);
    free(pg);
}

int main() {
    printf("=== PostgreSQL Database Operations ===\n");
    printf("Note: Make sure PostgreSQL server is running and database '%s' exists\n", DB_NAME);
    printf("Update DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD constants as needed\n\n");
    
    test_postgresql_operations();
    
    printf("\n=== PostgreSQL operations completed ===\n");
    
    return 0;
}
