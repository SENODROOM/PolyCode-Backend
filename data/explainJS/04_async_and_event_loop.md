## Asynchronous Programming

### Evolution of Async Patterns

```
1. Callbacks (callback hell 😱)
   fs.readFile('a', (err, data) => {
     fs.readFile('b', (err, data2) => {
       fs.readFile('c', (err, data3) => {
         // deeply nested pyramid of doom
       });
     });
   });

2. Promises (ES6 — chain-able)
   fetchUser()
     .then(user => fetchProfile(user.id))
     .then(profile => fetchPosts(profile.id))
     .catch(err => console.error(err));

3. Async/Await (ES2017 — reads like sync code ✅)
   async function loadData() {
     const user    = await fetchUser();
     const profile = await fetchProfile(user.id);
     const posts   = await fetchPosts(profile.id);
     return posts;
   }
```

```javascript
// Full Async/Await example with error handling
async function fetchUserData(userId) {
  try {
    const response = await fetch(`/api/users/${userId}`);

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    const user = await response.json();
    return user;
  } catch (error) {
    console.error("Failed to fetch user:", error);
    throw error;  // re-throw for caller to handle
  }
}

// Parallel execution with Promise.all
async function fetchDashboard(userId) {
  const [user, posts, notifications] = await Promise.all([
    fetchUser(userId),
    fetchPosts(userId),
    fetchNotifications(userId),
  ]);
  return { user, posts, notifications };
}
```

---

## The Event Loop

```
JavaScript Runtime Architecture
─────────────────────────────────────────────────

  ┌─────────────────────────────────┐
  │         Call Stack              │
  │  ┌─────────────────────────┐   │
  │  │   main()                │   │
  │  │   greet()               │   │
  │  │   console.log()  ◄──────┼───┼── Executes synchronously
  │  └─────────────────────────┘   │
  └─────────────────────────────────┘
           │
           │ when stack is empty
           ▼
  ┌─────────────────────────────────┐
  │         Event Loop              │ ◄── checks queue
  └─────────────────────────────────┘
           │
           ▼
  ┌─────────────────────────────────┐
  │      Callback / Task Queue      │
  │  [setTimeout cb] [click cb]...  │
  └─────────────────────────────────┘

  ┌─────────────────────────────────┐
  │       Web APIs / Node APIs      │
  │  setTimeout, fetch, fs.read...  │ ◄── Async work happens here
  └─────────────────────────────────┘
```

```javascript
console.log("1 - sync");

setTimeout(() => console.log("3 - macro-task"), 0);

Promise.resolve().then(() => console.log("2 - micro-task"));

console.log("1.5 - sync");

// Output order:
// 1 - sync
// 1.5 - sync
// 2 - micro-task   (microtasks run before macrotasks!)
// 3 - macro-task
```

---

