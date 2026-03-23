[![Build](https://img.shields.io/github/actions/workflow/status/tales-from-a-dev/website/ci.yaml?style=for-the-badge)](https://github.com/tales-from-a-dev/website/actions/workflows/ci.yml)
![PHP Version](https://img.shields.io/badge/php-8.5-4f5b93.svg?style=for-the-badge)
![Symfony Version](https://img.shields.io/badge/symfony-7.4-000.svg?style=for-the-badge)
![Tailwind Version](https://img.shields.io/badge/tailwind-4.2-00bcff.svg?style=for-the-badge)
![PostgreSQL Version](https://img.shields.io/badge/postgresql-17-6395be.svg?style=for-the-badge)

# Tales from a Dev

A personal website built with Symfony showcasing development experiences, projects, and technical blog posts.

## 📋 Overview

This is a modern web application built with Symfony 7.4 and PHP 8.5, featuring:
- **Modular Architecture**: Organized into domain modules (Analytics, Contact, Experience, GitHub, Settings, User)
- **Real-time Features**: Mercure integration for live updates
- **Modern Frontend**: Tailwind CSS 4.1 with Symfony AssetMapper and Stimulus
- **Message Queue**: Symfony Messenger with Doctrine transport
- **Caching**: Valkey (Redis-compatible) for application cache
- **Database**: PostgreSQL 17 with Doctrine ORM
- **Server**: FrankenPHP for high-performance PHP serving

## 🛠 Tech Stack

### Backend
- **Language**: PHP 8.5
- **Framework**: Symfony 7.4
- **ORM**: Doctrine ORM 3.5
- **Server**: FrankenPHP
- **Package Manager**: Composer

### Frontend
- **CSS Framework**: Tailwind CSS 4.2
- **JavaScript**: Stimulus (via Symfony UX)
- **Asset Management**: Symfony AssetMapper
- **Components**: Twig Components, Turbo

### Infrastructure
- **Database**: PostgreSQL 17
- **Cache**: Valkey 9.0 (Redis-compatible)
- **Containerization**: Docker & Docker Compose
- **Build Tool**: Make

## 📦 Requirements

- Docker & Docker Compose
- Make
- Git

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/tales-from-a-dev/website.git
cd website
```

### 2. Build Docker Images

```bash
make build
```

### 3. Start Services

```bash
make up
```

For development with Xdebug:
```bash
make up-dev
```

For testing environment:
```bash
make up-test
```

### 4. Install Dependencies

```bash
make install
```

### 5. Setup Database

```bash
make db
```

This command will:
- Create the database
- Apply migrations
- Seed initial data

### 6. Access the Application

- **HTTP**: http://localhost
- **HTTPS**: https://localhost

## 🎯 Available Commands

### Docker Management

| Command | Description |
|---------|-------------|
| `make build` | Build Docker images |
| `make up` | Start containers in detached mode |
| `make up-dev` | Start containers with Xdebug for debugging |
| `make up-test` | Start containers for testing |
| `make up-prod` | Start containers in production mode |
| `make stop` | Stop containers |
| `make restart` | Restart containers |
| `make down` | Stop and remove all containers, networks, volumes |
| `make logs` | Show live logs |
| `make sh` | Connect to PHP container shell |

### Composer

| Command | Description |
|---------|-------------|
| `make install` | Install dependencies |
| `make update` | Update dependencies |
| `make composer c='<command>'` | Run custom composer command |

### Symfony Console

| Command | Description |
|---------|-------------|
| `make sf c='<command>'` | Run Symfony console command |
| `make cc` | Clear cache |
| `make container` | Display all services |
| `make envs` | Display environment variables |
| `make parameters` | Display parameters |
| `make router` | Display all routes |

### Assets

| Command | Description |
|---------|-------------|
| `make importmap` | Install JavaScript dependencies |
| `make asset-build` | Build Tailwind CSS |
| `make asset-watch` | Watch and rebuild assets |
| `make asset-compile` | Compile assets for production |
| `make asset-outdated` | List outdated JavaScript packages |
| `make asset-update` | Update JavaScript packages |
| `make asset-audit` | Check for vulnerabilities in assets |

### Database

| Command | Description |
|---------|-------------|
| `make db` | Create database, apply migrations, and seed data |
| `make db-create` | Create database |
| `make db-migrate` | Run migrations |
| `make db-diff` | Generate migration from entity changes |
| `make db-update` | Force schema update |
| `make db-load` | Load fixtures |
| `make db-seed` | Seed database with initial data |
| `make db-validate` | Validate ORM mapping |
| `make db-test` | Setup test database |

### Testing

| Command | Description |
|---------|-------------|
| `make test` | Run all tests |
| `make test f='<path>'` | Run specific test file |
| `make test-unit` | Run unit tests |
| `make test-functional` | Run functional tests |
| `make test-integration` | Run integration tests |
| `make coverage` | Run tests with code coverage |

### Code Quality

| Command | Description |
|---------|-------------|
| `make phpcsfixer` | Fix PHP coding style |
| `make phpcsfixer-dry` | Check PHP coding style (dry-run) |
| `make phpstan` | Run static analysis |
| `make phpstan-baseline` | Update PHPStan baseline |
| `make rector` | Run Rector refactoring |
| `make rector-dry` | Check Rector suggestions (dry-run) |
| `make twigcsfixer` | Fix Twig coding style |
| `make twigcsfixer-dry` | Check Twig coding style (dry-run) |
| `make fixer` | Fix PHP and Twig coding style |
| `make linter` | Lint Twig, YAML, and validate Doctrine mapping |

## 🔧 Environment Variables

Key environment variables (defined in `.env` and `.env.local`):

### Application
- `APP_ENV`: Application environment (`dev`, `test`, `prod`)
- `APP_SECRET`: Secret key for cryptographic operations
- `SECURE_SCHEME`: URL scheme (`http` or `https`)

### Contact
- `CONTACT_EMAIL`: Contact email address
- `CONTACT_PHONE`: Contact phone number

### GitHub Integration
- `GITHUB_ACCESS_TOKEN`: GitHub API access token
- `GITHUB_USERNAME`: GitHub username

### Database
- `DATABASE_URL`: PostgreSQL connection string
- `POSTGRES_USER`: Database user (default: `app`)
- `POSTGRES_PASSWORD`: Database password
- `POSTGRES_DB`: Database name (default: `app`)
- `POSTGRES_VERSION`: PostgreSQL version (default: `17`)

### Cache
- `CACHE_DSN`: Valkey/Redis connection string

### Messenger
- `MESSENGER_TRANSPORT_DSN`: Message queue transport DSN

### Mailer
- `MAILER_DSN`: Mailer transport DSN

### Notifications
- `FREE_MOBILE_DSN`: Free Mobile notifier DSN

### Mercure (Real-time)
- `MERCURE_URL`: Internal Mercure hub URL
- `MERCURE_PUBLIC_URL`: Public Mercure hub URL
- `MERCURE_JWT_SECRET`: JWT secret for Mercure

## 🧪 Testing

The project uses PHPUnit with three test suites:

### Test Suites
- **Unit**: Fast, isolated tests for individual classes
- **Functional**: Tests for HTTP endpoints and user interactions
- **Integration**: Tests for database interactions and external services

### Running Tests

```bash
# Run all tests
make test

# Run specific suite
make test-unit
make test-functional
make test-integration

# Run specific test file
make test f=tests/Unit/Shared/Domain/ValueObject/AlertTest.php

# Generate code coverage (requires Xdebug)
make coverage
```

### Writing Tests

1. Place tests in `tests/` directory following the same structure as `src/`
2. Test classes must end with `Test`
3. Inherit from `PHPUnit\Framework\TestCase` (unit) or specialized Symfony test cases

## 📁 Project Structure

```
.
├── assets/              # Frontend assets (CSS, JS)
├── bin/                 # Executables (console)
├── config/              # Configuration files
│   ├── packages/        # Bundle configurations
│   └── routes/          # Route definitions
├── docker/              # Docker configuration files
├── fixtures/            # Database fixtures
├── migrations/          # Doctrine migrations
├── public/              # Web root
│   └── index.php        # Application entry point
├── src/                 # Application source code
│   ├── Analytics/       # Analytics module
│   ├── Contact/         # Contact module
│   ├── Experience/      # Experience module
│   ├── GitHub/          # GitHub integration module
│   ├── Settings/        # Settings module
│   ├── Shared/          # Shared/common code
│   └── User/            # User module
├── templates/           # Twig templates
├── tests/               # Test files
│   ├── Fixtures/        # Test fixtures
│   ├── Functional/      # Functional tests
│   ├── Integration/     # Integration tests
│   └── Unit/            # Unit tests
├── translations/        # Translation files
├── var/                 # Cache, logs, sessions
└── vendor/              # Composer dependencies
```

## 🔒 Security

- Never commit `.env.local` or production secrets
- Use Symfony Secrets for sensitive production data
- Change default passwords in production
- Keep dependencies up to date

## 📝 Code Quality Tools

The project enforces code quality through:

- **PHP CS Fixer**: PSR-12 coding standards
- **PHPStan**: Static analysis (Level 8)
- **Rector**: Automated refactoring and upgrades
- **Twig CS Fixer**: Twig template standards
- **Symfony Linters**: YAML and Twig validation

Run all quality checks:
```bash
make linter
make phpstan
make phpcsfixer-dry
make rector-dry
make twigcsfixer-dry
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

Copyright (c) Romain Monteil

## 🔗 Links

- **Repository**: https://github.com/tales-from-a-dev/website
- **CI/CD**: https://github.com/tales-from-a-dev/website/actions

## 📚 Additional Resources

- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [Doctrine Documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [FrankenPHP Documentation](https://frankenphp.dev/)
