# Contributing to Laravel DB Craft

First off, thank you for considering contributing to Laravel DB Craft! It's people like you that make this package better for everyone. 

We welcome contributions from the community and are pleased to have you join us. This document will guide you through the contribution process.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)
- [Reporting Bugs](#reporting-bugs)
- [Suggesting Features](#suggesting-features)

## Code of Conduct

This project and everyone participating in it is governed by our commitment to providing a welcoming and inclusive environment. By participating, you are expected to uphold this standard. Please be respectful and constructive in your interactions.

## How Can I Contribute?

There are many ways to contribute to Laravel DB Craft:

- **Report bugs** - Help us identify issues
- **Suggest features** - Share your ideas for improvements
- **Write documentation** - Improve or expand our docs
- **Submit code** - Fix bugs or implement new features
- **Review pull requests** - Help maintain code quality
- **Share the project** - Star the repo and spread the word

## Getting Started

### Prerequisites

Before you begin, ensure you have:

- PHP 8.0 or higher
- Composer
- Git
- Laravel 9.x, 10.x, or 11.x (for testing)
- A database system (MySQL, PostgreSQL, or SQLite)

### Setting Up Your Development Environment

1. **Fork the Repository**

   Visit [https://github.com/robinNcode/lara-db-craft](https://github.com/robinNcode/lara-db-craft) and click the "Fork" button in the top-right corner. This creates a copy of the repository in your GitHub account.

2. **Clone Your Fork**

   ```bash
   git clone https://github.com/YOUR-USERNAME/lara-db-craft.git
   cd lara-db-craft
   ```

3. **Add Upstream Remote**

   This allows you to sync your fork with the main repository:

   ```bash
   git remote add upstream https://github.com/robinNcode/lara-db-craft.git
   git remote -v  # Verify the new remote
   ```

4. **Install Dependencies**

   ```bash
   composer install
   ```

5. **Create a Test Laravel Application (Optional but Recommended)**

   For testing your changes in a real Laravel environment:

   ```bash
   cd ..
   composer create-project laravel/laravel test-app
   cd test-app
   
   # Link your local package
   composer config repositories.local '{"type": "path", "url": "../lara-db-craft"}'
   composer require robinncode/laravel-db-craft:@dev
   ```

## Development Workflow

### Step 1: Sync Your Fork

Before starting work, ensure your fork is up-to-date with the main repository:

```bash
# Fetch the latest changes from upstream
git fetch upstream

# Switch to your main branch
git checkout main

# Merge upstream changes
git merge upstream/main

# Push updates to your fork
git push origin main
```

### Step 2: Create a New Branch

Always create a new branch for your work. Never work directly on the `main` branch:

```bash
# Create and switch to a new branch
git checkout -b feature/your-feature-name

# For bug fixes, use:
git checkout -b fix/bug-description

# For documentation, use:
git checkout -b docs/what-you-are-documenting
```

**Branch Naming Convention:**
- `feature/` - New features or enhancements
- `fix/` - Bug fixes
- `docs/` - Documentation changes
- `refactor/` - Code refactoring
- `test/` - Adding or updating tests

### Step 3: Make Your Changes

Now you can start coding! Keep these guidelines in mind:

- Write clean, readable code
- Follow the existing code style
- Add comments for complex logic
- Write tests for new features
- Update documentation as needed

### Step 4: Test Your Changes

Before committing, thoroughly test your changes:

```bash
# Run tests (if available)
composer test

# Test in a Laravel application
# (Use the test-app you created earlier)
```

Manually verify:
- Your changes work as expected
- No existing functionality is broken
- Edge cases are handled properly

### Step 5: Commit Your Changes

Stage your changes:

```bash
# Add specific files
git add path/to/file

# Or add all changes
git add .

# Check what will be committed
git status
```

Commit with a descriptive message:

```bash
git commit -m "Add feature: description of what you did"
```

See [Commit Guidelines](#commit-guidelines) for more details on writing good commit messages.

### Step 6: Rebase with Main Repository

Before pushing, rebase your branch with the latest changes from upstream to ensure a clean history:

```bash
# Fetch latest changes from upstream
git fetch upstream

# Rebase your branch onto upstream/main
git rebase upstream/main
```

**If conflicts occur:**

```bash
# Fix conflicts in your editor
# After fixing, stage the resolved files
git add path/to/resolved/file

# Continue the rebase
git rebase --continue

# If you want to abort the rebase
git rebase --abort
```

### Step 7: Push to Your Fork

Push your changes to your forked repository:

```bash
# First time pushing this branch
git push -u origin feature/your-feature-name

# Subsequent pushes
git push

# If you've rebased and need to force push (use with caution)
git push --force-with-lease origin feature/your-feature-name
```

### Step 8: Create a Pull Request

1. Go to your fork on GitHub: `https://github.com/YOUR-USERNAME/lara-db-craft`

2. Click the "Compare & pull request" button (usually appears after pushing)

3. Ensure the base repository is `robinNcode/lara-db-craft` and the base branch is `main`

4. Fill out the pull request template:
   - **Title**: Clear, concise description of your changes
   - **Description**: 
     - What changes did you make?
     - Why did you make these changes?
     - How did you test them?
     - Any breaking changes?
     - Related issues (if any)

5. Click "Create pull request"

**Example PR Description:**

```markdown
## Description
Added support for generating migrations with enum column types.

## Motivation
Many legacy databases use ENUM types, but the package didn't handle them properly.

## Changes Made
- Added enum type detection in ColumnTypeMapper
- Added enum value extraction from database schema
- Added tests for enum column generation

## Testing
- Created test database with enum columns
- Verified migration generation includes enum values
- Tested with MySQL and PostgreSQL

## Breaking Changes
None

## Related Issues
Fixes #42
```

## Coding Standards

### PHP Code Style

- Follow PSR-12 coding standards
- Use type hints for parameters and return types
- Write descriptive variable and function names
- Keep functions small and focused (single responsibility)
- Add PHPDoc blocks for classes and public methods

**Example:**

```php
<?php

namespace RobinNcode\LaravelDbCraft\Services;

use Illuminate\Support\Facades\DB;

class MigrationGenerator
{
    /**
     * Generate migration for the specified table.
     *
     * @param string $tableName The name of the table
     * @param string|null $connection The database connection
     * @return string The generated migration content
     */
    public function generate(string $tableName, ?string $connection = null): string
    {
        // Implementation
    }
}
```

### Formatting

- Indentation: 4 spaces (no tabs)
- Line length: Try to keep under 120 characters
- Blank lines: Use them to separate logical sections
- Braces: Opening brace on the same line

```php
// Good
if ($condition) {
    // code
}

// Bad
if ($condition)
{
    // code
}
```

## Commit Guidelines

### Commit Message Format

```
<type>: <subject>

<body (optional)>

<footer (optional)>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, no logic change)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

### Examples

```bash
# Good commit messages
git commit -m "feat: add support for PostgreSQL enum types"
git commit -m "fix: handle nullable foreign keys correctly"
git commit -m "docs: update installation instructions"
git commit -m "refactor: simplify column type mapping logic"

# Bad commit messages
git commit -m "fixed stuff"
git commit -m "updates"
git commit -m "WIP"
```

### Detailed Commit Message Example

```
feat: add support for generating views

Added functionality to generate migrations for database views,
not just tables. This is useful for reverse engineering databases
that use views extensively.

Changes:
- Added view detection in schema inspector
- Created ViewMigrationGenerator class
- Added tests for view migration generation
- Updated documentation

Closes #123
```

## Pull Request Process

1. **Update Documentation**: If you've added features, update the README.md

2. **Add Tests**: Ensure your code is tested (if applicable)

3. **Update Changelog**: Add your changes to CHANGELOG.md (if exists)

4. **One Feature Per PR**: Keep pull requests focused on a single feature or fix

5. **Respond to Feedback**: Be open to feedback and make requested changes promptly

6. **Keep It Updated**: If the main branch moves ahead while your PR is open, rebase your branch:
   ```bash
   git fetch upstream
   git rebase upstream/main
   git push --force-with-lease origin your-branch-name
   ```

7. **Be Patient**: Maintainers will review your PR as soon as possible

### PR Review Checklist

Before submitting, ensure:

- [ ] Code follows the project's coding standards
- [ ] All tests pass
- [ ] Documentation is updated
- [ ] Commit messages are clear and descriptive
- [ ] Branch is rebased with the latest main
- [ ] No merge conflicts
- [ ] PR description is complete and clear

## Reporting Bugs

### Before Reporting

- Check if the bug has already been reported in [Issues](https://github.com/robinNcode/lara-db-craft/issues)
- Verify it's actually a bug and not expected behavior
- Test with the latest version of the package

### Creating a Bug Report

Include the following information:

- **Clear Title**: Descriptive summary of the issue
- **Description**: Detailed explanation of the problem
- **Steps to Reproduce**: Step-by-step instructions
- **Expected Behavior**: What should happen
- **Actual Behavior**: What actually happens
- **Environment**:
  - Laravel version
  - PHP version
  - Database type and version
  - Package version
- **Code Samples**: Minimal code to reproduce the issue
- **Error Messages**: Full error messages and stack traces

**Bug Report Template:**

```markdown
## Bug Description
Brief description of the bug

## Steps to Reproduce
1. Step one
2. Step two
3. Step three

## Expected Behavior
What should happen

## Actual Behavior
What actually happens

## Environment
- Laravel Version: 10.x
- PHP Version: 8.2
- Database: MySQL 8.0
- Package Version: 1.0.0

## Code Sample
```php
// Minimal code to reproduce
```

## Error Messages
```
// Full error message
```

## Additional Context
Any other relevant information
```

## Suggesting Features

We love feature suggestions! Before suggesting:

- Check if the feature has already been suggested
- Ensure it aligns with the package's goals
- Consider if it would benefit most users

### Feature Request Template

```markdown
## Feature Description
Clear description of the proposed feature

## Problem It Solves
What problem does this feature address?

## Proposed Solution
How should this feature work?

## Alternatives Considered
Other approaches you've thought about

## Example Usage
```php
// How the feature would be used
```

## Additional Context
Any other relevant information
```

## Questions?

If you have questions about contributing:

- Open a [Discussion](https://github.com/robinNcode/lara-db-craft/discussions)
- Check existing issues and pull requests
- Reach out to maintainers

## Recognition

All contributors will be recognized in our README.md. Your contributions, no matter how small, are valued and appreciated!

---

**Thank you for contributing to Laravel DB Craft! Together, we're making database management easier for the Laravel community.** 🚀

---

*This guide is inspired by open-source contribution best practices. If you have suggestions for improving this document, please submit a pull request!*
