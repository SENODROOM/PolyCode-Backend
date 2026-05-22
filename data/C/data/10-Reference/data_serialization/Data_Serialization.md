# Data Serialization

This file contains comprehensive data serialization examples in C, including binary serialization, JSON, XML, CSV, custom protocols, compression, and encryption techniques.

## 📚 Serialization Fundamentals

### 🔄 Serialization Concepts
- **Serialization**: Converting data structures to byte streams
- **Deserialization**: Reconstructing data structures from byte streams
- **Binary Format**: Compact, machine-readable format
- **Text Format**: Human-readable format (JSON, XML, CSV)
- **Protocol**: Custom binary format with headers and validation

### 🎯 Data Types
- **Primitive Types**: int, float, double, bool, string
- **Composite Types**: arrays, objects, structures
- **Variable Length**: Strings, dynamic arrays
- **Fixed Length**: Numbers, fixed-size arrays

## 🔢 Binary Serialization

### Serialization Context
```c
typedef struct {
    char buffer[MAX_BUFFER_SIZE];
    size_t position;
    size_t size;
    int error;
} SerializationContext;
```

### Basic Type Writers
```c
int writeInt(SerializationContext* ctx, int value) {
    if (ctx->position + sizeof(int) > MAX_BUFFER_SIZE) {
        ctx->error = 1;
        return 0;
    }
    
    *(int*)(ctx->buffer + ctx->position) = value;
    ctx->position += sizeof(int);
    ctx->size = ctx->position;
    return 1;
}

int writeFloat(SerializationContext* ctx, float value) {
    if (ctx->position + sizeof(float) > MAX_BUFFER_SIZE) {
        ctx->error = 1;
        return 0;
    }
    
    *(float*)(ctx->buffer + ctx->position) = value;
    ctx->position += sizeof(float);
    ctx->size = ctx->position;
    return 1;
}

int writeString(SerializationContext* ctx, const char* str) {
    size_t len = strlen(str) + 1; // Include null terminator
    
    if (ctx->position + sizeof(int) + len > MAX_BUFFER_SIZE) {
        ctx->error = 1;
        return 0;
    }
    
    // Write length first
    writeInt(ctx, (int)len);
    
    // Write string data
    strcpy(ctx->buffer + ctx->position, str);
    ctx->position += len;
    ctx->size = ctx->position;
    return 1;
}
```

### Basic Type Readers
```c
int readInt(SerializationContext* ctx, int* value) {
    if (ctx->position + sizeof(int) > ctx->size) {
        ctx->error = 1;
        return 0;
    }
    
    *value = *(int*)(ctx->buffer + ctx->position);
    ctx->position += sizeof(int);
    return 1;
}

int readString(SerializationContext* ctx, char* str, size_t max_len) {
    int len;
    if (!readInt(ctx, &len)) {
        return 0;
    }
    
    if (ctx->position + len > ctx->size || len > max_len) {
        ctx->error = 1;
        return 0;
    }
    
    strcpy(str, ctx->buffer + ctx->position);
    ctx->position += len;
    return 1;
}
```

**Binary Serialization Benefits**:
- **Compact**: Minimal storage overhead
- **Fast**: Direct memory operations
- **Type Safety**: Strong typing with fixed sizes
- **Endian Issues**: Need to handle byte order

## 📝 JSON Serialization

