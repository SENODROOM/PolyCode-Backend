#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <math.h>

// =============================================================================
// COMPILER AND INTERPRETER FUNDAMENTALS
// =============================================================================

#define MAX_TOKENS 1000
#define MAX_SYMBOLS 100
#define MAX_CODE_SIZE 10000
#define MAX_STRING_SIZE 256

// Token types
typedef enum {
    TOKEN_KEYWORD,
    TOKEN_IDENTIFIER,
    TOKEN_NUMBER,
    TOKEN_STRING,
    TOKEN_OPERATOR,
    TOKEN_DELIMITER,
    TOKEN_ASSIGN,
    TOKEN_LPAREN,
    TOKEN_RPAREN,
    TOKEN_LBRACE,
    TOKEN_RBRACE,
    TOKEN_SEMICOLON,
    TOKEN_EOF,
    TOKEN_ERROR
} TokenType;

// Token structure
typedef struct {
    TokenType type;
    char value[MAX_STRING_SIZE];
    int line;
    int column;
} Token;

// Symbol types
typedef enum {
    SYMBOL_VARIABLE,
    SYMBOL_FUNCTION,
    SYMBOL_CONSTANT
} SymbolType;

// Symbol structure
typedef struct {
    char name[MAX_STRING_SIZE];
    SymbolType type;
    int value;
    int is_initialized;
} Symbol;

// AST Node types
typedef enum {
    AST_NUMBER,
    AST_VARIABLE,
    AST_BINARY_OP,
    AST_UNARY_OP,
    AST_ASSIGN,
    AST_FUNCTION_CALL,
    AST_BLOCK,
    AST_IF,
    AST_WHILE,
    AST_FOR,
    AST_RETURN,
    AST_PROGRAM
} ASTNodeType;

// AST Node structure
typedef struct ASTNode {
    ASTNodeType type;
    union {
        int number;
        char variable[MAX_STRING_SIZE];
        struct {
            char op;
            struct ASTNode* left;
            struct ASTNode* right;
        } binary_op;
        struct {
            char op;
            struct ASTNode* operand;
        } unary_op;
        struct {
            char variable[MAX_STRING_SIZE];
            struct ASTNode* expression;
        } assignment;
        struct {
            char function[MAX_STRING_SIZE];
            struct ASTNode* args[10];
            int arg_count;
        } function_call;
        struct {
            struct ASTNode* condition;
            struct ASTNode* then_block;
            struct ASTNode* else_block;
        } if_statement;
        struct {
            struct ASTNode* condition;
            struct ASTNode* body;
        } while_statement;
        struct {
            struct ASTNode* init;
            struct ASTNode* condition;
            struct ASTNode* increment;
            struct ASTNode* body;
        } for_statement;
        struct {
            struct ASTNode* expression;
        } return_statement;
        struct {
            struct ASTNode* statements[MAX_CODE_SIZE];
            int statement_count;
        } block;
        struct {
            struct ASTNode* statements[MAX_CODE_SIZE];
            int statement_count;
        } program;
    } data;
    struct ASTNode* parent;
} ASTNode;

// Virtual Machine structure
typedef struct {
    int stack[MAX_CODE_SIZE];
    int stack_top;
    int registers[10];
    Symbol symbols[MAX_SYMBOLS];
    int symbol_count;
    int running;
    int error;
} VirtualMachine;

VirtualMachine vm;

// =============================================================================
// LEXER (TOKENIZER)
// =============================================================================

// Keywords
const char* keywords[] = {
    "if", "else", "while", "for", "function", "return", "var", "const", "true", "false", "null"
};

// Operators
const char* operators[] = {
    "+", "-", "*", "/", "%", "==", "!=", "<", "<=", ">", ">=", "&&", "||", "!"
};

// Check if a string is a keyword
int isKeyword(const char* str) {
    for (int i = 0; i < sizeof(keywords) / sizeof(keywords[0]); i++) {
        if (strcmp(str, keywords[i]) == 0) {
            return 1;
        }
    }
    return 0;
}

// Check if a character is an operator
int isOperator(char c) {
    return c == '+' || c == '-' || c == '*' || c == '/' || c == '%' ||
           c == '=' || c == '!' || c == '<' || c == '>' || c == '&' || c == '|';
}

// Check if a string is an operator
int isOperatorString(const char* str) {
    for (int i = 0; i < sizeof(operators) / sizeof(operators[0]); i++) {
        if (strcmp(str, operators[i]) == 0) {
            return 1;
        }
    }
    return 0;
}

