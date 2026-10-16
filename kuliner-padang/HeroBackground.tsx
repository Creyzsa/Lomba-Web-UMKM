"use client";

import { useEffect, useRef } from "react";

type Props = {
  /** Path gambar latar, misalnya foto Rumah Gadang di folder /public */
  image?: string;
};

type Ember = {
  x: number;
  y: number;
  r: number;
  vy: number;
  sway: number;
  phase: number;
  life: number;
  maxLife: number;
};

/**
 * Latar hero beranimasi:
 * 1. Gambar zoom pelan (Ken Burns)
 * 2. Overlay maroon + vignette
 * 3. Partikel emas yang naik perlahan (seperti bara/uap dari dapur)
 * 4. Cahaya hangat yang berdenyut halus
 *
 * Pemakaian:
 * <section className="relative isolate overflow-hidden">
 *   <HeroBackground image="/images/rumah-gadang.jpg" />
 *   <div className="relative z-10">...konten hero...</div>
 * </section>
 */
export default function HeroBackground({
  image = "assets/img/rumah-gadang.jpg",
}: Props) {
  const canvasRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    // Hormati pengguna yang mematikan animasi di sistemnya
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    let w = 0;
    let h = 0;
    let raf = 0;
    let embers: Ember[] = [];

    const spawn = (initial = false): Ember => {
      const maxLife = 360 + Math.random() * 360;
      return {
        x: Math.random() * w,
        y: initial ? Math.random() * h : h + 10,
        r: 0.6 + Math.random() * 1.8,
        vy: 0.15 + Math.random() * 0.45,
        sway: 8 + Math.random() * 22,
        phase: Math.random() * Math.PI * 2,
        life: initial ? Math.random() * maxLife : 0,
        maxLife,
      };
    };

    const resize = () => {
      const parent = canvas.parentElement;
      if (!parent) return;
      const dpr = Math.min(window.devicePixelRatio || 1, 2);
      w = parent.clientWidth;
      h = parent.clientHeight;
      canvas.width = w * dpr;
      canvas.height = h * dpr;
      canvas.style.width = `${w}px`;
      canvas.style.height = `${h}px`;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

      const count = Math.min(70, Math.round(w / 22));
      embers = Array.from({ length: count }, () => spawn(true));
    };

    const draw = () => {
      ctx.clearRect(0, 0, w, h);

      for (let i = 0; i < embers.length; i++) {
        const e = embers[i];
        e.life += 1;
        e.y -= e.vy;
        const x = e.x + Math.sin(e.life / 60 + e.phase) * e.sway * 0.05 * 10;

        // fade in di awal, fade out di akhir
        const t = e.life / e.maxLife;
        const alpha = Math.sin(Math.min(t, 1) * Math.PI) * 0.75;

        const g = ctx.createRadialGradient(x, e.y, 0, x, e.y, e.r * 4);
        g.addColorStop(0, `rgba(240, 196, 90, ${alpha})`);
        g.addColorStop(1, "rgba(240, 196, 90, 0)");
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.arc(x, e.y, e.r * 4, 0, Math.PI * 2);
        ctx.fill();

        if (e.life >= e.maxLife || e.y < -10) embers[i] = spawn();
      }

      raf = requestAnimationFrame(draw);
    };

    const onVisibility = () => {
      cancelAnimationFrame(raf);
      if (!document.hidden) raf = requestAnimationFrame(draw);
    };

    resize();
    raf = requestAnimationFrame(draw);
    window.addEventListener("resize", resize);
    document.addEventListener("visibilitychange", onVisibility);

    return () => {
      cancelAnimationFrame(raf);
      window.removeEventListener("resize", resize);
      document.removeEventListener("visibilitychange", onVisibility);
    };
  }, []);

  return (
    <div
      aria-hidden="true"
      className="pointer-events-none absolute inset-0 -z-10 overflow-hidden"
    >
      <style>{`
        @keyframes hero-kenburns {
          from { transform: scale(1.05) translate3d(0, 0, 0); }
          to   { transform: scale(1.16) translate3d(-1.5%, -1%, 0); }
        }
        @keyframes hero-glow {
          0%, 100% { opacity: .35; transform: scale(1); }
          50%      { opacity: .6;  transform: scale(1.08); }
        }
        @media (prefers-reduced-motion: reduce) {
          .hero-kenburns, .hero-glow { animation: none !important; }
        }
      `}</style>

      {/* 1. Gambar dengan zoom pelan */}
      <div
        className="hero-kenburns absolute inset-0 bg-cover bg-center will-change-transform"
        style={{
          backgroundImage: `url(${image})`,
          animation: "hero-kenburns 30s ease-in-out infinite alternate",
        }}
      />

      {/* 2. Overlay maroon + vignette */}
      <div className="absolute inset-0 bg-[#3a0d12]/70" />
      <div
        className="absolute inset-0"
        style={{
          background:
            "radial-gradient(ellipse at center, transparent 35%, rgba(20,4,6,.75) 100%)",
        }}
      />

      {/* 3. Cahaya hangat di belakang judul */}
      <div
        className="hero-glow absolute left-1/2 top-[38%] h-[420px] w-[720px] -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl"
        style={{
          background:
            "radial-gradient(circle, rgba(232,178,92,.35), transparent 70%)",
          animation: "hero-glow 7s ease-in-out infinite",
        }}
      />

      {/* 4. Partikel emas */}
      <canvas ref={canvasRef} className="absolute inset-0" />
    </div>
  );
}