### JSON String Escaping
```c
void escapeJsonString(const char* input, char* output, size_t output_size) {
    size_t input_len = strlen(input);
    size_t output_pos = 0;
    
    for (size_t i = 0; i < input_len && output_pos < output_size - 1; i++) {
        char c = input[i];
        
        switch (c) {
            case '"':
                if (output_pos < output_size - 2) {
                    output[output_pos++] = '\\';
                    output[output_pos++] = '"';
                }
                break;
            case '\\':
                if (output_pos < output_size - 2) {
                    output[output_pos++] = '\\';
                    output[output_pos++] = '\\';
                }
                break;
            case '\n':
                if (output_pos < output_size - 2) {
                    output[output_pos++] = '\\';
                    output[output_pos++] = 'n';
                }
                break;
            case '\r':
                if (output_pos < output_size - 2) {
                    output[output_pos++] = '\\';
                    output[output_pos++] = 'r';
                }
                break;
            case '\t':
                if (output_pos < output_size - 2) {
                    output[output_pos++] = '\\';
                    output[output_pos++] = 't';
                }
                break;
            default:
                output[output_pos++] = c;
                break;
        }
    }
    
    output[output_pos] = '\0';
}
```

### JSON Field Serialization
```c
void serializeFieldToJson(FILE* file, SerializationField* field, int is_last) {
    writeJsonString(file, field->name);
    fprintf(file, ": ");
    
    switch (field->type) {
        case TYPE_INT:
            fprintf(file, "%d", field->value.int_value);
            break;
        case TYPE_FLOAT:
            fprintf(file, "%.6f", field->value.float_value);
            break;
        case TYPE_DOUBLE:
            fprintf(file, "%.15f", field->value.double_value);
            break;
        case TYPE_STRING:
            writeJsonString(file, field->value.string_value);
            break;
        case TYPE_BOOL:
            fprintf(file, "%s", field->value.bool_value ? "true" : "false");
            break;
        case TYPE_ARRAY:
            fprintf(file, "[");
            // Array serialization would go here
            fprintf(file, "]");
            break;
        case TYPE_OBJECT:
            fprintf(file, "{");
            // Object serialization would go here
            fprintf(file, "}");
            break;
    }
    
    if (!is_last) {
        fprintf(file, ",");
    }
}
```

### Complete JSON Serialization
```c
void serializeToJson(FILE* file, SerializationField* fields, int field_count) {
    fprintf(file, "{\n");
    
    for (int i = 0; i < field_count; i++) {
        fprintf(file, "  ");
        serializeFieldToJson(file, &fields[i], i == field_count - 1);
        if (i < field_count - 1) {
            fprintf(file, "\n");
        }
    }
    
    fprintf(file, "\n}");
}
```

**JSON Benefits**:
- **Human Readable**: Easy to inspect and debug
- **Language Agnostic**: Supported by most languages
- **Web Standard**: Native support in browsers
- **Verbose**: Larger than binary formats

## 📄 XML Serialization

### XML String Escaping
```c
void escapeXmlString(const char* input, char* output, size_t output_size) {
    size_t input_len = strlen(input);
    size_t output_pos = 0;
    
    for (size_t i = 0; i < input_len && output_pos < output_size - 1; i++) {
        char c = input[i];
        
        switch (c) {
            case '<':
                if (output_pos < output_size - 4) {
                    strcpy(output + output_pos, "&lt;");
                    output_pos += 4;
                }
                break;
            case '>':
                if (output_pos < output_size - 4) {
                    strcpy(output + output_pos, "&gt;");
                    output_pos += 4;
                }
                break;
            case '&':
                if (output_pos < output_size - 5) {
                    strcpy(output + output_pos, "&amp;");
                    output_pos += 5;
                }
                break;
            case '"':
                if (output_pos < output_size - 6) {
                    strcpy(output + output_pos, "&quot;");
                    output_pos += 6;
                }
                break;
            case '\'':
                if (output_pos < output_size - 6) {
                    strcpy(output + output_pos, "&apos;");
                    output_pos += 6;
                }
                break;
            default:
                output[output_pos++] = c;
                break;
        }
    }
    
    output[output_pos] = '\0';
}
```

