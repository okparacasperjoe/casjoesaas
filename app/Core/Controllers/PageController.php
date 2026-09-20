<?php

namespace App\Core\Controllers;

use App\Core\Database;
use PDO;

class PageController
{
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function page($params)
    {
        // Simple router logic might pass slug, or we detect it
        // Assuming Route: /page/{slug} or /about -> slug=about-us if mapped manually
        // But better: /custom-page/{slug}
        // User asked for specific pages: about-us, privacy-policy, terms.
        
        $slug = $params['slug'] ?? '';
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM cms_pages WHERE slug = ?");
            $stmt->execute([$slug]);
            $page = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $page = false;
        }

        $aboutHtml = '<div class="about-content" style="line-height: 1.8; font-size: 1.05rem;">
    <h1 style="color: var(--brand-blue, #000066); font-size: 2.4rem; font-weight: 800; margin-bottom: 10px;">About Us</h1>
    <h2 style="color: var(--brand-amber, #FFA600); font-size: 1.5rem; font-weight: 700; margin-top: 0; margin-bottom: 25px;">Building Africa\'s AI Business Operating System</h2>

    <p>At <strong>Casjoetech Ltd</strong>, we\'re on a mission to transform how African businesses operate by making artificial intelligence accessible, practical, and affordable.</p>

    <p>We believe small and medium-sized businesses deserve the same powerful technology used by large enterprises. That\'s why we built <strong>Casjoe Biz</strong>—an AI-powered Business Operating System that brings together customer management, sales, finance, inventory, HR, business automation, and intelligent insights into one unified platform.</p>

    <p>Our goal is simple: help businesses spend less time managing operations and more time growing.</p>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 15px;">Our Story</h2>

    <p><strong>Casjoetech</strong> was founded to solve one of Africa\'s biggest business challenges: fragmented systems and inefficient processes.</p>

    <p>Too many businesses rely on multiple disconnected tools, spreadsheets, manual record-keeping, and repetitive tasks that slow growth and reduce productivity.</p>

    <p>We envisioned a single intelligent platform that could simplify business management while leveraging artificial intelligence to automate routine work, improve decision-making, and unlock sustainable growth.</p>

    <p>That vision became <strong>Casjoe Biz</strong>.</p>

    <p>Today, our platform empowers entrepreneurs, startups, and growing businesses with enterprise-grade tools designed specifically for the African market.</p>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 15px;">What We Do</h2>

    <p>We build intelligent software that enables businesses to:</p>

    <ul style="list-style: none; padding-left: 0; margin: 20px 0;">
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Manage customers with an integrated CRM</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Track sales, invoices, and payments</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Monitor finances and business performance</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Manage inventory and operations</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Automate repetitive workflows</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Generate AI-powered business insights</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Improve team collaboration</li>
        <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><strong style="color: var(--brand-amber, #FFA600); font-size: 1.2rem;">✓</strong> Scale confidently with cloud technology</li>
    </ul>

    <p style="font-weight: 600; font-size: 1.1rem; color: var(--brand-blue, #000066);">Everything is connected through one secure platform.</p>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin: 30px 0;">
        <div style="background: rgba(0, 0, 102, 0.04); padding: 25px; border-radius: 12px; border-left: 4px solid var(--brand-blue, #000066);">
            <h3 style="color: var(--brand-blue, #000066); font-size: 1.4rem; font-weight: 700; margin-top: 0;">Our Vision</h3>
            <p style="margin-bottom: 0;">To become Africa\'s leading AI Business Operating System, enabling millions of businesses to operate smarter, grow faster, and compete globally.</p>
        </div>
        <div style="background: rgba(255, 166, 0, 0.08); padding: 25px; border-radius: 12px; border-left: 4px solid var(--brand-amber, #FFA600);">
            <h3 style="color: var(--brand-blue, #000066); font-size: 1.4rem; font-weight: 700; margin-top: 0;">Our Mission</h3>
            <p style="margin-bottom: 0;">To empower businesses across Africa with intelligent technology that simplifies operations, improves productivity, and accelerates sustainable growth through AI-driven innovation.</p>
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 20px;">Our Core Values</h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 35px;">
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Innovation</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">We continuously build practical technology that solves real business challenges.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Customer Success</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">Our customers\' growth defines our success.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Simplicity</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">Complex business management should feel effortless.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Excellence</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">We deliver reliable, secure, and scalable technology.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-amber, #FFA600); font-size: 1.2rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Integrity</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">We build trust through transparency, accountability, and ethical innovation.</p>
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 20px;">Why Businesses Choose Casjoe</h2>

