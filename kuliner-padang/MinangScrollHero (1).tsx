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
const DOOR = { x: 0, y: 14 };

const DEFAULT_ITEMS: FoodItem[] = [
  { id: "rendang", label: "Rendang", src: "/images/makanan/rendang.jpg", size: 19, to: { x: -33, y: -16, r: -8, s: 1 }, window: [0.02, 0.26], depth: 1.2 },
  { id: "sate", label: "Sate Padang", src: "/images/makanan/sate.jpg", size: 16, to: { x: 31, y: -22, r: 6, s: 1 }, window: [0.06, 0.3], depth: 0.8 },
  { id: "ayam-pop", label: "Ayam Pop", src: "/images/makanan/ayam-pop.jpg", size: 15, to: { x: -37, y: 20, r: 5, s: 0.92 }, window: [0.1, 0.34], depth: 1.5 },
  { id: "dendeng", label: "Dendeng Balado", src: "/images/makanan/dendeng.jpg", size: 17, to: { x: 35, y: 15, r: -6, s: 1.04 }, window: [0.14, 0.38], depth: 1.0 },
  { id: "gulai", label: "Gulai", src: "/images/makanan/gulai.jpg", size: 13, to: { x: -18, y: 33, r: -4, s: 0.85 }, window: [0.16, 0.4], depth: 0.6 },
  { id: "kawa", label: "Kawa Daun", src: "/images/makanan/kawa-daun.jpg", size: 12, to: { x: 17, y: 34, r: 7, s: 0.88 }, window: [0.18, 0.42], depth: 1.3 },
  { id: "lamang", label: "Lamang", src: "/images/makanan/lamang.jpg", size: 12, to: { x: -22, y: -34, r: 10, s: 0.8 }, window: [0.2, 0.44], depth: 0.7 },
  { id: "cabai", label: "Cabai", src: "/images/makanan/cabai.jpg", size: 10, to: { x: 14, y: -35, r: -10, s: 0.75 }, window: [0.22, 0.46], depth: 1.8 },
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

  return (
    <div className="relative h-full w-full">
      <div
        className="absolute inset-0 overflow-hidden rounded-full bg-[#2a0a0e] shadow-[0_24px_50px_rgba(0,0,0,.6)]"
        style={{ boxShadow: "0 24px 50px rgba(0,0,0,.6), 0 0 0 3px #E8B25C, 0 0 0 7px rgba(58,13,18,.9), 0 0 0 8px rgba(232,178,92,.55)" }}
      >
        {failed ? (
          <div className="grid h-full w-full place-items-center px-3 text-center text-sm font-semibold text-[#F4D58D]">
            {label}
          </div>
        ) : (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            src={src}
            alt={label}
            draggable={false}
            decoding="async"
            onError={() => setFailed(true)}
            className="h-full w-full select-none object-cover"
          />
        )}
        {/* kilau tipis supaya terasa seperti permukaan piring */}
        <div
          aria-hidden="true"
          className="absolute inset-0 rounded-full"
          style={{ background: "radial-gradient(circle at 30% 25%, rgba(255,255,255,.18), transparent 45%)" }}
        />
      </div>
      <span className="mp-tag absolute left-1/2 top-[calc(100%+14px)] -translate-x-1/2 whitespace-nowrap rounded-full bg-[#140406]/80 px-3 py-1 text-xs font-medium text-[#F4D58D] backdrop-blur-sm">
        {label}
      </span>
    </div>
  );
}

/* ------------------------------------------------------------------ */
/*  Komponen utama                                                     */
/* ------------------------------------------------------------------ */

type Props = {
  /** Foto Rumah Gadang di /public */
  image?: string;
  /** Konten hero (badge, judul, statistik, tombol). Memudar saat scroll turun. */
  children: ReactNode;
  items?: FoodItem[];
  chapters?: Chapter[];
  /** Panjang area scroll dalam vh. Makin besar makin lambat animasinya. */
  scrollLength?: number;
};

