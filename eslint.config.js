import html from 'eslint-plugin-html';

export default [
    {
        files: ['**/*.blade.php', '**/*.html', '**/*.js'],
        plugins: {
            html,
        },
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                window: 'readonly',
                document: 'readonly',
                Alpine: 'readonly',
                localStorage: 'readonly',
                fetch: 'readonly',
                atob: 'readonly',
                btoa: 'readonly',
                TextEncoder: 'readonly',
                setTimeout: 'readonly',
                clearTimeout: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': 'warn',
            'no-undef': 'error',
            semi: ['error', 'always'],
        },
    },
];
