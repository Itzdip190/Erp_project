module.exports = function(api) {
  api.cache(true);
  return {
    presets: ['babel-preset-expo'],
    plugins: [
      [
        'module-resolver',
        {
          root: ['./src'],
          extensions: ['.ios.js', '.android.js', '.js', '.ts', '.tsx', '.json'],
          alias: {
            '@': './src',
            '@api': './src/api',
            '@components': './src/components',
            '@context': './src/context',
            '@navigation': './src/navigation',
            '@screens': './src/screens',
            '@types': './src/types',
            '@utils': './src/utils',
            '@config': './src/config'
          }
        }
      ]
    ]
  };
};
