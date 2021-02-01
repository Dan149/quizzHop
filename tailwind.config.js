module.exports = {
  purge: {
    content: ['./templates/**/*.html.twig'],

    options: {
      safelist: ['w-1/3'],
    }
  },
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
    container: {
        center: true,
        padding: '1rem',
    }
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
