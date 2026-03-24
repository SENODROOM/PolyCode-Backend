# File System Operations

This file contains comprehensive file system operations examples in C, including file creation, reading, writing, directory management, file searching, comparison, and advanced operations like compression and encryption.

## 📚 File System Fundamentals

### 🗃️ File System Concepts
- **Files**: Containers for data storage with metadata
- **Directories**: Hierarchical organization of files
- **Paths**: Location references in the file system
- **Attributes**: File properties (read-only, hidden, system, etc.)
- **Permissions**: Access control for files and directories

### 🎯 File Operations
- **Create**: New file creation with content
- **Read**: Accessing file contents
- **Write**: Adding content to files
- **Append**: Adding content to end of files
- **Delete**: Removing files from system

## 📁 File Operations

### File Creation
```c
FileOperationResult createFile(const char* file_path, const char* content) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    HANDLE hFile = CreateFile(
        file_path,                    // File name
        GENERIC_WRITE,                // Desired access
        FILE_SHARE_READ,              // Share mode
        NULL,                         // Security attributes
        CREATE_ALWAYS,                // Creation disposition
        FILE_ATTRIBUTE_NORMAL,        // Flags and attributes
        NULL                          // Template file handle
    );
    
    if (hFile == INVALID_HANDLE_VALUE) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to create file: %s", file_path);
        return result;
    }
    
    DWORD bytes_written;
    BOOL write_result = WriteFile(
        hFile,                        // File handle
        content,                      // Buffer to write
        strlen(content),              // Number of bytes to write
        &bytes_written,               // Number of bytes written
        NULL                          // Overlapped structure
    );
    
    if (!write_result) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to write to file: %s", file_path);
        CloseHandle(hFile);
        return result;
    }
    
    CloseHandle(hFile);
    result.success = 1;
    return result;
}
```

### File Reading
```c
FileOperationResult readFile(const char* file_path, char* buffer, int buffer_size) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    HANDLE hFile = CreateFile(
        file_path,                    // File name
        GENERIC_READ,                 // Desired access
        FILE_SHARE_READ,              // Share mode
        NULL,                         // Security attributes
        OPEN_EXISTING,                 // Creation disposition
        FILE_ATTRIBUTE_NORMAL,        // Flags and attributes
        NULL                          // Template file handle
    );
    
    if (hFile == INVALID_HANDLE_VALUE) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to open file: %s", file_path);
        return result;
    }
    
    DWORD bytes_read;
    BOOL read_result = ReadFile(
        hFile,                        // File handle
        buffer,                       // Buffer to read into
        buffer_size - 1,              // Number of bytes to read
        &bytes_read,                  // Number of bytes read
        NULL                          // Overlapped structure
    );
    
    if (!read_result) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to read file: %s", file_path);
        CloseHandle(hFile);
        return result;
    }
    
    buffer[bytes_read] = '\0'; // Null-terminate the string
    CloseHandle(hFile);
    result.success = 1;
    return result;
}
```

### File Appending
```c
FileOperationResult appendToFile(const char* file_path, const char* content) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    HANDLE hFile = CreateFile(
        file_path,                    // File name
        GENERIC_WRITE,                // Desired access
        FILE_SHARE_READ,              // Share mode
        NULL,                         // Security attributes
        OPEN_ALWAYS,                  // Creation disposition
        FILE_ATTRIBUTE_NORMAL,        // Flags and attributes
        NULL                          // Template file handle
    );
    
    if (hFile == INVALID_HANDLE_VALUE) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to open file for appending: %s", file_path);
        return result;
    }
    
    // Move to end of file
    SetFilePointer(hFile, 0, NULL, FILE_END, FILE_BEGIN);
    
    DWORD bytes_written;
    BOOL write_result = WriteFile(
        hFile,                        // File handle
        content,                      // Buffer to write
        strlen(content),              // Number of bytes to write
        &bytes_written,               // Number of bytes written
        NULL                          // Overlapped structure
    );
    
    if (!write_result) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to append to file: %s", file_path);
        CloseHandle(hFile);
        return result;
    }
    
    CloseHandle(hFile);
    result.success = 1;
    return result;
}
```

### File Deletion
```c
FileOperationResult deleteFile(const char* file_path) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    if (!DeleteFile(file_path)) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to delete file: %s", file_path);
        return result;
    }
    
    result.success = 1;
    return result;
}
```

### File Copying
```c
FileOperationResult copyFile(const char* source_path, const char* destination_path) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    if (!CopyFile(source_path, destination_path, FALSE)) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to copy file from %s to %s", source_path, destination_path);
        return result;
    }
    
    result.success = 1;
    return result;
}
```

### File Moving
```c
FileOperationResult moveFile(const char* source_path, const char* destination_path) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    if (!MoveFile(source_path, destination_path)) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to move file from %s to %s", source_path, destination_path);
        return result;
    }
    
    result.success = 1;
    return result;
}
```