### XML Field Serialization
```c
void serializeFieldToXml(FILE* file, SerializationField* field) {
    fprintf(file, "  <%s>", field->name);
    
    switch (field->type) {
        case TYPE_INT:
            fprintf(file, "%d", field->value.int_value);
            break;
        case TYPE_FLOAT:
            fprintf(file, "%.6f", field->value.float_value);
            break;
        case TYPE_DOUBLE:
            fprintf(file, "%.15f", field->value.double_value);
            break;
        case TYPE_STRING:
            writeXmlString(file, field->value.string_value);
            break;
        case TYPE_BOOL:
            fprintf(file, "%s", field->value.bool_value ? "true" : "false");
            break;
        case TYPE_ARRAY:
            // Array serialization would go here
            break;
        case TYPE_OBJECT:
            // Object serialization would go here
            break;
    }
    
    fprintf(file, "</%s>\n", field->name);
}
```

### Complete XML Serialization
```c
void serializeToXml(FILE* file, SerializationField* fields, int field_count, const char* root_name) {
    fprintf(file, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    fprintf(file, "<%s>\n", root_name);
    
    for (int i = 0; i < field_count; i++) {
        serializeFieldToXml(file, &fields[i]);
    }
    
    fprintf(file, "</%s>\n", root_name);
}
```

**XML Benefits**:
- **Structured**: Hierarchical data organization
- **Extensible**: Schema validation possible
- **Self-Documenting**: Tags describe data
- **Verbose**: Most verbose format

## 📊 CSV Serialization

### CSV String Escaping
```c
void escapeCsvString(const char* input, char* output, size_t output_size) {
    size_t input_len = strlen(input);
    int needs_quotes = 0;
    
    // Check if string needs quotes
    for (size_t i = 0; i < input_len; i++) {
        if (input[i] == ',' || input[i] == '"' || input[i] == '\n' || input[i] == '\r') {
            needs_quotes = 1;
            break;
        }
    }
    
    size_t output_pos = 0;
    
    if (needs_quotes) {
        if (output_pos < output_size - 1) {
            output[output_pos++] = '"';
        }
    }
    
    for (size_t i = 0; i < input_len && output_pos < output_size - 1; i++) {
        char c = input[i];
        
        if (c == '"') {
            if (output_pos < output_size - 2) {
                output[output_pos++] = '"';
                output[output_pos++] = '"';
            }
        } else {
            output[output_pos++] = c;
        }
    }
    
    if (needs_quotes) {
        if (output_pos < output_size - 1) {
            output[output_pos++] = '"';
        }
    }
    
    output[output_pos] = '\0';
}
```

### CSV Serialization
```c
void serializeToCsv(FILE* file, SerializationField* fields, int field_count) {
    // Write header
    for (int i = 0; i < field_count; i++) {
        writeCsvString(file, fields[i].name);
        if (i < field_count - 1) {
            fprintf(file, ",");
        }
    }
    fprintf(file, "\n");
    
    // Write data
    for (int i = 0; i < field_count; i++) {
        serializeFieldToCsv(file, &fields[i], i == field_count - 1);
    }
    fprintf(file, "\n");
}
```

**CSV Benefits**:
- **Simple**: Easy to parse and generate
- **Tabular**: Perfect for spreadsheet data
- **Compact**: Smaller than JSON/XML
- **Limited**: No nested structures

## 🔧 Custom Binary Protocol

### Protocol Header
```c
typedef struct {
    uint32_t magic;
    uint16_t version;
    uint16_t flags;
    uint32_t data_size;
    uint32_t checksum;
} ProtocolHeader;

#define PROTOCOL_MAGIC 0x4D455243 // "MERC"
#define PROTOCOL_VERSION 1
```

### Checksum Calculation
```c
uint32_t calculateChecksum(const void* data, size_t size) {
    const uint8_t* bytes = (const uint8_t*)data;
    uint32_t checksum = 0;
    
    for (size_t i = 0; i < size; i++) {
        checksum += bytes[i];
        checksum = (checksum << 1) | (checksum >> 31); // Rotate left
    }
    
    return checksum;
}
```

