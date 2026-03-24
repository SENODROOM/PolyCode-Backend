#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <windows.h>
#include <time.h>
#include <direct.h>
#include <sys/stat.h>
#include <errno.h>

// =============================================================================
// FILE SYSTEM FUNDAMENTALS
// =============================================================================

#define MAX_PATH_LENGTH 1024
#define MAX_FILE_SIZE (1024 * 1024 * 1024) // 1GB
#define BUFFER_SIZE 4096

// File operation result structure
typedef struct {
    int success;
    DWORD error_code;
    char error_message[256];
} FileOperationResult;

// File information structure
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

// Directory traversal structure
typedef struct {
    char path[MAX_PATH_LENGTH];
    int file_count;
    int directory_count;
    long long total_size;
} DirectoryInfo;

// =============================================================================
// FILE OPERATIONS
// =============================================================================

// Initialize file operation result
void initFileOperationResult(FileOperationResult* result) {
    result->success = 0;
    result->error_code = 0;
    strcpy(result->error_message, "");
}

// Create a new file
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

// Read file content
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

// Append content to file
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

// Delete a file
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

// Copy a file
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

// Move a file
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

// =============================================================================
// DIRECTORY OPERATIONS
// =============================================================================

// Create a directory
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

// Remove a directory
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

// Check if a path exists
int pathExists(const char* path) {
    DWORD attributes = GetFileAttributes(path);
    return (attributes != INVALID_FILE_ATTRIBUTES);
}

// Check if path is a directory
int isDirectory(const char* path) {
    DWORD attributes = GetFileAttributes(path);
    return (attributes != INVALID_FILE_ATTRIBUTES && (attributes & FILE_ATTRIBUTE_DIRECTORY));
}

// Check if path is a file
int isFile(const char* path) {
    DWORD attributes = GetFileAttributes(path);
    return (attributes != INVALID_FILE_ATTRIBUTES && !(attributes & FILE_ATTRIBUTE_DIRECTORY));
}

// =============================================================================
// FILE INFORMATION
// =============================================================================

// Get file information
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

// Format file time to readable string
void formatFileTime(FILETIME file_time, char* buffer, int buffer_size) {
    SYSTEMTIME system_time;
    FileTimeToSystemTime(&file_time, &system_time);
    
    sprintf(buffer, "%04d-%02d-%02d %02d:%02d:%02d",
            system_time.wYear, system_time.wMonth, system_time.wDay,
            system_time.wHour, system_time.wMinute, system_time.wSecond);
}

// Print file information
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

// =============================================================================
// DIRECTORY TRAVERSAL
// =============================================================================

// Directory traversal callback function
BOOL WINAPI traverseDirectoryCallback(
    LPCTSTR lpFilePath,
    LPWIN32_FIND_DATA lpFindFileData,
    DWORD dwFileAttributes,
    LPVOID lpContext
) {
    DirectoryInfo* dir_info = (DirectoryInfo*)lpContext;
    
    if (strcmp(lpFindFileData->cFileName, ".") == 0 || 
        strcmp(lpFindFileData->cFileName, "..") == 0) {
        return TRUE; // Skip . and ..
    }
    
    if (lpFindFileData->dwFileAttributes & FILE_ATTRIBUTE_DIRECTORY) {
        dir_info->directory_count++;
    } else {
        dir_info->file_count++;
        dir_info->total_size += lpFindFileData->nFileSizeLow;
    }
    
    return TRUE;
}

// Traverse directory and collect information
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

// List directory contents
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

// =============================================================================
// FILE SEARCH AND FILTERING
// =============================================================================

// Search for files by pattern
void searchFiles(const char* dir_path, const char* pattern, int recursive) {
    WIN32_FIND_DATA find_data;
    HANDLE hFind = INVALID_HANDLE_VALUE;
    char search_path[MAX_PATH_LENGTH];
    
    printf("Searching for pattern '%s' in: %s\n", pattern, dir_path);
    printf("=========================================\n");
    
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

// Find files by size
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

// Find files by date
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

// =============================================================================
// FILE COMPARISON
// =============================================================================

// Compare two files
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

// Find duplicate files in directory
void findDuplicateFiles(const char* dir_path) {
    printf("Finding duplicate files in: %s\n", dir_path);
    printf("================================\n");
    
    // This is a simplified implementation
    // In a real implementation, you'd use file hashing for efficiency
    
    WIN32_FIND_DATA find_data;
    HANDLE hFind = INVALID_HANDLE_VALUE;
    char search_path[MAX_PATH_LENGTH];
    
    sprintf(search_path, "%s\\*", dir_path);
    hFind = FindFirstFile(search_path, &find_data);
    
    if (hFind == INVALID_HANDLE_VALUE) {
        printf("Failed to search directory: %s\n", dir_path);
        return;
    }
    
    // Store file info for comparison
    typedef struct {
        char name[256];
        long long size;
    } FileInfoEntry;
    
    FileInfoEntry files[1000];
    int file_count = 0;
    
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

// =============================================================================
// FILE MONITORING
// =============================================================================

// File change monitoring (simplified)
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
            
            // In a real implementation, you'd parse the buffer to show specific changes
            // For simplicity, we just note that a change occurred
        }
        
        Sleep(1000); // Check every second
    }
    
    CloseHandle(hDir);
    printf("Monitoring completed.\n");
}

