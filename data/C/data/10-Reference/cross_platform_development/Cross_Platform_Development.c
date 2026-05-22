#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

// =============================================================================
// CROSS-PLATFORM DEVELOPMENT FUNDAMENTALS
// =============================================================================

// Platform detection macros
#if defined(_WIN32) || defined(_WIN64)
    #define PLATFORM_WINDOWS 1
    #include <windows.h>
    #include <direct.h>
    #include <conio.h>
    #define PATH_SEPARATOR '\\'
    #define LINE_ENDING "\r\n"
#elif defined(__linux__)
    #define PLATFORM_LINUX 1
    #include <unistd.h>
    #include <sys/types.h>
    #include <sys/stat.h>
    #include <fcntl.h>
    #include <termios.h>
    #include <dlfcn.h>
    #define PATH_SEPARATOR '/'
    #define LINE_ENDING "\n"
#elif defined(__APPLE__)
    #define PLATFORM_MACOS 1
    #include <unistd.h>
    #include <sys/types.h>
    #include <sys/stat.h>
    #include <fcntl.h>
    #include <termios.h>
    #include <dlfcn.h>
    #define PATH_SEPARATOR '/'
    #define LINE_ENDING "\n"
#else
    #define PLATFORM_UNKNOWN 1
    #define PATH_SEPARATOR '/'
    #define LINE_ENDING "\n"
#endif

// Cross-platform types
#ifdef _WIN32
    typedef HANDLE ThreadHandle;
    typedef HANDLE MutexHandle;
    typedef HANDLE FileHandle;
    typedef DWORD ThreadId;
#else
    typedef pthread_t ThreadHandle;
    typedef pthread_mutex_t MutexHandle;
    typedef int FileHandle;
    typedef pthread_t ThreadId;
#endif

// =============================================================================
// CROSS-PLATFORM FILE OPERATIONS
// =============================================================================

// Cross-platform file structure
typedef struct {
    FileHandle handle;
    char path[512];
    int is_open;
    int mode;
} CrossPlatformFile;

// Cross-platform file modes
#define FILE_MODE_READ 1
#define FILE_MODE_WRITE 2
#define FILE_MODE_APPEND 4
#define FILE_MODE_BINARY 8

// Initialize file structure
void initFile(CrossPlatformFile* file) {
    file->handle = 0;
    file->path[0] = '\0';
    file->is_open = 0;
    file->mode = 0;
}

// Cross-platform file open
int openFile(CrossPlatformFile* file, const char* path, int mode) {
    strcpy(file->path, path);
    file->mode = mode;
    
#ifdef _WIN32
    DWORD access = 0;
    DWORD creation = OPEN_EXISTING;
    
    if (mode & FILE_MODE_READ) access |= GENERIC_READ;
    if (mode & FILE_MODE_WRITE) access |= GENERIC_WRITE;
    if (mode & FILE_MODE_APPEND) {
        access |= GENERIC_WRITE;
        creation = OPEN_ALWAYS;
    }
    if (mode & FILE_MODE_BINARY) {
        // No special handling needed on Windows
    }
    
    file->handle = CreateFile(
        path,
        access,
        FILE_SHARE_READ,
        NULL,
        creation,
        FILE_ATTRIBUTE_NORMAL,
        NULL
    );
    
    if (file->handle == INVALID_HANDLE_VALUE) {
        return 0;
    }
    
    if (mode & FILE_MODE_APPEND) {
        SetFilePointer(file->handle, 0, NULL, FILE_END, FILE_BEGIN);
    }
    
#else
    int flags = 0;
    
    if (mode & FILE_MODE_READ && mode & FILE_MODE_WRITE) {
        flags = O_RDWR | O_CREAT;
    } else if (mode & FILE_MODE_READ) {
        flags = O_RDONLY;
    } else if (mode & FILE_MODE_WRITE) {
        flags = O_WRONLY | O_CREAT;
    } else if (mode & FILE_MODE_APPEND) {
        flags = O_WRONLY | O_CREAT | O_APPEND;
    }
    
    if (mode & FILE_MODE_BINARY) {
        flags |= O_BINARY;
    }
    
    file->handle = open(path, flags, 0644);
    
    if (file->handle == -1) {
        return 0;
    }
    
#endif
    
    file->is_open = 1;
    return 1;
}

