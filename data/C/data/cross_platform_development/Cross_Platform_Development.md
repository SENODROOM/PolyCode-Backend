# Cross-Platform Development

This file contains comprehensive cross-platform development examples in C, demonstrating how to write code that works across Windows, Linux, and macOS with platform-specific optimizations and abstractions.

## 📚 Cross-Platform Development Fundamentals

### 🌐 Platform Detection
- **Windows**: `_WIN32` and `_WIN64` macros
- **Linux**: `__linux__` macro
- **macOS**: `__APPLE__` macro
- **Unknown**: Fallback behavior

### 🎯 Cross-Platform Challenges
- **File System**: Different path separators and APIs
- **Threading**: Different threading models and APIs
- **Dynamic Libraries**: Different loading mechanisms
- **System Information**: Different ways to query system details
- **Console Operations**: Different terminal behaviors

## 🔧 Platform Detection

### Platform Detection Macros
```c
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
```

### Cross-Platform Types
```c
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
```

**Platform Detection Benefits**:
- **Conditional Compilation**: Different code for different platforms
- **API Abstraction**: Common interface for platform-specific APIs
- **Feature Detection**: Enable/disable features based on platform
- **Build Optimization**: Platform-specific optimizations

## 📁 Cross-Platform File Operations

### File Structure
```c
typedef struct {
    FileHandle handle;
    char path[512];
    int is_open;
    int mode;
} CrossPlatformFile;

// File modes
#define FILE_MODE_READ 1
#define FILE_MODE_WRITE 2
#define FILE_MODE_APPEND 4
#define FILE_MODE_BINARY 8
```

### Cross-Platform File Open
```c
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
    
    file->handle = CreateFile(
        path, access, FILE_SHARE_READ, NULL,
        creation, FILE_ATTRIBUTE_NORMAL, NULL
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
```

### Cross-Platform File Operations
```c
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
```

## 📂 Cross-Platform Directory Operations

### Directory Operations
```c
int createDirectory(const char* path) {
#ifdef _WIN32
    return _mkdir(path) == 0;
#else
    return mkdir(path, 0755) == 0;
#endif
}

int removeDirectory(const char* path) {
#ifdef _WIN32
    return _rmdir(path) == 0;
#else
    return rmdir(path) == 0;
#endif
}
```

### Path Handling
```c
void normalizePath(char* path) {
    for (int i = 0; path[i]; i++) {
        if (path[i] == '/' || path[i] == '\\') {
            path[i] = PATH_SEPARATOR;
        }
    }
}

void joinPath(char* result, const char* dir, const char* file) {
    strcpy(result, dir);
    int len = strlen(result);
    
    if (len > 0 && result[len-1] != PATH_SEPARATOR) {
        result[len] = PATH_SEPARATOR;
        result[len+1] = '\0';
    }
    
    strcat(result, file);
}
```

**File System Differences**:
- **Path Separators**: Windows `\`, Unix `/`
- **Case Sensitivity**: Windows case-insensitive, Unix case-sensitive
- **Permissions**: Windows ACLs, Unix permissions
- **File Limits**: Different file name length limits

## 🔄 Cross-Platform Threading

### Thread Data Structure
```c
typedef struct {
    int thread_id;
    void (*function)(void*);
    void* parameter;
    int result;
} ThreadData;
```

### Thread Creation
```c
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
```

### Thread Synchronization
```c
typedef struct {
#ifdef _WIN32
    HANDLE handle;
#else
    pthread_mutex_t mutex;
#endif
} CrossPlatformMutex;

int initMutex(CrossPlatformMutex* mutex) {
#ifdef _WIN32
    mutex->handle = CreateMutex(NULL, FALSE, NULL);
    return mutex->handle != NULL;
#else
    return pthread_mutex_init(&mutex->mutex, NULL) == 0;
#endif
}

int lockMutex(CrossPlatformMutex* mutex) {
#ifdef _WIN32
    return WaitForSingleObject(mutex->handle, INFINITE) == WAIT_OBJECT_0;
#else
    return pthread_mutex_lock(&mutex->mutex) == 0;
#endif
}
```

**Threading Differences**:
- **Thread APIs**: Windows CreateThread, Unix pthreads
- **Scheduling**: Different scheduling algorithms
- **Thread Limits**: Different maximum thread counts
- **Performance**: Different threading overheads

## 📚 Cross-Platform Dynamic Libraries

### Dynamic Library Structure
```c
typedef struct {
#ifdef _WIN32
    HMODULE handle;
#else
    void* handle;
#endif
} DynamicLibrary;
```

### Library Loading
```c
int loadLibrary(DynamicLibrary* lib, const char* path) {
#ifdef _WIN32
    lib->handle = LoadLibrary(path);
    return lib->handle != NULL;
#else
    lib->handle = dlopen(path, RTLD_LAZY);
    return lib->handle != NULL;
#endif
}

