const fs = require('fs');
const path = require('path');
const CleanCSS = require('clean-css');

const root = path.resolve(__dirname, '..');
const modules = [
  '01-base-layout.css',
  '02-home.css',
  '03-auth.css',
  '04-profile.css',
  '05-dashboard.css',
  '06-layout-overrides.css',
  '07-navigation.css',
  '08-components.css',
  '09-about.css',
  '10-safety.css',
  '11-contact.css',
  '12-visual-refresh.css',
  '13-brand-overrides.css',
  '14-palette-overrides.css',
  '15-final-overrides.css',
  '16-username-page.css',
  '17-dashboard-navigation.css',
  '18-admin.css',
];

const source = modules
  .map((file) => fs.readFileSync(path.join(root, 'resources', 'css', 'modules', file), 'utf8'))
  .join('\n');
const result = new CleanCSS({ level: 1, rebase: false }).minify(source);

if (result.errors.length > 0) {
  throw new Error(result.errors.join('\n'));
}

if (result.styles.length === 0) {
  throw new Error('CSS build produced an empty stylesheet.');
}

const outputPath = path.join(root, 'public', 'assets', 'css', 'style.css');
fs.writeFileSync(outputPath, `${result.styles}\n`, 'utf8');

console.log(`Built ${path.relative(root, outputPath)}: ${Buffer.byteLength(source)} -> ${Buffer.byteLength(result.styles)} bytes`);
