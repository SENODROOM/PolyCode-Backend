/*
 * File: rsa_encryption.c
 * Description: RSA encryption and digital signatures
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <string.h>
#include <time.h>
#include <math.h>

#define MAX_DIGITS 100
#define BLOCK_SIZE 4

// Big integer structure
typedef struct {
    int digits[MAX_DIGITS];
    int length;
    int sign;
} BigInt;

// RSA key pair structure
typedef struct {
    BigInt n;  // Modulus
    BigInt e;  // Public exponent
    BigInt d;  // Private exponent
    BigInt p;  // Prime p
    BigInt q;  // Prime q
} RSAKeyPair;

// Initialize big integer
void bigint_init(BigInt* num) {
    memset(num->digits, 0, sizeof(num->digits));
    num->length = 0;
    num->sign = 1;
}

// Set big integer from int
void bigint_from_int(BigInt* num, int value) {
    bigint_init(num);
    if (value < 0) {
        num->sign = -1;
        value = -value;
    }
    
    while (value > 0) {
        num->digits[num->length++] = value % 10;
        value /= 10;
    }
    
    if (num->length == 0) {
        num->length = 1;
    }
}

// Set big integer from string
void bigint_from_string(BigInt* num, const char* str) {
    bigint_init(num);
    
    int start = 0;
    if (str[0] == '-') {
        num->sign = -1;
        start = 1;
    }
    
    int len = strlen(str);
    for (int i = start; i < len; i++) {
        if (str[i] >= '0' && str[i] <= '9') {
            num->digits[num->length++] = str[i] - '0';
        }
    }
    
    // Reverse digits to get correct order
    for (int i = 0; i < num->length / 2; i++) {
        int temp = num->digits[i];
        num->digits[i] = num->digits[num->length - 1 - i];
        num->digits[num->length - 1 - i] = temp;
    }
    
    // Remove leading zeros
    while (num->length > 1 && num->digits[num->length - 1] == 0) {
        num->length--;
    }
}

// Compare big integers
int bigint_compare(const BigInt* a, const BigInt* b) {
    if (a->sign != b->sign) {
        return a->sign > b->sign ? 1 : -1;
    }
    
    if (a->length != b->length) {
        return (a->length > b->length) ? a->sign : -a->sign;
    }
    
    for (int i = a->length - 1; i >= 0; i--) {
        if (a->digits[i] != b->digits[i]) {
            return (a->digits[i] > b->digits[i]) ? a->sign : -a->sign;
        }
    }
    
    return 0;
}

// Add big integers
void bigint_add(BigInt* result, const BigInt* a, const BigInt* b) {
    bigint_init(result);
    
    if (a->sign != b->sign) {
        // Different signs, perform subtraction
        if (a->sign > 0) {
            BigInt temp_b = *b;
            temp_b.sign = 1;
            bigint_sub(result, a, &temp_b);
        } else {
            BigInt temp_a = *a;
            temp_a.sign = 1;
            bigint_sub(result, b, &temp_a);
        }
        return;
    }
    
    int carry = 0;
    int max_len = (a->length > b->length) ? a->length : b->length;
    
    for (int i = 0; i < max_len || carry; i++) {
        int digit_a = (i < a->length) ? a->digits[i] : 0;
        int digit_b = (i < b->length) ? b->digits[i] : 0;
        
        int sum = digit_a + digit_b + carry;
        result->digits[i] = sum % 10;
        carry = sum / 10;
        result->length++;
    }
    
    result->sign = a->sign;
}

// Subtract big integers
void bigint_sub(BigInt* result, const BigInt* a, const BigInt* b) {
    bigint_init(result);
    
    if (a->sign != b->sign) {
        // Different signs, perform addition
        BigInt temp_b = *b;
        temp_b.sign = -b->sign;
        bigint_add(result, a, &temp_b);
        return;
    }
    
    int cmp = bigint_compare(a, b);
    if (cmp == 0) {
        bigint_from_int(result, 0);
        return;
    }
    
    if (cmp < 0) {
        // Swap and subtract
        bigint_sub(result, b, a);
        result->sign = -result->sign;
        return;
    }
    
    int borrow = 0;
    for (int i = 0; i < a->length; i++) {
        int digit_a = a->digits[i];
        int digit_b = (i < b->length) ? b->digits[i] : 0;
        
        int diff = digit_a - digit_b - borrow;
        if (diff < 0) {
            diff += 10;
            borrow = 1;
        } else {
            borrow = 0;
        }
        
        result->digits[i] = diff;
        result->length++;
    }
    
    // Remove leading zeros
    while (result->length > 1 && result->digits[result->length - 1] == 0) {
        result->length--;
    }
    
    result->sign = a->sign;
}

// Multiply big integer by small int
void bigint_mul_int(BigInt* result, const BigInt* a, int b) {
    bigint_init(result);
    
    if (b == 0) {
        bigint_from_int(result, 0);
        return;
    }
    
    if (b < 0) {
        bigint_mul_int(result, a, -b);
        result->sign = -a->sign;
        return;
    }
    
    int carry = 0;
    for (int i = 0; i < a->length || carry; i++) {
        int digit_a = (i < a->length) ? a->digits[i] : 0;
        int product = digit_a * b + carry;
        
        result->digits[i] = product % 10;
        carry = product / 10;
        result->length++;
    }
    
    result->sign = a->sign;
}

// Modulo operation (big integer mod small int)
int bigint_mod_int(const BigInt* a, int b) {
    if (b == 0) return 0;
    
    int remainder = 0;
    for (int i = a->length - 1; i >= 0; i--) {
        remainder = (remainder * 10 + a->digits[i]) % b;
    }
    
    return remainder * a->sign;
}

// Exponentiation (big integer ^ small int mod small int)
int bigint_pow_mod_int(const BigInt* base, int exponent, int modulus) {
    if (modulus == 1) return 0;
    
    int result = 1;
    int base_mod = bigint_mod_int(base, modulus);
    
    while (exponent > 0) {
        if (exponent % 2 == 1) {
            result = (result * base_mod) % modulus;
        }
        base_mod = (base_mod * base_mod) % modulus;
        exponent /= 2;
    }
    
    return result;
}

// Print big integer
void bigint_print(const BigInt* num) {
    if (num->sign < 0) {
        printf("-");
    }
    
    for (int i = num->length - 1; i >= 0; i--) {
        printf("%d", num->digits[i]);
    }
}

// Check if number is prime (simple test)
int is_prime(int n) {
    if (n <= 1) return 0;
    if (n <= 3) return 1;
    if (n % 2 == 0 || n % 3 == 0) return 0;
    
    for (int i = 5; i * i <= n; i += 6) {
        if (n % i == 0 || n % (i + 2) == 0) return 0;
    }
    
    return 1;
}

// Generate random prime
int generate_prime(int min, int max) {
    int num;
    do {
        num = min + rand() % (max - min + 1);
    } while (!is_prime(num));
    
    return num;
}

// Calculate greatest common divisor
int gcd(int a, int b) {
    while (b != 0) {
        int temp = b;
        b = a % b;
        a = temp;
    }
    return a;
}

// Calculate modular inverse using extended Euclidean algorithm
int mod_inverse(int a, int m) {
    int m0 = m;
    int y = 0, x = 1;
    
    if (m == 1) return 0;
    
    while (a > 1) {
        int q = a / m;
        int t = m;
        
        m = a % m;
        a = t;
        t = y;
        
        y = x - q * y;
        x = t;
    }
    
    if (x < 0) x += m0;
    
    return x;
}

// Generate RSA key pair
void generate_rsa_keypair(RSAKeyPair* keypair, int bit_size) {
    srand(time(NULL));
    
    // Generate two prime numbers
    int p = generate_prime(1000, 3000);
    int q = generate_prime(3001, 6000);
    
    // Ensure p != q
    while (p == q) {
        q = generate_prime(3001, 6000);
    }
    
    int n = p * q;
    int phi = (p - 1) * (q - 1);
    
    // Choose public exponent (commonly 65537 or a small prime)
    int e = 65537;
    if (gcd(e, phi) != 1) {
        e = 3;
        while (gcd(e, phi) != 1) {
            e += 2;
        }
    }
    
    // Calculate private exponent
    int d = mod_inverse(e, phi);
    
    // Store key pair
    bigint_from_int(&keypair->n, n);
    bigint_from_int(&keypair->e, e);
    bigint_from_int(&keypair->d, d);
    bigint_from_int(&keypair->p, p);
    bigint_from_int(&keypair->q, q);
    
    printf("RSA Key Pair Generated:\n");
    printf("p = %d\n", p);
    printf("q = %d\n", q);
    printf("n = %d\n", n);
    printf("phi = %d\n", phi);
    printf("e = %d\n", e);
    printf("d = %d\n", d);
}

// RSA encryption
void rsa_encrypt(BigInt* ciphertext, const BigInt* plaintext, const BigInt* e, const BigInt* n) {
    int e_int = bigint_mod_int(e, 1000000);
    int n_int = bigint_mod_int(n, 1000000);
    
    int plaintext_int = bigint_mod_int(plaintext, n_int);
    int ciphertext_int = bigint_pow_mod_int(plaintext, e_int, n_int);
    
    bigint_from_int(ciphertext, ciphertext_int);
}

// RSA decryption
void rsa_decrypt(BigInt* plaintext, const BigInt* ciphertext, const BigInt* d, const BigInt* n) {
    int d_int = bigint_mod_int(d, 1000000);
    int n_int = bigint_mod_int(n, 1000000);
    
    int ciphertext_int = bigint_mod_int(ciphertext, n_int);
    int plaintext_int = bigint_pow_mod_int(ciphertext, d_int, n_int);
    
    bigint_from_int(plaintext, plaintext_int);
}

// Convert string to big integer blocks
void string_to_blocks(const char* message, BigInt* blocks, int* block_count) {
    int len = strlen(message);
    *block_count = (len + BLOCK_SIZE - 1) / BLOCK_SIZE;
    
    for (int i = 0; i < *block_count; i++) {
        int block_value = 0;
        for (int j = 0; j < BLOCK_SIZE && i * BLOCK_SIZE + j < len; j++) {
            block_value = block_value * 256 + (unsigned char)message[i * BLOCK_SIZE + j];
        }
        bigint_from_int(&blocks[i], block_value);
    }
}

// Convert big integer blocks to string
void blocks_to_string(const BigInt* blocks, int block_count, char* message) {
    int pos = 0;
    
    for (int i = 0; i < block_count; i++) {
        int block_value = bigint_mod_int(&blocks[i], 1000000000);
        
        // Convert block value back to bytes
        char temp[BLOCK_SIZE];
        int temp_len = 0;
        
        while (block_value > 0 && temp_len < BLOCK_SIZE) {
            temp[temp_len++] = block_value % 256;
            block_value /= 256;
        }
        
        // Reverse and copy to message
        for (int j = temp_len - 1; j >= 0; j--) {
            message[pos++] = temp[j];
        }
    }
    
    message[pos] = '\0';
}

// Digital signature
void rsa_sign(BigInt* signature, const BigInt* message_hash, const BigInt* d, const BigInt* n) {
    rsa_encrypt(signature, message_hash, d, n);
}

// Digital signature verification
int rsa_verify(const BigInt* message_hash, const BigInt* signature, const BigInt* e, const BigInt* n) {
    BigInt decrypted;
    rsa_decrypt(&decrypted, signature, e, n);
    
    return bigint_compare(message_hash, &decrypted) == 0;
}

// Simple hash function (for demonstration)
int simple_hash(const char* message) {
    int hash = 0;
    for (int i = 0; message[i]; i++) {
        hash = (hash * 31 + message[i]) % 1000000;
    }
    return hash;
}

// Test function
void test_rsa() {
    printf("=== RSA Encryption and Digital Signatures ===\n\n");
    
    RSAKeyPair keypair;
    generate_rsa_keypair(&keypair, 1024);
    
    // Test encryption/decryption
    printf("\n1. Encryption/Decryption Test:\n");
    
    const char* message = "Hello, RSA!";
    printf("Original message: %s\n", message);
    
    // Convert message to blocks
    BigInt blocks[10];
    int block_count;
    string_to_blocks(message, blocks, &block_count);
    
    // Encrypt each block
    BigInt encrypted_blocks[10];
    for (int i = 0; i < block_count; i++) {
        rsa_encrypt(&encrypted_blocks[i], &blocks[i], &keypair.e, &keypair.n);
        printf("Block %d: ", i);
        bigint_print(&blocks[i]);
        printf(" -> ");
        bigint_print(&encrypted_blocks[i]);
        printf("\n");
    }
    
    // Decrypt each block
    BigInt decrypted_blocks[10];
    for (int i = 0; i < block_count; i++) {
        rsa_decrypt(&decrypted_blocks[i], &encrypted_blocks[i], &keypair.d, &keypair.n);
    }
    
    // Convert back to string
    char decrypted_message[100];
    blocks_to_string(decrypted_blocks, block_count, decrypted_message);
    printf("Decrypted message: %s\n", decrypted_message);
    
    // Test digital signatures
    printf("\n2. Digital Signature Test:\n");
    
    const char* document = "This is a test document for digital signing.";
    printf("Document: %s\n", document);
    
    // Create hash
    int hash_value = simple_hash(document);
    BigInt message_hash;
    bigint_from_int(&message_hash, hash_value);
    printf("Hash: ");
    bigint_print(&message_hash);
    printf("\n");
    
    // Sign the hash
    BigInt signature;
    rsa_sign(&signature, &message_hash, &keypair.d, &keypair.n);
    printf("Signature: ");
    bigint_print(&signature);
    printf("\n");
    
    // Verify signature
    int is_valid = rsa_verify(&message_hash, &signature, &keypair.e, &keypair.n);
    printf("Signature verification: %s\n", is_valid ? "VALID" : "INVALID");
    
    // Test with modified document
    printf("\n3. Modified Document Test:\n");
    const char* modified_document = "This is a modified test document.";
    printf("Modified document: %s\n", modified_document);
    
    int modified_hash = simple_hash(modified_document);
    BigInt modified_message_hash;
    bigint_from_int(&modified_message_hash, modified_hash);
    printf("Modified hash: ");
    bigint_print(&modified_message_hash);
    printf("\n");
    
    // Try to verify with same signature (should fail)
    int is_modified_valid = rsa_verify(&modified_message_hash, &signature, &keypair.e, &keypair.n);
    printf("Signature verification (modified): %s\n", is_modified_valid ? "VALID" : "INVALID");
    
    // Test with different key sizes
    printf("\n4. Different Key Sizes Test:\n");
    
    RSAKeyPair small_keypair;
    generate_rsa_keypair(&small_keypair, 512);
    
    const char* short_message = "Test";
    BigInt short_blocks[10];
    int short_block_count;
    string_to_blocks(short_message, short_blocks, &short_block_count);
    
    BigInt short_encrypted;
    rsa_encrypt(&short_encrypted, &short_blocks[0], &small_keypair.e, &small_keypair.n);
    
    BigInt short_decrypted;
    rsa_decrypt(&short_decrypted, &short_encrypted, &small_keypair.d, &small_keypair.n);
    
    char short_decrypted_message[100];
    blocks_to_string(&short_decrypted, 1, short_decrypted_message);
    printf("Short message test: %s -> %s\n", short_message, short_decrypted_message);
    
    printf("\n=== RSA testing completed ===\n");
}

int main() {
    test_rsa();
    
    return 0;
}
