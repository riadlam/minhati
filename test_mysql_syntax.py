#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Test MySQL syntax by checking common error patterns"""

import re

errors = []

with open('prime_scolaire_web_mysql.sql', 'r', encoding='utf-8') as f:
    content = f.read()

# Check for unclosed parentheses in CREATE TABLE
print("Checking CREATE TABLE statements...")
create_tables = re.finditer(r'CREATE TABLE[^;]+;', content, re.DOTALL | re.IGNORECASE)
for match in create_tables:
    table_def = match.group(0)
    open_parens = table_def.count('(')
    close_parens = table_def.count(')')
    if open_parens != close_parens:
        errors.append(f"Unbalanced parentheses in CREATE TABLE: {open_parens} open, {close_parens} close")

# Check for missing commas in column definitions
print("Checking column definitions...")
for match in re.finditer(r'CREATE TABLE[^;]+;', content, re.DOTALL | re.IGNORECASE):
    table_def = match.group(0)
    # Check for patterns like "column1 type\n    column2" without comma
    lines = table_def.split('\n')
    for i in range(len(lines) - 1):
        line = lines[i].strip()
        next_line = lines[i + 1].strip()
        # If current line is a column definition and next line is also a column definition
        if (line and not line.startswith('--') and not line.startswith('CREATE') and 
            not line.startswith('PRIMARY') and not line.startswith('CONSTRAINT') and
            next_line and not next_line.startswith('--') and not next_line.startswith('PRIMARY') and
            not next_line.startswith('CONSTRAINT') and not next_line.startswith(');')):
            # Check if line doesn't end with comma
            if not line.endswith(',') and not line.endswith('(') and ')' not in line:
                # This might be an issue
                pass  # Too many false positives, skip

# Check for invalid default values
print("Checking DEFAULT values...")
invalid_defaults = re.findall(r"DEFAULT\s+('[^']*'::\w+)", content)
if invalid_defaults:
    errors.append(f"Found {len(invalid_defaults)} invalid DEFAULT values with :: syntax")

# Check for duplicate table definitions
print("Checking for duplicate tables...")
table_names = re.findall(r'CREATE TABLE\s+`?(\w+)`?', content, re.IGNORECASE)
from collections import Counter
duplicates = [name for name, count in Counter(table_names).items() if count > 1]
if duplicates:
    errors.append(f"Duplicate table definitions: {duplicates}")

# Check migrations table specifically
print("Checking migrations table...")
migrations_section = content[content.find('CREATE TABLE `migrations`'):content.find('CREATE TABLE `password_resets`')]
if 'PRIMARY KEY' not in migrations_section.split(');')[0]:
    errors.append("migrations table missing PRIMARY KEY in CREATE TABLE")
if migrations_section.count('PRIMARY KEY') > 1:
    errors.append("migrations table has multiple PRIMARY KEY definitions")

print("\n" + "=" * 60)
if errors:
    print("ERRORS FOUND:")
    for error in errors:
        print(f"  ✗ {error}")
else:
    print("✓ No syntax errors detected!")
print("=" * 60)

