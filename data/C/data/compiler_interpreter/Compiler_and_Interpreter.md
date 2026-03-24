# Compiler and Interpreter

This file contains comprehensive compiler and interpreter examples in C, including lexical analysis, parsing, AST generation, virtual machine execution, and basic programming language implementation.

## 📚 Compiler Theory Overview

### 🔧 Compiler Components
- **Lexer**: Tokenizes source code into tokens
- **Parser**: Builds Abstract Syntax Tree (AST) from tokens
- **Semantic Analyzer**: Validates semantics and builds symbol table
- **Code Generator**: Generates target code (bytecode or machine code)
- **Optimizer**: Improves generated code efficiency

### 🎯 Interpreter Components
- **Lexer**: Same as compiler - tokenizes source code
- **Parser**: Same as compiler - builds AST
- **Evaluator**: Executes AST directly
- **Environment**: Maintains runtime state and symbol table

## 🔤 Lexer (Tokenizer)

### Token Types
```c
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
```

### Token Structure
```c
typedef struct {
    TokenType type;
    char value[MAX_STRING_SIZE];
    int line;
    int column;
} Token;
```

### Tokenization Process
```c
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
```

### Utility Functions
```c
int isKeyword(const char* str) {
    for (int i = 0; i < sizeof(keywords) / sizeof(keywords[0]); i++) {
        if (strcmp(str, keywords[i]) == 0) {
            return 1;
        }
    }
    return 0;
}

int isOperator(char c) {
    return c == '+' || c == '-' || c == '*' || c == '/' || c == '%' ||
           c == '=' || c == '!' || c == '<' || c == '>' || c == '&' || c == '|';
}

int isOperatorString(const char* str) {
    for (int i = 0; i < sizeof(operators) / sizeof(operators[0]); i++) {
        if (strcmp(str, operators[i]) == 0) {
            return 1;
        }
    }
    return 0;
}
```

## 🌳 Parser

### AST Node Types
```c
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
```

### AST Node Structure
```c
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
```

### Parser Functions
```c
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

// Expect a specific token type
int expectToken(TokenType type) {
    Token* token = getCurrentToken();
    if (token && token->type == type) {
        getNextToken();
        return 1;
    }
    return 0;
}
```

### Expression Parsing
```c
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
```

### Statement Parsing
```c
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
```

### Control Flow Parsing
```c
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
```

## 🔄 Interpreter

### Virtual Machine Structure
```c
typedef struct {
    int stack[MAX_CODE_SIZE];
    int stack_top;
    int registers[10];
    Symbol symbols[MAX_SYMBOLS];
    int symbol_count;
    int running;
    int error;
} VirtualMachine;
```

### Symbol Table
```c
typedef struct {
    char name[MAX_STRING_SIZE];
    SymbolType type;
    int value;
    int is_initialized;
} Symbol;

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
```

### AST Evaluation
```c
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
```

### Virtual Machine Initialization
```c
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
```

## 🔧 Compiler

### Compiler Structure
```c
typedef struct {
    unsigned char bytecodes[MAX_CODE_SIZE];
    int bytecode_count;
    int labels[MAX_SYMBOLS];
    int label_count;
} Compiler;
```

### Bytecode Generation
```c
void emitBytecode(unsigned char bytecode) {
    if (compiler.bytecode_count < MAX_CODE_SIZE) {
        compiler.bytecodes[compiler.bytecode_count++] = bytecode;
    }
}

void emitInteger(int value) {
    emitBytecode((value >> 24) & 0xFF);
    emitBytecode((value >> 16) & 0xFF);
    emitBytecode((value >> 8) & 0xFF);
    emitBytecode(value & 0xFF);
}

int createLabel() {
    return compiler.label_count++;
}

void setLabel(int label) {
    compiler.labels[label] = compiler.bytecode_count;
}
```

### AST Compilation
```c
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
                compileAST(node->data.block.statements[i]);
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
```

## 🎯 Bytecode Execution

### Bytecode Instructions
```c
// Instruction set
#define LOAD_CONST  0x01
#define LOAD_VAR   0x02
#define STORE_VAR  0x03
#define ADD        0x10
#define SUB        0x11
#define MUL        0x12
#define DIV        0x13
#define MOD        0x14
#define EQ         0x20
#define NEQ        0x21
#define LT         0x22
#define LTE        0x23
#define GT         0x24
#define GTE        0x25
#define AND        0x30
#define OR         0x31
#define JUMP_IF_FALSE 0x40
#define JUMP       0x50
```

