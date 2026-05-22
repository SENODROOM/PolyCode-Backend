# Vue.js Framework

Vue.js is a progressive JavaScript framework for building user interfaces. It's designed from the ground up to be incrementally adoptable.

## What is Vue.js?

Vue.js is a progressive framework that focuses on the view layer. It's easy to pick up and integrates with other libraries or existing projects.

### Key Features
- **Reactive Data Binding**: Automatic updates when data changes
- **Component-Based Architecture**: Reusable components
- **Virtual DOM**: Efficient DOM manipulation
- **Template System**: Declarative templates with HTML-like syntax
- **Directives**: Special attributes for DOM manipulation
- **Computed Properties**: Cached computed values
- **Watchers**: React to data changes

## Getting Started

### Installation Options

#### CDN (for prototyping)
```html
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
```

#### NPM (for production)
```bash
npm install vue@next
```

#### Vue CLI
```bash
npm install -g @vue/cli
vue create my-project
```

#### Vite (recommended)
```bash
npm init vue@latest my-project
cd my-project
npm install
npm run dev
```

### Basic Vue Application
```html
<!DOCTYPE html>
<html>
<head>
    <title>Vue.js Basics</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <div id="app">
        <h1>{{ message }}</h1>
        <p>{{ greeting }}</p>
        <button @click="changeMessage">Change Message</button>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    message: 'Hello, Vue!',
                    name: 'World'
                };
            },
            computed: {
                greeting() {
                    return `Hello, ${this.name}!`;
                }
            },
            methods: {
                changeMessage() {
                    this.message = 'Message changed!';
                    this.name = 'Vue Developer';
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
```

## Vue Components

### Single File Components (.vue)
```vue
<!-- UserProfile.vue -->
<template>
    <div class="user-profile">
        <h2>{{ user.name }}</h2>
        <p>Email: {{ user.email }}</p>
        <p>Age: {{ user.age }}</p>
        <button @click="celebrateBirthday">Celebrate Birthday</button>
    </div>
</template>

<script>
export default {
    name: 'UserProfile',
    props: {
        userId: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            user: {
                name: 'John Doe',
                email: 'john@example.com',
                age: 30
            }
        };
    },
    methods: {
        celebrateBirthday() {
            this.user.age++;
            this.$emit('birthday', this.user.age);
        }
    },
    mounted() {
        console.log('UserProfile component mounted');
    }
};
</script>

<style scoped>
.user-profile {
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin: 10px;
}

h2 {
    color: #42b983;
}
</style>
```

### Component Registration
```javascript
// main.js
import { createApp } from 'vue';
import App from './App.vue';
import UserProfile from './components/UserProfile.vue';

const app = createApp(App);

// Global registration
app.component('UserProfile', UserProfile);

app.mount('#app');
```

### Using Components
```vue
<!-- App.vue -->
<template>
    <div id="app">
        <h1>User Management</h1>
        <UserProfile 
            :user-id="1" 
            @birthday="handleBirthday"
        />
    </div>
</template>

<script>
import UserProfile from './components/UserProfile.vue';

export default {
    name: 'App',
    components: {
        UserProfile
    },
    methods: {
        handleBirthday(newAge) {
            console.log(`User is now ${newAge} years old`);
        }
    }
};
</script>
```

## Data Binding

### Interpolation
```vue
<template>
    <div>
        <p>Message: {{ message }}</p>
        <p>Computed: {{ reversedMessage }}</p>
        <p>Method: {{ getTime() }}</p>
        <p>Expression: {{ number + 1 }}</p>
        <p>Ternary: {{ isLoggedIn ? 'Welcome' : 'Please login' }}</p>
    </div>
</template>
```

### Attribute Binding
```vue
<template>
    <div>
        <!-- Basic attribute binding -->
        <img :src="imageUrl" :alt="imageAlt">
        
        <!-- Class binding -->
        <div :class="{ active: isActive, 'text-danger': hasError }">
            Dynamic classes
        </div>
        
        <!-- Array syntax -->
        <div :class="[baseClass, { active: isActive }]">
            Array classes
        </div>
        
        <!-- Style binding -->
        <div :style="{ color: textColor, fontSize: fontSize + 'px' }">
            Dynamic styles
        </div>
        
        <!-- Boolean attributes -->
        <button :disabled="isDisabled">Click me</button>
        
        <!-- Multiple attributes -->
        <div v-bind="objectOfAttrs">Multiple attributes</div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            imageUrl: '/path/to/image.jpg',
            imageAlt: 'Description',
            isActive: true,
            hasError: false,
            baseClass: 'container',
            textColor: 'red',
            fontSize: 16,
            isDisabled: false,
            objectOfAttrs: {
                id: 'my-element',
                class: 'highlight',
                'data-value': 123
            }
        };
    }
};
</script>
```

