#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Fix migrations table syntax"""

import re

with open('prime_scolaire_web_mysql.sql', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the migrations table - remove the comma on its own line
content = re.sub(
    r'(batch INT NOT NULL)\s*\n\s*,\s*\n\s*PRIMARY KEY',
    r'\1,\n    PRIMARY KEY',
    content,
    flags=re.MULTILINE
)

# Also simplify the comment
content = re.sub(
    r'--\s*Name: migrations; Type: TABLE; Schema: public; Owner: postgres\s*--',
    '-- Name: migrations; Type: TABLE --',
    content
)

with open('prime_scolaire_web_mysql.sql', 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed migrations table syntax")

