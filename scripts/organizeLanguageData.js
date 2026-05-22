/**
 * Reorganize backend/data/<Language>/data/ to match C++ numbered layout:
 * 01-Getting-Started … 10-Reference
 *
 * Usage: node scripts/organizeLanguageData.js [--dry-run] [Language ...]
 */

const fs = require("fs").promises;
const path = require("path");

const DATA_BASE = path.join(__dirname, "../data");

const STANDARD_SECTIONS = [
  "01-Getting-Started",
  "02-Basics",
  "03-Core-Concepts",
  "04-Data-Structures",
  "05-OOP",
  "06-Memory",
  "07-Intermediate",
  "08-Advanced-Topics",
  "09-Projects",
  "10-Reference",
];

/** @type {Record<string, string>} normalized folder name -> section */
const FOLDER_TO_SECTION = {
  // 01 Getting started
  getting_started: "01-Getting-Started",
  "getting-started": "01-Getting-Started",
  "01-getting-started": "01-Getting-Started",
  "1-getting started": "01-Getting-Started",
  "01-hello": "01-Getting-Started",
  "01-introduction": "01-Getting-Started",
  "01-basic": "01-Getting-Started",
  introduction: "01-Getting-Started",
  intro: "01-Getting-Started",
  "level-1-foundations": "01-Getting-Started",
  "level-1-foundation": "01-Getting-Started",

  // 02 Basics
  basics: "02-Basics",
  "01-basics": "02-Basics",
  "02-basics": "02-Basics",
  "02-variables": "02-Basics",
  fundamentals: "02-Basics",
  variables: "02-Basics",
  "data-types": "02-Basics",
  datatypes: "02-Basics",
  operators: "02-Basics",
  strings: "02-Basics",

  // 03 Core concepts
  tutorials: "03-Core-Concepts",
  concepts: "03-Core-Concepts",
  "core-concepts": "03-Core-Concepts",
  "core concepts": "03-Core-Concepts",
  "2-core concepts": "03-Core-Concepts",
  "reference materials": "10-Reference",
  "learning resources": "10-Reference",
  "utilities and tools": "10-Reference",
  "troubleshooting and faq": "10-Reference",
  "best practices": "10-Reference",
  "performance and optimization": "08-Advanced-Topics",
  "security and error handling": "08-Advanced-Topics",
  "integration and automation": "08-Advanced-Topics",
  functions: "03-Core-Concepts",
  methods: "03-Core-Concepts",
  "control-statements": "03-Core-Concepts",
  "control-flow": "03-Core-Concepts",
  "03-control-flow": "03-Core-Concepts",
  "02-control-structures": "03-Core-Concepts",
  "03-functions": "03-Core-Concepts",
  "04-functions": "03-Core-Concepts",
  "file-io": "03-Core-Concepts",
  "06-file-io": "03-Core-Concepts",
  encoding: "03-Core-Concepts",
  modules: "03-Core-Concepts",
  packages: "03-Core-Concepts",

  // 04 Data structures
  data_structures: "04-Data-Structures",
  "data-structures": "04-Data-Structures",
  arrays: "04-Data-Structures",
  "04-arrays": "04-Data-Structures",
  "05-collections": "04-Data-Structures",
  "linked-lists": "04-Data-Structures",
  stacks: "04-Data-Structures",
  queues: "04-Data-Structures",
  trees: "04-Data-Structures",
  collections: "04-Data-Structures",
  "05-collections-framework": "04-Data-Structures",

  // 05 OOP
  oop: "05-OOP",
  "05-oop": "05-OOP",
  "classes-objects": "05-OOP",
  inheritance: "05-OOP",
  polymorphism: "05-OOP",
  encapsulation: "05-OOP",
  abstraction: "05-OOP",
  "abstract-classes": "05-OOP",
  interfaces: "05-OOP",
  "06-structs-interfaces": "05-OOP",
  "07-error-handling": "07-Intermediate",
  "09-channels": "07-Intermediate",
  "10-packages-modules": "03-Core-Concepts",
  "12-web-development": "08-Advanced-Topics",
  "05-collections-framework": "04-Data-Structures",
  "07-multithreading": "07-Intermediate",
  "level-2-oop-basics": "05-OOP",

  // 06 Memory / low-level
  memory: "06-Memory",
  pointers: "06-Memory",
  "smart-pointers": "06-Memory",
  explainpython: "06-Memory",
  explaincpp: "06-Memory",
  explainjs: "06-Memory",
  explainrust: "06-Memory",
  explainphp: "06-Memory",
  "explainc++": "06-Memory",
  internals: "06-Memory",
  "07-error-handling": "06-Memory",

  // 07 Intermediate
  intermediate: "07-Intermediate",
  "04-intermediate": "07-Intermediate",
  concurrency: "07-Intermediate",
  "07-multithreading": "07-Intermediate",
  "08-concurrency-goroutines": "07-Intermediate",
  decorators: "07-Intermediate",
  generators: "07-Intermediate",
  generics: "07-Intermediate",
  exceptions: "07-Intermediate",
  "04-exception-handling": "07-Intermediate",
  "level-3-intermediate": "07-Intermediate",
  metaprogramming: "07-Intermediate",
  "async await": "07-Intermediate",
  "closures and iterators": "07-Intermediate",

  // 08 Advanced
  advanced: "08-Advanced-Topics",
  advanced_topics: "08-Advanced-Topics",
  "advanced topics": "08-Advanced-Topics",
  "advanced-topics": "08-Advanced-Topics",
  "10-advanced-topics": "08-Advanced-Topics",
  "03-advanced": "08-Advanced-Topics",
  "03-advanced techniques": "08-Advanced-Topics",
  "05-advanced": "08-Advanced-Topics",
  algorithms: "08-Advanced-Topics",
  "06-algorithms": "08-Advanced-Topics",
  ai_ml: "08-Advanced-Topics",
  artificial_intelligence: "08-Advanced-Topics",
  machine_learning: "08-Advanced-Topics",
  data_science: "08-Advanced-Topics",
  data_science_and_ml: "08-Advanced-Topics",
  cybersecurity: "08-Advanced-Topics",
  blockchain: "08-Advanced-Topics",
  networking: "08-Advanced-Topics",
  "08-networking": "08-Advanced-Topics",
  jdbc: "08-Advanced-Topics",
  "09-jdbc": "08-Advanced-Topics",
  dom_and_browser: "08-Advanced-Topics",
  frameworks: "08-Advanced-Topics",
  web_and_api: "08-Advanced-Topics",
  web_development: "08-Advanced-Topics",
  api_development: "08-Advanced-Topics",
  database: "08-Advanced-Topics",
  "07-database": "08-Advanced-Topics",
  testing: "08-Advanced-Topics",
  deployment: "08-Advanced-Topics",
  devops: "08-Advanced-Topics",
  quantum: "08-Advanced-Topics",
  "03-quantum-fundamentals": "08-Advanced-Topics",
  robotics: "08-Advanced-Topics",
  iot: "08-Advanced-Topics",
  games: "08-Advanced-Topics",
  mathematics: "08-Advanced-Topics",
  stl: "08-Advanced-Topics",
  templates: "08-Advanced-Topics",
  "level-4-professional": "08-Advanced-Topics",

  // 09 Projects
  projects: "09-Projects",
  "07-projects": "09-Projects",
  "08-projects": "09-Projects",
  examples: "09-Projects",
  "4-practical examples": "09-Projects",
  "10-real world applications": "09-Projects",
  "hostel management": "09-Projects",

  // 10 Reference
  reference: "10-Reference",
  faq: "10-Reference",
  learning_curriculum: "10-Reference",
  utilities: "10-Reference",
  labs: "10-Reference",
  readme: "10-Reference",
};