## Directives

### v-if, v-else-if, v-else
```vue
<template>
    <div>
        <p v-if="isLoggedIn">Welcome back!</p>
        <p v-else-if="isGuest">Welcome, guest!</p>
        <p v-else>Please login</p>
        
        <!-- Template wrapper for multiple elements -->
        <template v-if="showDetails">
            <p>Name: {{ user.name }}</p>
            <p>Email: {{ user.email }}</p>
            <p>Age: {{ user.age }}</p>
        </template>
    </div>
</template>
```

### v-show
```vue
<template>
    <div>
        <p v-show="isVisible">This element is conditionally visible</p>
        <!-- v-show uses display: none, keeps element in DOM -->
    </div>
</template>
```

### v-for
```vue
<template>
    <div>
        <!-- Array iteration -->
        <ul>
            <li v-for="(item, index) in items" :key="item.id">
                {{ index }}: {{ item.name }}
            </li>
        </ul>
        
        <!-- Object iteration -->
        <ul>
            <li v-for="(value, key) in user" :key="key">
                {{ key }}: {{ value }}
            </li>
        </ul>
        
        <!-- Range iteration -->
        <span v-for="n in 10" :key="n">{{ n }} </span>
    </div>
</template>

<script>
export default {
    data() {
        return {
            items: [
                { id: 1, name: 'Item 1' },
                { id: 2, name: 'Item 2' },
                { id: 3, name: 'Item 3' }
            ],
            user: {
                name: 'John Doe',
                email: 'john@example.com',
                age: 30
            }
        };
    }
};
</script>
```

### v-on (Event Handling)
```vue
<template>
    <div>
        <!-- Basic event handling -->
        <button @click="sayHello">Say Hello</button>
        
        <!-- Event with parameter -->
        <button @click="sayHello('Vue')">Say Hello to Vue</button>
        
        <!-- Event with event object -->
        <button @click="handleClick($event)">Handle Click</button>
        
        <!-- Event modifiers -->
        <button @click.stop="doThis">Stop propagation</button>
        <form @submit.prevent="onSubmit">Prevent default</form>
        <input @keyup.enter="submit">Enter key</input>
        
        <!-- Multiple event handlers -->
        <button @click="doThis(), doThat()">Multiple actions</button>
        
        <!-- Dynamic event -->
        <button @[eventName]="doSomething">Dynamic event</button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            eventName: 'click'
        };
    },
    methods: {
        sayHello(name = 'World') {
            alert(`Hello, ${name}!`);
        },
        handleClick(event) {
            console.log('Button clicked', event);
        },
        doThis() {
            console.log('Doing this');
        },
        doThat() {
            console.log('Doing that');
        },
        doSomething() {
            console.log('Dynamic event handler');
        }
    }
};
</script>
```

### v-model (Two-way Binding)
```vue
<template>
    <div>
        <!-- Text input -->
        <input v-model="message" placeholder="Enter message">
        <p>Message: {{ message }}</p>
        
        <!-- Textarea -->
        <textarea v-model="description" placeholder="Enter description"></textarea>
        <p>Description: {{ description }}</p>
        
        <!-- Checkbox -->
        <input type="checkbox" id="checkbox" v-model="isChecked">
        <label for="checkbox">Checked: {{ isChecked }}</label>
        
        <!-- Multiple checkboxes -->
        <input type="checkbox" value="red" v-model="colors"> Red
        <input type="checkbox" value="green" v-model="colors"> Green
        <input type="checkbox" value="blue" v-model="colors"> Blue
        <p>Colors: {{ colors }}</p>
        
        <!-- Radio buttons -->
        <input type="radio" value="male" v-model="gender"> Male
        <input type="radio" value="female" v-model="gender"> Female
        <p>Gender: {{ gender }}</p>
        
        <!-- Select -->
        <select v-model="selectedCountry">
            <option value="">Select country</option>
            <option value="us">United States</option>
            <option value="uk">United Kingdom</option>
            <option value="ca">Canada</option>
        </select>
        <p>Country: {{ selectedCountry }}</p>
        
        <!-- Multiple select -->
        <select v-model="selectedCountries" multiple>
            <option value="us">United States</option>
            <option value="uk">United Kingdom</option>
            <option value="ca">Canada</option>
        </select>
        <p>Countries: {{ selectedCountries }}</p>
        
        <!-- Modifiers -->
        <input v-model.lazy="message"> <!-- Lazy update -->
        <input v-model.number="age" type="number"> <!-- Number conversion -->
        <input v-model.trim="message"> <!-- Trim whitespace -->
    </div>
</template>

<script>
export default {
    data() {
        return {
            message: '',
            description: '',
            isChecked: false,
            colors: [],
            gender: '',
            selectedCountry: '',
            selectedCountries: [],
            age: 0
        };
    }
};
</script>
```