    <div style="margin-bottom: 35px;">
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">🤖 AI-Powered by Design</h4>
            <p style="margin: 0; color: #334155;">Artificial intelligence is embedded throughout our platform to automate tasks, generate insights, and improve business performance.</p>
        </div>
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">⚡ One Unified Platform</h4>
            <p style="margin: 0; color: #334155;">Manage your business from a single dashboard instead of juggling multiple software tools.</p>
        </div>
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">🌍 Built for African Businesses</h4>
            <p style="margin: 0; color: #334155;">Designed with local business realities in mind while meeting global technology standards.</p>
        </div>
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">🔒 Secure and Scalable</h4>
            <p style="margin: 0; color: #334155;">Cloud-based infrastructure that grows with your business.</p>
        </div>
        <div style="margin-bottom: 20px;">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">📈 Business Growth Focused</h4>
            <p style="margin: 0; color: #334155;">Every feature is designed to help businesses increase productivity, improve decision-making, and achieve sustainable growth.</p>
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <div style="background: linear-gradient(135deg, var(--brand-blue, #000066), #000033); color: #ffffff; padding: 35px; border-radius: 16px; text-align: center; margin-top: 40px; box-shadow: 0 10px 25px rgba(0, 0, 102, 0.2);">
        <h2 style="color: var(--brand-amber, #FFA600); font-size: 1.8rem; font-weight: 800; margin-top: 0; margin-bottom: 15px;">Looking Ahead</h2>
        <p style="font-size: 1.15rem; max-width: 750px; margin: 0 auto 15px auto; line-height: 1.7;">We\'re building more than software.</p>
        <p style="font-size: 1.15rem; max-width: 750px; margin: 0 auto 20px auto; line-height: 1.7; font-weight: 600;">We\'re building the intelligent infrastructure that will power the next generation of African businesses.</p>
        <p style="font-size: 1rem; max-width: 750px; margin: 0 auto; line-height: 1.7; color: rgba(255,255,255,0.85);">Through continuous innovation in artificial intelligence, automation, and cloud technology, <strong>Casjoetech</strong> is creating a future where every entrepreneur has access to world-class business tools regardless of size, location, or industry.</p>
    </div>
</div>';

        $accessibilityHtml = '<div class="accessibility-content" style="line-height: 1.8; font-size: 1.05rem;">
    <h1 style="color: var(--brand-blue, #000066); font-size: 2.4rem; font-weight: 800; margin-bottom: 10px;">Accessibility Statement</h1>
    <h2 style="color: var(--brand-amber, #FFA600); font-size: 1.5rem; font-weight: 700; margin-top: 0; margin-bottom: 25px;">Our Commitment to Universal Inclusion &amp; Digital Accessibility</h2>

    <p><strong>Casjoe LLC</strong> is committed to ensuring digital accessibility for all users, including individuals with physical, sensory, cognitive, and neurological disabilities. We continuously enhance the user experience across our entire software ecosystem and apply relevant accessibility guidelines to ensure an inclusive, barrier-free business operating system.</p>