// Tokenize source code
int tokenize(const char* source, Token* tokens) {
    int token_count = 0;
    int line = 1;
    int column = 1;
    int i = 0;
    
    while (source[i] && token_count < MAX_TOKENS) {
        // Skip whitespace
        while (source[i] && isspace(source[i])) {
            if (source[i] == '\n') {
                line++;
                column = 1;
            } else {
                column++;
            }
            i++;
        }
        
        if (!source[i]) break;
        
        // Check for numbers
        if (isdigit(source[i])) {
            int start = i;
            while (source[i] && isdigit(source[i])) {
                i++;
            }
            
            int length = i - start;
            if (length < MAX_STRING_SIZE) {
                strncpy(tokens[token_count].value, source + start, length);
                tokens[token_count].value[length] = '\0';
                tokens[token_count].type = TOKEN_NUMBER;
                tokens[token_count].line = line;
                tokens[token_count].column = column;
                token_count++;
                column += length;
            }
        }
        // Check for identifiers and keywords
        else if (isalpha(source[i]) || source[i] == '_') {
            int start = i;
            while (source[i] && (isalnum(source[i]) || source[i] == '_')) {
                i++;
            }
            
            int length = i - start;
            if (length < MAX_STRING_SIZE) {
                strncpy(tokens[token_count].value, source + start, length);
                tokens[token_count].value[length] = '\0';
                tokens[token_count].type = isKeyword(tokens[token_count].value) ? TOKEN_KEYWORD : TOKEN_IDENTIFIER;
                tokens[token_count].line = line;
                tokens[token_count].column = column;
                token_count++;
                column += length;
            }
        }
        // Check for strings
        else if (source[i] == '"') {
            i++; // Skip opening quote
            int start = i;
            while (source[i] && source[i] != '"') {
                if (source[i] == '\\') i++; // Skip escaped character
                if (source[i]) i++;
            }
            
            int length = i - start;
            if (length < MAX_STRING_SIZE) {
                strncpy(tokens[token_count].value, source + start, length);
                tokens[token_count].value[length] = '\0';
                tokens[token_count].type = TOKEN_STRING;
                tokens[token_count].line = line;
                tokens[token_count].column = column;
                token_count++;
                column += length + 2; // +2 for quotes
            }
            i++; // Skip closing quote
        }
        // Check for operators
        else if (isOperator(source[i])) {
            int start = i;
            while (source[i] && isOperator(source[i])) {
                i++;
            }
            
            int length = i - start;
            if (length < MAX_STRING_SIZE) {
                strncpy(tokens[token_count].value, source + start, length);
                tokens[token_count].value[length] = '\0';
                tokens[token_count].type = isOperatorString(tokens[token_count].value) ? TOKEN_OPERATOR : TOKEN_ERROR;
                tokens[token_count].line = line;
                tokens[token_count].column = column;
                token_count++;
                column += length;
            }
        }
        // Check for delimiters
        else {
            char delimiter = source[i];
            i++;
            
            switch (delimiter) {
                case '(':
                    tokens[token_count].type = TOKEN_LPAREN;
                    strcpy(tokens[token_count].value, "(");
                    break;
                case ')':
                    tokens[token_count].type = TOKEN_RPAREN;
                    strcpy(tokens[token_count].value, ")");
                    break;
                case '{':
                    tokens[token_count].type = TOKEN_LBRACE;
                    strcpy(tokens[token_count].value, "{");
                    break;
                case '}':
                    tokens[token_count].type = TOKEN_RBRACE;
                    strcpy(tokens[token_count].value, "}");
                    break;
                case ';':
                    tokens[token_count].type = TOKEN_SEMICOLON;
                    strcpy(tokens[token_count].value, ";");
                    break;
                case '=':
                    tokens[token_count].type = TOKEN_ASSIGN;
                    strcpy(tokens[token_count].value, "=");
                    break;
                default:
                    tokens[token_count].type = TOKEN_ERROR;
                    sprintf(tokens[token_count].value, "%c", delimiter);
                    break;
            }
            
            tokens[token_count].line = line;
            tokens[token_count].column = column;
            token_count++;
            column++;
        }
    }
    
    // Add EOF token
    tokens[token_count].type = TOKEN_EOF;
    strcpy(tokens[token_count].value, "EOF");
    tokens[token_count].line = line;
    tokens[token_count].column = column;
    token_count++;
    
    return token_count;
}

// =============================================================================
// PARSER
// =============================================================================

// Global variables for parser
Token tokens[MAX_TOKENS];
int current_token = 0;
int token_count = 0;

// Initialize parser
void initParser(Token* input_tokens, int count) {
    memcpy(tokens, input_tokens, sizeof(Token) * count);
    token_count = count;
    current_token = 0;
}