### Protocol Header Writing
```c
int writeProtocolHeader(SerializationContext* ctx, uint32_t data_size, uint16_t flags) {
    ProtocolHeader header;
    header.magic = PROTOCOL_MAGIC;
    header.version = PROTOCOL_VERSION;
    header.flags = flags;
    header.data_size = data_size;
    
    // Calculate checksum of data that will follow
    header.checksum = calculateChecksum(ctx->buffer + ctx->position, data_size);
    
    // Write header at beginning of buffer
    size_t original_pos = ctx->position;
    ctx->position = 0;
    
    writeInt(ctx, (int)header.magic);
    writeInt(ctx, (int)((header.version << 16) | header.flags));
    writeInt(ctx, (int)header.data_size);
    writeInt(ctx, (int)header.checksum);
    
    ctx->position = original_pos;
    ctx->size += sizeof(ProtocolHeader);
    
    return 1;
}
```

### Protocol Header Reading
```c
int readProtocolHeader(SerializationContext* ctx, ProtocolHeader* header) {
    size_t original_pos = ctx->position;
    ctx->position = 0;
    
    int magic, version_flags, data_size, checksum;
    if (!readInt(ctx, &magic) || !readInt(ctx, &version_flags) || 
        !readInt(ctx, &data_size) || !readInt(ctx, &checksum)) {
        ctx->position = original_pos;
        return 0;
    }
    
    header->magic = (uint32_t)magic;
    header->version = (uint16_t)(version_flags >> 16);
    header->flags = (uint16_t)(version_flags & 0xFFFF);
    header->data_size = (uint32_t)data_size;
    header->checksum = (uint32_t)checksum;
    
    // Validate magic number
    if (header->magic != PROTOCOL_MAGIC) {
        ctx->position = original_pos;
        return 0;
    }
    
    // Validate version
    if (header->version != PROTOCOL_VERSION) {
        ctx->position = original_pos;
        return 0;
    }
    
    ctx->position = original_pos;
    return 1;
}
```

**Custom Protocol Benefits**:
- **Optimized**: Tailored to specific needs
- **Validation**: Built-in error checking
- **Versioning**: Protocol evolution support
- **Efficiency**: Minimal overhead

## 📚 Array Serialization

### Integer Array Serialization
```c
int serializeIntArray(SerializationContext* ctx, int* array, int count) {
    if (!writeInt(ctx, count)) {
        return 0;
    }
    
    for (int i = 0; i < count; i++) {
        if (!writeInt(ctx, array[i])) {
            return 0;
        }
    }
    
    return 1;
}

int deserializeIntArray(SerializationContext* ctx, int** array, int* count) {
    if (!readInt(ctx, count)) {
        return 0;
    }
    
    *array = malloc(*count * sizeof(int));
    if (!*array) {
        return 0;
    }
    
    for (int i = 0; i < *count; i++) {
        if (!readInt(ctx, &(*array)[i])) {
            free(*array);
            *array = NULL;
            return 0;
        }
    }
    
    return 1;
}
```

### String Array Serialization
```c
int serializeStringArray(SerializationContext* ctx, char** array, int count) {
    if (!writeInt(ctx, count)) {
        return 0;
    }
    
    for (int i = 0; i < count; i++) {
        if (!writeString(ctx, array[i])) {
            return 0;
        }
    }
    
    return 1;
}

int deserializeStringArray(SerializationContext* ctx, char*** array, int* count) {
    if (!readInt(ctx, count)) {
        return 0;
    }
    
    *array = malloc(*count * sizeof(char*));
    if (!*array) {
        return 0;
    }
    
    for (int i = 0; i < *count; i++) {
        (*array)[i] = malloc(MAX_STRING_LENGTH);
        if (!(*array)[i]) {
            for (int j = 0; j < i; j++) {
                free((*array)[j]);
            }
            free(*array);
            *array = NULL;
            return 0;
        }
        
        if (!readString(ctx, (*array)[i], MAX_STRING_LENGTH)) {
            for (int j = 0; j <= i; j++) {
                free((*array)[j]);
            }
            free(*array);
            *array = NULL;
            return 0;
        }
    }
    
    return 1;
}
```