    <div style="background: rgba(0, 0, 102, 0.04); border-left: 4px solid var(--brand-blue, #000066); border-radius: 8px; padding: 20px 24px; margin: 25px 0;">
        <h3 style="color: var(--brand-blue, #000066); font-size: 1.25rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">Conformance Standard: WCAG 2.1 Level AA</h3>
        <p style="margin: 0; color: #334155;">
            Our platform targets compliance with the <strong>Web Content Accessibility Guidelines (WCAG) 2.1 Level AA</strong> benchmarks set forth by the World Wide Web Consortium (W3C). These guidelines outline best practices to ensure digital content is <strong>Perceivable, Operable, Understandable, and Robust</strong>.
        </p>
    </div>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 15px;">Built-in Accessibility Suite Features</h2>
    <p>Every Casjoe page includes our lightweight floating Accessibility Suite, accessible via the blue icon at the bottom-left of the screen or using the keyboard shortcut <kbd style="background:#1e293b; color:#fff; padding:3px 6px; border-radius:4px; font-size:0.8rem;">Alt + A</kbd>:</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 25px 0;">
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.15rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">🎙️ Text-to-Speech (Voice Reader)</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">Click any paragraph, heading, card, or table cell to have it read aloud using the browser\'s native speech engine with customizable speeds (0.8x, 1.0x, 1.2x) and visual spotlight highlighting.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.15rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">🔦 Reading Focus Mask</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">Dims the top and bottom of the viewport with a soft black overlay, creating a 110px reading spotlight that follows the cursor to aid users with ADHD and cognitive tracking fatigue.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.15rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">📏 Reading Guide Ruler</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">A horizontal tracking ruler line that moves with your mouse to prevent line skipping across dense data tables, financial ledgers, and CRM records.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.15rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">📖 Dyslexia-Optimized Typography</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">Switches the entire platform interface to specialized dyslexia-friendly typography with weighted letterforms and enhanced character spacing.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.15rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">🎨 High-Contrast Modes</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">High Contrast Dark, High Contrast Light, and Monochrome modes with automated shielding to prevent inversion of logos, photos, QR codes, and virtual cards.</p>
        </div>
        <div style="padding: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
            <h4 style="color: var(--brand-blue, #000066); font-size: 1.15rem; font-weight: 700; margin-top: 0; margin-bottom: 8px;">⏸️ Animation Freezing</h4>
            <p style="margin: 0; font-size: 0.95rem; color: #475569;">Instantly halts all transitions and CSS keyframe animations for users with vestibular disorders or motion triggers.</p>
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 15px;">Keyboard Navigation Shortcuts</h2>
    <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
        <thead>
            <tr style="background: #000066; color: #ffffff; text-align: left;">
                <th style="padding: 12px 16px; font-weight: 700;">Keystroke</th>
                <th style="padding: 12px 16px; font-weight: 700;">Function</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px solid #e2e8f0;">
                <td style="padding: 12px 16px;"><kbd style="background:#f1f5f9; padding:4px 8px; border:1px solid #cbd5e1; border-radius:4px; font-weight:700;">Alt + A</kbd></td>
                <td style="padding: 12px 16px;">Open or close the Accessibility Suite toolbar from any page</td>
            </tr>
            <tr style="border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <td style="padding: 12px 16px;"><kbd style="background:#f1f5f9; padding:4px 8px; border:1px solid #cbd5e1; border-radius:4px; font-weight:700;">Tab</kbd> (on page load)</td>
                <td style="padding: 12px 16px;">Reveals "Skip to Main Content" banner to bypass headers directly to workspace</td>
            </tr>
            <tr style="border-bottom: 1px solid #e2e8f0;">
                <td style="padding: 12px 16px;"><kbd style="background:#f1f5f9; padding:4px 8px; border:1px solid #cbd5e1; border-radius:4px; font-weight:700;">Tab</kbd> / <kbd style="background:#f1f5f9; padding:4px 8px; border:1px solid #cbd5e1; border-radius:4px; font-weight:700;">Shift + Tab</kbd></td>
                <td style="padding: 12px 16px;">Navigate forward or backward through interactive links, buttons, and form inputs</td>
            </tr>
            <tr style="border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                <td style="padding: 12px 16px;"><kbd style="background:#f1f5f9; padding:4px 8px; border:1px solid #cbd5e1; border-radius:4px; font-weight:700;">Enter</kbd> / <kbd style="background:#f1f5f9; padding:4px 8px; border:1px solid #cbd5e1; border-radius:4px; font-weight:700;">Space</kbd></td>
                <td style="padding: 12px 16px;">Activate focused links, buttons, or checkboxes</td>
            </tr>
            <tr>
                <td style="padding: 12px 16px;"><kbd style="background:#f1f5f9; padding:4px 8px; border:1px solid #cbd5e1; border-radius:4px; font-weight:700;">Escape</kbd></td>
                <td style="padding: 12px 16px;">Dismiss open accessibility dialog, modals, or dropdown menus</td>
            </tr>
        </tbody>
    </table>

    <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 35px 0;">

    <h2 style="color: var(--brand-blue, #000066); font-size: 1.8rem; font-weight: 700; margin-bottom: 15px;">Accessibility Feedback &amp; Assistance</h2>
    <p>We welcome your feedback. If you encounter any accessibility hurdles, experience difficulties using assistive hardware or screen readers, or have suggestions for improvements, please reach out to us:</p>

    <ul style="list-style: none; padding-left: 0; margin: 20px 0;">
        <li style="margin-bottom: 10px;"><strong>Email:</strong> <a href="mailto:hello@casjoe.com" style="color: var(--brand-blue, #000066); font-weight: 600;">hello@casjoe.com</a></li>
        <li style="margin-bottom: 10px;"><strong>Online Contact Form:</strong> <a href="/contact" style="color: var(--brand-blue, #000066); font-weight: 600;">casjoe.com/contact</a></li>
        <li style="margin-bottom: 10px;"><strong>Expected Response:</strong> We aim to acknowledge and address accessibility reports within 2 business days.</li>
    </ul>

