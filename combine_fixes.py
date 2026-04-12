def combine_fixes():
    try:
        # Read the setup logic
        with open('auto_fix_journal_entries.sql', 'r') as f:
            setup_lines = f.readlines()
        
        # Extract lines 14 to 90 (index 13 to 90)
        # Verify indices: line 14 is index 13. Line 90 is index 89. 
        # So slice [13:90]
        setup_content = "".join(setup_lines[13:90])

        # Read the apply fixes logic
        with open('apply_fixes.sql', 'r') as f:
            apply_lines = f.readlines()
        
        apply_content = "".join(apply_lines)

        # Combine
        full_content = setup_content + "\n\n" + apply_content

        with open('full_apply_fixes.sql', 'w') as f:
            f.write(full_content)
        
        print("Successfully created full_apply_fixes.sql")

    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    combine_fixes()