// Get current token
Token* getCurrentToken() {
    if (current_token < token_count) {
        return &tokens[current_token];
    }
    return NULL;
}

// Get next token
Token* getNextToken() {
    if (current_token < token_count) {
        return &tokens[current_token++];
    }
    return NULL;
}

// Peek at next token
Token* peekNextToken() {
    if (current_token + 1 < token_count) {
        return &tokens[current_token + 1];
    }
    return NULL;
}

// Expect a specific token type
int expectToken(TokenType type) {
    Token* token = getCurrentToken();
    if (token && token->type == type) {
        getNextToken();
        return 1;
    }
    return 0;
}

// Parse expression (simplified)
ASTNode* parseExpression() {
    Token* token = getCurrentToken();
    if (!token) return NULL;
    
    // Parse number
    if (token->type == TOKEN_NUMBER) {
        ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
        node->type = AST_NUMBER;
        node->data.number = atoi(token->value);
        node->parent = NULL;
        getNextToken();
        return node;
    }
    
    // Parse variable
    if (token->type == TOKEN_IDENTIFIER) {
        ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
        node->type = AST_VARIABLE;
        strcpy(node->data.variable, token->value);
        node->parent = NULL;
        getNextToken();
        return node;
    }
    
    // Parse parentheses
    if (token->type == TOKEN_LPAREN) {
        getNextToken();
        ASTNode* node = parseExpression();
        if (expectToken(TOKEN_RPAREN)) {
            return node;
        }
        return NULL;
    }
    
    return NULL;
}

// Parse assignment
ASTNode* parseAssignment() {
    Token* token = getCurrentToken();
    if (!token) return NULL;
    
    if (token->type == TOKEN_IDENTIFIER) {
        char var_name[MAX_STRING_SIZE];
        strcpy(var_name, token->value);
        getNextToken();
        
        if (expectToken(TOKEN_ASSIGN)) {
            ASTNode* expression = parseExpression();
            if (expression) {
                ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
                node->type = AST_ASSIGN;
                strcpy(node->data.assignment.variable, var_name);
                node->data.assignment.expression = expression;
                node->parent = NULL;
                expression->parent = node;
                return node;
            }
        }
    }
    
    return NULL;
}

// Parse statement
ASTNode* parseStatement() {
    Token* token = getCurrentToken();
    if (!token) return NULL;
    
    // Parse variable declaration
    if (token->type == TOKEN_KEYWORD && (strcmp(token->value, "var") == 0 || strcmp(token->value, "const") == 0)) {
        getNextToken();
        return parseAssignment();
    }
    
    // Parse assignment
    if (token->type == TOKEN_IDENTIFIER) {
        return parseAssignment();
    }
    
    // Parse if statement
    if (token->type == TOKEN_KEYWORD && strcmp(token->value, "if") == 0) {
        return parseIfStatement();
    }
    
    // Parse while statement
    if (token->type == TOKEN_KEYWORD && strcmp(token->value, "while") == 0) {
        return parseWhileStatement();
    }
    
    // Parse for statement
    if (token->type == TOKEN_KEYWORD && strcmp(token->value, "for") == 0) {
        return parseForStatement();
    }
    
    // Parse return statement
    if (token->type == TOKEN_KEYWORD && strcmp(token->value, "return") == 0) {
        return parseReturnStatement();
    }
    
    // Parse block
    if (token->type == TOKEN_LBRACE) {
        return parseBlock();
    }
    
    return NULL;
}

// Parse if statement
ASTNode* parseIfStatement() {
    if (expectToken(TOKEN_KEYWORD) && expectToken(TOKEN_LPAREN)) {
        ASTNode* condition = parseExpression();
        if (condition && expectToken(TOKEN_RPAREN)) {
            ASTNode* then_block = parseStatement();
            if (then_block) {
                ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
                node->type = AST_IF;
                node->data.if_statement.condition = condition;
                node->data.if_statement.then_block = then_block;
                node->data.if_statement.else_block = NULL;
                node->parent = NULL;
                condition->parent = node;
                then_block->parent = node;
                
                // Check for else clause
                Token* token = getCurrentToken();
                if (token && token->type == TOKEN_KEYWORD && strcmp(token->value, "else") == 0) {
                    getNextToken();
                    node->data.if_statement.else_block = parseStatement();
                    if (node->data.if_statement.else_block) {
                        node->data.if_statement.else_block->parent = node;
                    }
                }
                
                return node;
            }
        }
    }
    
    return NULL;
}

