## ES6+ Modern Features

```javascript
// Destructuring
const [first, , third] = [1, 2, 3];     // Array
const { name, age: years } = person;    // Object (rename)
const { x = 0, y = 0 } = point;        // Default values

// Spread & Rest
const merged = { ...obj1, ...obj2 };    // Merge objects
const copy   = [...array];              // Clone array
const [head, ...tail] = [1, 2, 3, 4];  // Rest

// Template Literals
const msg = `Hello ${name}! You are ${age > 18 ? "adult" : "minor"}.`;

// Tagged Templates
const html = String.raw`<div class="box">\n</div>`;  // raw string

// Modules (ESM)
// math.js
export const PI = 3.14159;
export function square(x) { return x * x; }
export default class Calculator { ... }

// app.js
import Calculator, { PI, square } from './math.js';
import * as MathUtils from './math.js';

// Optional chaining & Nullish coalescing
const city = user?.address?.city ?? "Unknown";

// Logical Assignment (ES2021)
a ||= "default";  // a = a || "default"
a &&= transform(a);
a ??= fallback;

// Array methods
[1,[2,[3]]].flat(Infinity);  // [1, 2, 3]
[1, 2, 3].at(-1);            // 3 (negative indexing)
Object.entries({a:1,b:2});   // [["a",1],["b",2]]
Object.fromEntries([...]);   // reverse of entries
```

---

## JavaScript Ecosystem

```
The JS Universe
───────────────────────────────────────────────────
FRONTEND FRAMEWORKS
  React     ──► Component-based UI (Meta)
  Vue       ──► Progressive framework
  Angular   ──► Full framework (Google)
  Svelte    ──► Compile-time framework

BACKEND (Node.js)
  Express   ──► Minimal web framework
  Fastify   ──► High-performance
  NestJS    ──► Enterprise-grade (TypeScript)
  Hono      ──► Ultra-lightweight edge

FULL STACK
  Next.js   ──► React + SSR + API routes
  Nuxt.js   ──► Vue + SSR
  Remix     ──► Web standards first
  SvelteKit ──► Svelte full-stack

TESTING
  Jest      ──► Unit testing
  Vitest    ──► Vite-powered testing
  Playwright──► E2E browser testing
  Cypress   ──► E2E testing

TOOLING
  npm/pnpm/yarn ──► Package managers
  Vite      ──► Build tool & dev server
  esbuild   ──► Ultra-fast bundler (Go)
  TypeScript ──► Typed superset of JS
```

---

## Performance

| V8 Engine Optimization | Description |
|------------------------|-------------|
| **JIT Compilation** | Hot code paths compiled to native machine code |
| **Hidden Classes** | Objects with same shape share internal structure |
| **Inline Caching** | Cache property lookups for speed |
| **Garbage Collection** | Generational GC (young/old heap) |

```javascript
// Performance Tips

// ✅ Use const/let over var
// ✅ Prefer array methods over manual loops for readability
// ✅ Avoid frequent DOM access — cache references
const el = document.getElementById("box");  // cache this

// ✅ Debounce expensive operations
function debounce(fn, delay) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), delay);
  };
}

// ✅ Use Web Workers for CPU-intensive tasks (off main thread)
const worker = new Worker("heavy-computation.js");
```

---

## Use Cases

```
Where JavaScript Runs
──────────────────────────────────────────────
🌐 Web Browser     Vanilla JS, React, Vue
🖥️  Server         Node.js, Deno, Bun
📱 Mobile          React Native, Expo
🖥️  Desktop        Electron (VS Code, Slack)
🔌 IoT/Edge        Espruino, Cloudflare Workers
🤖 ML/AI           TensorFlow.js, ONNX Runtime
🎮 Games           Phaser.js, Three.js, Babylon.js
📊 Data Viz        D3.js, Chart.js, Observable
```

---

## Quick Reference Card

```
Variable   │ let x = 5; const PI = 3.14;
String     │ `Hello ${name}`
Array      │ [1,2,3].map(x => x*2).filter(x => x>2)
Object     │ const { a, b } = obj;
Arrow Fn   │ const fn = (x) => x * 2;
Async      │ const data = await fetch(url).then(r=>r.json())
Class      │ class A extends B { constructor() { super(); } }
Module     │ import { fn } from './module.js';
Error      │ try { ... } catch (e) { console.error(e); }
Spread     │ [...arr1, ...arr2]; { ...obj1, key: val }
```

---

*JavaScript: the language that powers 98% of the web, runs on every platform, and somehow makes `[] == ![]` evaluate to `true`.*
