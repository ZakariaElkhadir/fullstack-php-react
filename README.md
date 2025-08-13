# Fullstack PHP + React Ecommerce-website

A modern fullstack web application built with PHP backend and React TypeScript frontend.

## 🚀 Tech Stack

### Frontend
- **React** with TypeScript
- **Vite** for fast development and building
- **ESLint** for code quality

### Backend
- **PHP** for server-side logic
- RESTful API design

## 📁 Project Structure

```
fullstack-php-react/
├── frontend/          # React TypeScript application
├── backend/           # PHP API server
├── docs/             # Documentation
└── README.md         # This file
```

## 🛠️ Getting Started

### Prerequisites
- Node.js (v18 or higher)
- PHP (v8.0 or higher)
- Composer (for PHP dependencies)
- npm or yarn

### Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

### Backend Setup
```bash
cd backend
composer install
php -S localhost:8000
```

## 📝 Development

### Frontend Development
The React frontend uses Vite for fast HMR and TypeScript for type safety.

Available scripts:
- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run lint` - Run ESLint
- `npm run preview` - Preview production build

### Backend Development
PHP backend provides RESTful API endpoints for the frontend.

## 🔧 ESLint Configuration

For production applications, consider enabling type-aware lint rules:

```js
export default tseslint.config([
  globalIgnores(['dist']),
  {
    files: ['**/*.{ts,tsx}'],
    extends: [
      ...tseslint.configs.recommendedTypeChecked,
      ...tseslint.configs.strictTypeChecked,
      ...tseslint.configs.stylisticTypeChecked,
    ],
    languageOptions: {
      parserOptions: {
        project: ['./tsconfig.node.json', './tsconfig.app.json'],
        tsconfigRootDir: import.meta.dirname,
      },
    },
  },
])
```

## 📚 API Documentation

API documentation will be available at `/docs` when the backend server is running.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

This project is licensed under the MIT License.