## 🗜️ Compression

### Run-Length Encoding
```c
int rleCompress(const void* input, size_t input_size, void* output, size_t* output_size) {
    const uint8_t* in = (const uint8_t*)input;
    uint8_t* out = (uint8_t*)output;
    size_t out_pos = 0;
    
    for (size_t i = 0; i < input_size && out_pos < *output_size; ) {
        uint8_t current = in[i];
        size_t count = 1;
        
        // Count consecutive identical bytes
        while (i + count < input_size && in[i + count] == current && count < 255) {
            count++;
        }
        
        if (count > 3 || current == 0) {
            // Write run-length encoded data
            if (out_pos + 2 > *output_size) return 0;
            out[out_pos++] = 0; // Escape byte
            out[out_pos++] = (uint8_t)count;
        } else {
            // Write raw data
            for (size_t j = 0; j < count && out_pos < *output_size; j++) {
                out[out_pos++] = current;
            }
        }
        
        i += count;
    }
    
    *output_size = out_pos;
    return 1;
}

int rleDecompress(const void* input, size_t input_size, void* output, size_t* output_size) {
    const uint8_t* in = (const uint8_t*)input;
    uint8_t* out = (uint8_t*)output;
    size_t out_pos = 0;
    
    for (size_t i = 0; i < input_size && out_pos < *output_size; ) {
        if (in[i] == 0 && i + 1 < input_size) {
            // Run-length encoded data
            uint8_t count = in[i + 1];
            uint8_t value = (i + 2 < input_size) ? in[i + 2] : 0;
            
            for (uint8_t j = 0; j < count && out_pos < *output_size; j++) {
                out[out_pos++] = value;
            }
            
            i += 3;
        } else {
            // Raw data
            out[out_pos++] = in[i++];
        }
    }
    
    *output_size = out_pos;
    return 1;
}
```

**Compression Benefits**:
- **Space Savings**: Reduced storage requirements
- **Faster Transfer**: Less data to send
- **CPU Overhead**: Compression/decompression cost
- **Algorithm Choice**: Different data needs different algorithms

## 🔐 Encryption

### Simple XOR Encryption
```c
void xorEncrypt(const void* input, void* output, size_t size, uint8_t key) {
    const uint8_t* in = (const uint8_t*)input;
    uint8_t* out = (uint8_t*)output;
    
    for (size_t i = 0; i < size; i++) {
        out[i] = in[i] ^ key;
    }
}

void xorDecrypt(const void* input, void* output, size_t size, uint8_t key) {
    xorEncrypt(input, output, size, key);
}
```

**Encryption Benefits**:
- **Security**: Data protection
- **Privacy**: Confidential information
- **Integrity**: Tamper detection
- **Performance**: Trade-off between security and speed

## 🏗️ Structure Serialization

### Person Structure Example
```c
typedef struct {
    char name[50];
    int age;
    float height;
    double weight;
    int is_student;
    char email[100];
} Person;
```

### Person Serialization
```c
int serializePerson(SerializationContext* ctx, Person* person) {
    // Reserve space for header (will be written later)
    size_t data_start = ctx->position + sizeof(ProtocolHeader);
    ctx->position = data_start;
    
    // Serialize person data
    if (!writeString(ctx, person->name) ||
        !writeInt(ctx, person->age) ||
        !writeFloat(ctx, person->height) ||
        !writeDouble(ctx, person->weight) ||
        !writeBool(ctx, person->is_student) ||
        !writeString(ctx, person->email)) {
        return 0;
    }
    
    // Write header
    size_t data_size = ctx->position - data_start;
    ctx->position = 0;
    if (!writeProtocolHeader(ctx, (uint32_t)data_size, 0)) {
        return 0;
    }
    
    ctx->position = ctx->size;
    return 1;
}

int deserializePerson(SerializationContext* ctx, Person* person) {
    ProtocolHeader header;
    if (!readProtocolHeader(ctx, &header)) {
        return 0;
    }
    
    // Read person data
    if (!readString(ctx, person->name, sizeof(person->name)) ||
        !readInt(ctx, &person->age) ||
        !readFloat(ctx, &person->height) ||
        !readDouble(ctx, &person->weight) ||
        !readBool(ctx, &person->is_student) ||
        !readString(ctx, person->email, sizeof(person->email))) {
        return 0;
    }
    
    return 1;
}
```

