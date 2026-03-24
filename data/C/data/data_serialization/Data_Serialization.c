#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdint.h>
#include <time.h>
#include <math.h>

// =============================================================================
// DATA SERIALIZATION FUNDAMENTALS
// =============================================================================

#define MAX_BUFFER_SIZE 4096
#define MAX_FIELDS 100
#define MAX_STRING_LENGTH 256

// Serialization context
typedef struct {
    char buffer[MAX_BUFFER_SIZE];
    size_t position;
    size_t size;
    int error;
} SerializationContext;

// Data types for serialization
typedef enum {
    TYPE_INT,
    TYPE_FLOAT,
    TYPE_DOUBLE,
    TYPE_STRING,
    TYPE_BOOL,
    TYPE_ARRAY,
    TYPE_OBJECT
} DataType;

// Serialization field
typedef struct {
    char name[MAX_STRING_LENGTH];
    DataType type;
    union {
        int int_value;
        float float_value;
        double double_value;
        char string_value[MAX_STRING_LENGTH];
        int bool_value;
        struct {
            void* data;
            int count;
            DataType element_type;
        } array_value;
        struct {
            void* data;
            int field_count;
        } object_value;
    } value;
} SerializationField;

// =============================================================================
// BINARY SERIALIZATION
// =============================================================================

// Initialize serialization context
void initSerializationContext(SerializationContext* ctx) {
    ctx->position = 0;
    ctx->size = 0;
    ctx->error = 0;
    memset(ctx->buffer, 0, MAX_BUFFER_SIZE);
}

// Write integer to buffer
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

// Write float to buffer
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

// Write double to buffer
int writeDouble(SerializationContext* ctx, double value) {
    if (ctx->position + sizeof(double) > MAX_BUFFER_SIZE) {
        ctx->error = 1;
        return 0;
    }
    
    *(double*)(ctx->buffer + ctx->position) = value;
    ctx->position += sizeof(double);
    ctx->size = ctx->position;
    return 1;
}

// Write string to buffer
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

// Write boolean to buffer
int writeBool(SerializationContext* ctx, int value) {
    if (ctx->position + sizeof(int) > MAX_BUFFER_SIZE) {
        ctx->error = 1;
        return 0;
    }
    
    *(int*)(ctx->buffer + ctx->position) = value ? 1 : 0;
    ctx->position += sizeof(int);
    ctx->size = ctx->position;
    return 1;
}

// Read integer from buffer
int readInt(SerializationContext* ctx, int* value) {
    if (ctx->position + sizeof(int) > ctx->size) {
        ctx->error = 1;
        return 0;
    }
    
    *value = *(int*)(ctx->buffer + ctx->position);
    ctx->position += sizeof(int);
    return 1;
}

// Read float from buffer
int readFloat(SerializationContext* ctx, float* value) {
    if (ctx->position + sizeof(float) > ctx->size) {
        ctx->error = 1;
        return 0;
    }
    
    *value = *(float*)(ctx->buffer + ctx->position);
    ctx->position += sizeof(float);
    return 1;
}

// Read double from buffer
int readDouble(SerializationContext* ctx, double* value) {
    if (ctx->position + sizeof(double) > ctx->size) {
        ctx->error = 1;
        return 0;
    }
    
    *value = *(double*)(ctx->buffer + ctx->position);
    ctx->position += sizeof(double);
    return 1;
}

// Read string from buffer
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

// Read boolean from buffer
int readBool(SerializationContext* ctx, int* value) {
    int int_value;
    if (!readInt(ctx, &int_value)) {
        return 0;
    }
    
    *value = int_value != 0;
    return 1;
}

// =============================================================================
// JSON SERIALIZATION
// =============================================================================

// Escape JSON string
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

// Write JSON string
void writeJsonString(FILE* file, const char* str) {
    char escaped[MAX_STRING_LENGTH * 2];
    escapeJsonString(str, escaped, sizeof(escaped));
    fprintf(file, "\"%s\"", escaped);
}

// Serialize field to JSON
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

// Serialize multiple fields to JSON
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

// =============================================================================
// XML SERIALIZATION
// =============================================================================

// Escape XML string
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

// Write XML string
void writeXmlString(FILE* file, const char* str) {
    char escaped[MAX_STRING_LENGTH * 2];
    escapeXmlString(str, escaped, sizeof(escaped));
    fprintf(file, "%s", escaped);
}

// Serialize field to XML
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

