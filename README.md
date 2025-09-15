# Fullstack PHP-React E-commerce Project

A modern, full-stack e-commerce application featuring a PHP GraphQL backend and a React (Next.js) frontend. Designed for scalable product catalog management with multi-category, multi-currency, and flexible attribute support.

---

## 🚀 Features
- **GraphQL API** for flexible data querying
- **Multi-category Products** (Clothes, Tech, etc.)
- **Product Attributes** (text, swatch, etc.)
- **Multi-currency Pricing**
- **Image Galleries**
- **Category Management**
- **Order Management**
- **CORS Support**
- **Clean Architecture**

---

## 🏗️ Architecture

### Backend (PHP)
- **PHP 8+**
- **GraphQL**: webonyx/graphql-php
- **Routing**: FastRoute
- **Database**: MySQL/MariaDB (PDO abstraction)
- **Composer** for dependencies

**Key Directories:**
- `backend/src/Models/`: Product, Category, Attribute models
- `backend/src/GraphQL/`: Schema, Types, Resolvers
- `backend/public/`: API entry point

### Frontend (React/Next.js)
- **Next.js 15** (React 19)
- **TypeScript**
- **Tailwind CSS**
- **Apollo Client** for GraphQL
- **Radix UI** & custom components

**Key Directories:**
- `frontend/app/`: Next.js app router
- `frontend/components/`: UI components
- `frontend/contexts/`: React context providers
- `frontend/lib/`: Apollo client, utilities

---

## 📦 Project Structure
```
fullstack-php-react/
├── backend/
│   ├── src/
│   ├── public/
│   └── composer.json
├── frontend/
│   ├── app/
│   ├── components/
│   ├── contexts/
│   └── package.json
└── vendor/
```

---

## 🛠️ Setup & Installation

### Backend
1. Install dependencies:
   ```bash
   cd backend
   composer install
   ```
2. Configure your database in `.env` or Railway dashboard.
3. Import schema:
   ```bash
   mysql -u <user> -p <database> < create_db.sql
   ```
4. Start backend server:
   ```bash
   bash start_backend.sh
   ```

### Frontend
1. Install dependencies:
   ```bash
   cd frontend
   npm install
   ```
2. Start development server:
   ```bash
   npm run dev
   ```
3. Open [http://localhost:3000](http://localhost:3000)

---

## 🧪 Testing
- Backend: Run GraphQL tests with provided PHP scripts (see `backend/README.md`)
- Frontend: Use Next.js built-in testing and linting

---

## 🚀 Deployment
- Ready for Railway deployment (see `RAILWAY_DEPLOYMENT.md`)
- Set environment variables for database connection

---

## 📚 Documentation
- Backend API: See `backend/GRAPHQL_API.md`
- Frontend: See `frontend/README.md`

---

## 🤝 Contributing
Pull requests and issues are welcome. See individual `README.md` files for more details.

---

## 📄 License
MIT

---

**Built with ❤️ using PHP, GraphQL, React, and Next.js**