## Computed Properties and Watchers

### Computed Properties
```vue
<template>
    <div>
        <input v-model="firstName" placeholder="First name">
        <input v-model="lastName" placeholder="Last name">
        
        <p>Full name: {{ fullName }}</p>
        <p>Reversed: {{ reversedFullName }}</p>
        
        <button @click="addTodo">Add Todo</button>
        <ul>
            <li v-for="todo in completedTodos" :key="todo.id">
                {{ todo.text }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            firstName: '',
            lastName: '',
            todos: [
                { id: 1, text: 'Learn Vue', completed: true },
                { id: 2, text: 'Build app', completed: false },
                { id: 3, text: 'Deploy app', completed: true }
            ]
        };
    },
    computed: {
        fullName() {
            return `${this.firstName} ${this.lastName}`;
        },
        reversedFullName() {
            return this.fullName.split('').reverse().join('');
        },
        completedTodos() {
            return this.todos.filter(todo => todo.completed);
        }
    },
    methods: {
        addTodo() {
            this.todos.push({
                id: Date.now(),
                text: 'New todo',
                completed: false
            });
        }
    }
};
</script>
```

### Watchers
```vue
<template>
    <div>
        <input v-model="question" placeholder="Ask a question">
        <p v-if="answer">{{ answer }}</p>
        
        <input v-model.number="count" type="number">
        <p>Count squared: {{ countSquared }}</p>
    </div>
</template>

<script>
export default {
    data() {
        return {
            question: '',
            answer: '',
            count: 0,
            countSquared: 0
        };
    },
    watch: {
        // Watch for changes in question
        question(newQuestion, oldQuestion) {
            if (newQuestion.includes('?')) {
                this.getAnswer();
            }
        },
        
        // Watch object property
        'user.name'(newName) {
            console.log(`Name changed to: ${newName}`);
        },
        
        // Watch with immediate option
        count: {
            immediate: true,
            handler(newCount) {
                this.countSquared = newCount * newCount;
            }
        },
        
        // Watch with deep option for objects
        user: {
            handler(newUser) {
                console.log('User changed:', newUser);
            },
            deep: true
        }
    },
    methods: {
        getAnswer() {
            this.answer = 'Thinking...';
            setTimeout(() => {
                this.answer = 'The answer is 42';
            }, 1000);
        }
    }
};
</script>
```

## Lifecycle Hooks

### Component Lifecycle
```vue
<script>
export default {
    // Creation hooks
    beforeCreate() {
        console.log('beforeCreate: Instance not yet created');
    },
    created() {
        console.log('created: Instance created, no DOM yet');
        // Good for data fetching
        this.fetchData();
    },
    
    // Mounting hooks
    beforeMount() {
        console.log('beforeMount: Template compiled, not mounted yet');
    },
    mounted() {
        console.log('mounted: Component mounted to DOM');
        // Good for DOM manipulation
        this.initializePlugin();
    },
    
    // Updating hooks
    beforeUpdate() {
        console.log('beforeUpdate: Data changed, DOM not updated yet');
    },
    updated() {
        console.log('updated: DOM updated');
        // Be careful: can cause infinite loops
    },
    
    // Destruction hooks
    beforeUnmount() {
        console.log('beforeUnmount: Component about to be destroyed');
        // Good for cleanup
        this.cleanup();
    },
    unmounted() {
        console.log('unmounted: Component destroyed');
    },
    
    methods: {
        fetchData() {
            // Fetch initial data
        },
        initializePlugin() {
            // Initialize third-party plugins
        },
        cleanup() {
            // Clean up timers, event listeners, etc.
        }
    }
};
</script>
```

## Vue Router

### Basic Routing Setup
```bash
npm install vue-router@4
```

```javascript
// router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import About from '../views/About.vue';
import User from '../views/User.vue';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home
    },
    {
        path: '/about',
        name: 'About',
        component: About
    },
    {
        path: '/user/:id',
        name: 'User',
        component: User,
        props: true
    },
    {
        path: '/redirect',
        redirect: '/about'
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFound
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
```

### Using Router
```vue
<!-- App.vue -->
<template>
    <div id="app">
        <nav>
            <router-link to="/">Home</router-link>
            <router-link to="/about">About</router-link>
            <router-link :to="'/user/' + userId">User Profile</router-link>
        </nav>
        
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    data() {
        return {
            userId: 1
        };
    }
};
</script>
```

### Programmatic Navigation
```vue
<script>
export default {
    methods: {
        goToHome() {
            this.$router.push('/');
        },
        goBack() {
            this.$router.go(-1);
        },
        replaceRoute() {
            this.$router.replace('/about');
        }
    }
};
</script>
```
