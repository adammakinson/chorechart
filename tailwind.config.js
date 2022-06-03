const colors = require('tailwindcss/colors');

module.exports = {
  content: ["./resources/js/**/*.vue"],
  purge: [
    "./resources/js/**/*.vue"
  ],
  theme: {
    extend: {
      spacing: {
        '112': '30rem'
      },
      colors: {
        orange: colors.orange,
        green: colors.green
      }
    },
  },
  variants: {
    extend: {
      textColor: ['visited']
    }
  },
  plugins: [],
}
