#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Validate MySQL file for common import issues"""

import re

issues = []
warnings = []

with open('prime_scolaire_web_mysql.sql', 'r', encoding='utf-8') as f:
    lines = f.readlines()

print("=" * 60)
print("VALIDATING MySQL FILE")
print("=" * 60)

# Check for PostgreSQL-specific syntax
for i, line in enumerate(lines, 1):
    # Check for CREATE TYPE (should be converted to ENUM)
    if 'CREATE TYPE' in line.upper() and 'AS ENUM' not in line.upper():
        if '--' not in line[:10]:  # Not a comment
            issues.append(f"Line {i}: CREATE TYPE found (not converted)")
    
    # Check for PostgreSQL schema references
    if re.search(r'\bpublic\.\w+', line) and '--' not in line[:10]:
        issues.append(f"Line {i}: PostgreSQL schema prefix 'public.' found")
    
    # Check for OWNER TO (should be removed)
    if 'OWNER TO' in line.upper() and '--' not in line[:10]:
        issues.append(f"Line {i}: OWNER TO clause found")
    
    # Check for PostgreSQL functions
    if 'pg_catalog' in line.lower() and '--' not in line[:10]:
        issues.append(f"Line {i}: pg_catalog function found")
    
    # Check for nextval (should be AUTO_INCREMENT)
    if 'nextval' in line.lower() and '--' not in line[:10]:
        issues.append(f"Line {i}: nextval() function found")
    
    # Check for COPY command (should be INSERT)
    if line.strip().startswith('COPY ') and 'FROM stdin' in line:
        issues.append(f"Line {i}: COPY command found (not converted to INSERT)")
    
    # Check for missing semicolons on CREATE TABLE
    if 'CREATE TABLE' in line.upper():
        # Find the closing of this CREATE TABLE
        brace_count = 0
        found_brace = False
        for j in range(i, min(i+50, len(lines))):
            if '{' in lines[j-1] or '(' in lines[j-1]:
                found_brace = True
                brace_count += lines[j-1].count('(') - lines[j-1].count(')')
            if found_brace:
                brace_count += lines[j-1].count('(') - lines[j-1].count(')')
                if ');' in lines[j-1] or ';' in lines[j-1]:
                    break
        else:
            warnings.append(f"Line {i}: CREATE TABLE might be missing closing semicolon")

# Check for duplicate PRIMARY KEY on migrations
migrations_pk_count = 0
in_migrations_create = False
for i, line in enumerate(lines, 1):
    if 'CREATE TABLE `migrations`' in line:
        in_migrations_create = True
    if in_migrations_create and 'PRIMARY KEY' in line:
        migrations_pk_count += 1
    if in_migrations_create and ');' in line:
        in_migrations_create = False

# Check for ALTER TABLE migrations PRIMARY KEY
alter_migrations_pk = False
for line in lines:
    if 'ALTER TABLE `migrations`' in line and 'PRIMARY KEY' in ' '.join(lines[lines.index(line):lines.index(line)+5]):
        alter_migrations_pk = True
        break

if migrations_pk_count > 1 or alter_migrations_pk:
    issues.append(f"migrations table has duplicate PRIMARY KEY definitions")

# Check for proper table name backticks
for i, line in enumerate(lines, 1):
    if 'CREATE TABLE' in line.upper() and '`' not in line:
        table_match = re.search(r'CREATE TABLE\s+(\w+)', line, re.IGNORECASE)
        if table_match and table_match.group(1) != 'migrations':  # migrations handled separately
            warnings.append(f"Line {i}: Table name missing backticks: {table_match.group(1)}")

print(f"\nFound {len(issues)} issues and {len(warnings)} warnings\n")

if issues:
    print("ISSUES (must fix):")
    print("-" * 60)
    for issue in issues[:20]:  # Show first 20
        print(f"  ✗ {issue}")
    if len(issues) > 20:
        print(f"  ... and {len(issues) - 20} more issues")
else:
    print("✓ No critical issues found!")

if warnings:
    print("\nWARNINGS (may cause issues):")
    print("-" * 60)
    for warning in warnings[:10]:  # Show first 10
        print(f"  ⚠ {warning}")
    if len(warnings) > 10:
        print(f"  ... and {len(warnings) - 10} more warnings")

print("\n" + "=" * 60)
if not issues:
    print("✓ File appears to be valid for MySQL import!")
else:
    print("✗ File has issues that need to be fixed before import")
print("=" * 60)

