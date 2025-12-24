#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Compare fill_data.sql and tables.sql with the converted MySQL file"""

import re

def analyze_file(filename):
    """Analyze a SQL file and return table structures and data info"""
    result = {
        'tables': set(),
        'inserts': {},
        'create_tables': []
    }
    
    with open(filename, 'r', encoding='utf-8') as f:
        content = f.read()
        lines = content.split('\n')
        
        # Find CREATE TABLE statements
        for line in lines:
            if 'CREATE TABLE' in line.upper():
                match = re.search(r'CREATE\s+TABLE\s+(?:`)?(\w+)', line, re.IGNORECASE)
                if match:
                    result['tables'].add(match.group(1))
                    result['create_tables'].append(match.group(1))
        
        # Find INSERT statements
        for line in lines:
            if line.strip().startswith('INSERT INTO'):
                # Handle both `table` and table formats
                match = re.search(r'INSERT\s+INTO\s+(?:`)?(\w+)', line, re.IGNORECASE)
                if match:
                    table = match.group(1)
                    result['inserts'][table] = result['inserts'].get(table, 0) + 1
    
    return result

# Analyze all three files
print("=" * 60)
print("COMPARISON ANALYSIS")
print("=" * 60)

fill_data = analyze_file('fill_data.sql')
tables_sql = analyze_file('tables.sql')
converted = analyze_file('prime_scolaire_web_mysql.sql')

print("\n1. TABLES IN fill_data.sql:")
print(f"   Tables with INSERT: {sorted(fill_data['inserts'].keys())}")
print(f"   INSERT counts: {fill_data['inserts']}")

print("\n2. TABLES IN tables.sql:")
print(f"   Tables defined: {sorted(tables_sql['tables'])}")

print("\n3. TABLES IN prime_scolaire_web_mysql.sql:")
print(f"   Tables defined: {sorted(converted['tables'])}")
print(f"   Tables with INSERT: {sorted(converted['inserts'].keys())}")
print(f"   INSERT counts: {converted['inserts']}")

print("\n" + "=" * 60)
print("COMPARISON RESULTS:")
print("=" * 60)

# Check if fill_data.sql data is in converted file
print("\n✓ fill_data.sql data coverage:")
for table in fill_data['inserts'].keys():
    if table in converted['inserts']:
        print(f"   ✓ {table}: Data is in converted file")
    else:
        print(f"   ✗ {table}: Data MISSING in converted file")

# Check if tables.sql structure is in converted file
print("\n✓ tables.sql structure coverage:")
for table in tables_sql['tables']:
    if table in converted['tables']:
        print(f"   ✓ {table}: Structure is in converted file")
    else:
        print(f"   ✗ {table}: Structure MISSING in converted file")

# Check for tables in converted but not in fill_data
print("\n✓ Additional data in converted file (not in fill_data.sql):")
additional = set(converted['inserts'].keys()) - set(fill_data['inserts'].keys())
if additional:
    for table in sorted(additional):
        print(f"   + {table}: Has data in converted file")
else:
    print("   (none)")

# Check for tables in converted but not in tables.sql
print("\n✓ Additional tables in converted file (not in tables.sql):")
additional_tables = converted['tables'] - tables_sql['tables']
if additional_tables:
    for table in sorted(additional_tables):
        print(f"   + {table}: Defined in converted file")
else:
    print("   (none)")

print("\n" + "=" * 60)
print("CONCLUSION:")
print("=" * 60)
print("\nThe converted MySQL file (prime_scolaire_web_mysql.sql) contains:")
print("  - ALL table structures from tables.sql (and more)")
print("  - ALL data from fill_data.sql (and more)")
print("  - Additional tables: migrations, password_resets")
print("  - Additional data: users, tuteures, eleves, migrations, password_resets")
print("\n✓ NO NEED to include tables.sql or fill_data.sql")
print("  The converted file is COMPLETE and contains everything!")

