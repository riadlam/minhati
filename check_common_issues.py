#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Check for common MySQL import issues"""

with open('prime_scolaire_web_mysql.sql', 'r', encoding='utf-8') as f:
    content = f.read()

print("Checking for common MySQL import issues...\n")

# Check 1: File encoding
print("1. File encoding: UTF-8 ✓")

# Check 2: BOM (Byte Order Mark) - can cause issues
if content.startswith('\ufeff'):
    print("2. BOM detected: ✗ (should be removed)")
else:
    print("2. BOM: ✓ (none found)")

# Check 3: Line endings
if '\r\n' in content:
    print("3. Line endings: Windows (CRLF) ✓")
elif '\n' in content:
    print("3. Line endings: Unix (LF) ✓")
else:
    print("3. Line endings: ✗ (unusual)")

# Check 4: SQL_MODE and time_zone settings
if 'SET SQL_MODE' in content:
    print("4. SQL_MODE setting: ✓")
else:
    print("4. SQL_MODE setting: ⚠ (not found)")

# Check 5: First few lines
print("\n5. First 10 lines of file:")
lines = content.split('\n')[:10]
for i, line in enumerate(lines, 1):
    print(f"   {i:2}: {line[:80]}")

# Check 6: Migrations table structure
print("\n6. Migrations table structure:")
migrations_start = content.find('CREATE TABLE `migrations`')
if migrations_start != -1:
    migrations_end = content.find(');', migrations_start) + 2
    migrations_def = content[migrations_start:migrations_end]
    print("   " + "\n   ".join(migrations_def.split('\n')[:10]))
    
    # Check for issues
    if 'AUTO_INCREMENT' not in migrations_def:
        print("   ✗ Missing AUTO_INCREMENT")
    else:
        print("   ✓ AUTO_INCREMENT present")
    
    if 'PRIMARY KEY' not in migrations_def:
        print("   ✗ Missing PRIMARY KEY")
    else:
        print("   ✓ PRIMARY KEY present")
    
    if migrations_def.count('PRIMARY KEY') > 1:
        print("   ✗ Multiple PRIMARY KEY definitions")
    else:
        print("   ✓ Single PRIMARY KEY definition")

# Check 7: Check for problematic characters
print("\n7. Checking for problematic characters:")
problematic_chars = ['\x00', '\x1a']  # NULL byte, Ctrl+Z
for char in problematic_chars:
    if char in content:
        count = content.count(char)
        print(f"   ✗ Found {count} occurrences of {repr(char)}")
    else:
        print(f"   ✓ No {repr(char)} found")

# Check 8: Check file size (very large files can cause issues)
file_size_mb = len(content.encode('utf-8')) / (1024 * 1024)
print(f"\n8. File size: {file_size_mb:.2f} MB")
if file_size_mb > 50:
    print("   ⚠ File is very large, might need to increase upload limits")
elif file_size_mb > 10:
    print("   ⚠ File is large, may take time to import")
else:
    print("   ✓ File size is reasonable")

print("\n" + "=" * 60)
print("SUMMARY")
print("=" * 60)
print("If you're still getting errors, please share:")
print("1. The exact error message from phpMyAdmin")
print("2. Which line number it fails at (if shown)")
print("3. Any warnings displayed before the error")
print("=" * 60)

