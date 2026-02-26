import tailwindTheme from "@danaflex/tailwind-theme";
import { tailwindcssDanaflexui } from "@danaflex/ui-tailwind";
import { MAIN_THEME_DARK_MODE_SELECTOR } from "@danaflex/layout";

/** @type {import("tailwindcss").Config} */
export default {
  content: ["./resources/**/*.blade.php", "./resources/**/*.js", "./resources/**/*.vue"],
  presets: [tailwindTheme],
  theme: {
    extend: {}
  },
  plugins: [tailwindcssDanaflexui],
};