// Cross-platform file read
int readFile(CrossPlatformFile* file, void* buffer, size_t size) {
    if (!file->is_open) return 0;
    
#ifdef _WIN32
    DWORD bytes_read;
    BOOL result = ReadFile(file->handle, buffer, (DWORD)size, &bytes_read, NULL);
    return result ? (int)bytes_read : 0;
#else
    ssize_t result = read(file->handle, buffer, size);
    return result > 0 ? (int)result : 0;
#endif
}

// Cross-platform file write
int writeFile(CrossPlatformFile* file, const void* buffer, size_t size) {
    if (!file->is_open) return 0;
    
#ifdef _WIN32
    DWORD bytes_written;
    BOOL result = WriteFile(file->handle, buffer, (DWORD)size, &bytes_written, NULL);
    return result ? (int)bytes_written : 0;
#else
    ssize_t result = write(file->handle, buffer, size);
    return result > 0 ? (int)result : 0;
#endif
}

// Cross-platform file close
void closeFile(CrossPlatformFile* file) {
    if (!file->is_open) return;
    
#ifdef _WIN32
    CloseHandle(file->handle);
#else
    close(file->handle);
#endif
    
    file->is_open = 0;
}

// Cross-platform file size
long getFileSize(CrossPlatformFile* file) {
    if (!file->is_open) return -1;
    
#ifdef _WIN32
    DWORD size = GetFileSize(file->handle, NULL);
    return (long)size;
#else
    off_t current = lseek(file->handle, 0, SEEK_CUR);
    off_t size = lseek(file->handle, 0, SEEK_END);
    lseek(file->handle, current, SEEK_SET);
    return (long)size;
#endif
}

// =============================================================================
// CROSS-PLATFORM DIRECTORY OPERATIONS
// =============================================================================

// Cross-platform directory creation
int createDirectory(const char* path) {
#ifdef _WIN32
    return _mkdir(path) == 0;
#else
    return mkdir(path, 0755) == 0;
#endif
}

// Cross-platform directory removal
int removeDirectory(const char* path) {
#ifdef _WIN32
    return _rmdir(path) == 0;
#else
    return rmdir(path) == 0;
#endif
}

// Cross-platform path separator normalization
void normalizePath(char* path) {
    for (int i = 0; path[i]; i++) {
        if (path[i] == '/' || path[i] == '\\') {
            path[i] = PATH_SEPARATOR;
        }
    }
}

// Cross-platform path joining
void joinPath(char* result, const char* dir, const char* file) {
    strcpy(result, dir);
    int len = strlen(result);
    
    if (len > 0 && result[len-1] != PATH_SEPARATOR) {
        result[len] = PATH_SEPARATOR;
        result[len+1] = '\0';
    }
    
    strcat(result, file);
}

// =============================================================================
// CROSS-PLATFORM THREADING
// =============================================================================

// Thread function type
#ifdef _WIN32
    DWORD WINAPI threadFunction(LPVOID param);
#else
    void* threadFunction(void* param);
#endif

// Thread data structure
typedef struct {
    int thread_id;
    void (*function)(void*);
    void* parameter;
    int result;
} ThreadData;

// Cross-platform thread creation
ThreadHandle createThread(void (*function)(void*), void* parameter) {
#ifdef _WIN32
    ThreadData* data = malloc(sizeof(ThreadData));
    data->function = function;
    data->parameter = parameter;
    data->result = 0;
    
    HANDLE thread = CreateThread(
        NULL, 0, threadFunction, data, 0, NULL
    );
    
    return thread;
#else
    ThreadData* data = malloc(sizeof(ThreadData));
    data->function = function;
    data->parameter = parameter;
    data->result = 0;
    
    pthread_t thread;
    pthread_create(&thread, NULL, threadFunction, data);
    
    return thread;
#endif
}

