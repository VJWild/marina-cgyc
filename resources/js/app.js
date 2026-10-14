// Forzar a Vite 8 a empaquetar y registrar las imágenes en manifest.json
export const images = import.meta.glob([
    '../img/**',
], { eager: true });
