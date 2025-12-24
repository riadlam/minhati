#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Fix MySQL syntax issues in the converted file"""

import re

def fix_mysql_file(input_file, output_file):
    """Fix MySQL syntax issues"""
    
    with open(input_file, 'r', encoding='utf-8') as f_in, \
         open(output_file, 'w', encoding='utf-8') as f_out:
        
        content = f_in.read()
        lines = content.split('\n')
        
        for i, line in enumerate(lines):
            # Clean up PostgreSQL-specific comments
            if 'Schema: public; Owner: postgres' in line:
                # Simplify the comment
                line = re.sub(r';\s*Type:.*?Schema: public; Owner: postgres', '', line)
                line = re.sub(r'--\s*$', '--', line)  # Ensure comment ends properly
            
            # Ensure CREATE TABLE has backticks
            if 'CREATE TABLE' in line and 'migrations' in line.lower():
                line = re.sub(r'CREATE TABLE migrations\s*\(', 'CREATE TABLE `migrations` (', line, flags=re.IGNORECASE)
            
            # Remove empty comment lines that might cause issues
            if line.strip() == '--' and i < len(lines) - 1:
                next_line = lines[i + 1] if i + 1 < len(lines) else ''
                if next_line.strip().startswith('--') or next_line.strip() == '':
                    continue
            
            f_out.write(line + '\n')

if __name__ == '__main__':
    input_file = 'prime_scolaire_web_mysql.sql'
    output_file = 'prime_scolaire_web_mysql_fixed.sql'
    print("Fixing MySQL syntax issues...")
    fix_mysql_file(input_file, output_file)
    print(f"Fixed file written to {output_file}")