### Bytecode Execution
```c
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
                                  (compiler.bytecodes[pc + 3]);
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
                                  (compiler.bytecodes[pc + 3]);
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
                             (compiler.bytecodes[pc + 3]);
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
                             (compiler.bytecodes[pc + 3]);
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
```

## 💡 Advanced Features

### 1. Function Definition and Call
```c
// Function structure
typedef struct {
    char name[MAX_STRING_SIZE];
    char parameters[10][MAX_STRING_SIZE];
    int parameter_count;
    ASTNode* body;
    int is_defined;
} Function;

Function functions[MAX_SYMBOLS];
int function_count = 0;

// Parse function definition
ASTNode* parseFunctionDefinition() {
    if (expectToken(TOKEN_KEYWORD) && expectToken(TOKEN_IDENTIFIER)) {
        char function_name[MAX_STRING_SIZE];
        strcpy(function_name, getCurrentToken()->value);
        getNextToken();
        
        if (expectToken(TOKEN_LPAREN)) {
            // Parse parameters
            ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
            node->type = AST_FUNCTION_DEFINITION;
            node->parent = NULL;
            
            // Store function definition
            if (function_count < MAX_SYMBOLS) {
                strcpy(functions[function_count].name, function_name);
                functions[function_count].parameter_count = 0;
                functions[function_count].body = parseBlock();
                functions[function_count].is_defined = 1;
                function_count++;
            }
            
            return node;
        }
    }
    
    return NULL;
}

// Parse function call
ASTNode* parseFunctionCall() {
    if (expectToken(TOKEN_IDENTIFIER) && expectToken(TOKEN_LPAREN)) {
        char function_name[MAX_STRING_SIZE];
        strcpy(function_name, getCurrentToken()->value);
        
        ASTNode* node = (ASTNode*)malloc(sizeof(ASTNode));
        node->type = AST_FUNCTION_CALL;
        strcpy(node->data.function_call.function, function_name);
        node->data.function_call.arg_count = 0;
        node->parent = NULL;
        
        // Parse arguments
        while (!expectToken(TOKEN_RPAREN)) {
            ASTNode* arg = parseExpression();
            if (arg && node->data.function_call.arg_count < 10) {
                node->data.function_call.args[node->data.function_call.arg_count++] = arg;
                arg->parent = node;
            }
            
            if (!expectToken(TOKEN_COMMA)) {
                break;
            }
        }
        
        return node;
    }
    
    return NULL;
}
```

### 2. Type System
```c
typedef enum {
    TYPE_INT,
    TYPE_FLOAT,
    TYPE_STRING,
    TYPE_BOOL,
    TYPE_VOID
} DataType;

typedef struct {
    char name[MAX_STRING_SIZE];
    DataType type;
    int is_constant;
} TypedSymbol;

TypedSymbol typed_symbols[MAX_SYMBOLS];
int typed_symbol_count = 0;

void typeCheck(ASTNode* node) {
    if (!node) return;
    
    switch (node->type) {
        case AST_NUMBER:
            // Numbers are always valid
            break;
            
        case AST_VARIABLE: {
            TypedSymbol* symbol = getTypedSymbol(node->data.variable);
            if (!symbol) {
                printf("Error: Undefined variable '%s'\n", node->data.variable);
                vm.error = 1;
            }
            break;
        }
        
        case AST_BINARY_OP: {
            typeCheck(node->data.binary_op.left);
            typeCheck(node->data.binary_op.right);
            
            // Check operator compatibility
            // (Implementation depends on type system rules)
            break;
        }
        
        case AST_ASSIGN: {
            typeCheck(node->data.assignment.expression);
            
            // Check if variable can be assigned
            TypedSymbol* symbol = getTypedSymbol(node->data.assignment.variable);
            if (!symbol) {
                printf("Error: Undefined variable '%s'\n", node->data.assignment.variable);
                vm.error = 1;
            }
            break;
        }
        
        default:
            break;
    }
}
```

