const fs = require('fs');
const path = require('path');

const src = path.join(__dirname, '..', 'apk for erp', 'app', 'src', 'main', 'res', 'drawable-xxhdpi', 'splash_bg.png');
const destDir = path.join(__dirname, 'assets');

if (!fs.existsSync(destDir)) {
  fs.mkdirSync(destDir, { recursive: true });
}

fs.copyFileSync(src, path.join(destDir, 'icon.png'));
fs.copyFileSync(src, path.join(destDir, 'adaptive-icon.png'));
fs.copyFileSync(src, path.join(destDir, 'splash.png'));
fs.copyFileSync(src, path.join(destDir, 'favicon.png'));

console.log('Copied valid assets successfully.');
