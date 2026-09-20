<?php

namespace App\Modules\CasjoeLinks\Services;

use App\Core\Database;

class FunnelBuilder
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all steps for a funnel
     */
    public function getSteps($funnelId)
    {
        $stmt = $this->db->query(
            "SELECT * FROM funnel_steps WHERE funnel_id = ? ORDER BY step_order ASC",
            [$funnelId]
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Add a new step to funnel
     */
    public function addStep($funnelId, $stepType, $config = [])
    {
        // Get current max order
        $stmt = $this->db->query(
            "SELECT MAX(step_order) as max_order FROM funnel_steps WHERE funnel_id = ?",
            [$funnelId]
        );
        $result = $stmt->fetch();
        $newOrder = ($result['max_order'] ?? 0) + 1;

        // Insert new step
        $stmt = $this->db->prepare(
            "INSERT INTO funnel_steps (funnel_id, step_type, step_order, config) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$funnelId, $stepType, $newOrder, json_encode($config)]);

        return $this->db->getConnection()->lastInsertId();
    }

    /**
     * Update step configuration
     */
    public function updateStep($stepId, $config)
    {
        $stmt = $this->db->prepare(
            "UPDATE funnel_steps SET config = ? WHERE id = ?"
        );
        $stmt->execute([json_encode($config), $stepId]);
        return true;
    }

    /**
     * Reorder steps
     */
    public function reorderSteps($funnelId, $stepIds)
    {
        $order = 1;
        foreach ($stepIds as $stepId) {
            $this->db->query(
                "UPDATE funnel_steps SET step_order = ? WHERE id = ? AND funnel_id = ?",
                [$order, $stepId, $funnelId]
            );
            $order++;
        }
        return true;
    }

    /**
     * Delete a step
     */
    public function deleteStep($stepId, $funnelId)
    {
        $this->db->query(
            "DELETE FROM funnel_steps WHERE id = ? AND funnel_id = ?",
            [$stepId, $funnelId]
        );

        // Reorder remaining steps
        $steps = $this->getSteps($funnelId);
        $order = 1;
        foreach ($steps as $step) {
            $this->db->query(
                "UPDATE funnel_steps SET step_order = ? WHERE id = ?",
                [$order, $step['id']]
            );
            $order++;
        }

        return true;
    }

    /**
     * Get all available templates
     */
    public function getTemplates()
    {
        return [
            'landing' => [
                'modern' => [
                    'name' => 'Modern Conversion',
                    'thumbnail' => 'https://placehold.co/600x400/4F46E5/ffffff?text=Modern+Conversion',
                    'config' => [
                        'headline' => 'Transform Your Business Today',
                        'content' => '<div class="text-center py-12"><h1 class="text-5xl font-extrabold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600">Transform Your Business Today</h1><p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">The all-in-one solution you have been waiting for. Stop juggling multiple tools and start scaling.</p><div class="flex justify-center gap-4"><button class="btn btn-primary btn-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">Get Started Free</button><button class="btn btn-secondary btn-lg">Watch Demo</button></div></div>',
                        'cta_text' => 'Get Started',
                        'cta_color' => '#4F46E5'
                    ]
                ],
                'minimalist' => [
                    'name' => 'Clean Minimalist',
                    'thumbnail' => 'https://placehold.co/600x400/f8f9fa/333333?text=Minimalist',
                    'config' => [
                        'headline' => 'Less is More',
                        'content' => '<div class="max-w-3xl mx-auto py-20 text-center"><h1 class="text-4xl font-light text-gray-900 mb-8">Focus on what matters.</h1><p class="text-lg text-gray-500 mb-12">Clarity is the key to conversion. Our platform provides the essential tools without the clutter.</p><div class="border-t border-b border-gray-100 py-8 my-8"><p class="italic text-gray-400">"Simplicity is the ultimate sophistication."</p></div></div>',
                         'cta_text' => 'Learn More',
                         'cta_color' => '#333333'
                    ]
                ],
                'video_sales' => [
                    'name' => 'Video Centric',
                    'thumbnail' => 'https://placehold.co/600x400/000000/ffffff?text=Video+Sales',
                    'config' => [
                        'headline' => 'Watch This Video',
                        'content' => '<div class="bg-gray-900 text-white py-16"><div class="max-w-4xl mx-auto text-center"><h1 class="text-4xl font-bold mb-8">See How It Works</h1><div class="aspect-w-16 aspect-h-9 bg-black rounded-xl shadow-2xl mb-12 flex items-center justify-center border border-gray-700" style="height: 400px;"><img src="https://placehold.co/800x450/333/666?text=Video+Placeholder" alt="Video"></div><p class="text-xl text-gray-300 mb-8">Discover the secret method used by top pros.</p></div></div>',
                        'cta_text' => 'Watch Now',
                        'cta_color' => '#DC2626'
                    ]
                ],
                'dark_mode' => [
                    'name' => 'SaaS Dark Mode',
                    'thumbnail' => 'https://placehold.co/600x400/0f172a/38bdf8?text=SaaS+Dark',
                    'config' => [
                        'headline' => 'Develop Faster',
                        'content' => '<div class="bg-slate-900 text-slate-50 py-20"><div class="max-w-5xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center"><div><h1 class="text-5xl font-bold mb-6 text-blue-400">Developer First.</h1><p class="text-lg text-slate-400 mb-8">Built for speed, reliability, and scale. Deploy your next idea in seconds, not days.</p><ul class="space-y-4 text-slate-300 mb-8"><li>✓ 99.9% Uptime</li><li>✓ Global CDN</li><li>✓ API First</li></ul></div><div class="bg-slate-800 p-8 rounded-lg border border-slate-700"><div class="h-64 bg-slate-900 rounded flex items-center justify-center text-slate-600"><img src="https://placehold.co/400x300/1e293b/475569?text=App+Preview" alt="App"></div></div></div></div>',
                        'cta_text' => 'Deploy App',
                        'cta_color' => '#3B82F6'
                    ]
                ],
                 'startup' => [
                    'name' => 'Startup Launch',
                    'thumbnail' => 'https://placehold.co/600x400/4F46E5/ffffff?text=Startup',
                    'config' => [
                        'headline' => 'We satisfy your needs',
                        'content' => '<div class="py-16"><div class="text-center mb-16"><span class="text-sm font-bold tracking-wider text-indigo-600 uppercase">New Release</span><h1 class="mt-2 text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">Take control of your team.</h1><p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">Project management software that actually works.</p></div><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"><div class="grid grid-cols-1 md:grid-cols-3 gap-8"><div class="p-6 bg-white rounded-lg shadow-lg border border-gray-100"><h3>Track</h3><p class="text-gray-500 mt-2">Real-time updates.</p></div><div class="p-6 bg-white rounded-lg shadow-lg border border-gray-100"><h3>Manage</h3><p class="text-gray-500 mt-2">Drag and drop tasks.</p></div><div class="p-6 bg-white rounded-lg shadow-lg border border-gray-100"><h3>Ship</h3><p class="text-gray-500 mt-2">Release with confidence.</p></div></div></div></div>',
                        'cta_text' => 'Start Trial',
                        'cta_color' => '#4F46E5'
                    ]
                ],
                'enterprise' => [
                    'name' => 'Corporate Pro',
                    'thumbnail' => 'https://placehold.co/600x400/0F172A/ffffff?text=Enterprise',
                    'config' => [
                        'headline' => 'Enterprise Solutions',
                        'content' => '<div class="py-20 bg-gray-50"><div class="max-w-7xl mx-auto px-4"><div class="lg:text-center"><p class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Solutions</p><h2 class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Trusted by the Fortune 500</h2><p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">Security, Compliance, and Scalability built into every layer.</p></div><div class="mt-20"><div class="flex justify-center gap-8 grayscale opacity-50"><img src="https://placehold.co/150x50?text=LOGO1" alt="Logo"><img src="https://placehold.co/150x50?text=LOGO2" alt="Logo"><img src="https://placehold.co/150x50?text=LOGO3" alt="Logo"></div></div></div></div>',
                        'cta_text' => 'Contact Sales',
                        'cta_color' => '#0F172A'
                    ]
                ]
            ],
            'sales' => [
                'long_form' => [
                   'name' => 'Classic Sales Letter',
                   'config' => $this->getDefaultConfig('sales') // Reuse existing logic helper
                ],
                // Reuse landing templates for sales too, just changing CTA context? 
                // Creating simplified pointers to above for now to save space, logic handled in controller/builder UI
            ]
        ];
    }

    /**
     * Get default config for step type
     */
    public function getDefaultConfig($stepType)
    {
        // Default to 'modern' for landing
        if ($stepType === 'landing') {
            return $this->getTemplates()['landing']['modern']['config'];
        }
        
        $defaults = [
            'sales' => [
                'headline' => 'The Ultimate Solution',
                'content' => '<div class="max-w-4xl mx-auto py-10"><div class="text-center mb-12"><h1 class="text-5xl font-bold mb-6 text-purple-900">The Ultimate Solution for Growth</h1><p class="text-2xl text-gray-700 mb-8">Stop struggling with manual tasks. Automate everything today.</p></div><div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12 items-center"><div><h2 class="text-3xl font-bold mb-4">Is This You?</h2><ul class="space-y-2"><li class="flex items-center"><span class="text-red-500 mr-2">❌</span> Overwhelmed by spreadsheets</li><li class="flex items-center"><span class="text-red-500 mr-2">❌</span> Losing leads in the cracks</li></ul></div><div class="bg-purple-50 p-8 rounded-xl"><h2 class="text-3xl font-bold mb-4 text-purple-800">There is a Better Way</h2><p>Our system handles the heavy lifting so you can focus on what you do best.</p></div></div></div>',
                'cta_text' => 'Buy Now',
                'cta_color' => '#27ae60'
            ],
            'form' => [
                'smart_form_id' => null,
                'title' => 'Fill in your details'
            ],
            'payment' => [
                'product_id' => null,
                'amount' => 0,
                'currency' => 'NGN',
                'description' => 'Premium Access',
                'has_order_bump' => false,
                'bump_title' => 'Priority VIP Support',
                'bump_amount' => 5000,
                'bump_description' => 'Get direct 24/7 priority support and expedited turnaround.'
            ],
            'upsell' => [
                'headline' => 'Wait! Upgrade Your Order With This Special 1-Time Offer',
                'subheadline' => 'Add the Masterclass Bundle at a 70% discount today only.',
                'product_name' => 'VIP Fast-Track Bundle',
                'amount' => 15000,
                'currency' => 'NGN',
                'content' => '<p>Get instant access to over 20+ ready-to-use templates, step-by-step video walkthroughs, and private community access.</p>',
                'accept_text' => 'Yes! Add This To My Order for ₦15,000',
                'decline_text' => 'No thanks, I will pass on this one-time discount'
            ],
            'downsell' => [
                'headline' => 'Wait — How About a Lighter Option?',
                'subheadline' => 'Get the Essential Toolkit for just ₦5,000.',
                'product_name' => 'Essential Starter Kit',
                'amount' => 5000,
                'currency' => 'NGN',
                'content' => '<p>We understand the full bundle might be more than you need right now. Get just the core cheat-sheets and templates for a fraction of the cost.</p>',
                'accept_text' => 'Yes! Give Me The Starter Kit for ₦5,000',
                'decline_text' => 'No thanks, take me to my order confirmation'
            ],
            'thankyou' => [
                'headline' => 'Thank You!',
                'content' => '<div class="text-center py-16"><div class="text-6xl mb-4">🎉</div><h1 class="text-4xl font-bold mb-4">You\'re In!</h1><p class="text-xl text-gray-600 mb-8">Thank you for signing up. Check your email for the next steps.</p></div>',
                'message' => 'We have received your submission.',
                'redirect_url' => '',
                'redirect_delay' => 0
            ]
        ];

        return $defaults[$stepType] ?? [];
    }
}