### 3. Optimization
```c
// Constant folding
ASTNode* constantFold(ASTNode* node) {
    if (!node) return NULL;
    
    if (node->type == AST_BINARY_OP) {
        ASTNode* left = constantFold(node->data.binary_op.left);
        ASTNode* right = constantFold(node->data.binary_op.right);
        
        if (left && right && left->type == AST_NUMBER && right->type == AST_NUMBER) {
            int result;
            switch (node->data.binary_op.op) {
                case '+': result = left->data.number + right->data.number; break;
                case '-': result = left->data.number - right->data.number; break;
                case '*': result = left->data.number * right->data.number; break;
                case '/': result = right->data.number != 0 ? left->data.number / right->data.number : 0; break;
                default: return node;
            }
            
            // Replace with constant
            node->type = AST_NUMBER;
            node->data.number = result;
            
            // Free old nodes
            free(left);
            free(right);
        }
    }
    
    return node;
}

// Dead code elimination
void eliminateDeadCode(ASTNode* node) {
    if (!node) return;
    
    switch (node->type) {
        case AST_IF: {
            // Check if condition is always true or false
            if (node->data.if_statement.condition->type == AST_NUMBER) {
                if (node->data.if_statement.condition->data.number != 0) {
                    // Always true - keep only then block
                    node->type = node->data.if_statement.then_block->type;
                    node->data = node->data.if_statement.then_block->data;
                    free(node->data.if_statement.condition);
                    free(node->data.if_statement.else_block);
                } else {
                    // Always false - keep only else block
                    if (node->data.if_statement.else_block) {
                        node->type = node->data.if_statement.else_block->type;
                        node->data = node->data.if_statement.else_block->data;
                        free(node->data.if_statement.condition);
                        free(node->data.if_statement.then_block);
                    } else {
                        // Always false with no else - remove entire if
                        node->type = AST_BLOCK;
                        node->data.block.statement_count = 0;
                        free(node->data.if_statement.condition);
                        free(node->data.if_statement.then_block);
                    }
                }
            }
            break;
        }
        
        default:
            // Recursively process child nodes
            break;
    }
}
```

### 4. Error Recovery
```c
typedef struct {
    int line;
    int column;
    char message[256];
    int severity; // 0 = warning, 1 = error
} ErrorInfo;

ErrorInfo errors[MAX_CODE_SIZE];
int error_count = 0;

void reportError(int line, int column, const char* message, int severity) {
    if (error_count < MAX_CODE_SIZE) {
        errors[error_count].line = line;
        errors[error_count].column = column;
        strcpy(errors[error_count].message, message);
        errors[error_count].severity = severity;
        error_count++;
    }
}

int hasErrors() {
    for (int i = 0; i < error_count; i++) {
        if (errors[i].severity == 1) {
            return 1;
        }
    }
    return 0;
}

void printErrors() {
    for (int i = 0; i < error_count; i++) {
        printf("%s at line %d, column %d: %s\n",
               errors[i].severity == 1 ? "Error" : "Warning",
               errors[i].line, errors[i].column, errors[i].message);
    }
}
```

### 5. Garbage Collection
```c
typedef struct {
    void* ptr;
    int marked;
    int size;
} HeapObject;

HeapObject heap[MAX_CODE_SIZE];
int heap_count = 0;

void* allocateMemory(int size) {
    if (heap_count < MAX_CODE_SIZE) {
        HeapObject* obj = &heap[heap_count];
        obj->ptr = malloc(size);
        obj->marked = 0;
        obj->size = size;
        heap_count++;
        return obj->ptr;
    }
    return NULL;
}

void markObject(void* ptr) {
    for (int i = 0; i < heap_count; i++) {
        if (heap[i].ptr == ptr) {
            heap[i].marked = 1;
            break;
        }
    }
}

void markFromRoots() {
    // Mark all symbols that are still in use
    for (int i = 0; i < vm.symbol_count; i++) {
        if (vm.symbols[i].is_initialized) {
            markObject(&vm.symbols[i]);
        }
    }
}

void sweep() {
    for (int i = 0; i < heap_count; i++) {
        if (!heap[i].marked && heap[i].ptr) {
            free(heap[i].ptr);
            heap[i].ptr = NULL;
            heap[i].size = 0;
        } else {
            heap[i].marked = 0;
        }
    }
    
    // Compact heap
    int new_count = 0;
    for (int i = 0; i < heap_count; i++) {
        if (heap[i].ptr) {
            heap[new_count++] = heap[i];
        }
    }
    heap_count = new_count;
}

void garbageCollect() {
    markFromRoots();
    sweep();
}
```

## 📊 Performance Analysis

### Execution Profiling
```c
typedef struct {
    char function_name[MAX_STRING_SIZE];
    int call_count;
    int total_time;
} ProfileEntry;

ProfileEntry profile[MAX_SYMBOLS];
int profile_count = 0;

void startProfiling(const char* function_name) {
    if (profile_count < MAX_SYMBOLS) {
        strcpy(profile[profile_count].function_name, function_name);
        profile[profile_count].call_count = 0;
        profile[profile_count].total_time = 0;
        profile_count++;
    }
}

void endProfiling(const char* function_name, int time_taken) {
    for (int i = 0; i < profile_count; i++) {
        if (strcmp(profile[i].function_name, function_name) == 0) {
            profile[i].call_count++;
            profile[i].total_time += time_taken;
            break;
        }
    }
}

void printProfile() {
    printf("Profile Results:\n");
    printf("===============\n");
    
    for (int i = 0; i < profile_count; i++) {
        printf("%s: %d calls, %d total time\n",
               profile[i].function_name,
               profile[i].call_count,
               profile[i].total_time);
    }
}
```