/**
 * Hero "scroll-scrubbed": layar terkunci (sticky), seluruh animasi
 * dikendalikan posisi scroll (maju saat turun, mundur saat naik).
 *
 *  0.00 - 0.26  Konten hero memudar; makanan keluar satu per satu dari pintu Rumah Gadang
 *  0.00 - 1.00  Rumah Gadang zoom ke arah pintu, latar menggelap bertahap
 *  0.34 - 0.94  Tiga bab bergantian; bab terakhir bertahan dan tombolnya bisa diklik
 *  0.86 - 1.00  Makanan memudar saat hero selesai
 */
export default function MinangScrollHero({
  image = "/images/rumah-gadang.jpg",
  children,
  items = DEFAULT_ITEMS,
  chapters = DEFAULT_CHAPTERS,
  scrollLength = 360,
}: Props) {
  const trackRef = useRef<HTMLElement>(null);
  const houseRef = useRef<HTMLDivElement>(null);
  const dimRef = useRef<HTMLDivElement>(null);
  const glowRef = useRef<HTMLDivElement>(null);
  const contentRef = useRef<HTMLDivElement>(null);
  const railRef = useRef<HTMLDivElement>(null);
  const hintRef = useRef<HTMLDivElement>(null);
  const itemRefs = useRef<(HTMLDivElement | null)[]>([]);
  const chapterRefs = useRef<(HTMLDivElement | null)[]>([]);

  useEffect(() => {
    const track = trackRef.current;
    if (!track) return;

    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    let vw = window.innerWidth;
    let vh = window.innerHeight;
    let target = 0;
    let cur = 0;
    let raf = 0;

    const measure = () => {
      vw = window.innerWidth;
      vh = window.innerHeight;
      items.forEach((it, i) => {
        const el = itemRefs.current[i];
        if (!el) return;
        const px = clamp((it.size * vw) / 100, 110, 280);
        el.style.width = `${px}px`;
        el.style.height = `${px}px`;
        el.style.marginLeft = `${-px / 2}px`;
        el.style.marginTop = `${-px / 2}px`;
      });
    };

    const apply = (p: number) => {
      /* Rumah Gadang: zoom ke arah pintu */
      if (houseRef.current) {
        const s = 1.05 + easeInOut(p) * 0.6;
        houseRef.current.style.transform = `translate3d(0, ${-p * vh * 0.08}px, 0) scale(${s})`;
      }
      if (dimRef.current) {
        dimRef.current.style.opacity = String(0.1 + easeInOut(clamp(p / 0.8)) * 0.55);
      }
      if (glowRef.current) {
        glowRef.current.style.opacity = String(0.9 - easeInOut(clamp(p / 0.5)) * 0.9);
      }

      /* Konten hero memudar dan naik */
      if (contentRef.current) {
        const c = easeInOut(clamp(p / 0.26));
        contentRef.current.style.opacity = String(1 - c);
        contentRef.current.style.transform = `translate3d(0, ${-c * 70}px, 0) scale(${1 - c * 0.04})`;
        contentRef.current.style.pointerEvents = c > 0.4 ? "none" : "auto";
      }

      /* Makanan: keluar dari pintu, melengkung ke posisi akhir */
      items.forEach((it, i) => {
        const el = itemRefs.current[i];
        if (!el) return;
        const [a, b] = it.window;
        const raw = clamp((p - a) / (b - a));
        const t = reduced ? 1 : raw;
        const pp = reduced ? 0 : p;

        /* x cepat, y menyusul: lintasan jadi lengkung, bukan garis lurus */
        const tx = easeOutCubic(t);
        const ty = easeOutBack(clamp(t * 1.05));
        const ts = easeOutBack(clamp(t * 1.1));

        const fromR = it.to.r + (it.to.x < 0 ? -35 : 35);

        const x = (lerp(DOOR.x, it.to.x, tx) * vw) / 100;
        const y = ((lerp(DOOR.y, it.to.y, ty) - pp * it.depth * 22) * vh) / 100;
        const r = lerp(fromR, it.to.r, tx) + pp * it.depth * 14;
        const s = lerp(0.06, it.to.s, ts) * (1 + pp * 0.1 * it.depth);

        const outro = 1 - easeInOut(clamp((p - 0.86) / 0.14));
        el.style.opacity = String(clamp(t * 3) * (reduced ? 1 : outro));
        el.style.transform = `translate3d(${x}px, ${y}px, 0) rotate(${r}deg) scale(${s})`;

        const tag = el.querySelector<HTMLElement>(".mp-tag");
        if (tag) tag.style.opacity = String(clamp((t - 0.7) / 0.3));
      });

      /* Bab cerita bergantian; bab terakhir bertahan sampai akhir */
      const start = 0.34;
      const span = (0.94 - start) / chapters.length;
      const lastIdx = chapters.length - 1;
      chapters.forEach((_, i) => {
        const el = chapterRefs.current[i];
        if (!el) return;
        const a = start + i * span;
        const local = clamp((p - a) / span);
        const isLast = i === lastIdx;
        const fadeIn = clamp(local / 0.25);
        const fadeOut = isLast ? 1 : clamp((1 - local) / 0.25);
        const vis = easeInOut(Math.min(fadeIn, fadeOut));
        el.style.opacity = String(reduced ? 0 : vis);
        el.style.transform = `translate3d(0, ${isLast ? lerp(36, 0, easeOutCubic(fadeIn)) : lerp(36, -36, local)}px, 0)`;
        el.style.pointerEvents = !reduced && vis > 0.6 ? "auto" : "none";
        el.style.visibility = vis < 0.01 ? "hidden" : "visible";
      });

      if (railRef.current) railRef.current.style.transform = `scaleY(${p})`;
      if (hintRef.current) hintRef.current.style.opacity = String(1 - clamp(p / 0.08));
    };

    const readTarget = () => {
      const rect = track.getBoundingClientRect();
      const total = track.offsetHeight - vh;
      target = total > 0 ? clamp(-rect.top / total) : 0;
    };

    /* Inersia: nilai mengejar target supaya gerakannya halus di dua arah */
    const tick = () => {
      cur += (target - cur) * 0.12;
      if (Math.abs(target - cur) < 0.0004) cur = target;
      apply(cur);
      raf = cur !== target ? requestAnimationFrame(tick) : 0;
    };

    const kick = () => {
      readTarget();
      if (!raf) raf = requestAnimationFrame(tick);
    };

    measure();

    if (reduced) {
      track.style.height = "100vh";
      apply(0);
      window.addEventListener("resize", measure);
      return () => window.removeEventListener("resize", measure);
    }

    readTarget();
    cur = target;
    apply(cur);

    const onResize = () => {
      measure();
      kick();
    };
    window.addEventListener("scroll", kick, { passive: true });
    window.addEventListener("resize", onResize);
    return () => {
      cancelAnimationFrame(raf);
      window.removeEventListener("scroll", kick);
      window.removeEventListener("resize", onResize);
    };
  }, [items, chapters]);

  return (
    <section
      ref={trackRef}
      aria-label="Peta Rasa Padang"
      className="relative"
      style={{ height: `${scrollLength}vh` }}
    >
      <style>{`
        @keyframes mp-float {
          from { transform: translateY(-7px) rotate(-1.5deg); }
          to   { transform: translateY(7px)  rotate(1.5deg); }
        }
        @media (prefers-reduced-motion: reduce) {
          .mp-float { animation: none !important; }
        }
      `}</style>

      <div className="sticky top-0 h-screen overflow-hidden bg-[#1a0508]">
        {/* Rumah Gadang */}
        <div
          ref={houseRef}
          className="absolute inset-0 bg-cover bg-center will-change-transform"
          style={{ backgroundImage: `url(${image})`, transformOrigin: "50% 65%", transform: "scale(1.05)" }}
        />

        {/* Warna maroon + vignette */}
        <div className="absolute inset-0 bg-[#3a0d12]/65" />
        <div
          className="absolute inset-0"
          style={{ background: "radial-gradient(ellipse at center, transparent 35%, rgba(20,4,6,.8) 100%)" }}
        />
        <div ref={dimRef} className="absolute inset-0 bg-[#140406]" style={{ opacity: 0.1 }} />

        {/* Cahaya hangat */}
        <div
          ref={glowRef}
          className="absolute left-1/2 top-[40%] h-[460px] w-[760px] -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl"
          style={{ background: "radial-gradient(circle, rgba(232,178,92,.35), transparent 70%)" }}
        />

        {/* Makanan (foto asli, keluar dari pintu) */}
        {items.map((it, i) => (
          <div
            key={it.id}
            ref={(el) => {
              itemRefs.current[i] = el;
            }}
            className="pointer-events-none absolute left-1/2 top-1/2 will-change-transform"
            style={{ opacity: 0 }}
          >
            <div
              className="mp-float h-full w-full"
              style={{ animation: `mp-float ${6 + (i % 3)}s ease-in-out ${i * 0.4}s infinite alternate` }}
            >
              <Plate src={it.src} label={it.label} />
            </div>
          </div>
        ))}

        {/* Bab cerita */}
        {chapters.map((ch, i) => (
          <div
            key={`${i}-${ch.title || ch.kicker || "bab"}`}
            ref={(el) => {
              chapterRefs.current[i] = el;
            }}
            className="absolute inset-0 z-10 flex flex-col items-center justify-center px-6 text-center"
            style={{ opacity: 0, pointerEvents: "none", visibility: "hidden" }}
          >
            {ch.kicker && (
              <span className="mb-4 max-w-2xl text-sm font-medium tracking-wide text-[#E8B25C] sm:text-base">
                {ch.kicker}
              </span>
            )}

            {ch.title && (
              <h2
                className={`max-w-4xl font-bold text-[#F4D58D] ${
                  ch.text ? "text-2xl leading-tight sm:text-4xl" : "text-5xl sm:text-7xl"
                }`}
              >
                {ch.title}
              </h2>
            )}

            {ch.text && (
              <p className="mt-5 max-w-2xl text-base leading-relaxed text-white/85 sm:text-lg">{ch.text}</p>
            )}

            {ch.stats && (
              <dl className="flex max-w-3xl flex-wrap items-stretch justify-center gap-y-6">
                {ch.stats.map((s, k) => (
                  <div
                    key={s.label}
                    className={`px-6 sm:px-10 ${k > 0 ? "sm:border-l sm:border-[#E8B25C]/35" : ""}`}
                  >
                    <dd className="font-serif text-6xl font-bold leading-none text-[#F4D58D] sm:text-7xl">
                      {s.value}
                    </dd>
                    <dt className="mt-2 text-sm text-white/80 sm:text-base">{s.label}</dt>
                  </div>
                ))}
              </dl>
            )}

            {ch.actions && (
              <div className="mt-10 flex flex-wrap items-center justify-center gap-3">
                {ch.actions.map((a) => (
                  <a
                    key={a.label}
                    href={a.href}
                    className={
                      a.primary
                        ? "rounded-full bg-[#E8B25C] px-7 py-3 text-sm font-semibold text-[#2a0a0e] transition hover:bg-[#F4D58D] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F4D58D] sm:text-base"
                        : "rounded-full border border-[#E8B25C]/70 px-7 py-3 text-sm font-semibold text-[#F4D58D] transition hover:bg-[#E8B25C]/15 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F4D58D] sm:text-base"
                    }
                  >
                    {a.label}
                  </a>
                ))}
              </div>
            )}
          </div>
        ))}

        {/* Konten hero (badge, judul, statistik, tombol) */}
        <div
          ref={contentRef}
          className="absolute inset-0 z-20 flex flex-col items-center justify-center px-4 pt-20 will-change-transform"
        >
          {children}
        </div>

        {/* Indikator scroll */}
        <div
          ref={hintRef}
          className="pointer-events-none absolute inset-x-0 bottom-6 z-20 text-center text-xs uppercase tracking-[0.3em] text-white/70"
        >
          ↓ Scroll
        </div>
        <div className="pointer-events-none absolute bottom-10 right-5 top-24 z-20 hidden w-px bg-white/15 sm:block">
          <div
            ref={railRef}
            className="h-full w-full origin-top bg-[#E8B25C]"
            style={{ transform: "scaleY(0)" }}
          />
        </div>
      </div>
    </section>
  );
}
