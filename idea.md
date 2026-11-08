### **Product Requirements Document (PRD): The Synthesis Engine**

**Version:** 1.0
**Date:** January 15, 2026
**Status:** Draft
**Prepared For:** One-Person Operator (Laravel/React Expo Stack)

---

### **1. Executive Summary**

The **Synthesis Engine** is a specialized SaaS application designed to displace traditional agency workflows. It allows a single operator to ingest raw data (competitor URLs, YouTube videos, internal documents) and output high-level strategic intelligence assets.

Unlike generic AI chats, this system uses **Prompt Chaining** and **Context Orchestration** to produce specific "Intelligence Products" (Market Radars, Retention Blueprints) that can be sold directly to clients or used for internal strategy.

---

### **2. Target User**

* **Primary User:** The "One-Person Operator" / Consultant.
* **Goal:** Replace manual research time (20+ hours) with automated AI synthesis (minutes).
* **Pain Point:** Overwhelmed by "blank canvas" AI chats; needs a structured system that delivers sellable assets.

---

### **3. Functional Modules (The "Services")**

The system is divided into three core "Intelligence Modules," each corresponding to a specific service offering.

#### **Module A: The Market Radar (Real-Time Web Intelligence)**

* **Input:** Client Niche (e.g., "Cosmetic Dentistry in Austin") + 3 Competitor URLs.
* **System Action:**
1. Triggers a search API (e.g., Serper/Tavily) to scan the inputs for pricing, offers, and recent reviews.
2. Feeds raw data into the **"Radar Prompt"** (see Section 5).


* **Output:** A structured "Threat Assessment Report" identifying competitor weaknesses and immediate market gaps.

#### **Module B: The Retention Blueprint (Video Analysis)**

* **Input:** YouTube Video URL (Competitor or Market Leader).
* **System Action:**
1. Extracts video transcript and metadata (via YouTube API or scraper).
2. Feeds transcript into the **"Hook Engineer Prompt."**


* **Output:** A "Retention Architecture" document breaking down the video's pacing, hooks, and emotional triggers.

#### **Module C: The Deep Context Core (Internal Synthesis)**

* **Input:** Upload Client Assets (PDFs, CSVs, Past Emails).
* **System Action:**
1. Parses documents into a Vector Database or uses a Long-Context LLM window.
2. Cross-references internal data with external web trends (from Module A).


* **Output:** A "Unified Strategy Map" that aligns client history with current market reality.

---

### **4. Technical Architecture**

This system utilizes your preferred stack to ensure rapid development and scalability.

* **Frontend (Mobile/Web):** **React Expo**
* *Role:* User interface for inputting URLs, uploading files, and viewing/exporting the rendered Markdown reports.
* *Key Feature:* "One-Tap Synthesis" buttons for quick mobile usage.


* **Backend:** **Laravel (Starter Kit)**
* *Role:* API handling, Job Queues (crucial for long AI processing times), User Auth, Database management.
* *Key Library:* `openai-php/laravel` (or similar wrapper) for LLM communication.


* **AI/Intelligence Layer:** **OpenAI (GPT-4o)** OR **Gemini API**
* *Recommendation:* Use **GPT-4o** for text/reasoning. Use a helper service (like a Python microservice or API) to extract YouTube transcripts, as OpenAI cannot "watch" YouTube URLs directly yet.
* *Alternative:* Use **Gemini 1.5 Pro API** if you want massive context windows (1M+ tokens) to dump entire books/manuals into the analysis without RAG complexity.



---

### **5. The Prompt Logic (The "Brain")**

The value of this software lies in the *System Prompts* hidden from the user. The user clicks a button; the system runs these complex chains.

#### **A. The "Radar" System Prompt**

> **Role:** You are an elite Corporate Spy and Market Analyst.
> **Context:** The user is a consultant for [Client Niche].
> **Input Data:** Here is the scraped text from 3 competitor websites: [Insert Scraped Data].
> **Task:**
> 1. **Analyze Pricing:** Reverse engineer their pricing model.
> 2. **Find the Gap:** Identify one specific customer pain point these competitors are ignoring (check their negative reviews or lack of specific FAQ answers).
> 3. **Attack Vector:** Propose 3 specific "Counter-Positioning" angles my client can use to win.
> **Format:** Return as a sleek, professional Markdown table suitable for a C-Level executive summary.
> 
> 

#### **B. The "Hook Engineer" System Prompt**

> **Role:** You are a Hollywood Script Doctor and YouTube Retention Specialist.
> **Input Data:** Here is the transcript of a high-performing video: [Insert Transcript].
> **Task:**
> 1. **Dissect the First 30 Seconds:** What exact visual or verbal hook was used? Why did it work psychologically?
> 2. **Pacing Graph:** Map the video's structure (Intro -> Conflict -> Resolution).
> 3. **Replication:** Write 3 script variations for *my* client that use this exact psychological structure but applied to [Client Topic].
> 
> 

---

### **6. UI/UX Wireframe Concepts**

#### **Screen 1: The Dashboard (Mission Control)**

* **Header:** "Synthesis Engine - Active"
* **Main Action:** Three large cards:
1. 📡 **Run Market Radar** (Input: URLs)
2. 👁️ **Analyze Video** (Input: YouTube Link)
3. 🧠 **Synthesize Strategy** (Input: Upload Files)



#### **Screen 2: The Report View**

* **Display:** Clean, rendered Markdown.
* **Action Bar:**
* "Export to PDF" (For client delivery).
* "Refine" (Chat interface to tweak the results).
* "Save to Dossier" (Save to client database).



---

### **7. Development Roadmap (MVP)**

