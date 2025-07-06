#!/bin/bash

# Jalankan PHPUnit dengan coverage
echo "🧪 Menjalankan PHPUnit dengan coverage..."
vendor/bin/phpunit --coverage-html coverage-report

# Cek apakah berhasil
if [ $? -eq 0 ]; then
  echo "✅ Test selesai. Buka laporan di: coverage-report/index.html"
else
  echo "❌ Test gagal. Periksa error di atas."
fi
