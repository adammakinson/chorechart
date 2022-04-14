module.exports = {
  content: ["./resources/js/**/*.vue"],
  purge: [
    "./resources/js/**/*.vue"
  ],
  theme: {
    extend: {
      spacing: {
        '112': '30rem'
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