// Serialize multiple fields to XML
void serializeToXml(FILE* file, SerializationField* fields, int field_count, const char* root_name) {
    fprintf(file, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    fprintf(file, "<%s>\n", root_name);
    
    for (int i = 0; i < field_count; i++) {
        serializeFieldToXml(file, &fields[i]);
    }
    
    fprintf(file, "</%s>\n", root_name);
}

// =============================================================================
// CSV SERIALIZATION
// =============================================================================

// Escape CSV string
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

// Write CSV string
void writeCsvString(FILE* file, const char* str) {
    char escaped[MAX_STRING_LENGTH * 2];
    escapeCsvString(str, escaped, sizeof(escaped));
    fprintf(file, "%s", escaped);
}

// Serialize field to CSV
void serializeFieldToCsv(FILE* file, SerializationField* field, int is_last) {
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
            writeCsvString(file, field->value.string_value);
            break;
        case TYPE_BOOL:
            fprintf(file, "%s", field->value.bool_value ? "true" : "false");
            break;
        case TYPE_ARRAY:
            fprintf(file, "[array]");
            break;
        case TYPE_OBJECT:
            fprintf(file, "[object]");
            break;
    }
    
    if (!is_last) {
        fprintf(file, ",");
    }
}

// Serialize multiple fields to CSV
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

// =============================================================================
// CUSTOM BINARY PROTOCOL
// =============================================================================

// Protocol header
typedef struct {
    uint32_t magic;
    uint16_t version;
    uint16_t flags;
    uint32_t data_size;
    uint32_t checksum;
} ProtocolHeader;

#define PROTOCOL_MAGIC 0x4D455243 // "MERC"
#define PROTOCOL_VERSION 1

// Calculate simple checksum
uint32_t calculateChecksum(const void* data, size_t size) {
    const uint8_t* bytes = (const uint8_t*)data;
    uint32_t checksum = 0;
    
    for (size_t i = 0; i < size; i++) {
        checksum += bytes[i];
        checksum = (checksum << 1) | (checksum >> 31); // Rotate left
    }
    
    return checksum;
}

// Write protocol header
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

// Read protocol header
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

// =============================================================================
// STRUCT SERIALIZATION
// =============================================================================

// Person structure example
typedef struct {
    char name[50];
    int age;
    float height;
    double weight;
    int is_student;
    char email[100];
} Person;

// Serialize person to binary
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

// Deserialize person from binary
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

// =============================================================================
// ARRAY SERIALIZATION
// =============================================================================

// Serialize integer array
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

// Deserialize integer array
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

// Serialize string array
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

// Deserialize string array
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

// =============================================================================
// COMPRESSION (SIMPLE RUN-LENGTH ENCODING)
// =============================================================================

// Simple run-length encoding
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

// Simple run-length decoding
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

// =============================================================================
// ENCRYPTION (SIMPLE XOR)
// =============================================================================

// Simple XOR encryption
void xorEncrypt(const void* input, void* output, size_t size, uint8_t key) {
    const uint8_t* in = (const uint8_t*)input;
    uint8_t* out = (uint8_t*)output;
    
    for (size_t i = 0; i < size; i++) {
        out[i] = in[i] ^ key;
    }
}

