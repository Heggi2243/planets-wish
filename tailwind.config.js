/**
 * npm run build
 */

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [    
    "./views/**/*.php", 
    "./index.php",
    "./**/*.html",
    "./verify-email.php" 
  ],
  theme: {
      extend: {
            inset: {
                '10': '2rem', // 這會將top-10, bottom-10等都設為2.5rem
                '15': '4rem',
                '24': '6rme',
                '30': '7rem',
            },
            spacing: {
            '1/4': '25%',
            '1/3': '33.333333%',
            '1/2': '50%',
            '2/3': '66.666667%',
            '3/4': '75%',
            '40' : '10rem',
        },
          fontFamily: {
              'orbitron': ['"Orbitron"', 'sans-serif'],
              'display': ['"Orbitron"', 'sans-serif'],
              'body': ['"Noto Sans TC"', 'sans-serif'],
          },
          colors: {
              'cosmic-dark': '#0f172a',
              'cosmic-purple': '#7e22ce',
              'neon-blue': '#00f0ff',
              'cosmic-bg': '#0B0E14',
              'panel-bg': 'rgba(15, 23, 42, 0.6)',
              'neon-cyan': '#00f2ff',
              'neon-purple': '#bc13fe',
          },
          animation: {
              'spin-slow': 'spin-slow 20s linear infinite',
              'pulse-fast': 'pulse-fast 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
              'float': 'float 3s ease-in-out infinite', 
              'fade-in': 'fadeIn 1s ease-out forwards',
              'pulse-glow': 'pulseGlow 3s infinite',
          },
          keyframes: {
              'spin-slow': {
                  '0%': { transform: 'rotate(0deg)' },
                  '100%': { transform: 'rotate(360deg)' }
              },
              'pulse-fast': {
                  '0%, 100%': { 
                      opacity: '0.75',
                      transform: 'scale(1)' 
                  },
                  '50%': { 
                      opacity: '1',
                      transform: 'scale(1.05)' 
                  }
              },
              float: {
                  '0%, 100%': { transform: 'translateY(0px)' },
                  '50%': { transform: 'translateY(-10px)' },
              },
              fadeIn: {
                  '0%': { opacity: '0', transform: 'translateY(20px)' },
                  '100%': { opacity: '1', transform: 'translateY(0)' },
              },
              pulseGlow: {
                  '0%, 100%': { boxShadow: '0 0 10px rgba(0, 242, 255, 0.2)' },
                  '50%': { boxShadow: '0 0 25px rgba(0, 242, 255, 0.5)' },
              }
          }
      }
  },
  plugins: [],
}