"use client";

import { useEffect, useRef, type ReactNode } from "react";

/* ------------------------------------------------------------------ */
/*  Tipe & data                                                        */
/* ------------------------------------------------------------------ */

export type SongketItem = {
  id: string;
  label: string;
  /** Posisi gantungan dari tengah layar, dalam vw (negatif = kiri, positif = kanan) */
  x: number;
  /** Panjang tali dalam vh: kecil = pendek, besar = menjuntai panjang */
  len: number;
  /** Lebar motif songket dalam vw (dibatasi 52 sampai 170 px) */
  size: number;
  /** Cermin motif secara horizontal supaya tidak terlihat kembar */
  flip?: boolean;
  /** Rentang progress scroll (0 sampai 1) saat motif turun dari atas */
  window: [number, number];
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

/** Rasio tinggi/lebar file songket-minangkabau.png (900 x 1341). */
const MOTIF_RATIO = 1.49;

/** File motif (sudah di-crop dan diperkecil) di /public */
const SONGKET_SRC = "assets/img/songketminangkabau.png";

/** Aset scene bawah (sudah di-crop): jalan + dua rangkiang yang tepi potongnya menempel di tepi layar. */
const ROAD_SRC = "assets/img/jalan.png";
const RANGKIANG_LEFT_SRC = "assets/img/rangkiang1.png";
const RANGKIANG_RIGHT_SRC = "assets/img/rangkiang2.png";
/** Rasio tinggi/lebar masing-masing file */
const ROAD_RATIO = 152 / 2128;
const LEFT_RATIO = 975 / 360;
const RIGHT_RATIO = 975 / 357;

/**
 * Motif menggantung dari tepi atas layar, selang-seling panjang dan pendek.
 * Bagian tengah dibiarkan kosong supaya teks bab tetap terbaca.
 */
const DEFAULT_ITEMS: SongketItem[] = [
  { id: "r1", label: "Songket 1", x: 17, len: 14, size: 9, window: [0.02, 0.24] },
  { id: "l1", label: "Songket 2", x: -17, len: 40, size: 8.5, flip: true, window: [0.05, 0.27] },
  { id: "r2", label: "Songket 3", x: 26, len: 36, size: 9.5, flip: true, window: [0.08, 0.3] },
  { id: "l2", label: "Songket 4", x: -26, len: 12, size: 9, window: [0.11, 0.33] },
  { id: "r3", label: "Songket 5", x: 35, len: 22, size: 8.5, window: [0.14, 0.36] },
  { id: "l3", label: "Songket 6", x: -35, len: 34, size: 9.5, flip: true, window: [0.17, 0.39] },
  { id: "r4", label: "Songket 7", x: 44, len: 44, size: 8, flip: true, window: [0.2, 0.42] },
  { id: "l4", label: "Songket 8", x: -44, len: 20, size: 8.5, window: [0.23, 0.45] },
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
const easeInOutCubic = (t: number) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

/** Motif songket yang tergantung pada seutas benang emas. */
function Songket({ src, flip }: { src: string; flip?: boolean }) {
  return (
    <div className="flex h-full w-full flex-col items-center">
      {/* benang */}
      <div
        aria-hidden="true"
        className="w-[1.5px] shrink-0"
        style={{ height: "var(--mp-len)", background: "linear-gradient(to bottom, rgba(232,178,92,.25), #E8B25C)" }}
      />
      {/* simpul kecil di antara benang dan motif */}
      <div
        aria-hidden="true"
        className="-mb-[3px] h-[7px] w-[7px] shrink-0 rotate-45 bg-[#F4D58D]"
        style={{ boxShadow: "0 0 8px rgba(244,213,141,.7)" }}
      />
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img
        src={src}
        alt=""
        aria-hidden="true"
        draggable={false}
        decoding="async"
        className="block w-full select-none"
        style={{
          transform: flip ? "scaleX(-1)" : undefined,
          filter: "drop-shadow(0 10px 16px rgba(0,0,0,.55)) drop-shadow(0 0 10px rgba(232,178,92,.25))",
        }}
      />
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
  items?: SongketItem[];
  chapters?: Chapter[];
  /** Panjang area scroll dalam vh. Makin besar makin lambat animasinya. */
  scrollLength?: number;
};

/**
 * Hero "scroll-scrubbed": layar terkunci (sticky), seluruh animasi
 * dikendalikan posisi scroll (maju saat turun, mundur saat naik).
 *
 *  0.00 - 0.34  Jalan naik dari bawah, rangkiang kiri dan kanan masuk dari tepi layar
 *  0.00 - 0.45  Konten hero memudar; motif songket turun dari atas lalu menjuntai (ada yang panjang, ada yang pendek)
 *  0.00 - 1.00  Rumah Gadang zoom ke arah pintu, latar menggelap bertahap
 *  0.34 - 0.94  Tiga bab bergantian; bab terakhir bertahan dan tombolnya bisa diklik
 *  0.80 - 1.00  Rangkiang menepi, jalan turun, motif ditarik kembali ke atas saat hero selesai
 */
export default function MinangScrollHero({
  image = "assets/img/rumah-gadang.jpg",
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
  const heightsRef = useRef<number[]>([]);
  const roadRef = useRef<HTMLImageElement>(null);
  const leftRef = useRef<HTMLImageElement>(null);
  const rightRef = useRef<HTMLImageElement>(null);
  const sceneRef = useRef({ roadH: 0, wl: 0, wr: 0 });
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

      /* Scene bawah: jalan selebar layar (sedikit lebih), rangkiang berdiri di atas jalan */
      const roadW = Math.max(vw * 1.25, 900);
      const roadH = roadW * ROAD_RATIO;
      const houseH = Math.min(vh * 0.5, vw * 0.3 * LEFT_RATIO);
      const wl = houseH / LEFT_RATIO;
      const wr = houseH / RIGHT_RATIO;
      sceneRef.current = { roadH, wl, wr };
      if (roadRef.current) {
        roadRef.current.style.width = `${roadW}px`;
        roadRef.current.style.height = `${roadH}px`;
        roadRef.current.style.marginLeft = `${-roadW / 2}px`;
      }
      /* kaki rumah berada di tengah permukaan jalan */
      const footY = roadH * 0.42;
      if (leftRef.current) {
        leftRef.current.style.height = `${houseH}px`;
        leftRef.current.style.width = `${wl}px`;
        leftRef.current.style.bottom = `${footY}px`;
      }
      if (rightRef.current) {
        rightRef.current.style.height = `${houseH}px`;
        rightRef.current.style.width = `${wr}px`;
        rightRef.current.style.bottom = `${footY}px`;
      }

      items.forEach((it, i) => {
        const el = itemRefs.current[i];
        if (!el) return;
        const px = clamp((it.size * vw) / 100, 52, 170);
        const lenPx = (it.len * vh) / 100;
        el.style.width = `${px}px`;
        el.style.marginLeft = `${-px / 2}px`;
        el.style.setProperty("--mp-len", `${lenPx}px`);
        /* tinggi total = benang + simpul + motif */
        heightsRef.current[i] = lenPx + 4 + px * MOTIF_RATIO;
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

      /* Scene bawah: jalan naik dari bawah, rangkiang masuk dari kiri dan kanan.
         Keluar dibalik: rangkiang menepi dulu, baru jalan turun. */
      {
        const { roadH, wl, wr } = sceneRef.current;
        const ease = easeInOutCubic;

        const roadIn = reduced ? 1 : ease(clamp(p / 0.16));
        const roadOut = reduced ? 0 : ease(clamp((p - 0.86) / 0.14));
        if (roadRef.current) {
          const y = (roadH + 12) * (1 - roadIn + roadOut);
          roadRef.current.style.transform = `translate3d(0, ${y}px, 0)`;
          roadRef.current.style.visibility = roadIn <= 0 || roadOut >= 1 ? "hidden" : "visible";
        }

        /* Rangkiang kiri masuk dari tepi luar kiri layar dan menempel di margin luar kiri */
        const leftIn = reduced ? 1 : ease(clamp(p / 0.22));
        const leftOut = reduced ? 0 : ease(clamp((p - 0.8) / 0.12));
        if (leftRef.current) {
          const x = -(wl + 32) * (1 - leftIn) - (wl + 32) * leftOut;
          leftRef.current.style.transform = `translate3d(${x}px, 0, 0)`;
          leftRef.current.style.visibility = leftIn <= 0 || leftOut >= 1 ? "hidden" : "visible";
        }

        /* Rangkiang kanan masuk dari tepi luar kanan layar dan menempel di margin luar kanan */
        const rightIn = reduced ? 1 : ease(clamp(p / 0.22));
        const rightOut = reduced ? 0 : ease(clamp((p - 0.8) / 0.12));
        if (rightRef.current) {
          const x = (wr + 32) * (1 - rightIn) + (wr + 32) * rightOut;
          rightRef.current.style.transform = `translate3d(${x}px, 0, 0)`;
          rightRef.current.style.visibility = rightIn <= 0 || rightOut >= 1 ? "hidden" : "visible";
        }
      }

      /* Songket: turun dari atas (masuk), lalu ditarik naik lagi (keluar) */
      const n = items.length;
      items.forEach((it, i) => {
        const el = itemRefs.current[i];
        if (!el) return;
        const H = (heightsRef.current[i] ?? 400) + 24;
        const dir = it.x < 0 ? -1 : 1;

        /* masuk */
        const [a, b] = it.window;
        const t = reduced ? 1 : clamp((p - a) / (b - a));
        const enter = easeInOutCubic(t);

        /* keluar: bergantian, yang paling akhir masuk paling akhir keluar */
        const exitStart = 0.8 + (n > 1 ? (i / (n - 1)) * 0.06 : 0);
        const ex = reduced ? 0 : easeInOutCubic(clamp((p - exitStart) / 0.14));

        const y = -H * (1 - enter) - H * ex;

        /* ayunan meredam saat tiba, ayunan kecil saat ditarik naik */
        const swing = dir * 7 * Math.pow(1 - t, 1.6) * Math.cos(t * Math.PI * 2.5);
        const pull = dir * 3.5 * Math.sin(ex * Math.PI);

        el.style.transform = `translate3d(0, ${y}px, 0) rotate(${swing + pull}deg)`;
        el.style.visibility = t <= 0 || ex >= 1 ? "hidden" : "visible";
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
      cur += (target - cur) * 0.1;
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
        @keyframes mp-sway {
          from { transform: rotate(-2.4deg); }
          to   { transform: rotate(2.4deg); }
        }
        @media (prefers-reduced-motion: reduce) {
          .mp-sway { animation: none !important; }
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

        {/* Jalan (naik dari bawah) */}
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          ref={roadRef}
          src={ROAD_SRC}
          alt=""
          aria-hidden="true"
          draggable={false}
          decoding="async"
          className="pointer-events-none absolute bottom-0 left-1/2 max-w-none select-none will-change-transform"
          style={{ visibility: "hidden", filter: "drop-shadow(0 -6px 18px rgba(0,0,0,.45))" }}
        />

        {/* Rangkiang kiri (masuk dari kiri) */}
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          ref={leftRef}
          src={RANGKIANG_LEFT_SRC}
          alt=""
          aria-hidden="true"
          draggable={false}
          decoding="async"
          className="pointer-events-none absolute left-0 max-w-none select-none will-change-transform"
          style={{ visibility: "hidden", filter: "drop-shadow(0 14px 22px rgba(0,0,0,.55))" }}
        />

        {/* Rangkiang kanan (masuk dari kanan) */}
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          ref={rightRef}
          src={RANGKIANG_RIGHT_SRC}
          alt=""
          aria-hidden="true"
          draggable={false}
          decoding="async"
          className="pointer-events-none absolute right-0 max-w-none select-none will-change-transform"
          style={{ visibility: "hidden", filter: "drop-shadow(0 14px 22px rgba(0,0,0,.55))" }}
        />

        {/* Songket menjuntai dari atas */}
        {items.map((it, i) => (
          <div
            key={it.id}
            ref={(el) => {
              itemRefs.current[i] = el;
            }}
            aria-label={it.label}
            className="pointer-events-none absolute top-0 will-change-transform"
            style={{
              left: `calc(50% + ${it.x}vw)`,
              transformOrigin: "50% 0",
              visibility: "hidden",
            }}
          >
            <div
              className="mp-sway"
              style={{
                transformOrigin: "50% 0",
                animation: `mp-sway ${3.8 + it.len * 0.06}s ease-in-out ${-i * 0.9}s infinite alternate`,
              }}
            >
              <Songket src={SONGKET_SRC} flip={it.flip} />
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
