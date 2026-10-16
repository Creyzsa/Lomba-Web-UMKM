"use client";

import { useEffect, useRef, useState, type ReactNode } from "react";

/* ------------------------------------------------------------------ */
/*  Tipe & data                                                        */
/* ------------------------------------------------------------------ */

type Vec = { x: number; y: number; r: number; s: number };

export type FoodItem = {
  id: string;
  label: string;
  /** FOTO ASLI makanan di /public (JPG/WebP biasa sudah cukup, tidak perlu transparan). */
  src: string;
  /** Ukuran piring dalam vw (dibatasi 110 sampai 280 px) */
  size: number;
  /** Posisi akhir dari titik tengah layar: x dalam vw, y dalam vh, r derajat, s skala */
  to: Vec;
  /** Rentang progress scroll (0 sampai 1) saat item keluar dari pintu */
  window: [number, number];
  /** Kedalaman parallax: makin besar makin cepat bergerak saat scroll */
  depth: number;
};

export type ChapterStat = { value: string; label: string };
export type ChapterAction = { label: string; href: string; primary?: boolean };

export type Chapter = {
  kicker?: string;
  title: string;
  text?: string;
  stats?: ChapterStat[];
  actions?: ChapterAction[];
};

/**
 * Titik awal semua makanan: ambang pintu Rumah Gadang
 * (tengah layar, sedikit di bawah titik tengah). Ubah jika pintu di fotomu
 * berada di posisi lain. Satuan: x vw, y vh.
 */
const DOOR = { x: 0, y: -58 };

const DEFAULT_ITEMS: FoodItem[] = [
  { id: "rendang", label: "Rendang", src: "/images/makanan/rendang.jpg", size: 19, to: { x: -15, y: -33, r: 0, s: 0.55 }, window: [0.01, 0.085], depth: 1.2 },
  { id: "sate", label: "Sate Padang", src: "/images/makanan/sate.jpg", size: 16, to: { x: 15, y: -33, r: 0, s: 0.55 }, window: [0.045, 0.12], depth: 0.8 },
  { id: "ayam-pop", label: "Ayam Pop", src: "/images/makanan/ayam-pop.jpg", size: 15, to: { x: -15, y: -11, r: 0, s: 0.55 }, window: [0.08, 0.155], depth: 1.5 },
  { id: "dendeng", label: "Dendeng Balado", src: "/images/makanan/dendeng.jpg", size: 17, to: { x: 15, y: -11, r: 0, s: 0.55 }, window: [0.115, 0.19], depth: 1.0 },
  { id: "gulai", label: "Gulai", src: "/images/makanan/gulai.jpg", size: 13, to: { x: -15, y: 11, r: 0, s: 0.55 }, window: [0.15, 0.225], depth: 0.6 },
  { id: "kawa", label: "Kawa Daun", src: "/images/makanan/kawa-daun.jpg", size: 12, to: { x: 15, y: 11, r: 0, s: 0.55 }, window: [0.185, 0.26], depth: 1.3 },
  { id: "lamang", label: "Lamang", src: "/images/makanan/lamang.jpg", size: 12, to: { x: -15, y: 33, r: 0, s: 0.55 }, window: [0.22, 0.295], depth: 0.7 },
  { id: "cabai", label: "Cabai", src: "/images/makanan/cabai.jpg", size: 10, to: { x: 15, y: 33, r: 0, s: 0.55 }, window: [0.255, 0.33], depth: 1.8 },
];

const DEFAULT_CHAPTERS: Chapter[] = [
  {
    kicker: "Digitalisasi UMKM Kuliner Minangkabau",
    title: "Peta Rasa Padang",
  },
  {
    title: "Jelajahi Cita Rasa Autentik Ranah Minang Langsung dari Sentra Kuliner Kota Padang",
    text: "Temukan rumah makan legendaris, sate kuah rempah khas Pariaman, seduhan kawa daun hangat, hingga oleh-oleh khas Padang dalam etalase digital interaktif modern.",
  },
  {
    title: "",
    stats: [
      { value: "36", label: "Titik UMKM Kuliner" },
      { value: "112", label: "Pilihan Menu Minang" },
      { value: "5", label: "Kategori Kuliner" },
    ],
    actions: [
      { label: "Buka Peta Interaktif", href: "/peta", primary: true },
      { label: "Jelajahi Daftar Toko", href: "/toko" },
    ],
  },
];

/* ------------------------------------------------------------------ */
/*  Util                                                               */
/* ------------------------------------------------------------------ */

const clamp = (v: number, a = 0, b = 1) => Math.min(Math.max(v, a), b);
const lerp = (a: number, b: number, t: number) => a + (b - a) * t;
const easeInOut = (t: number) => t * t * (3 - 2 * t);
const easeOutCubic = (t: number) => 1 - Math.pow(1 - t, 3);
/** Sedikit melampaui target lalu kembali: terasa seperti piring "diletakkan" */
const easeOutBack = (t: number) => {
  const c1 = 1.4;
  const c3 = c1 + 1;
  return 1 + c3 * Math.pow(t - 1, 3) + c1 * Math.pow(t - 1, 2);
};

/** Foto makanan dalam piring bulat dengan cincin emas. */
function Plate({ src, label }: { src: string; label: string }) {
  const [failed, setFailed] = useState(false);