// =============================================================================
// FILE BACKUP AND RESTORE
// =============================================================================

// Backup a file (copy with timestamp)
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

// =============================================================================
// FILE COMPRESSION (Windows NTFS)
// =============================================================================

// Compress a file
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

// Uncompress a file
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

// =============================================================================
// FILE ENCRYPTION (Windows EFS)
// =============================================================================

// Encrypt a file
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

// Decrypt a file
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

// =============================================================================
// BATCH FILE OPERATIONS
// =============================================================================

// Process multiple files in a directory
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

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateBasicFileOperations() {
    printf("=== BASIC FILE OPERATIONS DEMO ===\n");
    
    const char* test_file = "test_file.txt";
    const char* test_content = "This is a test file for demonstrating basic file operations.\n"
                             "It contains multiple lines of text.\n"
                             "We will test reading, writing, and other operations.\n";
    
    // Create file
    printf("Creating file...\n");
    FileOperationResult result = createFile(test_file, test_content);
    if (result.success) {
        printf("File created successfully.\n");
    } else {
        printf("Error: %s\n", result.error_message);
        return;
    }
    
    // Read file
    printf("\nReading file...\n");
    char buffer[1024];
    result = readFile(test_file, buffer, sizeof(buffer));
    if (result.success) {
        printf("File content:\n%s\n", buffer);
    } else {
        printf("Error: %s\n", result.error_message);
    }
    
    // Append to file
    printf("\nAppending to file...\n");
    const char* append_content = "\nThis line was appended.\n";
    result = appendToFile(test_file, append_content);
    if (result.success) {
        printf("Content appended successfully.\n");
    } else {
        printf("Error: %s\n", result.error_message);
    }
    
    // Copy file
    printf("\nCopying file...\n");
    const char* copy_file = "test_file_copy.txt";
    result = copyFile(test_file, copy_file);
    if (result.success) {
        printf("File copied successfully to %s\n", copy_file);
    } else {
        printf("Error: %s\n", result.error_message);
    }
    
    // Move file
    printf("\nMoving file...\n");
    const char* moved_file = "test_file_moved.txt";
    result = moveFile(copy_file, moved_file);
    if (result.success) {
        printf("File moved successfully to %s\n", moved_file);
    } else {
        printf("Error: %s\n", result.error_message);
    }
    
    // Print file info
    printf("\nFile information:\n");
    printFileInfo(test_file);
    printFileInfo(moved_file);
    
    // Delete files
    printf("\nCleaning up...\n");
    deleteFile(test_file);
    deleteFile(moved_file);
    
    printf("Basic file operations demo completed.\n\n");
}

void demonstrateDirectoryOperations() {
    printf("=== DIRECTORY OPERATIONS DEMO ===\n");
    
    const char* test_dir = "test_directory";
    const char* sub_dir = "test_directory\\subdirectory";
    
    // Create directory
    printf("Creating directory: %s\n", test_dir);
    FileOperationResult result = createDirectory(test_dir);
    if (result.success) {
        printf("Directory created successfully.\n");
    } else {
        printf("Error: %s\n", result.error_message);
        return;
    }
    
    // Create subdirectory
    printf("Creating subdirectory: %s\n", sub_dir);
    result = createDirectory(sub_dir);
    if (result.success) {
        printf("Subdirectory created successfully.\n");
    } else {
        printf("Error: %s\n", result.error_message);
    }
    
    // Create files in directories
    printf("\nCreating test files...\n");
    createFile("test_directory\\file1.txt", "Content of file1");
    createFile("test_directory\\file2.txt", "Content of file2");
    createFile("test_directory\\subdirectory\\file3.txt", "Content of file3");
    
    // List directory contents
    printf("\nListing directory contents:\n");
    listDirectoryContents(test_dir);
    
    // Get directory information
    printf("\nDirectory information:\n");
    DirectoryInfo info = traverseDirectory(test_dir);
    printf("Total files: %d\n", info.file_count);
    printf("Total directories: %d\n", info.directory_count);
    printf("Total size: %lld bytes\n", info.total_size);
    
    // Clean up
    printf("\nCleaning up...\n");
    deleteFile("test_directory\\file1.txt");
    deleteFile("test_directory\\file2.txt");
    deleteFile("test_directory\\subdirectory\\file3.txt");
    removeDirectory(sub_dir);
    removeDirectory(test_dir);
    
    printf("Directory operations demo completed.\n\n");
}

