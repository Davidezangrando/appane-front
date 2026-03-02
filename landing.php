<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> — Pane artigianale a domicilio</title>
    <link href="<?= SITE_URL ?>/assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #F8F5F1;
            color: #2C1810;
        }

        a { text-decoration: none; }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(196,134,42,0.18) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(122,82,64,0.25) 0%, transparent 55%),
                linear-gradient(160deg, #1a0e09 0%, #2C1810 40%, #4E3228 75%, #7A5240 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 3rem 1.5rem 4rem;
            position: relative;
            overflow: hidden;
        }

        /* decorative grain overlay */
        .hero::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4'%3E%3Crect width='4' height='4' fill='transparent'/%3E%3Ccircle cx='1' cy='1' r='0.5' fill='%23ffffff' fill-opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
        }

        .hero-inner { position: relative; z-index: 1; max-width: 680px; }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(196,134,42,0.2);
            border: 1px solid rgba(196,134,42,0.45);
            color: #e8b96a;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.35rem 1rem;
            border-radius: 50px;
            margin-bottom: 1.75rem;
        }

        .hero-emoji {
            font-size: 4rem;
            line-height: 1;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));
        }

        .hero-title {
            font-size: clamp(2.6rem, 7vw, 4.2rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.08;
            letter-spacing: -1.5px;
            margin-bottom: 0.5rem;
        }

        .hero-title span {
            color: #C4862A;
        }

        .hero-divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #C4862A, #e8b96a);
            border-radius: 2px;
            margin: 1.25rem auto;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 2.2vw, 1.15rem);
            color: rgba(255,255,255,0.72);
            line-height: 1.65;
            margin-bottom: 2.5rem;
            max-width: 520px;
        }

        .hero-cta {
            display: flex;
            gap: 0.9rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 3.5rem;
        }

        .btn-primary-cta {
            background: #C4862A;
            color: #fff;
            border: none;
            padding: 0.9rem 2.2rem;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
            box-shadow: 0 4px 18px rgba(196,134,42,0.45);
        }
        .btn-primary-cta:hover {
            background: #a8711f;
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(196,134,42,0.55);
            color: #fff;
        }

        .btn-secondary-cta {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.4);
            padding: 0.9rem 2.2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-secondary-cta:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.7);
            color: #fff;
        }

        .hero-pills {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            color: rgba(255,255,255,0.7);
            font-size: 0.82rem;
            padding: 0.4rem 0.9rem;
            border-radius: 50px;
        }

        /* ── WAVE ── */
        .wave-divider {
            display: block;
            width: 100%;
            line-height: 0;
            background: #1a0e09;
        }
        .wave-divider svg { display: block; }

        /* ── FEATURES ── */
        .features-section {
            background: #F8F5F1;
            padding: 4rem 1.5rem;
        }
        .section-label {
            text-align: center;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #C4862A;
            margin-bottom: 0.5rem;
        }
        .section-title {
            text-align: center;
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            font-weight: 800;
            color: #2C1810;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }
        .section-subtitle {
            text-align: center;
            color: #7A6054;
            max-width: 440px;
            margin: 0 auto 3rem;
            font-size: 0.97rem;
            line-height: 1.6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .feature-card {
            background: #fff;
            border: 1px solid #E8DDD5;
            border-radius: 16px;
            padding: 1.75rem 1.5rem;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(44,24,16,0.1);
        }
        .feature-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #4E3228, #7A5240);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.1rem;
            font-size: 1.5rem;
            color: #fff;
        }
        .feature-card h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #2C1810;
            margin-bottom: 0.5rem;
        }
        .feature-card p {
            font-size: 0.875rem;
            color: #7A6054;
            line-height: 1.6;
        }

        /* ── HOW IT WORKS ── */
        .how-section {
            background: linear-gradient(160deg, #2C1810 0%, #4E3228 100%);
            padding: 4.5rem 1.5rem;
            color: #fff;
        }
        .how-section .section-label { color: #e8b96a; }
        .how-section .section-title { color: #fff; }
        .how-section .section-subtitle { color: rgba(255,255,255,0.6); }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 820px;
            margin: 0 auto;
            position: relative;
        }
        .step {
            text-align: center;
        }
        .step-number {
            width: 52px;
            height: 52px;
            border: 2px solid rgba(196,134,42,0.6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.1rem;
            font-size: 1.3rem;
            font-weight: 800;
            color: #C4862A;
        }
        .step h3 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.45rem;
            color: #fff;
        }
        .step p {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.58);
            line-height: 1.6;
        }

        /* ── FOOTER CTA ── */
        .footer-cta {
            background: #F0E6DE;
            padding: 3.5rem 1.5rem;
            text-align: center;
            border-top: 1px solid #E8DDD5;
        }
        .footer-cta h2 {
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 800;
            color: #2C1810;
            margin-bottom: 0.5rem;
        }
        .footer-cta p {
            color: #7A6054;
            margin-bottom: 1.75rem;
            font-size: 0.97rem;
        }

        /* ── SITE FOOTER ── */
        .site-footer {
            background: #1a0e09;
            color: rgba(255,255,255,0.5);
            text-align: center;
            padding: 1.5rem 1rem;
            font-size: 0.8rem;
        }
        .site-footer strong { color: rgba(255,255,255,0.85); }

        @media (max-width: 600px) {
            .hero-cta { flex-direction: column; align-items: center; }
            .hero-title { letter-spacing: -0.5px; }
        }
    </style>
</head>
<body>

<!-- ═══ HERO ═══ -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge">
            <i class="bi bi-award-fill"></i> Panificio Artigianale
        </div>
        <div class="hero-emoji">🍞</div>
        <h1 class="hero-title"><?= SITE_NAME ?><br><span>Pane vero,</span> ogni venerdì.</h1>
        <div class="hero-divider"></div>
        <p class="hero-subtitle">
            Pane a lievitazione naturale con lievito madre,
            impastato a mano e consegnato direttamente a casa tua ogni venerdì mattina.
            Ordina entro <strong style="color:#e8b96a">giovedì sera</strong>!
        </p>
        <div class="hero-cta">
            <a href="<?= SITE_URL ?>/" class="btn-primary-cta">
                <i class="bi bi-basket2-fill"></i> Ordina ora
            </a>
            <?php if (isLoggedIn()): ?>
                <a href="<?= SITE_URL ?>/dashboard/" class="btn-secondary-cta">
                    <i class="bi bi-speedometer2"></i> La mia area
                </a>
            <?php else: ?>
                <a href="<?= SITE_URL ?>/auth/login.php" class="btn-secondary-cta">
                    <i class="bi bi-person-circle"></i> Accedi
                </a>
            <?php endif; ?>
        </div>
        <div class="hero-pills">
            <span class="hero-pill"><i class="bi bi-patch-check-fill"></i> Lievito madre</span>
            <span class="hero-pill"><i class="bi bi-truck"></i> Consegna a domicilio</span>
            <span class="hero-pill"><i class="bi bi-heart-fill"></i> Fatto a mano</span>
        </div>
    </div>
</section>

<!-- wave -->
<div class="wave-divider">
    <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" height="50">
        <path d="M0,40 C360,80 1080,0 1440,40 L1440,0 L0,0 Z" fill="#1a0e09"/>
    </svg>
</div>

<!-- ═══ FEATURES ═══ -->
<section class="features-section">
    <p class="section-label">Perché sceglierci</p>
    <h2 class="section-title">Artigianalità in ogni fetta</h2>
    <p class="section-subtitle">Nessun additivo, nessuna farina raffinata. Solo ingredienti veri e la pazienza di chi ama il proprio lavoro.</p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-award-fill"></i></div>
            <h3>Lievito Madre Naturale</h3>
            <p>Fermentazione lenta di 24 ore per un pane più digeribile, profumato e ricco di sapore autentico.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-truck"></i></div>
            <h3>Consegna a Domicilio</h3>
            <p>Ritiri il venerdì mattina direttamente a casa tua. Nessuna coda, nessuna frenesia — solo pane fresco.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="bi bi-calendar3-week"></i></div>
            <h3>Menu Settimanale</h3>
            <p>Ogni settimana un nuovo assortimento. Scopri i prodotti del momento e scegli i tuoi preferiti.</p>
        </div>
    </div>
</section>

<!-- ═══ HOW IT WORKS ═══ -->
<section class="how-section">
    <p class="section-label">Come funziona</p>
    <h2 class="section-title">Semplice come mangiare il pane</h2>
    <p class="section-subtitle">Tre passi e il tuo pane artigianale ti aspetta a casa.</p>

    <div class="steps-grid">
        <div class="step">
            <div class="step-number">1</div>
            <h3>Sfoglia il menu</h3>
            <p>Il mercoledì pubblichiamo i prodotti della settimana. Scegli quello che ti piace.</p>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <h3>Ordina entro giovedì</h3>
            <p>Aggiungi al carrello e conferma l'ordine. Ricevi una conferma immediata.</p>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <h3>Pane fresco venerdì</h3>
            <p>Il venerdì mattina il tuo pane è già sulla soglia di casa. Buona colazione!</p>
        </div>
    </div>
</section>

<!-- ═══ FOOTER CTA ═══ -->
<section class="footer-cta">
    <h2>Pronto a ordinare? 🍞</h2>
    <p>Scopri il menu di questa settimana e scegli il tuo pane preferito.</p>
    <a href="<?= SITE_URL ?>/" class="btn-primary-cta" style="margin: 0 auto; display: inline-flex;">
        <i class="bi bi-basket2-fill"></i> Vedi il menu della settimana
    </a>
</section>

<!-- ═══ SITE FOOTER ═══ -->
<footer class="site-footer">
    <p><strong><?= SITE_NAME ?></strong> — Panificio Artigianale &mdash; Pane fresco ogni venerd&igrave;</p>
</footer>

</body>
</html>