// Parse while statement
ASTNode* parseWhileStatement() {
    if (expectToken(TOKEN_KEYWORD) && expectToken(TOKEN_LPAREN)) {
        ASTNode* condition = parseExpression();
        if (condition && expectToken(TOKEN_RPAREN)) {
            ASTNode* body = parseStatement();
            if (body) {
                ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
                node->type = AST_WHILE;
                node->data.while_statement.condition = condition;
                node->data.while_statement.body = body;
                node->parent = NULL;
                condition->parent = node;
                body->parent = node;
                return node;
            }
        }
    }
    
    return NULL;
}

// Parse for statement
ASTNode* parseForStatement() {
    if (expectToken(TOKEN_KEYWORD) && expectToken(TOKEN_LPAREN)) {
        ASTNode* init = parseStatement();
        if (init && expectToken(TOKEN_SEMICOLON)) {
            ASTNode* condition = parseExpression();
            if (condition && expectToken(TOKEN_SEMICOLON)) {
                ASTNode* increment = parseExpression();
                if (increment && expectToken(TOKEN_RPAREN)) {
                    ASTNode* body = parseStatement();
                    if (body) {
                        ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
                        node->type = AST_FOR;
                        node->data.for_statement.init = init;
                        node->data.for_statement.condition = condition;
                        node->data.for_statement.increment = increment;
                        node->data.for_statement.body = body;
                        node->parent = NULL;
                        init->parent = node;
                        condition->parent = node;
                        increment->parent = node;
                        body->parent = node;
                        return node;
                    }
                }
            }
        }
    }
    
    return NULL;
}

// Parse return statement
ASTNode* parseReturnStatement() {
    if (expectToken(TOKEN_KEYWORD)) {
        ASTNode* expression = parseExpression();
        ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
        node->type = AST_RETURN;
        node->data.return_statement.expression = expression;
        node->parent = NULL;
        if (expression) {
            expression->parent = node;
        }
        return node;
    }
    
    return NULL;
}

// Parse block
ASTNode* parseBlock() {
    if (expectToken(TOKEN_LBRACE)) {
        ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
        node->type = AST_BLOCK;
        node->data.block.statement_count = 0;
        node->parent = NULL;
        
        while (!expectToken(TOKEN_RBRACE)) {
            ASTNode* statement = parseStatement();
            if (statement) {
                if (node->data.block.statement_count < MAX_CODE_SIZE) {
                    node->data.block.statements[node->data.block.statement_count] = statement;
                    statement->parent = node;
                    node->data.block.statement_count++;
                }
            } else {
                break;
            }
        }
        
        return node;
    }
    
    return NULL;
}

// Parse program
ASTNode* parseProgram() {
    ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
    node->type = AST_PROGRAM;
    node->data.program.statement_count = 0;
    node->parent = NULL;
    
    while (getCurrentToken() && getCurrentToken()->type != TOKEN_EOF) {
        ASTNode* statement = parseStatement();
        if (statement) {
            if (node->data.program.statement_count < MAX_CODE_SIZE) {
                node->data.program.statements[node->data.program.statement_count] = statement;
                statement->parent = node;
                node->data.program.statement_count++;
            }
        } else {
            break;
        }
    }
    
    return node;
}

// =============================================================================
// INTERPRETER
// =============================================================================

// Initialize virtual machine
void initVM() {
    vm.stack_top = 0;
    vm.symbol_count = 0;
    vm.running = 1;
    vm.error = 0;
    
    for (int i = 0; i < 10; i++) {
        vm.registers[i] = 0;
    }
    
    for (int i = 0; i < MAX_SYMBOLS; i++) {
        vm.symbols[i].name[0] = '\0';
        vm.symbols[i].type = SYMBOL_VARIABLE;
        vm.symbols[i].value = 0;
        vm.symbols[i].is_initialized = 0;
    }
}

// Find or create symbol
Symbol* getSymbol(const char* name) {
    for (int i = 0; i < vm.symbol_count; i++) {
        if (strcmp(vm.symbols[i].name, name) == 0) {
            return &vm.symbols[i];
        }
    }
    
    // Create new symbol
    if (vm.symbol_count < MAX_SYMBOLS) {
        strcpy(vm.symbols[vm.symbol_count].name, name);
        vm.symbols[vm.symbol_count].type = SYMBOL_VARIABLE;
        vm.symbols[vm.symbol_count].value = 0;
        vm.symbols[vm.symbol_count].is_initialized = 0;
        return &vm.symbols[vm.symbol_count++];
    }
    
    return NULL;
}