void* getFunction(DynamicLibrary* lib, const char* name) {
#ifdef _WIN32
    return GetProcAddress(lib->handle, name);
#else
    return dlsym(lib->handle, name);
#endif
}

void unloadLibrary(DynamicLibrary* lib) {
#ifdef _WIN32
    FreeLibrary(lib->handle);
#else
    dlclose(lib->handle);
#endif
}
```

### Error Handling
```c
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
```

**Dynamic Library Differences**:
- **File Extensions**: Windows `.dll`, Unix `.so`, macOS `.dylib`
- **Loading Functions**: Different API names and parameters
- **Symbol Resolution**: Different symbol handling
- **Error Reporting**: Different error mechanisms

## 💻 Cross-Platform System Information

### System Information Structure
```c
typedef struct {
    char os_name[64];
    char architecture[32];
    int cpu_count;
    long page_size;
    long long total_memory;
} SystemInfo;
```

### System Information Retrieval
```c
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
    
    // Memory info from /proc/meminfo
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
```

## ⏱️ Cross-Platform Time Functions

### High-Resolution Timer
```c
typedef struct {
#ifdef _WIN32
    LARGE_INTEGER start;
    LARGE_INTEGER frequency;
#else
    struct timespec start;
#endif
} HighResTimer;
```

### Timer Operations
```c
void initTimer(HighResTimer* timer) {
#ifdef _WIN32
    QueryPerformanceFrequency(&timer->frequency);
    QueryPerformanceCounter(&timer->start);
#else
    clock_gettime(CLOCK_MONOTONIC, &timer->start);
#endif
}

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
```

**Time Function Differences**:
- **Clock Sources**: Different high-resolution timers
- **Epoch**: Different epoch definitions
- **Precision**: Different timer resolutions
- **Time Zones**: Different timezone handling

## 🖥️ Cross-Platform Console Operations

### Console Colors
```c
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
```

### Console Color Operations
```c
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

void resetConsoleColor() {
#ifdef _WIN32
    HANDLE hConsole = GetStdHandle(STD_OUTPUT_HANDLE);
    SetConsoleTextAttribute(hConsole, 7); // White on black
#else
    printf("\033[0m");
#endif
}
```

### Console Input
```c
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

void clearConsole() {
#ifdef _WIN32
    system("cls");
#else
    system("clear");
#endif
}
```

**Console Differences**:
- **Color Codes**: Windows API vs ANSI escape sequences
- **Input Handling**: Different console input mechanisms
- **Terminal Emulation**: Different terminal capabilities
- **Buffering**: Different output buffering behavior

## 🌍 Cross-Platform Environment Variables

### Environment Variable Operations
```c
const char* getEnvVar(const char* name) {
    return getenv(name);
}

int setEnvVar(const char* name, const char* value) {
#ifdef _WIN32
    return SetEnvironmentVariable(name, value) != 0;
#else
    return setenv(name, value, 1) == 0;
#endif
}

int unsetEnvVar(const char* name) {
#ifdef _WIN32
    return SetEnvironmentVariable(name, NULL) != 0;
#else
    return unsetenv(name) == 0;
#endif
}
```

**Environment Variable Differences**:
- **Case Sensitivity**: Windows case-insensitive, Unix case-sensitive
- **Size Limits**: Different maximum variable sizes
- **Persistence**: Different persistence mechanisms
- **Special Variables**: Platform-specific variables

## ⚠️ Cross-Platform Error Handling

### Error Codes
```c
typedef enum {
    ERROR_NONE = 0,
    ERROR_FILE_NOT_FOUND,
    ERROR_ACCESS_DENIED,
    ERROR_INVALID_PARAMETER,
    ERROR_OUT_OF_MEMORY,
    ERROR_UNKNOWN
} ErrorCode;
```

### Error Handling
```c
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
```

**Error Handling Differences**:
- **Error Codes**: Different error number ranges
- **Error Messages**: Different message formats
- **Error Persistence**: Different error persistence behavior
- **Thread Safety**: Different thread-local error handling

## ⚙️ Cross-Platform Configuration

### Configuration Structure
```c
typedef struct {
    char config_file[512];
    char app_name[64];
    char version[16];
} CrossPlatformConfig;
```

### Configuration Management
```c
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
```

**Configuration Differences**:
- **File Locations**: Different standard config directories
- **File Formats**: Different preferred config formats
- **Permissions**: Different permission requirements
- **User vs System**: Different user/system config separation

## 🔧 Best Practices

### 1. Use Abstraction Layers
```c
// Good: Abstract platform differences
typedef struct {
    PlatformHandle handle;
    int is_valid;
} CrossPlatformResource;

