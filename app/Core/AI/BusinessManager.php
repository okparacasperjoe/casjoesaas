<?php
namespace App\Core\AI;
class BusinessManager {
    protected AIService $ai;
    protected ContextAggregator $aggregator;
    protected string $userName = "User";

    public function __construct(int $tenantId, string $userName = "User") {
        $this->ai = new AIService();
        $this->aggregator = new ContextAggregator($tenantId);
        $this->userName = $userName;
    }

    public function getMorningBriefing(): string {
        $context = $this->aggregator->getDailyContext();
        $company = $this->aggregator->companyName;
        
        // Time of Day Logic
        $hour = (int)date('H');
        if ($hour < 12) $greeting = "Good Morning";
        elseif ($hour < 17) $greeting = "Good Afternoon";
        else $greeting = "Good Evening";

        $dataStr = json_encode($context);
        
        $prompt = "You are a friendly Business Consultant for $company. The user is $this->userName. ";
        $prompt .= "Current time is " . date('h:i A') . ". Greeting should be '$greeting'. ";
        $prompt .= "Write a concise business briefing based on this data: $dataStr. ";
        $prompt .= "CRITICAL INSTRUCTION: The very FIRST sentence of your response MUST be a short, catchy 'hook' summarizing the most important or urgent insight to grab the user's attention (e.g. 'You have 2 overdue invoices to collect today!' or 'Business is stable, let's focus on marketing!'). ";
        $prompt .= "After the hook, add EXACTLY two newline characters (\\n\\n). ";
        $prompt .= "Then, write the detailed briefing starting exactly with '$greeting $this->userName, here is your update for $company'. Keep it professional and encouraging.";

        try {
            $result = $this->ai->generate($prompt, ["max_tokens"=>300]);
            if (str_starts_with($result, "Error:")) throw new \Exception($result);
            return $result;
        } catch (\Exception $e) {
            $financials = !empty($context['financials']) ? $context['financials'][0] : [];
            $revenue = floatval($financials['total'] ?? 0);
            $currency = $financials['currency'] ?? 'NGN';

            $crm = $context['crm'] ?? [];
            $leads = intval($crm['new_leads'] ?? 0);
            $clients = intval($crm['clients_count'] ?? 0);

            $marketing = $context['marketing'] ?? [];
            $emailLists = intval($marketing['email_lists'] ?? 0);

            $hr = $context['hr'] ?? [];
            $staff = intval($hr['staff_count'] ?? 0);
            $pendingApplications = intval($hr['pending_applications'] ?? 0);

            // 1. Create a true, actionable hook based on data
            $hook = "Business is quiet today, let's focus on acquiring new clients!";
            if ($revenue > 0 || $leads > 0) {
                $hook = "Great news—we tracked " . number_format($revenue) . " $currency in revenue and $leads new leads today!";
            } elseif ($pendingApplications > 0) {
                $hook = "You have $pendingApplications pending job applications waiting for your review.";
            } elseif ($clients > 5 && $emailLists == 0) {
                $hook = "You have $clients clients! It's time to create an email list to drive more engagement.";
            }

            // 2. Build the main body
            $msg = "$hook\n\n";
            $msg .= "**$greeting, $this->userName!**\n";
            $msg .= "Here is the latest snapshot for **$company**:\n\n";

            if ($revenue > 0 || $leads > 0) {
                $msg .= "🚀 **Activity:** We tracked " . number_format($revenue) . " $currency in revenue and $leads new leads today.\n\n";
            } else {
                $msg .= "📉 **Status:** Business is quiet today. No new transactions or leads yet.\n\n";
            }
            
            $msg .= "**👉 Next Thing To Do:**\n";
            
            // Dynamic suggestions based on actual business state
            $suggestions = [];
            
            if ($leads > 0) {
                $suggestions[] = "Follow up with your $leads new lead" . ($leads > 1 ? 's' : '') . " to convert them into clients.";
            }
            if ($pendingApplications > 0) {
                $suggestions[] = "Review $pendingApplications pending job application" . ($pendingApplications > 1 ? 's' : '') . " in your HR module.";
            }
            if ($clients > 5 && $emailLists == 0) {
                $suggestions[] = "Create an email list in Casjoe Mail to stay connected with your $clients clients.";
            } elseif ($emailLists > 0 && $clients > 0) {
                $suggestions[] = "Send a value-packed email to your $clients client" . ($clients > 1 ? 's' : '') . " to drive engagement.";
            }
            if ($revenue == 0 && $clients > 0) {
                $suggestions[] = "Reach out to existing clients with a special offer or follow up on pending invoices.";
            }
            if ($staff == 0 && $clients > 3) {
                $suggestions[] = "Growing client base! Consider hiring support staff to scale your operations.";
            }
            
            if (empty($suggestions)) {
                $generalSuggestions = [
                    "Focus on acquiring your first client. Reach out to your network or run a targeted ad campaign.",
                    "Build your foundation: Set up your services, pricing, and client onboarding process.",
                    "Create valuable content to attract potential clients. Share your expertise!"
                ];
                $suggestions[] = $generalSuggestions[array_rand($generalSuggestions)];
            }
            
            $msg .= $suggestions[array_rand($suggestions)];
            
            return $msg;
        }
    }
}
