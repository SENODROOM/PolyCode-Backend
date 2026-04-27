const express = require("express");
const router = express.Router();
const {
  listDocuments,
  getStats,
  getLanguagesHandler,
  getTreeHandler,
  getCategoriesHandler,
  executePython,
  getDocument,
} = require("./controllers/documentController");

/**
 * GET /api/documents - List documents with filters
 */
router.get("/", listDocuments);

/**
 * GET /api/documents/stats - Get statistics
 */
router.get("/stats", getStats);

/**
 * GET /api/documents/languages - Get available languages
 */
router.get("/languages", getLanguagesHandler);

/**
 * GET /api/documents/tree - Get directory tree
 */
router.get("/tree", getTreeHandler);

/**
 * GET /api/documents/categories - Get categories
 */
router.get("/categories", getCategoriesHandler);

/**
 * POST /api/documents/run-python - Execute Python code
 */
router.post("/run-python", executePython);

/**
 * GET /api/documents/* - Get single document
 */
router.get("/*path", getDocument);

module.exports = router;