## 📂 Directory Operations

### Directory Creation
```c
FileOperationResult createDirectory(const char* dir_path) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    if (!CreateDirectory(dir_path, NULL)) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to create directory: %s", dir_path);
        return result;
    }
    
    result.success = 1;
    return result;
}
```

### Directory Removal
```c
FileOperationResult removeDirectory(const char* dir_path) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    if (!RemoveDirectory(dir_path)) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to remove directory: %s", dir_path);
        return result;
    }
    
    result.success = 1;
    return result;
}
```

### Path Existence Check
```c
int pathExists(const char* path) {
    DWORD attributes = GetFileAttributes(path);
    return (attributes != INVALID_FILE_ATTRIBUTES);
}

int isDirectory(const char* path) {
    DWORD attributes = GetFileAttributes(path);
    return (attributes != INVALID_FILE_ATTRIBUTES && (attributes & FILE_ATTRIBUTE_DIRECTORY));
}

int isFile(const char* path) {
    DWORD attributes = GetFileAttributes(path);
    return (attributes != INVALID_FILE_ATTRIBUTES && !(attributes & FILE_ATTRIBUTE_DIRECTORY));
}
```

## 📊 File Information

### File Information Structure
```c
typedef struct {
    char path[MAX_PATH_LENGTH];
    char name[256];
    DWORD size;
    FILETIME creation_time;
    FILETIME last_access_time;
    FILETIME last_write_time;
    DWORD attributes;
    int is_directory;
    int is_readonly;
} FileInfo;
```

### Getting File Information
```c
FileInfo getFileInfo(const char* file_path) {
    FileInfo info;
    memset(&info, 0, sizeof(FileInfo));
    strcpy(info.path, file_path);
    
    HANDLE hFile = CreateFile(
        file_path,
        GENERIC_READ,
        FILE_SHARE_READ,
        NULL,
        OPEN_EXISTING,
        FILE_ATTRIBUTE_NORMAL,
        NULL
    );
    
    if (hFile != INVALID_HANDLE_VALUE) {
        // Get file size
        info.size = GetFileSize(hFile, NULL);
        
        // Get file times
        GetFileTime(hFile, &info.creation_time, &info.last_access_time, &info.last_write_time);
        
        // Get file attributes
        info.attributes = GetFileAttributes(file_path);
        info.is_directory = (info.attributes & FILE_ATTRIBUTE_DIRECTORY) ? 1 : 0;
        info.is_readonly = (info.attributes & FILE_ATTRIBUTE_READONLY) ? 1 : 0;
        
        // Extract filename
        char* filename = strrchr(file_path, '\\');
        if (filename) {
            strcpy(info.name, filename + 1);
        } else {
            strcpy(info.name, file_path);
        }
        
        CloseHandle(hFile);
    }
    
    return info;
}
```

### Time Formatting
```c
void formatFileTime(FILETIME file_time, char* buffer, int buffer_size) {
    SYSTEMTIME system_time;
    FileTimeToSystemTime(&file_time, &system_time);
    
    sprintf(buffer, "%04d-%02d-%02d %02d:%02d:%02d",
            system_time.wYear, system_time.wMonth, system_time.wDay,
            system_time.wHour, system_time.wMinute, system_time.wSecond);
}
```

### File Information Display
```c
void printFileInfo(const char* file_path) {
    FileInfo info = getFileInfo(file_path);
    
    printf("File Information:\n");
    printf("  Path: %s\n", info.path);
    printf("  Name: %s\n", info.name);
    printf("  Size: %lu bytes\n", info.size);
    printf("  Type: %s\n", info.is_directory ? "Directory" : "File");
    printf("  Read-only: %s\n", info.is_readonly ? "Yes" : "No");
    
    char time_buffer[64];
    formatFileTime(info.creation_time, time_buffer, sizeof(time_buffer));
    printf("  Created: %s\n", time_buffer);
    
    formatFileTime(info.last_access_time, time_buffer, sizeof(time_buffer));
    printf("  Last accessed: %s\n", time_buffer);
    
    formatFileTime(info.last_write_time, time_buffer, sizeof(time_buffer));
    printf("  Last modified: %s\n", time_buffer);
    
    // Print attributes
    printf("  Attributes: ");
    if (info.attributes & FILE_ATTRIBUTE_HIDDEN) printf("Hidden ");
    if (info.attributes & FILE_ATTRIBUTE_SYSTEM) printf("System ");
    if (info.attributes & FILE_ATTRIBUTE_ARCHIVE) printf("Archive ");
    if (info.attributes & FILE_ATTRIBUTE_COMPRESSED) printf("Compressed ");
    if (info.attributes & FILE_ATTRIBUTE_ENCRYPTED) printf("Encrypted ");
    printf("\n");
}
```

## 🔍 Directory Traversal

### Directory Traversal Structure
```c
typedef struct {
    char path[MAX_PATH_LENGTH];
    int file_count;
    int directory_count;
    long long total_size;
} DirectoryInfo;
```

