#!/bin/bash
# Script para abrir o relatório de cobertura no navegador

COVERAGE_FILE="coverage/html/index.html"

if [ ! -f "$COVERAGE_FILE" ]; then
    echo "❌ Relatório não encontrado. Execute primeiro: ./coverage.sh"
    exit 1
fi

echo "📊 Abrindo relatório de cobertura..."
echo "📁 Arquivo: $(pwd)/$COVERAGE_FILE"
echo ""

# Tentar abrir no Windows
if command -v explorer.exe &> /dev/null; then
    explorer.exe "$(wslpath -w "$(pwd)/$COVERAGE_FILE")" 2>/dev/null
elif command -v cmd.exe &> /dev/null; then
    cmd.exe /c start "$(wslpath -w "$(pwd)/$COVERAGE_FILE")" 2>/dev/null
else
    echo "⚠️  Abra manualmente no navegador:"
    echo "   file://$(pwd)/$COVERAGE_FILE"
    echo ""
    echo "Ou no Windows Explorer:"
    echo "   \\\\wsl$\\Ubuntu$(echo $(pwd) | sed 's|/|\\|g')\\coverage\\html\\index.html"
fi