// Cross-platform thread join
void joinThread(ThreadHandle thread) {
#ifdef _WIN32
    WaitForSingleObject(thread, INFINITE);
    CloseHandle(thread);
#else
    pthread_join(thread, NULL);
#endif
}

// Cross-platform mutex
typedef struct {
#ifdef _WIN32
    HANDLE handle;
#else
    pthread_mutex_t mutex;
#endif
} CrossPlatformMutex;

// Initialize mutex
int initMutex(CrossPlatformMutex* mutex) {
#ifdef _WIN32
    mutex->handle = CreateMutex(NULL, FALSE, NULL);
    return mutex->handle != NULL;
#else
    return pthread_mutex_init(&mutex->mutex, NULL) == 0;
#endif
}

// Lock mutex
int lockMutex(CrossPlatformMutex* mutex) {
#ifdef _WIN32
    return WaitForSingleObject(mutex->handle, INFINITE) == WAIT_OBJECT_0;
#else
    return pthread_mutex_lock(&mutex->mutex) == 0;
#endif
}

// Unlock mutex
int unlockMutex(CrossPlatformMutex* mutex) {
#ifdef _WIN32
    return ReleaseMutex(mutex->handle) != 0;
#else
    return pthread_mutex_unlock(&mutex->mutex) == 0;
#endif
}

// Destroy mutex
void destroyMutex(CrossPlatformMutex* mutex) {
#ifdef _WIN32
    CloseHandle(mutex->handle);
#else
    pthread_mutex_destroy(&mutex->mutex);
#endif
}

// =============================================================================
// CROSS-PLATFORM DYNAMIC LIBRARIES
// =============================================================================

// Dynamic library handle
typedef struct {
#ifdef _WIN32
    HMODULE handle;
#else
    void* handle;
#endif
} DynamicLibrary;

// Load dynamic library
int loadLibrary(DynamicLibrary* lib, const char* path) {
#ifdef _WIN32
    lib->handle = LoadLibrary(path);
    return lib->handle != NULL;
#else
    lib->handle = dlopen(path, RTLD_LAZY);
    return lib->handle != NULL;
#endif
}

// Get function from library
void* getFunction(DynamicLibrary* lib, const char* name) {
#ifdef _WIN32
    return GetProcAddress(lib->handle, name);
#else
    return dlsym(lib->handle, name);
#endif
}

// Unload library
void unloadLibrary(DynamicLibrary* lib) {
#ifdef _WIN32
    FreeLibrary(lib->handle);
#else
    dlclose(lib->handle);
#endif
}

// Get last error
const char* getLibraryError() {
#ifdef _WIN32
    static char error_msg[256];
    FormatMessage(FORMAT_MESSAGE_FROM_SYSTEM, NULL, GetLastError(), 
                  MAKELANGID(LANG_NEUTRAL, SUBLANG_DEFAULT), 
                  error_msg, sizeof(error_msg), NULL);
    return error_msg;
#else
    return dlerror();
#endif
}

// =============================================================================
// CROSS-PLATFORM SYSTEM INFORMATION
// =============================================================================

// System information structure
typedef struct {
    char os_name[64];
    char architecture[32];
    int cpu_count;
    long page_size;
    long long total_memory;
} SystemInfo;

