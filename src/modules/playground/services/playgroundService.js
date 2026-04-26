const { executePythonCode, executeJavaScriptCode } = require("../../../services/executionService");

/**
 * Execute code in playground
 * @param {string} language - Programming language (javascript, python, js, py)
 * @param {string} code - Code to execute
 * @param {string} stdin - Standard input (optional)
 * @returns {Promise<Object>} Execution result
 */
async function executeCode(language, code, stdin = "") {
  const normalizedLanguage = language.toLowerCase();

  if (["javascript", "js"].includes(normalizedLanguage)) {
    return await executeJavaScriptCode(code);
  } else if (["python", "py"].includes(normalizedLanguage)) {
    return await executePythonCode(code, stdin);
  } else {
    throw new Error(
      `Unsupported language: ${language}. Supported: javascript, python`,
    );
  }
}

/**
 * Validate code execution request
 * @param {string} language - Language
 * @param {string} code - Code
 * @returns {Object} Validation result with status and error message
 */
function validateExecutionRequest(language, code) {
  if (!language || typeof language !== "string") {
    return {
      valid: false,
      error: "Language is required and must be a string.",
    };
  }

  if (!code || typeof code !== "string") {
    return {
      valid: false,
      error: "Code is required and must be a string.",
    };
  }

  if (code.length > 50_000) {
    return {
      valid: false,
      error: "Code is too large. Maximum allowed size is 50,000 characters.",
    };
  }

  const supportedLanguages = ["javascript", "python", "js", "py"];
  if (!supportedLanguages.includes(language.toLowerCase())) {
    return {
      valid: false,
      error: `Unsupported language: ${language}. Supported: ${supportedLanguages.join(", ")}`,
    };
  }

  return { valid: true };
}

module.exports = {
  executeCode,
  validateExecutionRequest,
};