### Memory Usage
```c
void printMemoryUsage() {
    printf("Memory Usage:\n");
    printf("=============\n");
    printf("Stack: %d/%d (%.1f%%)\n", vm.stack_top, MAX_CODE_SIZE, 
           (float)vm.stack_top / MAX_CODE_SIZE * 100);
    printf("Symbols: %d/%d (%.1f%%)\n", vm.symbol_count, MAX_SYMBOLS,
           (float)vm.symbol_count / MAX_SYMBOLS * 100);
    printf("Bytecodes: %d/%d (%.1f%%)\n", compiler.bytecode_count, MAX_CODE_SIZE,
           (float)compiler.bytecode_count / MAX_CODE_SIZE * 100);
    printf("Heap objects: %d/%d (%.1f%%)\n", heap_count, MAX_CODE_SIZE,
           (float)heap_count / MAX_CODE_SIZE * 100);
}
```

## ⚠️ Common Pitfalls

### 1. Memory Leaks
```c
// Wrong - Not freeing AST nodes
ASTNode* parseExpression() {
    ASTNode* node = malloc(sizeof(ASTNode));
    // ... parse logic
    return node; // Caller must free this
}

// Right - Use reference counting or garbage collection
ASTNode* parseExpressionSafe() {
    ASTNode* node = allocateASTNode();
    // ... parse logic
    return node; // Garbage collector will handle cleanup
}
```

### 2. Left Recursion in Parser
```c
// Wrong - Left recursion can cause infinite loops
ASTNode* parseExpression() {
    ASTNode* left = parseExpression(); // Recursive call
    // ... parse operator
    ASTNode* right = parseExpression();
    // ... combine nodes
    return node;
}

// Right - Use proper precedence parsing
ASTNode* parseExpression() {
    return parseAdditiveExpression();
}

ASTNode* parseAdditiveExpression() {
    ASTNode* left = parseMultiplicativeExpression();
    while (getCurrentToken()->type == TOKEN_OPERATOR && 
           (strcmp(getCurrentToken()->value, "+") == 0 || 
            strcmp(getCurrentToken()->value, "-") == 0)) {
        char op[10];
        strcpy(op, getCurrentToken()->value);
        getNextToken();
        ASTNode* right = parseMultiplicativeExpression();
        
        // Create binary operation node
        left = createBinaryOpNode(op, left, right);
    }
    return left;
}
```

### 3. Buffer Overflow
```c
// Wrong - No bounds checking
void unsafeCopy(char* dest, const char* src) {
    strcpy(dest, src); // May overflow
}

// Right - Always check bounds
void safeCopy(char* dest, const char* src, size_t dest_size) {
    strncpy(dest, src, dest_size - 1);
    dest[dest_size - 1] = '\0';
}
```

### 4. Null Pointer Dereferencing
```c
// Wrong - Not checking for null
void unsafeNodeAccess(ASTNode* node) {
    printf("Node type: %d\n", node->type); // May crash
}

// Right - Always check for null
void safeNodeAccess(ASTNode* node) {
    if (node) {
        printf("Node type: %d\n", node->type);
    }
}
```

## 🔧 Real-World Applications

### 1. Scripting Language
```c
void createScriptingLanguage() {
    // Add string operations
    addToken(TOKEN_STRING, "concat");
    addToken(TOKEN_STRING, "substring");
    addToken(TOKEN_STRING, "length");
    
    // Add array operations
    addToken(TOKEN_OPERATOR, "[]");
    addToken(TOKEN_OPERATOR, ".");
    
    // Add object operations
    addToken(TOKEN_OPERATOR, ".");
    
    // Add built-in functions
    addBuiltinFunction("print");
    addBuiltinFunction("input");
    addBuiltinFunction("math");
}
```

### 2. Configuration Language
```c
void createConfigLanguage() {
    // Add configuration-specific keywords
    addKeyword("config");
    addKeyword("section");
    addKeyword("include");
    
    // Add value types
    addToken(TOKEN_STRING, "string");
    addToken(TOKEN_STRING, "number");
    addToken(TOKEN_STRING, "boolean");
    
    // Add configuration operations
    addToken(TOKEN_OPERATOR, "=");
    addToken(TOKEN_OPERATOR, "+=");
}
```

