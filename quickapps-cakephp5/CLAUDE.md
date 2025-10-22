# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a CakePHP 5.x application skeleton that is part of a migration project from CakePHP 3 (QuickAppsCMS) to CakePHP 5. The application includes migrated plugins from the legacy QuickAppsCMS system.

## Development Environment

### Local Development (Docker)
- **Web**: http://localhost:8090
- **Database**: MySQL 8.0 on localhost:3307
- **phpMyAdmin**: http://localhost:8091
- **PHP**: 8.2+
- **Working directory**: `/src` (all commands should be run from here)

### Docker Commands
```bash
# Start environment (from parent directory)
docker-compose up -d

# Access web container
docker exec -it quickapps5-web bash

# Access database
docker exec quickapps5-db mysql -u quickapps5 -pquickapps123 quickapps5

# Reset database (clean testing)
docker exec quickapps5-db mysql -u root -prootpassword -e "DROP DATABASE quickapps5; CREATE DATABASE quickapps5 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Essential Commands

**IMPORTANT**: This project runs inside Docker. All PHP commands must be executed inside the `quickapps5-web` container.

### Running Commands in Docker Container

All development commands must be run inside the Docker container:

```bash
# Run all quality checks (tests + coding standards)
docker exec quickapps5-web composer check -d /var/www/html

# Run PHPUnit tests
docker exec quickapps5-web composer test -d /var/www/html

# Check coding standards (phpcs)
docker exec quickapps5-web composer cs-check -d /var/www/html

# Fix coding standards automatically (phpcbf)
docker exec quickapps5-web composer cs-fix -d /var/www/html

# Run PHPStan static analysis (level 8)
docker exec quickapps5-web composer stan -d /var/www/html
```

### Running Tests

```bash
# All tests
docker exec quickapps5-web vendor/bin/phpunit -c /var/www/html

# Specific test file
docker exec quickapps5-web vendor/bin/phpunit /var/www/html/tests/TestCase/Controller/UsersControllerTest.php

# Specific test method
docker exec quickapps5-web vendor/bin/phpunit --filter testLogin /var/www/html/tests/TestCase/Controller/UsersControllerTest.php

# Specific test class (recommended for development)
docker exec quickapps5-web vendor/bin/phpunit /var/www/html/tests/TestCase/Service/VacationCalculatorTest.php
```

### Alternative: Interactive Shell

You can also enter the container and run commands directly:

```bash
# Enter container
docker exec -it quickapps5-web bash

# Then run commands normally from /var/www/html
cd /var/www/html
composer test
vendor/bin/phpunit tests/TestCase/Service/VacationCalculatorTest.php
```

## Architecture

### Directory Structure

```
src/
├── config/          # Application configuration
├── plugins/         # CakePHP plugins (migrated from QuickAppsCMS)
│   ├── Cms/        # Core CMS functionality plugin
│   └── eav/        # Entity-Attribute-Value plugin
├── src/            # Application source code
│   ├── Controller/ # Controllers
│   ├── Model/      # Tables and Entities
│   └── View/       # View classes
├── templates/      # View templates
├── tests/          # PHPUnit tests
└── webroot/        # Public files (CSS, JS, images)
```

### Plugin Architecture

This application follows CakePHP 5 plugin architecture. Plugins are self-contained modules located in `plugins/`:

- **Cms Plugin**: Core CMS functionality migrated from QuickAppsCMS
- **EAV Plugin**: Entity-Attribute-Value model for flexible content attributes

Each plugin has its own:
- `src/` - Plugin source code (Controllers, Models, etc.)
- `config/` - Plugin configuration
- `tests/` - Plugin-specific tests
- `composer.json` - Plugin dependencies

### Configuration

- **Environment variables**: `.env` file in parent directory
- **Main config**: `config/app.php` (application-wide settings)
- **Local config**: `config/app_local.php` (environment-specific, not in git)
- **Database**: Configured via `DATABASE_URL` environment variable

## Code Quality Standards

### Coding Standards
- **Standard**: CakePHP coding standards (PSR-12 based)
- **Configuration**: `phpcs.xml`
- **Excludes**: Return type hints on controllers are excluded

### Static Analysis
- **Tool**: PHPStan
- **Level**: 8 (strict)
- **Configuration**: `phpstan.neon`
- **Scope**: `src/` directory only

### Testing
- **Framework**: PHPUnit 11.x / 12.x
- **Configuration**: `phpunit.xml.dist`
- **Test location**: `tests/TestCase/`
- **Plugin tests**: `plugins/*/tests/`

## MCP PHPUnit Integration

This project has **PHPUnit MCP** configured for enhanced AI-assisted testing workflows.

### Important Notes for MCP Usage

**CRITICAL**: When using MCP PHPUnit tools, do NOT pass absolute paths to test files or directories. The MCP server runs inside the Docker container at `/var/www/html`, so paths are already relative to the working directory.

✅ **Correct MCP usage**:
```
# Run all tests
mcp__phpunit_run_tests()

# Run specific test file (relative path)
mcp__phpunit_run_tests(path: "tests/TestCase/Service/VacationCalculatorTest.php")

# Run specific test class
mcp__phpunit_run_tests(filter: "VacationCalculatorTest")
```

❌ **Incorrect MCP usage**:
```
# DO NOT use absolute paths from host machine
mcp__phpunit_run_tests(path: "/Users/alex/work/projects/.../tests/...")
```

### MCP vs Bash Commands

- **MCP tools**: Use relative paths (tests are already in `/var/www/html`)
- **Bash commands**: Use absolute paths inside container (`/var/www/html/tests/...`)

## Common Development Workflows

### Adding a New Plugin
1. Create plugin structure in `plugins/YourPlugin/`
2. Add plugin namespace to autoload in composer.json
3. Load plugin in `src/Application.php`
4. Add plugin tests to `phpunit.xml.dist`

### Database Migrations
```bash
# Create migration
bin/cake bake migration CreateUsers

# Run migrations
bin/cake migrations migrate

# Rollback
bin/cake migrations rollback
```

### Working with Plugins

When working inside a plugin (e.g., `plugins/Cms/`):
- Plugin follows same MVC structure as main app
- Plugin config loaded via `config/bootstrap.php`
- Plugin routes defined in `config/routes.php`
- Plugin tests run as part of main test suite

## Docker vs Local Development

**Docker (Recommended for consistency)**:
- Guarantees PHP 8.2+ with required extensions (intl, mbstring, etc.)
- MySQL 8.0 with proper configuration
- Matches production environment

**Local Development**:
- Requires PHP 8.2+ with intl extension
- Must configure MySQL connection manually
- Use `bin/cake server -p 8765` for built-in server

## Migration Context

This application is the **target** for migrating QuickAppsCMS from CakePHP 3.3.16. When working on migration tasks:

1. Source application runs on http://localhost:8080 (CakePHP 3)
2. This target application runs on http://localhost:8090 (CakePHP 5)
3. Plugins are being migrated incrementally from `vendor/quickapps-plugins/` to `src/plugins/`
4. Database schemas may differ between MySQL 5.7 (source) and MySQL 8.0 (target)
