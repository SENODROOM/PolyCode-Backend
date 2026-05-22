#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

// Log levels
typedef enum {
    LOG_DEBUG,
    LOG_INFO,
    LOG_WARNING,
    LOG_ERROR,
    LOG_CRITICAL
} LogLevel;

// Log level names
const char* log_level_names[] = {
    "DEBUG",
    "INFO",
    "WARNING",
    "ERROR",
    "CRITICAL"
};

// Global log configuration
static LogLevel current_log_level = LOG_INFO;
static FILE* log_file = NULL;
static int log_to_console = 1;

// Initialize logging system
void initLogging(const char* filename, LogLevel level, int console_output) {
    current_log_level = level;
    log_to_console = console_output;
    
    if (filename != NULL) {
        log_file = fopen(filename, "a");
        if (log_file == NULL) {
            fprintf(stderr, "Warning: Could not open log file %s\n", filename);
        }
    }
}

// Close logging system
void closeLogging() {
    if (log_file != NULL) {
        fclose(log_file);
        log_file = NULL;
    }
}

// Get current timestamp
void getCurrentTimestamp(char* buffer, size_t buffer_size) {
    time_t rawtime;
    struct tm* timeinfo;
    
    time(&rawtime);
    timeinfo = localtime(&rawtime);
    
    strftime(buffer, buffer_size, "%Y-%m-%d %H:%M:%S", timeinfo);
}

// Core logging function
void logMessage(LogLevel level, const char* file, int line, const char* format, ...) {
    if (level < current_log_level) {
        return; // Skip messages below current log level
    }
    
    char timestamp[32];
    getCurrentTimestamp(timestamp, sizeof(timestamp));
    
    // Format the message
    va_list args;
    va_start(args, format);
    
    char message[1024];
    vsnprintf(message, sizeof(message), format, args);
    
    va_end(args);
    
    // Create log entry
    char log_entry[1200];
    snprintf(log_entry, sizeof(log_entry), 
             "[%s] %s %s:%d - %s\n",
             timestamp, log_level_names[level], file, line, message);
    
    // Output to console if enabled
    if (log_to_console) {
        printf("%s", log_entry);
        fflush(stdout);
    }
    
    // Output to file if available
    if (log_file != NULL) {
        fprintf(log_file, "%s", log_entry);
        fflush(log_file);
    }
}

// Convenience macros for logging
#define LOG_DEBUG_MSG(format, ...) logMessage(LOG_DEBUG, __FILE__, __LINE__, format, ##__VA_ARGS__)
#define LOG_INFO_MSG(format, ...) logMessage(LOG_INFO, __FILE__, __LINE__, format, ##__VA_ARGS__)
#define LOG_WARNING_MSG(format, ...) logMessage(LOG_WARNING, __FILE__, __LINE__, format, ##__VA_ARGS__)
#define LOG_ERROR_MSG(format, ...) logMessage(LOG_ERROR, __FILE__, __LINE__, format, ##__VA_ARGS__)
#define LOG_CRITICAL_MSG(format, ...) logMessage(LOG_CRITICAL, __FILE__, __LINE__, format, ##__VA_ARGS__)

// Function to demonstrate logging
void processUserData(int user_id, const char* action) {
    LOG_INFO_MSG("Processing user %d, action: %s", user_id, action);
    
    if (user_id <= 0) {
        LOG_ERROR_MSG("Invalid user ID: %d", user_id);
        return;
    }
    
    if (strcmp(action, "login") == 0) {
        LOG_INFO_MSG("User %d logged in successfully", user_id);
    } else if (strcmp(action, "logout") == 0) {
        LOG_INFO_MSG("User %d logged out", user_id);
    } else if (strcmp(action, "delete") == 0) {
        LOG_WARNING_MSG("User %d requested account deletion", user_id);
    } else {
        LOG_WARNING_MSG("Unknown action '%s' for user %d", action, user_id);
    }
    
    LOG_DEBUG_MSG("Finished processing user %d", user_id);
}

// Function to simulate file operations with logging
void simulateFileOperation(const char* filename, const char* operation) {
    LOG_INFO_MSG("Starting %s operation on file: %s", operation, filename);
    
    if (filename == NULL || strlen(filename) == 0) {
        LOG_ERROR_MSG("Filename is NULL or empty");
        return;
    }
    
    if (strcmp(operation, "read") == 0) {
        LOG_DEBUG_MSG("Reading file: %s", filename);
        // Simulate successful read
        LOG_INFO_MSG("File %s read successfully", filename);
    } else if (strcmp(operation, "write") == 0) {
        LOG_DEBUG_MSG("Writing to file: %s", filename);
        // Simulate successful write
        LOG_INFO_MSG("File %s written successfully", filename);
    } else if (strcmp(operation, "delete") == 0) {
        LOG_WARNING_MSG("Deleting file: %s", filename);
        // Simulate deletion
        LOG_INFO_MSG("File %s deleted successfully", filename);
    } else {
        LOG_ERROR_MSG("Unknown file operation: %s", operation);
    }
}

// Function to demonstrate error logging
void divideNumbers(int a, int b) {
    LOG_DEBUG_MSG("Attempting to divide %d by %d", a, b);
    
    if (b == 0) {
        LOG_CRITICAL_MSG("Division by zero attempted: %d / %d", a, b);
        return;
    }
    
    double result = (double)a / b;
    LOG_INFO_MSG("Division result: %d / %d = %.2f", a, b, result);
}

int main() {
    printf("=== Logging System Demo ===\n\n");
    
    // Initialize logging
    initLogging("application.log", LOG_DEBUG, 1);
    
    LOG_INFO_MSG("Application started");
    LOG_DEBUG_MSG("Debug mode is enabled");
    
    // Test different log levels
    LOG_DEBUG_MSG("This is a debug message");
    LOG_INFO_MSG("This is an info message");
    LOG_WARNING_MSG("This is a warning message");
    LOG_ERROR_MSG("This is an error message");
    LOG_CRITICAL_MSG("This is a critical message");
    
    // Test user data processing
    printf("\n--- User Processing Examples ---\n");
    processUserData(123, "login");
    processUserData(456, "delete");
    processUserData(-1, "login"); // This will generate an error
    processUserData(789, "unknown_action"); // This will generate a warning
    
    // Test file operations
    printf("\n--- File Operation Examples ---\n");
    simulateFileOperation("data.txt", "read");
    simulateFileOperation("output.txt", "write");
    simulateFileOperation("temp.txt", "delete");
    simulateFileOperation("", "read"); // This will generate an error
    simulateFileOperation("test.txt", "unknown"); // This will generate an error
    
    // Test mathematical operations
    printf("\n--- Mathematical Operation Examples ---\n");
    divideNumbers(10, 2);
    divideNumbers(15, 3);
    divideNumbers(20, 0); // This will generate a critical error
    
    // Change log level to WARNING
    printf("\n--- Changing log level to WARNING ---\n");
    current_log_level = LOG_WARNING;
    LOG_DEBUG_MSG("This debug message should not appear");
    LOG_INFO_MSG("This info message should not appear");
    LOG_WARNING_MSG("This warning message should appear");
    LOG_ERROR_MSG("This error message should appear");
    
    LOG_INFO_MSG("Application shutting down");
    
    // Close logging
    closeLogging();
    
    printf("\n=== Logging demo completed ===\n");
    printf("Check 'application.log' file for logged messages\n");
    
    return 0;
}