## 📊 Format Comparison

### Size Comparison
```c
void demonstrateFormatComparison() {
    printf("=== FORMAT COMPARISON DEMO ===\n");
    
    // Create test data
    SerializationField fields[3] = {
        {.name = "id", .type = TYPE_INT, .value.int_value = 123},
        {.name = "name", .type = TYPE_STRING, .value.string_value = "Test Object"},
        {.name = "value", .type = TYPE_DOUBLE, .value.double_value = 456.789}
    };
    
    printf("Data size comparison:\n");
    
    // Binary size
    SerializationContext binary_ctx;
    initSerializationContext(&binary_ctx);
    writeInt(&binary_ctx, fields[0].value.int_value);
    writeString(&binary_ctx, fields[1].value.string_value);
    writeDouble(&binary_ctx, fields[2].value.double_value);
    printf("Binary: %zu bytes\n", binary_ctx.size);
    
    // JSON size (approximate)
    printf("JSON: ~%zu bytes (estimated)\n", strlen("{\"id\":123,\"name\":\"Test Object\",\"value\":456.789000000000}"));
    
    // XML size (approximate)
    printf("XML: ~%zu bytes (estimated)\n", strlen("<id>123</id><name>Test Object</name><value>456.789000000000</value>") + strlen("<root></root>") + strlen("<?xml version=\"1.0\" encoding=\"UTF-8\"?>"));
    
    // CSV size (approximate)
    printf("CSV: ~%zu bytes (estimated)\n", strlen("id,name,value\n123,Test Object,456.789"));
}
```

### Format Characteristics
| Format | Size | Readability | Performance | Features |
|--------|------|-------------|------------|----------|
| Binary | Small | Low | Fast | Limited |
| JSON | Medium | High | Medium | Rich |
| XML | Large | High | Slow | Very Rich |
| CSV | Small | Medium | Fast | Simple |

## ⚠️ Common Pitfalls

### 1. Buffer Overflow
```c
// Wrong - No bounds checking
void unsafeSerialization() {
    char buffer[100];
    SerializationContext ctx;
    ctx.buffer = buffer;
    ctx.size = 100;
    
    // Could overflow buffer
    writeString(&ctx, "This string is too long for the buffer");
}

// Right - Check bounds
void safeSerialization() {
    SerializationContext ctx;
    initSerializationContext(&ctx);
    
    if (!writeString(&ctx, "This string is too long for the buffer")) {
        printf("Serialization failed: buffer too small\n");
    }
}
```

### 2. Endianness Issues
```c
// Wrong - Platform dependent
void unsafeIntSerialization(SerializationContext* ctx, int value) {
    *(int*)(ctx->buffer + ctx->position) = value;
    ctx->position += sizeof(int);
}

// Right - Handle endianness
void safeIntSerialization(SerializationContext* ctx, int value) {
    // Convert to network byte order (big-endian)
    uint32_t network_value = htonl(value);
    memcpy(ctx->buffer + ctx->position, &network_value, sizeof(uint32_t));
    ctx->position += sizeof(int);
}
```

### 3. String Encoding Issues
```c
// Wrong - No character encoding handling
void unsafeStringSerialization(SerializationContext* ctx, const char* str) {
    strcpy(ctx->buffer + ctx->position, str);
    ctx->position += strlen(str) + 1;
}

// Right - Handle UTF-8 and special characters
void safeStringSerialization(SerializationContext* ctx, const char* str) {
    // Convert to UTF-8 if necessary
    // Escape special characters for target format
    writeString(ctx, str);
}
```