// Bad: Direct platform API usage
HANDLE hFile = CreateFile(...); // Windows only
```

### 2. Feature Detection Over Platform Detection
```c
// Good: Check for feature availability
int hasHighResTimer() {
#ifdef _WIN32
    LARGE_INTEGER freq;
    return QueryPerformanceFrequency(&freq);
#else
    return clock_getres(CLOCK_MONOTONIC, NULL) == 0;
#endif
}

// Bad: Assume features based on platform
#ifdef _WIN32
    // Assume high-res timer available
#endif
```

### 3. Consistent Error Handling
```c
// Good: Consistent error codes across platforms
typedef enum {
    CP_SUCCESS = 0,
    CP_ERROR_FILE_NOT_FOUND,
    CP_ERROR_ACCESS_DENIED,
    CP_ERROR_INVALID_PARAMETER
} CrossPlatformError;

// Bad: Platform-specific error codes
#ifdef _WIN32
    DWORD error = GetLastError();
#else
    int error = errno;
#endif
```

### 4. Portable Data Types
```c
// Good: Use fixed-size types
#include <stdint.h>
uint32_t file_size;
int64_t timestamp;

// Bad: Use platform-specific types
DWORD file_size; // Windows only
time_t timestamp; // May vary in size
```

### 5. Build System Integration
```cmake
# CMake example for cross-platform builds
if(WIN32)
    set(PLATFORM_SOURCES windows_specific.c)
elseif(UNIX)
    set(PLATFORM_SOURCES unix_specific.c)
endif()

add_executable(myapp main.c ${PLATFORM_SOURCES})
```

## 🛠️ Build Systems

### CMake Configuration
```cmake
cmake_minimum_required(VERSION 3.10)
project(CrossPlatformApp)

# Platform-specific configurations
if(WIN32)
    add_definitions(-DPLATFORM_WINDOWS)
    set(PLATFORM_LIBS ws2_32)
elseif(UNIX AND NOT APPLE)
    add_definitions(-DPLATFORM_LINUX)
    set(PLATFORM_LIBS pthread dl)
elseif(APPLE)
    add_definitions(-DPLATFORM_MACOS)
    set(PLATFORM_LIBS pthread dl)
endif()

# Source files
set(SOURCES
    main.c
    file_operations.c
    threading.c
    system_info.c
)

add_executable(${PROJECT_NAME} ${SOURCES})
target_link_libraries(${PROJECT_NAME} ${PLATFORM_LIBS})
```

### Makefile Configuration
```makefile
# Makefile for cross-platform builds
UNAME_S := $(shell uname -s)

ifeq ($(UNAME_S),Linux)
    CFLAGS += -DPLATFORM_LINUX
    LIBS += -lpthread -ldl
endif

ifeq ($(UNAME_S),Darwin)
    CFLAGS += -DPLATFORM_MACOS
    LIBS += -lpthread -ldl
endif

ifeq ($(OS),Windows_NT)
    CFLAGS += -DPLATFORM_WINDOWS
    LIBS += -lws2_32
endif

all: myapp

myapp: main.c
	$(CC) $(CFLAGS) -o $@ $< $(LIBS)
```

## ⚠️ Common Pitfalls

### 1. Hardcoded Paths
```c
// Wrong: Hardcoded path separators
char* config_path = "config\\settings.ini";

// Right: Use path separator macro
char* config_path = "config" PATH_SEPARATOR "settings.ini";
```

### 2. Assuming Endianness
```c
// Wrong: Assume little-endian
uint32_t value = *(uint32_t*)data;

// Right: Handle endianness
uint32_t value = ntohl(*(uint32_t*)data);
```

### 3. Platform-Specific Includes
```c
// Wrong: Platform-specific includes in main code
#ifdef _WIN32
#include <windows.h>
#endif

// Right: Abstract in platform-specific files
#include "platform_abstraction.h"
```

### 4. Ignoring Line Endings
```c
// Wrong: Assume \n line endings
fprintf(file, "Line 1\nLine 2\n");

// Right: Use platform line ending
fprintf(file, "Line 1%sLine 2%s", LINE_ENDING, LINE_ENDING);
```

### 5. Thread-Local Storage
```c
// Wrong: Assume thread-local storage works the same
__declspec(thread) int tls_var; // Windows only

