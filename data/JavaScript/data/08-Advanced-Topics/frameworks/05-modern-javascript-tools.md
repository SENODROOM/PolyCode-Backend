# Modern JavaScript Tools and Ecosystem

The JavaScript ecosystem has evolved significantly with numerous tools that enhance development productivity, code quality, and application performance.

## Package Managers

### npm (Node Package Manager)
```bash
# Initialize a new project
npm init
npm init -y  # Skip questions

# Install packages
npm install express          # Local dependency
npm install -g nodemon       # Global package
npm install --save-dev jest  # Development dependency

# Update packages
npm update
npm update express

# Remove packages
npm uninstall express

# List packages
npm list
npm list --global

# Run scripts
npm run start
npm test
npm run build

# Clean cache
npm cache clean --force
```

### yarn (Facebook's Package Manager)
```bash
# Install yarn globally
npm install -g yarn

# Initialize project
yarn init

# Install packages
yarn add express
yarn add --dev jest
yarn global add nodemon

# Remove packages
yarn remove express

# Update packages
yarn upgrade
yarn upgrade express

# Run scripts
yarn start
yarn test
```

### pnpm (Fast, disk space efficient)
```bash
# Install pnpm
npm install -g pnpm

# Install packages
pnpm add express
pnpm add -D jest

# Remove packages
pnpm remove express

# List packages
pnpm list
```

## Build Tools

### Webpack
```bash
# Install webpack
npm install --save-dev webpack webpack-cli

# Install loaders
npm install --save-dev babel-loader css-loader style-loader

# Install plugins
npm install --save-dev html-webpack-plugin mini-css-extract-plugin
```

```javascript
// webpack.config.js
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
    entry: './src/index.js',
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'dist'),
        clean: true
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader']
            },
            {
                test: /\.(png|svg|jpg|jpeg|gif)$/i,
                type: 'asset/resource'
            }
        ]
    },
    plugins: [
        new HtmlWebpackPlugin({
            template: './src/index.html'
        })
    ],
    devServer: {
        contentBase: './dist',
        port: 3000,
        hot: true
    },
    mode: 'development'
};
```

### Vite (Modern Build Tool)
```bash
# Create Vite project
npm create vite@latest my-project
cd my-project
npm install
npm run dev
```

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [react()],
    build: {
        outDir: 'dist',
        sourcemap: true
    },
    server: {
        port: 3000,
        proxy: {
            '/api': 'http://localhost:8080'
        }
    },
    resolve: {
        alias: {
            '@': '/src'
        }
    }
});
```

### Parcel (Zero Configuration)
```bash
# Install parcel
npm install --save-dev parcel

# Run parcel
npx parcel src/index.html

# Build for production
npx parcel build src/index.html
```

## Transpilers

### Babel
```bash
# Install Babel
npm install --save-dev @babel/core @babel/cli @babel/preset-env
npm install --save @babel/polyfill
```

```json
// .babelrc
{
    "presets": [
        ["@babel/preset-env", {
            "targets": {
                "browsers": ["last 2 versions"]
            },
            "useBuiltIns": "usage",
            "corejs": 3
        }]
    ],
    "plugins": [
        "@babel/plugin-proposal-class-properties",
        "@babel/plugin-proposal-object-rest-spread"
    ]
}
```

### TypeScript
```bash
# Install TypeScript
npm install --save-dev typescript @types/node
npm install --save-dev @types/react @types/react-dom  # For React
```

```json
// tsconfig.json
{
    "compilerOptions": {
        "target": "ES2020",
        "module": "ESNext",
        "lib": ["ES2020", "DOM"],
        "declaration": true,
        "outDir": "./dist",
        "rootDir": "./src",
        "strict": true,
        "esModuleInterop": true,
        "skipLibCheck": true,
        "forceConsistentCasingInFileNames": true,
        "moduleResolution": "node",
        "allowSyntheticDefaultImports": true,
        "resolveJsonModule": true
    },
    "include": ["src/**/*"],
    "exclude": ["node_modules", "dist"]
}
```

```typescript
// src/types.ts
export interface User {
    id: number;
    name: string;
    email: string;
    age?: number;
}