### Recursive Directory Traversal
```c
DirectoryInfo traverseDirectory(const char* dir_path) {
    DirectoryInfo dir_info;
    memset(&dir_info, 0, sizeof(DirectoryInfo));
    strcpy(dir_info.path, dir_path);
    
    WIN32_FIND_DATA find_data;
    HANDLE hFind = INVALID_HANDLE_VALUE;
    char search_path[MAX_PATH_LENGTH];
    
    // Start directory traversal
    sprintf(search_path, "%s\\*", dir_path);
    hFind = FindFirstFile(search_path, &find_data);
    
    if (hFind == INVALID_HANDLE_VALUE) {
        printf("Failed to traverse directory: %s\n", dir_path);
        return dir_info;
    }
    
    do {
        if (strcmp(find_data.cFileName, ".") == 0 || 
            strcmp(find_data.cFileName, "..") == 0) {
            continue; // Skip . and ..
        }
        
        if (find_data.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY) {
            dir_info.directory_count++;
            
            // Recursively traverse subdirectory
            char sub_dir_path[MAX_PATH_LENGTH];
            sprintf(sub_dir_path, "%s\\%s", dir_path, find_data.cFileName);
            DirectoryInfo sub_dir_info = traverseDirectory(sub_dir_path);
            dir_info.file_count += sub_dir_info.file_count;
            dir_info.directory_count += sub_dir_info.directory_count;
            dir_info.total_size += sub_dir_info.total_size;
        } else {
            dir_info.file_count++;
            dir_info.total_size += find_data.nFileSizeLow;
        }
    } while (FindNextFile(hFind, &find_data));
    
    FindClose(hFind);
    return dir_info;
}
```

### Directory Listing
```c
void listDirectoryContents(const char* dir_path) {
    WIN32_FIND_DATA find_data;
    HANDLE hFind = INVALID_HANDLE_VALUE;
    char search_path[MAX_PATH_LENGTH];
    
    printf("Directory contents of: %s\n", dir_path);
    printf("=====================================\n");
    
    sprintf(search_path, "%s\\*", dir_path);
    hFind = FindFirstFile(search_path, &find_data);
    
    if (hFind == INVALID_HANDLE_VALUE) {
        printf("Failed to list directory: %s\n", dir_path);
        return;
    }
    
    do {
        if (strcmp(find_data.cFileName, ".") == 0 || 
            strcmp(find_data.cFileName, "..") == 0) {
            continue; // Skip . and ..
        }
        
        char full_path[MAX_PATH_LENGTH];
        sprintf(full_path, "%s\\%s", dir_path, find_data.cFileName);
        
        if (find_data.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY) {
            printf("  [DIR]  %s\n", find_data.cFileName);
        } else {
            printf("  [FILE] %s (%lu bytes)\n", find_data.cFileName, find_data.nFileSizeLow);
        }
    } while (FindNextFile(hFind, &find_data));
    
    FindClose(hFind);
    
    DirectoryInfo info = traverseDirectory(dir_path);
    printf("\nTotal: %d files, %d directories, %lld bytes\n", 
           info.file_count, info.directory_count, info.total_size);
}
```

## 🔎 File Search and Filtering

### Pattern-Based Search
```c
void searchFiles(const char* dir_path, const char* pattern, int recursive) {
    WIN32_FIND_DATA find_data;
    HANDLE hFind = INVALID_HANDLE_VALUE;
    char search_path[MAX_PATH_LENGTH];
    
    printf("Searching for pattern '%s' in: %s\n", pattern, dir_path);
    printf("========================================\n");
    
    sprintf(search_path, "%s\\%s", dir_path, pattern);
    hFind = FindFirstFile(search_path, &find_data);
    
    if (hFind == INVALID_HANDLE_VALUE) {
        printf("No files found matching pattern: %s\n", pattern);
        return;
    }
    
    int found_count = 0;
    do {
        if (strcmp(find_data.cFileName, ".") == 0 || 
            strcmp(find_data.cFileName, "..") == 0) {
            continue; // Skip . and ..
        }
        
        char full_path[MAX_PATH_LENGTH];
        sprintf(full_path, "%s\\%s", dir_path, find_data.cFileName);
        
        if (!(find_data.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY)) {
            printf("  Found: %s (%lu bytes)\n", find_data.cFileName, find_data.nFileSizeLow);
            found_count++;
        }
        
        if (recursive && (find_data.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY)) {
            // Recursively search subdirectory
            searchFiles(full_path, pattern, 1);
        }
    } while (FindNextFile(hFind, &find_data));
    
    FindClose(hFind);
    
    if (found_count == 0) {
        printf("No files found matching pattern: %s\n", pattern);
    }
}
```