### 3. Expression Evaluator
```c
int evaluateExpressionString(const char* expr) {
    Token tokens[MAX_TOKENS];
    int token_count = tokenize(expr, tokens);
    
    initParser(tokens, token_count);
    ASTNode* ast = parseExpression();
    
    initVM();
    return evaluateAST(ast);
}

int main() {
    int result = evaluateExpressionString("2 + 3 * 4");
    printf("Result: %d\n", result); // Should print 14
    return 0;
}
```

### 4. Template Engine
```c
char* processTemplate(const char* template_str, const char* variables[10][2]) {
    // Parse template for {{variable}} placeholders
    // Replace with actual values
    // Return processed string
    return processed_string;
}
```

## 🎓 Best Practices

### 1. Error Handling
```c
// Always check for errors
ASTNode* parseProgram() {
    ASTNode* node = malloc(sizeof(ASTNode));
    if (!node) {
        reportError(0, 0, "Memory allocation failed", 1);
        return NULL;
    }
    
    // Parse logic...
    
    if (parseError) {
        free(node);
        return NULL;
    }
    
    return node;
}
```

### 2. Memory Management
```c
// Use RAII-like patterns
typedef struct {
    ASTNode* root;
    Token* tokens;
    int token_count;
} Parser;

void cleanupParser(Parser* parser) {
    if (parser->root) {
        freeAST(parser->root);
    }
    if (parser->tokens) {
        free(parser->tokens);
    }
    parser->root = NULL;
    parser->tokens = NULL;
}
```

### 3. Testing
```c
void testParser() {
    const char* test_cases[] = {
        "var x = 10;",
        "var y = x + 5;",
        "if (x > 5) { x = x + 1; }",
        NULL
    };
    
    for (int i = 0; test_cases[i]; i++) {
        printf("Testing: %s\n", test_cases[i]);
        
        Token tokens[MAX_TOKENS];
        int token_count = tokenize(test_cases[i], tokens);
        
        initParser(tokens, token_count);
        ASTNode* ast = parseProgram();
        
        if (ast) {
            printf("  Parsed successfully\n");
            freeAST(ast);
        } else {
            printf("  Parse failed\n");
        }
    }
}
```

### 4. Extensibility
```c
// Use function pointers for extensibility
typedef struct {
    char name[MAX_STRING_SIZE];
    TokenType (*recognizer)(const char*);
    ASTNode* (*parser)(void);
} CustomTokenHandler;

CustomTokenHandler custom_handlers[MAX_SYMBOLS];
int custom_handler_count = 0;

void registerCustomToken(const char* name, TokenType (*recognizer)(const char*), ASTNode* (*parser)(void)) {
    if (custom_handler_count < MAX_SYMBOLS) {
        strcpy(custom_handlers[custom_handler_count].name, name);
        custom_handlers[custom_handler_count].recognizer = recognizer;
        custom_handlers[custom_handler_count].parser = parser;
        custom_handler_count++;
    }
}
```

### 5. Documentation
```c
/**
 * @brief Parse a program from tokens
 * @return Pointer to the root AST node, or NULL on error
 * @note The caller is responsible for freeing the returned AST
 */
ASTNode* parseProgram();

/**
 * @brief Evaluate an AST node
 * @param node The AST node to evaluate
 * @return The result of evaluation
 * @note Sets vm.error on error
 */
int evaluateAST(ASTNode* node);
```

## 🎓 Learning Path

### 1. Start Simple
- Basic lexer for numbers and identifiers
- Simple parser for expressions
- Tree-walk evaluator

### 2. Add Features
- Variables and assignment
- Control flow (if, while, for)
- Functions and procedures
- Arrays and objects

### 3. Advanced Topics
- Type checking and inference
- Optimization passes
- Error recovery
- Garbage collection

### 4. Real Languages
- Study existing language implementations
- Add advanced features
- Build your own language

## 📚 Further Reading

### Books
- "Compilers: Principles, Techniques, and Tools" by Aho, Lam, Sethi
- "Programming Language Pragmatics" by Michael Scott
- "Modern Compiler Implementation in C" by Appel

### Topics
- Lexical analysis and regular expressions
- Parsing algorithms (LL(1), LR(1), recursive descent)
- Semantic analysis and type systems
- Code generation and optimization
- Runtime systems and garbage collection

Compiler and interpreter implementation in C provides deep understanding of programming language theory and practice. While these examples are simplified for educational purposes, they demonstrate the fundamental concepts used in real compilers and interpreters!
