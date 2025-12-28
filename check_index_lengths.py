#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Check all indexes for length issues"""

import re

# Column definitions from the tables
column_sizes = {
    'code_commune': 5,
    'code_comm': 5,
    'code_ar': 2,
    'code_wil': 2,
    'code_wilaya': 2,
    'nom': 50,
    'prenom': 50,
    'nom_ar': 50,
    'prenom_ar': 50,
    'nom_fr': 50,
    'prenom_fr': 50,
    'nature_etablissement': 512,
    'niveau_enseignement': 512,
    'code_user': 18,
    'email': 255,
    'etat_das': 'ENUM',
    'etat_final': 'ENUM',
    'role': 'ENUM',
}

def calculate_index_size(index_def, column_sizes):
    """Calculate approximate index size in bytes"""
    # Extract column names from index definition
    match = re.search(r'\(([^)]+)\)', index_def)
    if not match:
        return 0
    
    columns = [col.strip() for col in match.group(1).split(',')]
    total_size = 0
    
    for col in columns:
        # Check for prefix like column(400)
        prefix_match = re.match(r'(\w+)\((\d+)\)', col)
        if prefix_match:
            col_name = prefix_match.group(1)
            prefix_len = int(prefix_match.group(2))
            if col_name in column_sizes:
                # Use prefix length
                total_size += prefix_len * 3  # UTF-8 can be 3 bytes per char
        else:
            col_name = col
            if col_name in column_sizes:
                if column_sizes[col_name] == 'ENUM':
                    total_size += 20  # ENUM values are small
                else:
                    total_size += column_sizes[col_name] * 3  # UTF-8 can be 3 bytes per char
    
    return total_size

with open('prime_scolaire_web_mysql_clean.sql', 'r', encoding='utf-8') as f:
    content = f.read()

print("Checking all indexes for length issues...\n")

indexes = re.findall(r'CREATE INDEX[^;]+;', content, re.IGNORECASE | re.DOTALL)

for index_def in indexes:
    index_def_clean = index_def.strip()
    size = calculate_index_size(index_def_clean, column_sizes)
    
    # Extract index name
    name_match = re.search(r'`(\w+)`', index_def_clean)
    index_name = name_match.group(1) if name_match else "unknown"
    
    status = "✓" if size <= 3072 else "✗"
    print(f"{status} {index_name}: {size} bytes (limit: 3072)")
    if size > 3072:
        print(f"   WARNING: Index exceeds MySQL key length limit!")
        print(f"   Definition: {index_def_clean[:100]}...")

print("\n" + "=" * 60)
print("All indexes checked!")