// Evaluate AST node
int evaluateAST(ASTNode* node) {
    if (!node) return 0;
    
    switch (node->type) {
        case AST_NUMBER:
            return node->data.number;
            
        case AST_VARIABLE: {
            Symbol* symbol = getSymbol(node->data.variable);
            if (symbol && symbol->is_initialized) {
                return symbol->value;
            }
            vm.error = 1;
            return 0;
        }
        
        case AST_BINARY_OP: {
            int left = evaluateAST(node->data.binary_op.left);
            int right = evaluateAST(node->data.binary_op.right);
            
            switch (node->data.binary_op.op) {
                case '+': return left + right;
                case '-': return left - right;
                case '*': return left * right;
                case '/': return right != 0 ? left / right : 0;
                case '%': return right != 0 ? left % right : 0;
                case '==': return left == right;
                case '!=': return left != right;
                case '<': return left < right;
                case '<=': return left <= right;
                case '>': return left > right;
                case '>=': return left >= right;
                case '&': return left && right;
                case '|': return left || right;
                default: vm.error = 1; return 0;
            }
        }
        
        case AST_ASSIGN: {
            int value = evaluateAST(node->data.assignment.expression);
            Symbol* symbol = getSymbol(node->data.assignment.variable);
            if (symbol) {
                symbol->value = value;
                symbol->is_initialized = 1;
                return value;
            }
            vm.error = 1;
            return 0;
        }
        
        case AST_IF: {
            int condition = evaluateAST(node->data.if_statement.condition);
            if (condition) {
                return evaluateAST(node->data.if_statement.then_block);
            } else if (node->data.if_statement.else_block) {
                return evaluateAST(node->data.if_statement.else_block);
            }
            return 0;
        }
        
        case AST_WHILE: {
            while (evaluateAST(node->data.while_statement.condition)) {
                evaluateAST(node->data.while_statement.body);
            }
            return 0;
        }
        
        case AST_FOR: {
            evaluateAST(node->data.for_statement.init);
            while (evaluateAST(node->data.for_statement.condition)) {
                evaluateAST(node->data.for_statement.body);
                evaluateAST(node->data.for_statement.increment);
            }
            return 0;
        }
        
        case AST_RETURN: {
            if (node->data.return_statement.expression) {
                return evaluateAST(node->data.return_statement.expression);
            }
            return 0;
        }
        
        case AST_BLOCK: {
            int result = 0;
            for (int i = 0; i < node->data.block.statement_count; i++) {
                result = evaluateAST(node->data.block.statements[i]);
                if (vm.error) break;
            }
            return result;
        }
        
        case AST_PROGRAM: {
            int result = 0;
            for (int i = 0; i < node->data.program.statement_count; i++) {
                result = evaluateAST(node->data.program.statements[i]);
                if (vm.error) break;
            }
            return result;
        }
        
        default:
            vm.error = 1;
            return 0;
    }
}

// =============================================================================
// COMPILER
// =============================================================================

// Simple compiler that generates bytecodes
typedef struct {
    unsigned char bytecodes[MAX_CODE_SIZE];
    int bytecode_count;
    int labels[MAX_SYMBOLS];
    int label_count;
} Compiler;

Compiler compiler;

// Initialize compiler
void initCompiler() {
    compiler.bytecode_count = 0;
    compiler.label_count = 0;
}

// Emit bytecode
void emitBytecode(unsigned char bytecode) {
    if (compiler.bytecode_count < MAX_CODE_SIZE) {
        compiler.bytecodes[compiler.bytecode_count++] = bytecode;
    }
}

// Emit integer
void emitInteger(int value) {
    emitBytecode((value >> 24) & 0xFF);
    emitBytecode((value >> 16) & 0xFF);
    emitBytecode((value >> 8) & 0xFF);
    emitBytecode(value & 0xFF);
}

// Create label
int createLabel() {
    return compiler.label_count++;
}

// Set label
void setLabel(int label) {
    compiler.labels[label] = compiler.bytecode_count;
}

