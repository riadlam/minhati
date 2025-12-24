#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Convert PostgreSQL dump to MySQL format
"""

import re
import sys

def escape_mysql_string(value):
    """Escape string for MySQL"""
    if value is None or value == '\\N' or value == '':
        return 'NULL'
    # Escape backslashes, single quotes, and null bytes
    escaped = value.replace('\\', '\\\\').replace("'", "\\'").replace('\x00', '')
    return f"'{escaped}'"

def convert_to_mysql(input_file, output_file):
    """Convert PostgreSQL SQL dump to MySQL format"""
    
    with open(input_file, 'r', encoding='utf-8') as f_in, \
         open(output_file, 'w', encoding='utf-8') as f_out:
        
        in_copy_block = False
        current_table = None
        current_columns = None
        copy_data_lines = []
        skip_until_semicolon = False
        in_enum_values = False
        
        f_out.write("-- MySQL database dump\n")
        f_out.write("-- Converted from PostgreSQL\n")
        f_out.write("--\n\n")
        f_out.write("SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n")
        f_out.write("SET time_zone = \"+00:00\";\n\n")
        
        for line_num, line in enumerate(f_in, 1):
            original_line = line
            
            # Skip lines until semicolon if flag is set
            if skip_until_semicolon:
                if ';' in line:
                    skip_until_semicolon = False
                continue
            
            # Skip PostgreSQL-specific SET statements
            if re.match(r'^\s*SET\s+(statement_timeout|lock_timeout|idle_in_transaction_session_timeout|client_encoding|standard_conforming_strings|check_function_bodies|xmloption|client_min_messages|row_security|default_tablespace|default_table_access_method)', line, re.IGNORECASE):
                continue
            
            # Skip PostgreSQL-specific SELECT statements
            if 'pg_catalog.set_config' in line or 'pg_catalog.setval' in line:
                continue
            
            # Skip ALTER TYPE ... OWNER
            if re.match(r'^\s*ALTER\s+TYPE\s+.*\s+OWNER\s+TO', line, re.IGNORECASE):
                continue
            
            # Skip ALTER TABLE ... OWNER
            if re.match(r'^\s*ALTER\s+TABLE\s+.*\s+OWNER\s+TO', line, re.IGNORECASE):
                continue
            
            # Skip ALTER SEQUENCE ... OWNER
            if re.match(r'^\s*ALTER\s+SEQUENCE\s+.*\s+OWNER\s+TO', line, re.IGNORECASE):
                continue
            
            # Skip ALTER SEQUENCE ... OWNED BY
            if re.match(r'^\s*ALTER\s+SEQUENCE\s+.*\s+OWNED\s+BY', line, re.IGNORECASE):
                continue
            
            # Skip CREATE TYPE (ENUM) - we'll convert them inline in table definitions
            if 'CREATE TYPE' in line and 'AS ENUM' in line:
                # Skip until closing );
                skip_until_semicolon = True
                continue
            
            # Skip ALTER TYPE ... OWNER (already handled above)
            
            # Skip CREATE SEQUENCE - handled by AUTO_INCREMENT
            if 'CREATE SEQUENCE' in line:
                skip_until_semicolon = True
                continue
            
            # Skip ALTER TABLE ... SET DEFAULT nextval
            if 'nextval' in line and 'SET DEFAULT' in line:
                continue
            
            # Handle COPY command
            if 'COPY' in line and 'FROM stdin' in line:
                in_copy_block = True
                # Extract table name and columns (handle public. prefix)
                match = re.search(r'COPY\s+(?:public\.)?(\w+)\s*\(([^)]+)\)\s+FROM\s+stdin', line, re.IGNORECASE)
                if match:
                    current_table = match.group(1)
                    current_columns = [col.strip() for col in match.group(2).split(',')]
                    copy_data_lines = []
                continue
            
            # Handle end of COPY block
            # The line contains a literal backslash followed by period: \.
            if in_copy_block and line.strip() == '\\.':
                in_copy_block = False
                # Convert COPY data to INSERT statements
                if copy_data_lines and current_table and current_columns:
                    # Write INSERT statement
                    f_out.write(f"INSERT INTO `{current_table}` (`{'`, `'.join(current_columns)}`) VALUES\n")
                    
                    insert_values = []
                    for data_line in copy_data_lines:
                        if not data_line.strip():
                            continue
                        # Split by tab
                        values = data_line.rstrip('\n\r').split('\t')
                        # Format values
                        formatted_values = []
                        for i in range(len(current_columns)):
                            if i < len(values):
                                val = values[i]
                                formatted_values.append(escape_mysql_string(val))
                            else:
                                formatted_values.append('NULL')
                        
                        insert_values.append(f"({', '.join(formatted_values)})")
                    
                    # Write all values (batch insert)
                    if insert_values:
                        # Write in batches of 1000 to avoid huge statements
                        batch_size = 1000
                        for batch_start in range(0, len(insert_values), batch_size):
                            batch_end = min(batch_start + batch_size, len(insert_values))
                            batch = insert_values[batch_start:batch_end]
                            
                            if batch_start > 0:
                                f_out.write(f"\nINSERT INTO `{current_table}` (`{'`, `'.join(current_columns)}`) VALUES\n")
                            
                            for i, val_line in enumerate(batch):
                                if i < len(batch) - 1:
                                    f_out.write(f"{val_line},\n")
                                else:
                                    f_out.write(f"{val_line};\n")
                        
                        f_out.write("\n")
                    
                    copy_data_lines = []
                    current_table = None
                    current_columns = None
                continue
            
            # Collect COPY data lines
            if in_copy_block:
                copy_data_lines.append(line)
                continue
            
            # Convert the line
            # Remove schema prefix (public.)
            line = re.sub(r'\bpublic\.', '', line)
            
            # Convert data types
            line = re.sub(r'\bcharacter varying\((\d+)\)', r'VARCHAR(\1)', line, flags=re.IGNORECASE)
            line = re.sub(r'\btimestamp\s+without\s+time\s+zone', 'DATETIME', line, flags=re.IGNORECASE)
            line = re.sub(r'\btimestamp\(0\)\s+without\s+time\s+zone', 'DATETIME', line, flags=re.IGNORECASE)
            line = re.sub(r'\bdouble precision', 'DOUBLE', line, flags=re.IGNORECASE)
            line = re.sub(r'\binteger\b', 'INT', line, flags=re.IGNORECASE)
            
            # Convert ENUM type references to MySQL ENUM
            line = re.sub(r'\bpublic\.dossier_enum\b', "ENUM('oui','non')", line, flags=re.IGNORECASE)
            line = re.sub(r'\bpublic\.etat_enum\b', "ENUM('accepte','en_cours','refuse')", line, flags=re.IGNORECASE)
            line = re.sub(r'\bpublic\.role_enum\b', "ENUM('ts_commune','das','comite_wilaya','antr','admin')", line, flags=re.IGNORECASE)
            
            # Also handle without public prefix
            line = re.sub(r'\bdossier_enum\b', "ENUM('oui','non')", line, flags=re.IGNORECASE)
            line = re.sub(r'\betat_enum\b', "ENUM('accepte','en_cours','refuse')", line, flags=re.IGNORECASE)
            line = re.sub(r'\brole_enum\b', "ENUM('ts_commune','das','comite_wilaya','antr','admin')", line, flags=re.IGNORECASE)
            
            # Convert default value syntax
            line = re.sub(r"'(\d+)'::character varying", r"'\1'", line)
            line = re.sub(r"'(\w+)'::(?:public\.)?(\w+_enum)", r"'\1'", line)
            # Fix ENUM defaults that still have ::ENUM syntax (must come after ENUM type conversion)
            line = re.sub(r"::ENUM\('[^']+'(?:,'[^']+')*\)", '', line)
            
            # Convert migrations table id to AUTO_INCREMENT
            if 'CREATE TABLE' in line and 'migrations' in line.lower():
                # Read the table definition
                table_def = [line]
                found_id = False
                while True:
                    next_line = f_in.readline()
                    if not next_line:
                        break
                    table_def.append(next_line)
                    # Check if this is the id column
                    if re.search(r'\bid\s+(?:integer|INT)\s+NOT\s+NULL', next_line, re.IGNORECASE) and not found_id:
                        # Replace with AUTO_INCREMENT
                        next_line = re.sub(r'\bid\s+(?:integer|INT)\s+NOT\s+NULL', 'id INT NOT NULL AUTO_INCREMENT', next_line, flags=re.IGNORECASE)
                        table_def[-1] = next_line
                        found_id = True
                    if ');' in next_line:
                        break
                
                # Write converted table definition
                for td_line in table_def:
                    # Apply conversions
                    td_line = re.sub(r'\bpublic\.', '', td_line)
                    td_line = re.sub(r'\bcharacter varying\((\d+)\)', r'VARCHAR(\1)', td_line, flags=re.IGNORECASE)
                    td_line = re.sub(r'\btimestamp\s+without\s+time\s+zone', 'DATETIME', td_line, flags=re.IGNORECASE)
                    td_line = re.sub(r'\binteger\b', 'INT', td_line, flags=re.IGNORECASE)
                    td_line = re.sub(r"'(\d+)'::character varying", r"'\1'", td_line)
                    td_line = re.sub(r"'(\w+)'::(?:public\.)?(\w+_enum)", r"'\1'", td_line)
                    # Convert ENUM types
                    td_line = re.sub(r'\bpublic\.dossier_enum\b', "ENUM('oui','non')", td_line, flags=re.IGNORECASE)
                    td_line = re.sub(r'\bpublic\.etat_enum\b', "ENUM('accepte','en_cours','refuse')", td_line, flags=re.IGNORECASE)
                    td_line = re.sub(r'\bpublic\.role_enum\b', "ENUM('ts_commune','das','comite_wilaya','antr','admin')", td_line, flags=re.IGNORECASE)
                    td_line = re.sub(r'\bdossier_enum\b', "ENUM('oui','non')", td_line, flags=re.IGNORECASE)
                    td_line = re.sub(r'\betat_enum\b', "ENUM('accepte','en_cours','refuse')", td_line, flags=re.IGNORECASE)
                    td_line = re.sub(r'\brole_enum\b', "ENUM('ts_commune','das','comite_wilaya','antr','admin')", td_line, flags=re.IGNORECASE)
                    f_out.write(td_line)
                continue
            
            # Remove "ONLY" from ALTER TABLE statements
            line = re.sub(r'ALTER\s+TABLE\s+ONLY\s+', 'ALTER TABLE ', line, flags=re.IGNORECASE)
            
            # Remove USING btree from CREATE INDEX (MySQL uses btree by default)
            line = re.sub(r'\s+USING\s+btree', '', line, flags=re.IGNORECASE)
            
            # Convert table names to use backticks (but not in CREATE TABLE migrations which we handle separately)
            if 'migrations' not in line.lower() or 'CREATE TABLE' not in line:
                line = re.sub(r'CREATE TABLE (\w+)', r'CREATE TABLE `\1`', line, flags=re.IGNORECASE)
            line = re.sub(r'ALTER TABLE (\w+)', r'ALTER TABLE `\1`', line, flags=re.IGNORECASE)
            line = re.sub(r'CREATE INDEX (\w+) ON (\w+)', r'CREATE INDEX `\1` ON `\2`', line, flags=re.IGNORECASE)
            # Add backticks to foreign key references
            line = re.sub(r'REFERENCES (\w+)\((\w+)\)', r'REFERENCES `\1`(`\2`)', line, flags=re.IGNORECASE)
            
            # Skip orphaned enum value lines (leftover from CREATE TYPE)
            if re.match(r'^\s*[\'"]\w+[\'"],?\s*$', line.strip()):
                # Check if previous context suggests this is an orphaned enum value
                continue
            
            # Skip PostgreSQL dump complete comment
            if 'PostgreSQL database dump complete' in line:
                f_out.write("-- MySQL database dump complete\n")
                continue
            
            # Write the converted line
            f_out.write(line)

if __name__ == '__main__':
    input_file = 'prime_scolaire_web.sql'
    output_file = 'prime_scolaire_web_mysql.sql'
    print("Converting PostgreSQL dump to MySQL format...")
    convert_to_mysql(input_file, output_file)
    print(f"Conversion complete! Output written to {output_file}")