function normalizeFolderName(name) {
  return name
    .toLowerCase()
    .replace(/^\d+[-.)]\s*/, "")
    .replace(/\s+/g, " ")
    .trim();
}

function resolveSection(folderName) {
  const key = normalizeFolderName(folderName);
  if (FOLDER_TO_SECTION[key]) return FOLDER_TO_SECTION[key];

  const lower = folderName.toLowerCase();
  for (const section of STANDARD_SECTIONS) {
    if (lower === section.toLowerCase()) return section;
  }

  if (/^\d{2}-/.test(folderName) && STANDARD_SECTIONS.includes(folderName)) {
    return folderName;
  }

  // Partial matches
  if (key.includes("getting") || key.includes("intro") || key.includes("hello")) {
    return "01-Getting-Started";
  }
  if (key.includes("basic") || key.includes("fundamental")) return "02-Basics";
  if (key.includes("data struct") || key.includes("array") || key.includes("linked")) {
    return "04-Data-Structures";
  }
  if (key.includes("oop") || key.includes("class") || key.includes("inherit")) {
    return "05-OOP";
  }
  if (key.includes("pointer") || key.includes("memory")) return "06-Memory";
  if (key.includes("intermediate") || key.includes("concurr")) {
    return "07-Intermediate";
  }
  if (key.includes("advanced") || key.includes("algorithm")) {
    return "08-Advanced-Topics";
  }
  if (key.includes("project") || key.includes("example")) return "09-Projects";

  return "10-Reference";
}