// Simple XOR decryption (same as encryption)
void xorDecrypt(const void* input, void* output, size_t size, uint8_t key) {
    xorEncrypt(input, output, size, key);
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateBinarySerialization() {
    printf("=== BINARY SERIALIZATION DEMO ===\n");
    
    // Create test data
    Person person = {
        .name = "John Doe",
        .age = 30,
        .height = 1.75f,
        .weight = 70.5,
        .is_student = 0,
        .email = "john.doe@example.com"
    };
    
    // Serialize
    SerializationContext ctx;
    initSerializationContext(&ctx);
    
    if (serializePerson(&ctx, &person)) {
        printf("Serialized person data (%zu bytes)\n", ctx.size);
        
        // Print hex dump
        printf("Hex dump: ");
        for (size_t i = 0; i < ctx.size && i < 64; i++) {
            printf("%02X ", (unsigned char)ctx.buffer[i]);
        }
        printf("\n");
        
        // Deserialize
        SerializationContext read_ctx;
        initSerializationContext(&read_ctx);
        memcpy(read_ctx.buffer, ctx.buffer, ctx.size);
        read_ctx.size = ctx.size;
        
        Person deserialized_person;
        if (deserializePerson(&read_ctx, &deserialized_person)) {
            printf("Deserialized person:\n");
            printf("  Name: %s\n", deserialized_person.name);
            printf("  Age: %d\n", deserialized_person.age);
            printf("  Height: %.2f\n", deserialized_person.height);
            printf("  Weight: %.2f\n", deserialized_person.weight);
            printf("  Student: %s\n", deserialized_person.is_student ? "Yes" : "No");
            printf("  Email: %s\n", deserialized_person.email);
        } else {
            printf("Deserialization failed\n");
        }
    } else {
        printf("Serialization failed\n");
    }
    
    printf("\n");
}

void demonstrateJsonSerialization() {
    printf("=== JSON SERIALIZATION DEMO ===\n");
    
    // Create test fields
    SerializationField fields[6] = {
        {.name = "name", .type = TYPE_STRING, .value.string_value = "Alice Smith"},
        {.name = "age", .type = TYPE_INT, .value.int_value = 25},
        {.name = "height", .type = TYPE_FLOAT, .value.float_value = 1.68f},
        {.name = "salary", .type = TYPE_DOUBLE, .value.double_value = 50000.50},
        {.name = "is_employed", .type = TYPE_BOOL, .value.bool_value = 1},
        {.name = "department", .type = TYPE_STRING, .value.string_value = "Engineering"}
    };
    
    // Serialize to JSON
    printf("JSON output:\n");
    serializeToJson(stdout, fields, 6);
    printf("\n\n");
}

void demonstrateXmlSerialization() {
    printf("=== XML SERIALIZATION DEMO ===\n");
    
    // Create test fields
    SerializationField fields[4] = {
        {.name = "product_id", .type = TYPE_INT, .value.int_value = 12345},
        {.name = "product_name", .type = TYPE_STRING, .value.string_value = "Super Widget"},
        {.name = "price", .type = TYPE_DOUBLE, .value.double_value = 99.99},
        {.name = "in_stock", .type = TYPE_BOOL, .value.bool_value = 1}
    };
    
    // Serialize to XML
    printf("XML output:\n");
    serializeToXml(stdout, fields, 4, "product");
    printf("\n");
}

void demonstrateCsvSerialization() {
    printf("=== CSV SERIALIZATION DEMO ===\n");
    
    // Create test fields
    SerializationField fields[5] = {
        {.name = "ID", .type = TYPE_INT, .value.int_value = 1},
        {.name = "Name", .type = TYPE_STRING, .value.string_value = "John Doe"},
        {.name = "Age", .type = TYPE_INT, .value.int_value = 30},
        {.name = "Salary", .type = TYPE_DOUBLE, .value.double_value = 50000.0},
        {.name = "Active", .type = TYPE_BOOL, .value.bool_value = 1}
    };
    
    // Serialize to CSV
    printf("CSV output:\n");
    serializeToCsv(stdout, fields, 5);
    printf("\n");
}

void demonstrateArraySerialization() {
    printf("=== ARRAY SERIALIZATION DEMO ===\n");
    
    // Test integer array
    int numbers[] = {1, 2, 3, 4, 5, 6, 7, 8, 9, 10};
    int count = sizeof(numbers) / sizeof(numbers[0]);
    
    SerializationContext ctx;
    initSerializationContext(&ctx);
    
    if (serializeIntArray(&ctx, numbers, count)) {
        printf("Serialized integer array (%d elements, %zu bytes)\n", count, ctx.size);
        
        // Deserialize
        int* deserialized_numbers;
        int deserialized_count;
        
        SerializationContext read_ctx;
        initSerializationContext(&read_ctx);
        memcpy(read_ctx.buffer, ctx.buffer, ctx.size);
        read_ctx.size = ctx.size;
        
        if (deserializeIntArray(&read_ctx, &deserialized_numbers, &deserialized_count)) {
            printf("Deserialized array: ");
            for (int i = 0; i < deserialized_count; i++) {
                printf("%d ", deserialized_numbers[i]);
            }
            printf("\n");
            free(deserialized_numbers);
        }
    }
    
    // Test string array
    char* strings[] = {"Apple", "Banana", "Cherry", "Date", "Elderberry"};
    count = sizeof(strings) / sizeof(strings[0]);
    
    initSerializationContext(&ctx);
    
    if (serializeStringArray(&ctx, strings, count)) {
        printf("Serialized string array (%d elements, %zu bytes)\n", count, ctx.size);
        
        // Deserialize
        char** deserialized_strings;
        int deserialized_count;
        
        SerializationContext read_ctx;
        initSerializationContext(&read_ctx);
        memcpy(read_ctx.buffer, ctx.buffer, ctx.size);
        read_ctx.size = ctx.size;
        
        if (deserializeStringArray(&read_ctx, &deserialized_strings, &deserialized_count)) {
            printf("Deserialized array: ");
            for (int i = 0; i < deserialized_count; i++) {
                printf("%s ", deserialized_strings[i]);
                free(deserialized_strings[i]);
            }
            printf("\n");
            free(deserialized_strings);
        }
    }
    
    printf("\n");
}

void demonstrateCompression() {
    printf("=== COMPRESSION DEMO ===\n");
    
    // Create test data with repeated patterns
    char input[256];
    for (int i = 0; i < 256; i++) {
        input[i] = (i % 50) ? 'A' : 'B'; // Create pattern
    }
    
    // Compress
    char compressed[512];
    size_t compressed_size = sizeof(compressed);
    
    if (rleCompress(input, 256, compressed, &compressed_size)) {
        printf("Original size: 256 bytes\n");
        printf("Compressed size: %zu bytes\n", compressed_size);
        printf("Compression ratio: %.2f%%\n", (1.0 - (double)compressed_size / 256) * 100);
        
        // Decompress
        char decompressed[256];
        size_t decompressed_size = sizeof(decompressed);
        
        if (rleDecompress(compressed, compressed_size, decompressed, &decompressed_size)) {
            printf("Decompressed size: %zu bytes\n", decompressed_size);
            
            // Verify
            int match = (decompressed_size == 256) && (memcmp(input, decompressed, 256) == 0);
            printf("Verification: %s\n", match ? "Success" : "Failed");
        }
    }
    
    printf("\n");
}

void demonstrateEncryption() {
    printf("=== ENCRYPTION DEMO ===\n");
    
    char message[] = "This is a secret message!";
    uint8_t key = 0x42; // Simple key
    
    printf("Original message: %s\n", message);
    
    // Encrypt
    char encrypted[100];
    xorEncrypt(message, encrypted, strlen(message) + 1, key);
    printf("Encrypted: ");
    for (size_t i = 0; i < strlen(encrypted); i++) {
        printf("%02X ", (unsigned char)encrypted[i]);
    }
    printf("\n");
    
    // Decrypt
    char decrypted[100];
    xorDecrypt(encrypted, decrypted, strlen(encrypted), key);
    printf("Decrypted: %s\n", decrypted);
    
    printf("\n");
}

void demonstrateProtocolSerialization() {
    printf("=== PROTOCOL SERIALIZATION DEMO ===\n");
    
    // Create complex data
    Person people[3] = {
        {.name = "Alice", .age = 25, .height = 1.65f, .weight = 60.0, .is_student = 1, .email = "alice@school.edu"},
        {.name = "Bob", .age = 30, .height = 1.80f, .weight = 80.0, .is_student = 0, .email = "bob@company.com"},
        {.name = "Charlie", .age = 35, .height = 1.75f, .weight = 75.0, .is_student = 0, .email = "charlie@freelance.com"}
    };
    
    // Serialize with protocol
    SerializationContext ctx;
    initSerializationContext(&ctx);
    
    // Write header and data
    if (writeInt(&ctx, 3)) { // Number of people
        for (int i = 0; i < 3; i++) {
            if (!serializePerson(&ctx, &people[i])) {
                printf("Failed to serialize person %d\n", i);
                return;
            }
        }
        
        printf("Protocol serialization successful (%zu bytes)\n", ctx.size);
        
        // Add protocol header
        size_t data_size = ctx.size;
        SerializationContext protocol_ctx;
        initSerializationContext(&protocol_ctx);
        
        // Reserve space for header
        protocol_ctx.position = sizeof(ProtocolHeader);
        
        // Copy data
        memcpy(protocol_ctx.buffer + protocol_ctx.position, ctx.buffer, data_size);
        protocol_ctx.position = protocol_ctx.position + data_size;
        protocol_ctx.size = protocol_ctx.position;
        
        // Write header
        if (writeProtocolHeader(&protocol_ctx, (uint32_t)data_size, 0)) {
            printf("Protocol with header: %zu bytes\n", protocol_ctx.size);
            
            // Print hex dump of header
            printf("Header: ");
            for (int i = 0; i < sizeof(ProtocolHeader); i++) {
                printf("%02X ", (unsigned char)protocol_ctx.buffer[i]);
            }
            printf("\n");
        }
    }
    
    printf("\n");
}

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
    
    printf("\n");
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Data Serialization Examples\n");
    printf("==========================\n\n");
    
    // Run all demonstrations
    demonstrateBinarySerialization();
    demonstrateJsonSerialization();
    demonstrateXmlSerialization();
    demonstrateCsvSerialization();
    demonstrateArraySerialization();
    demonstrateCompression();
    demonstrateEncryption();
    demonstrateProtocolSerialization();
    demonstrateFormatComparison();
    
    printf("All data serialization examples demonstrated!\n");
    printf("Note: These are simplified implementations for educational purposes.\n");
    printf("For production use, consider established libraries like Protocol Buffers, MessagePack, or JSON libraries.\n");
    
    return 0;
}