### Size-Based Search
```c
void findFilesBySize(const char* dir_path, long long min_size, long long max_size) {
    WIN32_FIND_DATA find_data;
    HANDLE hFind = INVALID_HANDLE_VALUE;
    char search_path[MAX_PATH_LENGTH];
    
    printf("Finding files between %lld and %lld bytes in: %s\n", min_size, max_size, dir_path);
    printf("====================================================\n");
    
    sprintf(search_path, "%s\\*", dir_path);
    hFind = FindFirstFile(search_path, &find_data);
    
    if (hFind == INVALID_HANDLE_VALUE) {
        printf("Failed to search directory: %s\n", dir_path);
        return;
    }
    
    int found_count = 0;
    do {
        if (strcmp(find_data.cFileName, ".") == 0 || 
            strcmp(find_data.cFileName, "..") == 0) {
            continue; // Skip . and ..
        }
        
        if (!(find_data.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY)) {
            long long file_size = find_data.nFileSizeLow;
            if (file_size >= min_size && file_size <= max_size) {
                printf("  Found: %s (%lld bytes)\n", find_data.cFileName, file_size);
                found_count++;
            }
        }
    } while (FindNextFile(hFind, &find_data));
    
    FindClose(hFind);
    
    if (found_count == 0) {
        printf("No files found in specified size range.\n");
    }
}
```

### Date-Based Search
```c
void findFilesByDate(const char* dir_path, int days_old) {
    WIN32_FIND_DATA find_data;
    HANDLE hFind = INVALID_HANDLE_VALUE;
    char search_path[MAX_PATH_LENGTH];
    
    // Calculate cutoff date
    SYSTEMTIME current_time;
    GetLocalTime(&current_time);
    
    FILETIME current_file_time;
    SystemTimeToFileTime(&current_time, &current_file_time);
    
    ULARGE_INTEGER current_time_int;
    current_time_int.LowPart = current_file_time.dwLowDateTime;
    current_time_int.HighPart = current_file_time.dwHighDateTime;
    
    ULONGLONG cutoff_time = current_time_int.QuadPart - (ULONGLONG)days_old * 24 * 60 * 60 * 10000000ULL;
    
    printf("Finding files older than %d days in: %s\n", days_old, dir_path);
    printf("============================================\n");
    
    sprintf(search_path, "%s\\*", dir_path);
    hFind = FindFirstFile(search_path, &find_data);
    
    if (hFind == INVALID_HANDLE_VALUE) {
        printf("Failed to search directory: %s\n", dir_path);
        return;
    }
    
    int found_count = 0;
    do {
        if (strcmp(find_data.cFileName, ".") == 0 || 
            strcmp(find_data.cFileName, "..") == 0) {
            continue; // Skip . and ..
        }
        
        if (!(find_data.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY)) {
            ULARGE_INTEGER file_time;
            file_time.LowPart = find_data.ftLastWriteTime.dwLowDateTime;
            file_time.HighPart = find_data.ftLastWriteTime.dwHighDateTime;
            
            if (file_time.QuadPart < cutoff_time) {
                printf("  Found: %s\n", find_data.cFileName);
                found_count++;
            }
        }
    } while (FindNextFile(hFind, &find_data));
    
    FindClose(hFind);
    
    if (found_count == 0) {
        printf("No files found older than %d days.\n", days_old);
    }
}
```

## 📋 File Comparison

### Binary File Comparison
```c
int compareFiles(const char* file1_path, const char* file2_path) {
    FILE* file1 = fopen(file1_path, "rb");
    FILE* file2 = fopen(file2_path, "rb");
    
    if (!file1 || !file2) {
        if (file1) fclose(file1);
        if (file2) fclose(file2);
        return -1; // Error opening files
    }
    
    int result = 0;
    char buffer1[BUFFER_SIZE];
    char buffer2[BUFFER_SIZE];
    
    while (!feof(file1) && !feof(file2)) {
        size_t read1 = fread(buffer1, 1, BUFFER_SIZE, file1);
        size_t read2 = fread(buffer2, 1, BUFFER_SIZE, file2);
        
        if (read1 != read2) {
            result = 1; // Different sizes
            break;
        }
        
        if (memcmp(buffer1, buffer2, read1) != 0) {
            result = 1; // Different content
            break;
        }
    }
    
    fclose(file1);
    fclose(file2);
    
    return result;
}
```