// Compile AST to bytecode
void compileAST(ASTNode* node) {
    if (!node) return;
    
    switch (node->type) {
        case AST_NUMBER:
            emitBytecode(0x01); // LOAD_CONST
            emitInteger(node->data.number);
            break;
            
        case AST_VARIABLE: {
            Symbol* symbol = getSymbol(node->data.variable);
            if (symbol) {
                emitBytecode(0x02); // LOAD_VAR
                emitInteger(symbol - vm.symbols); // Symbol index
            }
            break;
        }
        
        case AST_BINARY_OP:
            compileAST(node->data.binary_op.left);
            compileAST(node->data.binary_op.right);
            
            switch (node->data.binary_op.op) {
                case '+': emitBytecode(0x10); break; // ADD
                case '-': emitBytecode(0x11); break; // SUB
                case '*': emitBytecode(0x12); break; // MUL
                case '/': emitBytecode(0x13); break; // DIV
                case '%': emitBytecode(0x14); break; // MOD
                case '==': emitBytecode(0x20); break; // EQ
                case '!=': emitBytecode(0x21); break; // NEQ
                case '<': emitBytecode(0x22); break; // LT
                case '<=': emitBytecode(0x23); break; // LTE
                case '>': emitBytecode(0x24); break; // GT
                case '>=': emitBytecode(0x25); break; // GTE
                case '&': emitBytecode(0x30); break; // AND
                case '|': emitBytecode(0x31); break; // OR
            }
            break;
            
        case AST_ASSIGN:
            compileAST(node->data.assignment.expression);
            emitBytecode(0x03); // STORE_VAR
            {
                Symbol* symbol = getSymbol(node->data.assignment.variable);
                if (symbol) {
                    emitInteger(symbol - vm.symbols); // Symbol index
                }
            }
            break;
            
        case AST_IF: {
            int else_label = createLabel();
            int end_label = createLabel();
            
            compileAST(node->data.if_statement.condition);
            emitBytecode(0x40); // JUMP_IF_FALSE
            emitInteger(else_label);
            
            compileAST(node->data.if_statement.then_block);
            emitBytecode(0x50); // JUMP
            emitInteger(end_label);
            
            setLabel(else_label);
            if (node->data.if_statement.else_block) {
                compileAST(node->data.if_statement.else_block);
            }
            
            setLabel(end_label);
            break;
        }
        
        case AST_WHILE: {
            int start_label = createLabel();
            int end_label = createLabel();
            
            setLabel(start_label);
            compileAST(node->data.while_statement.condition);
            emitBytecode(0x40); // JUMP_IF_FALSE
            emitInteger(end_label);
            
            compileAST(node->data.while_statement.body);
            emitBytecode(0x50); // JUMP
            emitInteger(start_label);
            
            setLabel(end_label);
            break;
        }
        
        case AST_BLOCK: {
            for (int i = 0; i < node->data.block.statement_count; i++) {
                compileAST(node->data.block.statement[i]);
            }
            break;
        }
        
        case AST_PROGRAM: {
            for (int i = 0; i < node->data.program.statement_count; i++) {
                compileAST(node->data.program.statements[i]);
            }
            break;
        }
        
        default:
            break;
    }
}

// =============================================================================
// VIRTUAL MACHINE EXECUTOR
// =============================================================================