void demonstrateFileSearch() {
    printf("=== FILE SEARCH DEMO ===\n");
    
    // Create test files
    printf("Creating test files...\n");
    createDirectory("search_test");
    createFile("search_test\\test1.txt", "Test content 1");
    createFile("search_test\\test2.txt", "Test content 2");
    createFile("search_test\\data.log", "Log data");
    createFile("search_test\\config.ini", "Configuration data");
    
    // Search by pattern
    printf("\nSearching for *.txt files...\n");
    searchFiles("search_test", "*.txt", 0);
    
    // Search by size
    printf("\nSearching for files between 10 and 30 bytes...\n");
    findFilesBySize("search_test", 10, 30);
    
    // Search by date (files older than 0 days = all files)
    printf("\nSearching for files (all files):\n");
    findFilesByDate("search_test", 0);
    
    // Find duplicates
    printf("\nFinding duplicate files...\n");
    findDuplicateFiles("search_test");
    
    // Clean up
    printf("\nCleaning up...\n");
    deleteFile("search_test\\test1.txt");
    deleteFile("search_test\\test2.txt");
    deleteFile("search_test\\data.log");
    deleteFile("search_test\\config.ini");
    removeDirectory("search_test");
    
    printf("File search demo completed.\n\n");
}

void demonstrateFileComparison() {
    printf("=== FILE COMPARISON DEMO ===\n");
    
    // Create test files
    printf("Creating test files...\n");
    createFile("compare_test\\identical1.txt", "Identical content");
    createFile("compare_test\\identical2.txt", "Identical content");
    createFile("compare_test\\different.txt", "Different content");
    
    // Compare identical files
    printf("\nComparing identical files...\n");
    int result = compareFiles("compare_test\\identical1.txt", "compare_test\\identical2.txt");
    printf("Files are %s\n", result == 0 ? "identical" : "different");
    
    // Compare different files
    printf("\nComparing different files...\n");
    result = compareFiles("compare_test\\identical1.txt", "compare_test\\different.txt");
    printf("Files are %s\n", result == 0 ? "identical" : "different");
    
    // Clean up
    printf("\nCleaning up...\n");
    deleteFile("compare_test\\identical1.txt");
    deleteFile("compare_test\\identical2.txt");
    deleteFile("compare_test\\different.txt");
    removeDirectory("compare_test");
    
    printf("File comparison demo completed.\n\n");
}

void demonstrateAdvancedOperations() {
    printf("=== ADVANCED FILE OPERATIONS DEMO ===\n");
    
    // Create test file
    printf("Creating test file...\n");
    createFile("advanced_test\\test.txt", "This is a test file for advanced operations.\n");
    
    // File compression
    printf("\nTesting file compression...\n");
    FileOperationResult result = compressFile("advanced_test\\test.txt");
    if (result.success) {
        printf("File compressed successfully.\n");
        
        // Uncompress
        printf("\nTesting file uncompression...\n");
        result = uncompressFile("advanced_test\\test.txt");
        if (result.success) {
            printf("File uncompressed successfully.\n");
        }
    }
    
    // File encryption (if supported)
    printf("\nTesting file encryption...\n");
    result = encryptFile("advanced_test\\test.txt");
    if (result.success) {
        printf("File encrypted successfully.\n");
        
        // Decrypt
        printf("\nTesting file decryption...\n");
        result = decryptFile("advanced_test\\test.txt");
        if (result.success) {
            printf("File decrypted successfully.\n");
        }
    } else {
        printf("Encryption not supported or failed: %s\n", result.error_message);
    }
    
    // File backup
    printf("\nTesting file backup...\n");
    createDirectory("advanced_test\\backup");
    result = backupFile("advanced_test\\test.txt", "advanced_test\\backup");
    if (result.success) {
        printf("File backed up successfully.\n");
    }
    
    // Clean up
    printf("\nCleaning up...\n");
    deleteFile("advanced_test\\test.txt");
    removeDirectory("advanced_test\\backup");
    removeDirectory("advanced_test");
    
    printf("Advanced file operations demo completed.\n\n");
}

void demonstrateBatchOperations() {
    printf("=== BATCH OPERATIONS DEMO ===\n");
    
    // Create test files
    printf("Creating test files...\n");
    createDirectory("batch_test");
    createFile("batch_test\\file1.txt", "Content 1");
    createFile("batch_test\\file2.txt", "Content 2");
    createFile("batch_test\\file3.txt", "Content 3");
    
    // Batch file info
    printf("\nBatch file information...\n");
    batchProcessFiles("batch_test", "*.txt", printFileInfo);
    
    // Batch delete
    printf("\nBatch file deletion...\n");
    batchProcessFiles("batch_test", "*.txt", deleteFile);
    
    // Clean up
    removeDirectory("batch_test");
    
    printf("Batch operations demo completed.\n\n");
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("File System Operations\n");
    printf("=====================\n\n");
    
    // Run all demonstrations
    demonstrateBasicFileOperations();
    demonstrateDirectoryOperations();
    demonstrateFileSearch();
    demonstrateFileComparison();
    demonstrateAdvancedOperations();
    demonstrateBatchOperations();
    
    printf("All file system operations demonstrated!\n");
    printf("Note: Some operations use Windows-specific APIs.\n");
    printf("For cross-platform development, consider using POSIX functions or libraries like Boost.Filesystem.\n");
    
    return 0;
}
