#!/bin/bash

# POWAS OS Database Setup Helper Script
# This script helps you set up the database for your Laravel application

echo "========================================="
echo "POWAS OS - Database Setup Helper"
echo "========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Database configuration
DB_NAME="powas_os_app"
DB_USER="root"

# Auto-detect SQL backup file
SQL_BACKUP="if0_36544795_powas_os.sql"
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo -e "${YELLOW}This script will help you set up your database.${NC}"
echo ""

# Check if SQL backup exists
if [ -f "$SCRIPT_DIR/$SQL_BACKUP" ]; then
    echo -e "${GREEN}✓ Found SQL backup: $SQL_BACKUP${NC}"
    echo ""
fi

echo "Choose an option:"
echo "  1) ${BLUE}Restore from InfinityFree backup${NC} (${SQL_BACKUP}) ${GREEN}[RECOMMENDED]${NC}"
echo "  2) Import from custom SQL backup file"
echo "  3) Create fresh database with migrations (NO DATA - empty tables)"
echo "  4) Just create empty database (no tables)"
echo "  5) Exit"
echo ""
read -p "Enter your choice (1-5): " choice

case $choice in
    1)
        # Restore from InfinityFree backup (auto-detected)
        echo ""
        echo -e "${YELLOW}Restoring from InfinityFree backup...${NC}"
        
        if [ ! -f "$SCRIPT_DIR/$SQL_BACKUP" ]; then
            echo -e "${RED}✗ SQL backup file not found: $SQL_BACKUP${NC}"
            echo "Please make sure the file is in the same directory as this script."
            exit 1
        fi
        
        read -sp "Enter MySQL root password: " mysql_password
        echo ""
        
        # Create database
        echo "Creating database..."
        mysql -u $DB_USER -p"$mysql_password" -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Database created successfully!${NC}"
            echo ""
            echo "Importing SQL backup (this may take a moment)..."
            mysql -u $DB_USER -p"$mysql_password" $DB_NAME < "$SCRIPT_DIR/$SQL_BACKUP"
            
            if [ $? -eq 0 ]; then
                echo -e "${GREEN}✓ Database imported successfully!${NC}"
                echo ""
                echo -e "${GREEN}=========================================${NC}"
                echo -e "${GREEN}SUCCESS! Your InfinityFree database has been restored.${NC}"
                echo -e "${GREEN}=========================================${NC}"
            else
                echo -e "${RED}✗ Import failed. Check the error above.${NC}"
                exit 1
            fi
        else
            echo -e "${RED}✗ Failed to create database. Check your password and try again.${NC}"
            exit 1
        fi
        ;;
        
    2)
        # Import from custom SQL backup file
        echo ""
        read -p "Enter path to SQL backup file: " sql_file
        
        if [ ! -f "$sql_file" ]; then
            echo -e "${RED}✗ File not found: $sql_file${NC}"
            exit 1
        fi
        
        read -sp "Enter MySQL root password: " mysql_password
        echo ""
        
        # Create database
        mysql -u $DB_USER -p"$mysql_password" -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Database created successfully!${NC}"
            echo ""
            echo "Importing SQL file..."
            mysql -u $DB_USER -p"$mysql_password" $DB_NAME < "$sql_file"
            
            if [ $? -eq 0 ]; then
                echo -e "${GREEN}✓ Database imported successfully!${NC}"
            else
                echo -e "${RED}✗ Import failed. Check the error above.${NC}"
            fi
        else
            echo -e "${RED}✗ Failed to create database. Check your password and try again.${NC}"
        fi
        ;;
    
    3)
        # Create fresh database with migrations
        echo ""
        echo -e "${YELLOW}Creating fresh database with migrations...${NC}"
        echo -e "${RED}WARNING: This will create empty tables with NO data!${NC}"
        read -p "Are you sure? (yes/no): " confirm
        
        if [ "$confirm" != "yes" ]; then
            echo "Cancelled."
            exit 0
        fi
        
        read -sp "Enter MySQL root password: " mysql_password
        echo ""
        
        # Create database
        mysql -u $DB_USER -p"$mysql_password" -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Database created successfully!${NC}"
            echo ""
            echo "Running migrations..."
            php artisan migrate:fresh --force
            
            if [ $? -eq 0 ]; then
                echo -e "${GREEN}✓ Migrations completed successfully!${NC}"
                echo ""
                echo -e "${YELLOW}Note: You now have an empty database with tables.${NC}"
                echo "You may want to create a user or run seeders."
            else
                echo -e "${RED}✗ Migration failed. Check the error above.${NC}"
            fi
        else
            echo -e "${RED}✗ Failed to create database. Check your password and try again.${NC}"
        fi
        ;;
        
    4)
        # Just create empty database
        echo ""
        read -sp "Enter MySQL root password: " mysql_password
        echo ""
        
        mysql -u $DB_USER -p"$mysql_password" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Database created successfully!${NC}"
            echo ""
            echo "Database name: $DB_NAME"
            echo "You can now run migrations manually with: php artisan migrate"
        else
            echo -e "${RED}✗ Failed to create database. Check your password and try again.${NC}"
        fi
        ;;
        
    5)
        echo "Exiting..."
        exit 0
        ;;
        
    *)
        echo -e "${RED}Invalid option. Exiting.${NC}"
        exit 1
        ;;
esac

echo ""
echo "========================================="
echo "Next steps:"
echo "1. Make sure your .env file has the correct database credentials"
echo "2. Run: php artisan config:clear"
echo "3. Test connection: php artisan migrate:status"
echo "========================================="