function isStandardSection(name) {
  return STANDARD_SECTIONS.includes(name);
}

function samePathCaseInsensitive(a, b) {
  return path.resolve(a).toLowerCase() === path.resolve(b).toLowerCase();
}

async function exists(p) {
  try {
    await fs.access(p);
    return true;
  } catch {
    return false;
  }
}

async function ensureDir(p, dryRun) {
  if (await exists(p)) return;
  if (dryRun) {
    console.log(`  [mkdir] ${p}`);
    return;
  }
  await fs.mkdir(p, { recursive: true });
}

async function copyDirRecursive(src, dest) {
  await fs.mkdir(dest, { recursive: true });
  const entries = await fs.readdir(src, { withFileTypes: true });
  for (const entry of entries) {
    const from = path.join(src, entry.name);
    const to = path.join(dest, entry.name);
    if (entry.isDirectory()) {
      await copyDirRecursive(from, to);
    } else {
      await fs.copyFile(from, to);
    }
  }
}

async function removeDirRecursive(dir) {
  const entries = await fs.readdir(dir, { withFileTypes: true });
  for (const entry of entries) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) await removeDirRecursive(full);
    else await fs.unlink(full);
  }
  await fs.rmdir(dir);
}

/** Windows-safe move; handles case-only renames (02-basics -> 02-Basics). */
async function moveEntry(src, destDir, dryRun, dataPath) {
  const base = path.basename(src);
  const finalDest = path.join(destDir, base);

  if (samePathCaseInsensitive(src, finalDest)) {
    return;
  }

  if (samePathCaseInsensitive(src, destDir)) {
    if (dryRun) {
      console.log(`  [case-rename] ${src} -> ${destDir}`);
      return;
    }
    const temp = path.join(dataPath, `__reorg_${Date.now()}_${base}`);
    await fs.rename(src, temp);
    if (!(await exists(destDir))) {
      await fs.mkdir(destDir, { recursive: true });
    }
    await fs.rename(temp, destDir);
    return;
  }

  let dest = finalDest;
  if (await exists(dest)) {
    console.log(`  [skip] already exists: ${dest}`);
    return;
  }

  if (dryRun) {
    console.log(`  [move] ${src} -> ${dest}`);
    return;
  }

  await fs.mkdir(destDir, { recursive: true });
  try {
    await fs.rename(src, dest);
  } catch (error) {
    if (error.code !== "EPERM" && error.code !== "EXDEV" && error.code !== "EINVAL") {
      throw error;
    }
    console.log(`  [copy] ${src} -> ${dest} (${error.code})`);
    await copyDirRecursive(src, dest);
    await removeDirRecursive(src);
  }
}

