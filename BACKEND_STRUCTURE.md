# Backend Code Organization - PolyCode

## New Backend Structure

The backend has been reorganized with proper separation of concerns following the MVC (Model-View-Controller) pattern with additional service layers for business logic.

### Folder Structure

```
PolyCode-Backend/
├── src/
│   ├── config/
│   │   └── constants.js          # Global constants and configuration
│   │
│   ├── utils/
│   │   └── cache.js              # Caching utilities
│   │
│   ├── services/
│   │   ├── executionService.js   # Code execution (Python, JavaScript)
│   │   └── fileService.js        # File operations (reading, scanning, etc.)
│   │
│   ├── modules/
│   │   ├── documents/
│   │   │   ├── controllers/
│   │   │   │   └── documentController.js     # Route handlers for documents
│   │   │   ├── services/
│   │   │   │   └── documentService.js        # Document business logic
│   │   │   ├── documents.route.js            # Old route file (deprecated)
│   │   │   ├── documents.router.js           # New route definitions
│   │   │   └── documentRouter.js             # Old router file (can be removed)
│   │   │
│   │   └── playground/
│   │       ├── controllers/
│   │       │   └── playgroundController.js   # Route handlers for playground
│   │       ├── services/
│   │       │   └── playgroundService.js      # Playground business logic
│   │       └── playground.route.js           # Route definitions (NEW)
│   │
│   └── Routers/
│       └── healthRouter.js        # Health check routes
│
├── server.js                       # Main Express server
├── package.json
└── ...

```

## File Organization Guide

### 1. **Config Layer** (`src/config/`)
- **Purpose**: Global constants and configuration values
- **Files**:
  - `constants.js`: Paths, timeouts, file extensions, allowed directories

### 2. **Utils Layer** (`src/utils/`)
- **Purpose**: Reusable utility functions
- **Files**:
  - `cache.js`: Caching system with TTL management

### 3. **Services Layer** (`src/services/`)
- **Purpose**: Core business logic used across modules
- **Files**:
  - `executionService.js`: Python and JavaScript code execution
  - `fileService.js`: File operations (reading, scanning, metadata extraction)

### 4. **Modules** (`src/modules/`)
Each module follows this structure:

#### Documents Module (`src/modules/documents/`)
- **controllers/documentController.js**: Route handlers
  - `listDocuments()` - GET /api/documents
  - `getStats()` - GET /api/documents/stats
  - `getLanguagesHandler()` - GET /api/documents/languages
  - `getTreeHandler()` - GET /api/documents/tree
  - `getCategoriesHandler()` - GET /api/documents/categories
  - `executePython()` - POST /api/documents/run-python
  - `getDocument()` - GET /api/documents/*

- **services/documentService.js**: Business logic
  - `getDocuments()` - Fetch documents with filters
  - `getDocumentStats()` - Calculate statistics
  - `getLanguages()` - Get available languages
  - `getDocumentTree()` - Build directory tree
  - `getDocumentCategories()` - Get categories

- **documents.router.js**: Route definitions (NEW)
  - Routes all endpoints to appropriate controllers

#### Playground Module (`src/modules/playground/`)
- **controllers/playgroundController.js**: Route handlers
  - `executeCodeHandler()` - POST /api/playground

- **services/playgroundService.js**: Business logic
  - `executeCode()` - Execute code in specified language
  - `validateExecutionRequest()` - Validate incoming requests

- **playground.route.js**: Route definitions (NEW)
  - Routes POST /api/playground to controller

## Architecture Benefits

### 1. **Separation of Concerns**
- **Controllers**: Handle HTTP requests/responses
- **Services**: Contain business logic
- **Utils**: Provide reusable utilities
- **Config**: Centralized configuration

### 2. **Reusability**
- Services can be used by multiple controllers
- Utils are shared across the application
- Execution service used by both documents and playground

### 3. **Maintainability**
- Each file has a single responsibility
- Easier to debug and test
- Clear dependency flow

### 4. **Scalability**
- Easy to add new modules following the same pattern
- Services can be extracted to separate packages if needed
- Configuration centralized for easy updates

## Migration Notes

### Old Files (Deprecated)
- `src/modules/documents/documents.route.js` - Contains mixed concerns (old file)
- `src/modules/documents/documentRouter.js` - Contains only API docs comments
- `src/modules/playground/playgroundRouter.js` - Contains only API docs comments

### New Files to Use
- `src/modules/documents/documents.router.js` - New organized router
- `src/modules/playground/playground.route.js` - New playground implementation

## How to Add New Features

### Adding a New API Endpoint

1. **Create/Update Service** (`src/modules/[module]/services/`)
   ```javascript
   async function getNewData() {
     // Business logic here
   }
   ```

2. **Create/Update Controller** (`src/modules/[module]/controllers/`)
   ```javascript
   async function getNewDataHandler(req, res) {
     const data = await getNewData();
     res.json(data);
   }
   ```

3. **Update Router** (`src/modules/[module]/[module].router.js`)
   ```javascript
   router.get("/new-endpoint", getNewDataHandler);
   ```

### Adding a New Module

1. Create folder structure:
   ```
   src/modules/newmodule/
   ├── controllers/
   ├── services/
   └── newmodule.route.js
   ```

2. Create service with business logic

3. Create controller with route handlers

4. Create router file

5. Import router in `server.js`

## Code Examples

### Using Execution Service (Both Modules)

```javascript
const { executePythonCode, executeJavaScriptCode } = require("../../services/executionService");

// Execute Python
const pythonResult = await executePythonCode("print('Hello')", "");

// Execute JavaScript
const jsResult = await executeJavaScriptCode("console.log('Hello')");
```

### Using File Service (Documents Module)

```javascript
const { getFileInfo, scanDirectory, buildTree } = require("../../services/fileService");

// Get file info
const info = await getFileInfo(filePath, relativePath, { readContent: true });

// Scan directory
const docs = await scanDirectory(dirPath);

// Build tree
const tree = await buildTree(dirPath, basePath);
```

### Using Cache Utilities

```javascript
const { getFromCache, setInCache, cache } = require("../../utils/cache");

// Get from cache
const cached = getFromCache(cache.documents, "python");

// Set in cache
setInCache(cache.documents, "python", data);
```

## API Endpoints

### Documents API
- `GET /api/documents` - List documents with filters
- `GET /api/documents/stats` - Get statistics
- `GET /api/documents/languages` - Get available languages
- `GET /api/documents/tree` - Get directory tree
- `GET /api/documents/categories` - Get categories
- `POST /api/documents/run-python` - Execute Python code
- `GET /api/documents/*` - Get single document

### Playground API
- `POST /api/playground` - Execute code (JavaScript or Python)

## Next Steps

1. Remove or archive old route files if not needed
2. Test all endpoints to ensure they work with new structure
3. Update any documentation with new file paths
4. Consider adding error middleware
5. Consider adding request validation middleware
6. Consider adding authentication/authorization if needed
