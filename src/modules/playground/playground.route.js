const express = require("express");
const router = express.Router();
const { executeCodeHandler } = require("./controllers/playgroundController");

/**
 * POST /api/playground - Execute code
 * Request body:
 *   - language: "javascript" or "python" (required)
 *   - code: code to execute (required)
 *   - stdin: standard input (optional)
 * 
 * Response:
 *   - stdout: execution output
 *   - stderr: error output
 *   - error: error message or null
 *   - exitCode: process exit code
 */
router.post("/", executeCodeHandler);

module.exports = router;
