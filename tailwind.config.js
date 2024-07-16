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
      },
      gridTemplateColumns: {

        // grid spacing for expanded menu
        'menuexpanded': '160px 1fr',
        
        // grid spacing for expanded menu
        'menucollapsed': '48px 1fr',

        'listitemgrid': '1fr 2.5rem 2.5rem',

        'cardgrid': 'repeat(auto-fit, 232px)'
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