// Execute bytecode
void executeBytecode() {
    int pc = 0; // Program counter
    
    while (pc < compiler.bytecode_count && vm.running) {
        unsigned char opcode = compiler.bytecodes[pc++];
        
        switch (opcode) {
            case 0x01: // LOAD_CONST
            {
                int value = (compiler.bytecodes[pc] << 24) | 
                           (compiler.bytecodes[pc + 1] << 16) | 
                           (compiler.bytecodes[pc + 2] << 8) | 
                           compiler.bytecodes[pc + 3];
                pc += 4;
                vm.stack[vm.stack_top++] = value;
                break;
            }
            
            case 0x02: // LOAD_VAR
            {
                int symbol_index = (compiler.bytecodes[pc] << 24) | 
                                  (compiler.bytecodes[pc + 1] << 16) | 
                                  (compiler.bytecodes[pc + 2] << 8) | 
                                  compiler.bytecodes[pc + 3];
                pc += 4;
                if (symbol_index < vm.symbol_count && vm.symbols[symbol_index].is_initialized) {
                    vm.stack[vm.stack_top++] = vm.symbols[symbol_index].value;
                } else {
                    vm.error = 1;
                    vm.running = 0;
                }
                break;
            }
            
            case 0x03: // STORE_VAR
            {
                int symbol_index = (compiler.bytecodes[pc] << 24) | 
                                  (compiler.bytecodes[pc + 1] << 16) | 
                                  (compiler.bytecodes[pc + 2] << 8) | 
                                  compiler.bytecodes[pc + 3];
                pc += 4;
                if (symbol_index < vm.symbol_count) {
                    vm.symbols[symbol_index].value = vm.stack[--vm.stack_top];
                    vm.symbols[symbol_index].is_initialized = 1;
                } else {
                    vm.error = 1;
                    vm.running = 0;
                }
                break;
            }
            
            case 0x10: // ADD
            {
                int b = vm.stack[--vm.stack_top];
                int a = vm.stack[--vm.stack_top];
                vm.stack[vm.stack_top++] = a + b;
                break;
            }
            
            case 0x11: // SUB
            {
                int b = vm.stack[--vm.stack_top];
                int a = vm.stack[--vm.stack_top];
                vm.stack[vm.stack_top++] = a - b;
                break;
            }
            
            case 0x12: // MUL
            {
                int b = vm.stack[--vm.stack_top];
                int a = vm.stack[--vm.stack_top];
                vm.stack[vm.stack_top++] = a * b;
                break;
            }
            
            case 0x13: // DIV
            {
                int b = vm.stack[--vm.stack_top];
                int a = vm.stack[--vm.stack_top];
                vm.stack[vm.stack_top++] = b != 0 ? a / b : 0;
                break;
            }
            
            case 0x20: // EQ
            {
                int b = vm.stack[--vm.stack_top];
                int a = vm.stack[--vm.stack_top];
                vm.stack[vm.stack_top++] = a == b;
                break;
            }
            
            case 0x40: // JUMP_IF_FALSE
            {
                int address = (compiler.bytecodes[pc] << 24) | 
                             (compiler.bytecodes[pc + 1] << 16) | 
                             (compiler.bytecodes[pc + 2] << 8) | 
                             compiler.bytecodes[pc + 3];
                pc += 4;
                int condition = vm.stack[--vm.stack_top];
                if (!condition) {
                    pc = address;
                }
                break;
            }
            
            case 0x50: // JUMP
            {
                int address = (compiler.bytecodes[pc] << 24) | 
                             (compiler.bytecodes[pc + 1] << 16) | 
                             (compiler.bytecodes[pc + 2] << 8) | 
                             compiler.bytecodes[pc + 3];
                pc = address;
                break;
            }
            
            default:
                vm.error = 1;
                vm.running = 0;
                break;
        }
    }
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateLexer() {
    printf("=== LEXER DEMONSTRATION ===\n");
    
    const char* source_code = 
        "var x = 10;\n"
        "var y = 20;\n"
        "if (x < y) {\n"
        "    x = x + 1;\n"
        "}\n";
    
    printf("Source Code:\n%s\n", source_code);
    
    Token tokens[MAX_TOKENS];
    int token_count = tokenize(source_code, tokens);
    
    printf("\nTokens (%d):\n", token_count);
    for (int i = 0; i < token_count; i++) {
        const char* type_str;
        switch (tokens[i].type) {
            case TOKEN_KEYWORD: type_str = "KEYWORD"; break;
            case TOKEN_IDENTIFIER: type_str = "IDENTIFIER"; break;
            case TOKEN_NUMBER: type_str = "NUMBER"; break;
            case TOKEN_OPERATOR: type_str = "OPERATOR"; break;
            case TOKEN_ASSIGN: type_str = "ASSIGN"; break;
            case TOKEN_LPAREN: type_str = "LPAREN"; break;
            case TOKEN_RPAREN: type_str = "RPAREN"; break;
            case TOKEN_LBRACE: type_str = "LBRACE"; break;
            case TOKEN_RBRACE: type_str = "RBRACE"; break;
            case TOKEN_SEMICOLON: type_str = "SEMICOLON"; break;
            case TOKEN_EOF: type_str = "EOF"; break;
            default: type_str = "UNKNOWN"; break;
        }
        
        printf("  %s: '%s' (line %d, col %d)\n", type_str, tokens[i].value, tokens[i].line, tokens[i].column);
    }
    
    printf("\n");
}

void demonstrateParser() {
    printf("=== PARSER DEMONSTRATION ===\n");
    
    const char* source_code = "var x = 10 + 5;";
    
    printf("Source Code: %s\n", source_code);
    
    Token tokens[MAX_TOKENS];
    int token_count = tokenize(source_code, tokens);
    
    initParser(tokens, token_count);
    
    ASTNode* ast = parseProgram();
    if (ast) {
        printf("AST parsed successfully!\n");
        printf("Program has %d statements\n", ast->data.program.statement_count);
    } else {
        printf("Failed to parse AST\n");
    }
    
    printf("\n");
}

void demonstrateInterpreter() {
    printf("=== INTERPRETER DEMONSTRATION ===\n");
    
    const char* source_code = 
        "var x = 10;\n"
        "var y = 20;\n"
        "var z = x + y;\n";
    
    printf("Source Code:\n%s\n", source_code);
    
    Token tokens[MAX_TOKENS];
    int token_count = tokenize(source_code, tokens);
    
    initParser(tokens, token_count);
    ASTNode* ast = parseProgram();
    
    if (ast) {
        initVM();
        int result = evaluateAST(ast);
        
        printf("Interpretation completed!\n");
        printf("Final result: %d\n", result);
        printf("Error: %s\n", vm.error ? "Yes" : "No");
        
        // Print symbol table
        printf("\nSymbol Table:\n");
        for (int i = 0; i < vm.symbol_count; i++) {
            printf("  %s = %d (%s)\n", vm.symbols[i].name, vm.symbols[i].value, 
                   vm.symbols[i].is_initialized ? "initialized" : "uninitialized");
        }
    }
    
    printf("\n");
}

void demonstrateCompiler() {
    printf("=== COMPILER DEMONSTRATION ===\n");
    
    const char* source_code = "var x = 10 + 5;";
    
    printf("Source Code: %s\n", source_code);
    
    Token tokens[MAX_TOKENS];
    int token_count = tokenize(source_code, tokens);
    
    initParser(tokens, token_count);
    ASTNode* ast = parseProgram();
    
    if (ast) {
        initCompiler();
        compileAST(ast);
        
        printf("Compilation completed!\n");
        printf("Generated %d bytecodes\n", compiler.bytecode_count);
        
        printf("\nBytecodes:\n");
        for (int i = 0; i < compiler.bytecode_count; i++) {
            printf("  %02X", compiler.bytecodes[i]);
            if ((i + 1) % 8 == 0) printf("\n");
        }
        
        // Execute bytecode
        initVM();
        executeBytecode();
        
        printf("\nExecution completed!\n");
        printf("Error: %s\n", vm.error ? "Yes" : "No");
        
        if (vm.stack_top > 0) {
            printf("Stack top: %d\n", vm.stack[vm.stack_top - 1]);
        }
    }
    
    printf("\n");
}

void demonstrateControlFlow() {
    printf("=== CONTROL FLOW DEMONSTRATION ===\n");
    
    const char* source_code = 
        "var i = 0;\n"
        "var sum = 0;\n"
        "while (i < 10) {\n"
        "    sum = sum + i;\n"
        "    i = i + 1;\n"
        "}\n";
    
    printf("Source Code:\n%s\n", source_code);
    
    Token tokens[MAX_TOKENS];
    int token_count = tokenize(source_code, tokens);
    
    initParser(tokens, token_count);
    ASTNode* ast = parseProgram();
    
    if (ast) {
        initVM();
        int result = evaluateAST(ast);
        
        printf("Execution completed!\n");
        printf("Final result: %d\n", result);
        
        // Print symbol table
        printf("\nSymbol Table:\n");
        for (int i = 0; i < vm.symbol_count; i++) {
            printf("  %s = %d\n", vm.symbols[i].name, vm.symbols[i].value);
        }
    }
    
    printf("\n");
}

void demonstrateFunctions() {
    printf("=== FUNCTIONS DEMONSTRATION ===\n");
    
    const char* source_code = 
        "function add(a, b) {\n"
        "    return a + b;\n"
        "}\n"
        "var result = add(3, 4);\n";
    
    printf("Source Code:\n%s\n", source_code);
    printf("Note: Function support is simplified in this demo\n");
    
    // Simplified function demonstration
    printf("\nSimplified function demonstration:\n");
    printf("Function: add(a, b)\n");
    printf("Input: a=3, b=4\n");
    printf("Output: 7\n");
    
    printf("\n");
}

void demonstrateErrorHandling() {
    printf("=== ERROR HANDLING DEMONSTRATION ===\n");
    
    // Test with invalid code
    const char* invalid_code = "var x = ;";
    
    printf("Invalid Source Code: %s\n", invalid_code);
    
    Token tokens[MAX_TOKENS];
    int token_count = tokenize(invalid_code, tokens);
    
    initParser(tokens, token_count);
    ASTNode* ast = parseProgram();
    
    if (!ast) {
        printf("Parser correctly detected invalid syntax!\n");
    } else {
        initVM();
        evaluateAST(ast);
        if (vm.error) {
            printf("Interpreter correctly detected runtime error!\n");
        }
    }
    
    printf("\n");
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Compiler and Interpreter Examples\n");
    printf("=================================\n\n");
    
    // Run demonstrations
    demonstrateLexer();
    demonstrateParser();
    demonstrateInterpreter();
    demonstrateCompiler();
    demonstrateControlFlow();
    demonstrateFunctions();
    demonstrateErrorHandling();
    
    printf("All compiler and interpreter examples demonstrated!\n");
    printf("Note: These are simplified implementations for educational purposes.\n");
    printf("Real compilers and interpreters are much more complex and robust.\n");
    
    return 0;
}