export interface ApiResponse<T> {
    data: T;
    status: number;
    message: string;
}

// src/user.ts
import { User, ApiResponse } from './types';

class UserService {
    private baseUrl: string;

    constructor(baseUrl: string) {
        this.baseUrl = baseUrl;
    }

    async getUser(id: number): Promise<ApiResponse<User>> {
        const response = await fetch(`${this.baseUrl}/users/${id}`);
        const data = await response.json();
        
        return {
            data,
            status: response.status,
            message: response.statusText
        };
    }
}
```

## Testing Tools

### Jest
```bash
# Install Jest
npm install --save-dev jest

# Install additional packages
npm install --save-dev @types/jest ts-jest  # For TypeScript
npm install --save-dev @testing-library/react  # For React testing
```

```javascript
// jest.config.js
module.exports = {
    testEnvironment: 'jsdom',
    setupFilesAfterEnv: ['<rootDir>/src/setupTests.js'],
    moduleNameMapping: {
        '^@/(.*)$': '<rootDir>/src/$1'
    },
    collectCoverageFrom: [
        'src/**/*.{js,jsx,ts,tsx}',
        '!src/**/*.d.ts',
        '!src/index.js'
    ],
    coverageThreshold: {
        global: {
            branches: 80,
            functions: 80,
            lines: 80,
            statements: 80
        }
    }
};
```

```javascript
// src/math.test.js
import { sum, multiply, divide } from './math';

describe('Math functions', () => {
    test('adds 1 + 2 to equal 3', () => {
        expect(sum(1, 2)).toBe(3);
    });

    test('multiplies 3 * 4 to equal 12', () => {
        expect(multiply(3, 4)).toBe(12);
    });

    test('divides 10 / 2 to equal 5', () => {
        expect(divide(10, 2)).toBe(5);
    });

    test('throws error when dividing by zero', () => {
        expect(() => divide(10, 0)).toThrow('Cannot divide by zero');
    });
});
```

### Vitest (Modern Testing Framework)
```bash
# Install Vitest
npm install --save-dev vitest jsdom

# Run tests
npx vitest
npx vitest watch
npx vitest run
```

```javascript
// vitest.config.ts
import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [react()],
    test: {
        environment: 'jsdom',
        globals: true
    }
});
```

## Code Quality Tools

### ESLint
```bash
# Install ESLint
npm install --save-dev eslint

# Initialize ESLint
npx eslint --init

# Install additional configs
npm install --save-dev eslint-config-airbnb eslint-plugin-react
```

```json
// .eslintrc.json
{
    "env": {
        "browser": true,
        "es2021": true,
        "node": true
    },
    "extends": [
        "eslint:recommended",
        "@typescript-eslint/recommended",
        "plugin:react/recommended",
        "plugin:react-hooks/recommended"
    ],
    "parser": "@typescript-eslint/parser",
    "parserOptions": {
        "ecmaFeatures": {
            "jsx": true
        },
        "ecmaVersion": 12,
        "sourceType": "module"
    },
    "plugins": [
        "react",
        "@typescript-eslint",
        "react-hooks"
    ],
    "rules": {
        "indent": ["error", 2],
        "linebreak-style": ["error", "unix"],
        "quotes": ["error", "single"],
        "semi": ["error", "always"],
        "no-unused-vars": "warn",
        "no-console": "warn",
        "react/prop-types": "off"
    },
    "settings": {
        "react": {
            "version": "detect"
        }
    }
}
```

### Prettier
```bash
# Install Prettier
npm install --save-dev prettier eslint-config-prettier

# Create .prettierrc
echo '{}' > .prettierrc
```

```json
// .prettierrc
{
    "semi": true,
    "trailingComma": "es5",
    "singleQuote": true,
    "printWidth": 80,
    "tabWidth": 2,
    "useTabs": false
}
```

### Husky and lint-staged
```bash
# Install Husky
npm install --save-dev husky lint-staged