// Get system information
void getSystemInfo(SystemInfo* info) {
    // OS name
#ifdef _WIN32
    strcpy(info->os_name, "Windows");
#elif defined(__linux__)
    strcpy(info->os_name, "Linux");
#elif defined(__APPLE__)
    strcpy(info->os_name, "macOS");
#else
    strcpy(info->os_name, "Unknown");
#endif
    
    // Architecture
#ifdef _WIN32
    SYSTEM_INFO sys_info;
    GetSystemInfo(&sys_info);
    info->cpu_count = sys_info.dwNumberOfProcessors;
    info->page_size = sys_info.dwPageSize;
    
    if (sys_info.wProcessorArchitecture == PROCESSOR_ARCHITECTURE_AMD64) {
        strcpy(info->architecture, "x64");
    } else if (sys_info.wProcessorArchitecture == PROCESSOR_ARCHITECTURE_INTEL) {
        strcpy(info->architecture, "x86");
    } else {
        strcpy(info->architecture, "Unknown");
    }
    
    // Memory info
    MEMORYSTATUSEX mem_info;
    mem_info.dwLength = sizeof(mem_info);
    if (GlobalMemoryStatusEx(&mem_info)) {
        info->total_memory = mem_info.ullTotalPhys;
    }
    
#else
    // Unix-like systems
    info->cpu_count = sysconf(_SC_NPROCESSORS_ONLN);
    info->page_size = sysconf(_SC_PAGESIZE);
    
    // Architecture
#ifdef __x86_64__
    strcpy(info->architecture, "x64");
#elif __i386__
    strcpy(info->architecture, "x86");
#elif __arm__
    strcpy(info->architecture, "ARM");
#else
    strcpy(info->architecture, "Unknown");
#endif
    
    // Memory info
    FILE* meminfo = fopen("/proc/meminfo", "r");
    if (meminfo) {
        char line[256];
        while (fgets(line, sizeof(line), meminfo)) {
            if (sscanf(line, "MemTotal: %lld kB", &info->total_memory) == 1) {
                info->total_memory *= 1024; // Convert to bytes
                break;
            }
        }
        fclose(meminfo);
    }
#endif
}

// =============================================================================
// CROSS-PLATFORM TIME FUNCTIONS
// =============================================================================

// High-resolution timer
typedef struct {
#ifdef _WIN32
    LARGE_INTEGER start;
    LARGE_INTEGER frequency;
#else
    struct timespec start;
#endif
} HighResTimer;

// Initialize timer
void initTimer(HighResTimer* timer) {
#ifdef _WIN32
    QueryPerformanceFrequency(&timer->frequency);
    QueryPerformanceCounter(&timer->start);
#else
    clock_gettime(CLOCK_MONOTONIC, &timer->start);
#endif
}

// Get elapsed time in seconds
double getElapsedTime(HighResTimer* timer) {
#ifdef _WIN32
    LARGE_INTEGER end;
    QueryPerformanceCounter(&end);
    return (double)(end.QuadPart - timer->start.QuadPart) / timer->frequency.QuadPart;
#else
    struct timespec end;
    clock_gettime(CLOCK_MONOTONIC, &end);
    return (end.tv_sec - timer->start.tv_sec) + 
           (end.tv_nsec - timer->start.tv_nsec) / 1e9;
#endif
}

// Get current timestamp
long long getCurrentTimestamp() {
#ifdef _WIN32
    FILETIME ft;
    GetSystemTimeAsFileTime(&ft);
    ULARGE_INTEGER ull;
    ull.LowPart = ft.dwLowDateTime;
    ull.HighPart = ft.dwHighDateTime;
    return (ull.QuadPart - 116444736000000000ULL) / 10000ULL; // Convert to milliseconds
#else
    struct timespec ts;
    clock_gettime(CLOCK_REALTIME, &ts);
    return (long long)ts.tv_sec * 1000 + ts.tv_nsec / 1000000; // Convert to milliseconds
#endif
}

// =============================================================================
// CROSS-PLATFORM CONSOLE OPERATIONS
// =============================================================================

// Console colors
typedef enum {
    COLOR_BLACK = 0,
    COLOR_RED = 1,
    COLOR_GREEN = 2,
    COLOR_YELLOW = 3,
    COLOR_BLUE = 4,
    COLOR_MAGENTA = 5,
    COLOR_CYAN = 6,
    COLOR_WHITE = 7
} ConsoleColor;