### Duplicate File Detection
```c
void findDuplicateFiles(const char* dir_path) {
    printf("Finding duplicate files in: %s\n", dir_path);
    printf("================================\n");
    
    // Store file info for comparison
    typedef struct {
        char name[256];
        long long size;
    } FileInfoEntry;
    
    FileInfoEntry files[1000];
    int file_count = 0;
    
    // Collect file information
    WIN32_FIND_DATA find_data;
    HANDLE hFind = INVALID_HANDLE_VALUE;
    char search_path[MAX_PATH_LENGTH];
    
    sprintf(search_path, "%s\\*", dir_path);
    hFind = FindFirstFile(search_path, &find_data);
    
    if (hFind == INVALID_HANDLE_VALUE) {
        printf("Failed to search directory: %s\n", dir_path);
        return;
    }
    
    do {
        if (strcmp(find_data.cFileName, ".") == 0 || 
            strcmp(find_data.cFileName, "..") == 0) {
            continue; // Skip . and ..
        }
        
        if (!(find_data.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY)) {
            if (file_count < 1000) {
                strcpy(files[file_count].name, find_data.cFileName);
                files[file_count].size = find_data.nFileSizeLow;
                file_count++;
            }
        }
    } while (FindNextFile(hFind, &find_data));
    
    FindClose(hFind);
    
    // Compare files by size first
    int duplicates_found = 0;
    for (int i = 0; i < file_count; i++) {
        for (int j = i + 1; j < file_count; j++) {
            if (files[i].size == files[j].size && files[i].size > 0) {
                char file1_path[MAX_PATH_LENGTH];
                char file2_path[MAX_PATH_LENGTH];
                sprintf(file1_path, "%s\\%s", dir_path, files[i].name);
                sprintf(file2_path, "%s\\%s", dir_path, files[j].name);
                
                if (compareFiles(file1_path, file2_path) == 0) {
                    printf("Duplicate: %s and %s\n", files[i].name, files[j].name);
                    duplicates_found++;
                }
            }
        }
    }
    
    if (duplicates_found == 0) {
        printf("No duplicate files found.\n");
    }
}
```

## 🔄 File Monitoring

### Directory Change Monitoring
```c
void monitorFileChanges(const char* dir_path, int duration_seconds) {
    printf("Monitoring changes in: %s for %d seconds\n", dir_path, duration_seconds);
    printf("==========================================\n");
    
    HANDLE hDir = CreateFile(
        dir_path,
        FILE_LIST_DIRECTORY,
        FILE_SHARE_READ | FILE_SHARE_WRITE | FILE_SHARE_DELETE,
        NULL,
        OPEN_EXISTING,
        FILE_FLAG_BACKUP_SEMANTICS,
        NULL
    );
    
    if (hDir == INVALID_HANDLE_VALUE) {
        printf("Failed to monitor directory: %s\n", dir_path);
        return;
    }
    
    BYTE buffer[BUFFER_SIZE];
    DWORD bytes_returned;
    
    time_t start_time = time(NULL);
    
    while (time(NULL) - start_time < duration_seconds) {
        if (ReadDirectoryChangesW(
            hDir,
            buffer,
            BUFFER_SIZE,
            TRUE,
            FILE_NOTIFY_CHANGE_FILE_NAME | FILE_NOTIFY_CHANGE_DIR_NAME | 
            FILE_NOTIFY_CHANGE_ATTRIBUTES | FILE_NOTIFY_CHANGE_SIZE | 
            FILE_NOTIFY_CHANGE_LAST_WRITE | FILE_NOTIFY_CHANGE_CREATION,
            &bytes_returned,
            NULL,
            NULL
        )) {
            printf("Directory change detected!\n");
        }
        
        Sleep(1000); // Check every second
    }
    
    CloseHandle(hDir);
    printf("Monitoring completed.\n");
}
```

## 💾 File Backup and Restore

### File Backup with Timestamp
```c
FileOperationResult backupFile(const char* source_path, const char* backup_dir) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    // Create backup filename with timestamp
    time_t now = time(NULL);
    struct tm* timeinfo = localtime(&now);
    
    char backup_filename[MAX_PATH_LENGTH];
    char* source_filename = strrchr(source_path, '\\');
    if (source_filename) {
        source_filename++;
    } else {
        source_filename = (char*)source_path;
    }
    
    sprintf(backup_filename, "%s\\%s_%04d%02d%02d_%02d%02d%02d.bak",
            backup_dir, source_filename,
            timeinfo->tm_year + 1900, timeinfo->tm_mon + 1, timeinfo->tm_mday,
            timeinfo->tm_hour, timeinfo->tm_min, timeinfo->tm_sec);
    
    // Copy file to backup location
    if (!CopyFile(source_path, backup_filename, FALSE)) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to backup file: %s", source_path);
        return result;
    }
    
    result.success = 1;
    printf("File backed up to: %s\n", backup_filename);
    return result;
}
```

## 🗜️ File Compression (NTFS)

### File Compression
```c
FileOperationResult compressFile(const char* file_path) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    DWORD attributes = GetFileAttributes(file_path);
    if (attributes == INVALID_FILE_ATTRIBUTES) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to get file attributes: %s", file_path);
        return result;
    }
    
    if (SetFileAttributes(file_path, attributes | FILE_ATTRIBUTE_COMPRESSED)) {
        result.success = 1;
        printf("File compressed: %s\n", file_path);
    } else {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to compress file: %s", file_path);
    }
    
    return result;
}
```

