const path = require("path");

// Path configurations
const DATA_BASE_PATH = path.join(__dirname, "../../data");
const PYTHON_DATA_PATH = path.join(DATA_BASE_PATH, "Python", "data");
const RUNTIME_TMP_PATH = path.join(__dirname, "../../src/runtime/tmp");

// Execution configurations
const RUN_TIMEOUT_MS = 8000;
const MAX_EXEC_OUTPUT_BYTES = 1024 * 1024;

// Cache configurations
const CACHE_TTL = 5 * 60 * 1000; // 5 minutes

// Allowed file extensions
const ALLOWED_EXTENSIONS = [
  ".py",
  ".md",
  ".txt",
  ".js",
  ".jsx",
  ".ts",
  ".tsx",
  ".html",
  ".css",
  ".go",
  ".java",
  ".c",
  ".cpp",
  ".rs",
  ".php",
  ".rb",
  ".sql",
  ".sh",
  ".bash",
];

// File type mapping
const FILE_TYPE_MAP = {
  ".py": "python",
  ".md": "markdown",
  ".txt": "text",
  ".js": "javascript",
  ".jsx": "javascript",
  ".ts": "typescript",
  ".tsx": "typescript",
  ".html": "html",
  ".css": "css",
  ".c": "c",
  ".cpp": "cpp",
  ".java": "java",
  ".go": "go",
  ".rs": "rust",
  ".php": "php",
  ".rb": "ruby",
  ".cs": "csharp",
  ".sql": "sql",
  ".sh": "shell",
  ".bash": "shell",
};

// Directories to ignore
const IGNORE_DIRS = [
  "node_modules",
  "venv",
  "__pycache__",
  ".git",
  "dist",
  "build",
];

module.exports = {
  DATA_BASE_PATH,
  PYTHON_DATA_PATH,
  RUNTIME_TMP_PATH,
  RUN_TIMEOUT_MS,
  MAX_EXEC_OUTPUT_BYTES,
  CACHE_TTL,
  ALLOWED_EXTENSIONS,
  FILE_TYPE_MAP,
  IGNORE_DIRS,
};
