const { spawn } = require("child_process");
const { RUN_TIMEOUT_MS, PYTHON_DATA_PATH } = require("../config/constants");

let resolvedPythonCommand = null;

/**
 * Run a command using spawn and wait for it to spawn
 * @param {string} command - Command to run
 * @param {string[]} args - Command arguments
 * @param {Object} options - Spawn options
 * @returns {Promise} Child process or error
 */
function runSpawn(command, args = [], options = {}) {
  return new Promise((resolve, reject) => {
    const child = spawn(command, args, options);
    child.once("error", reject);
    child.once("spawn", () => resolve(child));
  });
}

/**
 * Resolve Python command on system
 * @returns {Promise<string>} Python command string
 */
async function resolvePythonCommand() {
  if (resolvedPythonCommand) return resolvedPythonCommand;

  const candidates =
    process.platform === "win32"
      ? [process.env.PYTHON_EXECUTABLE, "py -3", "python", "python3"]
      : [process.env.PYTHON_EXECUTABLE, "python3", "python"];

  for (const candidate of candidates.filter(Boolean)) {
    const [cmd, ...args] = candidate.split(" ");
    try {
      const probe = await runSpawn(cmd, [...args, "--version"], {
        stdio: ["ignore", "pipe", "pipe"],
      });
      await new Promise((resolve, reject) => {
        probe.once("exit", (code) =>
          code === 0 ? resolve() : reject(new Error("non-zero exit")),
        );
        probe.once("error", reject);
      });
      resolvedPythonCommand = candidate;
      return resolvedPythonCommand;
    } catch (_) {
      // Try next candidate
    }
  }
  throw new Error(
    "No Python runtime found on server. Install Python or set PYTHON_EXECUTABLE.",
  );
}

/**
 * Execute Python code with auto-input handling
 * @param {string} code - Python code to execute
 * @param {string} stdin - Standard input
 * @returns {Promise<Object>} Execution result with stdout, stderr, error, exitCode
 */
async function executePythonCode(code, stdin = "") {
  const command = await resolvePythonCommand();
  const [cmd, ...baseArgs] = command.split(" ");

  const autoInputPrelude = `
import builtins, re

def __polycode_auto_input(prompt=''):
    p = '' if prompt is None else str(prompt).lower()
    # yes/no prompts
    if 'y/n' in p or 'y / n' in p or re.search(r'\\by/n\\b', p):
        return 'n'
    # menu/choice prompts
    if 'select' in p or 'choice' in p or 'option' in p or re.search(r'\\(1\\s*-\\s*\\d+\\)', p):
        return '1'
    # common values
    if 'password' in p:
        return 'password'
    if 'name' in p:
        return 'Bob'
    if 'id' in p:
        return '1'
    if 'url' in p:
        return 'http://example.com'
    if 'command' in p:
        return 'echo hello'
    if 'file' in p or 'filename' in p or 'path' in p:
        return 'input.txt'
    if 'directory' in p or 'folder' in p:
        return '.'
    if 'target host' in p or 'host' in p:
        return 'localhost'
    if 'port' in p:
        return '80'
    if 'age' in p:
        return '20'
    # numeric-ish
    if re.search(r'(age|hours|minutes|rpm|degrees|score|rate|amount|quantity|number)', p):
        return '0'
    return ''

builtins.input = __polycode_auto_input
`;

  const args = [...baseArgs, "-c", `${autoInputPrelude}\n${code}`];

  return new Promise(async (resolve, reject) => {
    let child;
    try {
      child = await runSpawn(cmd, args, {
        cwd: PYTHON_DATA_PATH,
        env: { ...process.env, PYTHONIOENCODING: "utf-8" },
        stdio: ["pipe", "pipe", "pipe"],
      });
    } catch (e) {
      reject(e);
      return;
    }

    let stdout = "";
    let stderr = "";
    const timer = setTimeout(() => {
      child.kill("SIGKILL");
      reject(new Error(`Python execution timed out after ${RUN_TIMEOUT_MS}ms`));
    }, RUN_TIMEOUT_MS);

    child.stdout.on("data", (chunk) => {
      stdout += chunk.toString();
    });
    child.stderr.on("data", (chunk) => {
      stderr += chunk.toString();
    });
    child.on("error", (e) => {
      clearTimeout(timer);
      reject(e);
    });
    child.on("close", (code) => {
      clearTimeout(timer);
      resolve({
        stdout: stdout.trimEnd(),
        stderr: stderr.trimEnd(),
        error:
          code === 0
            ? null
            : stderr.trimEnd() || `Python exited with code ${code}`,
        exitCode: code,
      });
    });

    if (stdin) child.stdin.write(stdin);
    child.stdin.end();
  });
}

/**
 * Execute JavaScript code
 * @param {string} code - JavaScript code to execute
 * @returns {Promise<Object>} Execution result
 */
async function executeJavaScriptCode(code) {
  return new Promise((resolve) => {
    try {
      const result = eval(code);
      resolve({
        stdout: String(result),
        stderr: "",
        error: null,
        exitCode: 0,
      });
    } catch (error) {
      resolve({
        stdout: "",
        stderr: error.message,
        error: error.message,
        exitCode: 1,
      });
    }
  });
}

module.exports = {
  executePythonCode,
  executeJavaScriptCode,
  runSpawn,
  resolvePythonCommand,
};
