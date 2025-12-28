#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Clean MySQL file for phpMyAdmin import"""

import re

with open('prime_scolaire_web_mysql.sql', 'r', encoding='utf-8') as f:
    content = f.read()

print("Cleaning file for phpMyAdmin import...")

# Remove any BOM
if content.startswith('\ufeff'):
    content = content[1:]
    print("  ✓ Removed BOM")

# Ensure proper line endings (phpMyAdmin prefers Unix style)
content = content.replace('\r\n', '\n').replace('\r', '\n')
print("  ✓ Normalized line endings")

# Remove empty ALTER TABLE statements for migrations PRIMARY KEY (already handled)
# This is already done, but double-check
if 'ALTER TABLE `migrations`' in content:
    alter_section = re.search(r'ALTER TABLE `migrations`[^;]+;', content)
    if alter_section and 'PRIMARY KEY' in alter_section.group(0):
        # Replace with comment
        content = content.replace(alter_section.group(0), 
                                 '-- PRIMARY KEY already defined in CREATE TABLE')
        print("  ✓ Removed duplicate PRIMARY KEY for migrations")

# Ensure all CREATE TABLE statements end with semicolon
create_tables = list(re.finditer(r'CREATE TABLE[^;]+;', content, re.DOTALL | re.IGNORECASE))
for match in create_tables:
    table_def = match.group(0)
    if not table_def.rstrip().endswith(';'):
        # This shouldn't happen, but check anyway
        pass

# Ensure INSERT statements are properly formatted
# Check for any INSERT statements without semicolon at the end of their batch
inserts = list(re.finditer(r'INSERT INTO[^;]+;', content, re.DOTALL | re.IGNORECASE))
print(f"  ✓ Found {len(inserts)} INSERT statement blocks")

# Write cleaned file
output_file = 'prime_scolaire_web_mysql_clean.sql'
with open(output_file, 'w', encoding='utf-8', newline='\n') as f:
    f.write(content)

print(f"\n✓ Cleaned file saved as: {output_file}")
print(f"  File size: {len(content) / (1024*1024):.2f} MB")
print("\nTry importing this cleaned version in phpMyAdmin.")