    <p style="font-size: 0.9rem; color: #64748b; margin-top: 30px;">This Accessibility Statement was last updated in September 2026.</p>
</div>';

        if ($slug === 'about-us' && (!$page || strpos($page['content'] ?? '', "Building Africa's AI Business Operating System") === false)) {
            try {
                $this->pdo->exec("CREATE TABLE IF NOT EXISTS `cms_pages` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `tenant_id` INT NULL,
                    `title` VARCHAR(255) NOT NULL,
                    `slug` VARCHAR(255) NOT NULL UNIQUE,
                    `content` LONGTEXT,
                    `meta_title` VARCHAR(255) DEFAULT NULL,
                    `meta_description` TEXT DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )");
                $stmtUp = $this->pdo->prepare("INSERT INTO cms_pages (tenant_id, title, slug, content) VALUES (1, 'About Us', 'about-us', ?) ON DUPLICATE KEY UPDATE content = VALUES(content)");
                $stmtUp->execute([$aboutHtml]);
                $page = ['title' => 'About Us', 'slug' => 'about-us', 'content' => $aboutHtml];
            } catch (\Exception $ex) {
                $page = ['title' => 'About Us', 'slug' => 'about-us', 'content' => $aboutHtml];
            }
        }

        if (in_array($slug, ['accessibility-statement', 'accessibility']) && (!$page || strpos($page['content'] ?? '', 'Casjoe LLC') === false)) {
            try {
                $this->pdo->exec("CREATE TABLE IF NOT EXISTS `cms_pages` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `tenant_id` INT NULL,
                    `title` VARCHAR(255) NOT NULL,
                    `slug` VARCHAR(255) NOT NULL UNIQUE,
                    `content` LONGTEXT,
                    `meta_title` VARCHAR(255) DEFAULT NULL,
                    `meta_description` TEXT DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )");
                $stmtUp = $this->pdo->prepare("INSERT INTO cms_pages (tenant_id, title, slug, content) VALUES (1, 'Accessibility Statement', ?, ?) ON DUPLICATE KEY UPDATE content = VALUES(content)");
                $stmtUp->execute([$slug, $accessibilityHtml]);
                $page = ['title' => 'Accessibility Statement', 'slug' => $slug, 'content' => $accessibilityHtml];
            } catch (\Exception $ex) {
                $page = ['title' => 'Accessibility Statement', 'slug' => $slug, 'content' => $accessibilityHtml];
            }
        }

        if (!$page) {
            $defaults = [
                'about-us' => [
                    'title' => 'About Us',
                    'slug' => 'about-us',
                    'content' => $aboutHtml
                ],
                'privacy-policy' => [
                    'title' => 'Privacy Policy',
                    'slug' => 'privacy-policy',
                    'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us. This policy explains how we collect, use, and protect your personal information.</p><h2>1. Information We Collect</h2><p>We collect information you provide directly to us, such as when you create an account, use our services, or communicate with us.</p><h2>2. How We Use Information</h2><p>We use the information we collect to provide, maintain, and improve our services, and to communicate with you.</p>'
                ],
                'terms-of-service' => [
                    'title' => 'Terms of Service',
                    'slug' => 'terms-of-service',
                    'content' => '<h1>Terms of Service</h1><p>By using Casjoe Apps, you agree to these terms. Please read them carefully.</p><h2>1. Use of Services</h2><p>You must follow any policies made available to you within the Services.</p><h2>2. Your Account</h2><p>You may need a Casjoe Account in order to use some of our Services.</p>'
                ],
                'accessibility-statement' => [
                    'title' => 'Accessibility Statement',
                    'slug' => 'accessibility-statement',
                    'content' => $accessibilityHtml
                ],
                'accessibility' => [
                    'title' => 'Accessibility Statement',
                    'slug' => 'accessibility',
                    'content' => $accessibilityHtml
                ]
            ];

            if (isset($defaults[$slug])) {
                $page = $defaults[$slug];
                try {
                    $this->pdo->exec("CREATE TABLE IF NOT EXISTS `cms_pages` (
                        `id` INT AUTO_INCREMENT PRIMARY KEY,
                        `tenant_id` INT NULL,
                        `title` VARCHAR(255) NOT NULL,
                        `slug` VARCHAR(255) NOT NULL UNIQUE,
                        `content` LONGTEXT,
                        `meta_title` VARCHAR(255) DEFAULT NULL,
                        `meta_description` TEXT DEFAULT NULL,
                        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )");
                    $ins = $this->pdo->prepare("INSERT IGNORE INTO cms_pages (title, slug, content) VALUES (?, ?, ?)");
                    $ins->execute([$page['title'], $page['slug'], $page['content']]);
                } catch (\Exception $ex) {}
            } else {
                http_response_code(404);
                echo "Page Not Found";
                return;
            }
        }

