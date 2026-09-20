const fs = require('fs');
const path = require('path');

const assetsDir = path.join(__dirname, 'assets');
if (!fs.existsSync(assetsDir)) {
  fs.mkdirSync(assetsDir, { recursive: true });
}

// 1x1 solid blue PNG in base64
const samplePngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkWPjfDwAEeQHzE3m0ugAAAABJRU5ErkJggg==';
const buffer = Buffer.from(samplePngBase64, 'base64');

fs.writeFileSync(path.join(assetsDir, 'icon.png'), buffer);
fs.writeFileSync(path.join(assetsDir, 'adaptive-icon.png'), buffer);
fs.writeFileSync(path.join(assetsDir, 'splash.png'), buffer);
fs.writeFileSync(path.join(assetsDir, 'favicon.png'), buffer);

console.log('Assets created successfully.');