### File Decompression
```c
FileOperationResult uncompressFile(const char* file_path) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    DWORD attributes = GetFileAttributes(file_path);
    if (attributes == INVALID_FILE_ATTRIBUTES) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to get file attributes: %s", file_path);
        return result;
    }
    
    if (SetFileAttributes(file_path, attributes & ~FILE_ATTRIBUTE_COMPRESSED)) {
        result.success = 1;
        printf("File uncompressed: %s\n", file_path);
    } else {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to uncompress file: %s", file_path);
    }
    
    return result;
}
```

## 🔐 File Encryption (EFS)

### File Encryption
```c
FileOperationResult encryptFile(const char* file_path) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    if (!EncryptFile(file_path)) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to encrypt file: %s", file_path);
        return result;
    }
    
    result.success = 1;
    printf("File encrypted: %s\n", file_path);
    return result;
}
```

### File Decryption
```c
FileOperationResult decryptFile(const char* file_path) {
    FileOperationResult result;
    initFileOperationResult(&result);
    
    if (!DecryptFile(file_path)) {
        result.error_code = GetLastError();
        sprintf(result.error_message, "Failed to decrypt file: %s", file_path);
        return result;
    }
    
    result.success = 1;
    printf("File decrypted: %s\n", file_path);
    return result;
}
```

## 📦 Batch Operations

### Batch File Processing
```c
void batchProcessFiles(const char* dir_path, const char* pattern, 
                       FileOperationResult (*process_func)(const char*)) {
    WIN32_FIND_DATA find_data;
    HANDLE hFind = INVALID_HANDLE_VALUE;
    char search_path[MAX_PATH_LENGTH];
    
    printf("Batch processing files matching '%s' in: %s\n", pattern, dir_path);
    printf("=========================================\n");
    
    sprintf(search_path, "%s\\%s", dir_path, pattern);
    hFind = FindFirstFile(search_path, &find_data);
    
    if (hFind == INVALID_HANDLE_VALUE) {
        printf("No files found matching pattern: %s\n", pattern);
        return;
    }
    
    int processed_count = 0;
    int error_count = 0;
    
    do {
        if (strcmp(find_data.cFileName, ".") == 0 || 
            strcmp(find_data.cFileName, "..") == 0) {
            continue; // Skip . and ..
        }
        
        if (!(find_data.dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY)) {
            char full_path[MAX_PATH_LENGTH];
            sprintf(full_path, "%s\\%s", dir_path, find_data.cFileName);
            
            printf("Processing: %s\n", find_data.cFileName);
            FileOperationResult result = process_func(full_path);
            
            if (result.success) {
                processed_count++;
            } else {
                error_count++;
                printf("  Error: %s\n", result.error_message);
            }
        }
    } while (FindNextFile(hFind, &find_data));
    
    FindClose(hFind);
    
    printf("\nBatch processing completed: %d processed, %d errors\n", 
           processed_count, error_count);
}
```

## 📊 Performance Considerations

### Buffer Size Optimization
```c
void optimizeFileOperations() {
    // Use appropriate buffer sizes
    #define SMALL_BUFFER 1024    // For small files
    #define MEDIUM_BUFFER 4096   // For medium files
    #define LARGE_BUFFER 8192    // For large files
    
    // Choose buffer size based on file size
    DWORD file_size = GetFileSize(hFile, NULL);
    int buffer_size;
    
    if (file_size < SMALL_BUFFER) {
        buffer_size = SMALL_BUFFER;
    } else if (file_size < MEDIUM_BUFFER) {
        buffer_size = MEDIUM_BUFFER;
    } else {
        buffer_size = LARGE_BUFFER;
    }
    
    // Use optimal buffer for reading
    char* buffer = malloc(buffer_size);
    DWORD bytes_read;
    ReadFile(hFile, buffer, buffer_size, &bytes_read, NULL);
    free(buffer);
}
```

### Asynchronous File Operations
```c
void asynchronousFileOperations() {
    // Use overlapped I/O for better performance
    HANDLE hFile = CreateFile(
        file_path,
        GENERIC_READ,
        FILE_SHARE_READ,
        NULL,
        OPEN_EXISTING,
        FILE_FLAG_OVERLAPPED,
        NULL
    );
    
    OVERLAPPED overlapped;
    ZeroMemory(&overlapped, sizeof(overlapped));
    
    char buffer[BUFFER_SIZE];
    DWORD bytes_read;
    
    // Asynchronous read
    if (ReadFile(hFile, buffer, BUFFER_SIZE, &bytes_read, &overlapped)) {
        if (GetLastError() == ERROR_IO_PENDING) {
            // Operation is pending, wait for completion
            DWORD bytes_transferred;
            GetOverlappedResult(hFile, &bytes_transferred, FALSE);
        }
    }
    
    CloseHandle(hFile);
}
```

