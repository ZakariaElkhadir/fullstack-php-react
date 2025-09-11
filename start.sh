#!/bin/bash

# ----- MESSAGE FROM Zakaria -----
# Hi there!, if you want to run this script, make sure you have the following:
# 1. set the correct terminal emulator in the script
# 2. have the backend and frontend directories set up correctly
# 3. have the necessary permissions to run the script
# 4. have PHP and Node.js installed on your system


# Full Stack PHP-React Startup Script
set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}🚀 Starting Full Stack PHP-React App...${NC}"

# Directories
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="$SCRIPT_DIR/backend"
FRONTEND_DIR="$SCRIPT_DIR/frontend"

# Quick checks
[ ! -d "$BACKEND_DIR" ] && { echo -e "${RED}❌ Backend directory not found${NC}"; exit 1; }
[ ! -d "$FRONTEND_DIR" ] && { echo -e "${RED}❌ Frontend directory not found${NC}"; exit 1; }
[ ! -f "$BACKEND_DIR/public/index.php" ] && { echo -e "${RED}❌ Backend public/index.php not found${NC}"; exit 1; }
[ ! -f "$FRONTEND_DIR/package.json" ] && { echo -e "${RED}❌ package.json not found${NC}"; exit 1; }
[ ! -d "$FRONTEND_DIR/node_modules" ] && { echo -e "${RED}❌ node_modules not found. Run: cd frontend && npm install${NC}"; exit 1; }

# Check if PHP is available
command -v php >/dev/null 2>&1 || { echo -e "${RED}❌ PHP not found${NC}"; exit 1; }

# Check if Node.js is available
command -v node >/dev/null 2>&1 || { echo -e "${RED}❌ Node.js not found${NC}"; exit 1; }

echo -e "${GREEN}✅ All checks passed${NC}"

# Create backend script
cat > "$BACKEND_DIR/start_backend.sh" << 'EOF'
#!/bin/bash
cd "$(dirname "$0")"
echo "🐘 Starting PHP server on http://localhost:8000"
php -S localhost:8000 -t public
EOF

# Create frontend script
cat > "$FRONTEND_DIR/start_frontend.sh" << 'EOF'
#!/bin/bash
cd "$(dirname "$0")"
echo "⚛️  Starting React/Next.js on http://localhost:3000"
npm run dev
EOF

chmod +x "$BACKEND_DIR/start_backend.sh" "$FRONTEND_DIR/start_frontend.sh"

# Try to open terminals
if command -v gnome-terminal >/dev/null 2>&1; then
    echo -e "${BLUE}Opening terminals with gnome-terminal...${NC}"
    gnome-terminal --tab --working-directory="$BACKEND_DIR" --title="PHP Backend" -- ./start_backend.sh &
    sleep 1
    gnome-terminal --tab --working-directory="$FRONTEND_DIR" --title="React Frontend" -- ./start_frontend.sh &
elif command -v tmux >/dev/null 2>&1; then
    echo -e "${BLUE}Using tmux to manage sessions...${NC}"
    tmux kill-session -t fullstack 2>/dev/null || true
    tmux new-session -d -s fullstack -c "$BACKEND_DIR" './start_backend.sh'
    tmux split-window -h -t fullstack -c "$FRONTEND_DIR" './start_frontend.sh'
    echo -e "${GREEN}✅ Servers starting in tmux session 'fullstack'${NC}"
    echo "Use 'tmux attach-session -t fullstack' to view"
    tmux attach-session -t fullstack
    exit
elif command -v xterm >/dev/null 2>&1; then
    echo -e "${BLUE}Opening terminals with xterm...${NC}"
    xterm -T "PHP Backend" -e "cd '$BACKEND_DIR' && ./start_backend.sh" &
    sleep 1
    xterm -T "React Frontend" -e "cd '$FRONTEND_DIR' && ./start_frontend.sh" &
else
    echo -e "${BLUE}No GUI terminal found. Starting backend only...${NC}"
    echo -e "${BLUE}Manual frontend start: cd $FRONTEND_DIR && ./start_frontend.sh${NC}"
    cd "$BACKEND_DIR" && exec ./start_backend.sh
fi

echo -e "${GREEN}✅ Servers starting!${NC}"
echo "Backend (PHP): http://localhost:8000"
echo "Frontend (React): http://localhost:3000"
echo ""
echo "Press Ctrl+C to stop servers"
