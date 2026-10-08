export const previewSpaces = {
  none: "0px",
  xxs: "0.375rem",
  xs: "0.75rem",
  s: "1rem",
  m: "1.625rem",
  l: "2.625rem",
  xl: "4.236rem",
  xxl: "6.854rem",
  "3xl": "11.09rem",
  "4xl": "17.944rem",
};

export function previewSpace(value, fallback = "none") {
  const key = String(value ?? fallback).toLowerCase();
  return previewSpaces[key] ?? previewSpaces[fallback] ?? "0px";
}