### Memory-Mapped Files
```c
void memoryMappedFileOperations() {
    HANDLE hFile = CreateFile(
        file_path,
        GENERIC_READ | GENERIC_WRITE,
        FILE_SHARE_READ,
        NULL,
        OPEN_EXISTING,
        FILE_ATTRIBUTE_NORMAL,
        NULL
    );
    
    DWORD file_size = GetFileSize(hFile, NULL);
    
    HANDLE hMapping = CreateFileMapping(
        hFile,
        NULL,
        PAGE_READWRITE,
        0,
        file_size,
        NULL
    );
    
    if (hMapping != NULL) {
        // Map the file into memory
        LPVOID pView = MapViewOfFile(
            hMapping,
            FILE_MAP_ALL_ACCESS,
            0,
            0,
            file_size
        );
        
        if (pView != NULL) {
            // Access file as memory
            char* file_data = (char*)pView;
            printf("File content: %s\n", file_data);
            
            UnmapViewOfFile(pView);
        }
        
        CloseHandle(hMapping);
    }
    
    CloseHandle(hFile);
}
```

## ⚠️ Common Pitfalls

### 1. Not Checking Return Values
```c
// Wrong - Not checking return values
void unsafeFileOperation() {
    HANDLE hFile = CreateFile("test.txt", GENERIC_WRITE, 0, NULL, CREATE_ALWAYS, 0, NULL);
    WriteFile(hFile, "data", 4, NULL, NULL); // May fail
    CloseHandle(hFile); // May close invalid handle
}

// Right - Always check return values
void safeFileOperation() {
    HANDLE hFile = CreateFile("test.txt", GENERIC_WRITE, 0, NULL, CREATE_ALWAYS, 0, NULL);
    if (hFile != INVALID_HANDLE_VALUE) {
        DWORD bytes_written;
        if (WriteFile(hFile, "data", 4, &bytes_written, NULL)) {
            printf("Successfully wrote %lu bytes\n", bytes_written);
        }
        CloseHandle(hFile);
    } else {
        printf("Failed to create file: %d\n", GetLastError());
    }
}
```

### 2. Buffer Overflows
```c
// Wrong - Fixed buffer size
void unsafeRead() {
    char buffer[256];
    DWORD bytes_read;
    ReadFile(hFile, buffer, 1024, &bytes_read, NULL); // Buffer overflow!
}

// Right - Use appropriate buffer size
void safeRead(HANDLE hFile) {
    char buffer[1024];
    DWORD bytes_read;
    ReadFile(hFile, buffer, sizeof(buffer), &bytes_read, NULL);
    
    // Ensure null termination for strings
    if (bytes_read < sizeof(buffer)) {
        buffer[bytes_read] = '\0';
    }
}
```

### 3. Resource Leaks
```c
// Wrong - Not closing file handles
void resourceLeak() {
    HANDLE hFile = CreateFile("test.txt", GENERIC_READ, 0, NULL, OPEN_EXISTING, 0, NULL);
    // Forgot to close handle!
}

// Right - Always close handles
void noResourceLeak() {
    HANDLE hFile = CreateFile("test.txt", GENERIC_READ, 0, NULL, OPEN_EXISTING, 0, NULL);
    if (hFile != INVALID_HANDLE_VALUE) {
        // Use file
        CloseHandle(hFile);
    }
}
```

### 4. Path Traversal Vulnerabilities
```c
// Wrong - Unsafe path construction
void unsafePathConstruction(char* filename) {
    char path[256];
    sprintf(path, "C:\\data\\%s", filename); // Could be "C:\\..\\windows\\system32"
    // Use path
}

// Right - Validate and sanitize paths
void safePathConstruction(char* filename) {
    char path[256];
    
    // Validate filename
    if (strstr(filename, "..") || strstr(filename, ":") || strstr(filename, "\\\\\")) {
        printf("Invalid filename: %s\n", filename);
        return;
    }
    
    // Construct safe path
    sprintf(path, "C:\\safe_data\\%s", filename);
}
```

## 🔧 Real-World Applications

### 1. Log File Management
```c
void writeLogEntry(const char* message) {
    time_t now = time(NULL);
    struct tm* timeinfo = localtime(&now);
    
    char log_entry[1024];
    sprintf(log_entry, "[%04d-%02d-%02d %02d:%02d:%02d] %s\n",
            timeinfo->tm_year + 1900, timeinfo->tm_mon + 1, timeinfo->tm_mday,
            timeinfo->tm_hour, timeinfo->tm_min, timeinfo->tm_sec, message);
    
    appendToFile("application.log", log_entry);
}
```

### 2. Configuration Management
```c
void loadConfiguration(const char* config_file) {
    char buffer[4096];
    FileOperationResult result = readFile(config_file, buffer, sizeof(buffer));
    
    if (result.success) {
        // Parse configuration
        parseConfiguration(buffer);
    } else {
        printf("Failed to load configuration: %s\n", result.error_message);
        // Use default configuration
        useDefaultConfiguration();
    }
}
```

