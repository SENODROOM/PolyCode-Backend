/**
 * Finish Q# data/ layout: create 02-Basics and move stray top-level folders.
 */
const fs = require("fs").promises;
const path = require("path");

const DATA = path.join(__dirname, "../data/Q#/data");
const BASICS = path.join(DATA, "02-Basics");

const MOVE_INTO_BASICS = ["basics", "03-quantum-fundamentals"];

async function exists(p) {
  try {
    await fs.access(p);
    return true;
  } catch {
    return false;
  }
}

async function moveDir(src, dest) {
  if (!(await exists(src))) return;
  if (await exists(dest)) {
    console.log(`skip (dest exists): ${dest}`);
    return;
  }
  await fs.mkdir(path.dirname(dest), { recursive: true });
  await fs.rename(src, dest);
  console.log(`moved: ${path.basename(src)} -> ${path.relative(DATA, dest)}`);
}

async function main() {
  await fs.mkdir(BASICS, { recursive: true });

  for (const name of MOVE_INTO_BASICS) {
    await moveDir(path.join(DATA, name), path.join(BASICS, name));
  }

  const top = await fs.readdir(DATA, { withFileTypes: true });
  const allowed = new Set([
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
    "README.md",
    "BEGINNER_GUIDE.md",
  ]);

  const stray = top.filter((e) => e.isDirectory() && !allowed.has(e.name));
  if (stray.length) {
    console.log("Remaining top-level folders:", stray.map((e) => e.name).join(", "));
  } else {
    console.log("Q# data/ top level is clean.");
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