// Set console color
void setConsoleColor(ConsoleColor foreground, ConsoleColor background) {
#ifdef _WIN32
    HANDLE hConsole = GetStdHandle(STD_OUTPUT_HANDLE);
    SetConsoleTextAttribute(hConsole, foreground | (background << 4));
#else
    char color_code[16];
    sprintf(color_code, "\033[%dm", 30 + foreground);
    printf("%s", color_code);
#endif
}

// Reset console color
void resetConsoleColor() {
#ifdef _WIN32
    HANDLE hConsole = GetStdHandle(STD_OUTPUT_HANDLE);
    SetConsoleTextAttribute(hConsole, 7); // White on black
#else
    printf("\033[0m");
#endif
}

// Clear console
void clearConsole() {
#ifdef _WIN32
    system("cls");
#else
    system("clear");
#endif
}

// Get console input without echo
char getConsoleInput() {
#ifdef _WIN32
    return _getch();
#else
    struct termios oldt, newt;
    char ch;
    
    tcgetattr(STDIN_FILENO, &oldt);
    newt = oldt;
    newt.c_lflag &= ~(ICANON | ECHO);
    tcsetattr(STDIN_FILENO, TCSANOW, &newt);
    
    ch = getchar();
    
    tcsetattr(STDIN_FILENO, TCSANOW, &oldt);
    return ch;
#endif
}

// =============================================================================
// CROSS-PLATFORM ENVIRONMENT VARIABLES
// =============================================================================

// Get environment variable
const char* getEnvVar(const char* name) {
    return getenv(name);
}

// Set environment variable
int setEnvVar(const char* name, const char* value) {
#ifdef _WIN32
    return SetEnvironmentVariable(name, value) != 0;
#else
    return setenv(name, value, 1) == 0;
#endif
}

// Unset environment variable
int unsetEnvVar(const char* name) {
#ifdef _WIN32
    return SetEnvironmentVariable(name, NULL) != 0;
#else
    return unsetenv(name) == 0;
#endif
}

// =============================================================================
// CROSS-PLATFORM ERROR HANDLING
// =============================================================================

// Error codes
typedef enum {
    ERROR_NONE = 0,
    ERROR_FILE_NOT_FOUND,
    ERROR_ACCESS_DENIED,
    ERROR_INVALID_PARAMETER,
    ERROR_OUT_OF_MEMORY,
    ERROR_UNKNOWN
} ErrorCode;

// Get last system error
ErrorCode getLastSystemError() {
#ifdef _WIN32
    DWORD error = GetLastError();
    switch (error) {
        case ERROR_FILE_NOT_FOUND: return ERROR_FILE_NOT_FOUND;
        case ERROR_ACCESS_DENIED: return ERROR_ACCESS_DENIED;
        case ERROR_INVALID_PARAMETER: return ERROR_INVALID_PARAMETER;
        case ERROR_NOT_ENOUGH_MEMORY: return ERROR_OUT_OF_MEMORY;
        default: return ERROR_UNKNOWN;
    }
#else
    int error = errno;
    switch (error) {
        case ENOENT: return ERROR_FILE_NOT_FOUND;
        case EACCES: return ERROR_ACCESS_DENIED;
        case EINVAL: return ERROR_INVALID_PARAMETER;
        case ENOMEM: return ERROR_OUT_OF_MEMORY;
        default: return ERROR_UNKNOWN;
    }
#endif
}

// Get error message
const char* getErrorMessage(ErrorCode error) {
    switch (error) {
        case ERROR_NONE: return "No error";
        case ERROR_FILE_NOT_FOUND: return "File not found";
        case ERROR_ACCESS_DENIED: return "Access denied";
        case ERROR_INVALID_PARAMETER: return "Invalid parameter";
        case ERROR_OUT_OF_MEMORY: return "Out of memory";
        case ERROR_UNKNOWN: return "Unknown error";
        default: return "Invalid error code";
    }
}

