import re

def parse_renumbering_plan(input_file, output_file):
    try:
        with open(input_file, 'r') as f:
            content = f.read()

        # Find the section starting with 'update_sql'
        if 'update_sql' not in content:
            print("Error: Could not find 'update_sql' section.")
            return

        # Extract the SQL part (roughly everything after 'update_sql')
        # The file seems to be tab-separated or just raw output.
        # Based on step 477 content, the SQL statements are in the 'update_sql' column.
        # They appear to be escaped with \n.
        
        # Split by lines
        lines = content.splitlines()
        
        sql_statements = []
        capture = False
        
        for line in lines:
            if line.strip() == 'update_sql':
                capture = True
                continue
            
            if capture:
                # The line might be a quoted string or just contain the SQL
                # It seems to contain literal '\n' characters based on the preview
                clean_line = line.replace('\\n', '\n')
                
                # Basic validation to ensure it looks like SQL
                if 'UPDATE transactions' in clean_line or '-- Renumber' in clean_line:
                    sql_statements.append(clean_line)

        if not sql_statements:
            print("No SQL statements found.")
            return

        with open(output_file, 'w') as f:
            f.write("-- GENERATED FIX SCRIPT\n")
            f.write("-- Based on renumbering_plan.txt\n\n")
            for stmt in sql_statements:
                f.write(stmt)
                f.write("\n")
                
        print(f"Successfully extracted {len(sql_statements)} SQL blocks to {output_file}")

    except Exception as e:
        print(f"An error occurred: {e}")

if __name__ == "__main__":
    parse_renumbering_plan('renumbering_plan.txt', 'apply_fixes.sql')