### 4. Version Compatibility
```c
// Wrong - No version checking
void unsafeDeserialization(SerializationContext* ctx) {
    // Assume data is always in current format
    readData(ctx);
}

// Right - Check version
void safeDeserialization(SerializationContext* ctx) {
    ProtocolHeader header;
    if (!readProtocolHeader(ctx, &header)) {
        printf("Invalid protocol header\n");
        return;
    }
    
    if (header.version != PROTOCOL_VERSION) {
        printf("Unsupported protocol version: %d\n", header.version);
        return;
    }
    
    readData(ctx);
}
```

### 5. Memory Management
```c
// Wrong - Memory leaks
void unsafeArrayDeserialization(SerializationContext* ctx) {
    int* array;
    int count;
    deserializeIntArray(ctx, &array, &count);
    // Forgot to free array!
}

// Right - Proper cleanup
void safeArrayDeserialization(SerializationContext* ctx) {
    int* array;
    int count;
    if (deserializeIntArray(ctx, &array, &count)) {
        // Use array
        free(array);
    }
}
```

## 🔧 Best Practices

### 1. Error Handling
```c
int robustSerialization(SerializationContext* ctx, const char* data) {
    if (!ctx || !data) {
        return 0; // Invalid parameters
    }
    
    if (!writeString(ctx, data)) {
        printf("Serialization failed: %s\n", ctx->error ? "Buffer overflow" : "Unknown error");
        return 0;
    }
    
    return 1;
}
```

### 2. Validation
```c
int validateSerializedData(SerializationContext* ctx) {
    if (ctx->size == 0) {
        return 0; // Empty data
    }
    
    if (ctx->position > ctx->size) {
        return 0; // Invalid position
    }
    
    return 1; // Valid data
}
```

### 3. Backward Compatibility
```c
void serializeWithVersion(SerializationContext* ctx, int version) {
    writeInt(ctx, PROTOCOL_VERSION);
    writeInt(ctx, version);
    
    // Serialize data based on version
    if (version >= 2) {
        writeNewField(ctx);
    }
    
    writeOldField(ctx);
}
```

### 4. Performance Optimization
```c
void optimizedSerialization(SerializationContext* ctx, LargeData* data) {
    // Use memory pooling for frequent allocations
    MemoryPool pool;
    initMemoryPool(&pool, 1024 * 1024);
    
    // Serialize in chunks for large data
    size_t chunk_size = 4096;
    for (size_t i = 0; i < data->size; i += chunk_size) {
        size_t current_size = (i + chunk_size > data->size) ? 
                              data->size - i : chunk_size;
        
        serializeChunk(ctx, data->data + i, current_size);
    }
    
    cleanupMemoryPool(&pool);
}
```

### 5. Security Considerations
```c
void secureSerialization(SerializationContext* ctx, const char* sensitive_data) {
    // Encrypt sensitive data
    char encrypted_data[MAX_STRING_LENGTH];
    xorEncrypt(sensitive_data, encrypted_data, strlen(sensitive_data), 0x42);
    
    // Serialize encrypted data
    writeString(ctx, encrypted_data);
    
    // Clear sensitive data from memory
    memset(encrypted_data, 0, sizeof(encrypted_data));
}
```

## 🔧 Real-World Applications

### 1. Network Protocol
```c
void serializeNetworkPacket(SerializationContext* ctx, NetworkPacket* packet) {
    // Add protocol header
    writeProtocolHeader(ctx, packet->data_size, packet->flags);
    
    // Add packet data
    writeInt(ctx, packet->packet_id);
    writeInt(ctx, packet->timestamp);
    writeString(ctx, packet->source);
    
    // Add payload
    memcpy(ctx->buffer + ctx->position, packet->payload, packet->data_size);
    ctx->position += packet->data_size;
    ctx->size = ctx->position;
}
```