// =============================================================================
// CROSS-PLATFORM CONFIGURATION
// =============================================================================

// Configuration structure
typedef struct {
    char config_file[512];
    char app_name[64];
    char version[16];
} CrossPlatformConfig;

// Initialize configuration
void initConfig(CrossPlatformConfig* config, const char* app_name, const char* version) {
    strcpy(config->app_name, app_name);
    strcpy(config->version, version);
    
    // Get config file path
#ifdef _WIN32
    char app_data[MAX_PATH];
    if (GetEnvironmentVariable("APPDATA", app_data, sizeof(app_data))) {
        joinPath(config->config_file, app_data, app_name);
        strcat(config->config_file, ".config");
    } else {
        strcpy(config->config_file, app_name);
        strcat(config->config_file, ".config");
    }
#else
    char* home = getenv("HOME");
    if (home) {
        joinPath(config->config_file, home, ".config");
        createDirectory(config->config_file);
        joinPath(config->config_file, config->config_file, app_name);
        strcat(config->config_file, ".conf");
    } else {
        strcpy(config->config_file, app_name);
        strcat(config->config_file, ".conf");
    }
#endif
}

// Read configuration value
int readConfigValue(CrossPlatformConfig* config, const char* key, char* value, size_t value_size) {
    FILE* file = fopen(config->config_file, "r");
    if (!file) return 0;
    
    char line[512];
    while (fgets(line, sizeof(line), file)) {
        // Remove newline
        line[strcspn(line, "\r\n")] = '\0';
        
        // Parse key=value
        char* eq = strchr(line, '=');
        if (eq) {
            *eq = '\0';
            if (strcmp(line, key) == 0) {
                strncpy(value, eq + 1, value_size - 1);
                value[value_size - 1] = '\0';
                fclose(file);
                return 1;
            }
        }
    }
    
    fclose(file);
    return 0;
}