        require __DIR__ . '/../../Views/page.php';
    }

    public function about() { $this->page(['slug' => 'about-us']); }
    public function privacy() { $this->page(['slug' => 'privacy-policy']); }
    public function terms() { $this->page(['slug' => 'terms-of-service']); }
    public function accessibility() { $this->page(['slug' => 'accessibility-statement']); }

    public function contact()
    {
        require __DIR__ . '/../../Views/contact.php';
    }

    public function blogIndex()
    {
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS cms_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tenant_id INT NULL,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                summary TEXT,
                content LONGTEXT,
                featured_image VARCHAR(255) DEFAULT NULL,
                is_published TINYINT(1) DEFAULT 1,
                meta_title VARCHAR(255) DEFAULT NULL,
                meta_description TEXT DEFAULT NULL,
                meta_keywords VARCHAR(500) DEFAULT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $stmtCount = $this->pdo->query("SELECT COUNT(*) FROM cms_posts");
            if ($stmtCount && (int)$stmtCount->fetchColumn() === 0) {
                $post1Content = "<p>Welcome to Casjoe, the all-in-one AI-driven SaaS platform. In this post, we explore how autonomous AI employees revolutionize daily operations, from automated customer support to multi-currency financial accounting.</p><h2>The AI Revolution in Enterprise</h2><p>Gone are the days of siloed software tools. With Casjoe's built-in AI employees, businesses can automate repetitive tasks, issue virtual cards, and manage multi-vendor shops right from a single unified dashboard.</p>";
                $post2Content = "<p>Global commerce requires borderless payment infrastructure. We are excited to announce the expansion of Casjoe Virtual Cards, powered by instant bank integrations and secure one-tap checkout.</p><h2>Issue Instant Virtual Cards</h2><p>With customizable spending limits and instant multi-currency funding, your team can operate globally with complete security and peace of mind.</p>";

                $stmtSeed = $this->pdo->prepare("INSERT INTO cms_posts (tenant_id, title, slug, summary, content, is_published) VALUES (?, ?, ?, ?, ?, 1)");
                $stmtSeed->execute([1, 'The Future of Autonomous AI Employees in Business', 'future-of-autonomous-ai-employees', 'Discover how Casjoe AI Employees automate ERP workflows, sales pipelines, and financial operations.', $post1Content]);
                $stmtSeed->execute([1, 'Introducing Instant Multi-Currency Virtual Cards', 'introducing-instant-virtual-cards', 'Issue, fund, and manage Visa and Mastercard virtual cards right from your Casjoe dashboard.', $post2Content]);
            }
        } catch (\Exception $e) { /* Ignore */ }

        $stmt = $this->pdo->query("SELECT * FROM cms_posts WHERE is_published = 1 ORDER BY created_at DESC");
        $posts = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        require __DIR__ . '/../../Views/blog/index.php';
    }

    public function blogPost($params)
    {
        $slug = $params['slug'];
        $post = null;
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM cms_posts WHERE slug = ? AND is_published = 1");
            $stmt->execute([$slug]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) { /* Ignore */ }

        if (!$post) {
             http_response_code(404);
             echo "Post Not Found";
             return;
        }
        
        require __DIR__ . '/../../Views/blog/single.php';
    }

    public function submitContact()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? 'New Contact Discovery';
        $message = $_POST['message'] ?? '';

        if (empty($name) || empty($email) || empty($message)) {
            header('Location: /contact?error=missing_fields');
            exit;
        }

        // 1. Log to DB (optional, but good for records)
        // Assume a table exists or just send email
        
        // 2. Send Email to Admin
        $adminEmail = "hello@casjoe.com"; // Default or fetch from settings
        $stmt = $this->pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'admin_email'");
        $settingEmail = $stmt->fetchColumn();
        if ($settingEmail) $adminEmail = $settingEmail;

        $emailBody = "
            <h2>New Contact Message</h2>
            <p><b>Name:</b> $name</p>
            <p><b>Email:</b> $email</p>
            <p><b>Subject:</b> $subject</p>
            <p><b>Message:</b></p>
            <div style='background: #f4f4f4; padding: 15px; border-left: 4px solid #3498db;'>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
        ";

        \App\Core\Mailer::send($adminEmail, "Contact Form: $subject", $emailBody);

        header('Location: /contact?success=message_sent');
        exit;
    }
}