# Initialize Husky
npx husky install
npx husky add .husky/pre-commit "npx lint-staged"
```

```json
// package.json
{
    "lint-staged": {
        "src/**/*.{js,jsx,ts,tsx}": [
            "eslint --fix",
            "prettier --write"
        ],
        "src/**/*.{css,scss,md}": [
            "prettier --write"
        ]
    },
    "scripts": {
        "lint": "eslint src --ext .js,.jsx,.ts,.tsx",
        "lint:fix": "eslint src --ext .js,.jsx,.ts,.tsx --fix",
        "format": "prettier --write src/**/*.{js,jsx,ts,tsx,css,scss,md}"
    }
}
```

## Development Tools

### Chrome DevTools
```javascript
// Console debugging
console.log('Debug info');
console.table(users);
console.group('Grouped logs');
console.log('Inside group');
console.groupEnd();

// Performance profiling
console.time('Operation');
// ... code to measure
console.timeEnd('Operation');

// Memory debugging
console.memory; // Memory usage info
```

### VS Code Extensions
```json
// .vscode/extensions.json
{
    "recommendations": [
        "esbenp.prettier-vscode",
        "dbaeumer.vscode-eslint",
        "ms-vscode.vscode-typescript-next",
        "bradlc.vscode-tailwindcss",
        "formulahendry.auto-rename-tag",
        "christian-kohler.path-intellisense",
        "ms-vscode.vscode-json",
        "yzhang.markdown-all-in-one"
    ]
}
```

```json
// .vscode/settings.json
{
    "editor.formatOnSave": true,
    "editor.defaultFormatter": "esbenp.prettier-vscode",
    "editor.codeActionsOnSave": {
        "source.fixAll.eslint": true
    },
    "emmet.includeLanguages": {
        "javascript": "javascriptreact"
    },
    "files.associations": {
        "*.jsx": "javascriptreact"
    }
}
```

## Performance Tools

### Lighthouse
```bash
# Install Lighthouse CLI
npm install -g lighthouse

# Run Lighthouse
lighthouse https://example.com --output html --output-path ./report.html
```

### Bundle Analysis
```bash
# Install webpack-bundle-analyzer
npm install --save-dev webpack-bundle-analyzer

# Add to webpack config
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

module.exports = {
    plugins: [
        new BundleAnalyzerPlugin({
            analyzerMode: 'static',
            openAnalyzer: false
        })
    ]
};
```

## Deployment Tools

### Docker
```dockerfile
# Dockerfile
FROM node:16-alpine

WORKDIR /app

COPY package*.json ./
RUN npm ci --only=production

COPY . .

EXPOSE 3000

CMD ["npm", "start"]
```

```yaml
# docker-compose.yml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "3000:3000"
    environment:
      - NODE_ENV=production
    volumes:
      - ./logs:/app/logs
```

### CI/CD with GitHub Actions
```yaml
# .github/workflows/ci.yml
name: CI/CD Pipeline

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        node-version: [16.x, 18.x]
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Use Node.js ${{ matrix.node-version }}
      uses: actions/setup-node@v3
      with:
        node-version: ${{ matrix.node-version }}
        cache: 'npm'
    
    - name: Install dependencies
      run: npm ci
    
    - name: Run linting
      run: npm run lint
    
    - name: Run tests
      run: npm test
    
    - name: Build
      run: npm run build
    
    - name: Upload coverage
      uses: codecov/codecov-action@v3
```

## Modern Development Workflow

### Package.json Scripts
```json
{
    "scripts": {
        "dev": "vite",
        "build": "vite build",
        "preview": "vite preview",
        "test": "vitest",
        "test:ui": "vitest --ui",
        "test:coverage": "vitest --coverage",
        "lint": "eslint src --ext .js,.jsx,.ts,.tsx",
        "lint:fix": "eslint src --ext .js,.jsx,.ts,.tsx --fix",
        "format": "prettier --write src/**/*.{js,jsx,ts,tsx,css,scss,md}",
        "type-check": "tsc --noEmit",
        "prepare": "husky install"
    }
}
```

### Environment Configuration
```javascript
// config/environments.js
const isDevelopment = process.env.NODE_ENV === 'development';
const isProduction = process.env.NODE_ENV === 'production';
const isTest = process.env.NODE_ENV === 'test';

export const config = {
    apiUrl: process.env.VITE_API_URL || 'http://localhost:3001',
    environment: process.env.NODE_ENV || 'development',
    isDevelopment,
    isProduction,
    isTest
};
```