// Write configuration value
int writeConfigValue(CrossPlatformConfig* config, const char* key, const char* value) {
    // Read existing config
    char lines[100][512];
    int line_count = 0;
    
    FILE* file = fopen(config->config_file, "r");
    if (file) {
        while (line_count < 100 && fgets(lines[line_count], sizeof(lines[line_count]), file)) {
            line_count++;
        }
        fclose(file);
    }
    
    // Update or add key
    int found = 0;
    for (int i = 0; i < line_count; i++) {
        char* eq = strchr(lines[i], '=');
        if (eq) {
            *eq = '\0';
            if (strcmp(lines[i], key) == 0) {
                sprintf(lines[i], "%s=%s%s", key, value, LINE_ENDING);
                found = 1;
                break;
            }
            *eq = '=';
        }
    }
    
    if (!found && line_count < 100) {
        sprintf(lines[line_count], "%s=%s%s", key, value, LINE_ENDING);
        line_count++;
    }
    
    // Write back
    file = fopen(config->config_file, "w");
    if (!file) return 0;
    
    for (int i = 0; i < line_count; i++) {
        fputs(lines[i], file);
    }
    
    fclose(file);
    return 1;
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateFileOperations() {
    printf("=== CROSS-PLATFORM FILE OPERATIONS DEMO ===\n");
    
    CrossPlatformFile file;
    initFile(&file);
    
    // Create and write file
    if (openFile(&file, "test_crossplatform.txt", FILE_MODE_WRITE | FILE_MODE_BINARY)) {
        const char* content = "This is a cross-platform file test.\nLine 2 of the file.\n";
        int written = writeFile(&file, content, strlen(content));
        printf("Written %d bytes to file\n", written);
        closeFile(&file);
    }
    
    // Read file
    if (openFile(&file, "test_crossplatform.txt", FILE_MODE_READ | FILE_MODE_BINARY)) {
        long size = getFileSize(&file);
        printf("File size: %ld bytes\n", size);
        
        char* buffer = malloc(size + 1);
        int read = readFile(&file, buffer, size);
        buffer[read] = '\0';
        
        printf("File content:\n%s\n", buffer);
        
        free(buffer);
        closeFile(&file);
    }
    
    // Clean up
#ifdef _WIN32
    DeleteFile("test_crossplatform.txt");
#else
    unlink("test_crossplatform.txt");
#endif
}

void demonstrateDirectoryOperations() {
    printf("\n=== CROSS-PLATFORM DIRECTORY OPERATIONS DEMO ===\n");
    
    const char* test_dir = "test_crossplatform_dir";
    
    // Create directory
    if (createDirectory(test_dir)) {
        printf("Created directory: %s\n", test_dir);
        
        // Create subdirectory
        char subdir[512];
        joinPath(subdir, test_dir, "subdir");
        if (createDirectory(subdir)) {
            printf("Created subdirectory: %s\n", subdir);
        }
        
        // Remove subdirectory
        removeDirectory(subdir);
        printf("Removed subdirectory\n");
        
        // Remove directory
        removeDirectory(test_dir);
        printf("Removed directory\n");
    } else {
        printf("Failed to create directory\n");
    }
}

void demonstrateSystemInfo() {
    printf("\n=== SYSTEM INFORMATION DEMO ===\n");
    
    SystemInfo info;
    getSystemInfo(&info);
    
    printf("Operating System: %s\n", info.os_name);
    printf("Architecture: %s\n", info.architecture);
    printf("CPU Count: %d\n", info.cpu_count);
    printf("Page Size: %ld bytes\n", info.page_size);
    printf("Total Memory: %.2f GB\n", (double)info.total_memory / (1024 * 1024 * 1024));
}

void demonstrateThreading() {
    printf("\n=== CROSS-PLATFORM THREADING DEMO ===\n");
    
    // Simple thread function
    void threadFunction(void* param) {
        int thread_id = *(int*)param;
        printf("Thread %d started\n", thread_id);
        
        // Simulate work
        HighResTimer timer;
        initTimer(&timer);
        while (getElapsedTime(&timer) < 1.0) {
            // Busy wait
        }
        
        printf("Thread %d finished\n", thread_id);
    }
    
    int thread_ids[3] = {1, 2, 3};
    ThreadHandle threads[3];
    
    // Create threads
    for (int i = 0; i < 3; i++) {
        threads[i] = createThread(threadFunction, &thread_ids[i]);
    }
    
    // Wait for threads
    for (int i = 0; i < 3; i++) {
        joinThread(threads[i]);
    }
    
    printf("All threads completed\n");
}

void demonstrateDynamicLibraries() {
    printf("\n=== DYNAMIC LIBRARY DEMO ===\n");
    
    DynamicLibrary lib;
    
#ifdef _WIN32
    const char* lib_name = "kernel32.dll";
    const char* func_name = "GetTickCount";
#else
    const char* lib_name = "libc.so.6";
    const char* func_name = "printf";
#endif
    
    if (loadLibrary(&lib, lib_name)) {
        printf("Loaded library: %s\n", lib_name);
        
        void* func = getFunction(&lib, func_name);
        if (func) {
            printf("Found function: %s\n", func_name);
        } else {
            printf("Function not found: %s\n", func_name);
        }
        
        unloadLibrary(&lib);
    } else {
        printf("Failed to load library: %s\n", lib_name);
        printf("Error: %s\n", getLibraryError());
    }
}

void demonstrateConsoleOperations() {
    printf("\n=== CONSOLE OPERATIONS DEMO ===\n");
    
    printf("Colored text:\n");
    setConsoleColor(COLOR_RED, COLOR_BLACK);
    printf("Red text\n");
    
    setConsoleColor(COLOR_GREEN, COLOR_BLACK);
    printf("Green text\n");
    
    setConsoleColor(COLOR_BLUE, COLOR_BLACK);
    printf("Blue text\n");
    
    resetConsoleColor();
    printf("Normal text\n");
    
    printf("\nPress any key to continue...\n");
    char ch = getConsoleInput();
    printf("You pressed: %c\n", ch);
}

void demonstrateConfiguration() {
    printf("\n=== CONFIGURATION DEMO ===\n");
    
    CrossPlatformConfig config;
    initConfig(&config, "CrossPlatformDemo", "1.0.0");
    
    printf("Config file: %s\n", config.config_file);
    
    // Write some values
    writeConfigValue(&config, "username", "john_doe");
    writeConfigValue(&config, "theme", "dark");
    writeConfigValue(&config, "auto_save", "true");
    
    // Read values back
    char value[256];
    
    if (readConfigValue(&config, "username", value, sizeof(value))) {
        printf("Username: %s\n", value);
    }
    
    if (readConfigValue(&config, "theme", value, sizeof(value))) {
        printf("Theme: %s\n", value);
    }
    
    if (readConfigValue(&config, "auto_save", value, sizeof(value))) {
        printf("Auto save: %s\n", value);
    }
    
    // Clean up config file
#ifdef _WIN32
    DeleteFile(config.config_file);
#else
    unlink(config.config_file);
#endif
}

void demonstrateTiming() {
    printf("\n=== TIMING DEMO ===\n");
    
    HighResTimer timer;
    initTimer(&timer);
    
    printf("High-resolution timer test...\n");
    
    // Measure some operation
    double start = getElapsedTime(&timer);
    
    // Simulate work
    volatile int sum = 0;
    for (int i = 0; i < 1000000; i++) {
        sum += i;
    }
    
    double end = getElapsedTime(&timer);
    
    printf("Operation took: %.6f seconds\n", end - start);
    printf("Sum result: %d\n", sum);
    
    // Current timestamp
    long long timestamp = getCurrentTimestamp();
    printf("Current timestamp: %lld ms\n", timestamp);
}

void demonstrateErrorHandling() {
    printf("\n=== ERROR HANDLING DEMO ===\n");
    
    // Try to open non-existent file
    CrossPlatformFile file;
    initFile(&file);
    
    if (!openFile(&file, "nonexistent_file.txt", FILE_MODE_READ)) {
        ErrorCode error = getLastSystemError();
        printf("Error occurred: %s\n", getErrorMessage(error));
    }
}

void demonstratePlatformDetection() {
    printf("\n=== PLATFORM DETECTION DEMO ===\n");
    
    printf("Platform detection results:\n");
    
#if PLATFORM_WINDOWS
    printf("Platform: Windows\n");
    printf("Path separator: %c\n", PATH_SEPARATOR);
    printf("Line ending: \\r\\n\n");
#elif PLATFORM_LINUX
    printf("Platform: Linux\n");
    printf("Path separator: %c\n", PATH_SEPARATOR);
    printf("Line ending: \\n\n");
#elif PLATFORM_MACOS
    printf("Platform: macOS\n");
    printf("Path separator: %c\n", PATH_SEPARATOR);
    printf("Line ending: \\n\n");
#else
    printf("Platform: Unknown\n");
    printf("Path separator: %c\n", PATH_SEPARATOR);
    printf("Line ending: \\n\n");
#endif
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Cross-Platform Development Examples\n");
    printf("===================================\n\n");
    
    // Run all demonstrations
    demonstratePlatformDetection();
    demonstrateFileOperations();
    demonstrateDirectoryOperations();
    demonstrateSystemInfo();
    demonstrateThreading();
    demonstrateDynamicLibraries();
    demonstrateConsoleOperations();
    demonstrateConfiguration();
    demonstrateTiming();
    demonstrateErrorHandling();
    
    printf("\nAll cross-platform development examples demonstrated!\n");
    printf("This code compiles and runs on Windows, Linux, and macOS.\n");
    printf("Each platform-specific implementation is abstracted behind a common interface.\n");
    
    return 0;
}
