const {
  executeCode,
  validateExecutionRequest,
} = require("../services/playgroundService");

/**
 * POST /api/playground - Execute code in playground
 */
async function executeCodeHandler(req, res) {
  try {
    const { language, code, stdin = "" } = req.body || {};

    // Validate request
    const validation = validateExecutionRequest(language, code);
    if (!validation.valid) {
      return res.status(400).json({ error: validation.error });
    }

    // Execute code
    const result = await executeCode(language, code, stdin);

    return res.json(result);
  } catch (err) {
    return res.status(500).json({
      stdout: "",
      stderr: err.message,
      error: err.message,
      exitCode: 1,
    });
  }
}

module.exports = {
  executeCodeHandler,
};