1. **Phase 1 (The Skeleton):** Setup Laravel + React Expo. Create the database schema for `Projects` (Clients) and `Reports` (Outputs).
2. **Phase 2 ( The Brain):** Integrate OpenAI API. Build the "Prompt Controller" in Laravel to handle the prompt chaining logic.
3. **Phase 3 (The Eyes):** Integrate a "Search/Scrape" API (like Serper.dev) to feed real data into the prompts.
4. **Phase 4 (The UI):** Build the React Expo frontend to display the generated intelligence beautifully.

Here is the **Laravel API Controller logic** for the "Market Radar" feature.

This controller handles the core heavy lifting: accepting the client's niche and competitor URLs, scraping the raw text from those sites, and feeding it into the AI to generate the "Threat Assessment."

### **1. The Controller (`MarketRadarController.php`)**

Create this file in `app/Http/Controllers/Api/`.

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI; // Assuming openai-php/laravel package
use Illuminate\Support\Str;

class MarketRadarController extends Controller
{
    /**
     * Handle the incoming request to generate a Market Radar report.
     */
    public function generate(Request $request)
    {
        // 1. Validate Inputs
        $validated = $request->validate([
            'niche' => 'required|string|max:255',
            'competitor_urls' => 'required|array|min:1|max:3',
            'competitor_urls.*' => 'url',
        ]);

        $niche = $validated['niche'];
        $urls = $validated['competitor_urls'];

        try {
            // 2. The "Eyes": Scrape the Competitor Data
            // In a production app, you might use a Job Queue here to avoid timeouts.
            $scrapedData = $this->scrapeCompetitors($urls);

            // 3. The "Brain": Construct the Prompt
            $systemPrompt = $this->buildSystemPrompt();
            $userPrompt = $this->buildUserPrompt($niche, $scrapedData);

            // 4. Call the AI (OpenAI or Gemini via Adapter)
            $result = OpenAI::chat()->create([
                'model' => 'gpt-4o', // Or 'gpt-4-turbo'
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.7, // Balance between creativity and analysis
            ]);

            $reportContent = $result->choices[0]->message->content;

            // 5. Return the Intelligence Product
            return response()->json([
                'status' => 'success',
                'data' => [
                    'niche' => $niche,
                    'report_markdown' => $reportContent,
                    'sources_analyzed' => $urls
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Synthesis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Scrape raw text from URLs.
     * Note: In production, consider using a service like Serper.dev, Browsingless,
     * or a headless browser to handle JS-heavy sites.
     */
    private function scrapeCompetitors(array $urls): string
    {
        $combinedText = "";

        foreach ($urls as $index => $url) {
            try {
                // Set a timeout to prevent hanging
                $response = Http::timeout(10)->get($url);

                if ($response->successful()) {
                    // Strip HTML tags to get raw text
                    $rawBody = strip_tags($response->body());
                    
                    // Clean up whitespace
                    $cleanText = preg_replace('/\s+/', ' ', $rawBody);
                    
                    // Limit text length per site to save tokens (e.g., 2000 chars)
                    $truncated = Str::limit($cleanText, 3000);

                    $combinedText .= "--- COMPETITOR " . ($index + 1) . " ($url) ---\n";
                    $combinedText .= $truncated . "\n\n";
                }
            } catch (\Exception $e) {
                // If one site fails, log it but don't stop the whole process
                $combinedText .= "--- COMPETITOR " . ($index + 1) . " (Failed to load) ---\n\n";
            }
        }

        return $combinedText;
    }

    /**
     * Helper: Define the AI Persona
     */
    private function buildSystemPrompt(): string
    {
        return <<<EOT
You are the "Synthesis Engine," an elite Corporate Spy and Market Analyst. 
Your goal is to analyze raw competitor data and produce a "Threat Assessment Report."
Do not use fluff. Be surgical, data-driven, and strategic.
Output your response in clean Markdown format with headers, bullet points, and tables.
EOT;
    }

    /**
     * Helper: Feed the data to the AI
     */
    private function buildUserPrompt(string $niche, string $data): string
    {
        return <<<EOT
**CLIENT NICHE:** $niche

**COMPETITOR DATA (RAW SCRAPES):**
$data

**MISSION:**
1. **Pricing Recon:** Reverse engineer their pricing models or offer structures based on the text.
2. **Gap Analysis:** Identify one specific customer pain point these competitors are ignoring (look for missing FAQ answers or generic copy).
3. **Attack Vector:** Propose 3 specific "Counter-Positioning" angles my client can use to displace them.

Generate the "Threat Assessment Report" now.
EOT;
    }
}

```

### **2. The Route (`routes/api.php`)**

Add this to your API routes file to expose the endpoint.

```php
use App\Http\Controllers\Api\MarketRadarController;

Route::post('/synthesis/market-radar', [MarketRadarController::class, 'generate']);

```

### **3. Implementation Notes for the "One-Person Operator"**

1. **The Scraping Logic:**
* The `strip_tags` method used above is a "quick and dirty" solution. It works for simple HTML sites.
* **Pro Tip:** If your competitors use heavy React/Vue sites (client-side rendering), `Http::get` won't see the text. In that case, you will need to swap the `scrapeCompetitors` logic to call an API like **ScrapingBee** or **ZenRows** later.


2. **Timeout Handling:**
* AI generation can take 10-30 seconds.
* Ensure your `php.ini` has `max_execution_time` set high enough (e.g., 60 seconds), or better yet, eventually move this logic into a **Laravel Job** (`MarketRadarJob`) so the user doesn't have to wait on a loading screen.


3. **Token Management:**
* I added `Str::limit($cleanText, 3000)` to ensure you don't accidentally blow up your OpenAI bill if a competitor site has a massive amount of text. 3,000 characters is usually enough to get the gist of a homepage.

