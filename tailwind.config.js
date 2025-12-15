/**
 * npm run build
 */

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [    
    "./view/**/*.php",
    "./index.php",
    "./**/*.html"
  ],
  theme: {
      extend: {
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
              'spin-slow': 'spin 12s linear infinite',
              'pulse-fast': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
              'float': 'float 6s ease-in-out infinite',
              'fade-in': 'fadeIn 1s ease-out forwards',
              'pulse-glow': 'pulseGlow 3s infinite',
          },
          keyframes: {
              float: {
                  '0%, 100%': { transform: 'translateY(0)' },
                  '50%': { transform: 'translateY(-20px)' },
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
  }
        
}