### 2. File Format
```c
void serializeDocument(SerializationContext* ctx, Document* doc) {
    // Write file header
    writeInt(ctx, doc->version);
    writeInt(ctx, doc->page_count);
    
    // Write metadata
    writeString(ctx, doc->title);
    writeString(ctx, doc->author);
    writeDouble(ctx, doc->creation_time);
    
    // Write content
    for (int i = 0; i < doc->page_count; i++) {
        serializePage(ctx, &doc->pages[i]);
    }
}
```

### 3. Database Record
```c
void serializeDatabaseRecord(SerializationContext* ctx, DatabaseRecord* record) {
    // Write record header
    writeInt(ctx, record->record_id);
    writeInt(ctx, record->table_id);
    
    // Write fields
    for (int i = 0; i < record->field_count; i++) {
        serializeField(ctx, &record->fields[i]);
    }
    
    // Write indexes
    for (int i = 0; i < record->index_count; i++) {
        serializeIndex(ctx, &record->indexes[i]);
    }
}
```

### 4. Configuration File
```c
void serializeConfiguration(FILE* file, Config* config) {
    // Use JSON for human-readable config
    SerializationField fields[10];
    
    // Create fields from config
    strcpy(fields[0].name, "database_url");
    fields[0].type = TYPE_STRING;
    strcpy(fields[0].value.string_value, config->database_url);
    
    strcpy(fields[1].name, "max_connections");
    fields[1].type = TYPE_INT;
    fields[1].value.int_value = config->max_connections;
    
    // Serialize to JSON
    serializeToJson(file, fields, 2);
}
```

## 📚 Cross-Platform Considerations

### Endianness Handling
```c
uint32_t toNetworkOrder(uint32_t value) {
    return htonl(value);
}

uint32_t fromNetworkOrder(uint32_t value) {
    return ntohl(value);
}

void portableIntSerialization(SerializationContext* ctx, int value) {
    uint32_t network_value = toNetworkOrder((uint32_t)value);
    memcpy(ctx->buffer + ctx->position, &network_value, sizeof(uint32_t));
    ctx->position += sizeof(int);
}
```

### Platform-Specific Types
```c
#ifdef _WIN32
    typedef int int32_t;
#else
    #include <stdint.h>
    typedef int32_t int32_t;
#endif

void portableTypeSerialization(SerializationContext* ctx, int32_t value) {
    writeInt(ctx, (int)value);
}
```

### Library Options
```c
// Use established serialization libraries
// - Protocol Buffers (Google)
// - MessagePack (Binary JSON)
// - FlatBuffers (Zero-copy)
// - Cap'n Proto (RPC)
// - BSON (MongoDB)

void useProtocolBuffers() {
    // Would use Protocol Buffers C++ API
    // This is just a placeholder
    printf("Use Protocol Buffers for production serialization\n");
}
```

## 🎓 Learning Path

### 1. Basic Serialization
- Binary format fundamentals
- Primitive type serialization
- String handling
- Error checking

### 2. Text Formats
- JSON serialization and parsing
- XML serialization and parsing
- CSV handling
- Escaping and encoding

### 3. Advanced Topics
- Custom protocols
- Versioning and compatibility
- Compression and encryption
- Performance optimization

### 4. Real-World Applications
- Network protocols
- File formats
- Database serialization
- Configuration management

### 5. Production Considerations
- Cross-platform compatibility
- Security and validation
- Performance profiling
- Library selection

## 📚 Further Reading

### Books
- "The Art of Computer Programming" by Donald Knuth
- "Data Compression: The Complete Reference" by David Salomon
- "Network Programming with Sockets" by Warren Gay

### Topics
- Serialization algorithms
- Data compression techniques
- Cryptographic protocols
- Network programming
- Data format design

Data serialization in C provides essential skills for data persistence, network communication, and system integration. Master these concepts to build robust, efficient applications that handle data effectively across different platforms and systems!