### 3. Data Export/Import
```c
void exportDataToCSV(const char* filename, DataRecord* records, int count) {
    FILE* file = fopen(filename, "w");
    if (!file) return;
    
    // Write CSV header
    fprintf(file, "ID,Name,Value,Timestamp\n");
    
    // Write data records
    for (int i = 0; i < count; i++) {
        fprintf(file, "%d,%s,%f,%ld\n", 
                records[i].id, records[i].name, records[i].value, records[i].timestamp);
    }
    
    fclose(file);
}
```

### 4. File System Monitoring
```c
void setupFileSystemMonitor() {
    // Create monitoring thread
    HANDLE hThread = CreateThread(NULL, 0, fileMonitorThread, NULL, 0, NULL);
    
    // Monitor for changes
    monitorFileChanges("C:\\data", INFINITE);
    
    // Clean up
    CloseHandle(hThread);
}
```

## 🎓 Best Practices

### 1. Error Handling
```c
void robustFileOperation(const char* filename) {
    HANDLE hFile = CreateFile(filename, GENERIC_READ, FILE_SHARE_READ, NULL, OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);
    
    if (hFile == INVALID_HANDLE_VALUE) {
        DWORD error = GetLastError();
        printf("Error opening file %s: %d\n", filename, error);
        return;
    }
    
    // Perform file operations
    // ...
    
    CloseHandle(hFile);
}
```

### 2. Resource Management
```c
void managedFileOperation(const char* filename) {
    HANDLE hFile = INVALID_HANDLE_VALUE;
    
    __try {
        hFile = CreateFile(filename, GENERIC_READ, FILE_SHARE_READ, NULL, OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);
        if (hFile == INVALID_HANDLE_VALUE) __leave;
        
        // Perform file operations
        // ...
        
        CloseHandle(hFile);
        hFile = INVALID_HANDLE_VALUE;
    }
    __finally {
        if (hFile != INVALID_HANDLE_VALUE) {
            CloseHandle(hFile);
        }
    }
}
```

### 3. Path Handling
```c
void safePathHandling(const char* filename) {
    char full_path[MAX_PATH_LENGTH];
    
    // Get full path
    DWORD result = GetFullPathName(filename, MAX_PATH_LENGTH, full_path, NULL);
    if (result == 0) {
        printf("Invalid path: %s\n", filename);
        return;
    }
    
    // Use full path for all operations
    HANDLE hFile = CreateFile(full_path, GENERIC_READ, FILE_SHARE_READ, NULL, OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);
    
    if (hFile != INVALID_HANDLE_VALUE) {
        CloseHandle(hFile);
    }
}
```

### 4. Buffer Management
```c
void efficientFileReading(const char* filename) {
    HANDLE hFile = CreateFile(filename, GENERIC_READ, FILE_SHARE_READ, NULL, OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);
    if (hFile == INVALID_HANDLE_VALUE) return;
    
    // Get file size for optimal buffer
    DWORD file_size = GetFileSize(hFile, NULL);
    
    DWORD buffer_size = min(file_size, BUFFER_SIZE);
    char* buffer = malloc(buffer_size);
    
    DWORD bytes_read;
    if (ReadFile(hFile, buffer, buffer_size, &bytes_read, NULL)) {
        // Process data
        processFileData(buffer, bytes_read);
    }
    
    free(buffer);
    CloseHandle(hFile);
}
```

### 5. Atomic Operations
```c
void atomicFileUpdate(const char* filename, const char* content) {
    // Create temporary file
    char temp_filename[MAX_PATH_LENGTH];
    sprintf(temp_filename, "%s.tmp", filename);
    
    // Write to temporary file
    if (createFile(temp_filename, content).success) {
        // Atomic rename
        if (MoveFileEx(temp_filename, filename, MOVEFILE_REPLACE_EXISTING)) {
            printf("File updated atomically\n");
        } else {
            printf("Failed to update file atomically\n");
            deleteFile(temp_filename);
        }
    }
}
```

## 📚 Cross-Platform Considerations

### POSIX Functions
```c
#ifdef _WIN32
    // Windows-specific implementations
    #include <windows.h>
#else
    // POSIX implementations
    #include <unistd.h>
    #include <sys/stat.h>
    #include <dirent.h>
#endif

void crossPlatformFileOperation(const char* filename) {
#ifdef _WIN32
    HANDLE hFile = CreateFile(filename, GENERIC_READ, FILE_SHARE_READ, NULL, OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);
    if (hFile != INVALID_HANDLE_VALUE) {
        // Windows-specific operations
        CloseHandle(hFile);
    }
#else
    int fd = open(filename, O_RDONLY);
    if (fd != -1) {
        // POSIX operations
        close(fd);
    }
#endif
}
```

### Library Options
```c
// Use portable libraries for cross-platform development
// - Boost.Filesystem
// - Qt QFile
// - std::filesystem (C++17)
// - Custom abstraction layer
```

File system operations in C provide powerful capabilities for managing files and directories. Master these concepts to build robust applications that handle data persistence effectively!