// Right: Use portable thread-local storage
_Thread_local int tls_var; // C11 standard
```

## 🔧 Real-World Applications

### 1. Cross-Platform File Manager
```c
void copyFileCrossPlatform(const char* src, const char* dst) {
    CrossPlatformFile src_file, dst_file;
    
    if (openFile(&src_file, src, FILE_MODE_READ | FILE_MODE_BINARY)) {
        if (openFile(&dst_file, dst, FILE_MODE_WRITE | FILE_MODE_BINARY)) {
            char buffer[4096];
            size_t bytes_read;
            
            while ((bytes_read = readFile(&src_file, buffer, sizeof(buffer))) > 0) {
                writeFile(&dst_file, buffer, bytes_read);
            }
            
            closeFile(&dst_file);
        }
        closeFile(&src_file);
    }
}
```

### 2. Cross-Platform Logger
```c
void logMessage(const char* level, const char* message) {
    time_t now = time(NULL);
    char timestamp[64];
    strftime(timestamp, sizeof(timestamp), "%Y-%m-%d %H:%M:%S", localtime(&now));
    
    printf("[%s] [%s] %s%s", timestamp, level, message, LINE_ENDING);
    
    // Also log to file
    CrossPlatformFile log_file;
    if (openFile(&log_file, "app.log", FILE_MODE_APPEND | FILE_MODE_BINARY)) {
        char log_entry[512];
        sprintf(log_entry, "[%s] [%s] %s%s", timestamp, level, message, LINE_ENDING);
        writeFile(&log_file, log_entry, strlen(log_entry));
        closeFile(&log_file);
    }
}
```

### 3. Cross-Platform Configuration Manager
```c
typedef struct {
    CrossPlatformConfig config;
    char settings[100][256];
    int setting_count;
} ConfigManager;

void loadConfig(ConfigManager* manager, const char* app_name) {
    initConfig(&manager->config, app_name, "1.0.0");
    
    // Load default settings
    manager->setting_count = 0;
    
    // Override with user settings
    char value[256];
    if (readConfigValue(&manager->config, "theme", value, sizeof(value))) {
        strcpy(manager->settings[manager->setting_count++], value);
    }
}
```

### 4. Cross-Platform Network Client
```c
typedef struct {
#ifdef _WIN32
    SOCKET socket;
#else
    int socket;
#endif
    int is_connected;
} CrossPlatformSocket;

int connectToServer(CrossPlatformSocket* sock, const char* host, int port) {
#ifdef _WIN32
    WSADATA wsa_data;
    WSAStartup(MAKEWORD(2, 2), &wsa_data);
    
    sock->socket = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
#else
    sock->socket = socket(AF_INET, SOCK_STREAM, 0);
#endif
    
    if (sock->socket == INVALID_SOCKET) {
        return 0;
    }
    
    // Connect logic would go here
    // This is simplified for demonstration
    
    sock->is_connected = 1;
    return 1;
}
```

## 📚 Cross-Platform Libraries

### Recommended Libraries
- **GLib**: Cross-platform utility library
- **Boost**: C++ libraries with C bindings
- **SDL**: Cross-platform multimedia library
- **OpenSSL**: Cryptographic library
- **SQLite**: Database engine
- **zlib**: Compression library

### Library Integration
```c
// Example: Using OpenSSL across platforms
#include <openssl/ssl.h>

void initOpenSSL() {
    SSL_library_init();
    OpenSSL_add_all_algorithms();
    SSL_load_error_strings();
}

void cleanupOpenSSL() {
    EVP_cleanup();
    ERR_free_strings();
}
```

## 🎓 Learning Path

### 1. Platform Differences
- Learn the differences between major platforms
- Understand API variations
- Study file system differences
- Master threading models

### 2. Abstraction Design
- Design clean abstractions
- Implement platform-specific layers
- Create consistent interfaces
- Handle edge cases

### 3. Build Systems
- Master CMake
- Learn Makefile techniques
- Understand conditional compilation
- Handle dependencies

### 4. Testing
- Test on multiple platforms
- Use continuous integration
- Handle platform-specific bugs
- Performance testing

### 5. Deployment
- Package for different platforms
- Handle installation differences
- Manage dependencies
- Version compatibility

## 📚 Further Reading

### Books
- "Cross-Platform Development in C" by David J. Wallace
- "Advanced Linux Programming" by Mark Mitchell
- "Windows System Programming" by Johnson M. Hart

### Topics
- POSIX standards
- Windows API programming
- macOS development
- Build system design
- Platform-specific optimizations

Cross-platform development in C requires careful abstraction and understanding of platform differences. Master these techniques to write code that works seamlessly across Windows, Linux, and macOS while taking advantage of each platform's unique capabilities!
