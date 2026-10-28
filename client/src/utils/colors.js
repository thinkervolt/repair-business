export const COLOR_TONE = {
    success: 'emerald',
    danger: 'red',
    warning: 'amber',
    primary: 'blue',
    info: 'sky',
    secondary: 'slate',
};

export function toneFor(color) {
    return COLOR_TONE[String(color || '').toLowerCase()] || 'slate';
}

const SOLID_TONES = {
    slate: 'bg-slate-600 text-white',
    blue: 'bg-blue-700 text-white',
    emerald: 'bg-emerald-600 text-white',
    amber: 'bg-amber-500 text-white',
    red: 'bg-red-600 text-white',
    sky: 'bg-sky-600 text-white',
};

export function solidToneFor(color) {
    return SOLID_TONES[toneFor(color)];
}