const fs = require("fs").promises;
const path = require("path");
const { DATA_BASE_PATH } = require("../../../config/constants");
const { getFileInfo, scanDirectory } = require("../../../services/fileService");
const {
  getDocuments,
  getDocumentStats,
  getLanguages,
  getDocumentTree,
  getDocumentCategories,
} = require("../services/documentService");
const { executePythonCode } = require("../../../services/executionService");

/**
 * GET /api/documents - List all documents with filters
 */
async function listDocuments(req, res) {
  try {
    const {
      language = "all",
      category,
      fileType,
      search,
      page = 1,
      limit = 20,
      ungrouped,
    } = req.query;

    const result = await getDocuments({
      language,
      category,
      fileType,
      search,
      page,
      limit,
      ungrouped: ungrouped === "true",
    });

    res.json(result);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

/**
 * GET /api/documents/stats - Get document statistics
 */
async function getStats(req, res) {
  try {
    const { language = "all" } = req.query;
    const stats = await getDocumentStats(language);
    res.json(stats);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

/**
 * GET /api/documents/languages - Get available languages
 */
async function getLanguagesHandler(req, res) {
  try {
    const languages = await getLanguages();
    res.json({ languages });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

/**
 * GET /api/documents/tree - Get document tree structure
 */
async function getTreeHandler(req, res) {
  try {
    const { language = "all" } = req.query;
    const tree = await getDocumentTree(language);
    res.json({ tree });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

/**
 * GET /api/documents/categories - Get document categories
 */
async function getCategoriesHandler(req, res) {
  try {
    const { language = "all" } = req.query;
    const categories = await getDocumentCategories(language);
    res.json(categories);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

/**
 * POST /api/documents/run-python - Execute Python code
 */
async function executePython(req, res) {
  try {
    const { code, stdin = "" } = req.body || {};
    if (!code || typeof code !== "string") {
      return res.status(400).json({
        error: "Request body must include a Python code string.",
      });
    }

    const result = await executePythonCode(
      code,
      typeof stdin === "string" ? stdin : "",
    );
    return res.json(result);
  } catch (err) {
    return res.status(500).json({
      stdout: "",
      stderr: err.message,
      error: err.message,
    });
  }
}

/**
 * GET /api/documents/* - Get single document with related code
 */
async function getDocument(req, res) {
  try {
    let requestedPath = Array.isArray(req.params.path)
      ? req.params.path.join("/")
      : req.params.path;
    const { language = "all" } = req.query;
    let fullPath = path.join(
      language === "all" ? DATA_BASE_PATH : path.join(DATA_BASE_PATH, language),
      requestedPath,
    );

    if (!path.resolve(fullPath).startsWith(path.resolve(DATA_BASE_PATH)))
      return res.status(403).json({ error: "Access denied" });

    const fileInfo = await getFileInfo(fullPath, requestedPath, {
      readContent: true,
    });
    if (!fileInfo) return res.status(404).json({ error: "Document not found" });

    if (fileInfo.fileType === "markdown") {
      const scanPath =
        language === "all"
          ? DATA_BASE_PATH
          : path.join(DATA_BASE_PATH, language);
      const allDocs = await scanDirectory(scanPath, scanPath);
      const codeDocs = allDocs.filter((d) => d.fileType !== "markdown");
      const k = fileInfo.title
        .toLowerCase()
        .replace(/[^a-z0-9]/g, "")
        .replace(/s$/, "");
      fileInfo.relatedCode = codeDocs.filter((c) => {
        const ck = c.title
          .toLowerCase()
          .replace(/[^a-z0-9]/g, "")
          .replace(/s$/, "");
        return ck.includes(k) || k.includes(ck);
      });
      fileInfo.isTopic = true;
    }
    res.json(fileInfo);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
}

module.exports = {
  listDocuments,
  getStats,
  getLanguagesHandler,
  getTreeHandler,
  getCategoriesHandler,
  executePython,
  getDocument,
};