async function organizeLanguage(language, dryRun) {
  const langPath = path.join(DATA_BASE, language);
  const dataPath = path.join(langPath, "data");

  if (!(await exists(dataPath))) {
    console.log(`Skip ${language}: no data/ folder`);
    return;
  }

  if (language === "C++") {
    console.log(`Skip ${language}: already organized`);
    return;
  }

  console.log(`\n=== ${language} ===`);

  for (const section of STANDARD_SECTIONS) {
    await ensureDir(path.join(dataPath, section), dryRun);
  }

  const entries = await fs.readdir(dataPath, { withFileTypes: true });
  const moves = [];

  for (const entry of entries) {
    if (!entry.isDirectory()) continue;
    if (entry.name.startsWith(".")) continue;
    if (entry.name.startsWith("__reorg_") || entry.name.startsWith("__tmp_")) continue;
    if (isStandardSection(entry.name)) continue;

    const src = path.join(dataPath, entry.name);
    const section = resolveSection(entry.name);

    if (entry.name.toLowerCase() === section.toLowerCase()) {
      if (entry.name !== section) {
        moves.push({
          src,
          dest: path.join(dataPath, section),
          name: entry.name,
          section,
          caseOnly: true,
        });
      }
      continue;
    }

    const dest = path.join(dataPath, section);
    moves.push({ src, dest, name: entry.name, section });
  }

  // Sort: move deeper/nested names last; process 10-Reference last
  moves.sort((a, b) => a.section.localeCompare(b.section));

  for (const { src, dest, name, section } of moves) {
    if (!(await exists(src))) continue;
    console.log(`  ${name} -> ${section}/`);
    try {
      await moveEntry(src, dest, dryRun, dataPath);
    } catch (error) {
      console.error(`  [error] ${name}: ${error.message}`);
    }
  }

  // Remove legacy top-level folders already present under a numbered section
  const topAfter = await fs.readdir(dataPath, { withFileTypes: true });
  for (const entry of topAfter) {
    if (!entry.isDirectory() || entry.name.startsWith(".")) continue;
    if (isStandardSection(entry.name)) continue;
    if (entry.name.startsWith("__reorg_") || entry.name.startsWith("__tmp_")) continue;

    const legacyPath = path.join(dataPath, entry.name);
    const section = resolveSection(entry.name);
    const nestedPath = path.join(dataPath, section, entry.name);

    if (await exists(nestedPath)) {
      console.log(`  [cleanup] remove duplicate top-level ${entry.name}`);
      if (!dryRun) {
        try {
          await removeDirRecursive(legacyPath);
        } catch (error) {
          console.error(`  [cleanup error] ${entry.name}: ${error.message}`);
        }
      }
    }
  }

  const readmePath = path.join(dataPath, "README.md");
  if (!(await exists(readmePath))) {
    const text = `# PolyCode ${language} Learning Library

Content is organized like the C++ track:

- \`01-Getting-Started\` through \`08-Advanced-Topics\` — learning path
- \`09-Projects\` — hands-on projects
- \`10-Reference\` — extras, FAQ, utilities

Start with \`01-Getting-Started\`, then follow numbered folders in order.
`;
    if (dryRun) {
      console.log(`  [write] ${readmePath}`);
    } else {
      await fs.writeFile(readmePath, text, "utf8");
    }
  }
}

async function main() {
  const args = process.argv.slice(2);
  const dryRun = args.includes("--dry-run");
  const langs = args.filter((a) => !a.startsWith("--"));

  let languages = langs;
  if (!languages.length) {
    const entries = await fs.readdir(DATA_BASE, { withFileTypes: true });
    languages = entries
      .filter((e) => e.isDirectory() && !e.name.startsWith("."))
      .map((e) => e.name);
  }

  console.log(dryRun ? "DRY RUN" : "LIVE RUN");
  for (const lang of languages) {
    await organizeLanguage(lang, dryRun);
  }
  console.log("\nDone.");
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
